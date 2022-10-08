<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FixMinSalePriceIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:minsaleprice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will fix the min sale price issue in direct sale invoices';

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
        $line = 0;
        $invoices = \App\Invoice::where("type", "sale")->with('orders')->get();

        foreach ($invoices as $invoice) {
            
            foreach($invoice->orders as $order)
            {
                if ($order->original_price > 0)
                {
                    continue;//skip to avoid disturbing data
                }
                $order->original_price = $order->salePrice;
                $order->salePrice = $order->product->min_sale_price;
                $order->save();
                $line++;
            }

        }
        echo "Lines: ".$line."Updated";

    }
}
