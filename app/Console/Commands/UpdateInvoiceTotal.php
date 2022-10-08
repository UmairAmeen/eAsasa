<?php

namespace App\Console\Commands;

use App\Invoice;
use App\Transaction;
use Illuminate\Console\Command;

class UpdateInvoiceTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateinvoicetotal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command is to update old invoices total worth to fix bug';

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
        $invoices = Invoice::whereNull('total')->orWhere('total', '=', 0)->get();
        foreach($invoices as $invoice) {
            $out_transaction = Transaction::select('payment_type', 'bank', 'transaction_id', 'amount')
            ->where(['type' => 'out', 'invoice_id' => $invoice->id])
            ->orderBy('id', 'asc')
            ->first();
            $total = 0;
            foreach ($invoice->orders as $key => $value) {
                //item brand description concatenation was done on client request
                $items[$value->product_id]['name'] = $value->product->name." ".$value->product->brand." ".$value->product->description . ' '. $value->note;
                if (!array_key_exists('quantity', $items[$value->product_id])) {
                    $items[$value->product_id]['quantity'] = 0;
                }
                $items[$value->product_id]['quantity'] += $value->quantity;
                $items[$value->product_id]['unit'] = $value->product->unit->name;
                $items[$value->product_id]['sale_price'] = $value->salePrice;
                $items[$value->product_id]['brand'] = $value->brand;
                $items[$value->product_id]['full'] = $value;
                if ($items[$value->product_id]['quantity'] > 0) {
                    $return = false;
                }
                $total += $value->quantity * $value->salePrice;
            }
            if ($invoice->is_manual && !empty($out_transaction->amount)) {
                $total = $out_transaction->amount - $invoice->tax;
            }
            $invoice->total = $total;
            $invoice->save();
            $this->line("Inovoice ID [{$invoice->id}] total: [{$total}]");
        }
        $this->info("Command ". $this->signature . ' completed successfully');
    }
}
