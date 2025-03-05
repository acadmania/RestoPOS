<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;

use App\Models\Product;

use App\Models\Kitchen;
use App\Models\Employee;

use App\Models\Consumption_item;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\DB;

class ConsumptionController extends BaseController
{
    protected $item = '';

    public function __construct(Consumption_item $item)
    {
        $this->middleware('auth:api');
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item->latest()->paginate(10);
        return $this->sendResponse($items, 'Consumption list');
    }

    public function getPurchase($purchase_id)
    {
      $purchase = Purchase::find($purchase_id);

      return $this->sendResponse([$purchase,$purchase->items,$purchase->statuses,$purchase->payStatuses], 'Purchase View');
    }



    public function getProduct($material_id,$supplier_id,$location_id)
    {
      $material = Product::find($material_id);

      $supplier = Supplier::find($supplier_id);
      $location = Location::find($location_id);

      /*if ($supplier->products()->where('product_id', $material->id)->where('location_id', $location->id)->exists()) {
        $mat = $supplier->products()->where('product_id', $material->id)->where('location_id', $location->id)->first();
        $price = $mat->pivot->cost;
        $cost_include_gst = $mat->cost_include_gst;
      }*/


      return [
       array(
         'price' => $price,
         'cost' => $cost,
         'tax_total' => $tax_total,
         'gst_percentage' => $gst_percentage,
         'cost_include_gst' => $cost_include_gst,
         'price_include_gst' => $price_include_gst,
         'sale_price' => $sale_price,
         'batch' => $batch,
      //   'code' => $code,
         'expiry_date' => $expiry_date,
      //   'units' => $ut,
       ),
       $material
      ];
    }

    public function ConsumeDirect(Request $request)
    {
      $response = DB::transaction(function () use (&$request) {
    $product = Product::find($request->id);
    $kitchen = Kitchen::find($request->kitchen_id);
    $employee = Employee::find($request->employee_id);



    $consumption_item = new Consumption_item;

    $consumption_item->product_id = $product->id;
    $consumption_item->name = $product->name;
    $consumption_item->quantity = $request->quantity;
    $consumption_item->kitchen_id = $kitchen->id;
    $consumption_item->kitchen_name = $kitchen->name;
    $consumption_item->employee_id = $employee->id;
    $consumption_item->employee_name = $employee->name;
    $consumption_item->wastage = $request->wastage;
    $consumption_item->user_id =  Auth::user()->id;
    $consumption_item->user_name =  Auth::user()->name;
    $consumption_item->save();
    $q = DB::table('location_product')
            ->where('location_id', $kitchen->location->id)
            ->where('product_id', $product->id);
    if($q->get()->count() > 0)
    {
      $f = $q->first();
        $quan = $consumption_item->quantity;
      $q->update(['quantity' => $q->first()->quantity-$quan]);
    }
    else {
      $kitchen->location->products()->attach(
          $product,
          [
          'quantity' => $consumption_item->quantity,

          ]
      );
    }
    });
    return $this->sendResponse('', 'Successfully Created');
}

}
