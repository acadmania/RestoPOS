<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


/*
Route::
    namespace ('App\\Http\\Controllers\\API\V1')->group(function () {
        Route::post('login', 'AuthController@login');
    });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    Log::debug('User:' . serialize($request->user()));
    return $request->user();
});


Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () {
    Route::get('profile', 'ProfileController@profile');

    Route::get('customer/list/{numberOfItems}', 'CustomerController@index');
    Route::get('Product/{numberOfItems}', 'ProductController@index');
    Route::put('product/AddStock', 'ProductController@addStock');
    Route::get('pos/close/request', 'POSController@closeRequest');
    Route::get('pos/close', 'POSController@close');
    Route::get('pos/list', 'POSController@posList');
    Route::get('pos/check/registers', 'POSController@checkRegisters');
    Route::post('register/open', 'POSController@openRegister');
    Route::put('profile', 'ProfileController@updateProfile');
    Route::post('change-password', 'ProfileController@changePassword');
    Route::get('tag/list', 'TagController@list');
    Route::get('category/list', 'CategoryController@list');
    Route::get('location/list', 'LocationController@list');
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
    Route::get('discount/product/list', 'DiscountController@ProductList');
    Route::get('discount/category/list', 'DiscountController@CategoryList');
    Route::get('employee/category/list', 'EmployeeCategoryController@list');
    Route::get('account/list', 'AccountController@list');
    Route::get('permission/list', 'RoleController@permissions');
    Route::get('supplier/list', 'SupplierController@list');
    Route::get('customer/list', 'CustomerController@list');
    Route::post('pos/addsale', 'POSController@addSale');
    Route::get('material/search/{search}', 'MaterialController@search');
    Route::get('customer/search/{search}', 'CustomerController@search');
    Route::get('product/search/{search}', 'ProductController@search');
    Route::get('product/list/search/{search}', 'ProductController@searchList');
    Route::get('customer/list/search/{search}', 'CustomerController@searchList');
    Route::get('sale/list/search/{search}', 'SaleController@searchList');
    Route::get('purchase/product/search/{search}', 'ProductController@purchaseSearch');
    Route::post('product/upload', 'ProductController@upload');
    Route::get('/user/permission/{id}', 'UserController@permission');
    

    Route::post('purchase/product/add', 'PurchaseController@addProduct');
    //Route::get('purchase/get/initiated', 'PurchaseController@getInitiated');
    Route::get('purchase/material/get/{material_id}/{supplier_id}', 'PurchaseController@getMaterial');
    Route::get('purchase/product/get/{material_id}/{supplier_id}/{location_id}', 'PurchaseController@getProduct');
    Route::get('purchase/get/{purchase_id}', 'PurchaseController@getPurchase');
    Route::get('sale/get/{sale_id}', 'SaleController@getSale');
    Route::get('quote/get/{sale_id}', 'QuoteController@getSale');
    Route::get('service/get/{service_id}', 'ServiceController@getService');
   

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
    Route::get('sale/product/get/{product_id}/{batch_id}/{customer_id}', 'SaleController@getProduct');
    Route::get('transfer/product/get/{product_id}/{from_location_id}/{to_location_id}', 'TransferController@getProduct');
    Route::get('sale/batch/get/{batch_id}/{customer_id}', 'SaleController@getBatch');
    Route::get('settings/get', 'SettingController@load');
    Route::post('report/consolidated', 'ReportController@consolidated');
    Route::post('report/product/sale', 'ReportController@productSale');
    Route::post('report/sales', 'ReportController@Sale');
    Route::post('report/payment_method', 'ReportController@Payment');
    Route::post('report/categoryExpence', 'ReportController@categoryExpence');
    Route::post('report/categorySale', 'ReportController@categorySale');
    Route::post('report/gst', 'ReportController@gst');
      Route::post('report/account', 'ReportController@account');
    Route::post('report/gst/sale/bill', 'ReportController@gstSaleBill');
    Route::post('report/supplier/stock', 'ReportController@supplierStock');
    Route::get('report/location/stock/value', 'ReportController@locationStockValue');
    Route::post('report/location/stock', 'ReportController@locationStock');
    Route::post('report/supplier/sales', 'ReportController@supplierSale');
    Route::post('productPurchase', 'PurchaseController@productPurchase');
    Route::post('settings/update', 'SettingController@update');
    Route::get('customer/points/view/{id}', 'CustomerController@pointTransactions');
    Route::get('inventory/price/update/{id}/{value}', 'ProductController@inventoryPriceChange');
    Route::get('inventory/saleprice/update/{id}/{value}', 'ProductController@inventorySalePriceChange');
    Route::get('inventory/qty/update/{id}/{value}', 'ProductController@inventoryQtyChange');
    Route::get('inventory/batch/merge/{ids}', 'ProductController@batchMerge');
    Route::get('customer/dashlist', 'SaleController@dashList');



    Route::apiResources([
        'user' => 'UserController',
        'Quote' => 'QuoteController',
        'role' => 'RoleController',
        'discount' => 'DiscountController',
        'product' => 'ProductController',
        'location' => 'LocationController',
        'sale/return' => 'SaleReturnController',
        'purchase/return' => 'PurchaseReturnController',
        'EmployeeAttendance' => 'EmployeeAttendanceController',
        'MaterialCategory' => 'MaterialCategoryController',
        'customerGroup' => 'CustomerGroupController',
        'MaterialBrand' => 'MaterialBrandController',
        'transfer' => 'TransferController',
        'MaterialUnit' => 'MaterialUnitController',
        'Material' => 'MaterialController',
        'ProductCategory' => 'ProductCategoryController',
        'ProductBrand' => 'ProductBrandController',
        'ProductUnit' => 'ProductUnitController',
        'Product' => 'ProductController',
        'supplier' => 'SupplierController',
        'customer' => 'CustomerController',
        'consumption' => 'ConsumptionController',
        'employeeCategory' => 'EmployeeCategoryController',
        'ExpenseCategory' => 'ExpenseCategoryController',
        'Expense' => 'ExpenseController',
        'Service' => 'ServiceController',
        'employee' => 'EmployeeController',
        'EmployeeSalary' => 'EmployeeSalaryController',
        'Investor' => 'InvestorController',
        'InvestorTransaction' => 'InvestorTransactionController',

        'account' => 'AccountController',
        'purchase' => 'PurchaseController',
        'sale' => 'SaleController',
        'tag' => 'TagController',
        'transaction' => 'TransactionController',
    ]);
    Route::get('customer/sale/{id?}', 'SaleController@index');
    Route::get('customer/sale/{id?}', 'SaleController@index');
});
*/