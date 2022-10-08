<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Order;
use App\SupplierPriceRecord;
use Illuminate\Console\Command;

class FixPurchaseOrderPricing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:purchaseOrderPrice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Order Details and update purchase price for each product';

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
        $total = $success = $failer = 0;
        $purchase_price = [];
        $supplier_prices = SupplierPriceRecord::orderBy('date', 'desc')->get();
        foreach ($supplier_prices as $supplier_price) {
            $purchase_price[$supplier_price->product_id][] = [
                'date' => $supplier_price->date,
                'price' => $supplier_price->price
            ];
        }
        $saleInvoices = Invoice::where('type', 'LIKE', 'sale%')->get();
        foreach( $saleInvoices as $invoice) {
            $this->line(" Processing INVOICE: {$invoice->id} ...");
            $invoiceDate = $invoice->date;
            $orders = $invoice->orders;
            foreach ($orders as $order) {
                $total++;
                $price = NULL;
                foreach($purchase_price[$order->product_id] as $record) {
                    if($invoiceDate >= $record['date']) {
                        $this->error(json_encode($record));
                        $price = $record['price'];
                    }
                }
                if ($price == NULL && !empty($purchase_price[$order->product_id][0]['price'])) {
                    $price = $purchase_price[$order->product_id][0]['price'];
                }
                if ($price != NULL) {
                    $order = Order::find($order->id);
                    if($order->purchasePrice == NULL) {
                        $order->purchasePrice = $price;
                        $order->save();
                        $success++;
                    }
                    $this->line("Invoice[{$invoice->id}] Dated: [{$invoiceDate}], Order: [{$order->id}] ProductID [{$order->product_id}] has pruchase Price : {$price}");
                } else {
                    $this->line("Invoice[{$invoice->id}] Dated: [{$invoiceDate}], Order: [{$order->id}] ProductID [{$order->product_id}] has : ");
                    $this->warn(json_encode($purchase_price[$order->product_id]));
                    $failer++;
                }
            }
        }
        $this->line("Orders: Total [{$total}], Updated [{$success}] Missed [{$failer}]");
    }
}
