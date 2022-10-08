<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use Cache;

use App\Products;
use App\StockManage;
use App\Warehouse;
use App\Transactions;

class CacheController extends Controller
{
    static public function clearAllCache()
    {
    	Cache::flush();
    }

    static public function rebuildAllCache()
    {
    	// CacheController::clearAllCache();
    	// CacheController::buildWarehouse();
    	// CacheController::buildProducts();
    	// CacheController::buildStockManage();
    }

    static public function buildWarehouse()
    {
    	Cache::forget('warehouses');
    	return Cache::put('warehouses',serialize(Warehouse::all()), 36000);
    }

    static public function buildProducts()
    {
    	Cache::forget('products');
    	$products = Products::orderBy('updated_at')->with('unit')->get();

    	foreach ($products as $key => $value) {
    		 $products[$key]->stock = calculateStockFromProductId($value->id);
             $products[$key]->unit_name = $value->unit->name;
    	}
        // dd($products[0]);

    	return Cache::put('products',serialize($products),36000);
    }

    static public function buildStockManage()
    {
    	Cache::forget('stockmanages');

        $stock_adjustments = CacheController::buildStockManageDatatable();

        return Cache::put('stockmanages',serialize($stock_adjustments),3600);                 
    }
    static private function buildStockManageDatatable()
    {
        $all = \App\StockManage::all();
        foreach ($all as $key => $value) {

            $all[$key]->id_display = '<input type="checkbox" value="'.$all[$key]->id.'" class="form-control" name="operation_id[]">';

            # supplier
            if (!$all[$key]->supplier)
            {
                $all[$key]->supplier_display = "N/A";
            }else{
                $all[$key]->supplier_display = '<a href="/suppliers/'.$all[$key]->supplier_id.'">'.$all[$key]->supplier->name."</a>";
            }
            #warehouse
            $all[$key]->warehouse_display = '<a href="/warehouses/'.$all[$key]->warehouse->id.'">'.$all[$key]->warehouse->name.'</a>';

            #product name
            if ($all[$key]->product)
            {
                $all[$key]->product_name = '<a href="/products/'.$all[$key]->product->id.'">'.$all[$key]->product->name.'</a>';
            }else{
                $all[$key]->product_name = "N/A";
            }
            #product brand
            if (isset($all[$key]->product) && $all[$key]->product->brand)
            {
                $all[$key]->product_brand = '<a href="/products/'.$all[$key]->product->id.'">'.$all[$key]->product->brand.'</a>'; 
            }else{
                $all[$key]->product_brand = "N/A";
            }

            #customer
            if (!$value->customer)
            {
                $all[$key]->customer_display = "N/A";
            }else{
                $all[$key]->customer_display = ($all[$key]->customer)?'<a href="/customers/'.$all[$key]->customer->id.'">'.$all[$key]->customer->name.'</a>':"N/A";
            }
            #date

            $all[$key]->date_display = date('d-m-Y',strtotime($all[$key]->date));
            
            // $all[$key]->type = $all[$key]->type;
            #quantity
            $all[$key]->quantity_display = ($all[$key]->type == "in" || $all[$key]->type == "purchase")?'<span class="label label-success">+':'<span class="label label-danger">-';
            $all[$key]->quantity_display .= $all[$key]->quantity.'</span>';

            #updated 
            if($all[$key]->updated_at)
            {
                $all[$key]->updated_at_display = date('d-m-Y (h:i A)',strtotime($all[$key]->updated_at));
            }else{
                $all[$key]->updated_at_display = "N/A";
            }
            #options
            $all[$key]->options = '<a class="btn btn-xs btn-warning" href="'. route('stock.edit', $all[$key]->id) .'"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
         }

         return $all;
    }
}
