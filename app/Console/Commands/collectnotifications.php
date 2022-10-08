<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SMController;

use App\AppointmentCalendar;
use App\ChequeManager;
use App\Customer;
use App\Transaction;

use Carbon\Carbon;
use DB;

class collectnotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collectnotifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will finalize all notification that require to be sent today';

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
        $notification = [];
        #All Cheque Notification
        $cheque_manager = ChequeManager::where('release_date',date('Y-m-d'))->get();
        foreach ($cheque_manager as $key => $value) {
            
            $customer_name = ($value->customer)?$value->customer->name:"";

            // $notification["Cheque Manager"][] = $value->bank." *".$value->transaction_id."* (".number_format($value->amount).") * ".$customer_name." | ".($value->type=="in")?"Received":"Forwarded";
            $chq  = ($value->bank)?"Bank: ".$value->bank."\n":"";
            $chq .= ($value->transaction_id)?"Cheque: ".$value->transaction_id."\n":"";
            $chq .= ($value->amount)?"Amount: ".number_format($value->amount)."\n":"";
            $chq .= ($customer_name)?"Customer: ".$customer_name."\n":"";
            $chq .= ($value->type=="in")?"Type: Received":"Type: Forwarded";
            $chq .= "\n----------------------------\n";

            $notification["Cheque Manager"][] = $chq;
        }

        # All Payment Collection Notification
        $transactions = Transaction::where('payment_type','cheque')->where('release_date',date('Y-m-d'))->where('amount','>',0)->get();
        foreach ($transactions as $key => $value) {
            $customer_name = ($value->customer)?$value->customer->name:"";
            // $notification["Payment Notification"][] = $value->bank." *".$value->transaction_id."* (".number_format($value->amount).")* ".$customer_name." | ".($value->type=="in")?"Received":"Forwarded";

            $chq  = ($value->bank)?"Bank: ".$value->bank."\n":"";
            $chq .= ($value->transaction_id)?"Transaction#: ".$value->transaction_id."\n":"";
            $chq .= ($value->amount)?"Amount: ".number_format($value->amount)."\n":"";
            $chq .= ($customer_name)?"Customer: ".$customer_name."\n":"";
            $chq .= ($value->type=="in")?"Type: Debit":"Type: Credit";
            $chq .= "\n----------------------------\n";

            $notification["Payment Notification"][] = $chq;
        }
        # All Calendar Notification
        $customer_call = Customer::where('after_last_payment','=', DB::raw('DATEDIFF(NOW(), last_contact_on)'))->where('payment_notify',true)->get();
        foreach ($customer_call as $key => $value) {
            $balance = getCustomerBalance($value->id);
            if ($balance < 1)
            {
                continue;
            }
            // $notification["Payment Call"][] = $value->name."* Last Contact: ".date('d-M-Y',strtotime($value->last_contact_on))."* Credit: ".$balance;

            // $chq  = ($value->bank)?"Bank: ".$value->bank."\n":"";
            // $chq .= ($value->transaction_id)?"Transaction#: ".$value->transaction_id."\n":"";
            // $chq .= ($value->amount)?"Amount: ".number_format($value->amount)."\n":"";
            $chq  = "Customer: ".$value->name."\n";
            $chq .= "Phone: ".$value->phone."\n";
            $chq .= "Last Contact: ".date('d-M-Y',strtotime($value->last_contact_on))."\n";
            $chq .= "Balance: ".number_format($balance)."\n";
            $chq .= "----------------------------\n";

            $notification["Payment Call"][] = $chq;
        }


        #OverDUE PAYMENT CALL
        # All Calendar Notification
        $days = [7,14,21,28];
        $customer_call = Customer::where('payment_notify',true)->where('after_last_payment','<', DB::raw('DATEDIFF(NOW(), last_contact_on)'))->get();
        foreach ($customer_call as $key => $value) {
            //OverDue Calculations
            $end = Carbon::parse($value->last_contact_on);
            $now = Carbon::now();
            $length = $end->diffInDays($now);
            $should_notify = false;
            foreach ($days as $k => $x) {
                # code...
                // echo '='.$length."\n-".($x + $value->after_last_payment)."\n\n";
                if ($length == ($x + $value->after_last_payment))
                {
                    $should_notify = $x+$value->after_last_payment;
                }
            }
            //End OverDue Calculations

            if (!$should_notify)
            {
                //exempt from Notification
                continue;
            }
            $balance = getCustomerBalance($value->id);
            if ($balance < 1)
            {
                continue;
            }
            // $notification["Payment Call"][] = $value->name."* Last Contact: ".date('d-M-Y',strtotime($value->last_contact_on))."* Credit: ".$balance;

            // $chq  = ($value->bank)?"Bank: ".$value->bank."\n":"";
            // $chq .= ($value->transaction_id)?"Transaction#: ".$value->transaction_id."\n":"";
            // $chq .= ($value->amount)?"Amount: ".number_format($value->amount)."\n":"";
            $chq  = "Customer: ".$value->name."\n";
            $chq .= "Phone: ".$value->phone."\n";
            $chq .= "Last Contact: ".date('d-M-Y',strtotime($value->last_contact_on))."\n";
            $chq .= "Overdue Days: ".$should_notify."\n";
            $chq .= "Balance: ".number_format($balance)."\n";
            $chq .= "----------------------------\n";

            $notification["OVERDUE Payment Call"][] = $chq;
        }
            // dd($should_notify);

         # All Calendar Notification
        $appointment_calendars = AppointmentCalendar::where(DB::raw('date(`start`)'), '=', date('Y-m-d'))->orderBy('id', 'desc')->get();
        foreach ($appointment_calendars as $key => $value) {
            $notification["Calendar Notification"][] = $value->title. "\n----------------------------\n";
        }
        //
        $reports = [];
        $format = $date."\n";
        if (empty($notification))
        {
            $format .= "\n"." No Notification for Today"."\n";
        }

        foreach ($notification as $key => $value) {
            $format .= "\n *".$key."*\n";
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
    }
}
