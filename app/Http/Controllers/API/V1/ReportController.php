<?php

namespace App\Http\Controllers\API\V1;
use App\Http\Controllers\Controller;
use App\Models\Sale;

use App\Models\Purchase;
use App\Models\Consumption_item;
use App\Models\Consumption;
use App\Models\PurchasePayment;
use App\Models\EmployeeSalary;
use App\Models\Expense;
use App\Models\Supplier;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Kitchen;
use App\Models\PurchaseItem;
use App\Models\FoodCategory;
use App\Models\Product;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
class ReportController extends BaseController
{

    public function foodCategory(Request $req)
    {
        $from = Carbon::parse(Carbon::parse($req->date[0]))->addDay()->startOfDay();
        $to = Carbon::parse($req->date[1])->addDay()->endOfDay();

        $options = SaleItem::with('product')->where('created_at', '>=', $from)->where('created_at', '<=', $to)
            ->wherehas('sale',function($q){
            $q->where('status','Completed');
            })->get();

            $products = $options->groupby('product.food_category_id');
            foreach($products as $key=>$q)
            {
              if($key == null)
              {
                $result[] = array(
                  'name' => 'Uncategorized',
                  'quantity' => $q->sum('quantity'),
                  'cost' => $q->sum('subtotal'),
               );
              }
              else
              {
                $result[] = array(
                  'name' => FoodCategory::find($key) ? FoodCategory::find($key)->name : 'Unnamed',
                  'quantity' => $q->sum('quantity'),
                  'cost' => $q->sum('subtotal'),
               );
              }
            }

return $result;

    }
    public function consolidated(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();



      if ($request->group === 'Daily') {
        $g='d/m/Y';
        $interval = \DateInterval::createFromDateString('1day');
        $i = 'Daily';

        $period = new \DatePeriod($from, $interval, $to);
        foreach ($period as $dt) {
          $p[] = $dt->format("d/m/Y");
        }
    }
    elseif ($request->group === 'Monthly') {
        $g='m/Y';
        $interval = \DateInterval::createFromDateString('1month');
        $i = 'Monthly';

        $period = new \DatePeriod($from, $interval, $to);
        foreach ($period as $dt) {
          $p[] = $dt->format("m/Y");
        }
    }
    elseif ($request->group === 'Yearly') {
        $g='Y';
        $interval = \DateInterval::createFromDateString('1year');
        $i = 'Yearly';

        $period = new \DatePeriod($from, $interval, $to);
        foreach ($period as $dt) {
          $p[] = $dt->format("Y");
        }
    }


    $sales = Sale::where('status','Completed')->where('created_at', '>=', $from)->where('created_at', '<=', $to)->get()->groupBy(function($val) use($g){
       return Carbon::parse($val->created_at)->format($g);
});


/*    $saleReturns = SaleReturn::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get()->groupBy(function($val) use($g){
       return Carbon::parse($val->created_at)->format($g);
    });*/





$purchases = Purchase::where('status','Received')->where('created_at', '>=', $from)->where('created_at', '<=', $to)->get()->groupBy(function($val) use($g){
   return Carbon::parse($val->created_at)->format($g);
});

$salaries = EmployeeSalary::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get()->groupBy(function($val) use($g){
   return Carbon::parse($val->created_at)->format($g);
});

$expenses = Expense::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get()->groupBy(function($val) use($g){
   return Carbon::parse($val->created_at)->format($g);
});

foreach ($p as $i => $v) {
  if(isset($sales[$v]))
  {
    $salesCount[$v] = $sales[$v]->count();
    $salesAmount[$v] = $sales[$v]->sum('grand_total');
    $salesDiscount[$v] = $sales[$v]->sum('discount');

    $saleItemCost[$v] = 0;
    /*foreach ($sales[$v] as $s) {
      foreach ($s->items as $item) {
        if($item->cost !== null){

          $saleItemCost[$v] = $saleItemCost[$v]+($item->cost * $item->quantity);

        }
        else {
          $saleItemCost[$v] = $saleItemCost[$v]+($item->product->cost * $item->quantity);

        }
      }

    }*/
  }
  else {
    $salesCount[$v] = 0;
    $salesAmount[$v] = 0;
      $salesDiscount[$v] = 0;
    $saleItemCost[$v] = 0;
  }
  if(isset($purchases[$v]))
  {
    $purchaseCount[$v] = $purchases[$v]->count();
    $purchaseAmount[$v] = $purchases[$v]->sum('grand_total');
  }
  else {
    $purchaseCount[$v] = 0;
    $purchaseAmount[$v] = 0;
  }
  if(isset($salaries[$v]))
  {

    $salariesArray[$v] = $salaries[$v]->where('incentive',null)->where('advance',null)->sum('actual_salary');
    $SalaryAdvance[$v] = $salaries[$v]->where('advance',1)->sum('actual_salary');
    $Incentive[$v] = $salaries[$v]->where('incentive',1)->sum('actual_salary');
  }
  else {
    $salariesArray[$v] = 0;
    $SalaryAdvance[$v] = 0;
    $Incentive[$v] = 0;
  }
  if(isset($expenses[$v]))
  {
  $expensesArray[$v] = $expenses[$v]->sum('amount');
  }
  else {
    $expensesArray[$v] = 0;
  }
  /*if(isset($saleReturns[$v]))
  {
    $saleReturnCount[$v] = $saleReturns[$v]->count();
    $saleReturnAmount[$v] = $saleReturns[$v]->sum('grand_total');
  }
  else {
    $saleReturnCount[$v] = 0;
    $saleReturnAmount[$v] = 0;
  }*/


}
    return $this->sendResponse(["periods"=>$p,
                                "salesCount"=>$salesCount,
                                "salesAmount"=>$salesAmount,
                                "salesDiscount"=>$salesDiscount,
                                /*"salesReturnCount"=>$saleReturnCount,
                                "salesReturnAmount"=>$saleReturnAmount,*/
                              //  "salesCost"=>$saleItemCost,
                                "purchaseCount"=>$purchaseCount,
                                "purchaseAmount"=>$purchaseAmount,
                                "salariesArray"=>$salariesArray,
                                "SalaryAdvance"=>$SalaryAdvance,
                                "Incentive"=>$Incentive,
                                "expensesArray"=>$expensesArray],
                                'Consolidated Report');
    }

