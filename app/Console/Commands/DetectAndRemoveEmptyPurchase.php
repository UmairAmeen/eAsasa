<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Invoice;

class DetectAndRemoveEmptyPurchase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:empty-purchase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This fix the error caused by Mr. Imran Yahya which make purchase invoice everytime we use stock in/out/transfer';

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
        $count = 0;
        $invoices = Invoice::where('type', 'purchase')->get();
        foreach ($invoices as $invoice) {
            if ($invoice->orders->count() == 0) {
                $count++;
                $invoice->delete();
            }
        }
        $this->info("$count invoices deleted");
    }
}
