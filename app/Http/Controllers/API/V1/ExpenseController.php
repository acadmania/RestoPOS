<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Auth;
class ExpenseController extends BaseController
{
    protected $item = '';

    public function __construct(Expense $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Expense  list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Expense  list');
    }

    public function store(Request $request)
    {
        $account = Account::find($request->account_id);
        $expense_category = ExpenseCategory::find($request->category_id);

         $expense = new Expense;
         $expense->note = $request->note;
         $expense->amount = $request->amount;
         if($expense_category)
         {
           $expense->expense_category_id = $expense_category->id;
           $expense->expense_category_name = $expense_category->name;
         }
         $expense->account_id = $account->id;
         $expense->account_name = $account->name;
         $expense->created_at = $request->datetime1;
         $expense->updated_at = $request->datetime1;
         $expense->user_id = Auth::user()->id;
         $expense->user_name = Auth::user()->name;
         $expense->save();


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
        $transaction->amount = $expense->amount;
        $transaction->type = 'd';
        $transaction->closing_balance = $account->balance - $transaction->amount;
        $transaction->expense_id = $expense->id;

        $transaction->user_id = Auth::user()->id;
        $transaction->user_name = Auth::user()->name;
        $transaction->save();

        $account->balance = $account->balance - $transaction->amount;
        $account->save();
        return $this->sendResponse($expense, 'Expense  Created Successfully');
    }



    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Expense  has been Deleted');
    }
}
