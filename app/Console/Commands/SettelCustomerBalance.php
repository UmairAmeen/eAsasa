<?php

namespace App\Console\Commands;
use DB;
use Storage;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Customer;
use App\Transaction;
class SettelCustomerBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settle:customer_balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settle customer balance';

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
        DB::beginTransaction();
        $file_location = "Customers.xlsx";
        $this->customer_count = $this->created = $this->deleted = $this->row = 0;

        @\Excel::load($file_location, function ($reader) {
            // Loop through all sheets
            $reader->each(function ($sheet) {
                try {
                    // $this->row++;
                    echo "Process row: ".++$this->row."\n";

                    $customer = Customer::where('id', $sheet->id)->where('name', $sheet->name)->first();
 
                    if ($customer) {
                        $credit = Transaction::where('customer_id', $sheet->id)->where('type', 'out')->sum('amount');
                        $debit	= Transaction::where('customer_id', $sheet->id)->where('type', 'in')->sum('amount');
                        $previous_balance = abs($credit) - abs($debit);
                        
                        if ($previous_balance > 0) {
                            $transaction = new Transaction;
                            $transaction->date = Carbon::now();
                            $transaction->type = 'in';
                            $transaction->amount = abs($previous_balance);
                            $transaction->customer_id = $sheet->id;
                            $transaction->added_by = '1';
                            $transaction->description = 'Settle customer balance with debit';
                            $transaction->save();
                        } elseif ($previous_balance < 0) {
                            $transaction = new Transaction;
                            $transaction->date = Carbon::now();
                            $transaction->type = 'out';
                            $transaction->amount = abs($previous_balance);
                            $transaction->customer_id = $sheet->id;
                            $transaction->description = 'Settle customer balance with credit';
                            $transaction->added_by = '1';
                            $transaction->save();
                        }
                        if ($previous_balance == 0 || $transaction->save()) {
                            $new_transaction = new Transaction;
                            $new_transaction->date = Carbon::now();
                            $new_transaction->type = ($sheet->new_balance > 0) ? 'out' : 'in';
                            $new_transaction->amount = abs($sheet->new_balance);
                            $new_transaction->customer_id = $sheet->id;
                            $new_transaction->added_by = '1';
                            $new_transaction->description = 'new balance';
                            $new_transaction->save();
                            $this->created++;
                        }
                    } else {
                        echo "".$sheet->name." Not Exist In Database. \n";
                    }
                } catch (\Exception $e) {
                    echo "Issue with: ".$sheet->id." ".$sheet->name." ".$sheet->city."\n";
                    echo $e->getMessage()."\n"."==========="."\n";
                }
            });
        })->get();
        DB::commit();
        echo  "Succesfully ".$this->created." Created \n";
        echo  "Succesfully ".$this->deleted." Deleted \n";
        echo  "Succesfully ".$this->customer_count." Updated \n";
    }
}
