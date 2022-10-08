<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\AddPriceRecordRequest;
use App\Http\Requests\UpdatePriceRecordRequest;

use Yajra\Datatables\Facades\Datatables;

use View;
use DB;
use Exception;


use App\PriceRecord;

class PriceRecordController extends Controller
{
	public function __construct()
	{
        \View::share('title',"Price Record");
		View::share('load_head',true);
		View::share('price_record_menu',true);
	}

    //
    public function index()
    {
    	return view('pricerecord.index');
    }


    public function edit($id)
    {
    	$content = PriceRecord::whereId($id)->first();
    	if (!$content)
    	{
    		return redirect('price_record');
    	}
    	return view('pricerecord.edit',compact('content'));
    }

    public function update($id, UpdatePriceRecordRequest $req)
    {
    	$content = PriceRecord::whereId($id)->first();
    	$content->price = $req->price;
    	$content->save();
    	return response()->json(['message' => 'Price Updated Successfully','action'=>'redirect','do'=>url('/price_record')], 200);
    }


    public function destroy($id)
    {
    	PriceRecord::whereId($id)->delete();
    	return response()->json(['message' => 'Price Deleted Successfully','action'=>'redirect','do'=>url('/price_record')], 200);
    }


    public function store(AddPriceRecordRequest $rqst)
    {
    	DB::beginTransaction();
    	foreach ($rqst->date as $key => $value) {
    		# code...
	    	try{
	    		$price = new PriceRecord;
	    		$price->product_id = $rqst->product_id[$key];
	    		$price->price = $rqst->price[$key];
	    		$price->date=date('Y-m-d',strtotime($value));
	    		$price->type="purchase";
	    		$price->save();
	    	}catch(Exception $e)
	    	{
	    		DB::rollBack();
	    		return response()->json(['message' => $e->getMessage()], 403);
	    	}
    	}//endforeach
    	DB::commit();
    	return response()->json(['message' => 'Price Added Successfully','action'=>'redirect','do'=>url('/price_record')], 200);
    }


    public function datatables()
    {
    	return Datatables::of(PriceRecord::with('product'))
    	->add_column('options',function($row){

    			return '<a class="btn btn-xs btn-warning" href="'. route('price_record.edit', $row->id) .'"><i class="glyphicon glyphicon-edit"></i> Edit</a>
                                    <form action="'. route('price_record.destroy', $row->id) .'" method="POST" style="display: inline;" >
                                        <input type="hidden" name="_method" value="DELETE">
                                        <input type="hidden" name="_token" value="'.csrf_token() .'">
                                        <button type="submit" class="btn btn-xs btn-danger" onclick="return (confirm(\'Delete? Are you sure?\'))"><i class="glyphicon glyphicon-trash"></i> Delete</button>
                                    </form>';
    		})
    	->make(true);
    }

    public function entry_price_record($date, $product_id, $price, $type=false)
    {
        $formated_date = date('Y-m-d', strtotime($date));
        $price = PriceRecord::firstOrNew(['date'=>$formated_date, 'product_id'=>$product_id]);
        $price->product_id = $product_id;
        $price->price = $price;
        $price->date=$formated_date;
        if ($type)
        {
            $price->type=$type; 
        }
        $price->save();
        return true;
    }
}
