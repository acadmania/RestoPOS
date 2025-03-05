<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use Illuminate\Http\Request;
use Auth;
class CustomerGroupController extends BaseController
{
    protected $item = '';

    public function __construct(CustomerGroup $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Customer Group list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Customer Group list');
    }

    public function store(Request $request)
    {

      $item = $this->item->create([
            'name' => $request->get('name'),

            'discount' => $request->get('discount'),

        ]);

        return $this->sendResponse($item, 'Customer Group Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
            'name' => $request->get('name'),

            'discount' => $request->get('discount'),

        ]);

        return $this->sendResponse($item, 'Customer Group Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Customer Group has been Deleted');
    }
}
