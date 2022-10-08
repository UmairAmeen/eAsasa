<?php

namespace App\Http\Controllers;

use App\Rates;
use App\Customer;
use App\Transaction;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveRateRequest;
use App\Http\Requests\CreateCustomerRequest;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Storage;
use Excel;

class CustomerController extends Controller
{
    public function __construct()
    {
        View::share('title', "Customer");
        View::share('load_head', true);
        View::share('customer_menu', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $customers = Customer::orderBy('id', 'desc')->paginate(10);
        return view('customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (!is_allowed('customer-create')) {
            return redirect('/');
        }
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateCustomerRequest $request)
    {
        if (!is_allowed('customer-create')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        try {
            DB::beginTransaction();
            $customer = new Customer();
            $customer->name = $request->name;
            $customer->cnic = $request->cnic;
            $customer->ntn = $request->ntn;
            $customer->phone = $request->phone;
            $customer->type = $request->type;
            $customer->after_last_payment = $request->remainder_days;
            $customer->registeration_number = $request->registeration_number;
            $customer->added_by = \Auth::id();
            if ($request->payment_remainder) {
                $customer->payment_notify = true;
            } else {
                $customer->payment_notify = false;
            }

            $customer->last_contact_on = $request->last_contact_on;
            $customer->city = $request->city;
            $customer->address = $request->address;
            $customer->notes = $request->notes;
            $customer->save();

            if ($request->openingbalance) {
                $trans = new Transaction;
                $trans->date = Carbon::now();
                $trans->type="out";
                $trans->amount = $request->openingbalance;
                $trans->customer_id = $customer->id;
                $trans->save();
            }
            DB::commit();
            if ($request->modal_redirection) {
                return response(['message'=>'Customer Added successfully', 'action'=>'dismiss','do'=>'[name=customer]', 'val'=>$customer->id, 'text'=>$customer->name." ".$customer->city]);
            }
            return response()->json(['message' => 'Customer is successfully added','action'=>'redirect','do'=>url('/customers/create')], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(),'action'=>'redirect','do'=>url('/customers/create')], 500);
        }
        // return redirect()->route('customers.index')->with('message', 'Item created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if (!is_allowed('customer-list')) {
            return redirect('/');
        }
        $customer = Customer::findOrFail($id);
        $trans = Transaction::where('customer_id', $id)->orderBy('date', 'asc')->get();
        return view('customers.show', compact('customer', 'trans'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (!is_allowed('customer-edit')) {
            return redirect('/');
        }
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param Request $request
     * @return Response
     */
    public function update(CreateCustomerRequest $request, $id)
    {
        if (!is_allowed('customer-edit')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        $customer = Customer::findOrFail($id);
        $customer->name = $request->name;
        $customer->cnic = $request->cnic;
        $customer->ntn = $request->ntn;
        $customer->phone = $request->phone;
        $customer->type = $request->type;
        $customer->registeration_number = $request->registeration_number;
        $customer->edited_by = \Auth::id();
        $customer->notes = $request->notes;
        // $customer->phone_number = $request->phone_number;
        // $customer->address = $request->address;
        // $customer->discount = $request->discount;
        $customer->last_contact_on = $request->last_contact_on;
        $customer->after_last_payment = $request->remainder_days;
        if ($request->payment_remainder) {
            $customer->payment_notify = true;
        } else {
            $customer->payment_notify = false;
        }
        $customer->city = $request->city;
        $customer->address = $request->address;
        $customer->save();
        return response()->json(['message' => 'Customer is successfully updated','action'=>'redirect','do'=>url('/customers')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (!is_allowed('customer-delete')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'Unable to Remove: '.$e->getMessage()], 403);
        }
        return response()->json(['message' => 'Customer is removed','action'=>'redirect','do'=>url('/customers')], 200);
    }

    public function rates($id)
    {
        $customer = Customer::whereId($id)->first();
        if (!$customer) {
            return redirect()->route('customers.index')->with('message', 'Invalid Customer.');
        }
        return view('customers.rate', compact('customer'));
    }
    public function saverates(SaveRateRequest $srr, $id)
    {
        $customer = Customer::whereId($id)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer Doesn\'t Exists','action'=>'redirect','do'=>url('/customers')], 403);
        }
        foreach ($srr->product_id as $key => $value) {
            $rate = Rates::FirstOrNew(['customer_id'=>$id, 'product_id'=>$srr->product_id[$key]]);
            $rate->salePrice = $srr->sale_price[$key];
            $rate->save();
        } //save the rates by using loop
        return response()->json(['message' => 'Customer is successfully updated','action'=>'redirect','do'=>url('/getrates/'.$id)], 200);
    }
    public function destroyrate($id)
    {
        $customer = Rates::findOrFail($id);
        $customer->delete();
        return response()->json(['message' => 'Rate is removed','action'=>'redirect','do'=>url('/getrates/'.$customer->customer_id)], 200);
    }

    public function returnJsonCustomized(Request $req)
    {
        if (isset($req->q)) {
            $customer = Customer::Where('name', 'like', '%' . $req->q.'%')->orwhere('phone', 'like', '%' . $req->q.'%')->paginate(10);
        } else {
            $customer = Customer::orderBy('id', 'asc')->paginate(10);
        }
        $returnArray = [];
        foreach ($customer as $key => $value) {
            $returnArray['items'][] = ['id'=>$value->id, 'text'=>$value->name." | ".$value->phone];
        }
        $returnArray['page']=$customer->currentPage();
        $returnArray['total_count']=$customer->total();
        return $returnArray;
    }

    public function getCustomerBalance(Request $request)
    {
        return getCustomerBalance($request->id);
    }


    public function datatables()
    {
        return Datatables::of(Customer::query())
        ->edit_column('payment_notify', function ($row) {
            if ($row->payment_notify) {
                return "<b style='color:green'>ON</b>";
            }
            return "<i style='color:red'>OFF</i>";
        })->add_column('balance', function ($row) {
            if (!is_allowed('report-balance_sheet')) {
                return 0;
            }
            return amount_cdr(getCustomerBalance($row->id));
        })->edit_column('last_contact_on', function ($row) {
            return (strtotime($row->last_contact_on)) ? date('d-M-Y', strtotime($row->last_contact_on)) : "-";
        })->add_column('options', function ($row) {
            return '<a class="btn btn-xs btn-info" href="/getrates/'.$row->id.'"><i class="glyphicon glyphicon-barcode"></i> Manage Product Rates</a>
    			<a class="btn btn-xs btn-default btn-group" href="'.url("reports").'/balance_sheet?customer_id='.$row->id.'">Ledger</a>
				<a class="btn btn-xs btn-warning" href="'. route('customers.edit', $row->id) .'"><i class="glyphicon glyphicon-edit"></i> Edit</a>
				<form action="'. route('customers.destroy', $row->id) .'" method="POST" style="display: inline;" >
					<input type="hidden" name="_method" value="DELETE">
					<input type="hidden" name="_token" value="'.csrf_token() .'">
					<button type="submit" class="btn btn-xs btn-danger" onclick="return (confirm(\'Delete? Are you sure?\'))"><i class="glyphicon glyphicon-trash"></i> Delete</button>
				</form>';
        })->add_column('added_by', function ($row) {
            return ($row->added_user)?$row->added_user->name:"-";
        })->add_column('updated_by', function ($row) {
            return ($row->edited_user)?$row->edited_user->name:"-";
        })->make(true);
    }

    public function process_json()
    {
        \Debugbar::disable();
        $prod = Customer::all();
        $processor = [];
        foreach ($prod as $key => $value) {
            $processor[] = ["id"=>$value['id'], "text"=>$value['name']." | ".$value['city']];
        }
        return "var customer_json_d=".json_encode($processor);
    }


    public function downloadExcel()
    {
        if (!is_allowed('customer-import-export')) {
            return redirect('/');
        }
        @\Excel::create('Customers', function ($excel) {
            $excel->sheet('Customers', function ($sheet) {
                $sheet->fromArray(Customer::selectRaw("id, registeration_number, name, phone, city, address, type, notes")->orderBy('id', 'asc')->get()->toArray());
            });
        })->export('xlsx');
    }


    public function uploadExcel(Request $request)
    {
        if (!is_allowed('customer-import-export')) {
            return redirect('/');
        }
        $this->customer_count = 0;
        $p = Storage::put(
            'importcustomer.xlsx',
            file_get_contents($request->file('importexcel')->getRealPath())
        );
        DB::beginTransaction();
        try {
            $this::importExcel(storage_path("app/public/importcustomer.xlsx"));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect("customers")->with('error', 'Unable to Import: '.$e->getMessage());
        }
        // Cache::forget('products');
        return redirect('customers')->with('message', 'Succesfully '.$this->customer_count.' Imported');
    }

    private function importExcel($path)
    {
        @\Excel::load($path, function ($reader) {
            $reader->each(function ($sheet) {// Loop through all sheets
                if ($sheet->id) {
                    $pr = Customer::firstOrNew(['id'=>(int)$sheet->id]);
                } else {
                    $pr = new Customer;
                }
                if ($sheet->name) {
                    $pr->name = $sheet->name;
                    $pr->phone = $sheet->phone;
                    $pr->type = (!$sheet->type) ? "counter" : $sheet->type;
                    $pr->city = (!$sheet->city) ? null : $sheet->city;
                    $pr->address = (!$sheet->address) ? null : $sheet->address;
                    $pr->notes = $sheet->notes;
                    $pr->registeration_number = $sheet->registeration_number;
                    $pr->cnic = isset($sheet->cnic)?$sheet->cnic:null;
                    $pr->ntn = isset($sheet->ntn)?$sheet->ntn:null;
                    $pr->strn = isset($sheet->strn)?$sheet->strn:null;
                    if (!$pr->id) {
                        $pr->added_by = \Auth::id();
                    } else {
                        $pr->edited_by = \Auth::id();
                    }
                    $pr->save();
										if($sheet->opening_balance != 0){
											$transaction  = new Transaction;
											$transaction->date = date('Y-m-d');
											if($sheet->opening_balance > 0){$transaction->type = 'out';}
											elseif($sheet->opening_balance < 0){$transaction->type = 'in';}
											$transaction->added_by = \Auth::id();
											$transaction->bank = "-";
											$transaction->amount = (int)abs($sheet->opening_balance);			
											$transaction->release_date = date('Y-m-d');
											$transaction->payment_type = "cash";
											$transaction->customer_id = $pr->id;
											$transaction->description = "opening balance";
											$transaction->save();
										}
                    $this->customer_count++;
                }
            });
        })->get();
    }

    // url: /customer_modal
    public function modal(Request $request)
    {
    }
}
