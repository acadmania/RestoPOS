<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;

use App\Models\Location;
use App\Models\Supplier;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\PurchaseStatus;
use Carbon\Carbon;
use App\Models\PurchasePayment;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;

class PurchaseController extends BaseController
{
    protected $item = '';

    public function __construct(Purchase $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Purchase list');
    }

    public function getPurchase($purchase_id)
    {
      $purchase = Purchase::find($purchase_id);

      return $this->sendResponse([$purchase,$purchase->items,$purchase->statuses,$purchase->payStatuses], 'Purchase View');
    }

    public function purchasePayment(Request $request)
    {
      $response = DB::transaction(function () use (&$request) {
      $purchase = Purchase::find($request->current_purchase_id);
      $purchase->payment_status = $request->payment_status;
      $purchase->save();
      $account = Account::find($request->account_id);
      $supplier = Supplier::find($purchase->supplier_id);
      if($purchase->payment_status !== "Unpaid")
      {
        $payStatus = new PurchasePayment;
        $payStatus->purchase_id = $purchase->id;
        $payStatus->account_id = $account->id;
        $payStatus->account_name = $account->name;
        $payStatus->status = $purchase->payment_status;
        $payStatus->user_id = Auth::user()->id;
        $payStatus->user_name = Auth::user()->name;
        if($request->amount_paid){
          $payStatus->amount = $request->amount_paid;
        }
        else {
          $payStatus->amount = $purchase->grand_total-$purchase->payStatuses()->sum('amount');
        }
          $payStatus->save();

        //   $lastTrans = Transaction::where('account_id',$account->id)->latest('id')->first();
        //   if($lastTrans)
        //   {
        //     $closing_balance = $lastTrans->closing_balance;
        //   }else {
        //   $closing_balance = $account->balance;
        //   }
          $transaction = new Transaction;
          $transaction->account_id = $account->id;
          $transaction->account_name = $account->name;
          $transaction->opening_balance = $account->balance;
          $transaction->amount = $payStatus->amount;
          $transaction->type = 'd';
          $transaction->closing_balance = $account->balance - $payStatus->amount;
          $transaction->supplier_id = $supplier->id;
          $transaction->purchase_id = $purchase->id;
          $transaction->supplier_name = $supplier->name;
          $transaction->user_id = Auth::user()->id;
          $transaction->user_name = Auth::user()->name;
          $transaction->save();

          $account->balance = $account->balance - $payStatus->amount;
          $account->save();
      }
      });
      return $response;
    }

    public function filterPurchase(Request $request)
    {
        $from = Carbon::parse(Carbon::parse(isset($request->dateFilter[0]) ? $request->dateFilter[0] : null))->addDay()->startOfDay();
        $to = Carbon::parse(isset($request->dateFilter[1]) ? $request->dateFilter[1] : null)->addDay()->endOfDay();

        $purchase = Purchase::query();

        if($request->dateFilter AND $request->dateFilter[1])
        $purchase = $purchase->where('created_at', '>=', $from)->where('created_at', '<=', $to);

        if($request->supplier)
        $purchase = $purchase->where('supplier_name','like', '%'.$request->supplier.'%');

        if($request->payment_status)
        $purchase = $purchase->where('payment_status',$request->payment_status);

        $purchase = $purchase->get();

        return $this->sendResponse($purchase, 'Purchase list');
    }

