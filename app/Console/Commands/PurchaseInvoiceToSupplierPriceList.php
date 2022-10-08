<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Invoice;
use DB;
use Exception;

class PurchaseInvoiceToSupplierPriceList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'purchase:move';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Move All Purchases to Supplier List';

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
        //
        $all_purchases_invoices = Invoice::where('type','purchase')->get();
        DB::beginTransaction();
        try{
            foreach ($all_purchases_invoices as $key => $invoice) {
                $supplier_id = $invoice->supplier_id;
                $date = $invoice->date;

                foreach ($invoice->orders as $key => $order) {
                    # code...
                    $product_id = $order->product_id;
                    $price = $order->salePrice;
                    supplier_price_record($date, $price, $supplier_id, $product_id);
                }//order loop

            }//invoice loop
            DB::commit();
            return true;
        }catch(Exception $e)
        {
            DB::rollBack();
            return false;
        }
        
    }
}