    public function account(Request $request){
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

      $accounts = Account::all();

      foreach ($accounts as $account) {
        $sale = SalePayment::where('created_at', '>=', $from)->where('created_at', '<=', $to)->where('account_id',$account->id)->get();
        $purchase = PurchasePayment::where('created_at', '>=', $from)->where('created_at', '<=', $to)->where('account_id',$account->id)->get();
        $expense = Expense::where('created_at', '>=', $from)->where('created_at', '<=', $to)->where('account_id',$account->id)->get();



        $res[$account->name] = array(
           'sale'       => $sale->sum('amount'),
           'purchase'    => $purchase->sum('amount'),
           'expense'    => $expense->sum('amount'),
           'balance'    => $account->balance

      );
      }



return $res;
    }

    public function kitchenOrder(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

      $kitchens = Kitchen::all();
      foreach ($kitchens as $k) {
        $options[$k->name] = SaleItem::with('product')->where('created_at', '>=', $from)->where('created_at', '<=', $to)
      ->wherehas('sale',function($q){
        $q->where('status','Completed');
      })
      ->wherehas('product',function($q) use($k){
        $q->where('kitchen_id',$k->id);
      })->get();
      }



    return $options;
    }
    public function productConsumption(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

      $consumption_items = Consumption_item::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();

      $consumption_items_by_name = $consumption_items->groupby('name');

    return array('all_items' => $consumption_items,
    'items_by_name' => $consumption_items_by_name);
    }

    public function ExpenseCategory(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

      $expenses = Expense::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();

    $exs = $expenses->groupby('expense_category_name');
    foreach ($exs as $key => $value) {


      $result[] = array(
        'name' => $key,
        'amount' => $value->sum('amount'),
    );
    }
    return $result;
    }
    public function paymentMethod(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();
      $results = SalePayment::where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
      $total_amount = $results->sum('amount');
      $results = $results->groupBy('payment_method');


     foreach($results as $name => $value){

      $result[$name] = array(

        "sale" => $value,
      );
     }

    return [$result, $total_amount];
    }


