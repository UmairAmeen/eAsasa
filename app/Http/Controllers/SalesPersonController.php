<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Facades\Datatables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Storage;
use Excel;

use App\Http\Requests;
use App\SalesPerson;

class SalesPersonController extends Controller
{
    public function __construct()
    {
        View::share('title', "Sales Person");
        View::share('load_head', true);
        View::share('salesPerson_menu', true);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $salesPerson = SalesPerson::orderBy('id', 'desc')->paginate(10);
        return view('sales_person.index', compact('salesPerson'));
    }

    public function datatables()
    {
        return Datatables::of(SalesPerson::query())
        ->add_column('options', function ($row) {
            return '<a class="btn btn-xs btn-warning" href="'. route('salesPerson.edit', $row->id) .'"><i class="glyphicon glyphicon-edit"></i> Edit</a>
				<form action="'. route('salesPerson.destroy', $row->id) .'" method="POST" style="display: inline;" >
					<input type="hidden" name="_method" value="DELETE">
					<input type="hidden" name="_token" value="'.csrf_token() .'">
					<button type="submit" class="btn btn-xs btn-danger" onclick="return (confirm(\'Delete? Are you sure?\'))"><i class="glyphicon glyphicon-trash"></i> Delete</button>
				</form>';
        })->make(true);
    }

    public function process_json()
    {
        \Debugbar::disable();
        $prod = SalesPerson::all();
        $processor = [];
        foreach ($prod as $key => $value) {
            $processor[] = ["id"=>$value['id'], "text"=>$value['name']." | ".$value['city']];
        }
        return "var salesPerson_json_d=".json_encode($processor);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!is_allowed('salesPerson-create')) {
            return redirect('/');
        }
        return view('sales_person.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!is_allowed('salesPerson-create')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        try {
            DB::beginTransaction();
            $salesPerson = new SalesPerson();
            $salesPerson->name = $request->name;
            $salesPerson->phone = $request->phone;
            $salesPerson->address = $request->address;
            $salesPerson->commission = $request->commission;
            $salesPerson->save();
            DB::commit();
            if ($request->modal_redirection) {
                return response(['message'=>'Sales Person Added successfully', 'action'=>'dismiss','do'=>'[name=salesPerson]', 'val'=>$salesPerson->id, 'text'=>$salesPerson->name]);
            }
            return response()->json(['message' => 'Sales Person is successfully added','action'=>'redirect','do'=>url('/salesPerson')], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage(),'action'=>'redirect','do'=>url('/salesPerson/create')], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!is_allowed('salesPerson-list')) {
            return redirect('/');
        }
        $salesPerson = SalesPerson::findOrFail($id);
        return view('sales_person.show', compact('salesPerson'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!is_allowed('salesPerson-edit')) {
            return redirect('/');
        }
        $salesPerson = SalesPerson::findOrFail($id);
        // dd($salesPerson);
        // dd($salesPerson->salePerson_order);
        return view('sales_person.edit', compact('salesPerson'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!is_allowed('salesPerson-edit')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        $salesPerson = SalesPerson::findOrFail($id);
        $salesPerson->name = $request->name;
        $salesPerson->phone = $request->phone;
        $salesPerson->address = $request->address;
        $salesPerson->commission = $request->commission;
        $salesPerson->save();
        return response()->json(['message' => 'Sales Person is successfully updated','action'=>'redirect','do'=>url('/salesPerson')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!is_allowed('salesPerson-delete')) {
            return response()->json(['message' => "Unauthorized"], 403);
        }
        try {
            $customer = SalesPerson::findOrFail($id);
            $customer->delete();
        } catch (Exception $e) {
            return response()->json(['message' => 'Unable to Remove: '.$e->getMessage()], 403);
        }
        return response()->json(['message' => 'Sales Person is removed','action'=>'redirect','do'=>url('/salesPerson')], 200);
    }

    public function downloadExcel()
    {
        if (!is_allowed('salesPerson-import-export')) {
            return redirect('/');
        }
        @\Excel::create('Sales Persons', function ($excel) {
            $excel->sheet('Sales Persons', function ($sheet) {
                $sheet->fromArray(SalesPerson::selectRaw("id, name, phone, address")->orderBy('id', 'asc')->get()->toArray());
            });
        })->export('xlsx');
    }


    public function uploadExcel(Request $request)
    {
        if (!is_allowed('SalesPerson-import-import')) {
            return redirect('/');
        }
        $this->salesPerson_count = 0;
        $p = Storage::put(
            'importSalesPerson.xlsx',
            file_get_contents($request->file('importexcel')->getRealPath())
        );
        DB::beginTransaction();
        try {
            $this->importExcel(storage_path("app/public/importSalesPerson.xlsx"));
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return redirect("salesPerson")->with('error', 'Unable to Import: '.$e->getMessage());
        }
        return redirect('salesPerson')->with('message', 'Succesfully '.$this->salesPerson_count.' Imported');
    }

    private function importExcel($path)
    {
        @\Excel::load($path, function ($reader) {
            $reader->each(function ($sheet) {// Loop through all sheets
                if ($sheet->id) {
                    //id, name, phone, address
                    $pr = SalesPerson::firstOrNew(['id'=>$sheet->id]);
                } else {
                    $pr = new SalesPerson;
                }
                if ($sheet->name) {
                    $pr->name = $sheet->name;
                    $pr->phone = $sheet->phone;
                    $pr->address = (!$sheet->address) ? null : $sheet->address;
                    $pr->save();
                    $this->salesPerson_count++;
                }
            });
        })->get();
    }
}
