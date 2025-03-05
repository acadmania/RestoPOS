<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Auth;
class CustomerController extends BaseController
{
    protected $item = '';

    public function __construct(Customer $item)
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

        $items = $this->item->where('name', 'like', '%'.$request['searchText'].'%')->orwhere('phone', 'like', '%'.$request['searchText'].'%')->with('sales')->paginate($numberOfItems);
       }
      else {
            $items = $this->item->with('sales')->latest()->paginate($numberOfItems);
            //  $items = $this->item->with('locations','units','brand')->latest()->paginate($numberOfItems);
          }
        return $this->sendResponse($items, 'Customer list');
    }

    public function searchList($search)
    {
            $items = $this->item->with('sales')->where('name', 'like', '%'.$search.'%')->orwhere('phone', 'like', '%'.$search.'%')->paginate(10);
            return $this->sendResponse($items, 'Product list Search');
    }

    public function list()
    {
        $items = $this->item->pluck('name', 'id');

        return $this->sendResponse($items, 'Customer list');
    }

    public function store(Request $request)
    {

      $item = $this->item->create([
            'name' => $request->get('name'),

            'phone' => $request->get('phone'),
            'address' => $request->get('address'),
            'area' => $request->get('area'),

            'district' => $request->get('district'),
            'no_points' => $request->get('no_points'),


        ]);

        return $this->sendResponse($item, 'Customer Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = $this->item->findOrFail($id);

        $item->update([
          'name' => $request->get('name'),

          'phone' => $request->get('phone'),
          'address' => $request->get('address'),
          'area' => $request->get('area'),

          'district' => $request->get('district'),
          'no_points' => $request->get('no_points'),
        ]);

        return $this->sendResponse($item, 'Customer Information has been updated');

    }
    public function search($search)
    {
        $items = $this->item->where('name', 'like', '%'.$search.'%')
        ->orwhere('phone', 'like', '%'.$search.'%')

        ->get();
        foreach ($items as $i) {
          $item_array[] = [
            'label' => $i->name,
            'code' => $i->id,
            'phone' => $i->phone,
            'points' => $i->points,

          ];
        }
        return $item_array;


    }
    public function pointTransactions($id)
    {
      $customer = Customer::find($id);
      $transactions = $customer->points()->paginate(10);
      return $this->sendResponse(['customer' => $customer, 'transactions' => $transactions], 'Customer Point Transactions');
    }

    public function destroy($id)
    {
      $item = $this->item->findOrFail($id);

      $item->delete();

        return $this->sendResponse($item, 'Customer has been Deleted');
    }
}
