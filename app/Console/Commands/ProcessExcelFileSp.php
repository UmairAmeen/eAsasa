<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Storage;
use App\Products;
use App\Supplier;
use App\Invoice;
use App\Unit;
use App\ProductCategory;
use App\Order;
use App\StockManage;

class ProcessExcelFileSp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:excel_sp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Excel with custom modules';

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
        $file_location = "excel_file.xlsx";
        $this->product_count = $this->created = $this->deleted = $this->row = 0;
        $this->default_supplier = Supplier::firstOrNew(['name' => "Default Supplier"]);
        $this->default_supplier->save();

        @\Excel::load($file_location, function ($reader) {
            // Loop through all sheets
            $reader->each(function ($sheet) {
                try {
                    // $this->row++;
                    echo "Process row: ".++$this->row."\n";

                    if ($sheet->product || $sheet->brand) {
                        $product = Products::firstOrNew(['name' => ($sheet->product)?:"-", 'brand' => $sheet->brand,'barcode' => $sheet->barcode,'size' => $sheet->size,'color' => $sheet->color]);

                        if ($sheet->category) {
                            //add new product category
                            $category = ProductCategory::firstOrNew(['name' => $sheet->category]);
                            $category->save();
                            $product->category_id = $category->id;
                        }
                        //add new product
                        if (!$sheet->unit_name) {
                            $sheet->unit_name = "pcs";
                        }
                        $unit = Unit::firstOrNew(['name'=>$sheet->unit_name]);
                        $unit->save();
                        $product->unit_id = $unit->id;
                        $product->size = $sheet->size ? : null;
                        $product->color = $sheet->color ? : null;
                        $product->pattern = $sheet->pattern ? : null;
                        $product->description = $sheet->desc ? : null;
                        $product->itemcode = $sheet->item_code ? : null;
                        $product->barcode = (!$sheet->barcode) ? null : $sheet->barcode;
                        $product->brand = (!$sheet->brand) ? null : $sheet->brand;
                        $product->salePrice = $sheet->sale_price ? : 0;
                        if ($sheet->min_sale_price) {
                            $product->min_sale_price = $sheet->min_sale_price  ? : 0;
                        }
                        $product->save();

                        if ($sheet->supplier) {
                            //add new supplier
                            $supplier = Supplier::firstOrNew(['name' => $sheet->supplier]);
                            $supplier->save();
                        } else {
                            $supplier = $this->default_supplier;
                        }
                    
                        if ($sheet->qty > 0) {
                    
                            //add new purchase invoice with qty and cost price
                            $invoice = new Invoice;
                            $invoice->supplier_id = $supplier->id;
                            $invoice->type = "purchase";
                            $invoice->date = date("Y-m-d");
                            $invoice->total = $sheet->qty * ($sheet->purchase_price)?:0;
                            $invoice->save();
                    
                            //add invoice item in Order model
                            $order = new Order;
                            $order->invoice_id = $invoice->id;
                            $order->product_id = $product->id;
                            $order->quantity = $sheet->qty;
                            $order->salePrice = ($sheet->purchase_price)?:0;
                            $order->save();

                            //add new stock
                            $stock = new StockManage;
                            $stock->product_id = $product->id;
                            $stock->supplier_id = $supplier->id;
                            $stock->quantity = $sheet->qty;
                            $stock->date = date("Y-m-d");
                            $stock->type="purchase";
                            $stock->warehouse_id = 1;
                            $stock->purchase_id = $order->id;
                            $stock->save();
                        }
                
                        if ($sheet->purchase_price) {
                            //add supplier price record
                            supplier_price_record(date("Y-m-d"), $sheet->purchase_price, $supplier->id, $product->id);
                        }
                        $this->created++;
                    }
                } catch (\Exception $e) {
                    echo "Issue with: ".$sheet->id." ".$sheet->product." ".$sheet->brand."\n";
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
