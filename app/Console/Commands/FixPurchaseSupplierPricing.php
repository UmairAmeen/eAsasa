<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Invoice;
use App\Order;

class FixPurchaseSupplierPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:purchaserecord';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixing Purchase Price Record Issue';

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
        $all_purchase_invoice = Invoice::where('type','purchase')->get();

        foreach ($all_purchase_invoice as $invoice) {
            # code...
            foreach ($invoice->orders as $key => $order) {
                # code...
                supplier_price_record($invoice->date, $order->salePrice, $invoice->supplier_id, $order->product_id);
            }
        }
        //
    }
}