    public function getProduct($material_id,$supplier_id,$location_id)
    {
      $material = Product::find($material_id);

      $supplier = Supplier::find($supplier_id);
      $location = Location::find($location_id);

      /*if ($supplier->products()->where('product_id', $material->id)->where('location_id', $location->id)->exists()) {
        $mat = $supplier->products()->where('product_id', $material->id)->where('location_id', $location->id)->first();
        $price = $mat->pivot->cost;
        $cost_include_gst = $mat->cost_include_gst;
      }*/

      $q = DB::table('location_product')
              ->where('supplier_id', $supplier->id)
              ->where('product_id', $material->id)
              ->where('location_id', $location->id);



      if ($q->get()->count() > 0) {
        $mat = $q->latest()->first();
        $cost = $material->cost;
        $cost_include_gst = $mat->cost_include_gst;
        $price_include_gst = $mat->price_include_gst;
        $gst_percentage = $mat->gst_percentage;
        $sale_price = $mat->sale_price;
        $batch = $mat->batch;
        $price = $mat->price;
//        $code = $mat->code;
        $expiry_date = $mat->expiry_date;
      }
      else {
        $cost = $material->cost;
        $cost_include_gst = $material->cost_include_gst;
        $price_include_gst = $material->price_include_gst;
        $gst_percentage = $material->gst_percentage;
        $sale_price = $material->sale_price;
        $price = $material->price;
        $batch = null;
        $code = null;
        $expiry_date = null;
      }
      $tax_total = 0;
      if($material->gst_percentage)
      {
        if($cost_include_gst == 1)
        {
          $tax_total = $cost*$material->gst_percentage/(100+$material->gst_percentage);
          $cost = $cost-$tax_total;
        }
        else {
          $tax_total = $cost*$material->gst_percentage/(100);
          $cost = $cost;
        }
      }


          /*   $units = DB::table('product_unit')
              ->where('product_id', $material->id);
 if($units->get()->count() > 0)
      {
        $d = $units->select('unit_a_id','unit_b_id')->get()->pluck('unit_a_id','unit_b_id');
        foreach ($d as $k => $v) {
          $uts[] = $k;
          $uts[] = $v;
        }

        $ut = ProductUnit::find(array_unique($uts));
      }
      else {
        $ut = null;
      }*/

      return [
       array(
         'price' => $price,
         'cost' => $cost,
         'tax_total' => $tax_total,
         'gst_percentage' => $gst_percentage,
         'cost_include_gst' => $cost_include_gst,
         'price_include_gst' => $price_include_gst,
         'sale_price' => $sale_price,
         'batch' => $batch,
      //   'code' => $code,
         'expiry_date' => $expiry_date,
      //   'units' => $ut,
       ),
       $material
      ];
    }

    public function productPurchase(Request $request)
    {
      $response = DB::transaction(function () use (&$request) {
      $supplier = Supplier::find($request->supplier_id);
      $account = Account::find($request->account_id);
      $location = Location::find($request->location_id);

      if($request->purchase_id)
      {
          $purchase = Purchase::find($request->purchase_id);
          $purchase->items()->delete();
          $purchase->status = $request->order_status;
          $purchase->payment_status = $request->payment_status;

          $purchase->save();
      }
      else {
        $purchase = $this->item->create([
          'supplier_id' => $supplier->id,
          'supplier_name' => $supplier->name,
          'supplier_tax_number' => $supplier->tax_number,
          'total' => $request->total_subtotal,
          'gst' => $request->grand_tax_total,
          'shipping' => $request->shipping,
          'discount' => $request->total_discount,
          'grand_total' => $request->grand_total,
          'status' => $request->order_status,
          'payment_status' => $request->payment_status,
          'location_id' => $location->id,
          'location_name' => $location->name,
          'user_id' => Auth::user()->id,
          'user_name' => Auth::user()->name,
          'created_at' => $request->datetime,
          'updated_at' => $request->datetime,
          ]);
      }
        $pStatus = new PurchaseStatus();
        $pStatus->purchase_id = $purchase->id;
        $pStatus->status = $purchase->status;
        $pStatus->user_id = Auth::user()->id;
        $pStatus->user_name = Auth::user()->name;
        $pStatus->save();

        foreach ($request->items as $i) {

          $itm = PurchaseItem::create([
            'product_id' => $i['product_id'],
            'product_name' => $i['product_name'],
            'hsn' => $i['hsn'],
          //  'unit' => $i['purchase_unit'],
          //  'sale_unit' => $i['sale_unit'],
            'quantity' => $i['quantity'],
            'subtotal' => $i['subtotal'],
            'cost' => $i['cost'],
            'discount' => $i['discount'],
            'gst_percentage' => $i['gst_percentage'],
            'gst' => $i['tax_total'],

            'batch' => $i['batch'],
            'expiry_date' => $i['expiry_date'],
            'purchase_id' => $purchase->id,
            'created_at' => $request->datetime,
          'updated_at' => $request->datetime,
          ]);

          if($itm)
          {
            $material = Product::find($itm->product_id);
          /*  if ($supplier->products()->where('product_id', $material->id)->exists()) {
                          $supplier->products()->updateExistingPivot($material->id, array(
                            'cost' => $itm->cost,

                          ));
                      }
          else {
            $supplier->products()->attach(
                $material,
                [
                'cost' => $itm->cost,

                ]
            );
          }*/

          if($purchase->status == "Received")
          {
                $q = DB::table('location_product')
                        ->where('supplier_id', $supplier->id)
                        ->where('location_id', $location->id)
                        ->where('product_id', $material->id);

                        //->where('cost_include_gst', $i['cost_include_gst'])
                        //->where('price_include_gst', $i['price_include_gst'])
                        //->where('sale_price', $i['sale_price']);

                if($q->get()->count() > 0)
                {
                  //here we need to do unit conversion and add the quantity
                  $f = $q->first();

                    $quan = $itm->quantity;



                  $q->update(['quantity' => $q->first()->quantity+$quan]);
                }

          else {
            $location->products()->attach(
                $material,
                [
                'quantity' => $itm->quantity,
                'batch' => $i['batch'],

                'expiry_date' => $i['expiry_date'],
                'gst_percentage' => $i['gst_percentage'],

                'cost' => $i['cost'],

                'supplier_id' => $supplier->id
                ]
            );
          }
          }
          }

        }

        if($purchase->payment_status !== "Unpaid")
        {
          $payStatus = new PurchasePayment;
          $payStatus->purchase_id = $purchase->id;
          $payStatus->account_id = $account->id;
          $payStatus->account_name = $account->name;
          $payStatus->status = $purchase->payment_status;
          $payStatus->user_id = Auth::user()->id;
          $payStatus->user_name = Auth::user()->name;
          if($request->amount_paid){
            $payStatus->amount = $request->amount_paid;
          }
          else {
            $payStatus->amount = $purchase->grand_total;
          }
            $payStatus->save();

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
            $transaction->amount = $payStatus->amount;
            $transaction->type = 'd';
            $transaction->closing_balance = $account->balance - $payStatus->amount;
            $transaction->supplier_id = $supplier->id;
            $transaction->supplier_name = $supplier->name;
            $transaction->purchase_id = $purchase->id;
            $transaction->user_id = Auth::user()->id;
            $transaction->user_name = Auth::user()->name;
            $transaction->save();

            $account->balance = $account->balance - $payStatus->amount;
            $account->save();
        }
        return $this->sendResponse($purchase, 'Purchase Created Successfully');
    });
    return $response;
  }



