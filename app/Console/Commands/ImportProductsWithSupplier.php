<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Storage;
use App\Products;
use App\StockManage;
use App\SupplierPriceRecord;
use App\Unit;


class ImportProductsWithSupplier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products along with supplier,warehouse,purchase price and quantity';

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

        @\Excel::load($file_location, function ($reader) {
            // Loop through all sheets
            $reader->each(function ($sheet) {
                try {
                    echo "Process row: ".++$this->row."\n";
                    if ($sheet->delete && $sheet->delete == "1") {
                        $product = Products::whereId($sheet->id)->delete();
                    } elseif ($sheet->id) {
                        $pr = Products::withTrashed()->firstOrNew(['id'=>$sheet->id]);
                        $pr->name = $sheet->name;
                        $pr->brand = (!$sheet->brand) ? null : $sheet->brand;
                        $pr->size = (!$sheet->size) ? null : $sheet->size;
                        $pr->color = (!$sheet->color) ? null : $sheet->color;
                        $pr->pattern = (!$sheet->pattern) ? null : $sheet->pattern;
                        $pr->barcode = (!$sheet->barcode) ? null : $sheet->barcode;
                        $pr->description = (!$sheet->description) ? null : $sheet->description;
                        $pr->min_sale_price = (!$sheet->min_sale_price) ? null : $sheet->min_sale_price;
                        $pr->category_id = (!$sheet->category) ? 1 : (int)$sheet->category;
                        $pr->notify = (!$sheet->notify) ? 0 : $sheet->notify;
                        $pr->salePrice = (!$sheet->sale_price) ? 0 : $sheet->sale_price;
                        if (!$sheet->unit_name) {
                            $sheet->unit_name = "pcs";
                        }
                        $unit = Unit::firstOrNew(['name'=>$sheet->unit_name]);
                        $unit->save();
                        $pr->unit_id = $unit->id;
                        if (empty($pr->added_by)) {
                            $pr->added_by = \Auth::id();
                        } else {
                            $pr->edited_by = \Auth::id();
                        }
                        $pr->save();
                        if (!empty($sheet->supplier) && !empty($sheet->purchase_price)) {
                            $supplier_price = new SupplierPriceRecord;
                            $supplier_price->date = date('Y-m-d');
                            $supplier_price->product_id = (int)$sheet->id;
                            $supplier_price->supplier_id = (int)$sheet->supplier;
                            $supplier_price->price = $sheet->purchase_price;
                            $supplier_price->save();

                            if (!empty($sheet->quantity) && !empty($sheet->warehouse)) {
                                $stock_m = new StockManage;
                                $stock_m->date = date('Y-m-d');
                                $stock_m->product_id = (int)$sheet->id;
                                $stock_m->supplier_id = (int)$sheet->supplier;
                                $stock_m->warehouse_id = (int)$sheet->warehouse;
                                $stock_m->type = ((int)$sheet->quantity > 0)?'in':'out';
                                $stock_m->quantity = abs($sheet->quantity);
                                $stock_m->save();
                            }
                        }
                        $this->product_count++;
                    }
                } catch (\Exception $e) {
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
