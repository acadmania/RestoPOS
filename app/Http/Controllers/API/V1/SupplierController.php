<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Auth;
class SupplierController extends BaseController
{
    protected $item = '';

    public function __construct(Supplier $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Supplier list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Supplier list');
    }

    public function store(Request $request)
    {

      $item = $this->item->create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'tax_number' => $request->get('tax_number'),
            'address' => $request->get('address'),
            'phone' => $request->get('phone'),


        ]);

        return $this->sendResponse($item, 'Supplier Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'tax_number' => $request->get('tax_number'),
            'address' => $request->get('address'),
            'phone' => $request->get('phone'),
        ]);

        return $this->sendResponse($item, 'Supplier Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Supplier has been Deleted');
    }
}