    public function productSale(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

      $options = SaleItem::where('created_at', '>=', $from)->where('created_at', '<=', $to)
    ->wherehas('sale',function($q){
      $q->where('status','Completed');
    })->get();
    $products = $options->groupby('food_name');
    foreach ($products as $key => $value) {
      if($value[0]->price)
      {
        $cost = $value[0]->price;
      }
      else {
        $product = $value[0]->product;

        $cost_only = $product->price;
        if($product->gst_percentage)
        {
          if($product->cost_include_gst == 0)
          {
            $tax_total = $cost_only*$product->gst_percentage/(100);
            $cost = $cost_only+$tax_total;
          }
          else {
            $cost = $cost_only;
          }
        }
        else {
          $cost = $cost_only;
        }
      }
      $result[] = array(
        'name' => $key,
        'quantity' => $value->sum('quantity'),
        'cost' => $value->sum('quantity')*$cost,
    );
    }
    return $result;
    }
    public function productPurchase(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

      $options = PurchaseItem::where('created_at', '>=', $from)->where('created_at', '<=', $to)
    ->wherehas('purchase',function($q){
      $q->where('status','Received');
    })->get();
    $products = $options->groupby('product_name');
    foreach ($products as $key => $value) {
      if($value[0]->cost)
      {
        $cost = $value[0]->cost;
      }
      else {
        $product = $value[0]->product;

        $cost_only = $product->cost;
        if($product->gst_percentage)
        {
          if($product->cost_include_gst == 0)
          {
            $tax_total = $cost_only*$product->gst_percentage/(100);
            $cost = $cost_only+$tax_total;
          }
          else {
            $cost = $cost_only;
          }
        }
        else {
          $cost = $cost_only;
        }
      }
      $result[] = array(
        'name' => $key,
        'quantity' => $value->sum('quantity'),
        'cost' => $value->sum('quantity')*$cost,
    );
    }
    return $result;
    }


    public function gst(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

      $options = SaleItem::wherehas('sale',function($q) use($from,$to){
      $q->where('status','Completed')->where('created_at', '>=', $from)->where('created_at', '<=', $to);
    })->get();
    $products = $options->groupby('gst_percentage');

    return $products;
    }


    public function gstSaleBill(Request $request)
    {
      $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
      $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

      $bills = Sale::where('created_at', '>=', $from)->where('created_at', '<=', $to)->with('items')->get();

      foreach ($bills as $b) {

        $res[] = array('bill_number' => $b->id,
                      'name' => $b->customer_name,
                      'date' => $b->created_at,
                      'salesExcludingGst' => (double)$b->total-(double)$b->discounts-(double)$b->gst,
                      'totalGst' => $b->gst,
                      'salesIncludingGst' => $b->grand_total,

                      'exempted_total' => isset($b->items->groupby('gst_percentage')[""]) ? $b->items->groupby('gst_percentage')[""]->sum('subtotal') : 0 ,
                      'exempted_gst' => isset($b->items->groupby('gst_percentage')[""]) ? $b->items->groupby('gst_percentage')[""]->sum('gst') : 0 ,

                      'point25_total' => isset($b->items->groupby('gst_percentage')["0.25"]) ? $b->items->groupby('gst_percentage')["0.25"]->sum('subtotal') : 0 ,
                      'point25_gst' => isset($b->items->groupby('gst_percentage')["0.25"]) ? $b->items->groupby('gst_percentage')["0.25"]->sum('subtotal') * 0.25 / 100 : 0 ,

                      'three_total' => isset($b->items->groupby('gst_percentage')["3"]) ? $b->items->groupby('gst_percentage')["3"]->sum('subtotal') : 0 ,
                      'three_gst' => isset($b->items->groupby('gst_percentage')["3"]) ? $b->items->groupby('gst_percentage')["3"]->sum('subtotal') * 3 / 100 : 0 ,

                      'five_total' => isset($b->items->groupby('gst_percentage')["5"]) ? $b->items->groupby('gst_percentage')["5"]->sum('subtotal') : 0 ,
                      'five_gst' => isset($b->items->groupby('gst_percentage')["5"]) ? $b->items->groupby('gst_percentage')["5"]->sum('subtotal') * 5 / 100 : 0 ,

                      'twelve_total' => isset($b->items->groupby('gst_percentage')["12"]) ? $b->items->groupby('gst_percentage')["12"]->sum('subtotal') : 0 ,
                      'twelve_gst' => isset($b->items->groupby('gst_percentage')["12"]) ? $b->items->groupby('gst_percentage')["12"]->sum('subtotal') * 12 / 100 : 0 ,

                      'eighteen_total' => isset($b->items->groupby('gst_percentage')["18"]) ? $b->items->groupby('gst_percentage')["18"]->sum('subtotal') : 0 ,
                      'eighteen_gst' => isset($b->items->groupby('gst_percentage')["18"]) ? $b->items->groupby('gst_percentage')["18"]->sum('subtotal') * 18 / 100 : 0 ,

                      'twentyeight_total' => isset($b->items->groupby('gst_percentage')["28"]) ? $b->items->groupby('gst_percentage')["28"]->sum('subtotal') : 0 ,
                      'twentyeight_gst' => isset($b->items->groupby('gst_percentage')["28"]) ? $b->items->groupby('gst_percentage')["28"]->sum('subtotal') * 28 / 100  : 0 ,


                    );

      }


    return $res;
    }


