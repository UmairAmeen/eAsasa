<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Invoice;
use App\Transaction;

class FixTransactionWithInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:invoiceTransaction';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix all ledgers with Invalid Transactions';

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
        $backup_file_in_txt = "changes_".date("Y-m-d H:i:s")."_backup.txt";

        $changes = ['invoice' => [], 'transaction' => []];
        echo Invoice::where('is_manual',false)->orWhereNull('is_manual')->count();
        $this->progressBar = $this->output->createProgressBar(Invoice::where('is_manual',false)->orWhereNull('is_manual')->count());
        $this->progressBar->setFormat('%current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%');
        $this->progressBar->setBarCharacter('#');

        $this->total_update = $this->transaction_update = 0;

        $invoices = Invoice::where('is_manual',false)->orWhereNull('is_manual')->get();
        //update all invoices using worth calculator
        foreach ($invoices as $key => $invoice) {
            # code...
            $total = 0;
            //loop through all order
            foreach ($invoice->orders as $order) {
                //add order total to total
                $total += $order->salePrice * $order->quantity;
            }
            $total += $invoice->shipping - $invoice->discount + $invoice->tax;
            //update invoice total
            if ($invoice->total != $total)
            {
                // $changes[] = "Invoice: ".$invoice->id." Total: ".$invoice->total." -> ".$total;
                $changes['invoice'][] = ["id"=>$invoice->id,"invoice_total"=>$invoice->total,"total"=>$total];
                $this->total_update++;
                $invoice->total = $total;
                $invoice->save();
            }
            //update invoice related in/out transaction based on invoice type
            $trans_type = ($invoice->type == "purchase")?"in":"out";


            $transaction = Transaction::where('invoice_id',$invoice->id)->where('type',$trans_type)->first();
            if ($transaction)
            {
                if ($transaction->amount != $total)
                {
                //     echo "Our Total: ".$total."\n";
                // echo "Invoice Total: ".$invoice->total."\n";
                // echo "Invoice Id: ".$invoice->id."\n";
                // echo "Transaction Id: ".$transaction->id."\n";
                // echo "Transaction Amount: ".$transaction->amount."\n";
                // exit();
                // die();
                    $changes['transaction'][] = ["id"=>$transaction->id,"trans_amt"=>$transaction->amount,"total"=>$total];
                    $transaction->amount = $total;
                    $transaction->save();

                    $this->transaction_update++;
                }
            }

            //update progress bar
            $this->progressBar->advance();
        }
        //store changes in storage
        file_put_contents(storage_path("app/public/".$backup_file_in_txt), json_encode($changes));

        $this->progressBar->finish();
        
        echo "\n==> Result <==\n";
        echo "=> Invoice Updated: ".$this->total_update."\n";
        echo "=> Transaction Updated: ".$this->transaction_update."\n";
        echo "=> Changes:".$backup_file_in_txt." \n";
    }
}
