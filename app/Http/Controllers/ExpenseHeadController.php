<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ExpenseHead;
use Illuminate\Http\Request;
use View;
use Exception;
use Storage;
use Excel;
use DB;

class ExpenseHeadController extends Controller {


	public function __construct()
	{
		\View::share('title',"Expense Head");
		View::share('load_head',true);
		View::share('expensehead_menu',true);
		// $this->supplier = new SupplierPurchaseController();
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (!is_allowed('product-list'))
		{
			// return 0;
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$expenses = ExpenseHead::orderBy('id', 'desc')->get();

		return view('expensehead.index', compact('expenses'));
	}

	
	public function downloadExcel()
	{
			@\Excel::create('Expense Heads', function ($excel) {
					$excel->sheet('Expense Heads', function ($sheet) {
							$sheet->fromArray(ExpenseHead::selectRaw("id, name")->orderBy('id', 'asc')->get()->toArray());
					});
			})->export('xlsx');
	}

	public function uploadExcel(Request $request)
	{
			$this->expenseHead_count = 0;
			$p = Storage::put(
					'expense.xlsx',
					file_get_contents($request->file('importexcel')->getRealPath())
			);
			DB::beginTransaction();
			try {
					$this->importExcel(storage_path("app/public/expense.xlsx"));
					DB::commit();
			} catch (Exception $e) {
					DB::rollBack();
					return redirect()->route('expensehead.index')->with('error', 'Unable to Import: '.$e->getMessage());
			}
			return redirect()->route('expensehead.index')->with('message', 'Succesfully '.$this->expenseHead_count.' Imported');
	}

	private function importExcel($path)
	{
			@\Excel::load($path, function ($reader) {
					$reader->each(function ($sheet) {// Loop through all sheets
							if ($sheet->id) {
									$pr = ExpenseHead::firstOrNew(['id'=>$sheet->id]);
							} else {
									$pr = new ExpenseHead;
							}
							if ($sheet->name) {
									$pr->name = $sheet->name;
									$pr->save();
									$this->expenseHead_count++;
							}
					});
			})->get();
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('expensehead.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		if (!is_allowed('product-create'))
		{
			// return 0;
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$expensehead = ExpenseHead::firstOrNew(['name'=>$request->name]);
		$expensehead->name = $request->name;
		$expensehead->deleteable = true;
		$expensehead->save();

		return redirect()->route('expensehead.index')->with('message', 'Head created successfully.');
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$expensehead = ExpenseHead::findOrFail($id);

		return view('expensehead.show', compact('expensehead'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$expensehead = ExpenseHead::findOrFail($id);

		return view('expensehead.edit', compact('expensehead'));
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
		if (!is_allowed('product-edit'))
		{
			// return 0;
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$expensehead = ExpenseHead::findOrFail($id);
		$expensehead->name = $request->name;
		$expensehead->save();

		return redirect()->route('expensehead.index')->with('message', 'Expense Head Updated Successfully.');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if (!is_allowed('product-delete'))
		{
			// return 0;
			return redirect('/');
			// return response(['message'=>'Unauthorised'],500);
		}
		$expensehead = ExpenseHead::findOrFail($id);
		$expensehead->delete();

		return redirect()->route('expensehead.index')->with('message', 'Expense deleted successfully.');
	}

	public function returnJson(Request $req)
    {
    	if (isset($req->q)){
    		$tables = ExpenseHead::Where('name', 'like', '%' . $req->q.'%')->get();
    		$supplier = $tables->merge(ExpenseHead::Where('id', 'like', '%' . $req->q.'%')->get());
    	}else {
    	 	# code...
    	 	$supplier = ExpenseHead::all();
    	 } 
    	 $returnArray = [];
    	 foreach ($supplier as $key => $value) {
    	 	$returnArray[] = ['id'=>$value->id, 'text'=>$value->id.": " . $value->id];
    	 }
    	 return $returnArray;
    }

	public function process_json()
    {
        
        \Debugbar::disable();
        $prod = ExpenseHead::all();
        $processor = [];
        foreach ($prod as $key => $value) {
            # code...
            $processor[] = ["id"=>$value['id'], "text"=>$value['name']];
        }
        return "var expense_head_json_d=".json_encode($processor);

    }

}
