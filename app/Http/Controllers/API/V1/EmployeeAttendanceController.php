<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\EmployeeAttendance;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Auth;
class EmployeeAttendanceController extends BaseController
{
    protected $item = '';

    public function __construct(EmployeeAttendance $item)
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

      $employee = Employee::find($request->employee_id);
      $item = $this->item->create([
            'employee_id' => $employee->id,
            'employee_name' => $employee->name,
            'time' => Carbon::parse(Carbon::parse($request->dateTime)),
            'type' => $request->type,

        ]);

      
        return $this->sendResponse([], 'Employee Salary Created Successfully');
    }



    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Employee Salary has been Deleted');
    }
}
