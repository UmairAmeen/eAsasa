<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Invoice;
use App\Order;

class FixInvoiceTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:invoice {type}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Command will fix invoice total';

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
        $type = $this->argument('type');
        //set progress bar
        $this->progressBar = $this->output->createProgressBar(Invoice::where('type', $type)->count());
        $this->progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
        $this->progressBar->setBarCharacter('#');
        //get all invoice based on input
        $invoices = Invoice::where('type', $type)->get();
        //loop through all invoice
        foreach ($invoices as $invoice) {
            
            //set total to 0
            $total = 0;
            //loop through all order
            foreach ($invoice->orders as $order) {
                //add order total to total
                $total += $order->salePrice * $order->quantity;
            }
            //update invoice total
            $invoice->total = $total + $invoice->shipping - $invoice->discount + $invoice->tax;
            $invoice->save();
            //update progress bar
            $this->progressBar->advance();
        }
    }
}
