<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Customer;
use App\Invoice;
use App\Transaction;
use App\StockManage;
use App\Rates;
use App\SaleOrder;

use DB;

class MergeCustomerProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Will merge given ids of Customers';

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
        /*
                

ID  REG #   Name    Phone Number    City    Type    Notifications   Notify Days Last Contact on Balance OPTIONS
485 -   SABIR BEARING FM BB 1,825 DR      
413     SABIR BEARING FM BB 196,090 CR

488 -   ADNAN BEARING BB            counter OFF 0   -   14,620 CR      
405     ADNAN BEARING BB LHR 945,062 CR

481 -   IRFAN TRADERS BB            counter OFF 0   -   108,300 DR     
450     IRFAN TRADER BB         counter ON  30  30-Nov--0001    114,720 CR

        */
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $ids = [[413,485], [405,488], [450,481]];
        foreach ($ids as $key => $value) {
            # code...
             Invoice::where('customer_id',$value[0])->update(['customer_id'=>$value[1]]);
            //transfer all the transactions
            Transaction::where('customer_id',$value[0])->update(['customer_id'=>$value[1]]);
            //Stock Log
            StockManage::where('customer_id',$value[0])->update(['customer_id'=>$value[1]]);
            //transfer supplier price record to customer id
            Rates::where('customer_id',$value[0])->update(['customer_id'=>$value[1]]);
            //okay
            SaleOrder::where('customer_id',$value[0])->update(['customer_id'=>$value[1]]);

            Customer::whereId($value[0])->delete();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        //
    }
}
