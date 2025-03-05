<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeSalary;
use Illuminate\Http\Request;
use Auth;
class EmployeeController extends BaseController
{
    protected $item = '';

    public function __construct(Employee $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->with('salaries')->paginate(10);
        return $this->sendResponse($items, 'Employee list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');
      
        return $this->sendResponse($items, 'Employee list');
    }
    public function show($id)
    {
        //
    }

    public function store(Request $request)
    {

      $item = $this->item->create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'employee_category_id' => $request->get('employee_category_id'),
            'kitchen_id' => $request->get('kitchen_id'),
            'waiter' => $request->get('waiter'),
            'login' => $request->get('login'),
            'address' => $request->get('address'),
            'area' => $request->get('area'),
            'district' => $request->get('district'),
            'phone' => $request->get('phone'),
            'salary' => $request->get('salary'),
            'salary_frequency' => $request->get('salary_frequency'),

            'user_id' => Auth::user()->id,
            'user_name' => Auth::user()->name,


        ]);



        return $this->sendResponse($item, 'Employee Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
          'name' => $request->get('name'),
          'code' => $request->get('code'),
          'employee_category_id' => $request->get('employee_category_id'),
          'kitchen_id' => $request->get('kitchen_id'),
          'waiter' => $request->get('waiter'),
          'login' => $request->get('login'),
          'address' => $request->get('address'),
          'area' => $request->get('area'),
          'district' => $request->get('district'),
          'phone' => $request->get('phone'),
          'salary' => $request->get('salary'),
          'salary_frequency' => $request->get('salary_frequency'),
          'user_id' => Auth::user()->id,
          'user_name' => Auth::user()->name,
        ]);



        return $this->sendResponse($item, 'Employee Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Employee has been Deleted');
    }
}
