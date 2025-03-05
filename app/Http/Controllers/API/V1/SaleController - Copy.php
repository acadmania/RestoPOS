<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Material;
use App\Models\Table_item;
use App\Models\Consumption_item;
use App\Models\Product;
//use App\Models\ProductUnit;
use App\Models\Location;
use App\Models\Food;
use App\Models\Register;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\SaleStatus;
use App\Models\Table;
use App\Models\SalePayment;
use App\Models\Setting;
use App\Models\Point;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\CapabilityProfiles\DefaultCapabilityProfile;
use Mike42\Escpos\CapabilityProfiles\SimpleCapabilityProfile;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\ImagickEscposImage;
use mpdf\mpdf;

class SaleController extends BaseController
{
  protected $item = '';

  public function __construct(Sale $item)
  {
    $this->middleware('auth:api');
    $this->item = $item;
  }

  public function index($customer_id = null)
  {
    if ($customer_id) {
      $items = $this->item->where('customer_id', $customer_id)->latest()->paginate(10);
    } else {
      $items = $this->item->latest()->paginate(10);
    }

    return $this->sendResponse($items, 'Sale list');
  }
  public function getIncomplete()
  {

     $items = $this->item->with('items')->where('status', '<>', 'Completed')->latest()->paginate(10);


     return $this->sendResponse($items, 'Sale list');
  }

  public function getSale($sale_id)
  {
    $sale = Sale::with('items', 'statuses', 'payStatuses', 'customer')->find($sale_id);

    $sale->gsts = $sale->items->groupby('gst_percentage');
    $sale->totalDiscount = 0;
    foreach ($sale->items as $i) {
      $sale->totalDiscount = $sale->totalDiscount + $i->discount * $i->quantity;
    }
    return $this->sendResponse($sale, 'Sale View');
  }
  public function getBookingOrder($customer_id)
  {
    $sale = Sale::with('items', 'statuses', 'payStatuses', 'customer')->where('customer_id', $customer_id)->where('type', 'b')->where('status', 'Ordered')->first();
    $sale->gsts = $sale->items->groupby('gst_percentage');
    $sale->totalDiscount = 0;
    foreach ($sale->items as $i) {
      $sale->totalDiscount = $sale->totalDiscount + $i->discount * $i->quantity;
    }
    return $this->sendResponse($sale, 'Sale View');
  }

  public function salePayment_old(Request $request)
  {
    $response = DB::transaction(
      function () use (&$request) {
        $sale = Sale::find($request->current_sale_id);
        $sale->payment_status = $request->payment_status;
        $sale->save();
        $account = Account::find($request->account_id);
        $supplier = Customer::find($sale->customer_id);
        if ($sale->payment_status !== "Unpaid") {
          $payStatus = new SalePayment;
          $payStatus->sale_id = $sale->id;
          $payStatus->account_id = $account->id;
          $payStatus->account_name = $account->name;
          $payStatus->status = $sale->payment_status;
          $payStatus->user_id = Auth::user()->id;
          $payStatus->user_name = Auth::user()->name;
          if ($request->amount_paid) {
            $payStatus->amount = $request->amount_paid;
          } else {
            $payStatus->amount = $sale->grand_total - $sale->payStatuses()->sum('amount');
          }
          $payStatus->save();

          $lastTrans = Transaction::where('account_id', $account->id)->latest('id')->first();
          if ($lastTrans) {
            $closing_balance = $lastTrans->closing_balance;
          } else {
            $closing_balance = $account->balance;
          }
          $transaction = new Transaction;
          $transaction->account_id = $account->id;
          $transaction->account_name = $account->name;
          $transaction->opening_balance = $closing_balance;
          $transaction->amount = $payStatus->amount;
          $transaction->type = 'c';
          $transaction->closing_balance = $closing_balance + $payStatus->amount;
          $transaction->customer_id = $supplier->id;
          $transaction->customer_name = $supplier->name;
          $transaction->user_id = Auth::user()->id;
          $transaction->user_name = Auth::user()->name;
          $transaction->save();

          $account->balance = $account->balance - $payStatus->amount;
          $account->save();
        }
      }
    );
    return $response;
  }

  public function searchList($search)
  {
    $items = $this->item->where('id', $search)->paginate(10);
    return $this->sendResponse($items, 'Sale list Search');
  }


