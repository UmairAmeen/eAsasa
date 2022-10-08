<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateSupplierRequest;
use App\Http\Requests;
use App\Transaction;
use App\Supplier;
use Carbon\Carbon;
use Exception;
use Storage;
use Excel;
use DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SupplierController extends Controller
{
    public function __construct()
    {
        View::share('title', "Supplier");
        View::share('load_head', true);
        View::share('supplier_menu', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $suppliers = Supplier::orderBy('id', 'desc')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if (!is_allowed('supplier-create')) {
            return redirect('/');
        }
        return view('suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return Response
     */
    public function store(CreateSupplierRequest $request)
    {
        if (!is_allowed('supplier-create')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        $supplier = new Supplier();
        $supplier->name = $request->name;
        $supplier->phone = $request->phone_number;
        $supplier->address = $request->address;
        $supplier->type = $request->type;
        $supplier->company_name = $request->company_name;
        $supplier->description = $request->description;
        $supplier->save();
        if ($request->openingbalance) {
            $trans = new Transaction;
            $trans->date = Carbon::now();
            $trans->type="in";
            $trans->amount = $request->openingbalance;
            $trans->supplier_id = $supplier->id;
            $trans->save();
        }
        return response()->json(['message' => 'Supplier is successfully added','action'=>'redirect','do'=>url('/suppliers')], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if (!is_allowed('supplier-list')) {
            return redirect('/');
        }
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.show', compact('supplier'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if (!is_allowed('supplier-edit')) {
            return redirect('/');
        }
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param Request $request
     * @return Response
     */
    public function update(CreateSupplierRequest $request, $id)
    {
        if (!is_allowed('supplier-edit')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        $supplier = Supplier::findOrFail($id);
        $supplier->name = $request->name;
        $supplier->phone = $request->phone_number;
        $supplier->address = $request->address;
        $supplier->type = $request->type;
        $supplier->company_name = $request->company_name;
        $supplier->description = $request->description;
        $supplier->save();
        return response()->json(['message' => 'Supplier is successfully updated','action'=>'redirect','do'=>url('/suppliers')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (!is_allowed('supplier-delete')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        try {
            $supplier = Supplier::findOrFail($id);
            $supplier->delete();
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()], 401);
        }
        return response()->json(['message' => 'Supplier is successfully removed','action'=>'redirect','do'=>url('/suppliers')], 200);
    }


    public function getSupplierBalance(Request $request)
    {
        return getSupplierBalance($request->id);
    }


    public function returnJson(Request $req)
    {
        if (isset($req->q)) {
            $tables = Supplier::Where('name', 'like', '%' . $req->q.'%')->get();
            $supplier = $tables->merge(Supplier::Where('id', 'like', '%' . $req->q.'%')->get());
        } else {
            $supplier = Supplier::all();
        }
        $returnArray = [];
        foreach ($supplier as $key => $value) {
            $returnArray[] = ['id'=>$value->id, 'text'=>$value->id.": " . $value->name];
        }
        return $returnArray;
    }
    public function process_json()
    {
        \Debugbar::disable();
        $supplier = Supplier::all();
        $processor = [];
        foreach ($supplier as $key => $value) {
            $processor[] = ["id"=>$value['id'], "text"=>$value['name']];
        }
        return "var supplier_json_d=".json_encode($processor);
    }

    public function downloadExcel()
    {
        @\Excel::create('Suppliers', function ($excel) {
            $excel->sheet('Suppliers', function ($sheet) {
                $sheet->fromArray(Supplier::selectRaw("id, name, phone, address, type, company_name,description ")->orderBy('id', 'asc')->get()->toArray());
            });
        })->export('xlsx');
    }
    
    public function uploadExcel(Request $request)
    {
        $this->suppliers_count = 0;
        Storage::put('suppliers.xlsx', file_get_contents($request->file('importexcel')->getRealPath()));
        DB::beginTransaction();
        try {
            $this->importExcel(storage_path("app/public/suppliers.xlsx"));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('suppliers.index')->with('error', 'Unable to Import: '.$e->getMessage());
        }
        return redirect()->route('suppliers.index')->with('message', 'Succesfully '.$this->suppliers_count.' Imported');
    }
    
    private function importExcel($path)
    {
        @\Excel::load($path, function ($reader) {
            $reader->each(function ($sheet) {// Loop through all sheets
                if ($sheet->id) {
					$supplier = Supplier::firstOrNew(['id'=>$sheet->id]);
                } else {
					$supplier = new Supplier;
                }
                if ($sheet->name) {
                    $supplier->name = $sheet->name;
                    $supplier->phone = $sheet->phone;
                    $supplier->address = $sheet->address;
                    $supplier->type = $sheet->type;
                    $supplier->company_name = $sheet->company_name;
                    $supplier->description = $sheet->description ? : 'null';
                    $supplier->cnic = isset($sheet->cnic)?$sheet->cnic:null;
                    $supplier->ntn = isset($sheet->ntn)?$sheet->ntn:null;
                    $supplier->strn = isset($sheet->strn)?$sheet->strn:null;
                    $supplier->registeration_number = isset($sheet->registeration_number)?$sheet->registeration_number:null;
                    $supplier->save();

                    if($sheet->opening_balance != 0){
                        $transaction  = new Transaction;
                        $transaction->date = date('Y-m-d');
                        if($sheet->opening_balance > 0){$transaction->type = 'in';}
                        elseif($sheet->opening_balance < 0){$transaction->type = 'out';}
                        $transaction->added_by = \Auth::id();
                        $transaction->bank = "-";
                        $transaction->amount = (int)abs($sheet->opening_balance);			
                        $transaction->release_date = date('Y-m-d');
                        $transaction->payment_type = "cash";
                        $transaction->supplier_id = $supplier->id;
                        $transaction->description = "opening balance";
                        $transaction->save();
                    }
                    $this->suppliers_count++;
                }
            });
        })->get();
    }
}
