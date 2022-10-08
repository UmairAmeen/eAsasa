<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\ChequeManager;
use Illuminate\Http\Request;
use App\Http\Requests\CreateChequeRequest;
use Yajra\Datatables\Facades\Datatables;

use View;
use DB;
use Exception;

class ChequeManagerController extends Controller {

	public function __construct()
	{
		\View::share('title',"Cheque Manager");
		 View::share('load_head', true);
		 View::share('cheaque_menu',true);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if (!is_allowed('transaction-list'))
			{
				return redirect('/');
			}
		$cheque_managers = ChequeManager::orderBy('id', 'desc')->paginate(10);

		return view('cheque_managers.index', compact('cheque_managers'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		return view('cheque_managers.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param Request $request
	 * @return Response
	 */
	public function store(CreateChequeRequest $request)
	{
		if (!is_allowed('transaction-create'))
			{
				return response()->json(['message' => "Unauthorized"], 403);
			}
		DB::beginTransaction();
		foreach ($request->bank as $key => $value) {
			try{
				$type = $request->type;
				if (!$request->amount[$key])
				{
					continue;
				}
				$transaction = new ChequeManager();
				$transaction->date = date('Y-m-d',strtotime($request->date));	
				$transaction->type = $type;
				$transaction->bank = $request->bank[$key];
				$transaction->amount = $request->amount[$key];
				$transaction->release_date = date('Y-m-d',strtotime($request->release_date[$key]));
				if ($request->transacion_id[$key]) {
					$transaction->transaction_id = $request->transacion_id[$key];
				}

				// $transaction->invoice_id = $request->transacion_id[$key];
				if ($request->customer[$key]) {
					$transaction->customer_id = $request->customer[$key];
				}

				$transaction->save();
			} catch(\Exception $e)
			{
				DB::rollBack();
				
				return response()->json(['message' => $e->getMessage()], 403);
			}
		}

		DB::commit();
		return response()->json(['message' => 'Cheque added successfully.','action'=>'update','do'=>'.cheaque_listing'], 200);
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
		$cheque_manager = ChequeManager::findOrFail($id);

		return view('cheque_managers.edit', compact('cheque_manager'))->render();
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		
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
		if (!is_allowed('transaction-edit'))
			{
				return response()->json(['message' => "Unauthorized"], 403);
			}
		DB::beginTransaction();
		try{
			// $type = $request->type;
			$transaction = ChequeManager::whereId($id)->first();
			if (!$transaction)
			{
				throw new Exception("Invalid Request", 1);
			}
			$transaction->date = date('Y-m-d',strtotime($request->date));	
			// $transaction->type = $type;
			$transaction->bank = $request->bank;
			$transaction->amount = $request->amount;
			$transaction->release_date = date('Y-m-d',strtotime($request->release_date));
			// if (isset($request->transacion_id[$key])) {
			$transaction->transaction_id = $request->transacion_id;
			// }

			// $transaction->invoice_id = $request->transacion_id[$key];
			if ($request->customer) {
				$transaction->customer_id = $request->customer;
			}else{
				$transaction->customer_id = null;
			}

			$transaction->save();
		} catch(\Exception $e)
		{
			DB::rollBack();
			
			return response()->json(['message' => $e->getMessage()], 403);
		}

		DB::commit();
		return response()->json(['message' => 'Cheque updated successful.','action'=>'update','do'=>'.cheaque_listing'], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
		public function destroy($id)
	{
		if (!is_allowed('transaction-delete'))
			{
				return response()->json(['message' => "Unauthorized"], 403);
			}
		$transaction = ChequeManager::findOrFail($id);
		$transaction->delete();

		return response()->json(['message' => 'Cheque added successfully.','action'=>'update','do'=>'.cheaque_listing'], 200);
		// return redirect()->route('transactions.index')->with('message', 'Transaction deleted successfully.');
	}

	public function datatables()
    {
    	// echo "abc";
    	
    	// $all = unserialize(Cache::get('stockmanages'));

    	return Datatables::of(ChequeManager::with('customer'))
    	->edit_column('customer.name', function($row){
    		if ($row->customer)
    		{
    			
    		return "<a href='/customers/".$row->customer->id."' target='_blank'>".$row->customer->name."<br><small>".$row->customer->city."</small></a>";
    		}
    		return "N/A";
    	})
    		->edit_column('type', function($row){
    			if ($row->type == "in")
    			{
    				return "<span class='label label-success'>received</span>";
    			}
    			return "<span class='label label-warning'>forward</span>";
    		})
    		->edit_column('date', function($row){
    			return date("d-M-Y",strtotime($row->date));
    		})
    		->edit_column('release_date', function($row){
    			if (strtotime($row->release_date) < 1)
    			{
    				return "-";
    			}
    			return date("d-M-Y",strtotime($row->release_date));
    		})
    		->add_column('options',function($row){
    			return '
                                   <a class="btn btn-xs btn-warning" href="#" onclick="fetch_show(\''.route('cheque_managers.show', $row->id) .'\', \'#spareModal .modal-content\', true)"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="'. route('cheque_managers.destroy', $row->id).'" method="POST" style="display: inline;" onsubmit="if(confirm(\'Delete? Are you sure?\')) { return true } else {return false };">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="'.csrf_token() .'">
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>';
    		})
    	->make(true);
    }

}