  public function store(Request $request)
  {

    $response = DB::transaction(
      function () use (&$request) {
        $register_id = null;
        if (str_contains(request()->headers->get('referer'), 'pos')) {
          $user = Auth::user();
          $register = $user->registers->where('status', 'open')->first();
          $location_id = $user->registers->where('status', 'open')->first()->location_id;
          $location = Location::find($location_id);
          $register_id = $register->id;
          if ($request->payment_method == 'Cash')
            $account_id = $user->cashAccounts[$location_id];
          elseif ($request->payment_method == 'Card')
            $account_id = $user->cardAccounts[$location_id];
          elseif ($request->payment_method == 'Online')
            $account_id = $user->onlineAccounts[$location_id];
          elseif ($request->payment_method == 'GPay')
            $account_id = $user->gpayAccounts[$location_id];
          elseif ($request->payment_method == 'Phonepe')
            $account_id = $user->phonepeAccounts[$location_id];
          elseif ($request->payment_method == 'Amazon Pay')
            $account_id = $user->amazonpayAccounts[$location_id];
          if ($request->payment_method !== "Unpaid")
            $account = Account::find($account_id);
          $customer = Customer::find($request->customer['code']);

        } else {
          $account = Account::find($request->account_id);
          $location = Location::find($request->location_id);
          $customer = Customer::find($request->customer_id);
        }


        $setting = Setting::first();
        $already_paid_amount = 0;
        $pre_book = Sale::where('customer_id', $customer->id)->where('type', 'b')->where('status', 'Ordered')->first();
        if ($pre_book) {
          $sale = $pre_book;

          $sale->items()->delete();
          $already_paid_amount = $sale->payStatuses->sum('amount');
        } else {
          $sale = new Sale;
          $sale->customer_id = $customer->id;
          $sale->register_id = $register_id;
          $sale->customer_name = $customer->name;




          $sale->location_id = $location->id;
          if (isset($request->table) && $request->type == 'd') {
            $sale->table_id = $request->table['value'];
            $sale->table_name = Table::find($request->table['value'])->name;
          }

          $sale->type = $request->type;
          $sale->employee_id= $request->employee_id;

          $sale->location_name = $location->name;
          $sale->user_id = Auth::user()->id;
          $sale->user_name = Auth::user()->name;

        }
        $sale->note = $request->note;
        if ($request->type == 'b' and $request->dateTime !== null)
          $sale->dateTime = Carbon::parse(Carbon::parse($request->dateTime));
        $sale->total = $request->total_subtotal;
        $sale->gst = $request->grand_tax_total;

        $sale->discount = $request->grand_discount;
        $sale->discounts = $request->discounts;
        $sale->cash_tendered = $request->amount_paid;
        if ($request->cash_balance) {
          $sale->cash_balance = $request->cash_balance;
        }

        if (str_contains(request()->headers->get('referer'), 'pos')) {
          $sale->status = 'Completed';

          if ($request->amount_paid + $already_paid_amount >= $request->grand_total or $request->amount_paid + $already_paid_amount == $request->grand_total) {

            $sale->payment_status = 'Paid';
          } elseif ($request->amount_paid + $already_paid_amount < $request->grand_total && $request->payment_method != 'Unpaid') {
            $sale->payment_status = 'Partially Paid';
          }
          /*  elseif($request->amount_paid < $request->grand_total AND $request->payment_method != 'Cash') {
           $sale->payment_status = 'Paid';
           }*/elseif ($request->amount_paid + $already_paid_amount == null and $request->amount_paid + $already_paid_amount == 0) {
            $sale->payment_status = 'Unpaid';
          } elseif ($request->payment_method == 'Unpaid') {

            $sale->payment_status = 'Unpaid';
          }
          if ($request->orderStatus == 'Ordered') {
            $sale->status = 'Ordered';
          }
        } else {
          $sale->status = $request->order_status;
          $sale->payment_status = $request->payment_status;
        }
        $sale->grand_total = $request->grand_total;
        $sale->save();




        if ($setting) {
          if ($setting->amountpoint and $customer->no_points == 0) {
            $point = new Point;
            $point->customer_id = $customer->id;
            $point->customer_name = $customer->name;
            $lastTrans = Point::where('customer_id', $customer->id)->latest('id')->first();
            if ($lastTrans) {
              $closing_balance = $lastTrans->closing_balance;
            } else {
              $closing_balance = $customer->points;
            }
            $point->opening_balance = $closing_balance;
            $points = $setting->amountpoint * $request->grand_total;
            $point->points = $points;
            $point->type = 'c';
            $point->closing_balance = $point->opening_balance + $points;
            $point->sale_id = $sale->id;
            $point->user_id = Auth::user()->id;
            $point->user_name = Auth::user()->name;
            $point->save();

            $customer->points = $point->closing_balance;
            $customer->save();
          }
        }

        $pStatus = new SaleStatus();
        $pStatus->sale_id = $sale->id;
        if ($request->orderStatus == 'Ordered') {
          $pStatus->status = 'Ordered';
        } else {
          $pStatus->status = $sale->status;
        }

        $pStatus->user_id = Auth::user()->id;
        $pStatus->user_name = Auth::user()->name;
        $pStatus->save();

        foreach (array_reverse($request->items) as $i) {
          $itm = SaleItem::create(
            [
              'food_id' => $i['product_id'],
              'food_name' => $i['product_name'],
              'hsn' => $i['hsn'],
              'code' => $i['code'],
              'quantity' => $i['quantity'],
              'subtotal' => $i['subtotal'],
              'price' => $i['rate'],
              'selling_price' => $i['price'],
              'gst' => $i['tax_total'],
              'gst_percentage' => $i['gst_percentage'],
              'discount' => $i['discount'],
              'cost' => $i['cost'],
              'sale_id' => $sale->id,
            ]
          );
          foreach ($i['selectedModifiers'] as $sm) {
            $itm->modifiers()->attach($sm['id'], ['price' => $sm['price']]);
            $itm->food_name = $itm->food_name . ' | ' . $sm['name'];
            $itm->selling_price = $itm->selling_price + $sm['price'];
            $itm->save();
          }

          $food = Food::find($itm->food_id);

          if ($sale->status = "Completed")
            foreach ($food->products as $p) {
              $pq = $p->pivot->quantity;


              $t = DB::table('location_product')->where('product_id', $p->id);

              if ($t->get()->count() > 0) {

                $q = $t->first()->quantity - ($itm->quantity * $pq);
                $t->update(['quantity' => $q]);
              } else {
                $location->products()->attach($p->id, ['quantity' => 0 - $pq]);
              }

              //consumption
              $consumption_item = new Consumption_item;

              $consumption_item->product_id = $p->id;
              $consumption_item->name = $p->name;
              $consumption_item->quantity = $pq;

              $consumption_item->auto_deduct = 1;

              $consumption_item->save();


            }
        }

        if (isset($request->table) && $request->type == 'd') {

            $sale->gst = $sale->items->sum(function ($item) {
                return $item->gst * $item->quantity;
            });
            $sale->save();
          Table_item::where('table_id', $request->table['value'])->delete();

        }


        if ($request->payment_status !== "Unpaid" and $request->amount_paid !== 0 and $request->payment_method !== "Unpaid") {
          $payStatus = new SalePayment;
          $payStatus->sale_id = $sale->id;
          $payStatus->account_id = $account->id;
          $payStatus->account_name = $account->name;
          $payStatus->payment_method = $request->payment_method;
          $payStatus->status = $sale->payment_status;
          $payStatus->user_id = Auth::user()->id;
          $payStatus->user_name = Auth::user()->name;
          if($request->payment_method == 'Cash'){

            if($request->grand_total <= $request->amount_paid)
            $payStatus->amount = $request->grand_total;
            else
            $payStatus->amount = $request->amount_paid;
          }
          else {
            $payStatus->amount = $sale->grand_total;
          }
            $payStatus->save();

          $lastTrans = Transaction::where('account_id', $account->id)->latest('id')->first();
          if ($lastTrans) {
            $closing_balance = $lastTrans->closing_balance;
          } else {
            $closing_balance = $account->balance;
          }
          $transaction = new Transaction;
          $transaction->account_id = $account->id;
          $transaction->account_name = $account->name;
          $transaction->opening_balance = $closing_balance;
          $transaction->amount = $payStatus->amount;
          $transaction->type = 'c';
          $transaction->closing_balance = $closing_balance + $payStatus->amount;
          $transaction->customer_id = $customer->id;
          $transaction->sale_id = $sale->id;
          $transaction->customer_name = $customer->name;
          $transaction->user_id = Auth::user()->id;
          $transaction->user_name = Auth::user()->name;
          $transaction->save();

          $account->balance = $account->balance + $payStatus->amount;
          $account->save();
        }
        return $this->sendResponse($sale, 'Sale Created Successfully');
      }
    );
    return $response;
  }
  function getTokens()
  {
    $sales = Sale::where('status', 'Ordered')->orwhere('status', 'Ready')->get();
    return $this->sendResponse($sales, 'Sale Token');
  }
  function statusChange($id, $status)
  {
    $sale = Sale::find($id);
    $sale->status = $status;
    $sale->save();
    return $this->sendResponse($sale, 'Sale Completed');
  }