    function productPurchaseReceive($id)
    {
      $response = DB::transaction(function () use (&$id) {
      $purchase = Purchase::find($id);
      $location = Location::find($purchase->location_id);
      $purchase->status = "Received";
      $purchase->save();

      $pStatus = new PurchaseStatus();
      $pStatus->purchase_id = $purchase->id;
      $pStatus->status = $purchase->status;
      $pStatus->user_id = Auth::user()->id;
      $pStatus->user_name = Auth::user()->name;
      $pStatus->save();

      foreach($purchase->items as $itm)
      {
        $material = Product::find($itm->product_id);
      if ($location->products()->where('product_id', $material->id)->exists()) {
          $q = $location->products()->where('product_id', $material->id)->first()->pivot->quantity;
          $location->products()->updateExistingPivot($material->id, array('quantity' => $q+$itm->quantity));
                }
    else {
      $location->products()->attach(
          $material,
          [

          'quantity' => $itm->quantity
          ]
      );
    }
  }
});
return $response;
}

    public function destroy($id)
    {
      $response = DB::transaction(function () use (&$id) {
      $item = $this->item->findOrFail($id);
      $purchase = $item;
      $location = $purchase->location;
      $purchase->statuses()->delete();
      foreach($purchase->payStatuses as $payment)
      {
        $account = $payment->account;
        $a = Transaction::where('purchase_id',$purchase->id)->first();
        $a->purchase_id = null;
        $a->save();
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
        $transaction->amount = $payment->amount;
        $transaction->type = 'c';
        $transaction->closing_balance = $account->balance + $payment->amount;
        $transaction->supplier_id = $purchase->supplier_id;
        $transaction->supplier_name = $purchase->supplier_name;
        $transaction->user_id = Auth::user()->id;
        $transaction->user_name = Auth::user()->name;
        $transaction->save();

        $account->balance = $account->balance + $payment->amount;
        $account->save();
      }
      $purchase->payStatuses()->delete();
      if($purchase->status == "Received")
      {
      foreach ($purchase->items as $item) {
        $product = $item->product;

              $q = $location->products()->where('product_id', $product->id)->first()->pivot->quantity;
              $location->products()->updateExistingPivot($product->id, array('quantity' => $q-$item->quantity));
      }
      }
        $purchase->items()->delete();
        $purchase->delete();
        return $this->sendResponse($item, 'Product has been Deleted');
    });
    return $response;
  }
}
