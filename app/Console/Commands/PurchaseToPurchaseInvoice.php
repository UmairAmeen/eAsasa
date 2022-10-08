<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Invoice;
use App\Purchase;
use App\Transaction;
use App\Order;
use DB;
use Exception;

class PurchaseToPurchaseInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:purchase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purchases to Purchase Invoice';

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
        $purchases = Purchase::where('on_invoice',false)->get();
        echo "\n"."Processing Purchases: ".count($purchases)."\n";
        foreach ($purchases as $key => $value) {

            try{
                DB::beginTransaction();
                $total = $value->price*$value->stock;

                $invoice = new Invoice();
                $invoice->description = " ";
                $invoice->shipping = 0;
                $invoice->supplier_id = $value->supplier_id;
                $invoice->discount = 0;
                $invoice->total = $total;
                $invoice->type="purchase";
                $invoice->date = date('Y-m-d',strtotime($value->date));
                $invoice->save();

                $invoice_id = $invoice->id;


                // $transaction = new Transaction;
                // $transaction->date = date('Y-m-d',strtotime($value->date));
                // $transaction->supplier_id = $value->supplier_id;
                // $transaction->amount = $total;
                // $transaction->type = "in";//debit
                // $transaction->invoice_id = $invoice_id;
                // $transaction->save();

                    
                $order = new Order();
                $order->invoice_id = $invoice_id;
                $order->product_id = $value->product_id;
                $order->salePrice = $value->price;
                $order->quantity = $value->stock;
                $order->save();


                $value->on_invoice = true;
                $value->save();
                DB::commit();
            }catch(Exception $e)
            {
                echo "\n".$e->getLine()."\n".$e->getMessage()."\n\n";
                DB::rollBack();
            }
        }
        
    }
}
