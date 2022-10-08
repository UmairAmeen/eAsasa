<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\AdminTransaction;
use App\Transaction;
use App\MyPDF;
use Illuminate\Http\Request;
use App\Http\Requests\CreateAdminTransactionRequest;
use App\User;
use Yajra\Datatables\Facades\Datatables;
use DB;
use View;
use Cache;

class AdminTransactionController extends Controller
{
   public function __construct()
    {
        \View::share('title', "Admin Transaction");
        View::share('load_head', true);
        View::share('admin_transaction_menu', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        if (!is_allowed('admin_transaction-list')) {
            return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }

        $date_format = GetDateFormatForJS(session()->get('settings.misc.date_format'));
        return view('admin_transactions.index', compact('date_format'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (!is_allowed('admin_transaction-create')) {
            return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }
        return view('admin_transactions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateAdminTransactionRequest $request)
    {
        if (!is_allowed('admin_transaction-create')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }

        DB::beginTransaction();
        foreach ($request->amount as $key => $value) {
            try {
                $type = $request->type;

                $transaction = new AdminTransaction();
                $transaction->date = date('Y-m-d', strtotime($request->input('date')));
                $transaction->type = $request->input('type');

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

                if ($request->description[$key]) {
                    $transaction->description = $request->description[$key];
                }

                $transaction->save();

            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json(['message' => $e->getMessage()], 403);
            }
        }

        DB::commit();
        return response()->json(['message' => 'Transaction created successfully.','action'=>'update','do'=>'.admin_transaction_listing'], 200);
        // return redirect()->route('transactions.index')->with('message', 'Transaction created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (!is_allowed('admin_transaction-edit')) {
            return redirect('/');
            // return response(['message'=>'Unauthorised'],500);
        }
        $transaction = AdminTransaction::findOrFail($id);

        return view('admin_transactions.edit', compact('transaction'));
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
        if (!is_allowed('admin_transaction-edit')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        try {
            DB::beginTransaction();
            $transaction = AdminTransaction::whereId($id)->first();
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
        return response()->json(['message' => 'Transaction updated successfully.','action'=>'redirect','do'=>url('/admin_transactions')], 200);

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
        if (!is_allowed('admin_transaction-delete')) {
            // return redirect('/');
            return response(['message'=>'Unauthorised'], 500);
        }
        $transaction = AdminTransaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully.','action'=>'redirect','do'=>url('/admin_transactions')], 200);



        // return redirect()->route('transactions.index')->with('message', 'Transaction deleted successfully.');
    }

    public function datatables(Request $request)
    {
        $from = $request->searchFrom;
        $to = $request->searchTo;
        $users = User::pluck('name', 'id')->toArray();
        $selected = ['b.name AS bank_name', 'b.branch AS bank_branch', DB::raw('t.*')
        ];
        $query = DB::table('admin_transaction AS t')
                ->leftJoin('bank_accounts AS b', 'b.id', '=', 't.bank')
                ->select($selected)->whereNull('t.deleted_at');
        if ($from && $to) {
            $query->whereBetween('t.date', [$from, $to]);
        }
        $data = $query->get();
        return Datatables::of(collect($data))
        ->edit_column('type', function ($row) {
            if ($row->type == "in") {
                return "<span class='label label-success'>debit</span>";
            }
            if ($row->type == "out") {
                return "<span class='label label-danger'>credit</span>";
            }
        })->edit_column('amount', function ($row) {
            return formating_price($row->amount);
        })->edit_column('payment_type', function ($row) {
            return $row->payment_type;
        })
        ->edit_column('date', function ($row) {
            return (strtotime($row->date)) ? date_format_app($row->date) : "-";
        })->edit_column('release_date', function ($row) {
            return (strtotime($row->release_date) > 0) ? date_format_app($row->release_date) : "-";
        })->edit_column('bank', function ($row) {
            return !empty($row->bank_name) ? $row->bank_name . " - " . $row->bank_branch : "";
        })->add_column('options', function ($row) {
            return '<a class="btn btn-xs btn-warning" href="'.route('admin_transactions.edit', $row->id) .'"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                    <form action="'. route('admin_transactions.destroy', $row->id).'" method="POST" style="display: inline;" >
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
}