  function saleReceive($id)
  {
    $response = DB::transaction(
      function () use (&$id) {
        $sale = Sale::find($id);
        $location = Location::find($sale->location_id);
        $sale->status = "Received";
        $sale->save();

        $pStatus = new SaleStatus();
        $pStatus->sale_id = $sale->id;
        $pStatus->status = $sale->status;
        $pStatus->user_id = Auth::user()->id;
        $pStatus->user_name = Auth::user()->name;
        $pStatus->save();

        foreach ($sale->items as $itm) {
          $material = Material::find($itm->material_id);
          if ($location->materials()->where('material_id', $material->id)->exists()) {
            $q = $location->materials()->where('material_id', $material->id)->first()->pivot->quantity;
            $location->materials()->updateExistingPivot($material->id, array('quantity' => $q + $itm->quantity));
          } else {
            $location->materials()->attach(
              $material,
              [
                'quantity' => $itm->quantity
              ]
            );
          }
        }
      }
    );
    return $response;
  }


  public function getBatch($batch_id, $customer_id)
  {
    $b = DB::table('location_product')->where('id', $batch_id)->get()->first();
    $product = Product::with('category')->find($b->product_id);
    $price = $b->price;
    $rate = $b->price;
    $discount = 0;
    $customer = Customer::find($customer_id);
    $group_id = $customer->customer_group_id;
    $flag = 0;
    if ($group_id !== null and $product->customer_group_discounts !== null) {
      foreach ($product->customer_group_discounts as $grp) {
        if ($grp['group_id'] == $group_id) {
          $flag = 1;
          //$offer_price = $value;
          $price = $grp['discounted_price'];

          $discount = $rate - $price;

        }
      }
    }
    if (!$flag) {
      if ($b->sale_price)
        $price = $b->sale_price;
      $discount = $b->price - $b->sale_price;
      $batch = $b->batch;
      $code = $b->code;
      $expiry_date = $b->expiry_date;
    }

    $tax_total = 0;


    if ($b->gst_percentage) {
      if ($b->price_include_gst == 1) {
        $tax_total = $price * $b->gst_percentage / (100 + $b->gst_percentage);
      } else {
        $tax_total = $price * $b->gst_percentage / (100);
      }
    }
    $cost_only = $b->cost;
    if ($b->gst_percentage) {
      if ($b->cost_include_gst == 0) {
        $tax_total_cost = $cost_only * $b->gst_percentage / (100);
        $cost = $cost_only + $tax_total_cost;
      } else {
        $cost = $cost_only;
      }
    } else {
      $cost = $cost_only;
    }

    return [
      'tax_total' => $tax_total,
      'price' => $price,
      'rate' => $rate,
      'code' => $b->code,
      'batch' => $b->batch,
      'expiry_date' => $b->expiry_date,
      'price_include_gst' => $b->price_include_gst,
      'cost_include_gst' => $b->cost_include_gst,
      'cost' => $cost,

    ];

  }



