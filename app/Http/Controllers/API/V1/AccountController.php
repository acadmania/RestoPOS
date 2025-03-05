<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Auth;
use DB;
class AccountController extends BaseController
{
    protected $item = '';

    public function __construct(Account $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Account list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Account list');
    }

    public function store(Request $request)
    {

      $item = $this->item->create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'balance' => $request->get('balance'),


        ]);

        return $this->sendResponse($item, 'Account Created Successfully');
    }


    public function transfer(Request $request)
    {
      $response = DB::transaction(function () use (&$request) {
      $from_account = Account::find($request->from_account);
      $to_account = Account::find($request->to_account);
      $from_transaction = new Transaction;
      $from_transaction->account_id = $from_account->id;
      $from_transaction->account_name = $from_account->name;
      $from_transaction->amount = $request->amount;
      $from_transaction->description = 'Amount transferred to Account: '.$to_account->name;
      $from_transaction->opening_balance = $from_account->balance;
      $from_transaction->type = 'd';
      $from_transaction->closing_balance = $from_account->balance - $request->amount;
      $from_transaction->user_id = Auth::user()->id;
      $from_transaction->user_name = Auth::user()->name;
      $from_transaction->save();
      $from_account->balance = $from_account->balance - $request->amount;
      $from_account->save();

      $to_transaction = new Transaction;
      $to_transaction->account_id = $to_account->id;
      $to_transaction->account_name = $to_account->name;
      $to_transaction->amount = $request->amount;
      $to_transaction->description = 'Amount transferred from Account: '.$from_account->name;
      $to_transaction->opening_balance = $to_account->balance;
      $to_transaction->type = 'c';
      $to_transaction->closing_balance = $to_account->balance + $request->amount;
      $to_transaction->user_id = Auth::user()->id;
      $to_transaction->user_name = Auth::user()->name;
      $to_transaction->save();
      $to_account->balance = $to_account->balance + $request->amount;
      $to_account->save();
    });
        return $this->sendResponse($response, 'Transfer Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
              'balance' => $request->get('balance'),
            // 'updated_by' => Auth::user()->name,
        ]);

        return $this->sendResponse($item, 'Account Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Account has been Deleted');
    }
}
