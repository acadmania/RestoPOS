<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Food;
use App\Models\Product;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;
class FoodController extends BaseController
{
    protected $item = '';

    public function __construct(Food $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index(Request $request)
    {
        //$items = $this->item->latest()->with('locations','units')->paginate(10);
        if ($request['searchText'] != "null") {

          $items = $this->item->where('name', 'like', '%'.$request['searchText'].'%')->orwhere('code', 'like', '%'.$request['searchText'].'%')->with('locations','products')->paginate(10);
        }
        else {
          $items = $this->item->latest()->with('locations','products','modifiers')->paginate(10);
        }
        return $this->sendResponse($items, 'Food list');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Food list');
    }

    public function searchList($search)
    {
        $items = $this->item->where('name', 'like', '%'.$search.'%')->orwhere('code', 'like', '%'.$search.'%')->with('locations','products','modifiers')->paginate(10);
        return $this->sendResponse($items, 'Food list Search');
    }

    public function search($search)
    {
        $items = $this->item->where('name', 'like', '%'.$search.'%')->orwhere('code', 'like', '%'.$search.'%')->get();
        foreach ($items as $i) {
          $item_array[] = [
            'label' => $i->name .' ('.$i->code.')',
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

    public function getFood($product_id)
    {
      return $this->sendResponse(Food::find($product_id), 'Food');
    }

    public function store(Request $request)
    {
      $response = DB::transaction(function () use (&$request) {
      $item = $this->item->create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),
            'hsn' => $request->get('hsn'),
            'cost' => $request->get('cost'),
            'price' => $request->get('price'),
            'price_include_gst' => $request->get('price_include_gst'),
            'cost_include_gst' => $request->get('cost_include_gst'),
            'sale_price' => $request->get('sale_price'),
            'gst_percentage' => $request->get('gst_percentage'),
            'food_category_id' => $request->get('food_category_id'),
            'kitchen_id' => $request->get('kitchen_id'),
            'food_brand_id' => $request->get('food_brand_id'),
            'created_by' => Auth::user()->name,

        ]);

        if($request->file != "undefined")
        {
          $newPath = $request->file->store('','public');
          $item->image = $newPath;

        }
        else {
        $item->image = '/images/food.png';
        }
        $item->save();
        if($request->products)
        {
          $item->products()->detach();
          foreach (json_decode($request->products) as $i) {
            if($i->pivot->product_id !== ''){
              $product = Product::find($i->pivot->product_id);
              $item->products()->attach($i->pivot->product_id, ['quantity' => $i->pivot->quantity, 'product_name' => $i->pivot->product_name]);

            }
            }
        }
        if($request->modifiers)
        {
          $item->modifiers()->detach();
          foreach (json_decode($request->modifiers) as $i) {
            if($i->id !== ''){

              $item->modifiers()->attach($i->id);
            }
            }
        }

      /*  if($request->customer_group_discounts)
        $item->customer_group_discounts = $request->customer_group_discounts;
        $item->save();

        if($request->units)
        {
          foreach ($request->units as $unit) {
            $u = FoodUnit::find($unit['pivot']['unit_a_id']);
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
        return $this->sendResponse($response, 'Food Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
          'name' => $request->get('name'),
          'code' => $request->get('code'),
          'hsn' => $request->get('hsn'),
          'cost' => $request->get('cost'),
          'price' => $request->get('price'),
          'price_include_gst' => $request->get('price_include_gst'),
          'cost_include_gst' => $request->get('cost_include_gst'),
          'sale_price' => $request->get('sale_price'),
          'gst_percentage' => $request->get('gst_percentage'),
          'food_category_id' => $request->get('food_category_id'),
          'kitchen_id' => $request->get('kitchen_id'),
          'food_brand_id' => $request->get('food_brand_id'),

            'updated_by' => Auth::user()->name,
        ]);

        if($request->file !=="undefined")
        {
          $newPath = $request->file->store('','public');
          $item->image = $newPath;
          $item->save();
        }
        if($request->products)
        {
          $item->products()->detach();
          foreach (json_decode($request->products) as $i) {

            $product = Product::find($i->pivot->product_id);
            $item->products()->attach($i->pivot->product_id, ['quantity' => $i->pivot->quantity, 'product_name' => $i->pivot->product_name]);
          }
        }
        if($request->modifiers)
        {
          $item->modifiers()->detach();
          foreach (json_decode($request->modifiers) as $i) {
            if($i->id !== ''){

              $item->modifiers()->attach($i->id);

            }
            }
        }
      /*  if($request->customer_group_discounts)
        $item->customer_group_discounts = $request->customer_group_discounts;
        $item->save();

        if($request->units)
        {
          foreach ($request->units as $unit) {
            $u = FoodUnit::find($unit['pivot']['unit_a_id']);
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
*/
        return $this->sendResponse($item, 'Food Information has been updated');

    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);
      $item->products()->detach();
      $item->delete();

        return $this->sendResponse($item, 'Food has been Deleted');
    }
    public function all()
    {
      $item = Food::all();



        return $this->sendResponse($item, 'All Food');
    }

}
