<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\EmployeeCategory;
use Illuminate\Http\Request;
use Auth;
class EmployeeCategoryController extends BaseController
{
    protected $item = '';

    public function __construct(EmployeeCategory $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Employee Category list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Employee Category list');
    }

    public function store(Request $request)
    {

      $item = $this->item->create([
            'name' => $request->get('name'),

            'extras' => $request->get('extras'),


        ]);

        return $this->sendResponse($item, 'Employee Category Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
            'name' => $request->get('name'),

            'extras' => $request->get('extras'),
          
        ]);

        return $this->sendResponse($item, 'Employee Category Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Employee Category has been Deleted');
    }
}
