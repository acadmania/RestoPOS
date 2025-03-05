<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
class EmployeeSalaryController extends BaseController
{
    protected $item = '';

    public function __construct(EmployeeSalary $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Employee Salary list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Employee Salary list');
    }

    public function store(Request $request)
    {
      $account = Account::find($request->account_id);
      $employee = Employee::find($request->employee_id);
      $item = $this->item->create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'salary' => $employee->salary,
            'actual_salary' => $request->actual_salary,
            'salary_frequency' => $employee->salary_frequency,
            'note' => $request->note,
            'advance' => $request->advance,
            'incentive' => $request->incentive,
            'from' => Carbon::parse(Carbon::parse($request->fromTo[0])),
            'to' => Carbon::parse(Carbon::parse($request->fromTo[1])),
            'account_id' => $account->id,
            'account_name' => $account->name,
            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,
        ]);

        if($request->datetime1)
        {
          $item->created_at =  Carbon::parse(Carbon::parse($request->datetime1));
          $item->updated_at =  Carbon::parse(Carbon::parse($request->datetime1));
          $item->save();
        }

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
        $transaction->amount = $item->actual_salary;
        $transaction->type = 'd';
        $transaction->closing_balance = $account->balance - $transaction->amount;
        $transaction->employee_id = $employee->id;
        $transaction->employee_name = $employee->name;
        $transaction->user_id = Auth::user()->id;
        $transaction->user_name = Auth::user()->name;
        $transaction->save();

        $account->balance = $account->balance - $transaction->amount;
        $account->save();



        return $this->sendResponse([], 'Employee Salary Created Successfully');
    }



    public function destroy($id)
    {
        return "hello this expence salari";
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Employee Salary has been Deleted');
    }
}
