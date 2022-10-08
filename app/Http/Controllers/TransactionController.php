<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Supplier;
use App\Customer;
use App\MyPDF;
use App\ExpenseHead;
use Illuminate\Http\Request;
use App\Http\Requests\CreateTransactionRequest;
use App\User;
use Yajra\Datatables\Facades\Datatables;
use DB;
use View;
use Cache;

class TransactionController extends Controller
{
    public function __construct()
    {
        \View::share('title', "Transaction");
        View::share('load_head', true);
        View::share('transaction_menu', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (!$request->customer && !$request->supplier) {
            session(['trans_where'=>false]);
        }

        if ($request->customer) {
            session(['trans_where'=>'customer']);
        }

        if ($request->supplier) {
            session(['trans_where'=>'supplier']);
        }


        if (!is_allowed('transaction-list')) {
            return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }

        $date_format = GetDateFormatForJS(session()->get('settings.misc.date_format'));
        return view('transactions.index', compact('date_format'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (!is_allowed('transaction-create')) {
            return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }
        return view('transactions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateTransactionRequest $request)
    {
        if (!is_allowed('transaction-create')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }

        if($request->transaction_type == 'expense')
        {
            $this->validate($request, [ 
                'expense_head' => 'required', 
            ]); 
        }
        else{
            $this->validate($request, [ 
                'customer' => 'required_without_all:supplier', 
            ], 
            [  
                'customer.required_without_all' => 'At least one field is required from customer and supplier',
            ]);
        }
        
        DB::beginTransaction();
        foreach ($request->amount as $key => $value) {
            try {
                $type = $request->type;

                $transaction = new Transaction();
                $transaction->date = date('Y-m-d', strtotime($request->input('date')));
                $transaction->type = $request->input('type');

                #make entry when user pay the amount
                // if ($request->type == "in" && $request->customer[$key])
                // {
                // 	$cust = Customer::whereId($request->customer[$key])->first();
                // 	if($cust)
                // 	{
                // 		if(!$cust->last_contact_on)
                // 		{
                // 			$cust->last_contact_on = $transaction->date;
                // 		}else
                // 		if (strtotime($cust->last_contact_on) < strtotime($transaction->date))
                // 		{
                // 			$cust->last_contact_on = $transaction->date;
                // 		}
                // 		$cust->save();
                // 	}

                // }
                $transaction->added_by = \Auth::id();
                $transaction->bank = $request->bank[$key] ?$request->bank[$key]:"-";
                $transaction->amount = $request->amount[$key];

                if ($request->release_date[$key]) {
                    $transaction->release_date = $request->release_date[$key];
                }

                $transaction->payment_type = $request->payment_type[$key];
                if ($request->payment_type[$key] == 1) {
                    $transaction->payment_type  = "cash";
                }
                
                if (isset($request->transacion_id[$key])) {
                    $transaction->transaction_id = $request->transacion_id[$key];
                }

                $transaction->invoice_id = $request->invoice_id;
                if (gettype($request->customer) == 'array' && isset($request->customer[$key])) {
                    $transaction->customer_id = $request->customer[$key];
                } else {
                    $transaction->customer_id = $request->customer;
                }

                if (isset($request->supplier[$key])) {
                    $transaction->supplier_id = $request->supplier[$key];
                    $transaction->customer_id=null;
                }

                if ($request->expense_head[$key]) {
                    $transaction->expense_head = $request->expense_head[$key];
                    $transaction->type = 'expense';
                }

                if ($request->description[$key]) {
                    $transaction->description = $request->description[$key];
                }

                $transaction->save();

//
                if ($type == "in" && $transaction->customer_id) {
                    $customer = Customer::whereId($transaction->customer_id)->first();
                    // $customer->last_contact_on = $transaction->date;
                    if (!$customer->last_contact_on) {
                        $customer->last_contact_on = $transaction->date;
                    } elseif (strtotime($customer->last_contact_on) < strtotime($transaction->date)) {
                        $customer->last_contact_on = $transaction->date;
                    }
                    $customer->save();
                }
            } catch (\Exception $e) {
                DB::rollBack();
                
                return response()->json(['message' => $e->getMessage()], 403);
            }
        }

        DB::commit();
        return response()->json(['message' => 'Transaction created successfully.','action'=>'update','do'=>'.transaction_listing'], 200);
        // return redirect()->route('transactions.index')->with('message', 'Transaction created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if (!is_allowed('transaction-list')) {
            return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }
        View::share('load_head', false);
        View::share('transaction_menu', false);
        $transaction = Transaction::findOrFail($id);
        // dd($transaction->invoice->sale_order);
        // return view('transactions.show', compact('transaction'));
        $background = storage_path('app/public/transaction_bg.jpg');
        $pdf = new MyPDF($title= "Receipt",$background);
        $pdf->addPage();
        $pdf->SetFont(PDF_FONT_NAME_MAIN, '', 10);
        $pdf->SetY(intval(session()->get('settings.misc.content_position') * 3));
        $contents = view('transactions.show', compact('transaction'))->render();
        $pdf->writeHTML($contents);
        $pdf->Output('Receipt_'.$id.'.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (!is_allowed('transaction-edit')) {
            return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }
        $transaction = Transaction::findOrFail($id);

        return view('transactions.edit', compact('transaction'));
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
        if (!is_allowed('transaction-edit')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        try {
            DB::beginTransaction();
            $transaction = Transaction::whereId($id)->first();
            $transaction->date = date('Y-m-d', strtotime($request->input('date')));
            $transaction->bank = $request->bank;
            $transaction->amount = $request->amount;
            $transaction->release_date = date('Y-m-d', strtotime($request->release_date));
            $transaction->payment_type = $request->payment_type;
            $transaction->transaction_id = $request->transaction_id;
            $transaction->description = $request->description;
            $transaction->edited_by = \Auth::id();
            $transaction->save();
        } catch (\Exception $e) {
            DB::rollBack();
                
            return response()->json(['message' => $e->getMessage()], 403);
        }

        DB::commit();
        return response()->json(['message' => 'Transaction updated successfully.','action'=>'redirect','do'=>url('/transactions')], 200);

        // return redirect()->route('transactions.index')->with('message', 'Item updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        $custom = $request->custom_redirect;
        if (!is_allowed('transaction-delete')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        if ($custom) {
            return response()->json(['message' => 'Transaction deleted successfully.','action'=>'redirect','do'=>url('/sale_orders/'.$custom)], 200);
        } else {
            return response()->json(['message' => 'Transaction deleted successfully.','action'=>'redirect','do'=>url('/transactions')], 200);
        }


        // return redirect()->route('transactions.index')->with('message', 'Transaction deleted successfully.');
    }

    public function datatables(Request $request)
    {
        $from = $request->searchFrom;
        $to = $request->searchTo;
        $users = User::pluck('name', 'id')->toArray();
        $selected = ['c.name AS customer_name', 'c.city AS customer_city',
            's.name AS supplier_name', 's.address AS supplier_address',
            'i.sales_person', 'i.bill_number','i.total', 'e.name AS expense_name',
            'so.source AS order_source', 'so.status AS order_status',
            'b.name AS bank_name', 'b.branch', DB::raw('t.*')
        ];
        $query = DB::table('transaction AS t')
              ->leftJoin('customer AS c', 'c.id', '=', 't.customer_id')
              ->leftJoin('supplier AS s', 's.id', '=', 't.supplier_id')
              ->leftJoin('expense_heads AS e', 'e.id', '=', 't.expense_head')
              ->leftJoin('invoice AS i', 'i.id', '=', 't.invoice_id')
              ->leftJoin('sale_orders AS so', 'so.invoice_id', '=', 't.invoice_id')
              ->leftJoin('bank_accounts AS b', 'b.id', '=', 't.bank')
              ->select($selected)->whereNull('t.deleted_at');
        if ($from && $to) {
            $query->whereBetween('t.date', [$from, $to]);
        }
        $data = $query->get();
        return Datatables::of(collect($data))
        ->edit_column('customer.name', function ($row) {
            if ($row->customer_id) {
                return "<a href='/customers/{$row->customer_id}' target='_blank'>{$row->customer_name}<br><small>{$row->customer_city}</small></a>";
            }
            return "-";
        })->edit_column('supplier.name', function ($row) {
            if ($row->supplier_id) {
                return "<a href='/suppliers/{$row->supplier_id}' target='_blank'>{$row->supplier_name}<br><small>{$row->supplier_address}</small></a>";
            }
            return "-";
        })->edit_column('type', function ($row) {
            if ($row->type == "in") {
                return "<span class='label label-success'>debit</span>";
            }
            if ($row->type == "out" && empty($row->expense_head)) {
                return "<span class='label label-danger'>credit</span>";
            }
            if (!empty($row->expense_head)) {
                return "<span class='label label-warning'>Expense</span>";
            }
        })->edit_column('amount', function ($row) {
            return formating_price($row->amount);
        })->add_column('total', function ($row) {
            return ($row->invoice->total)?$row->invoice->total:"-";
        })->edit_column('expense_head', function ($row) {
            return !empty($row->expense_name) ? $row->expense_name : "-";
        })->edit_column('payment_type', function ($row) {
            if ($row->type == "out") {
                return "-";
            }
            return $row->payment_type;
        })->add_column('sales_person', function ($row) {
            return ($row->sales_person) ? $row->sales_person : "-";
        })
        ->add_column('source', function ($row) {
            return ($row->order_source) ? $row->order_source : "-";
        })
        ->add_column('status', function ($row) {
            $status_array = array("PENDING", "ACTIVE", "null", "QUOTATION", "COMPLETED");
            $status = $status_array[$row->order_status];
            return ($status) ? $status : "-";
        })
        ->edit_column('date', function ($row) {
            return (strtotime($row->date)) ? date_format_app($row->date) : "-";
        })->edit_column('release_date', function ($row) {
            return (strtotime($row->release_date) > 0) ? date_format_app($row->release_date) : "-";
        })->edit_column('bank', function ($row) {
            return !empty($row->bank_name) ? $row->bank_name . " - " . $row->bank_branch : "";
        })->edit_column("invoice_id", function ($row) {
            if ($row->invoice_id) {
                $b_n = ($row->bill_number) ? " ({$row->bill_number})" : "";
                return "<a href='".url('invoices')."/".$row->invoice_id."' target='_blank'>".$row->invoice_id.$b_n."</a>";
            }
            return "-";
        })->edit_column("total_worth", function ($row) {
            if ($row->invoice_id) {
                return number_format($row->total,2);
            }
            return "-";
        })->add_column('options', function ($row) {
            return '<a class="btn btn-xs btn-primary" target="_blank" href="'.route('transactions.show', $row->id).'"><i class="glyphicon glyphicon-eye-open"></i> View Receipt</a>
					<a class="btn btn-xs btn-warning" href="'.route('transactions.edit', $row->id) .'"><i class="glyphicon glyphicon-edit"></i> Edit</a>
					<form action="'. route('transactions.destroy', $row->id).'" method="POST" style="display: inline;" >
						<input type="hidden" name="_method" value="DELETE">
						<input type="hidden" name="_token" value="'.csrf_token() .'">
						<button type="submit" class="btn btn-xs btn-danger" onclick="return confirm(\'Delete? Are you sure?\');"><i class="glyphicon glyphicon-trash"></i> Delete</button>
					</form>';
        })->add_column('added_by', function ($row) use ($users) {
            return ($row->added_by)? $users[$row->added_by] : "-";
        })->add_column('updated_by', function ($row) use ($users) {
            return ($row->edited_by) ? $users[$row->edited_by] : "-";
        })->make(true);
    }

    public function getCustomerBalance(Request $request)
    {
        if (!is_allowed('report-balance_sheet')) {
            return 0;
            // return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }
        return getCustomerBalance($request->customer_id);
    }

    public function getSupplierBalance(Request $request)
    {
        if (!is_allowed('report-balance_sheet')) {
            return 0;
            // return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }
        return getSupplierBalance($request->id??$request->customer_id);
    }
}
