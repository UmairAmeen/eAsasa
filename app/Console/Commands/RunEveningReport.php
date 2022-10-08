<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SMController;

use App\Transaction;

use DB;

class RunEveningReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send All Credit/Debit Message';

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

        $date = "**".date('d-M-Y')."**";
        $date .= "\n**".date("h:i:s a")."**";
        $notification = [];
        # Credit Collection Notification
        $transactions = Transaction::where('updated_at', '>=', DB::raw('DATE_SUB(NOW(),INTERVAL 24 HOUR)'))->where('type','out')->where('amount','>',0)->limit(5)->get();
        // $transactions = Transaction::where('type','out')->get();
        foreach ($transactions as $key => $value) {
            $customer_name = ($value->customer)?$value->customer->name:"";
            if (!$customer_name)
            {
                $customer_name = ($value->supplier)?$value->supplier->name:"";
            }
            // DISABLE SUPPLIER START
            if ($value->supplier)
            {
                continue;
            }
            // DISABLE SUPPLIER END


            // $notification["Credit"][] = $customer_name."|".$value->transaction_id."|(".number_format($value->amount).")";
            $notification_text = "Name: ".$customer_name."\n";
            // $notification_text .= "Type: ".$value->payment_type."\n";
            // if ($value->bank)
            //     $notification_text .= "Bank: ".$value->bank."\n";
            if ($value->transaction_id)
                $notification_text .= "Transaction# ".$value->transaction_id."\n";
            if (strtotime($value->release_date) > 0)
                $notification_text .= "Release Date ".app_date_format($value->release_date)."\n";
            $notification_text .= "Amount: ".number_format($value->amount)."\n";
            $notification_text .= "----------------------------\n";
            
            $notification["Credit"][] = $notification_text;
        }

        # Debit Collection Notification
        $transactions_debit = Transaction::where('updated_at', '>=', DB::raw('DATE_SUB(NOW(),INTERVAL 24 HOUR)'))->where('type','in')->where('amount','>',0)->limit(5)->get();
        // $transactions_debit = Transaction::where('type','in')->get();
        foreach ($transactions_debit as $key => $value) {
            $customer_name = ($value->customer)?$value->customer->name:"";
            if (!$customer_name)
            {
                $customer_name = ($value->supplier)?$value->supplier->name:"";
            }

            // DISABLE SUPPLIER START
            if ($value->supplier)
            {
                continue;
            }
            // DISABLE SUPPLIER END

            
            // $notification["Debit"][] = $customer_name."|".$value->bank."|".$value->transaction_id."|".app_date_format($value->release_date)."|(".number_format($value->amount).")";
            $notification_text = "Name: ".$customer_name."\n";
            $notification_text .= "Type: ".$value->payment_type."\n";
            if ($value->bank)
                $notification_text .= "Bank: ".$value->bank."\n";
            if ($value->transaction_id)
                $notification_text .= "Transaction# ".$value->transaction_id."\n";
            if (strtotime($value->release_date) > 0)
                $notification_text .= "Release Date ".app_date_format($value->release_date)."\n";
            $notification_text .= "Amount: ".number_format($value->amount)."\n";
            $notification_text .= "----------------------------\n";
            $notification["Debit"][] = $notification_text;
        }

        $reports=[];
        $format = $date."\n";
        // print_r($notification);

        if (empty($notification))
        {
            return true;
            //donot send notification if there is no transaction
            $format .= "\n"." No Notification for Today"."\n";
            // return;
        }
        foreach ($notification as $key => $value) {
            $format .= "\n*".$key." (Upto 5 Transactions)*\n";
            foreach ($value as $d) {
                $format .= $d."\n";
            if (strlen($format) > 1500)
                {
                    $reports[] = $format;
                    $format = "\n *".$key."*\n";
                }
            }
        }
        $reports[] = $format;
        // echo $format;
        foreach ($reports as $key => $value) {
            # code...
            (new SMController)->line_notification($value);
        }
        //
    }
}