<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Storage;
use App\Products;
use App\Unit;

class ProcessExcelFileWithQuantity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Excel File: process.xlsx and sync quantities and delete unwanted products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
            DB::beginTransaction();
            $file_location = "process.xlsx";
            $this->product_count = $this->created = $this->deleted = $this->row = 0;

            \Excel::load($file_location, function($reader) {
            // Loop through all sheets
            $reader->each(function($sheet) {
                // dd($sheet);
        try{
            // $this->row++;
            echo "Process row: ".++$this->row."\n";
                if ($sheet->delete)
                {
                    if ($sheet->id)
                    {
                        Products::whereId($sheet->id)->delete();
                        // continue;
                        $this->deleted++;
                    }
                    // continue;
                }else if ($sheet->id)
                {
                    $pr = Products::withTrashed()->firstOrNew(['id'=>$sheet->id]);
                    $pr->name = $sheet->name;
                    $pr->barcode = $sheet->barcode;
                    if (!$sheet->brand)
                    {
                        $pr->brand = NULL;
                    }else{
                        $pr->brand = $sheet->brand;
                    }
                    if(!$sheet->saleprice)
                    {
                        $pr->salePrice = 0;
                    }else{
                        $pr->salePrice = $sheet->saleprice;
                    }


                    if (!$sheet->unit_name)
                    {
                        $sheet->unit_name = "pcs";
                    }
                    
                    $unit = Unit::firstOrNew(['name'=>$sheet->unit_name]);
                    $unit->save();

                    $pr->unit_id = $unit->id;
                    $pr->save();


                    //Stock Update
                    warehouse_stock_adj($pr, "murghi market", ($sheet->murghi)?:0);
                    warehouse_stock_adj($pr, "MUBARIK MARKET", ($sheet->mubarik)?:0);
                    warehouse_stock_adj($pr, "shop", ($sheet->shop)?:0);

                    $this->product_count++;
                }else if ($sheet->name){
                    //helps merging
                    $pr = Products::firstOrNew(['name'=>$sheet->name, "brand"=>$sheet->brand]);
                    $pr->name = $sheet->name;
                    $pr->barcode = $sheet->barcode;
                    $pr->brand = $sheet->brand;
                    $pr->salePrice = ($sheet->saleprice)?:0;
                     if (!$sheet->unit_name)
                    {
                        $sheet->unit_name = "pcs";
                    }
                    $unit = Unit::firstOrNew(['name'=>$sheet->unit_name]);
                    $unit->save();

                    $pr->unit_id = $unit->id;
                    $pr->save();

                    warehouse_stock_adj($pr, "murghi market", ($sheet->murghi)?:0);
                    warehouse_stock_adj($pr, "MUBARIK MARKET", ($sheet->mubarik)?:0);
                    warehouse_stock_adj($pr, "shop", ($sheet->shop)?:0);
                    $this->created++;
                }
                
            }catch(\Exception $e)
            {
                echo "Issue with: ".$sheet->id." ".$sheet->name." ".$sheet->brand."\n";
                echo $e->getMessage()."\n"."==========="."\n";
            }
                               
            });
        })->get();
            DB::commit();
        // Cache::forget('products');
        echo  'Succesfully '.$this->created.' Created \n';
        echo  'Succesfully '.$this->deleted.' Deleted \n';
        echo  'Succesfully '.$this->product_count.' Updated \n';
    }
}