  public function getProduct($product_id, $customer_id)
  {
    $batch = null;
    $code = null;
    $expiry_date = null;
    $l = Register::where('status', 'open')->first()->location_id;
    $location = Location::find($l);
    $product = Food::with('category', 'modifiers')->find($product_id);


    $price = $product->price;
    $rate = $product->price;



    $discount = 0;





    if ($product->sale_price) {
      $price = $product->sale_price;
      $discount = $product->price - $product->sale_price;

      $code = $product->code;

    }





    $tax_total = 0;



    if ($product->gst_percentage) {
      if ($product->price_include_gst == 1) {
        $tax_total = $price * $product->gst_percentage / (100 + $product->gst_percentage);
      } else {
        $tax_total = $price * $product->gst_percentage / (100);
      }
    }
    $cost_only = $product->cost;
    if ($product->gst_percentage) {
      if ($product->cost_include_gst == 0) {
        $tax_total_cost = $cost_only * $product->gst_percentage / (100);
        $cost = $cost_only + $tax_total_cost;
      } else {
        $cost = $cost_only;
      }
    } else {
      $cost = $cost_only;
    }




    return [
      array(
        'rate' => $rate,
        'price' => $price,
        'tax_total' => $tax_total,
        'discount' => $discount,
        'cost' => $cost,
        'code' => $code,
      ),
      $product
    ];

  }

