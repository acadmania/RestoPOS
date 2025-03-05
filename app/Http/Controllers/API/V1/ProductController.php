<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Food;
use App\Models\ProductUnit;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
class ProductController extends BaseController
{
    protected $item = '';

    public function __construct(Product $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index(Request $request)
    {

        if($request['numberOfItems'])
        $numberOfItems = $request['numberOfItems'];

        if($numberOfItems == "All")
        $numberOfItems = 999999999999999999999999;


        if (($request['searchText'] != "null") && ( $request['searchText'] != null)) {
            $items = $this->item->where('name', 'like', '%'.$request['searchText'].'%')->orwhere('code', 'like', '%'.$request['searchText'].'%')->with('locations')->paginate($numberOfItems);
        }
        else {
            $items = $this->item->latest()->with('locations')->paginate($numberOfItems);
        }


        return $this->sendResponse($items, 'Product list');


        //$items = $this->item->latest()->with('locations','units')->paginate(10);


    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Product list');
    }

    public function searchList($search)
    {
        $items = $this->item->where('name', 'like', '%'.$search.'%')->orwhere('code', 'like', '%'.$search.'%')->with('locations')->paginate(999999);
        return $this->sendResponse($items, 'Product list Search');
    }

    public function search($search)
    {
        $items = $this->item->where('name', 'like', '%'.$search.'%')->orwhere('code', 'like', '%'.$search.'%')->get();
        foreach ($items as $i) {
          $item_array[] = [
            'label' => $i->name,
            'code' => $i->id,
          ];
        }
        return $item_array;
    }
    public function purchaseSearch($search)
    {
        $items = $this->item->where('name', 'like', '%'.$search.'%')->orwhere('code', 'like', '%'.$search.'%')->get();
        $item_array = [];
        foreach ($items as $i) {
          $item_array[] = [
            'label' => $i->name,
            'code' => $i->id,
          ];
        }
        return $item_array;


    }

    public function getProduct($product_id)
    {
      return $this->sendResponse(Product::find($product_id), 'Product');
    }

    public function store(Request $request)
    {
      $response = DB::transaction(function () use (&$request) {
      $item = $this->item->create([
            'name' => $request->get('name'),
            'hsn' => $request->get('hsn'),
            'code' => $request->get('code'),
            'sellable' => $request->get('sellable'),
            'cost' => $request->get('cost'),
            'cost_include_gst' => $request->get('cost_include_gst'),
            'price' => $request->get('price'),
            'price_include_gst' => $request->get('price_include_gst'),
            'sale_price' => $request->get('sale_price'),
            'gst_percentage' => $request->get('gst_percentage'),
            'product_category_id' => $request->get('product_category_id'),
            'product_brand_id' => $request->get('product_brand_id'),
            'created_by' => Auth::user()->name,

        ]);

        if($item->sellable)
        {
          $f = new Food;
          $f->name = $request->get('name');
          $f->code = $request->get('code');
          $f->hsn = $request->get('hsn');
          $f->cost = $request->get('cost');
          $f->price = $request->get('price');
          $f->price_include_gst = $request->get('price_include_gst');
          $f->cost_include_gst = $request->get('cost_include_gst');
          $f->sale_price = $request->get('sale_price');
          $f->gst_percentage = $request->get('gst_percentage');
          $f->food_category_id = $request->get('food_category_id');
          $f->food_brand_id = $request->get('food_brand_id');
          $f->created_by = Auth::user()->name;
          $f->save();
        }
      /*  if($request->customer_group_discounts)
        $item->customer_group_discounts = $request->customer_group_discounts;
        $item->save();

        if($request->units)
        {
          foreach ($request->units as $unit) {
            $u = ProductUnit::find($unit['pivot']['unit_a_id']);
            $item->units()->attach(
                $u,
                [
                'unit_a_value' => 1,
                'unit_b_id' => $unit['pivot']['unit_b_id'],
                'unit_b_value' =>  $unit['pivot']['unit_b_value'],


                ]
            );
          }

        }*/
      });
        return $this->sendResponse($response, 'Product Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
          'name' => $request->get('name'),
          'hsn' => $request->get('hsn'),
          'code' => $request->get('code'),
          'sellable' => $request->get('sellable'),
          'cost' => $request->get('cost'),
          'cost_include_gst' => $request->get('cost_include_gst'),
          'price' => $request->get('price'),
          'price_include_gst' => $request->get('price_include_gst'),
          'sale_price' => $request->get('sale_price'),
          'gst_percentage' => $request->get('gst_percentage'),
          'product_category_id' => $request->get('product_category_id'),
          'product_brand_id' => $request->get('product_brand_id'),
          'updated_by' => Auth::user()->name,
        ]);
        if($request->customer_group_discounts)
        $item->customer_group_discounts = $request->customer_group_discounts;
        $item->save();

        if($request->units)
        {
          foreach ($request->units as $unit) {
            $u = ProductUnit::find($unit['pivot']['unit_a_id']);
            if($u)
            {
            if ($item->units()->where('unit_a_id', $u->id)->exists()) {
              $item->units()->updateExistingPivot($u->id, array(
                'unit_a_value' => 1,
                'unit_b_id' => $unit['pivot']['unit_b_id'],
                'unit_b_value' =>  $unit['pivot']['unit_b_value']
              ));
            }
            else {
              $item->units()->attach(
                  $u,
                  [
                  'unit_a_value' => 1,
                  'unit_b_id' => $unit['pivot']['unit_b_id'],
                  'unit_b_value' =>  $unit['pivot']['unit_b_value'],


                  ]
              );
            }
          }
          }

        }

        return $this->sendResponse($item, 'Product Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Product has been Deleted');
    }
}