    public function supplierStock(Request $request)
    {
      foreach($request->supplier_id as $id)
      {
        $supplier = Supplier::find($id);
        $products = $supplier->products;
        foreach($products as $p)
        $res[] = array(
          'supplier_name' => $supplier->name,
          'product_name' => $p->name,
          'cost' => $p->cost,
          'quantity' => $p->locations()->sum('quantity'),
          'total' => $p->cost * $p->locations()->sum('quantity'),

      );
      }

      return $res;
    }

    public function supplierSale(Request $request)
    {
      foreach($request->supplier_id as $id)
      {
        $from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
        $to = Carbon::parse($request->date[1])->addDay()->endOfDay();

        $items = SaleItem::wherehas('sale',function($q) use($from,$to){
        $q->where('status','Completed')->where('created_at', '>=', $from)->where('created_at', '<=', $to);
      })->get();
      $products = $items->groupby('product_name');

      foreach ($products as $key => $value) {
        $product = Product::find($value[0]->product_id);
        $supplier = $product->suppliers->first();
        if($supplier)
        {

        if($id == $supplier->id)
        {

          $result[] = array(
            'name' => $key,
            'supplier' => $supplier->name,
              'cost' => $product->cost,
                'price' => $value[0]->selling_price,
            'quantity' => $value->sum('quantity'),

            'total_price' => $value->sum('quantity')* $value[0]->selling_price,

            'total_cost' => $value->sum('quantity') * $product->cost,
        );
        }
      }

      }




      }

      return $result;
    }




    public function supplierTransactions(Request $request)
    {
		if($request->date[0] !== null)
		{
			$from = Carbon::parse(Carbon::parse($request->date[0]))->addDay()->startOfDay();
			$to = Carbon::parse($request->date[1])->addDay()->endOfDay();
			$purchases = Purchase::where('status','Received')->where('created_at', '>=', $from)->where('created_at', '<=', $to)->where('supplier_id',$request->supplier_id)->get();
        $payments = PurchasePayment::wherehas('purchase',function($q) use($request){
        $q->where('supplier_id',$request->supplier_id);
      })->where('created_at', '>=', $from)->where('created_at', '<=', $to)->get();
		}

		else
		{
			$purchases = Purchase::where('status','Received')->where('supplier_id',$request->supplier_id)->get();
        $payments = PurchasePayment::wherehas('purchase',function($q) use($request){
        $q->where('supplier_id',$request->supplier_id);
      })->get();
		}



        //return $payments->merge($purchases);
        return array('purchases' => $purchases, 'payments' => $payments );
    }
}
