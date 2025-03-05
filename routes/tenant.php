<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([

    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'api',
])->prefix('api')->group(function () {



});




Route::middleware([

    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    'api',
])->prefix('api')->group(function () {


    Route::middleware('auth:api')->get('/user', function (Request $request) {
        Log::debug('User:' . serialize($request->user()));
        return $request->user();
    });


    Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () {
        Route::post('login', 'AuthController@login');
        Route::get('license/check', 'SettingController@licenseCheck');
      Route::post('account/transfer', 'AccountController@transfer');
      Route::post('sale/filter', 'SaleController@filterSale');
      Route::post('purchase/filter', 'PurchaseController@filterPurchase');
        Route::get('profile', 'ProfileController@profile');
        Route::get('pos/close/request', 'POSController@closeRequest');
        Route::get('pos/close', 'POSController@close');
        Route::get('pos/list', 'POSController@posList');
        Route::get('pos/check/registers', 'POSController@checkRegisters');
        Route::post('register/open', 'POSController@openRegister');
        Route::post('direct/consume', 'ConsumptionController@ConsumeDirect');
        Route::put('profile', 'ProfileController@updateProfile');
        Route::post('change-password', 'ProfileController@changePassword');
        Route::get('tag/list', 'TagController@list');
        Route::get('category/list', 'CategoryController@list');
        Route::get('employee/list', 'EmployeeController@list');
        Route::get('location/list', 'LocationController@list');
          Route::get('table/list', 'TableController@list');
          Route::get('table/all', 'TableController@all');
        Route::get('customer/group/list', 'CustomerGroupController@list');
        Route::get('material/category/list', 'MaterialCategoryController@list');
        Route::get('expense/category/list', 'ExpenseCategoryController@list');
        Route::get('material/brand/list', 'MaterialBrandController@list');
        Route::get('material/unit/list', 'MaterialUnitController@list');
        Route::get('material/list', 'MaterialController@list');
        Route::get('product/category/list', 'ProductCategoryController@list');
        Route::get('product/brand/list', 'ProductBrandController@list');
        Route::get('product/unit/list', 'ProductUnitController@list');
        Route::get('product/list', 'ProductController@list');
        Route::get('food/list', 'FoodController@list');
        Route::get('kitchen/list', 'KitchenController@list');
        Route::get('food/category/list', 'FoodCategoryController@list');
        Route::get('food/brand/list', 'FoodBrandController@list');
        Route::get('discount/product/list', 'DiscountController@ProductList');
        Route::get('discount/category/list', 'DiscountController@CategoryList');
        Route::get('employee/category/list', 'EmployeeCategoryController@list');
        Route::get('account/list', 'AccountController@list');
        Route::get('get/account', 'TransactionController@getAccount');
        Route::get('filter/transaction/{search}','TransactionController@filterAccount');
        Route::get('permission/list', 'RoleController@permissions');
        Route::get('supplier/list', 'SupplierController@list');
        Route::get('customer/list', 'CustomerController@list');
        Route::post('pos/addsale', 'POSController@addSale');
        Route::get('material/search/{search}', 'MaterialController@search');
        Route::get('customer/search/{search}', 'CustomerController@search');
        Route::get('customer/list/search/{search}', 'CustomerController@searchList');
        Route::get('food/all', 'FoodController@all');
        Route::get('food/category/all', 'FoodCategoryController@all');
        Route::get('table/search/{search}', 'TableController@search');
        Route::get('modifiers/search/{search}', 'ModifierController@search');
        Route::get('product/search/{search}', 'ProductController@search');
        Route::get('food/search/{search}', 'FoodController@search');
        Route::get('table/order/{table_id}', 'TableController@printOrder');
        Route::get('table/order/get/{table_id}', 'TableController@getOrder');
        Route::get('booking/order/get/{customer_id}', 'SaleController@getBookingOrder');
        Route::get('sale/token/get', 'SaleController@getTokens');
        Route::get('sale/notcompleted', 'SaleController@getIncomplete');
        Route::get('settings', 'SettingController@get');
        Route::get('food/ingredient/get/{product_id}', 'ProductController@getProduct');
        Route::post('table/order', 'TableController@order');

        Route::get('product/list/search/{search}', 'ProductController@searchList');
        Route::get('sale/status/change/{sale_id}/{status}', 'SaleController@statusChange');
        Route::get('food/list/search/{search}', 'FoodController@searchList');
        Route::get('sale/list/search/{search}', 'SaleController@searchList');
        Route::get('purchase/product/search/{search}', 'ProductController@purchaseSearch');
        Route::post('product/upload', 'ProductController@upload');
        Route::post('transfer/account', 'TransactionController@transfer');
        Route::post('report/account', 'ReportController@account');

        Route::post('purchase/product/add', 'PurchaseController@addProduct');
        //Route::get('purchase/get/initiated', 'PurchaseController@getInitiated');
        Route::get('purchase/material/get/{material_id}/{supplier_id}', 'PurchaseController@getMaterial');
        Route::get('modifier/get/{modifier_id}', 'ModifierController@getModifier');
        Route::get('kot/direct/print/{order_id}/{type}', 'SaleController@kotDirectPrint');
        Route::get('purchase/product/get/{material_id}/{supplier_id}/{location_id}', 'PurchaseController@getProduct');
        Route::get('purchase/get/{purchase_id}', 'PurchaseController@getPurchase');
        Route::get('sale/get/{sale_id}', 'SaleController@getSale');
        Route::get('daily/count/sale/get/{sale_id}', 'SaleController@getSaleDailyCount');

        Route::get('sale/return/get/{return_id}', 'SaleReturnController@getRetrun');
        Route::get('purchase/return/get/{return_id}', 'PurchaseReturnController@getRetrun');
        Route::get('consumption/get/{consumption_id}', 'ConsumptionController@getConsumption');
        Route::get('consumption/product/get/{consumption_id}', 'ConsumptionController@getConsumptionProducts');
        Route::get('purchase/receive/{purchase_id}', 'PurchaseController@purchaseReceive');
        Route::get('product/purchase/receive/{purchase_id}', 'PurchaseController@productPurchaseReceive');
        Route::post('purchase/payment', 'PurchaseController@purchasePayment');
        Route::post('sale/payment', 'SaleController@salePayment');

        Route::post('product/purchase', 'PurchaseController@productPurchase');
        Route::post('consumption/complete', 'ConsumptionController@productionEntry');
        Route::get('consumption/material/get/{material_id}', 'ConsumptionController@getMaterial');
        Route::get('product/get/{product_id}', 'ProductController@getProduct');
        Route::get('sale/product/get/{product_id}/{customer_id}', 'SaleController@getProduct');
        Route::get('sale/batch/get/{batch_id}/{customer_id}', 'SaleController@getBatch');
        Route::get('settings/get', 'SettingController@load');
        Route::post('report/consolidated', 'ReportController@consolidated');
        Route::post('report/food/category', 'ReportController@foodCategory');
        Route::post('report/product/sale', 'ReportController@productSale');
        Route::post('report/payment_method', 'ReportController@paymentMethod');
        Route::post('report/product/purchase', 'ReportController@productPurchase');
          Route::post('report/expense/category', 'ReportController@ExpenseCategory');
        Route::post('report/kitchen/order', 'ReportController@kitchenOrder');
        Route::post('report/product/consumption', 'ReportController@productConsumption');
        Route::get('settings/auto-refresh/{status}', 'SettingController@autoRefresh');
        Route::post('report/gst', 'ReportController@gst');
        Route::post('report/gst/sale/bill', 'ReportController@gstSaleBill');
        Route::post('report/supplier/stock', 'ReportController@supplierStock');
        Route::post('report/supplier/sales', 'ReportController@supplierSale');
        Route::post('report/supplier/transactions', 'ReportController@supplierTransactions');
        Route::post('productPurchase', 'PurchaseController@productPurchase');
        Route::post('settings/update', 'SettingController@update');
        Route::get('customer/points/view/{id}', 'CustomerController@pointTransactions');


        Route::apiResources([
            'user' => 'UserController',
            'role' => 'RoleController',
            'discount' => 'DiscountController',
            'product' => 'ProductController',
            'Food' => 'FoodController',
            'location' => 'LocationController',
            'table' => 'TableController',
            'department' => 'KitchenController',
            'sale/return' => 'SaleReturnController',
            'purchase/return' => 'PurchaseReturnController',

            'MaterialCategory' => 'MaterialCategoryController',
            'Modifier' => 'ModifierController',
            'customerGroup' => 'CustomerGroupController',
            'MaterialBrand' => 'MaterialBrandController',
            'MaterialUnit' => 'MaterialUnitController',
            'Material' => 'MaterialController',
            'ProductCategory' => 'ProductCategoryController',
            'ProductBrand' => 'ProductBrandController',
            'FoodCategory' => 'FoodCategoryController',
            'FoodBrand' => 'FoodBrandController',
            'ProductUnit' => 'ProductUnitController',
            'Product' => 'ProductController',
            'supplier' => 'SupplierController',
            'customer' => 'CustomerController',
            'consumption' => 'ConsumptionController',
            'employeeCategory' => 'EmployeeCategoryController',
            'ExpenseCategory' => 'ExpenseCategoryController',
            'Expense' => 'ExpenseController',
            'employee' => 'EmployeeController',
            'EmployeeSalary' => 'EmployeeSalaryController',
            'EmployeeAttendance' => 'EmployeeAttendanceController',
            'Investor' => 'InvestorController',
            'InvestorTransaction' => 'InvestorTransactionController',

            'account' => 'AccountController',
            'purchase' => 'PurchaseController',
            'sale' => 'SaleController',
            'tag' => 'TagController',
            'transaction' => 'TransactionController',
        ]);
        Route::get('customer/sale/{id?}', 'SaleController@index');
    });


});











Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {




Route::get('/print', [App\Http\Controllers\HomeController::class, 'printTest']);

Auth::routes(['verify' => true]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('submit/license', [App\Http\Controllers\API\V1\SettingController::class, 'licenseUpdate']);

Route::get('home', function () {
    return redirect('/dashboard');
});

Route::get('storage/{filename}', function ($filename)
{
    $path = storage_path('app/public/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('/{vue_capture?}', function () {
    return view('home');
})->where('vue_capture', '[\/\w\.-]*')->middleware('auth');

});