  public function filterSale(Request $request)
  {


    if($request->dateFilter !== null AND $request->dateFilter[1] !== null AND $request->customer !== null AND $request->type !== null)
    {

      $from = Carbon::parse(Carbon::parse($request->dateFilter[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->dateFilter[1])->addDay()->endOfDay();

      $sale = Sale::where('created_at', '>=', $from)->where('created_at', '<=', $to)->where('customer_name','like', '%'.$request->customer.'%')->where('type','like', '%'.$request->type.'%')->get();

        }

    elseif ($request->customer == null ) {

      $from = Carbon::parse(Carbon::parse($request->dateFilter[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->dateFilter[1])->addDay()->endOfDay();

      $sale = Sale::where('created_at', '>=', $from)->where('created_at', '<=', $to)->where('type','like', '%'.$request->type.'%')->get();


    }

    else {

      $sale = Sale::where('customer_name','like', '%'.$request->customer.'%')->get();
      }


      return $this->sendResponse($sale, 'Sales list');
  }

  public function destroy($id)
  {
    $response = DB::transaction(
      function () use (&$id) {
        $item = $this->item->findOrFail($id);
        $sale = $item;
        $location = $sale->location;

        $customer = $sale->customer;
        $sale->statuses()->delete();
        foreach ($sale->payStatuses as $payment) {
          $account = $payment->account;
          $lastTrans = Transaction::where('account_id', $account->id)->latest('id')->first();
          if ($lastTrans) {
            $closing_balance = $lastTrans->closing_balance;
          } else {
            $closing_balance = $account->balance;
          }
          $transaction = new Transaction;
          $transaction->account_id = $account->id;
          $transaction->account_name = $account->name;
          $transaction->opening_balance = $closing_balance;
          $transaction->amount = $payment->amount;
          $transaction->type = 'd';
          $transaction->closing_balance = $closing_balance - $payment->amount;
          $transaction->customer_id = $customer->id;
          $transaction->sale_id = $sale->id;
          $transaction->customer_name = $customer->name;
          $transaction->user_id = Auth::user()->id;
          $transaction->user_name = Auth::user()->name;
          $transaction->save();

          $account->balance = $account->balance - $payment->amount;
          $account->save();
        }
        $sale->payStatuses()->delete();
        if ($sale->status == "Completed") {
          /*foreach ($sale->items as $item) {
           $product = $item->product;
           $q = $location->products()->where('product_id', $product->id)->first()->pivot->quantity;
           $location->products()->updateExistingPivot($product->id, array('quantity' => $q+$item->quantity));
           }*/
        }
        $sale->items()->delete();
        $sale->delete();
        return $this->sendResponse($item, 'Product has been Deleted');
      }
    );
    return $response;
  }



  public function kotDirectPrint($order_id, $type)
  {
     $settings = Setting::first();
    $customer_name = null;
    if ($type == 'complete') {
      $sale = Sale::with('items', 'statuses', 'payStatuses', 'customer')->find($order_id);
      $items = $sale->items;
      if($sale->type == 'd')
      {
        exit ('some');
      }

      if ($sale->customer_id !== $settings->customer_id) {
        $customer_name = $sale->customer_name;
    }

    } else {

      $items = Table_item::with('product.kitchen')->where('table_id', $order_id)->whereNull('printed')->Orwhere('printed', 0)->get();
      Table_item::where('table_id',$order_id)->whereNull('printed')->Orwhere('printed',0)->update(['printed'=>1]);
      $table_customer_id = Table_item::with('product.kitchen')->where('table_id', $order_id)->first()->customer_id;
      if ($table_customer_id !== $settings->customer_id) {
        $customer_name = Customer::find($table_customer_id);
    }
    }


    $mpdf = new \Mpdf\Mpdf([
        'setAutoTopMargin' => 'stretch',
        'setAutoBottomMargin' => 'stretch',
        'autoMarginPadding' => 0,
        'margBuffer' => 0,
        'default_font' => 'freeserif',
        'default_font_size' => 40,
        'format' => [200, 150+(count($items)*10)]

      ]);


    if($type == 'complete')
      {
        if ($sale->type == 'd')
        $mpdf->WriteHTML("DINE IN\n");
      elseif ($sale->type == 's')
      $mpdf->WriteHTML("DELIVERY\n");
      elseif ($sale->type == 't')
      $mpdf->WriteHTML("TAKEWAY\n");
      if ($customer_name) {
        $mpdf->WriteHTML("CUSTOMER NAME: $customer_name \n");
      }
      $mpdf->WriteHTML("\n");
      $mpdf->WriteHTML("ORDER NUMBER: <span style='font-size:20px'> <b> $sale->id </b> </span> \n");

      $mpdf->WriteHTML("DATE / TIME: ");
      $mpdf->WriteHTML($sale->created_at->format('jS F Y h:i:s A'));
      }
      else
      {
        $mpdf->WriteHTML("DINE IN\n");
        $mpdf->WriteHTML("TABLE NUMBER: $order_id \n");
        if ($customer_name) {
            $mpdf->WriteHTML("CUSTOMER NAME: $customer_name \n");
          }

          $mpdf->WriteHTML("DATE / TIME: ");
          $mpdf->WriteHTML($items[0]->created_at->format('jS F Y h:i:s A'));
      }


      foreach($items as $i)
      {
        $mpdf->WriteHTML($i->food_name ." X ". (int)$i->quantity ."<br>");
      }






    $mpdf->Output('filename.pdf','F');
    $pdf = 'D:\xampp\htdocs\restazen\public\filename.pdf';


    $connector = new WindowsPrintConnector($settings->kot_printer);
    $printer = new Printer($connector);

    try {
        $pages = ImagickEscposImage::loadPdf($pdf);
        foreach ($pages as $page) {
            $printer -> bitImage($page);
        }
        $printer -> cut();
    } catch (Exception $e) {
        /*
         * loadPdf() throws exceptions if files or not found, or you don't have the
         * imagick extension to read PDF's
         */
        echo $e -> getMessage() . "\n";
    } finally {

        $printer -> close();
    }

  }


  public function kotDirectPrintold($order_id, $type)
  {

    $settings = Setting::first();
    $customer_name = null;
    if ($type == 'complete') {
      $sale = Sale::with('items', 'statuses', 'payStatuses', 'customer')->find($order_id);
      $items = $sale->items->groupBy('product.kitchen.kot_printer');
      if($sale->type == 'd')
      {
        exit ('some');
      }

      if ($sale->customer_id !== $settings->customer_id) {
        $customer_name = $sale->customer_name;
    }

    } else {

      $items = Table_item::with('product.kitchen')->where('table_id', $order_id)->whereNull('printed')->Orwhere('printed', 0)->get()->groupBy('product.kitchen.kot_printer');
      Table_item::where('table_id',$order_id)->whereNull('printed')->Orwhere('printed',0)->update(['printed'=>1]);
      $table_customer_id = Table_item::with('product.kitchen')->where('table_id', $order_id)->first()->customer_id;
      if ($table_customer_id !== $settings->customer_id) {
        $customer_name = Customer::find($table_customer_id);
    }
    }


    foreach ($items as $key => $value) {


      if ($key == '') {

        $connector = new WindowsPrintConnector($settings->kot_printer);
      } else {

        $connector = new WindowsPrintConnector($key);
      }
      $printer = new Printer($connector);
      $printer->setJustification(Printer::JUSTIFY_CENTER);
      $printer->setTextSize(2, 2);
      $printer->text("$settings->business_name \n");
      if($type == 'complete')
      {
        if ($sale->type == 'd')
        $printer->text("DINE IN\n");
      elseif ($sale->type == 's')
        $printer->text("DELIVERY\n");
      elseif ($sale->type == 't')
        $printer->text("TAKEWAY\n");
      if ($customer_name) {
        $printer->text("CUSTOMER NAME: $customer_name \n");
      }
      $printer->text("\n");
      $printer->text("ORDER NUMBER: $sale->id \n");
      $printer->setTextSize(1, 2);
      $printer->text("DATE / TIME: ");
      $printer->text($sale->created_at->format('jS F Y h:i:s A'));
      }
      else
      {
        $printer->text("DINE IN\n");
        $printer->text("TABLE NUMBER: $order_id \n");
        if ($customer_name) {
            $printer->text("CUSTOMER NAME: $customer_name \n");
          }
      $printer->setTextSize(1, 2);
      $printer->text("DATE / TIME: ");
      $printer->text($value[0]->created_at->format('jS F Y h:i:s A'));
      }

      //$printer->setJustification(Printer::JUSTIFY_LEFT);

      $printer->text("\n");
      $printer->text("\n");
      $printer->setEmphasis(false);
      $printer->text($this->addSpaces('Item', 38) . $this->addSpaces('Quantity', 11) . "\n");


      foreach ($value as $item) {

        //Current item ROW 1
        $name_lines = str_split($item->food_name .$item->code, 30);


          if($item->old_quantity !== null)
          {
            $name_lines = str_split($item->food_name.$item->code ."(MODIFIED OQ = $item->old_quantity)", 30);
            $x = Table_item::find($item->id);
            $x->old_quantity = null;
            $x->save();
          }



        foreach ($name_lines as $k => $l) {
          $l = trim($l);
          $name_lines[$k] = $this->addSpaces($l, 40);
        }

        $qtyx_price = str_split($item->quantity, 15);
        foreach ($qtyx_price as $k => $l) {
          $l = trim($l);
          $qtyx_price[$k] = $this->addSpaces($l, 8);
        }



        $counter = 0;
        $temp = [];
        $temp[] = count($name_lines);
        $temp[] = count($qtyx_price);

        $counter = max($temp);

        for ($i = 0; $i < $counter; $i++) {
          $line = '';
          if (isset($name_lines[$i])) {
            $line .= ($name_lines[$i]);
          }
          if (isset($qtyx_price[$i])) {
            $line .= ($qtyx_price[$i]);
          }

          $printer->text($line . "\n");
        }

        $printer->feed();
      }
      $printer->setEmphasis(false);

      $printer->cut();
      $printer->pulse();
      $printer->close();
    }












  }

  function addSpaces($string = '', $valid_string_length = 0)
  {
    if (strlen($string) < $valid_string_length) {
      $spaces = $valid_string_length - strlen($string);
      for ($index1 = 1; $index1 <= $spaces; $index1++) {
        $string = $string . ' ';
      }
    }

    return $string;
  }

  public function kotDirectPrint_OLD($order_id)
    {

      $settings = Setting::first();
      $sale = Sale::with('items','statuses','payStatuses','customer')->find($order_id);

      $connector = new WindowsPrintConnector($settings->kot_printer);
$printer = new Printer($connector);
$printer->setJustification(Printer::JUSTIFY_CENTER);
$printer -> setTextSize(2, 2);
$printer -> text("AFC THIRUVARUR\n");
if($sale->type == 'd')
$printer -> text("DINE IN\n");
elseif($sale->type == 's')
$printer -> text("DELIVERY\n");
elseif($sale->type == 't')
$printer -> text("TAKEWAY\n");
$printer -> text("\n");
//$printer->setJustification(Printer::JUSTIFY_LEFT);


$printer -> text("ORDER NUMBER: $sale->id \n");
$printer -> setTextSize(1, 2);
$printer -> text("DATE / TIME: ");
$printer -> text($sale->created_at->format('jS F Y h:i:s A'));
$printer -> text("\n");
$printer -> text("\n");
$printer->setEmphasis(false);
$printer->text($this->addSpaces('Item', 38) . $this->addSpaces('Quantity', 11) . "\n");




foreach ($sale->items as $item) {

    //Current item ROW 1
    $name_lines = str_split($item->food_name, 30);
    foreach ($name_lines as $k => $l) {
        $l = trim($l);
        $name_lines[$k] = $this->addSpaces($l, 40);
    }

    $qtyx_price = str_split($item->quantity, 15);
    foreach ($qtyx_price as $k => $l) {
        $l = trim($l);
        $qtyx_price[$k] = $this->addSpaces($l, 8);
    }



    $counter = 0;
    $temp = [];
    $temp[] = count($name_lines);
    $temp[] = count($qtyx_price);

    $counter = max($temp);

    for ($i = 0; $i < $counter; $i++) {
        $line = '';
        if (isset($name_lines[$i])) {
            $line .= ($name_lines[$i]);
        }
        if (isset($qtyx_price[$i])) {
            $line .= ($qtyx_price[$i]);
        }

        $printer->text($line . "\n");
    }

    $printer->feed();
}
$printer->setEmphasis(false);

$printer->cut();
$printer->pulse();
$printer->close();

    }





    public function salePayment_old1(Request $request)
  {

    $response = DB::transaction(
      function () use (&$request) {
        $register_id = null;

          $user = Auth::user();
          $register = $user->registers->where('status', 'open')->first();
          $location_id = $user->registers->where('status', 'open')->first()->location_id;
          $location = Location::find($location_id);
          $register_id = $register->id;
          if ($request->payment_method == 'Cash')
            $account_id = $user->cashAccounts[$location_id];
          elseif ($request->payment_method == 'Card')
            $account_id = $user->cardAccounts[$location_id];
          elseif ($request->payment_method == 'Online')
            $account_id = $user->onlineAccounts[$location_id];
          elseif ($request->payment_method == 'GPay')
            $account_id = $user->gpayAccounts[$location_id];
          elseif ($request->payment_method == 'Phonepe')
            $account_id = $user->phonepeAccounts[$location_id];
          elseif ($request->payment_method == 'Amazon Pay')
            $account_id = $user->amazonpayAccounts[$location_id];
          if ($request->payment_method !== "Unpaid")
            $account = Account::find($account_id);
            //return $account;





        $sale = Sale::find($request->current_sale_id);
        $sale->payment_status = 'Paid';
        $sale->save();
        $customer = Customer::find($sale->customer_id);
        //$account = Account::find($request->account_id);
        $supplier = Customer::find($sale->customer_id);
        if ($sale->payment_status !== "Unpaid") {
          $payStatus = new SalePayment;
          $payStatus->sale_id = $sale->id;
          $payStatus->account_id = $account->id;
          $payStatus->account_name = $account->name;
          $payStatus->status = $sale->payment_status;
          $payStatus->payment_method = $request->payment_method;
          $payStatus->user_id = Auth::user()->id;
          $payStatus->user_name = Auth::user()->name;
          if ($request->amount_paid) {

            $payStatus->amount = $sale->cash_balance;
          } else {
            $payStatus->amount = $sale->cash_balance;

          }
          $payStatus->save();

          $lastTrans = Transaction::where('account_id', $account->id)->latest('id')->first();
          if ($lastTrans) {
            $closing_balance = $lastTrans->closing_balance;
          } else {
            $closing_balance = $account->balance;
          }
          $transaction = new Transaction;
          $transaction->account_id = $account->id;
          $transaction->account_name = $account->name;
          $transaction->opening_balance = $closing_balance;
          $transaction->amount = $payStatus->amount;
          $transaction->type = 'c';
          $transaction->closing_balance = $closing_balance + $payStatus->amount;
          $transaction->customer_id = $supplier->id;
          $transaction->customer_name = $supplier->name;
          $transaction->user_id = Auth::user()->id;
          $transaction->user_name = Auth::user()->name;
          $transaction->save();

          $account->balance = $account->balance - $payStatus->amount;
          $account->save();
        }
      }
    );
    return $response;
  }
  public function salePayment(Request $request)
  {
    $response = DB::transaction(function () use (&$request) {
      $sale = Sale::find($request->current_sale_id);
     /* $sale->payment_status = $request->payment_status;

      $sale->save();*/

      $account = Account::find($request->account_id);
      $supplier = Customer::find($sale->customer_id);
      if($sale->payment_status !== "Paid")
      {
        $payStatus = new SalePayment;
        $payStatus->sale_id = $sale->id;
        $payStatus->account_id = $account->id;
        $payStatus->account_name = $account->name;
        $payStatus->status = $sale->payment_status;
        $payStatus->payment_method = $request->payment_method;
        $payStatus->user_id = Auth::user()->id;
        $payStatus->user_name = Auth::user()->name;
        if($request->amount_paid){
          $payStatus->amount = $request->amount_paid;
        }
        else {
          $payStatus->amount = $sale->grand_total-$sale->payStatuses()->sum('amount');
        }

          $payStatus->save();
          $sale->cash_balance = $sale->cash_balance - $request->amount_paid;
          if($sale->grand_total-$sale->payStatuses()->sum('amount') <= 0)
          {
            $sale->payment_status = "Paid";
            $sale->save();
          }
          else
          {
            $sale->payment_status = "Partially Paid";
            $sale->save();
          }
          $payStatus->status = $sale->payment_status;
          $payStatus->save();
          $lastTrans = Transaction::where('account_id',$account->id)->latest('id')->first();
          if($lastTrans)
          {
            $closing_balance = $lastTrans->closing_balance;
          }else {
          $closing_balance = $account->balance;
          }
          $transaction = new Transaction;
          $transaction->account_id = $account->id;
          $transaction->account_name = $account->name;
          $transaction->opening_balance = $closing_balance;
          $transaction->amount = $payStatus->amount;
          $transaction->type = 'c';
          $transaction->closing_balance = $closing_balance + $payStatus->amount;
          $transaction->customer_id = $supplier->id;
          $transaction->customer_name = $supplier->name;
          $transaction->user_id = Auth::user()->id;
          $transaction->user_name = Auth::user()->name;
          $transaction->save();

          $account->balance = $account->balance - $payStatus->amount;
          $account->save();
      }
    });
    return $response;
  }


}
