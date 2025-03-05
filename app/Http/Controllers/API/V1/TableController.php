<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Table;
use App\Models\Table_item;
use Illuminate\Http\Request;
use Auth;
class TableController extends BaseController
{
    protected $location = '';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Table $location)
    {
        $this->middleware('auth:api');
        $this->location = $location;
    }


    public function search($search)
    {
        $items = $this->location->with('items')->where('name', 'like', '%'.$search.'%')
        ->orwhere('code', 'like', '%'.$search.'%')

        ->get();
        foreach ($items as $i) {
          if($i->items->count() > 0)
          {
            $item_array[] = [
              'label' => $i->name . ' (RUNNING)',
              'code' => $i->id,
          ];
          }
          else {
            $item_array[] = [
              'label' => $i->name,
              'code' => $i->id,
          ];
          }

        }
        return $item_array;


    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $factories = $this->location->with('items')->latest()->paginate(10);
        return $this->sendResponse($factories, 'Table list');
    }

    public function list()
    {
        $factories = $this->location->pluck('name', 'id');

        return $this->sendResponse($factories, 'Table list');
    }


    public function all()
    {
        $factories = $this->location->pluck('name', 'id');
        $arr=[];

foreach($factories as $k=>$v)
{
$arr[] = array(
    "label" => $v . (Table_item::where('table_id',$k)->get()->count() > 0 ? " (Running)" : null),
    "value" => $k,


);
}
        return $this->sendResponse($arr, 'Table list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $location = $this->location->create([
            'name' => $request->get('name'),
            'code' => $request->get('code'),


        ]);

        return $this->sendResponse($location, 'Table Created Successfully');
    }
    public function order(Request $request)
    {
      foreach ($request->items as $i) {

          if($i['printed'] !== 1)
          {
            $order = new Table_item;
            if($request->customer)
            $order->customer_id = $request->customer['code'];
            $order->table_id = isset($request->table['value']) ? $request->table['value'] : $request->table['code'] ;
            $order->table_name = $request->table['label'];


            $order->food_id = $i['product_id'];
            $order->food_name = $i['product_name'];
            $order->hsn = $i['hsn'];
            $order->code = $i['code'];
            $order->quantity = $i['quantity'];
            $order->price = $i['rate'];
            $order->selling_price = $i['price'];
            $order->gst = $i['tax_total'];
            $order->gst_percentage = $i['gst_percentage'];
            $order->discount = $i['discount'];
            $order->cost = $i['cost'];
            $order->save();
          }
          elseif(isset($i['is_modified']))
          {
            if($i['is_modified'] == 1)
            {
              $order = Table_item::find($i['id']);
            $order->old_quantity = $order->quantity;
            $order->quantity = $i['quantity'];
            $order->printed = 0;
            $order->save();
            }

          }






      }


      if(isset($order))
      {
        return $this->sendResponse($order, 'Order Created Successfully');

      }


    }

    public function printOrder($table_id)
    {

      $items = Table_item::where('table_id',$table_id)->whereNull('printed')->Orwhere('printed',0)->get();
      Table_item::where('table_id',$table_id)->whereNull('printed')->Orwhere('printed',0)->update(['printed'=>1]);
      return $this->sendResponse($items, 'Order View');
    }
    public function getOrder($table_id)
    {
      $items = Table_item::where('table_id',$table_id)->get()->groupby('food_id');
      $res = [];
      foreach ($items as $i) {
        $res[] = array(
          'id' => $i[0]->id ,
          'food_name' => $i[0]->food_name ,
          'food_id' => $i[0]->food_id ,
          'tax_total' => $i[0]->gst ,
          'price' => $i[0]->selling_price ,
          'rate' => $i[0]->price ,
          'hsn' => $i[0]->hsn ,
          'code' => $i[0]->code ,
          'gst_percentage' => $i[0]->gst_percentage ,
          'discount' => $i[0]->discount ,
          'cost' => $i[0]->cost ,
          'quantity' => $i->sum('quantity') ,
          'printed' => $i[0]->printed ,

      );
      }


      return $this->sendResponse($res, 'Order View');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $location = $this->location->findOrFail($id);

        $location->update([
            'name' => $request->get('name'),
            'code' => $request->get('code'),

        ]);


        return $this->sendResponse($location, 'Table Information has been updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $location = $this->location->findOrFail($id);

        $location->delete();

        return $this->sendResponse($location, 'Table has been Deleted');
    }
}
