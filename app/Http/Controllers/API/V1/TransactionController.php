<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Account;
use Illuminate\Http\Request;
use Auth;
use DB;
class TransactionController extends BaseController
{
    protected $item = '';

    public function __construct(Transaction $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index(Request $request)
    {
      $items = $this->item->latest()->paginate(10);

        if (($request['selectedAcc'] != "null") && ( $request['selectedAcc'] != null)) {

          $items = $this->item->where('account_id',$request['selectedAcc'])->latest()->paginate(10);

          }
      return $this->sendResponse($items, 'Transaction list');
    }

    public function getAccount(Request $request){

      $acc = Account::get();
      return $this->sendResponse($acc, '');
    }


    public function filterAccount($search){
      $items = $this->item->where('account_id', $search)->latest()->paginate(10);

      return $this->sendResponse($items, '');
  }


    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);
      $transaction = $item;
      $account = $transaction->account;
      if($transaction->type == 'c')
      {
        $account->balance = $account->balance-$transaction->amount;
        $account->save();
      }
      else {
        $account->balance = $account->balance+$transaction->amount;
        $account->save();
      }
      $transaction->delete();
      return $this->sendResponse($item, 'Product has been Deleted');
    }

    public function transfer(Request $request){

      $response = DB::transaction(function () use (&$request) {
      $fromAccount = Account::find($request->fromAcc);
      $toAccount = Account::find($request->toAcc);


    //   $lastTrans = Transaction::where('account_id',$fromAccount->id)->latest('id')->first();
    //   if($lastTrans)
    //   {
    //     $closing_balance = $lastTrans->closing_balance;
    //   }else {
    //   $closing_balance = $fromAccount->balance;
    //   }
      $transaction = new Transaction;
      $transaction->account_id = $fromAccount->id;
      if($request->description){
          $transaction->description = $request->description;
      }
      else{
          $transaction->description = "Account Transfer";
      }
      $transaction->account_name = $fromAccount->name;
      $transaction->opening_balance = $fromAccount->balance;
      $transaction->amount = $request->amount;
      $transaction->type = 'd';
      $transaction->closing_balance = $fromAccount->balance - $request->amount;
      $transaction->user_id = Auth::user()->id;
      $transaction->user_name = Auth::user()->name;
      $transaction->save();

      $fromAccount->balance = $transaction->closing_balance;
      $fromAccount->save();



    //   $lastTrans = Transaction::where('account_id',$toAccount->id)->latest('id')->first();
    //   if($lastTrans)
    //   {
    //     $closing_balance = $lastTrans->closing_balance;
    //   }else {
    //   $closing_balance = $toAccount->balance;
    //   }
      $transaction = new Transaction;
      $transaction->account_id = $toAccount->id;
      if($request->description){
          $transaction->description = $request->description;
      }
      else{
          $transaction->description = "Account Transfer";
      }
      $transaction->account_name = $toAccount->name;
      $transaction->opening_balance = $toAccount->balance;
      $transaction->amount = $request->amount;
      $transaction->type = 'c';
    //   $transaction->closing_balance = $closing_balance + $request->amount;
      $transaction->closing_balance = $toAccount->balance + $request->amount;
      $transaction->user_id = Auth::user()->id;
      $transaction->user_name = Auth::user()->name;
      $transaction->save();

      $toAccount->balance = $transaction->closing_balance;
      $toAccount->save();

              });
              }





}
