<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\PurchaseStatus;
use App\Models\PurchasePayment;
use App\Models\Setting;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class PurchaseReturnController extends BaseController
{
    protected $item = '';

    public function __construct(PurchaseReturn $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Purchase Return list');
    }

    public function getRetrun($return_id)
    {
      $return = PurchaseReturn::with('items')->find($return_id);
      //$return->gsts = $return->items->groupby('gst_percentage');
    /*  $return->totalDiscount = 0;
      foreach ($return->items as $i) {
        $return->totalDiscount = $return->totalDiscount + $i->discount * $i->quantity;
      }*/
      return $this->sendResponse($return, 'Retrun View');
    }


    public function store(Request $request)
    {

      $response = DB::transaction(function () use (&$request) {

          $purchase = Purchase::find($request->id);
          $location = Location::find($purchase->location_id);
          if($purchase->payment_status !== 'Unpaid')
          $account = Account::find($purchase->payStatuses->first()->account_id);
          $user = Auth::user();

          $setting = Setting::first();
          $return = new PurchaseReturn;
          $return->purchase_id = $purchase->id;
          $return->supplier_id = $purchase->supplier_id;
          $return->supplier_name = $purchase->supplier_name;

          $return->total = $request->total_subtotal;
          $return->gst = $request->grand_tax_total;
          $return->shipping = $request->shipping;
          $return->discount = $request->total_discount;

          if ($purchase->status == 'Ordered')
            $return->status = 'Cancelled';

          elseif ($purchase->status == 'Received')
            $return->status = 'Returned';


          if($purchase->payment_status == 'Unpaid')
            $return->payment_status = 'Cancelled';
          else
            $return->payment_status = 'Refunded';



          $return->grand_total = $request->grand_total;

          $return->location_id = $purchase->location_id;
          $return->location_name = $purchase->location_name;


          $return->user_id = Auth::user()->id;
          $return->user_name = Auth::user()->name;
          $return->save();




        foreach ($request->items as $i) {
          if($i['r_quantity'] !== null AND $i['r_quantity'] > 0 AND $i['r_quantity'])
          {
          $itm = PurchaseReturnItem::create([
            'product_id' => $i['product_id'],
            'product_name' => $i['product_name'],
            'hsn' => $i['hsn'],

            'quantity' => $i['r_quantity'],
            'subtotal' => $i['subtotal'],


            'gst' => $i['gst'],
            'gst_percentage' => $i['gst_percentage'],
            'discount' =>$i['discount'],
            'cost' =>$i['cost'],
            'purchase_return_id' => $return->id,

          //  'code' => $i['code'],
          //  'unit' => $i['unit'],
          //  'expiry_date' => $i['expiry_date'],
          //  'batch' => $i['batch'],
          ]);
        }
          if($itm)
          {
            $product = Product::find($itm->product_id);

          if($purchase->status == "Received")
          {
            $q = DB::table('location_product')
                    ->where('location_id', $location->id)
                    ->where('product_id', $product->id);
                      $quan = $itm->quantity;
                    $q->update(['quantity' => $q->first()->quantity-$quan]);
                  //  ->where('batch', $i['batch'])
                  //  ->where('code', $i['code'])
                //    ->where('expiry_date', $i['expiry_date'])
                  //  ->where('cost', $i['cost']);

                  /*  if($q->get()->count() > 0)
                    {
                      //here we need to do unit conversion and add the quantity
                      $f = $q->first();
                      $existing_unit = $f->unit;
                      $new_unit = $i['unit'];
                      if($existing_unit == $new_unit)
                      {

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

                    }*/

          }
          }

        }

        if($purchase->payment_status !== "Unpaid")
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
            $transaction->type = 'c';
            $transaction->closing_balance = $account->balance + $return->grand_total;
            $transaction->supplier_id = $purchase->supplier_id;
            $transaction->purchase_id = $purchase->id;
            $transaction->purchase_return_id = $return->id;
            $transaction->supplier_name = $purchase->supplier_name;
            $transaction->user_id = Auth::user()->id;
            $transaction->user_name = Auth::user()->name;
            $transaction->save();

            $account->balance = $account->balance - $return->grand_total;
            $account->save();
        }
        return $this->sendResponse($return, 'Return Created Successfully');
    });
    return $response;
  }


}
