<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Order;
use App\Invoice;
use App\Products;
use App\StockManage;

use Carbon\Carbon;

class AIReportingController extends Controller
{
    //
    public function forcasting(Request $request)
    {
		$validated = is_password_validated();
		if (!$validated) {
			header('WWW-Authenticate: Basic realm="My Realm"');
			header('HTTP/1.0 401 Unauthorized');
			die ("Not authorized");
		}
		  //password protected
		  
    	$month_now = $request->month_now;
    	if (!$request->month_now)
    	{
    		$month_now = date('m');	
    	}
    	$year = date('Y') - 1;
    	//products
    	// $invoices = Invoice::whereRaw('MONTH(date) = '.$month_now)->whereRaw('YEAR(date) = '.$year)->whereIn('type',['sale','sale_order'])->pluck('id')->toArray();
    	$orders = StockManage::selectRaw('product_id, avg(quantity) as qty, sum(quantity) as sm')->whereRaw('MONTH(date) = '.$month_now)->whereRaw('YEAR(date) = '.$year)->whereIn('type',['out','sale'])->groupBy('product_id')->get()->toArray();

    	// dd($orders);
		// Current Price
		// Future Price Promotions
		// Weather
		// Public Holidays
		// Events
		// Google Analytics
		// Web crawlers are replacing syndicated data sources
    	$end = new Carbon('last day of this month');

		$invoices_now = Invoice::where("date",">=",date('Y-m-1'))->where('date',"<=",$end->format('Y-m-d'))->whereIn('type',['sale','sale_order'])->pluck('id')->toArray();

    	$orders_now = Order::selectRaw('product_id, sum(quantity) as sm')->whereIn('invoice_id', $invoices_now)->groupBy('product_id')->get()->toArray();


		//what result to expect
		/*
			List of Products with forcasting sale this month vs total sold this month
		*/
		return view('reports.ai.forcasting',compact('orders','orders_now'));

    }
}
