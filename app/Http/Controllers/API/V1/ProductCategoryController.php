<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Auth;
class ProductCategoryController extends BaseController
{
    protected $item = '';

    public function __construct(ProductCategory $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Product category list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Product category list');
    }

    public function store(Request $request)
    {

      $item = $this->item->create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),


        ]);

        return $this->sendResponse($item, 'Category Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
          
        ]);

        return $this->sendResponse($item, 'Category Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Category has been Deleted');
    }
}
