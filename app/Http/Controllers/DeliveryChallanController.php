<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SMController;
use App\Http\Requests\StockAdjustmentRequest;
use App\DeliveryChallan;
use App\SaleOrder;
use App\Customer;
use App\StockManage;
use App\Order;
use App\Products;
use App\Warehouse;
use Illuminate\Http\Request;
use View;
use App\MyPDF;
// use Illuminate\Support\Facades\DB;

use DB;
use Exception;

class DeliveryChallanController extends Controller {

	public function __construct()
	{
		\View::share('title',"Delivery Challans");
		View::share('load_head',true);
		View::share('deliverychallan_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (!is_allowed('product-list')) {
			return redirect('/');
		}
		$deliverychallans = DeliveryChallan::with('delivery_invoice')->leftJoin('customer', 'customer.id', '=', 'delivery_challans.customer_id')
		   ->select('customer.name as name', 'customer.phone as phone', 'delivery_challans.id as d_id', 'delivery_challans.*')
           ->get();
		return view('deliverychallans.index', compact('deliverychallans'));

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        $warehouses = Warehouse::pluck('name', 'id')->toArray();
		$customers = Customer::select('id', 'name', 'phone', 'city', 'address')->get()->toArray();
		$sale_orders = SaleOrder::where('status', '!=', SaleOrder::COMPLETED)->select('id', 'invoice_id', 'customer_id')->get()->toArray();
		return view('deliverychallans.create',compact('warehouses','customers', 'sale_orders'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
        DB::beginTransaction();
        $sale_orders_id = SaleOrder::where('invoice_id', $request->get('order_no'))->first()->id;

        //nested associated array to filter Deliverable products from the request->details;
        $only_delivery_products = [
            'order_id' => [],'product_id' => [],'quantity' => [],'status' =>[],'remarks' => []
        ];

        //only select deliverable products from delivery challan creation
        foreach($request->details['product_id'] as $key => $value){
            if($request->details['status'][$key] != 'Pending'){
                foreach(array_keys($only_delivery_products) as $k_d)
                {
                    $only_delivery_products[$k_d][]=$request->details[$k_d][$key];
                }
            }
		}
        try {
            $details = $this->GetEncodedArray($request->details);
            foreach ($details as $ky => $qty) {
               if($qty['quantity'] > $request->remaining_quantity[$ky]) {
                    throw new Exception("Quantity in Sale Order to be delivered is less than you entered at line# ".($ky+1)." It should be ".$request->remaining_quantity[$ky]." or less than ".$request->remaining_quantity[$ky].".");
                }
                if ($qty['quantity'] < 0) {
                    throw new Exception("Stock can't be below zero at line# ".($ky+1));
                }
                if ($qty['quantity'] > $request->complete_quantity[$ky]) {
                    throw new Exception("Quantity in Sale order is less than in line# ".($ky+1));
                }                    
                if ($qty['quantity'] && !$qty['status']) {
                    throw new Exception("Please set status of quantity line# ".($ky+1));
                }
                if (
                    $qty['status'] != "Pending" && 
                    !session()->get('settings.products.ignore_stock_verification')  &&
                    warehouse_stock(false,$request->warehouse_id,$qty['product_id']) < $qty['quantity']
                ) {
                    throw new Exception("Stock Not Available at line# ".($ky+1));
                }
            }    
            $should_proceed = true;
            if(!empty($only_delivery_products['product_id']))
            {
                //instantiation of Stocklog object
                $new_Request = new StockAdjustmentRequest($only_delivery_products);
                $next_deliveryChallan =  DeliveryChallan::withTrashed()->max('id') + 1;
                // $next_deliveryChallan = $deliverychallanId->delivery_ChallanNo + 1;
                $new_Request->merge([
                    'is_purchase' => 0,
                    'date' => $request->date,
                    'customer_id' => $request->customer_id,
                    'warehouse_id' => $request->warehouse_id,
                    'sale_orders_id' => $sale_orders_id,
                    'next_deliveryChallan' => $next_deliveryChallan,
                ]);
                $should_proceed = app('App\Http\Controllers\StockAdjustmentController')->store($new_Request,true);
                // $should_proceed = ($response->getData()->message == 'Stock is successfully updated');
            }
            $response = null;
            if($should_proceed) {
                $deliverychallans =  new DeliveryChallan();
                $deliverychallans->date = date("Y-m-d",strtotime($request->date));
                $deliverychallans->customer_id = $request->customer_id;
                $deliverychallans->warehouse_id = $request->warehouse_id;
                $deliverychallans->rep_by = $request->rep_by;
                $deliverychallans->order_no = $request->order_no;
                $deliverychallans->address = $request->address;
                $deliverychallans->o_details = $details;
                $deliverychallans->save();
                DB::commit();

                $products=$quantites=$status=[];
                foreach($deliverychallans->o_details as $detail)
                {
                   $products[] = $detail['product_id'];
                   $quantites[] = $detail['quantity'];
                   $status[] = $detail['status'];
                }
                $product_names = Products::whereIn('id',$products)->pluck('name')->toArray();
                
                //Branded SMS functionality started
                
                if(getSetting('sms_enable') == '1')
                {
                    if(getSetting('sms_url') != "" && getSetting('sms_user_name') != "" && getSetting('sms_mask') != "" && getSetting('sms_password') != ""){
                        $message = "Dear customer your order # ".$deliverychallans->order_no;
                        $result = (new SMController)->send_sms($deliverychallans->delivery_invoice->customer->phone,$message);
                        $response = ($result == "Sent Successfully")?"SMS Sent" : $result;
                    }
                    else{
                        $response = "SMS Not Sent due to invalid Credentials";
                    }
                }

                //Branded SMS fucntionality ended
            }
            return response()->json(['message' => 'Delivery Challan is successfully added','sms_message' => $response,'action'=>'redirect','do'=>url('/deliverychallans')], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

	public function edit($id) {
		$challan = DeliveryChallan::findOrFail($id);
        $warehouses = Warehouse::pluck('name', 'id')->toArray();
		$customer = Customer::where('id', $challan->customer_id)->select('id', 'name', 'phone', 'city', 'address')->first();
		// $order = SaleOrder::where('invoice_id',$challan->order_no)->get();
		return view('deliverychallans.edit', compact('warehouses','challan', 'customer'));
	}

    public function show($id, $is_purchase = 0)
    {
        $challan = DeliveryChallan::findOrFail($id);
        $customer = Customer::find($challan->customer_id);
        $background_image_select = 'delivery_note.jpg';
        $background = session()->get('settings.misc.custom_header_footer') ? false : storage_path('app/public/'.$background_image_select.'');
        $pdf = new MyPDF($title ='Delivery Challan',$background);
        $pdf->addPage();
		$pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
        $pdf->SetY(intval(session()->get('settings.misc.content_position') * 2));
        $invoice_top = view('deliverychallans.print.default_invoice_top', compact('challan', 'customer'));
        $pdf->writeHTML($invoice_top);

        $order_details = collect($challan->o_details)->pluck('remarks','order_id')->toArray();
        $image_enable = session()->get('settings.sales.is_image_enable_on_delivery_challan');
        if($image_enable == '1')
        {
            $items_per_invoice = 7;
        }
        else{
            $items_per_invoice = 20;
        }
        $paginated_items = array_chunk($order_details, $items_per_invoice, true);
        
        foreach ($paginated_items as $page => $order_details) {
            $serial_no = $page * $items_per_invoice;
            $orders = Order::with('product')->whereIn('id', array_keys($order_details))->get();

            $contents = view('deliverychallans.print.default_invoice', compact('challan','image_enable', 'order_details', 'orders', 'serial_no'));

            if ($page) {
                $pdf->SetY(intval(session()->get('settings.misc.content_position') * 3));
            }
            $pdf->writeHTML($contents);
            if (count($paginated_items) > 1 && $page < count($paginated_items) -1) {
                $pdf->AddPage();
                $pdf->SetY(36);
            }
        }
        $pdf->Output("delivery_note_{$challan->id}.pdf");
    }

    public function update(Request $request, $id) {
        $challan = DeliveryChallan::findOrFail($id);
        try {
            DB::beginTransaction();
            $challan->date = date('Y-m-d', strtotime($request->date));
            $challan->rep_by = $request->rep_by;
            $challan->address = $request->address;
            $details = $this->GetEncodedArray($request->details);
            foreach ($details as $key => $order) {
                Order::where('id', $order['order_id'])->update(['delivery_status' => $order['status']]);
            }
            $challan->o_details = $details;
            $challan->save();
            DB::commit();
            return redirect()->route('deliverychallans.index')->with('message', 'Challan Updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 403);
        }


    }




    public function customer_orders_dropdown(Request $request)
    {
        $customer_id = $request->customer_id;
        return response()->json(['status' =>'success' , 'data' => SaleOrder::where('customer_id', '=', $customer_id)->select('invoice_id', 'id')->get()]);
    }

    public function customer_orders_dropdown_detail(Request $request)
    {
        $sale_order_id = SaleOrder::where('invoice_id', $request->invoices)->first()->id;
        $orders = Order::leftJoin('products', 'products.id', '=', 'order.product_id')
            ->select('products.name as name', 'products.brand as brand',
            'products.color as color','products.pattern as pattern',
            'products.size as size','products.description as description'
            ,'products.*', 'order.id as orderid', 'order.*')
			->where('order.invoice_id', $request->invoices)
            ->get();
        $is_quantity_fixed = $request->get('is_quantity_fixed');
        return view('deliverychallans.details', compact('sale_order_id','orders', 'is_quantity_fixed'));
    }

	private function GetEncodedArray($data)
    {
        $result = [];
        reset($data);
        $key = key($data);
        for ($i = 0; $i < sizeof($data[$key]); $i++) {
            foreach (array_keys($data) as $key) {
                $result[$i][$key] = $data[$key][$i];
            }
        }
        return $result;
    }

    public function destroy($id){
        $dc = DeliveryChallan::find($id);
        $prod = StockManage::where('delivery_ChallanNo',$id)->delete();
        $dc->delete();
        return redirect()->back();
    }
}
