<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\Users\UserRequest;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;



class DiscountController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
     {
         $items = Discount::paginate(10);
         return $this->sendResponse($items, 'Discount list');
     }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Users\UserRequest  $request
     *
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $discount = Discount::create([
            'title' => $request['title'],
            'type' => $request['type'],
            'value_type' => $request['value_type'],
            'minimum' => $request['minimum'],
            'maximum' => $request['maximum'],
            'value' => $request['value'],
            'excluded_categories' => $request['excluded_categories'],
            'active' => 1,

        ]);

        return $this->sendResponse($discount, 'Discount Created Successfully');
    }

    public function update(Request $request, $id)
    {
        $item = Discount::find($id);

        $item->update([
            'title' => $request->get('title'),
            'type' => $request->get('type'),
            'value_type' => $request->get('value_type'),
            'minimum' => $request->get('minimum'),
            'maximum' => $request->get('maximum'),
            'value' => $request->get('value'),
            'excluded_categories' => $request->get('excluded_categories'),



        ]);

        return $this->sendResponse($item, 'Account Information has been updated');

    }

    /**
     * Update the resource in storage
     *
     * @param  \App\Http\Requests\Users\UserRequest  $request
     * @param $id
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $this->authorize('isAdmin');

        $user = Discount::findOrFail($id);
        // delete the user

        $user->delete();

        return $this->sendResponse([$user], 'Discount has been Deleted');
    }

    public function ProductList()
    {

          $products = Product::get(['name', 'id']);

        return $this->sendResponse($products, 'Product list');
    }
    public function CategoryList()
    {

          $categories = ProductCategory::get(['name', 'id']);

        return $this->sendResponse($categories, 'Category list');
    }
}
