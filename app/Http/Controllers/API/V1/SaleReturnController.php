<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Location;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\SaleStatus;
use App\Models\SalePayment;
use App\Models\Setting;
use App\Models\Point;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class SaleReturnController extends BaseController
{
    protected $item = '';

    public function __construct(SaleReturn $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {

        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Sale Return list');
    }

    public function getRetrun($return_id)
    {
      $return = SaleReturn::with('items')->find($return_id);
      $return->gsts = $return->items->groupby('gst_percentage');
      $return->totalDiscount = 0;
      foreach ($return->items as $i) {
        $return->totalDiscount = $return->totalDiscount + $i->discount * $i->quantity;
      }
      return $this->sendResponse($return, 'Retrun View');
    }



  public function searchList($search)
  {
      $items = $this->item->where('id', $search)->paginate(10);
      return $this->sendResponse($items, 'Sale list Search');
  }


    public function store(Request $request)
    {

      $response = DB::transaction(function () use (&$request) {
          $user = Auth::user();
          $register = $user->registers->where('status','open')->first();
          $sale = Sale::find($request->id);
          $location = Location::find($sale->location_id);
          $account = Account::find($sale->payStatuses->first()->account_id);
          $user = Auth::user();

          $setting = Setting::first();
          $return = new SaleReturn;
          $return->sale_id = $sale->id;
          $return->register_id = $register->id;
          $return->customer_id = $sale->customer_id;
          $return->customer_name = $sale->customer_name;
          $return->igst = $request->igst;
          $return->total = $request->total_subtotal;
          $return->gst = $request->grand_tax_total;
          $return->shipping = $request->shipping;
          $return->discount = $request->total_discount;

          if ($sale->status == 'Ordered')
            $return->status = 'Cancelled';

          elseif ($sale->status == 'Completed')
            $return->status = 'Returned';


          if($sale->payment_status == 'Unpaid')
            $return->payment_status = 'Cancelled';
          else
            $return->payment_status = 'Refunded';



          $return->grand_total = $request->grand_total;

          $return->location_id = $sale->location_id;
          $return->location_name = $sale->location_name;


          $return->user_id = Auth::user()->id;
          $return->user_name = Auth::user()->name;
          $return->save();

        if($setting)
        {
          if($setting->amountpoint)
          {
            $customer = Customer::find($return->customer_id);
            $point = new Point;
            $point->customer_id = $return->customer_id;
            $point->customer_name = $return->customer_name;
            $lastTrans = Point::where('customer_id',$customer->id)->latest('id')->first();
            if($lastTrans)
            {
              $closing_balance = $lastTrans->closing_balance;
            }else {
            $closing_balance = $customer->points;
            }
            $point->opening_balance = $closing_balance;
            $points = $setting->amountpoint * $request->grand_total;
            $point->points = $points;
            $point->type = 'd';
            $point->closing_balance = $point->opening_balance - $points;
            $point->sale_return_id = $return->id;
            $point->user_id = Auth::user()->id;
            $point->user_name = Auth::user()->name;
            $point->save();

            $customer->points = $point->closing_balance;
            $customer->save();
          }
        }


        foreach ($request->items as $i) {
          if($i['r_quantity'] !== null AND $i['r_quantity'] > 0 AND $i['r_quantity'])
          {
          $itm = SaleReturnItem::create([
            'product_id' => $i['product_id'],
            'product_name' => $i['product_name'],
            'hsn' => $i['hsn'],
            'code' => $i['code'],
            'quantity' => $i['r_quantity'],
            'subtotal' => $i['subtotal'],
            'price' => $i['price'],
            'selling_price' => $i['selling_price'],
            'gst' => $i['gst'],
            'gst_percentage' => $i['gst_percentage'],
            'discount' =>$i['discount'],
            'cost' =>$i['cost'],
            'sale_return_id' => $return->id,
            'unit' => $i['unit'],
            'expiry_date' => $i['expiry_date'],
            'batch' => $i['batch'],
          ]);
        }
          if($itm)
          {
            $product = Product::find($itm->product_id);

          if($sale->status == "Completed")
          {

            $q = DB::table('location_product')
                    ->where('location_id', $location->id)
                    ->where('product_id', $product->id)
                    ->where('batch', $i['batch'])
                    ->where('code', $i['code'])
                    ->where('expiry_date', $i['expiry_date'])
                    ->where('price', $i['price'])
                    ->where('sale_price', $i['selling_price'])
                    ->where('cost', $i['cost']);

                    if($q->get()->count() > 0)
                    {
                      //here we need to do unit conversion and add the quantity
                      $f = $q->first();
                      $existing_unit = $f->unit;
                      $new_unit = $i['unit'];

                      if($existing_unit == $new_unit)
                      {
                        $quan = $itm->quantity;
                      }

                  else {
                        foreach ($i['product']['units'] as $unittable) {
                        if ($unittable['pivot']['unit_a_id'] == $existing_unit
                        AND $unittable['pivot']['unit_b_id'] == $new_unit) {
                          $quan = $itm->quantity / $unittable['pivot']['unit_b_value'];
                          break;
                        }
                        elseif ($unittable['pivot']['unit_a_id'] == $new_unit
                        AND $unittable['pivot']['unit_b_id'] == $existing_unit) {
                          $quan = $itm->quantity * $unittable['pivot']['unit_b_value'];
                          break;
                        }
                        //multi level conversion is not supported.  now it support one level unit conversion
                        }
                      }
                      $q->update(['quantity' => $q->first()->quantity+$quan]);
                    }


        /*    if ($location->products()->where('product_id', $product->id)->exists()) {
                $q = $location->products()->where('product_id', $product->id)->first()->pivot->quantity;
                $location->products()->updateExistingPivot($product->id, array('quantity' => $q+$itm->quantity));
                      }
          else {
            $location->products()->attach(
                $product,
                [

                'quantity' => 0+$itm->quantity
                ]
            );
          }*/
        }
          }

        }

        if($sale->payment_status !== "Unpaid")
        {

            // $lastTrans = Transaction::where('account_id',$account->id)->latest('id')->first();
            // if($lastTrans)
            // {
            //   $closing_balance = $lastTrans->closing_balance;
            // }else {
            // $closing_balance = $account->balance;
            // }
            $transaction = new Transaction;
            $transaction->account_id = $account->id;
            $transaction->account_name = $account->name;
            $transaction->opening_balance = $account->balance;
            $transaction->amount = $return->grand_total;
            $transaction->type = 'd';
            $transaction->closing_balance = $account->balance - $return->grand_total;
            $transaction->customer_id = $sale->customer_id;
            $transaction->sale_id = $sale->id;
            $transaction->sale_return_id = $return->id;
            $transaction->customer_name = $sale->customer_name;
            $transaction->user_id = Auth::user()->id;
            $transaction->user_name = Auth::user()->name;
            $transaction->save();

            $account->balance = $account->balance + $return->grand_total;
            $account->save();
        }
        return $this->sendResponse($sale, 'Return Created Successfully');
    });
    return $response;
  }


}
