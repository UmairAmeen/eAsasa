<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\StockManage;
use App\Transaction;
use App\AdminTransaction;
use App\DeliveryChallan;
use App\Customer;
use App\Products;
use App\ProductCategory;
use App\ProductGroup;
use App\Supplier;
use App\Invoice;
use App\Purchase;
use App\Order;
use App\Warehouse;
use App\SupplierPriceRecord;
use App\SaleOrder;
use App\SalesPerson;
use App\Expense;
use App\ExpenseHead;
use App\User;
// use App\Http\Controllers\SMController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Cache;
use Carbon\Carbon;
use GuzzleHttp\Psr7\Query;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller {

	public function __construct()
	{
		View::share('title',"Report");
		View::share('load_head',true);
		View::share('reports_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index(Request $request)
	{
		// $heightPerItem = 100;	// to adjust height for both sections multiple it with hight number for each row
		$reports = [
			'Accounts' => [
				// 'height' => $heightPerItem * 5,
				'list' => [
					['name' => 'Revenue Report', 'url' => 'reports/revenue_report', 'text' => 'Get A Detailed View of Revenue', 'icon' => '',],
					['name' => 'Cash In Hand Report', 'url' => 'reports/cash_in_hand', 'text' => 'Calculate Cash In Hand Amount', 'icon' => '',],
					['name' => 'Accounts Receivable Aging', 'url' => 'reports/aging', 'text' => 'Accounts Receivable Aging Sheet Reporting', 'icon' => ''],
					['name' => 'Delivery Wise Accounts Receivable Aging', 'url' => 'reports/delivery_wise_aging', 'text' => 'Accounts Receivable Aging Delivery Wise Sheet Reporting', 'icon' => ''],
					['name' => 'Expense Report', 'url' => 'reports/expense', 'text' => 'Get A Detailed View of Expenses', 'icon' => ''],
					['name' => 'Stock Worth Calculator&nbsp;', 'url' => 'reports/worth', 'text' => 'Get A Detailed View of Revenue', 'icon' => 'fa fa-lock'],
					['name' => 'Receivables History', 'url' => 'reports/receivable', 'text' => 'Payment/Balance for All Customers', 'icon' => ''],
					['name' => 'Payable History', 'url' => 'reports/payable', 'text' => 'Payment/Balance for All Suppliers', 'icon' => ''],
					['name' => 'Tax Report', 'url' => 'reports/tax', 'text' => 'Tax Added to All Sale/SaleOrder Invoice', 'icon' => ''],
					['name' => 'Shipping/Packing/Transport Report', 'url' => 'reports/shipping', 'text' => 'Shipping/Packing/Transport Cost Added to Sale/Sale Order Invoice', 'icon' => ''],
				]
			],
			'Sales' => [
				// 'height' => $heightPerItem * 5,
				'list' => [
					['name' => 'Day Discounts and Cashier Report', 'url' => 'reports/day_discount', 'text' => 'Get A Detailed View of who sold products and gave discounts', 'icon' => ''],
					['name' => 'Day Sale Report', 'url' => 'reports/day_sale', 'text' => 'Get A Detailed View of today'.'\''.'s sale', 'icon' => ''],
					['name' => 'Business Capital Insight', 'url' => 'reports/business_capital', 'text' => 'Business Capital Calculation Based on your Sale & Purhcases', 'icon' => 'fa fa-lock'],
					['name' => 'Profit/Loss Insight', 'url' => 'reports/profit_all', 'text' => 'Profit/Loss Calculation Based on your Sale & Purhcases', 'icon' => 'fa fa-lock'],
					['name' => 'Product Bundle Wise Profit/Loss', 'url' => 'reports/bundle_wise_profit', 'text' => 'Profit/Loss Calculation Based on your Product Bundles', 'icon' => 'fa fa-lock'],
					['name' => 'Sale Order Profit/Loss Insight&nbsp;', 'url' => 'reports/profit', 'text' => 'Profit/Loss Calculation Based on your Sale Orders', 'icon' => 'fa fa-lock'],
					['name' => 'Order Wise Profit/Loss &nbsp;', 'url' => 'reports/orderWise_profit', 'text' => 'Profit/Loss Calculation Based on All Sale Orders', 'icon' => 'fa fa-lock'],
					['name' => 'Completed Sale Order Profit/Loss Insight&nbsp;', 'url' => 'reports/profit_sale_order', 'text' => 'Profit/Loss Calculation Based on Completed Sale Orders with zero balance', 'icon' => ''],
					['name' => 'Sale Order Details', 'url' => 'reports/saleorders_details', 'text' => 'Complete details of Sale orders', 'icon' => ''],
					['name' => 'Sale Order(Completed) Details', 'url' => 'reports/completed_saleorders_details', 'text' => 'Completed Sale orders details for Commision calculation', 'icon' => ''],
					['name' => 'Return Sale Invoice', 'url' => 'reports/returnSale_invoice', 'text' => 'Complete details of Return Sale Invoice', 'icon' => ''],
					['name' => 'Delivery Details', 'url' => 'reports/delivery_report', 'text' => 'Delivery Details of Sale orders', 'icon' => ''],
					['name' => 'Sale Person Commission Report', 'url' => 'reports/salePerson_commission', 'text' => 'Get A Detailed View of Sale Person Commision', 'icon' => ''],
				],
			],
			'Suppliers' => [
				// 'height' => $heightPerItem * 4,
				'list' => [
					['name' => 'Supplier', 'url' => 'supplier_reporting/customer_reporting', 'text' => 'Complete Supplier Transaction Report', 'icon' => ''],
					['name' => 'Supplier Transaction', 'url' => 'supplier_reporting/balance_sheet', 'text' => 'Supplier Ledger Sheet', 'icon' => ''],
					['name' => 'Product History Supplier&nbsp;', 'url' => 'supplier_reporting/product_record', 'text' => 'List of Suppliers', 'icon' => 'fa fa-lock'],
					['name' => 'Supplier to Product Relations&nbsp;', 'url' => 'supplier_reporting/product_supplier', 'text' => 'Based on purchases, categories supplier with products', 'icon' => 'fa fa-lock'],
					['name' => 'Return Purchase Invoice&nbsp;', 'url' => 'supplier_reporting/returnPurchase_invoice', 'text' => 'Based on purchases, Return Purchase Invoice', 'icon' => ''],
					['name'	=> 'Purchase Dashboard', 'url' => 'supplier_reporting/purchase_dashboard', 'text' => 'Purchase Dashboard', 'icon' => '']
				],
			],

			'Customers' => [
				// 'height' => $heightPerItem * 4,
				'list' => [
					['name' => 'Customers&nbsp;', 'url' => 'reports/customer_reporting', 'text' => 'Complete Customers Report (Sales, Transactions, Stocks)', 'icon' => 'fa fa-lock'],
					['name' => 'Customer Transaction', 'url' => 'reports/balance_sheet', 'text' => 'Customer Ledger Sheet', 'icon' => ''],
					['name' => 'Product History Customer', 'url' => 'reports/product_record', 'text' => 'List of Customer, Last Products sold to customer at which price and quantity', 'icon' => ''],
				],
			],
			'Products' => [
				// 'height' => $heightPerItem * 3,
				'list' => [
					['name' => 'Top Selling Product&nbsp;', 'url' => 'reports/top_selling', 'text' => 'Get A Detailed View of top selling products', 'icon' => 'fa fa-lock'],
					['name' => 'Warehouse Report', 'url' => 'reports/department', 'text' => 'Get A Detailed View of Sale by Warehouse', 'icon' => ''],
					['name' => 'Purchase Insight&nbsp;', 'url' => 'reports/purchase_detailed', 'text' => 'BI for Product Purchase Insight', 'icon' => 'fa fa-lock'],
				],
			],
			'Stocks' => [
				// 'height' => $heightPerItem * 3,
				'list' => [
					['name' => 'Stock In/Out Report&nbsp;', 'url' => 'reports/stock_detail', 'text' => 'Detailed Stock Report', 'icon' => 'fa fa-lock'],
					['name' => 'ALL Stock In/Out Report', 'url' => 'reports/stock_in_out_view', 'text' => 'Stock Detailed In Out (ALL)', 'icon' => ''],
					['name' => 'WareHouse Transfer', 'url' => 'reports/warehouse_transfer', 'text' => 'Stock Detailed In Out (ALL)', 'icon' => ''],
				],
			],
			'Miscellaneous' => [
				// 'height' => $heightPerItem * 3,
				'list' => [
					['name' => 'Log History&nbsp;', 'url' => 'reports/log_report', 'text' => 'Details of Data Entered on this System', 'icon' => 'fa fa-lock'],
					['name' => 'Forcasting &nbsp;', 'url' => 'forcasting', 'text' => 'Forcasting with Artificial Intelligence', 'icon' => 'fa fa-lock'],
				],
			],
			'Deleted Sales & Orders' => [
				'list' => [
					['name' => 'Deleted Invoices &nbsp;', 'url' => 'reports/deleted_invioces', 'text' => 'Details of Deleted Invoices']
				],
			],
		];
		$chunked_array_report = array_chunk($reports,2,true);
		return view('reports.index', compact('request','chunked_array_report'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('reports.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$report = new Report();
		$report->save();
		return redirect()->route('reports.index')->with('message', 'Item created successfully.');
	}

	public function supplier_reporting($id, Request $request)
	{
		$from = ($request->has('from'))?$request->from:false;
		$to = ($request->has('to'))?$request->to:false;
		if (!is_allowed("report-product_supplier")) {
			return redirect("reports?error=You are not authorized to access this report");
		}
		switch ($id) {
			case 'balance_sheet':
				$users = User::pluck('name', 'id')->toArray();
				$debit_now = $credit_debit_percentage = $credit_now = false;
				//Getting all Parameter of Transaction
				$transaction = Transaction::orderBy('date','asc')->where('amount','<>',0);
				$debit_now = Transaction::where('type','in');
				$credit_now = Transaction::where('type','out');
				$customer_id = ($request->has('customer_id'))?$request->customer_id:false;
				// $supplier_id = ($request->has('supplier_id'))?$request->supplier_id:false;
				$customers = Supplier::all();
				$customers_id = Supplier::all()->pluck('id');
				// $suppliers = Supplier::all();
				if ($from) {
					$transaction = $transaction->where('date',">=",$from);
					$debit_now=$debit_now->where('date',">=",$from);
					$credit_now=$credit_now->where('date',">=",$from);
				}
				if ($to) {
					$transaction = $transaction->where('date',"<=",$to);
					$debit_now=$debit_now->where('date',"<=",$to);
					$credit_now=$credit_now->where('date',"<=",$to);
				}
				$type = 'supplier';
				$chart = [];
				if(intval($customer_id > 0)) {
					$transaction = $transaction->where('supplier_id',$customer_id);
					$debit_now=$debit_now->where('supplier_id',$customer_id);
					$credit_now=$credit_now->where('supplier_id',$customer_id);
					$debit_now=$debit_now->sum('amount');
					$credit_now=$credit_now->sum('amount');	
				} else {
					// $t_date = ($to == false) ? date('Y-m-d') : $to;
					// $transaction = DB::table('transaction AS t')
					// ->join('supplier AS s', 's.id', '=', 't.supplier_id')
					// ->select(
					// 	's.id', 's.name AS name',
					// 	DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM transaction WHERE supplier_id = t.supplier_id AND type =  'in' AND deleted_at IS NULL AND date <= '{$t_date}'), 0)) + 0 AS debit"),
					// 	DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM transaction WHERE supplier_id = t.supplier_id AND type = 'out' AND deleted_at IS NULL AND date <= '{$t_date}'), 0)) + 0 AS credit")
					// )->whereNull('t.deleted_at')->where('t.date', '<=', $t_date)->groupBy('t.supplier_id')->get();
					$f_date = ($from == false) ? date('Y-m-d') : $from;
					$t_date = ($to == false) ? date('Y-m-d') : $to;
					$chart = [];
					$transaction = DB::table('transaction AS t')
					->select([
						't.supplier_id AS id',
						DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM `transaction` WHERE deleted_at IS NULL AND `type` = 'in' AND supplier_id = t.supplier_id AND date < '$f_date'), 0)) + 0 AS open_credit"),
						DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM `transaction` WHERE deleted_at IS NULL AND `type` = 'out' AND supplier_id = t.supplier_id AND date < '$f_date'), 0)) + 0 AS open_debit"),
						DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM `transaction` WHERE deleted_at IS NULL AND `type` = 'in' AND supplier_id = t.supplier_id AND date BETWEEN '$f_date' AND '$t_date'), 0)) + 0 AS credit"),
						DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM `transaction` WHERE deleted_at IS NULL AND `type` = 'out' AND supplier_id = t.supplier_id AND date BETWEEN '$f_date' AND '$t_date'), 0)) + 0 AS debit")
					])->whereNull('t.deleted_at')->whereNotNull('t.supplier_id')->groupBy('t.supplier_id')->get();
					$persons = $customers->pluck('name', 'id')->toArray();
					return view('reports.balance_sheet', compact('chart', 'transaction', 'customers', 'persons', 'request', 'to', 'from', 'type', 'users'));
				}
				$transaction = $transaction->with('invoice', 'supplier', 'bank_detail')->get();
				$chart_data = [];
				$chart_data["amount_in"] = $payment_type_in = $chart_data["amount_out"] = $amount_in_array = $amount_out_array =[];
				foreach ($transaction as $key => $value) {
					# code...
					// $chart_data['date'] = $value->date;
					if ($value->type == "in") {
						if (!array_key_exists($value->payment_type, $payment_type_in)) {
							$payment_type_in[$value->payment_type] = 1;
						} else {
							$payment_type_in[$value->payment_type]++;
						}
						if (!array_key_exists($value->date, $chart_data["amount_in"])) {
							$chart_data["amount_in"][$value->date] = [Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000, $value->amount + 0];
						} else {
							$chart_data["amount_in"][$value->date] = [Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000, $chart_data["amount_in"][$value->date][1] + $value->amount + 0];
						}
						// $chart_data["amount_out"][] = 0; 
					} else {
						if (!array_key_exists($value->date, $chart_data["amount_out"])) {
							$chart_data["amount_out"][$value->date] = [Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000, $value->amount + 0];
						} else {
							$chart_data["amount_out"][$value->date] = [Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000, $chart_data["amount_out"][$value->date][1] + $value->amount + 0]; 
						} 
						// $chart_data["amount_in"][] = 0; 
					}
				}
				foreach ($chart_data['amount_in'] as $key => $value) {
					$amount_in_array[] = $value;
				}
				foreach ($chart_data['amount_out'] as $key => $value) {
					$amount_out_array[] = $value;
				}
				$payment_type_in_output = [];
				foreach ($payment_type_in as $key => $value) {
					$payment_type_in_output[] = ["name"=>$key, "y"=>$value];
				}
				//making graphs
				$chart = [
				    'chart' => ['type' => 'line'],
				    'title' => ['text' => 'Balance Sheet Representation'],
				    'xAxis' => ['type' => 'datetime',],
				    'yAxis' => ['title' => ['text' => 'Amount']],
					"tooltip" => ["split"=> true],
				    'series' => [
				        ['name' => 'Debit','data' => $amount_in_array],
				        ['name' => 'Credit','data' => $amount_out_array],
				    ]
				];
				$payment_type_chart_amount_in = [
					'chart' => ['type' => 'pie'],
				    'title' => ['text' => 'Debit Payment Type'],
				    'xAxis' => ['type' => 'datetime',],
				    'yAxis' => ['title' => ['text' => 'Type of Payments']],
				    'series' => [
						[
				        	'colorByPoint' => true,
				            'name' => 'Amount Type',
				            'data' => $payment_type_in_output
				        ]
				    ]
				];
				if ($credit_now) {
					$credit_debit_percentage = [
						'chart' => ['type' => 'pie'],
					    'title' => ['text' => 'Balance'],
					    'xAxis' => ['type' => 'datetime',],
					    'yAxis' => ['title' => ['text' => 'Current Balance']],
					    "plotOptions" => [
							"pie" => [
								"allowPointSelect" => true,
								"cursor" => 'pointer',
								"dataLabels" => ["enabled"=>true,"format"=>'<b>{point.name}</b>: {point.percentage:.1f} %',]
							]
				    	],
					    'series' => [
					        [
					        	'colorByPoint'=>true,
					            'name' => 'Amount Type',
					            'data' => [
					            	["name"=>'credit', "y"=>doubleval($credit_now)],
					            	["name"=>'debit', "y"=>doubleval($debit_now)],
					            ]
					        ]
					    ]
					];
				}
				$opening_balance = getSupplierOpeningBalance($customer_id, $request->from);
				return view('reports.balance_sheet', compact("opening_balance",'chart','transaction','payment_type_chart_amount_in','customers','request','to','from','debit_now','credit_debit_percentage','credit_now', 'type', 'users'));
				break;
			case 'customer_reporting':
				$customer = Supplier::whereId($request->customer_id)->first();
				$customers = Supplier::all();
				$chart = false;
				$debit_now = $credit_now = $all_credit_now = $all_debit_now = 0;
				$type="supplier";
				if (!$customer) {
					return view('reports.customer_reporting',compact('chart','customer','from','to','request','customers','debit_now','credit_now','all_credit_now','all_debit_now','type'));
				}
				$all_debit_now = Transaction::where('type','in')->where('supplier_id',$customer->id)->sum('amount');
				$all_credit_now = Transaction::where('type','out')->where('supplier_id',$customer->id)->sum('amount');
				$transaction = Transaction::orderBy('date','asc');
				$query4 = clone $transaction;
				if ($from) {
					$transaction = $transaction->where('date',">=",$from);
				}
				if ($to) {
					$transaction = $transaction->where('date',"<=",$to);
				}
				if($customer) {
					$transaction = $transaction->where('supplier_id',$customer->id);
					$query2 = clone $transaction;
					$query3 = clone $transaction;
					$debit_now = $query2->where('type','in')->sum('amount');
					$credit_now = $query3->where('type','out')->sum('amount');	
					$query4->where('supplier_id',$customer->id);
				}
				//monthly bases
				$transaction = $query4->get();
				$debit_collection = $credit_collection = [];
				foreach ($transaction as $key => $value) {
					# code...
					$key_format = date('M-Y', strtotime($value->date));
					if ($value->type == "in") {//debit
						if (array_key_exists($key_format, $debit_collection)) {
							$debit_collection[$key_format] += $value->amount;
						} else {
							$debit_collection[$key_format] = $value->amount;
						}
					}
					if ($value->type == "out") {//credit
						if (array_key_exists($key_format, $debit_collection)) {
							$credit_collection[$key_format] += $value->amount;
						} else {
							$credit_collection[$key_format] = $value->amount;
						}
					}
				}
				$collection_percentage = $credit_output = $debit_output = [];
				foreach ($credit_collection as $key => $value) {
					# code...
					if (array_key_exists($key, $debit_collection)) {
						$collection_percentage[] = [strtotime($key)*1000, floatval($debit_collection[$key]/$credit_collection[$key])*100];
					}
					$credit_output[] = [strtotime($key)*1000, $value];
				}
				foreach ($debit_collection as $key => $value) {
					# code...
					$debit_output[] = [strtotime($key)*1000, $value];
				}
				// $credit_output
				// var_dump($collection_percentage);
				$t2 = $to;
				$f2 = $from;
				if (!$t2) {
					$t2 = "1970-1-1";
				}
				if (!$f2) {
					$f2 = "1970-1-1";
				}
				$chart = [
					"title" => ["text"=> 'Collection Chart'],
					"subtitle" => ["text" => 'Recovery Percentage Graph Distributed Over Monthly'],
					"yAxis" => [
						["title" => ["text"=> 'Recovery percentage'],"labels"=>["format"=>'{value}%',]],
						["title" => ["text"=> 'Amount'],"opposite"=>true,"labels" => ["align"=>'right'],]
					],
					"xAxis" => [
						'type' => 'datetime',
						"plotBands"=>[[ // visualize the weekend
							"from"=>strtotime($f2)*1000,
							"to"=>strtotime($t2)*1000,
							"color"=>'rgba(68, 170, 213, .2)'
						]]
					],
					"legend" => ["layout" => 'vertical', "align" => 'right', "verticalAlign" => 'middle'],
					"plotOptions" => ['series' => ['label'=> ['connectorAllowed'=>false], 'pointStart'=> 2010]],
					'series' => [
						['name'=>'Percentage','data'=>$collection_percentage,'yAxis'=>0,],
						['name'=>'Credit Taken','data'=>$credit_output,'yAxis'=>1,],
						['name'=>'Debit Added','data'=>$debit_output,'yAxis'=>1,]
					],
					"tooltip" => ["shared"=> true],
					"responsive" => [
						"rules"=>[[
							"condition"=>["maxWidth"=>500],
							"chartOptions" => [
								"legend" => ["layout" => 'horizontal',"align" => 'center',"verticalAlign" => 'bottom']
							]
						]]
					]
				];
				return view('reports.customer_reporting',compact('chart','customer','from','to','request','customers','debit_now','credit_now','all_credit_now','all_debit_now','type'));
				break;
				case 'returnPurchase_invoice':

					$from = ($from)?:date('Y-m-01');
					$to = ($to)?:date('Y-m-t');
					
					$is_return = $request->is_return;
					$return_purchases =  !$request->is_return ? "and `i`.total < 0" : " ";
					$returnPurchase_invoice = DB::select("
					SELECT invoice_idd, GROUP_CONCAT(t.orderID) AS order_id,  GROUP_CONCAT(t.product_id) AS prod_id, GROUP_CONCAT(t.quantity) AS prod_quantity,GROUP_CONCAT(t.prodName SEPARATOR ', <br>') AS pName,t.* FROM (
					SELECT 
					  o.id AS orderID,
					  `p`.`id` AS `product_id`,
					  `p`.`unit_id` AS `unit`,
					  `i`.`id` AS `invoice_idd`,
					  `i`.`bill_number` AS `bill_number`,
					  CONCAT_WS(' ', p.name, p.brand, p.description, o.note ) AS prodName,
					  `o`.`salePrice` AS `p_price`,
					  `i`.`related_to` AS `invoiceRelatedTo`,
					  `i`.`total` AS `invoiceTotal`,
					  `i`.`date` AS `invoiceDate`,
					  `i`.`description` AS `notes`,
					  `i`.`supplier_id` AS `supplier_id`,
					  `i`.`total` AS `total`,
					  `supplier`.`name` AS `pName`,
					  `supplier`.`phone` AS `pPhone`,
					  `supplier`.`address` AS `pAddress`,
					  `o`.`quantity` AS `quantity`,
					  `i`.`type` AS `invoiceType`,
					  (SELECT     SUM(amount)   FROM    `transaction`   WHERE invoice_id = invoice_idd     AND `type` = 'out'    AND deleted_at IS NULL) AS tOut,
					  (SELECT     SUM(amount)   FROM    `transaction`   WHERE invoice_id = invoice_idd     AND `type` = 'in'     AND deleted_at IS NULL) AS tIn 
					FROM  `order` AS `o` 
					  INNER JOIN `products` AS `p`    ON `p`.`id` = `o`.`product_id`     AND `p`.`deleted_at` IS NULL 
					  LEFT JOIN `invoice` AS `i`     ON `i`.`id` = `o`.`invoice_id`     AND `i`.`type` LIKE 'purchase%' 
					  LEFT JOIN `supplier` AS `supplier`  ON  `supplier`.`id` = `i`.`supplier_id`
					WHERE `i`.`date` BETWEEN '{$from}'   AND '{$to}' AND `i`.`type` = 'purchase' ".$return_purchases."
					  AND `o`.`deleted_at` IS NULL 
					ORDER BY `i`.`id` DESC 
					) AS t GROUP BY invoice_idd
					");
	
					foreach ($returnPurchase_invoice as $key => $value) {
						$value->balance = $value->tOut - $value->tIn;
					}
					return view('reports.returnPurchase_invoice',compact('is_return','from','to','returnPurchase_invoice'));
					break;


			case 'product_supplier':
				//password protected
				// $validated = is_password_validated();
				// 	if (!$validated) {
				// 	  header('WWW-Authenticate: Basic realm="My Realm"');
				// 	  header('HTTP/1.0 401 Unauthorized');
				// 	  die ("Not authorized");
				// 	}
				//password protected
				$suppliers = Supplier::all();
				$supplier = Supplier::whereId($request->supplier_id)->first();
				$can_view_purchase_price = is_allowed('product-show-purchase-price');
				$purchases = [];
				if ($supplier) {
					// $product_price=[];
					$all_invoice = Invoice::where('type', 'purchase')
						->where('supplier_id', $request->supplier_id)->orderBy('date','desc')->get();
					foreach ($all_invoice as $key => $value) {
						foreach ($value->orders as $k2 => $v2) {
							if (array_key_exists($v2->product_id, $purchases)) {
								continue;
							}
							$purchases[$v2->product_id] = [];
							$purchases[$v2->product_id]['invoice_id'] = $value->id;
							$purchases[$v2->product_id]['date'] = $value->date;
							$purchases[$v2->product_id]['price'] = $can_view_purchase_price ? $v2->salePrice + 0 : '-';
							$purchases[$v2->product_id]['qty'] = $v2->quantity + 0;
						}
					}
				}
				return view('reports.product_supplier',compact('suppliers','request','supplier','purchases'));
				break;
			case 'product_record';
				//password protected
				// $validated = is_password_validated();
				// if (!$validated) {
				//   header('WWW-Authenticate: Basic realm="My Realm"');
				//   header('HTTP/1.0 401 Unauthorized');
				//   die ("Not authorized");
				// }
				//password protected
				$products = Products::all();
				$product = get_product($request->product_id);
				$purchases = [];
				if ($product) {
					$can_view_purchase_price = is_allowed('product-show-purchase-price');
					$invoices = Invoice::where('type','purchase')->orderBy('date','desc')->pluck('id')->toArray();
					$orders = Order::where('product_id',$request->product_id)->whereIn('invoice_id',$invoices)->get();
					foreach ($orders as $key => $value) {
						$pk_k = $value->invoice->supplier_id.$value->invoice->date;
						if (array_key_exists($pk_k, $purchases)) {
							continue;
						}
						$purchases[$pk_k]['supplier_id'] = $value->invoice->supplier_id;
						$purchases[$pk_k]['price'] = $can_view_purchase_price ? $value->salePrice : 'NA';
						$purchases[$pk_k]['qty'] = $value->quantity;
						$purchases[$pk_k]['invoice'] = $value->invoice;
					}
				}
				return view('reports.product_record',compact('product','products','request','purchases'));
				break;

			case 'purchase_dashboard':
				if (!$from)
				{
					$from = date('Y-m-d',strtotime('-1 month'));
				}
				if (!$to)
				{
					$to = date('Y-m-d');
				}
				//number of supplier we deal in date range
				$invoices = Invoice::where('type','purchase')->whereBetween('date',[$from, $to])->get();
				$suppliers = $products = [];
				$value_ordered = $invoices->sum('total');
				$total_order_count = $total_return_count = 0;
				$total_order_value = $total_return_value = 0;
				foreach($invoices as $invoice)
				{
					//get all supplier
					if(!array_key_exists($invoice->supplier_id, $suppliers))
					{
						$suppliers[$invoice->supplier_id] = ['name'=>$invoice->supplier->name,'total'=>0, 'return'=>0,'ordered_quantity'=>0, 'return_quantity'=>0];
					}
					
					foreach($invoice->orders as $order)
					{
						//get all products purchase data
						if(!array_key_exists($order->product_id, $products))
						{
							$products[$order->product_id] = ['name'=>$order->product->name,'quantity'=>0,'total'=>0, 'return_qty'=>0, 'return_total'=>0];
						}
						if ($order->quantity > 0)
						{
							//purchasing
							$products[$order->product_id]['quantity'] += $order->quantity;
							$products[$order->product_id]['total'] += $order->salePrice*$order->quantity;

							$suppliers[$invoice->supplier_id]['ordered_quantity'] += $order->quantity;
							$suppliers[$invoice->supplier_id]['total'] += $products[$order->product_id]['total'];
							$total_order_count += $order->quantity;
							$total_order_value += $products[$order->product_id]['total'];
						}else{
							//return/damange
							$products[$order->product_id]['return_qty'] += abs($order->quantity);
							$products[$order->product_id]['return_total'] += $order->salePrice*abs($order->quantity);

							$suppliers[$invoice->supplier_id]['return_quantity'] += $products[$order->product_id]['return_qty'];
							$suppliers[$invoice->supplier_id]['return'] += $products[$order->product_id]['return_total'];
							$total_return_count += abs($order->quantity);
							$total_return_value += $products[$order->product_id]['return_total'];
						}

					}
				}
				//return cost analysis
				//value ordered
				//value rejected
				//vendor rejection rate 
				//top products purchased
				//top value purchased
				
				return view('reports.purchase_dashboard',compact('request','suppliers','products','value_ordered','from','to','total_order_count','total_return_count','total_order_value','total_return_value'));
				break;
			default:
				return redirect('reports');
				break;
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id, Request $request)
	{
		$from = ($request->has('from'))?$request->from:false;
		$to = ($request->has('to'))?$request->to:false;
		if (!is_allowed("report-".$id)) {
			return redirect("reports?error=You are not authorized to access this report");
		}
		switch ($id) {
			case 'balance_sheet':
				$users = User::pluck('name', 'id')->toArray();
				$type = "customer";
				$debit_now = $credit_debit_percentage =$credit_now = false;
				//Getting all Parameter of Transaction
				$transaction = Transaction::orderBy('date','asc')->where('amount','<>',0);
				$customer_id = ($request->has('customer_id'))?$request->customer_id:false;
				// $supplier_id = ($request->has('supplier_id'))?$request->supplier_id:false;
				$customers = Customer::all();
				$customers_id = Customer::all()->pluck('id');
				// dd($customers_id);
				$debit_now = Transaction::where('type','in');
				$credit_now = Transaction::where('type','out');
				// $suppliers = Supplier::all();
				if($from) {
					$transaction = $transaction->where('date',">=",$from);
					$debit_now=$debit_now->where('date',">=",$from);
					$credit_now=$credit_now->where('date',">=",$from);
				}
				if ($to) {
					$transaction = $transaction->where('date',"<=",$to);
					$debit_now=$debit_now->where('date',"<=",$to);
					$credit_now=$credit_now->where('date',"<=",$to);
				}
				if(intval($customer_id > 0)) {
					$debit_now=$debit_now->where('customer_id',$customer_id);
					$credit_now=$credit_now->where('customer_id',$customer_id);
					$transaction = $transaction->where('customer_id',$customer_id);
					$debit_now=$debit_now->sum('amount');
					$credit_now=$credit_now->sum('amount');	
				} else {
					$f_date = ($from == false) ? date('Y-m-d') : $from;
					$t_date = ($to == false) ? date('Y-m-d') : $to;
					$chart = [];
					$transaction = DB::table('transaction AS t')
					->select([
						't.customer_id AS id',
						DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM `transaction` WHERE deleted_at IS NULL AND `type` = 'in' AND customer_id = t.customer_id AND date < '$f_date'), 0)) + 0 AS open_credit"),
						DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM `transaction` WHERE deleted_at IS NULL AND `type` = 'out' AND customer_id = t.customer_id AND date < '$f_date'), 0)) + 0 AS open_debit"),
						DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM `transaction` WHERE deleted_at IS NULL AND `type` = 'in' AND customer_id = t.customer_id AND date BETWEEN '$f_date' AND '$t_date'), 0)) + 0 AS credit"),
						DB::raw("TRIM(IFNULL((SELECT SUM(amount) FROM `transaction` WHERE deleted_at IS NULL AND `type` = 'out' AND customer_id = t.customer_id AND date BETWEEN '$f_date' AND '$t_date'), 0)) + 0 AS debit")
					])->whereNull('t.deleted_at')->whereNotNull('t.customer_id')->groupBy('t.customer_id')->get();
					$persons = $customers->pluck('name', 'id')->toArray();
					return view('reports.balance_sheet', compact('chart', 'transaction', 'customers', 'persons', 'request', 'to', 'from', 'type', 'users'));
				}
				$transaction = $transaction->with('invoice', 'customer')->get();
				$chart_data = [];
				$chart_data["amount_in"] = $payment_type_in = $chart_data["amount_out"] = $amount_in_array = $amount_out_array =[];
				foreach ($transaction as $key => $value) {
					# code...
					// $chart_data['date'] = $value->date;
					if ($value->type == "in") {
						if (!array_key_exists($value->payment_type, $payment_type_in)) {
							$payment_type_in[$value->payment_type] = 1;
						} else {
							$payment_type_in[$value->payment_type]++;
						}
						if (!array_key_exists($value->date, $chart_data["amount_in"])) {
							$chart_data["amount_in"][$value->date] = [Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000, $value->amount + 0];
						} else {
							$chart_data["amount_in"][$value->date] = [Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000, $chart_data["amount_in"][$value->date][1] + $value->amount + 0]; 
						}
						// $chart_data["amount_out"][] = 0; 
					} else {
						if (!array_key_exists($value->date, $chart_data["amount_out"])) {
							$chart_data["amount_out"][$value->date] = [Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000, $value->amount + 0];
						} else {
							$chart_data["amount_out"][$value->date] = [Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000, $chart_data["amount_out"][$value->date][1] + $value->amount + 0]; 
						}// $chart_data["amount_in"][] = 0; 
					}
				}//end loop
				foreach ($chart_data['amount_in'] as $key => $value) {# code...
					$amount_in_array[] = $value;
				}
				foreach ($chart_data['amount_out'] as $key => $value) {# code...					
					$amount_out_array[] = $value;
				}
				$payment_type_in_output = [];
				foreach ($payment_type_in as $key => $value) {# code...					
					$payment_type_in_output[] = ["name"=>$key, "y"=>$value];
				}
				//making graphs
				$chart = [
				    'chart' => ['type' => 'line'],
				    'title' => ['text' => 'Balance Sheet Representation'],
				    'xAxis' => ['type' => 'datetime',],
				    'yAxis' => ['title' => ['text' => 'Amount']],
					"tooltip" => ["split"=> true],
					'series' => [
						['name' => 'Debit', 'data' => $amount_in_array],
						['name' => 'Credit', 'data' => $amount_out_array],
					]
				];
				$payment_type_chart_amount_in = [
					'chart' => ['type' => 'pie'],
				    'title' => ['text' => 'Debit Payment Type'],
				    'xAxis' => ['type' => 'datetime',],
				    'yAxis' => ['title' => [ 'text' => 'Type of Payments' ]],
				    'series' => [['colorByPoint'=>true, 'name' => 'Amount Type', 'data' => $payment_type_in_output]]
				];
				if ($credit_now) {
					$credit_debit_percentage = [
						'chart' => ['type' => 'pie'],
					    'title' => ['text' => 'Balance'],
					    'xAxis' => ['type' => 'datetime',],
					    'yAxis' => ['title' => ['text' => 'Current Balance']],
					    "plotOptions" => [
							"pie" => [
								"allowPointSelect"=>true,
								"cursor"=>'pointer',
								"dataLabels" => ["enabled" => true,"format" => '<b>{point.name}</b>: {point.percentage:.1f} %',]
							]
				    	],
					    'series' => [[
							'colorByPoint' => true,
							'name' => 'Amount Type',
							'data' => [
								["name"=>'credit', "y"=>doubleval($credit_now)],
								["name"=>'debit', "y"=>doubleval($debit_now)],
							]
						]]
					];
				}
				$opening_balance = getCustomerOpeningBalance($customer_id, $request->from);
				return view('reports.balance_sheet', compact('chart','transaction','payment_type_chart_amount_in','customers','request','to','from','debit_now','credit_debit_percentage','credit_now','type','opening_balance', 'users'));
				break;
			// Another case
			case "top_selling":
				//password protected
				$validated = is_password_validated();
				if (!$validated) {
					header('WWW-Authenticate: Basic realm="My Realm"');
					header('HTTP/1.0 401 Unauthorized');
					die ("Not authorized");
				}//password protected					
				$stc = StockManage::select(DB::raw('sum(quantity) as qty, product_id '))->whereIn('type',['out','sale'])->groupBy('product_id')->orderBy('qty','desc');
				if ($from) {
					$stc = $stc->where('date',">=",$from);
				}
				if($to) {
					$stc = $stc->where('date',"<=",$to);
				}
				if (!intval($request->limit)) {
					$request->limit = 10;
				}
				$stc = $stc->limit($request->limit);
				$stocks = $stc->get();
				$product_output = [];
				foreach ($stocks as $key => $value) {
					$product_output[] = ["name"=>$value->product->name, "y"=>intval($value->qty)];
				}
				$type_chart_amount = [
					'chart' => ['type' => 'pie'],
					'title' => ['text' => 'Top Selling Products'],
					'xAxis' => ['type' => 'datetime',],
					'yAxis' => ['title' => ['text' => 'Top selling Products']],
					"plotOptions" => [
						"pie" => [
							"allowPointSelect" => true,
							"cursor" => 'pointer',
							"dataLabels" => ["enabled" => true, "format"=>'<b>{point.name}</b>: {point.percentage:.1f} %',]
						]
					],
					'series' => [['colorByPoint'=>true, 'name' => 'Products', 'data' => $product_output]]
				];
				return view('reports.top_seller',compact('stocks','request','to','from','type_chart_amount'));
				break;
				// Another case
			case "stock_detail":
				//password protected
				$validated = is_password_validated();

				if (!$validated) {
				  header('WWW-Authenticate: Basic realm="My Realm"');
				  header('HTTP/1.0 401 Unauthorized');
				  die ("Not authorized");
				}
				//password protected
				$stc_out = StockManage::select(DB::raw('sum(quantity) as qty, date, type'))->whereIn('type',['out','sale'])->groupBy('date');
				$stc_in = StockManage::select(DB::raw('sum(quantity) as qty, date, type'))->whereIn('type',['in','purchase', 'refund'])->groupBy('date');

				if ($from) {
					$stc_out = $stc_out->where('date',">=",$from);
					$stc_in = $stc_in->where('date',">=",$from);
				}
				if ($to) {
					$stc_out = $stc_out->where('date',"<=",$to);
					$stc_in = $stc_in->where('date',"<=",$to);
				}
				if (!intval($request->product_id)) {
					$request->product_id = 0;
				}
				$stc_out = $stc_out->where('product_id',$request->product_id);
				$stc_in = $stc_in->where('product_id',$request->product_id);
				$stocks_out = $stc_out->get();
				$stocks_in = $stc_in->get();
				$chart = [];
				$product_id = $request->product_id;
				if (count($stocks_out) || count($stocks_in)) {
					$product = Products::whereId($request->product_id)->first();
					if (!$product) {
						return view('reports.stock_report',compact('stocks_out','stocks_in','request','to','from','chart'));
					}
				} else {
					#if nothing retunred
					return view('reports.stock_report',compact('stocks_out','stocks_in','request','to','from','chart'));
				}
				$amount_in_array = [];
				$amount_out_array = [];
				foreach ($stocks_out as $key => $value) {
					# code...
					$timestamp = Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000;
					$amount_out_array[] = [$timestamp, intval($value->qty)];
				}
				foreach ($stocks_in as $key => $value) {
					# code...
					$timestamp = Carbon::createFromFormat('Y-m-d',$value->date)->timestamp*1000;
					$amount_in_array[] = [$timestamp, intval($value->qty)];
				}
				$chart = [
					'chart' => ['type' => 'area'],
				    'title' => ['text' => $product->name.' Stock Report'],
				    'xAxis' => ['type' => 'datetime',],
				    'yAxis' => ['title' => ['text' => 'Quantity']],
					"tooltip"=>["split"=> true],					
				    'series' => [
				        ['name' => 'Product In', 'data' => $amount_in_array, 'connectNull'=>true],
				        ['name' => 'Product Out', 'data' => $amount_out_array, 'connectNull'=>true],
				    ]
				];
				return view('reports.stock_report',compact('stocks_out','product_id','stocks_in','request','to','from','chart'));				
				break;

			case 'stock_in_out_view':
				$product_categories = ProductCategory::pluck('name','id');
				$from = ($from == false) ? date('Y-m-1') : date('Y-m-d', strtotime($from));
				$to = ($to == false) ? date('Y-m-d') : date('Y-m-d', strtotime($to));
				$stock_in_out = DB::table('stocklog AS l')->join('products AS p', 'l.product_id', '=', 'p.id')
					->select(
						'p.id', 'p.name AS name', 'p.brand', 'p.itemcode','p.category_id','p.size',
						DB::raw("TRIM(IFNULL((SELECT SUM(quantity) FROM stocklog WHERE product_id = p.id AND type IN('in', 'purchase') AND deleted_at IS NULL AND date >= '{$from}' AND date <= '{$to}'), 0)) + 0 AS stockIn"),
						DB::raw("TRIM(IFNULL((SELECT SUM(quantity) FROM stocklog WHERE product_id = p.id AND type IN('out', 'sale') AND deleted_at IS NULL AND date >= '{$from}' AND date <= '{$to}'), 0)) + 0 AS stockOut")
					)->whereNull('p.deleted_at')->whereBetween('l.date', [$from, $to])->groupBy('p.id')->get();
				return view('reports.stock_in_out',compact('stock_in_out', 'request','product_categories'));
				break;

			case 'customer_reporting':
				// password protected
				// $validated = is_password_validated();
				// 	if (!$validated) {
				// 	  header('WWW-Authenticate: Basic realm="My Realm"');
				// 	  header('HTTP/1.0 401 Unauthorized');
				// 	  die ("Not authorized");
				// 	}
				//password protected
				$customer = Customer::whereId($request->customer_id)->first();
				$customers = Customer::all();
				$chart = false;
				$debit_now = $credit_now = $all_credit_now = $all_debit_now = 0;
				$type="customer";
				if (!$customer) {
					return view('reports.customer_reporting',compact('chart','customer','from','to','request','customers','debit_now','credit_now','all_credit_now','all_debit_now','type'));
				}
				$all_debit_now = Transaction::where('type','in')->where('customer_id',$customer->id)->sum('amount');
				$all_credit_now = Transaction::where('type','out')->where('customer_id',$customer->id)->sum('amount');
				$transaction = Transaction::orderBy('date','asc');
				$query4 = clone $transaction;
				if ($from) {
					$transaction = $transaction->where('date',">=",$from);
				}
				if ($to) {
					$transaction = $transaction->where('date',"<=",$to);
				}
				if($customer) {
					$transaction = $transaction->where('customer_id',$customer->id);
					$query2 = clone $transaction;
					$query3 = clone $transaction;
					$debit_now = $query2->where('type','in')->sum('amount');
					$credit_now = $query3->where('type','out')->sum('amount');	
					$query4->where('customer_id',$customer->id);
				}
				//monthly bases
				//check
				$transaction = $query4->get();
				$debit_collection = $credit_collection = [];
				foreach ($transaction as $key => $value) {
						# code....
					$key_format = date('M-Y', strtotime($value->date));
					if ($value->type == "in") {//debit
						if (array_key_exists($key_format, $debit_collection)) {
							$debit_collection[$key_format] += $value->amount;
						} else {
							$debit_collection[$key_format] = $value->amount;
						}
					}
					if ($value->type == "out") {//credit
						if (array_key_exists($key_format, $credit_collection)) {
							$credit_collection[$key_format] += $value->amount;
						} else {
							$credit_collection[$key_format] = $value->amount;
						}
					}
				}
				$collection_percentage = $credit_output = $debit_output = [];
				foreach ($credit_collection as $key => $value) {
					# code...
					if (array_key_exists($key, $debit_collection)) {
						$collection_percentage[] = [strtotime($key)*1000, floatval($debit_collection[$key]/$credit_collection[$key])*100];
					}
					$credit_output[] = [strtotime($key)*1000, $value];
				}
				foreach ($debit_collection as $key => $value) {
					# code...
					$debit_output[] = [strtotime($key)*1000, $value];
				}
				// $credit_output
				// var_dump($collection_percentage);
				$t2 = $to;
				$f2 = $from;
				if (!$t2) {
					$t2 = "1970-1-1";
				}
				if (!$f2) {
					$f2 = "1970-1-1";
				}
				$chart = [
					"title" => ["text"=> 'Collection Chart'],
					"subtitle" => ["text"=>'Recovery Percentage Graph Distributed Over Monthly'],
					"yAxis" => [
						[
							"title"=>["text"=> 'Recovery percentage'],
							"labels"=>["format"=>'{value}%',]
						], [
							"title" => ["text"=> 'Amount'],
							"opposite" => true,
							"labels" => ["align"=>'right'],
						]
					],
					"xAxis" => [
						'type' => 'datetime',
						"plotBands" => [
							[ // visualize the weekend
								"from" => strtotime($f2)*1000,
								"to" => strtotime($t2)*1000,
								"color" => 'rgba(68, 170, 213, .2)'
							]
						]
					],
					"legend" => [
						"layout" => 'vertical',
						"align" => 'right',
						"verticalAlign" => 'middle'
					],
					"plotOptions" => [
						'series' => [
							'label' => ['connectorAllowed' => false],
							'pointStart'=> 2010
						]
					],
					'series' => [
						['name' => 'Percentage', 'data' => $collection_percentage, 'yAxis' => 0,],
						['name' => 'Credit Taken', 'data' => $credit_output, 'yAxis' => 1,],
						['name' => 'Debit Added', 'data' => $debit_output, 'yAxis' => 1,]
					],
					"tooltip" => ["shared"=> true],
					"responsive"=>[
						"rules"=>[
							[
								"condition"=>["maxWidth" => 500],
								"chartOptions" => [
									"legend" => [
										"layout" => 'horizontal',
										"align" => 'center',
										"verticalAlign" => 'bottom'
									]
								]
							]
						]
					]
				];
				$orders = Invoice::where('customer_id', $request->customer_id);
				if ($request->from) {
					$orders = $orders->where('date',">=", $request->from);
				}
				if ($request->to) {
					$orders = $orders->where('date',"<=", $request->to);
				}
				$orders = $orders->get();
				return view('reports.customer_reporting',compact('chart','customer','from','to','request','customers','debit_now','credit_now','all_credit_now','all_debit_now','type', 'orders'));
				break;
			case 'receivable':					
				$customers = Customer::all();
				$blnc = [];
				foreach ($customers as $key => $value) {
					$balance = getCustomerBalance($value->id);
					if ($balance <= 0) {
						continue;
					}
					$blnc[] = [
						'customer'=>$value,
						'balance'=>$balance,
						'last_paid'=>last_payment_made_customer($value->id)
					];
				}
				return view('reports.receiveable',compact('from','to','request','blnc'));
				break;
			case 'payable':			
				$suppliers = Supplier::all();
				$blnc = [];
				foreach ($suppliers as $key => $value) {
					$balance = getSupplierBalance($value->id);
					if (!$balance) {
						continue;
					}
					$blnc[] = [
						'supplier'=>$value,
						'balance'=>abs($balance),
						'last_paid'=>last_payment_made_supplier($value->id)
					];
				}
				return view('reports.payable',compact('from','to','request','blnc'));
				break;
			case 'log_report':
				//password protected
				$validated = is_password_validated();
				if (!$validated) {
				  header('WWW-Authenticate: Basic realm="My Realm"');
				  header('HTTP/1.0 401 Unauthorized');
				  die ("Not authorized");
				}
				//password protected
				if(!$from) {
					$from = date("Y-m-d");
				}
				// $from = "2019-01-01";
				$data['customers'] = Customer::whereDate('created_at',"=",$from)->get();
				$data['suppliers'] = Supplier::whereDate('created_at',"=",$from)->get();
				$data['products'] = Products::whereDate('created_at',"=",$from)->get();
				$data['transactions'] = Transaction::whereDate('created_at',"=",$from)->get();
				$data['warehouses'] = Warehouse::whereDate('created_at',"=",$from)->get();
				$data['sale_orders'] = Invoice::where('type','sale_order')->whereDate('created_at',"=",$from)->get();
				$data['purchases'] = Invoice::where('type','purchase')->whereDate('created_at',"=",$from)->get();
				$data['refunds'] = Invoice::where('type','refund_customer')->whereDate('created_at',"=",$from)->get();
				$data['sales'] = Invoice::where('type','sale')->whereDate('created_at',"=",$from)->get();
				$data['stocks'] = StockManage::whereDate('created_at',"=",$from)->get();
				return view('reports.log_report',compact('from','to','request','data'));
				break;
				//this is profit loss
			case 'profit':
				//password protected
				// $validated = is_password_validated();

				// 	if (!$validated) {
				// 	  header('WWW-Authenticate: Basic realm="My Realm"');
				// 	  header('HTTP/1.0 401 Unauthorized');
				// 	  die ("Not authorized");
				// 	}
				//password protected

				if (!$from) {
					$from = date('Y-m-1');
				}
				if (!$to) {
					$to = date('Y-m-d');
				}
				// $sales = SaleOrder::where('posted',1);
				// if ($request->sale_id) {
				// 	$to = $from = false;
				// 	$sales = SaleOrder::where('invoice_id', $request->sale_id);
				// }
				// if ($from) {
				// 	$sales = $sales->where('date','>=',$from);
				// }
				// if ($to) {
				// 	$sales = $sales->where('date','<=',$to);
				// }
				// $sales = $sales->get()->pluck('invoice_id')->toArray();
				// $invoice = Order::whereIn('invoice_id',$sales)->get();
				$query = DB::table('sale_order_view');
				if ($request->sale_id) {
					$query->where('invoice_id', $request->sale_id);
				} else {
					$query->whereBetween('invoice_date', [$from, $to]);
				}
				$invoice = $query->get();
				$product = [];
				foreach ($invoice as $key => $value) {
					if (!array_key_exists($value->product_id, $product)) {
						$product[$value->product_id] = [
							'name' => "{$value->product_name} {$value->brand} {$value->product_description} {$value->product_itemcode}",
							'total' => 0,
							'sale' => 0,
							'profit' => 0,
							'total_purchase'=>0,
							'total_sale'=>0,
						];
					}
					// $product[$value->product_id]['total']  += $value->salePrice*$value->quantity;
					// $product[$value->product_id]['quantity']  += $value->quantity;
					// $product[$value->product_id]['profit'] += $product[$value->product_id]['total'] - (get_purchase_pricing_on_date($value->product_id, $value->invoice->date)*$value->quantity);
					// $purchase_price = get_purchase_pricing_on_date($value->product_id, $value->invoice->date);
					$product[$value->product_id]['total_sale']  += $value->sale_price * $value->quantity;
					$product[$value->product_id]['sale_quantity']  += $value->quantity;
					$product[$value->product_id]['sale'] = $value->sale_price;
					$product[$value->product_id]['purchase'] = $value->purchase_price;
					$product[$value->product_id]['total_purchase'] += ($value->purchase_price * $value->quantity);
					$product[$value->product_id]['profit'] += $value->sale_price * $value->quantity - ($value->purchase_price * $value->quantity);
					$product[$value->product_id]['invoice_total'] = $value->invoice->total;
					$invoice_total[$value->invoice_id] = $value->invoice->total;
				}
				// foreach ($product as $key => $value) {
				// 	# code...
				// 	$product[$key]['purchase'] = estimated_purchased_price($key);
				// }
				// dd($product);
    			return view('reports.profit',compact('from','to','request','all','pin','product'));
			break;
			
			case 'profit_sale_order':
				// $validated = is_password_validated(); // password protected
				// if (!$validated) {
				// 	header('WWW-Authenticate: Basic realm="My Realm"');
				// 	header('HTTP/1.0 401 Unauthorized');
				// 	die ("Not authorized");
				// }// password protected */
				if (!$from) {
					$from = date('Y-01-01');
					$request->from = $from;
				}
				if (!$to) {
					$to = date('Y-m-d');
					$request->to = $to;
				}

				//identify completed sale orders based on status complete and when balance got zero.

				$valid_sale_orders_invoices = SaleOrder::where('posted',true)
				->where('status',SaleOrder::COMPLETED)
				->get()
				->pluck('invoice_id')
				->toArray();


				//identify using transactions
				$debit = Transaction::groupBy('invoice_id')->where('type','in')
				->selectRaw('sum(amount) as debit, invoice_id, max(date) as dt')->pluck('debit','invoice_id');

				// $debit_date = Transaction::groupBy('invoice_id')->where('type','in')
				// ->selectRaw('invoice_id, max(date) as dt')->pluck('dt','invoice_id');

				$date_invoice = SaleOrder::where('posted',true)
				->where('status',SaleOrder::COMPLETED)
				->whereBetween('completion_date',[$from, $to])
				->pluck('invoice_id')
				->toArray();
				// dd($date_invoice);
				
				$credit = Transaction::groupBy('invoice_id')->where('type','out')
				->selectRaw('sum(amount) as credit, invoice_id')->pluck('credit','invoice_id');

				$qualified_sale_orders=[];

				foreach($valid_sale_orders_invoices as $invoice_id)
				{
					$sum = $debit[$invoice_id] - $credit[$invoice_id];
					if ($sum == 0 && in_array($invoice_id, $date_invoice)) {
						$qualified_sale_orders[] = $invoice_id;
					}
					
				}

				$qualified_sale_orders = array_unique($qualified_sale_orders);

				$totalDiscount = Invoice::whereIn("id",$qualified_sale_orders)->sum('discount');
				


				$sale_ords = DB::select('SELECT product_id, 
				-- avg(`order`.salePrice) as sale_price, 
				SUM(`order`.salePrice * `order`.quantity) / SUM(`order`.quantity) as sale_price,
				sum(quantity) as qty, 
				SUM(`order`.salePrice*quantity) as total_sales, 
				products.name as product_name, 
				products.itemcode as product_itemcode, 
				products.brand as brand, 
				sum(discount) as discount FROM `order` 
				LEFT JOIN sale_orders on sale_orders.invoice_id = `order`.invoice_id 
				JOIN products on products.id = `order`.product_id 
				JOIN invoice on invoice.id = `sale_orders`.invoice_id 
				and `order`.deleted_at is NULL  
				and invoice.id IN ('.implode(',',$qualified_sale_orders).') 
				GROUP BY product_id;');

				$product = $discount = [];


				foreach ($sale_ords as $key => $value) {
					# code...
					if (!array_key_exists($value->product_id, $product))
					{
						$product[$value->product_id] = [
							'name' => "{$value->product_name} {$value->brand} {$value->product_itemcode}",'purchase'=>0,'sale'=>0,'sale_quantity'=>0,"purchase_quantity"=>0,"total_purchase"=>0,"total_sale"=>0, "profit"=>0];
					}
					$product[$value->product_id]['sale'] = $value->sale_price;
					$product[$value->product_id]['sale_quantity'] += $value->qty;
					$product[$value->product_id]['total_sale'] += $value->total_sales;
					$product[$value->product_id]['profit'] += $value->total_sales;
					// $totalDiscount += $value->discount;
				}

				$purchase_order = Order::join('invoice','invoice.id','=','order.invoice_id')
				->whereIn('invoice.type',['purchase'])
				->where('invoice.date','<=',$to)
				->orderBy('invoice.date','asc')
				->select('order.product_id','order.salePrice','order.quantity','invoice.date','invoice.id')->get();

				$purchase_dump = [];

				foreach($purchase_order as $order)
				{
					$key = $order->product_id;

					//only product that exists in sales
					if (array_key_exists($key, $product))
					{
						//if purchase dump not calculated then seed with zero
						if (!array_key_exists($key, $purchase_dump))
						{
							$purchase_dump[$key] = [
								'c_purchase_qty' => 0,
								'avg_purchase_price' => 0,
								'total_invested' => 0,
								'purchase_history'=>[]
							];
						}
						
						//sold item is less than the purchase item
						if ($purchase_dump[$key]['c_purchase_qty'] < $product[$key]['sale_quantity'])
						{
							//if sale is 20 items
							//recorded purchase is 10 items
							//order quantity is 20 item
							//difference = 20 - (10 + 20) = -10
							$difference = $product[$key]['sale_quantity'] - ($purchase_dump[$key]['c_purchase_qty'] + $order->quantity);
							// echo $difference."<br>";
							// 	echo $product[$key]['sale_quantity']."<br>";
							// 	echo $purchase_dump[$key]['c_purchase_qty']."<br>";
							// 	dd("x");
							//now add only the difference and based on purchase price and quantity
							//if difference is negative then add the difference
							if ($difference > 0)
							{
								$purchase_dump[$key]['total_invested'] += $order->salePrice * $order->quantity;
								$purchase_dump[$key]['c_purchase_qty'] += $order->quantity;
								// $purchase_dump[$key]['purchase_history'][] = ['quantity'=>$order->quantity,'price'=>$order->salePrice];
							}else{
								$small_diff = $product[$key]['sale_quantity'] - $purchase_dump[$key]['c_purchase_qty'];
								// $purchase_dump[$key]['purchase_history'][] = ['quantity'=>$small_diff,'price'=>$order->salePrice];
								$purchase_dump[$key]['total_invested'] += $order->salePrice * $small_diff;
								$purchase_dump[$key]['c_purchase_qty'] += $small_diff;
							}
						}
						//calculate the difference between the purchase price and the sale price based on item limitation
					}//endif
				}//endforeach
				// dd($purchase_dump[1398]);
				if(env('FURNITURE_GALLERY'))
				{
					$query = SupplierPriceRecord::where('date','<=',date('y-m-d'));
					$s_price_record = $query->orderBy('id','DESC')->get()->unique('product_id')->keyBy('product_id')->pluck('price','product_id')->toArray();
					$sum_purchase = $query->groupBy('product_id')->selectRaw('*, sum(price) as sum')->get()->keyBy('product_id')->pluck('sum','product_id')->toArray();
				}

				foreach($product as $key => $value)
				{
					if (array_key_exists($key, $purchase_dump))
					{		
						$product[$key]['purchase'] = $purchase_dump[$key]['total_invested'] / $purchase_dump[$key]['c_purchase_qty'];
						$product[$key]['total_purchase'] = $purchase_dump[$key]['total_invested'];
						$product[$key]['purchase_quantity'] = $purchase_dump[$key]['c_purchase_qty'];
						$product[$key]['profit'] -= $product[$key]['total_purchase'];
						// $product[$key]['purchase_history'] = $purchase_dump[$key]['purchase_history'];
					}else{
						// $product[$key]['purchase_history'] = [];
					}
					if(env('FURNITURE_GALLERY'))
						{
							if ($product[$key]['total_purchase'] <= 0)
								{
									$product[$key]['purchase'] = (isset($s_price_record[$key]))?$s_price_record[$key]:0;
									$product[$key]['total_purchase'] = (isset($sum_purchase[$key]))?$sum_purchase[$key]:0;
									$product[$key]['purchase_quantity'] = '-';
									$product[$key]['profit'] -= ($product[$key]['purchase']*$product[$key]['sale_quantity']);
								}
						}
				}			

				$is_all = true;
				return view('reports.completed_sale_order_profit',compact('from', 'to', 'request', 'all', 'pin', 'product', 'is_all', 'totalDiscount','qualified_sale_orders'));
			break;

			
					
			case 'business_capital':
				$validated = is_password_validated(); // password protected
				if (!$validated) {
					header('WWW-Authenticate: Basic realm="My Realm"');
					header('HTTP/1.0 401 Unauthorized');
					die ("Not authorized");
				}// password protected */
				if (!$from) {
					// $from = date('Y-01-01',strtotime("-1 year"));
					// $request->from = $from;
				}
				if (!$to) {
					$to = date('Y-m-d');
					$request->to = $to;
				}
				$totalDiscount = Invoice::whereIn("type",['sale','sale_order'])->where('date','<=',$to)->sum('discount');
				// $orders = DB::table('sale_order_view')->whereBetween("invoice_date", [$from, $to])
				// ->select("product_id", "product_name", "brand", "product_description", "product_itemcode", "invoice_id",
				// DB::raw("TRIM(quantity) + 0 AS quantity"), 
				// DB::raw("TRIM(purchase_price) + 0 AS purchase_price"), 
				// DB::raw("TRIM(sale_price) + 0 AS sale_price"), 
				// DB::raw("TRIM(invoice_discount) + 0 AS invoice_discount")
				// )->get();

				$purchase_orders = DB::select('SELECT product_id, SUM(`order`.salePrice * `order`.quantity) / SUM(`order`.quantity) as purchase_price, sum(quantity) as qty, SUM(`order`.salePrice*quantity) as total_purchase, products.name as product_name, products.itemcode as product_itemcode, products.brand as brand, sum(discount) as discount FROM `order` LEFT JOIN invoice on invoice.id = `order`.invoice_id JOIN products on products.id = `order`.product_id where invoice.type IN ("purchase") and invoice.deleted_at is NULL and `order`.deleted_at is NULL and invoice.date <= "'.$to.'" GROUP BY product_id;');


				$sale_orders = DB::select('SELECT product_id, SUM(`order`.salePrice * `order`.quantity) / SUM(`order`.quantity) as sale_price, sum(quantity) as qty, SUM(`order`.salePrice*quantity) as total_sales, products.name as product_name, products.itemcode as product_itemcode, products.brand as brand, sum(discount) as discount FROM `order` LEFT JOIN invoice on invoice.id = `order`.invoice_id JOIN products on products.id = `order`.product_id where invoice.type IN ("sale") and invoice.deleted_at is NULL and `order`.deleted_at is NULL and invoice.date <= "'.$to.'"  GROUP BY product_id;');


				$sale_ords = DB::select('SELECT product_id, SUM(`order`.salePrice * `order`.quantity) / SUM(`order`.quantity) as sale_price, sum(quantity) as qty, SUM(`order`.salePrice*quantity) as total_sales, products.name as product_name, products.itemcode as product_itemcode, products.brand as brand, sum(discount) as discount FROM `order` LEFT JOIN sale_orders on sale_orders.invoice_id = `order`.invoice_id JOIN products on products.id = `order`.product_id JOIN invoice on invoice.id = `sale_orders`.invoice_id where sale_orders.posted = 1 and sale_orders.deleted_at is NULL and `order`.deleted_at is NULL  and invoice.date <= "'.$to.'" GROUP BY product_id;');

				$product = $discount = [];

				foreach ($purchase_orders as $key => $value) {
					# code...
					if (!array_key_exists($value->product_id, $product))
					{
						$product[$value->product_id] = ['name' => "{$value->product_name} {$value->brand} {$value->product_itemcode}",'purchase'=>0,'sale'=>0,'sale_quantity'=>0,"purchase_quantity"=>0,"total_purchase"=>0,"total_sale"=>0, "profit"=>0];
					}
					$product[$value->product_id]['purchase'] = $value->purchase_price;
					$product[$value->product_id]['purchase_quantity'] = $value->qty;
					$product[$value->product_id]['total_purchase'] = $value->total_purchase;
					$product[$value->product_id]['profit'] -= $value->total_purchase;
					// $totalDiscount += $value->discount;
				}

				foreach ($sale_orders as $key => $value) {
					# code...
					if (!array_key_exists($value->product_id, $product))
					{
						$product[$value->product_id] = [
							'name' => "{$value->product_name} {$value->brand} {$value->product_itemcode}",'purchase'=>0,'sale'=>0,'sale_quantity'=>0,"purchase_quantity"=>0,"total_purchase"=>0,"total_sale"=>0, "profit"=>0];
					}
					$product[$value->product_id]['sale'] = $value->sale_price;
					$product[$value->product_id]['sale_quantity'] = $value->qty;
					$product[$value->product_id]['total_sale'] = $value->total_sales;
					$product[$value->product_id]['profit'] += $value->total_sales;
					// $totalDiscount += $value->discount;
				}


				foreach ($sale_ords as $key => $value) {
					# code...
					if (!array_key_exists($value->product_id, $product))
					{
						$product[$value->product_id] = [
							'name' => "{$value->product_name} {$value->brand} {$value->product_itemcode}",'purchase'=>0,'sale'=>0,'sale_quantity'=>0,"purchase_quantity"=>0,"total_purchase"=>0,"total_sale"=>0, "profit"=>0];
					}
					$product[$value->product_id]['sale'] = $value->sale_price;
					$product[$value->product_id]['sale_quantity'] += $value->qty;
					$product[$value->product_id]['total_sale'] += $value->total_sales;
					$product[$value->product_id]['profit'] += $value->total_sales;
					// $totalDiscount += $value->discount;
				}

				$is_all = true;
				return view('reports.calculation',compact('from', 'to', 'request', 'all', 'pin', 'product', 'is_all', 'totalDiscount'));
				break;

			case 'profit_all':
				$validated = is_password_validated(); // password protected
				if (!$validated) {
					header('WWW-Authenticate: Basic realm="My Realm"');
					header('HTTP/1.0 401 Unauthorized');
					die ("Not authorized");
				}// password protected */
				$from_query = "";
				if (!$from) {
					$request->from = $from;
				}
				if (!$to) {
					$to = date('Y-m-d');
					$request->to = $to;
				}
				if ($from)
				{
					$from_query = " and invoice.date >= '".date('Y-m-d', strtotime($from))."'";
				}
				$totalDiscount = Invoice::whereIn("type",['sale','sale_order']);
				if ($request->from)
				{
					$totalDiscount = $totalDiscount->where('date','>=',$from);
				}
				$totalDiscount = $totalDiscount->where('date','<=',$to)->sum('discount');
				// $orders = DB::table('sale_order_view')->whereBetween("invoice_date", [$from, $to])
				// ->select("product_id", "product_name", "brand", "product_description", "product_itemcode", "invoice_id",
				// DB::raw("TRIM(quantity) + 0 AS quantity"), 
				// DB::raw("TRIM(purchase_price) + 0 AS purchase_price"), 
				// DB::raw("TRIM(sale_price) + 0 AS sale_price"), 
				// DB::raw("TRIM(invoice_discount) + 0 AS invoice_discount")
				// )->get();

				// $purchase_orders = DB::select('SELECT product_id, avg(`order`.salePrice) as purchase_price, sum(quantity) as qty, SUM(`order`.salePrice*quantity) as total_purchase, products.name as product_name, products.itemcode as product_itemcode, products.brand as brand, sum(discount) as discount FROM `order` LEFT JOIN invoice on invoice.id = `order`.invoice_id JOIN products on products.id = `order`.product_id where invoice.type IN ("purchase") and invoice.deleted_at is NULL and `order`.deleted_at is NULL and invoice.date <= "'.$to.'" GROUP BY product_id;');
				$supplier_list = SupplierPriceRecord::select("product_id","price")->whereRaw('date = (select max(date) from supplier_price_records as t2 where t2.product_id=supplier_price_records.product_id  and t2.date <= "'.$to.'")')->groupBy("product_id")->pluck('price','product_id')->toArray();
				// $list = DB::select('select product_id, price', [1])
				// dd($list);

				$sale_orders = DB::select('SELECT 
				product_id, 
				-- avg(`order`.salePrice) as sale_price, 
				SUM(`order`.salePrice * `order`.quantity) / SUM(`order`.quantity) as sale_price,
				sum(quantity) as qty, 
				SUM(`order`.salePrice*quantity) as total_sales, 
				products.name as product_name, 
				products.itemcode as product_itemcode, 
				products.brand as brand, 
				products.size as size, 
				sum(discount) as discount 
				FROM `order` LEFT JOIN invoice on invoice.id = `order`.invoice_id 
				JOIN products on products.id = `order`.product_id 
				where invoice.type IN ("sale") 
				and invoice.deleted_at is NULL
				and `order`.quantity > 0
				and `order`.deleted_at is NULL 
				and invoice.date <= "'.$to.'" '.$from_query.'  
				GROUP BY product_id;'
			);


				$sale_ords = DB::select('SELECT 
				product_id, 
				-- avg(`order`.salePrice) as sale_price, 
				SUM(`order`.salePrice * `order`.quantity) / SUM(`order`.quantity) as sale_price,
				sum(quantity) as qty, 
				SUM(`order`.salePrice*quantity) as total_sales, 
				products.name as product_name, 
				products.itemcode as product_itemcode, 
				products.brand as brand, 
				products.size as size, 
				sum(discount) as discount 
				FROM `order` 
				LEFT JOIN sale_orders on sale_orders.invoice_id = `order`.invoice_id 
				JOIN products on products.id = `order`.product_id 
				JOIN invoice on invoice.id = `sale_orders`.invoice_id 
				where sale_orders.posted = 1 and sale_orders.deleted_at is NULL 
				and `order`.deleted_at is NULL 
				and `order`.quantity > 0
				 and invoice.date <= "'.$to.'"'.$from_query.' 
				 GROUP BY product_id;');

				$product = $discount = [];

				

				foreach ($sale_orders as $key => $value) {
					# code...
					if (!array_key_exists($value->product_id, $product))
					{
						$product[$value->product_id] = [
							'name' => "{$value->product_name} {$value->brand} {$value->product_itemcode}",'purchase'=>0,'sale'=>0,'sale_quantity'=>0,"purchase_quantity"=>0,"total_purchase"=>0,"total_sale"=>0, "profit"=>0, "size"=>$value->size];
					}
					$product[$value->product_id]['sale'] = $value->sale_price;
					$product[$value->product_id]['sale_quantity'] = $value->qty;
					$product[$value->product_id]['total_sale'] = $value->total_sales;
					$product[$value->product_id]['profit'] += $value->total_sales;
					// $totalDiscount += $value->discount;
				}


				foreach ($sale_ords as $key => $value) {
					# code...
					if (!array_key_exists($value->product_id, $product))
					{
						$product[$value->product_id] = [
							'name' => "{$value->product_name} {$value->brand} {$value->product_itemcode}",'purchase'=>0,'sale'=>0,'sale_quantity'=>0,"purchase_quantity"=>0,"total_purchase"=>0,"total_sale"=>0, "profit"=>0, "size"=>$value->size];
					}
					$product[$value->product_id]['sale'] = $value->sale_price;
					$product[$value->product_id]['sale_quantity'] += $value->qty;
					$product[$value->product_id]['total_sale'] += $value->total_sales;
					$product[$value->product_id]['profit'] += $value->total_sales;
					// $totalDiscount += $value->discount;
				}

				$purchase_order = Order::join('invoice','invoice.id','=','order.invoice_id')
				->whereIn('invoice.type',['purchase'])
				->where('invoice.date','<=',$to)
				->orderBy('invoice.date','asc')
				->select('order.product_id','order.salePrice','order.quantity','invoice.date','invoice.id')->get();


				$purchase_dump = [];

				foreach($purchase_order as $order)
				{
					$key = $order->product_id;

					//only product that exists in sales
					if (array_key_exists($key, $product))
					{
						//if purchase dump not calculated then seed with zero
						if (!array_key_exists($key, $purchase_dump))
						{
							$purchase_dump[$key] = [
								'c_purchase_qty' => 0,
								'avg_purchase_price' => 0,
								'total_invested' => 0,
								'purchase_history'=>[]
							];
						}
						
						//sold item is less than the purchase item
						if ($purchase_dump[$key]['c_purchase_qty'] < $product[$key]['sale_quantity'])
						{
							//if sale is 20 items
							//recorded purchase is 10 items
							//order quantity is 20 item
							//difference = 20 - (10 + 20) = -10
							$difference = $product[$key]['sale_quantity'] - ($purchase_dump[$key]['c_purchase_qty'] + $order->quantity);
							// echo $difference."<br>";
							// 	echo $product[$key]['sale_quantity']."<br>";
							// 	echo $purchase_dump[$key]['c_purchase_qty']."<br>";
							// 	dd("x");
							//now add only the difference and based on purchase price and quantity
							//if difference is negative then add the difference
							if ($difference > 0)
							{
								$purchase_dump[$key]['total_invested'] += $order->salePrice * $order->quantity;
								$purchase_dump[$key]['c_purchase_qty'] += $order->quantity;
								// $purchase_dump[$key]['purchase_history'][] = ['quantity'=>$order->quantity,'price'=>$order->salePrice];
							}else{
								$small_diff = $product[$key]['sale_quantity'] - $purchase_dump[$key]['c_purchase_qty'];
								// $purchase_dump[$key]['purchase_history'][] = ['quantity'=>$small_diff,'price'=>$order->salePrice];
								$purchase_dump[$key]['total_invested'] += $order->salePrice * $small_diff;
								$purchase_dump[$key]['c_purchase_qty'] += $small_diff;
							}
						}
						//calculate the difference between the purchase price and the sale price based on item limitation
					}//endif
				}//endforeach
			
				// dd($purchase_dump[20]);

				foreach($product as $key => $value)
				{
					if (array_key_exists($key, $purchase_dump))
					{
						$product[$key]['purchase'] = $purchase_dump[$key]['total_invested'] / $purchase_dump[$key]['c_purchase_qty'];
						$product[$key]['total_purchase'] = $purchase_dump[$key]['total_invested'];
						$product[$key]['purchase_quantity'] = $purchase_dump[$key]['c_purchase_qty'];
						$product[$key]['profit'] -= $product[$key]['total_purchase'];
						// $product[$key]['purchase_history'] = $purchase_dump[$key]['purchase_history'];
					}else if (array_key_exists($key, $supplier_list)){
						// $product[$key]['purchase_history'] = [];
						$product[$key]['purchase'] = $supplier_list[$key];
						$product[$key]['total_purchase'] = $supplier_list[$key] * $product[$key]['sale_quantity'];
						$product[$key]['purchase_quantity'] = $product[$key]['sale_quantity'];
						$product[$key]['profit'] -= $product[$key]['total_purchase'];
					}
				}
				// dd($product[20]);

				// //validate purchase if item have not enough purchase than sale
				foreach ( $product as $key => $value ) {

					if ($value['purchase_quantity'] < $value['sale_quantity'])
					{
						$diff = $value['sale_quantity'] - $value['purchase_quantity'];
						$product[$key]['purchase_quantity'] += $diff;
						$product[$key]['total_purchase'] += $diff*$value['purchase'];
						$product[$key]['profit'] =  $product[$key]['total_sale'] - $product[$key]['total_purchase'];	
					}

				}

				$is_all = true;
				return view('reports.profit',compact('from', 'to', 'request', 'all', 'pin', 'product', 'is_all', 'totalDiscount'));
			break;

			case 'bundle_wise_profit':
				// $validated = is_password_validated(); // password protected
				// if (!$validated) {
				// 	header('WWW-Authenticate: Basic realm="My Realm"');
				// 	header('HTTP/1.0 401 Unauthorized');
				// 	die ("Not authorized");
				// }// password protected */
				$from_query = "";
				if (!$from) {
					$request->from = $from;
				}
				if (!$to) {
					$to = date('Y-m-d');
					$request->to = $to;
				}
				if ($from)
				{
					$from_query = " and invoice.date >= '".date('Y-m-d', strtotime($from))."'";
				}
				$totalDiscount = Invoice::whereIn("type",['sale','sale_order']);
				if ($request->from)
				{
					$totalDiscount = $totalDiscount->where('date','>=',$from);
				}
				$totalDiscount = $totalDiscount->where('date','<=',$to)->sum('discount');
				
				$supplier_list = SupplierPriceRecord::select("product_id","price")->whereRaw('date = (select max(date) from supplier_price_records as t2 where t2.product_id=supplier_price_records.product_id  and t2.date <= "'.$to.'")')->groupBy("product_id")->pluck('price','product_id')->toArray();

				$sale_orders = DB::select('SELECT product_id, SUM(`order`.salePrice * `order`.quantity) / SUM(`order`.quantity) as sale_price, sum(quantity) as qty, SUM(`order`.salePrice*quantity) as total_sales, products.name as product_name, products.itemcode as product_itemcode, products.brand as brand, sum(discount) as discount FROM `order` LEFT JOIN invoice on invoice.id = `order`.invoice_id JOIN products on products.id = `order`.product_id where invoice.type IN ("sale") and invoice.deleted_at is NULL and `order`.deleted_at is NULL and invoice.date <= "'.$to.'" '.$from_query.'  GROUP BY product_id;');

				$sale_ords = DB::select('SELECT product_id, SUM(`order`.salePrice * `order`.quantity) / SUM(`order`.quantity) as sale_price, sum(quantity) as qty, SUM(`order`.salePrice*quantity) as total_sales, products.name as product_name, products.itemcode as product_itemcode, products.brand as brand, sum(discount) as discount FROM `order` LEFT JOIN sale_orders on sale_orders.invoice_id = `order`.invoice_id JOIN products on products.id = `order`.product_id JOIN invoice on invoice.id = `sale_orders`.invoice_id where sale_orders.posted = 1 and sale_orders.deleted_at is NULL and `order`.deleted_at is NULL  and invoice.date <= "'.$to.'"'.$from_query.' GROUP BY product_id;');

				$product = $discount = $productGroup = [];	

				foreach ($sale_orders as $key => $value) {
					# code...
					if (!array_key_exists($value->product_id, $product))
					{
						$product[$value->product_id] = [
							'id' => "{$value->product_id}" ,'name' => "{$value->product_name} {$value->brand} {$value->product_itemcode}",'purchase'=>0,'sale'=>0,'sale_quantity'=>0,"purchase_quantity"=>0,"total_purchase"=>0,"total_sale"=>0, "profit"=>0, "size"=>$value->size];
					}
					$product[$value->product_id]['sale'] = $value->sale_price;
					$product[$value->product_id]['sale_quantity'] = $value->qty;
					$product[$value->product_id]['total_sale'] = $value->total_sales;
					$product[$value->product_id]['profit'] += $value->total_sales;
					// $totalDiscount += $value->discount;
				}


				foreach ($sale_ords as $key => $value) {
					# code...
					if (!array_key_exists($value->product_id, $product))
					{
						$product[$value->product_id] = [
							'id' => "{$value->product_id}" ,'name' => "{$value->product_name} {$value->brand} {$value->product_itemcode}",'purchase'=>0,'sale'=>0,'sale_quantity'=>0,"purchase_quantity"=>0,"total_purchase"=>0,"total_sale"=>0, "profit"=>0, "size"=>$value->size];
					}
					$product[$value->product_id]['sale'] = $value->sale_price;
					$product[$value->product_id]['sale_quantity'] += $value->qty;
					$product[$value->product_id]['total_sale'] += $value->total_sales;
					$product[$value->product_id]['profit'] += $value->total_sales;
					// $totalDiscount += $value->discount;
				}

				$purchase_order = Order::join('invoice','invoice.id','=','order.invoice_id')
				->whereIn('invoice.type',['purchase'])
				->where('invoice.date','<=',$to)
				->orderBy('invoice.date','asc')
				->select('order.product_id','order.salePrice','order.quantity','invoice.date','invoice.id')->get();


				$purchase_dump = [];

				foreach($purchase_order as $order)
				{
					$key = $order->product_id;

					//only product that exists in sales
					if (array_key_exists($key, $product))
					{
						//if purchase dump not calculated then seed with zero
						if (!array_key_exists($key, $purchase_dump))
						{
							$purchase_dump[$key] = [
								'c_purchase_qty' => 0,
								'avg_purchase_price' => 0,
								'total_invested' => 0,
								'purchase_history'=>[]
							];
						}
						
						//sold item is less than the purchase item
						if ($purchase_dump[$key]['c_purchase_qty'] < $product[$key]['sale_quantity'])
						{
							//if sale is 20 items
							//recorded purchase is 10 items
							//order quantity is 20 item
							//difference = 20 - (10 + 20) = -10
							$difference = $product[$key]['sale_quantity'] - ($purchase_dump[$key]['c_purchase_qty'] + $order->quantity);
							// echo $difference."<br>";
							// 	echo $product[$key]['sale_quantity']."<br>";
							// 	echo $purchase_dump[$key]['c_purchase_qty']."<br>";
							// 	dd("x");
							//now add only the difference and based on purchase price and quantity
							//if difference is negative then add the difference
							if ($difference > 0)
							{
								$purchase_dump[$key]['total_invested'] += $order->salePrice * $order->quantity;
								$purchase_dump[$key]['c_purchase_qty'] += $order->quantity;
								// $purchase_dump[$key]['purchase_history'][] = ['quantity'=>$order->quantity,'price'=>$order->salePrice];
							}else{
								$small_diff = $product[$key]['sale_quantity'] - $purchase_dump[$key]['c_purchase_qty'];
								// $purchase_dump[$key]['purchase_history'][] = ['quantity'=>$small_diff,'price'=>$order->salePrice];
								$purchase_dump[$key]['total_invested'] += $order->salePrice * $small_diff;
								$purchase_dump[$key]['c_purchase_qty'] += $small_diff;
							}
						}
						//calculate the difference between the purchase price and the sale price based on item limitation
					}//endif
				}//endforeach

				$groups= ProductGroup::all();
				foreach($product as $key => $value)
				{
					if (array_key_exists($key, $purchase_dump))
					{
						$product[$key]['purchase'] = $purchase_dump[$key]['total_invested'] / $purchase_dump[$key]['c_purchase_qty'];
						$product[$key]['total_purchase'] = $purchase_dump[$key]['total_invested'];
						$product[$key]['purchase_quantity'] = $purchase_dump[$key]['c_purchase_qty'];
						$product[$key]['profit'] -= $product[$key]['total_purchase'];
						// $product[$key]['purchase_history'] = $purchase_dump[$key]['purchase_history'];
					}else if (array_key_exists($key, $supplier_list)){
						// $product[$key]['purchase_history'] = [];
						$product[$key]['purchase'] = $supplier_list[$key];
						$product[$key]['total_purchase'] = $supplier_list[$key] * $product[$key]['sale_quantity'];
						$product[$key]['purchase_quantity'] = $product[$key]['sale_quantity'];
						$product[$key]['profit'] -= $product[$key]['total_purchase'];
					}

					$exists_in_group = [];

					foreach($groups as $val)
					{
						if(in_array($product[$key]['id'], unserialize($val->products)))
						{
							$productGroup[$val->id]['id'] = $val->id;
							$productGroup[$val->id]['name'] = $val->name;
							$productGroup[$val->id]['details'][] = $product[$key];

							$exists_in_group[] = $product[$key]['id'];
						}
					}

					if(!in_array($product[$key]['id'], $exists_in_group))
					{
						$productGroup[0]['id'] = '';
						$productGroup[0]['name'] = "No Group";
						$productGroup[0]['details'][] = $product[$key];
					}
				}

				return view('reports.bundle_wise_profit',compact('from', 'to', 'request', 'all', 'pin', 'totalDiscount', 'productGroup'));
			break;

			case 'orderWise_profit':
				// $validated = is_password_validated(); // password protected
				// if (!$validated) {
				// 	header('WWW-Authenticate: Basic realm="My Realm"');
				// 	header('HTTP/1.0 401 Unauthorized');
				// 	die ("Not authorized");
				// }// password protected */

				if (!$from) {
					$request->from = date('Y-m-1');
				}
				else{
					$request->from = $from;
				}
				if (!$to) {
					$request->to = date('Y-m-d');
				}
				else{
					$request->to = $to;
				}
				
				$totalDiscount = Invoice::join('sale_orders','sale_orders.invoice_id','=','invoice.id')->whereIn("invoice.type",['sale','sale_order']);

				$query = DB::table('sale_order_view')->select('sale_order_view.*', 'sale_orders.id as sale_order_id', 'customer.name as customer_name')
													 ->join('sale_orders','sale_orders.invoice_id','=','sale_order_view.invoice_id')
													 ->join('customer','customer.id','=','sale_order_view.customer_id');
				if($request->completion_date){
					$query->whereBetween('sale_order_completion_date', [$request->from, $request->to]);
					$totalDiscount = $totalDiscount->whereBetween('sale_orders.completion_date', [$request->from, $request->to])->sum('discount');
				}
				else{
					$query->whereBetween('invoice_date', [$request->from, $request->to]);
					$totalDiscount = $totalDiscount->whereBetween('invoice.date', [$request->from, $request->to])->sum('discount');
				}
				
				$invoice = $query->whereNull('order_deleted_at')->get();
				
				$order_invoice=array();
				foreach($invoice as $key => $value){	
				$order_invoice[$value->invoice_id]['invoice_id']	 =$value->invoice_id;
				$order_invoice[$value->invoice_id]['sale_order_id']	 =$value->sale_order_id;
				$order_invoice[$value->invoice_id]['customer_id']	 =$value->customer_id;
				$order_invoice[$value->invoice_id]['customer_name']	 =$value->customer_name;
				$order_invoice[$value->invoice_id]['order_details'][]=array(
																		'order_id'		 => $value->order_id,
																		'product_id'	 => $value->product_id,
																		'product_name'	 => $value->product_name,
																		'brand'			 => $value->brand,
																		'purchase_price' => $value->purchase_price,
																		'sale_price'     => $value->sale_price,
																		'quantity'		 => $value->quantity,
																		'invoice_total'  => $value->invoice_total
																		);
				}
				// dd($order_invoice);
				
				foreach ($order_invoice as $sale_orders) {
					foreach ($sale_orders['order_details'] as $key => $value) {
						$sale_order[$sale_orders['invoice_id']]['sale_order_id']   = $sale_orders['sale_order_id'];
						$sale_order[$sale_orders['invoice_id']]['invoice_no'] 	   = $sale_orders['invoice_id'];
						$sale_order[$sale_orders['invoice_id']]['customer_name']   = $sale_orders['customer_name'];
						$sale_order[$sale_orders['invoice_id']]['total_sale']     += $value['sale_price'] * $value['quantity'];
						$sale_order[$sale_orders['invoice_id']]['sale_quantity']  += $value['quantity'];
						$sale_order[$sale_orders['invoice_id']]['product_id'][$key]  = $value['product_id'];
						$sale_order[$sale_orders['invoice_id']]['product_name'][$key]  = $value['product_name'];
						$sale_order[$sale_orders['invoice_id']]['sale'] 		   = $value['sale_price'];
						$sale_order[$sale_orders['invoice_id']]['purchase'] 	   = $value['purchase_price'];
						$sale_order[$sale_orders['invoice_id']]['total_purchase'] += ($value['purchase_price'] * $value['quantity']);
						$sale_order[$sale_orders['invoice_id']]['profit'] 		  += $value['sale_price'] * $value['quantity'] - ($value['purchase_price'] * $value['quantity']);
						$sale_order[$sale_orders['invoice_id']]['invoice_total']   = $value['invoice_total'];
					}
				}

				$is_all = true;

				return view('reports.orderwise_profit',compact('from', 'to', 'request', 'all', 'pin', 'sale_order', 'is_all', 'totalDiscount'));
			break;

			case 'tax':
				/* $validated = is_password_validated(); // password protected
				if (!$validated) {
					header('WWW-Authenticate: Basic realm="My Realm"');
					header('HTTP/1.0 401 Unauthorized');
					die ("Not authorized");
				}// password protected */
				if (!$from) {
					$from = date('Y-07-01',strtotime("-1 year"));
					$request->from = $from;
				}
				if (!$to) {
					$to = date('Y-06-30');
					$request->to = $to;
				}
				$total_sale_tax = Invoice::where("type",'sale')->selectRaw('invoice.id,invoice.bill_number, invoice.date, tax')->get();
				$total_sale_order = SaleOrder::where('posted',1)->join('invoice','sale_orders.invoice_id','=','invoice.id')->selectRaw('invoice.id,invoice.bill_number, invoice.date, tax')->get();
				$total_sale_tax = $total_sale_tax->merge($total_sale_order);
				
				return view('reports.tax',compact('from', 'to', 'request', 'total_sale_tax'));
			break;
			case 'shipping':
				/* $validated = is_password_validated(); // password protected
				if (!$validated) {
					header('WWW-Authenticate: Basic realm="My Realm"');
					header('HTTP/1.0 401 Unauthorized');
					die ("Not authorized");
				}// password protected */
				if (!$from) {
					$from = date('Y-01-01',strtotime("-1 year"));
					$request->from = $from;
				}
				if (!$to) {
					$to = date('Y-m-d');
					$request->to = $to;
				}
				$total_sale_tax = Invoice::where("type",'sale')->selectRaw('invoice.id,invoice.bill_number, invoice.date, shipping')->get();
				$total_sale_order = SaleOrder::where('posted',1)->join('invoice','sale_orders.invoice_id','=','invoice.id')->selectRaw('invoice.id,invoice.bill_number, invoice.date, shipping')->get();
				$total_sale_tax = $total_sale_tax->merge($total_sale_order);
				
				return view('reports.shipping',compact('from', 'to', 'request', 'total_sale_tax'));
			break;


			case 'saleorders_details':

				$from = ($from)?:date('Y-m-01');
				$to = ($to)?:date('Y-m-t');
				
				$sale_orders = DB::select("
				SELECT invoice_idd, GROUP_CONCAT(t.orderID) AS order_id,  GROUP_CONCAT(t.product_id) AS prod_id, GROUP_CONCAT(t.quantity) AS prod_quantity,GROUP_CONCAT(t.prodName SEPARATOR ', <br>') AS pName,t.* FROM (
				SELECT 
				  o.id AS orderID,
				  `p`.`id` AS `product_id`,
				  `p`.`unit_id` AS `unit`,
				  `i`.`id` AS `invoice_idd`,
				  `i`.`bill_number` AS `bill_number`,
				  CONCAT_WS(' ', p.name, p.brand, p.description, o.note ) AS prodName,
				  `o`.`salePrice` AS `s_price`,
				  `i`.`type` AS `invoiceType`,
				  `i`.`related_to` AS `invoiceRelatedTo`,
				  `i`.`total` AS `invoiceTotal`,
				  `i`.`date` AS `invoiceDate`,
				  `i`.`sales_person` AS `invoiceSales_person`,
				  `i`.`description` AS `notes`,
				  `i`.`customer_id` AS `customer_id`,
				  `so`.`posted`,
				  `i`.`total` AS `total`,
				  `customers`.`name` AS `cName`,
				  `customers`.`phone` AS `cPhone`,
				  `customers`.`address` AS `cAddress`,
				  `so`.`delivery_date` AS `delivery_date`,
				  `so`.`source` AS `source`,
				  `so`.`status` AS `status`,
				  `o`.`quantity` AS `quantity`,
				  (SELECT     SUM(amount)   FROM    `transaction`   WHERE invoice_id = invoice_idd     AND `type` = 'out'    AND deleted_at IS NULL) AS tOut,
				  (SELECT     SUM(amount)   FROM    `transaction`   WHERE invoice_id = invoice_idd     AND `type` = 'in'     AND deleted_at IS NULL) AS tIn 
				FROM  `order` AS `o` 
				  INNER JOIN `products` AS `p`     ON `p`.`id` = `o`.`product_id`     AND `p`.`deleted_at` IS NULL 
				  LEFT JOIN `sale_orders` AS `so`     ON `so`.`invoice_id` = `o`.`invoice_id` 
				  LEFT JOIN `invoice` AS `i`     ON `i`.`id` = `o`.`invoice_id`     AND `i`.`type` LIKE 'sale%' 
				  LEFT JOIN `customer` AS `customers`     ON `customers`.`id` = `i`.`customer_id` 
				WHERE `i`.`date` BETWEEN '{$from}'   AND '{$to}' 
				  AND `o`.`deleted_at` IS NULL 
				ORDER BY `i`.`id` DESC 
				) AS t GROUP BY invoice_idd
				");

				foreach ($sale_orders as $key => $value) {
					$value->balance = $value->tOut - $value->tIn;
					$invoice_data = Invoice::where('id',$value->invoice_idd)->first();
					$value->advance = $invoice_data->advance;
				}
				return view('reports.saleorder_details',compact('from','to','sale_orders'));
				break;

				case 'deleted_invioces':
					$from = ($from)?:date('Y-m-01');
					$to = ($to)?:date('Y-m-t');
					$sale_orders = Invoice::with('trashed_orders','trashed_sale_order','customer')
					->whereBetween('date',[$from,$to])
					->onlyTrashed()->get();
					return view('reports.deleted_invioces',compact('from','to','sale_orders'));
					break;

				case 'returnSale_invoice':

					$from = ($from)?:date('Y-m-01');
					$to = ($to)?:date('Y-m-t');
					
					$returnSale_invoice = DB::select("
					SELECT invoice_idd, GROUP_CONCAT(t.orderID) AS order_id,  GROUP_CONCAT(t.product_id) AS prod_id, GROUP_CONCAT(t.quantity) AS prod_quantity,GROUP_CONCAT(t.prodName SEPARATOR ', <br>') AS pName,t.* FROM (
					SELECT 
					  o.id AS orderID,
					  `p`.`id` AS `product_id`,
					  `p`.`unit_id` AS `unit`,
					  `i`.`id` AS `invoice_idd`,
					  `i`.`bill_number` AS `bill_number`,
					  CONCAT_WS(' ', p.name, p.brand, p.description, o.note ) AS prodName,
					  `o`.`salePrice` AS `s_price`,
					  `i`.`type` AS `invoiceType`,
					  `i`.`related_to` AS `invoiceRelatedTo`,
					  `i`.`total` AS `invoiceTotal`,
					  `i`.`date` AS `invoiceDate`,
					  `i`.`sales_person` AS `invoiceSales_person`,
					  `i`.`description` AS `notes`,
					  `i`.`customer_id` AS `customer_id`,
					  `so`.`posted`,
					  `i`.`total` AS `total`,
					  `customers`.`name` AS `cName`,
					  `customers`.`phone` AS `cPhone`,
					  `customers`.`address` AS `cAddress`,
					  `so`.`delivery_date` AS `delivery_date`,
					  `so`.`source` AS `source`,
					  `so`.`status` AS `status`,
					  `o`.`quantity` AS `quantity`,
					  (SELECT     SUM(amount)   FROM    `transaction`   WHERE invoice_id = invoice_idd     AND `type` = 'out'    AND deleted_at IS NULL) AS tOut,
					  (SELECT     SUM(amount)   FROM    `transaction`   WHERE invoice_id = invoice_idd     AND `type` = 'in'     AND deleted_at IS NULL) AS tIn 
					FROM  `order` AS `o` 
					  INNER JOIN `products` AS `p`     ON `p`.`id` = `o`.`product_id`     AND `p`.`deleted_at` IS NULL 
					  LEFT JOIN `sale_orders` AS `so`     ON `so`.`invoice_id` = `o`.`invoice_id` 
					  LEFT JOIN `invoice` AS `i`     ON `i`.`id` = `o`.`invoice_id`     AND `i`.`type` LIKE 'sale%' 
					  LEFT JOIN `customer` AS `customers`     ON `customers`.`id` = `i`.`customer_id` 
					WHERE `i`.`date` BETWEEN '{$from}'   AND '{$to}' AND `i`.`type` = 'sale' AND `i`.total < 0
					  AND `o`.`deleted_at` IS NULL 
					ORDER BY `i`.`id` DESC 
					) AS t GROUP BY invoice_idd
					");

	
					foreach ($returnSale_invoice as $key => $value) {
						
						$value->balance = $value->tOut - $value->tIn;
					}
					return view('reports.returnSale_invoice',compact('from','to','returnSale_invoice'));
					break;

			case 'completed_saleorders_details':
				$from = ($request->from)?:date('Y-m-01');
				$to = ($request->to)?:date('Y-m-t');
				
				$sale_orders = DB::select("
				SELECT invoice_idd, GROUP_CONCAT(t.orderID) AS order_id,  GROUP_CONCAT(t.product_id) AS prod_id, GROUP_CONCAT(t.quantity) AS prod_quantity,GROUP_CONCAT(t.prodName SEPARATOR ', <br>') AS pName,t.* FROM (
				SELECT 
				  o.id AS orderID,
				  `p`.`id` AS `product_id`,
				  `p`.`unit_id` AS `unit`,
				  `i`.`id` AS `invoice_idd`,
				  `i`.`bill_number` AS `bill_number`,
				  CONCAT_WS(' ', p.name, p.brand, p.description, o.note ) AS prodName,
				  `o`.`salePrice` AS `s_price`,
				  `i`.`type` AS `invoiceType`,
				  `i`.`related_to` AS `invoiceRelatedTo`,
				  `i`.`total` AS `invoiceTotal`,
				  `i`.`date` AS `invoiceDate`,
				  `i`.`sales_person` AS `invoiceSales_person`,
				  `i`.`description` AS `notes`,
				  `i`.`customer_id` AS `customer_id`,
				  `so`.`completion_date`,
				  `so`.`posted`,
				  `i`.`total` AS `total`,
				  `customers`.`name` AS `cName`,
				  `customers`.`phone` AS `cPhone`,
				  `customers`.`address` AS `cAddress`,
				  `so`.`delivery_date` AS `delivery_date`,
				  `so`.`source` AS `source`,
				  `so`.`status` AS `status`,
				  `o`.`quantity` AS `quantity`,
				  (SELECT     SUM(amount)   FROM    `transaction`   WHERE invoice_id = invoice_idd     AND `type` = 'out'    AND deleted_at IS NULL) AS tOut,
				  (SELECT     SUM(amount)   FROM    `transaction`   WHERE invoice_id = invoice_idd     AND `type` = 'in'     AND deleted_at IS NULL) AS tIn 
				FROM  `order` AS `o` 
				  INNER JOIN `products` AS `p`     ON `p`.`id` = `o`.`product_id`
				  LEFT JOIN `sale_orders` AS `so`     ON `so`.`invoice_id` = `o`.`invoice_id` 
				  LEFT JOIN `invoice` AS `i`     ON `i`.`id` = `o`.`invoice_id`     AND `i`.`type` LIKE 'sale%' 
				  LEFT JOIN `customer` AS `customers`     ON `customers`.`id` = `i`.`customer_id` 
					WHERE `so`.`completion_date` BETWEEN '{$from}' AND '{$to}' 
				  AND `o`.`deleted_at` IS NULL AND `so`.`status` = 4 
				ORDER BY `i`.`id` DESC 
				) AS t GROUP BY invoice_idd
				");

				foreach ($sale_orders as $key => $value) {
					$value->balance = $value->tOut - $value->tIn;
				}
				return view('reports.saleorder_completed',compact('from','to','sale_orders'));
				break;

			case 'worth':
				//password protected
				$validated = is_password_validated();
				if (!$validated) {
				  header('WWW-Authenticate: Basic realm="My Realm"');
				  header('HTTP/1.0 401 Unauthorized');
				  die ("Not authorized");
				}// password protected

				$all = Products::selectRaw("id, brand, size, barcode, name, salePrice, calculate_stock(products.id) as stock, last_purchase_price(products.id) as purchase_price")->get();
    			
    			return view('reports.worth',compact('from','to','request','all','pin'));
				break;

				// this is what a master purchase report will show\
			case "purchase_detailed":
				//password protected
				$validated = is_password_validated();
				if (!$validated) {
				  header('WWW-Authenticate: Basic realm="My Realm"');
				  header('HTTP/1.0 401 Unauthorized');
				  die ("Not authorized");
				}
				//password protected
				//products
				$product = Products::all();
				$output = [];
				if($request->product_id) {
					//stock balance by month
					$output['stk_in'] = Purchase::selectRaw('sum(stock) as sm,DATE_FORMAT(date, "%M %Y") as mdt, DATE_FORMAT(date,"%Y%m") as odr')
						->where('product_id', $request->product_id)
						->groupBy('mdt')
						// ->whereIn('type',['purchase', 'in'])
						->orderBy('odr')
						->get();
					$output['stk_in_invoice'] = Order::selectRaw('sum(quantity) as sm,DATE_FORMAT(date, "%M %Y") as mdt, DATE_FORMAT(date,"%Y%m") as odr')
					->join('invoice','invoice.id','=','order.invoice_id')
					->where('invoice.type','purchase')
					->where('product_id', $request->product_id)
					->groupBy('mdt')
					->orderBy('odr')
					->get();
					$amount_out_array = $po = [];

					foreach ($output['stk_in_invoice'] as $k2 => $v2) {
						$found = false;
						foreach ($output['stk_in'] as $key => $value) {
							if ($v2->mdt == $value->mdt) {
								$found = true;
								$output['stk_in'][$key]->sm += $v2->sm;
							}
						}
						if (!$found) {
							$output['stk_in'][]= $v2;	
						}
					}
					foreach ($output['stk_in'] as $key => $value) {
						$po[] = $value->mdt;
						$amount_out_array[] = (int)$value->sm;
					}

					$output['stk_in_chart'] = [
						'chart' => ['type' => 'line'],
						'title' => ['text' => 'Stock Purchased /Month'],
						'xAxis' => ['categories' => $po,],
						'yAxis' => ['title' => ['text' => 'Quantity']],
						"tooltip" => ["split"=> true],
						'series' => [
							['name' => 'Months', 'data' => $amount_out_array],
						]
					];
					// balance
					//average costing
					$output['costing'] = Purchase::selectRaw('avg(price) as sm, DATE_FORMAT(date, "%M %Y") as mdt, DATE_FORMAT(date,"%Y%m") as odr')
						->where('product_id', $request->product_id)
						->groupBy('mdt')
						->orderBy('odr')
						->get();
					$output['costing_invoice'] = Order::selectRaw('avg(salePrice) as sm,DATE_FORMAT(date, "%M %Y") as mdt, DATE_FORMAT(date,"%Y%m") as odr')
						->join('invoice','invoice.id','=','order.invoice_id')
						->where('product_id', $request->product_id)
						->where('invoice.type','purchase')
						->groupBy('mdt')
						->orderBy('odr')
						->get();
					$amount_out_array = $po = [];
					foreach ($output['costing_invoice'] as $k2 => $v2) {
						$found = false;
						foreach ($output['costing'] as $key => $value) {
							if ($v2->mdt == $value->mdt) {
								$found = true;
								$output['costing'][$key]->sm = ($v2->sm + $value->sm)/2;
							}
						}
						if (!$found) {
							$output['costing'][] = $v2;	
						}
					}
					foreach ($output['costing'] as $key => $value) {
						$po[] = $value->mdt;
						$amount_out_array[] = (int)$value->sm;
					}
					$output['costing_chart'] = [
						'chart' => ['type' => 'line'],
						'title' => ['text' => 'Average Cost per unit /Month'],
						'xAxis' => ['categories' => $po,],
						'yAxis' => ['title' => ['text' => 'Avg Cost']],
						"tooltip" => ["split"=> true],
						'series' => [
							['name' => 'Months','data' => $amount_out_array],
						]
					];
					// cost amount purchased
					$output['amount'] = Purchase::selectRaw('sum(price * stock) as sm, DATE_FORMAT(date, "%M %Y") as mdt, DATE_FORMAT(date,"%Y%m") as odr')
						->where('product_id', $request->product_id)
						->groupBy('mdt')
						->orderBy('odr')
						->get();
					$output['amount_invoice'] = Order::selectRaw('sum(salePrice * quantity) as sm,DATE_FORMAT(date, "%M %Y") as mdt, DATE_FORMAT(date,"%Y%m") as odr')
						->join('invoice','invoice.id','=','order.invoice_id')
						->where('product_id', $request->product_id)
						->where('invoice.type','purchase')
						->groupBy('mdt')
						->orderBy('odr')
						->get();
					foreach ($output['amount_invoice'] as $k2 => $v2) {
						$found = false;
						foreach ($output['amount'] as $key => $value) {
							if ($v2->mdt == $value->mdt) {
								$found = true;
								$output['amount'][$key]->sm += $v2->sm;
							}
						}
						if (!$found) {
							$output['amount'][] = $v2;	
						}
					}
					$amount_out_array = $po = [];
					foreach ($output['amount'] as $key => $value) {
						$po[] = $value->mdt;
						$amount_out_array[] = (int)$value->sm;
					}
					$output['amount_chart'] = [
						'chart' => ['type' => 'column'],
						'title' => ['text' => 'Money Invested in Purchase /Month'],
						'xAxis' => ['categories' => $po,],
						'yAxis' => ['title' => ['text' => 'Amount']],
						"tooltip" => ["split"=> true],						
						'series' => [
							['name' => 'Months', 'data' => $amount_out_array],
						]
					];
					// quantity purchased
				}
				return view('reports.master_purchase',compact('from','to','request','product','output'));
				break;
			case "aging":
				//password protected
				$validated = is_password_validated();

				// if (!$validated) {
				//   header('WWW-Authenticate: Basic realm="My Realm"');
				//   header('HTTP/1.0 401 Unauthorized');
				//   die ("Not authorized");
				// }
				//password protected
				$response = [];
				$transaction = Transaction::all();
				$now = Carbon::now();
				$paid = [];
				foreach ($transaction as $key => $value) {
					# code...
					if ($value->customer_id && $value->customer)
					{
						$transs = Carbon::parse($value->date);
						$amount = abs($value->amount);
						if ($value->type == "in")
						{
							if(!array_key_exists($value->customer_id, $paid))
							{
								$paid[$value->customer_id] = 0;
							}
							$paid[$value->customer_id] += $amount;
							continue;
						}
						
						$days_30 = $days_60 = $days_90 = $days_120 = 0;

						if ($transs->diffInDays($now) < 31)
						{
							$days_30 = $amount;
						}else if ($transs->diffInDays($now) < 61)
						{
							$days_60 = $amount;
						}else if ($transs->diffInDays($now) < 121){
							$days_90 = $amount;
						}else{
							$days_120 = $amount;
						}



						if (!array_key_exists($value->customer_id, $response))
						{
							$response[$value->customer_id] = [
								'name' => $value->customer->name,
								'30Days'=> $days_30,
								'60Days'=> $days_60,
								'60Days+'=> $days_90,
								'120Days+'=>$days_120,
								'total'	=> getCustomerBalance($value->customer_id)
						];
						}else{
							$response[$value->customer_id]['30Days'] += $days_30;
							$response[$value->customer_id]['60Days'] += $days_60;
							$response[$value->customer_id]['60Days+'] += $days_90;
							$response[$value->customer_id]['120Days+'] += $days_120;
						}		
					}

				}
				//now remove paid
				$periods = ['120Days+','60Days+','60Days','30Days'];
				foreach ($paid as $paidkey => $xpo) {
					foreach ($periods as $v2) {
						if ($xpo < 1) {
							continue;
						}
						if ($response[$paidkey][$v2] > 0) {
							if ($xpo >= $response[$paidkey][$v2]) {
								$xpo -= $response[$paidkey][$v2];
								$response[$paidkey][$v2] = 0;
							} else {
								$response[$paidkey][$v2] -= $xpo;
								$xpo = 0;
							}//endelse
						}//endif
					}//end period foreach
				}//end paid foreach
				return view('reports.aging',compact('response','paid'));
				break;
			case "delivery_wise_aging":
				//password protected
				$validated = is_password_validated();

				if (!$validated) {
				  header('WWW-Authenticate: Basic realm="My Realm"');
				  header('HTTP/1.0 401 Unauthorized');
				  die ("Not authorized");
				}
				//password protected
				$response = [];
				$transaction = Transaction::all();
				$now = Carbon::now();
				$paid = [];
				foreach ($transaction as $key => $value) {
					# code...
					if ($value->customer_id && $value->customer)
					{
						$sale_order = SaleOrder::where('invoice_id',$value->invoice_id)->first();
						if($sale_order->delivery_date)
						{
							$transs = Carbon::parse($sale_order->delivery_date);
							$amount = abs($value->amount);
							if ($value->type == "in")
							{
								if(!array_key_exists($value->customer_id, $paid))
								{
									$paid[$value->customer_id] = 0;
								}
								$paid[$value->customer_id] += $amount;
								continue;
							}
							
							$days_30 = $days_60 = $days_90 = $days_120 = 0;

							if ($transs->diffInDays($now) < 31)
							{
								$days_30 = $amount;
							}else if ($transs->diffInDays($now) < 61)
							{
								$days_60 = $amount;
							}else if ($transs->diffInDays($now) < 121){
								$days_90 = $amount;
							}else{
								$days_120 = $amount;
							}



							if (!array_key_exists($value->customer_id, $response))
							{
								$response[$value->customer_id] = [
									'name' => $value->customer->name,
									'30Days'=> $days_30,
									'60Days'=> $days_60,
									'60Days+'=> $days_90,
									'120Days+'=>$days_120,
									'total'	=> getCustomerBalance($value->customer_id)
							];
							}else{
								$response[$value->customer_id]['30Days'] += $days_30;
								$response[$value->customer_id]['60Days'] += $days_60;
								$response[$value->customer_id]['60Days+'] += $days_90;
								$response[$value->customer_id]['120Days+'] += $days_120;
							}
						}
								
					}

				}
				//now remove paid
				$periods = ['120Days+','60Days+','60Days','30Days'];
				foreach ($paid as $paidkey => $xpo) {
					foreach ($periods as $v2) {
						if ($xpo < 1) {
							continue;
						}
						if ($response[$paidkey][$v2] > 0) {
							if ($xpo >= $response[$paidkey][$v2]) {
								$xpo -= $response[$paidkey][$v2];
								$response[$paidkey][$v2] = 0;
							} else {
								$response[$paidkey][$v2] -= $xpo;
								$xpo = 0;
							}//endelse
						}//endif
					}//end period foreach
				}//end paid foreach
				return view('reports.delivery_wise_aging',compact('response','paid'));
				break;
			case 'product_record':
				//password protected
				$validated = is_password_validated();
				if (!$validated) {
				  header('WWW-Authenticate: Basic realm="My Realm"');
				  header('HTTP/1.0 401 Unauthorized');
				  die ("Not authorized");
				}
				//password protected
				$products = Products::all();
				$product = get_product($request->product_id);
				$sales = [];
				if ($product) {
					$invoices = Invoice::whereIn('type',['sale','sale_order'])->orderBy('date','desc')->pluck('id')->toArray();
					$orders = Order::where('product_id',$request->product_id)->get();
					foreach ($orders as $kao => $xsah) {
						if (!in_array($xsah->invoice_id, $invoices)) {
							unset($orders[$kao]);
						}
					}
					foreach ($orders as $key => $value) {
						if (array_key_exists($value->invoice->customer_id, $sales)) {
							if ($value->quantity < 0) {
								//if it's return then skip;
								continue;
							}
							if ($value->invoice->date < $sale[$value->invoice->customer_id]['date']) {
								//if this order is previous than already in loop order then skip this entry
								continue;
							}
						}
						$sales[$value->invoice->customer_id]['price'] = $value->salePrice;
						$sales[$value->invoice->customer_id]['qty'] = $value->quantity;
						$sales[$value->invoice->customer_id]['invoice'] = $value->invoice;
						$sale[$value->invoice->customer_id]['date'] = $value->invoice->date;
					}
				}
				return view('reports.product_record_customer',compact('product','products','request','sales'));
				break;
				//day sale start
			case 'day_sale':
				$from = ($from)?:date('Y-m-01');
				$to = ($to)?:date('Y-m-t');
				$total_sale_invoices = DB::table('invoice AS i')
					->leftJoin('sale_orders AS o', function($q) {
						$q->on('i.id', '=', 'o.invoice_id')->where('o.posted', '=', 1);
					})->join('customer AS c', 'c.id','=', 'i.customer_id')
					->join('users AS u', 'u.id', '=', 'i.added_by')
					->whereBetween('i.date', [$from, $to])->where('i.type', 'like', 'sale%')->whereNull('i.deleted_at')
					->select('i.id','i.bill_number', 'i.date', 'i.total', 'i.discount', 'i.description', 'c.name AS customer', 'u.name as user')->get();

				return view('reports.day_sale',compact('from','to','total_sale_invoices'));
				break;
				//day sale end
				//day revenue report start
			case 'revenue_report':
				$from = ($from)?:date('Y-m-01');
				$to = ($to)?:date('Y-m-t');
				$total_invoices = Transaction::where('date','>=',$from)->where('date','<=',$to)->whereIn('type',['in','out','expense'])->orderBy('date')->get();
				// $invoicesData = Invoice::where('date','>=',$from)->where('date','<=',$to)->where('type','!=','sale_order')->get();
				// $total_invoices = $transactionsData->merge($invoicesData);
				// $total_invoices = collect($total_invoices)->sortBy('date')->all();
				return view('reports.revenue_report',compact('from','to','total_invoices'));
				break;
				//day revenue report end
				//day sale start

			case 'cash_in_hand':
				$to = ($to)?:date('Y-m-d');
				$prev_date = date('Y-m-d', strtotime($to .' -1 day'));
				
				$previous_invoices = Transaction::select('transaction.*','invoice.type as order_type','invoice.total as order_total')->leftjoin('invoice','invoice.id','=','transaction.invoice_id')->where('transaction.date','<=',$prev_date)->whereIn('transaction.type',['in','out','expense'])->orderBy('transaction.date')->get();
				$today_invoices = Transaction::select('transaction.*','invoice.type as order_type','invoice.total as order_total')->leftjoin('invoice','invoice.id','=','transaction.invoice_id')->where('transaction.date','=',$to)->whereIn('transaction.type',['in','out','expense'])->orderBy('transaction.date')->get();

				$previous_admin_transactions = AdminTransaction::where('date','<=',$prev_date)->whereIn('type',['in','out'])->orderBy('date')->get();
				$today_admin_transactions = AdminTransaction::where('date','=',$to)->whereIn('type',['in','out'])->orderBy('date')->get();

				$admin_transactions = AdminTransaction::select('b.name AS bank_name', 'b.branch AS bank_branch', 'admin_transaction.*')->leftJoin('bank_accounts AS b', 'b.id', '=', 'admin_transaction.bank')->where('admin_transaction.date','<=',$to)->whereIn('admin_transaction.type',['in','out'])->orderBy('admin_transaction.date', 'DESC')->get();

				return view('reports.cash_in_hand',compact('to','previous_invoices','today_invoices', 'previous_admin_transactions', 'today_admin_transactions', 'admin_transactions'));
				break;

			case 'day_discount':
				$from = ($from)?:date('Y-m-01');
				$to = ($to)?:date('Y-m-d');
				$total_sale_invoices = Invoice::where('date','>=',$from)->where('date','<=',$to)->whereIn('type',['sale','sale_order'])->where('discount','>',0)->get();
				return view('reports.day_discount_cashier',compact('from','to','total_sale_invoices'));
				break;
				//day sale end
				//expense start 
			case 'delivery_report':
				/*
				Sale order delivery date report to check the sale orders
				according to their delivery status and Posted/Not-Posted
				*/
				$from = ($from)?:date('Y-m-01');
				$to = ($to)?:date('Y-m-d');
				$delivery_status = $request->delivery_status;

				//Array of Delivery Statuses
				$delivery_status_array = ['Select','Completed & Delivered','Delivered','Pending'];
				$sale_order_status = ['Pending','Active','','Quotation','Completed'];

				//Get all records between the provided date
				$delivery = SaleOrder::whereBetween('delivery_date',[$from, $to]);

				/*
				switch statement to filter data according to Delivery Status.
				1 Completed and delivered
				2 Delivered
				3 Pending
				*/
				switch($delivery_status){
					case(1):
						$saleOrders = $delivery->where('status', 4)
						->where('posted',1);
					break;
					case(2):
						$saleOrders = $delivery->where('status', 4);
					break;
					case(3):
						$saleOrders = $delivery->where('status', 0);
					break;
					default:
						$saleOrders = $delivery;
					break;
				}
				$saleOrders = $saleOrders->get();

				$total_saleOrders = count($saleOrders);

				$customers = Customer::pluck('name','id');
				$products = Products::pluck('name','id');

				return view('reports.delivery_report',
				compact('sale_order_status','products','customers','from','to','saleOrders',
				'total_saleOrders','delivery_status','delivery_status_array','total_saleOrders'));
				
				break;

				case 'salePerson_commission':
					/*
					Commision report of Sale Person to calculate commission
					*/
					$from = ($from)?:date('Y-m-01');
					$to = ($to)?:date('Y-m-d');
					$selected_person = $request->sale_person?:'';

					//Get all Sale Persons
					$sale_persons = SalesPerson::all();

					$sale_orders = null;
					if($selected_person)
					{
						//Get all Sale Orders against Sale Person
						$sale_orders = SaleOrder::with('invoice','saleOrder_person')->whereBetween('sale_orders.date',[$from, $to])->where('sale_orders.sales_people_id',$selected_person)->get();
					}

					return view('reports.salePerson_commission_report', compact('from','to','sale_persons','sale_orders','selected_person'));
					break;

			case 'expense':
				$exp = ExpenseHead::all();
				if($request->select_date == 1){
					$from = date('Y-m-d');
					$to = date('Y-m-d');
				} elseif ($request->select_date == 2) {
					$from = $request->start_of_week;
					$to = $request->end_of_week;
				} elseif($request->select_date == 3) {
					$from = $request->start_of_month;
					$to = $request->end_of_month;
				} else {
					$from = ($from)?:date('Y-m-01');
					$to = ($to)?:date('Y-m-d');
				}
				$query = Transaction::whereBetween('date', [$from, $to])->where('type','expense')->whereNull('customer_id');
				if($request->expense_head!= "select" && $request->expense_head!= "all") {
					$current_expense = ExpenseHead::where('id',$request->expense_head)->first();
					$query->where('expense_head',$request->expense_head);
				}
				$total_sale_invoices = $query->get();
				return view('reports.expense',compact('from','to','total_sale_invoices','exp','current_expense'));
				break;
				//expnese end
				//department start
			case 'department':
				$from = ($from)?:date('Y-m-01');
				$to = ($to)?:date('Y-m-d');
				$total_sale_invoices = Invoice::where('date','>=',$from)->where('date','<=',$to)->where('type','sale')->pluck('id')->toArray();
				$orders = Order::whereIn('invoice_id',$total_sale_invoices)->pluck('id')->toArray();
				$stocks = StockManage::whereIn('sale_id',$orders)->get();
				return view('reports.stocks',compact('from','to','stocks'));
				break;
				//department end
			case 'warehouse_transfer':
				$to = ($to) ?: date('Y-m-d');
				$from = ($from) ?: date('Y-m-01');
				$to_warehouse = ($request->to_warehouse) ?: "";
				$from_warehouse = ($request->from_warehouse) ?: "";
				$query =  StockManage::with('product')
					->whereBetween('date', [$from, $to])
					->whereIn('type', ['in', 'out'])
					->whereNull('customer_id')->whereNull('supplier_id')
					->whereNull('sale_id')->whereNull('purchase_id')
					->whereNull('refund_id')->whereNull('sale_orders_id')
					->whereNull('delivery_ChallanNo');
					// if(!empty($from_warehouse)) {
					// 	$query->where(function($q) use ($from_warehouse) {
					// 		$q->where(['type' => 'out', 'warehouse_id' => $from_warehouse]);
					// 	});
					// }
					// if(!empty($to_warehouse)) {
					// 	$query->where(function($q) use ($to_warehouse) {
					// 		$q->where(['type' => 'in', 'warehouse_id' => $to_warehouse]);
					// 	});
					// }
				$result = [];
				$is_image_enable = session()->get('settings.products.is_image_enable');
				$is_barcode_enable= session()->get('settings.barcode.is_enable');
				
				$names_fields = (session()->get('settings.products.optional_items'));
				$names_fields = empty($names_fields) ? [] : explode(",", session()->get('settings.products.optional_items'));
				$stockData = $query->get();
				foreach ($stockData as $record) {
					$index = $record->product_id."_".strtotime($record->date);
					if (!isset($result[$index])) {
						$result[$index] = [
							'product_id' => $record->product_id,
							'date' => $record->date,
							'name' => $record->product->name,
							'quantity' => $record->quantity + 0,
							'from' => $record->type == 'out' ? $record->warehouse_id : null,
							'to' => $record->type == 'in' ? $record->warehouse_id : null,
							'added_by' => $record->added_by,
						];
						if($is_barcode_enable) {
							$result[$index]['barcode'] = $record->product->barcode;
						}
						if($is_barcode_enable) {
							$result[$index]['image'] = $record->product->image_path;
						}
						foreach ($names_fields as $field) {
							$result[$index]['name'] .= $record->product->$field ?(($field == 'category')? " ".$record->product->$field->name : " ".$record->product->$field) : "";
						}
					} elseif($result[$index]['quantity'] == $record->quantity) {
						if ($record->type == 'in') {
							$result[$index]['to'] = $record->warehouse_id;
						} elseif ($record->type == 'out') {
							$result[$index]['from'] = $record->warehouse_id;
						}
					}
				}
				if ($from_warehouse != "") {
					foreach ($result as $index => $record) {
						if($record['from'] != $from_warehouse) {
							unset($result[$index]);
						}
					}
				}
				if ($to_warehouse != "") {
					foreach ($result as $index => $record) {
						if($record['to'] != $from_warehouse) {
							unset($result[$index]);
						}
					}
				}
				$warehouses = ["" => "Select Warehouse"] + Warehouse::withTrashed()->pluck('name', 'id')->toArray();
				$users = User::pluck('name', 'id')->toArray();

				return view('reports.transfer', compact('warehouses', 'users', 'result', 'from', 'to', 'from_warehouse', 'to_warehouse'));
				break;
			default:
				return redirect('reports');
				break;
		}
		return view('reports.show', compact('report'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$report = Report::findOrFail($id);
		return view('reports.edit', compact('report'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @param Request $request
	 * @return Response
	 */
	public function update(Request $request, $id)
	{
		$report = Report::findOrFail($id);
		$report->save();
		return redirect()->route('reports.index')->with('message', 'Item updated successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$report = Report::findOrFail($id);
		$report->delete();
		return redirect()->route('reports.index')->with('message', 'Item deleted successfully.');
	}

	public function productWarehouseStockLog($product_id, $warehouse_id)
	{
		$stocks = StockManage::where('product_id',$product_id)->where('warehouse_id',$warehouse_id)->paginate(20);
		return view('reports.productWarehouse',compact('stocks'));
	}
}
