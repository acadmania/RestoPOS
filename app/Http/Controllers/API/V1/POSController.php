<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Location;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Artisan;
use App\Models\Sale;
use App\Models\SaleReturn;
use Illuminate\Support\Arr;
use DB;
class POSController extends BaseController
{
  public function __construct()
  {
      $this->middleware('auth:api');

  }
  public function closeRequest()
  {
    $user = Auth::user();
    $register = $user->registers->where('status','open')->first();


    $cash = Sale::where('register_id',$register->id)
    ->where('status','Completed')->wherehas('payStatuses',function($q){
    $q->where('payment_method','Cash');
  })->sum('grand_total') ;

    $card = Sale::where('register_id',$register->id)
    ->where('status','Completed')->wherehas('payStatuses',function($q){
    $q->where('payment_method','Card');
    })->sum('grand_total');

    $online = Sale::where('register_id',$register->id)
    ->where('status','Completed')->wherehas('payStatuses',function($q){
    $q->where('payment_method','Online');
    })->sum('grand_total');

    $gpay = Sale::where('register_id',$register->id)
    ->where('status','Completed')->wherehas('payStatuses',function($q){
    $q->where('payment_method','Gpay');
    })->sum('grand_total');

    $phonepe = Sale::where('register_id',$register->id)
    ->where('status','Completed')->wherehas('payStatuses',function($q){
    $q->where('payment_method','Phonepe');
  })->sum('grand_total');

    $amazonpay = Sale::where('register_id',$register->id)
    ->where('status','Completed')->wherehas('payStatuses',function($q){
    $q->where('payment_method','Amazon Pay');
    })->sum('grand_total');

    //$SaleReturns = SaleReturn::where('register_id',$register->id)->sum('grand_total');

    return array(

      'Cash On Hand' => $register->cash_in_hand,
      'Cash Sales' => $cash,
      'Card Sales' => $card,
      'Online Sales' => $online,
      'Gpay Sales' => $gpay,
      'Phonepe Sales' => $phonepe,
      'Amazonpay Sales' => $amazonpay,
      'Total Sales' => $amazonpay + $phonepe + $gpay + $online + $card + $cash,
      'Total Cash Amount' => $cash + $register->cash_in_hand,
      'Total Non-Cash Amount' => $amazonpay + $phonepe + $gpay + $online + $card,
      //'Return Sales' => $SaleReturns,
  );
  }

  public function close()
  {
    $user = Auth::user();
    $register = $user->registers->where('status','open')->first();
    $register->status = 'close';
    $register->save();
  
  }

    public function posList()
    {
      //here is the problem
      $user = Auth::user();
      //$openedRegisters = Register::where('user_id',$user->id)->where('status','open')->wherein('location_id',$user->locations)->get();
      $openedRegisters = Register::where('user_id',$user->id)->where('status','open')->wherein('location_id',Arr::pluck($user->locations, 'id'))->get();
      $userBranches = Location::find(Arr::pluck($user->locations, 'id'));

      foreach ($userBranches as $ub) {
        $status = 'close';
        $orId = null;
        foreach($openedRegisters as $or)
        {
          if($or->status == 'open' AND $ub->id == $or->location_id)
          {
            $orId = $or->id;
            $status = 'open';
          }
        }
        $result[] = array(
          'name' => $ub->name,
          'status' => $status,
          'branchId' => $ub->id,
          'registerId' => $orId,
      );
      }
        return $result;

    }

    public function checkRegisters()
    {
      $user = Auth::user();
      if($user->registers->where('status','open')->first())
      {
        return 1;
      }
      else {
        return 0;
      }
    }
    public function openRegister(Request $request)
    {

      $r = new Register;
      $r->user_id = Auth::user()->id;
      $r->location_id = $request->branchId;
      $r->cash_in_hand = $request->cash_on_hand;
      $r->status = 'open';
      $r->total_cash = 0;
      $r->total_others = 0;
      $r->total_amount = $r->cash_in_hand;
      $r->save();


    }

/*    public function addSale(Request $request)
    {

        $customer = Customer::find($request->customer_id);
        $account = Account::find($request->account_id);
        $location = Location::find($request->location_id);
        $setting = Setting::first();
        $sale = $this->item->create([
          'customer_id' => $customer->id,
          'customer_name' => $customer->name,

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
          ]);
          if($setting)
          {
            if($setting->amountpoint)
            {
              $point = new Point;
              $point->customer_id = $customer->id;
              $point->customer_name = $customer->name;
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
          $pStatus->status = $sale->status;
          $pStatus->user_id = Auth::user()->id;
          $pStatus->user_name = Auth::user()->name;
          $pStatus->save();

          foreach ($request->items as $i) {
            $itm = SaleItem::create([
              'product_id' => $i['product_id'],
              'product_name' => $i['product_name'],
              'hsn' => $i['hsn'],
              'quantity' => $i['quantity'],
              'subtotal' => $i['subtotal'],
              'price' => $i['price'],
              'gst' => $i['tax_total'],
              'gst_percentage' => $i['gst_percentage'],

              'sale_id' => $sale->id,
            ]);

            if($itm)
            {
              $product = Product::find($itm->product_id);

            if($sale->status == "Completed")
            {
              if ($location->products()->where('product_id', $product->id)->exists()) {
                  $q = $location->products()->where('product_id', $product->id)->first()->pivot->quantity;
                  $location->products()->updateExistingPivot($product->id, array('quantity' => $q-$itm->quantity));
                        }
            else {
              $location->products()->attach(
                  $product,
                  [

                  'quantity' => 0-$itm->quantity
                  ]
              );
            }
            }
            }

          }

          if($sale->payment_status !== "Unpaid")
          {
            $payStatus = new SalePayment;
            $payStatus->sale_id = $sale->id;
            $payStatus->account_id = $account->id;
            $payStatus->account_name = $account->name;
            $payStatus->status = $sale->payment_status;
            $payStatus->user_id = Auth::user()->id;
            $payStatus->user_name = Auth::user()->name;
            if($request->amount_paid){
              $payStatus->amount = $request->amount_paid;
            }
            else {
              $payStatus->amount = $sale->grand_total;
            }
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
              $transaction->customer_id = $customer->id;
              $transaction->customer_name = $customer->name;
              $transaction->user_id = Auth::user()->id;
              $transaction->user_name = Auth::user()->name;
              $transaction->save();

              $account->balance = $account->balance + $payStatus->amount;
              $account->save();
          }
          return $this->sendResponse($sale, 'Sale Created Successfully');
    }*/
}
