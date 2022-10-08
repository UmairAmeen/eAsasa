<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Supplier;
use App\Customer;
use App\Invoice;
use App\Transaction;
use App\StockManage;
use App\SupplierPriceRecord;
use DB;

class SupplierTableToCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:supplier';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer Supplier To Customer Table';

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
        $suppliers = Supplier::all();

        foreach ($suppliers as $key => $value) {
            # code...
            $customer = Customer::firstOrNew(['supplier_id'=>$value->id]);
            $customer->name = $value->name;
            $customer->phone = $value->phone;
            $customer->city=$value->address;
            $customer->description=$value->description;
            $customer->type = "counter";
            $customer->payment_notify=false;
            $customer->after_last_payment=0;
            $customer->save();


            //transfer all the invoices
            Invoice::where('supplier_id',$value->id)->update(['customer_id'=>$customer->id, "supplier_id"=>null]);
            //transfer all the transactions
            Transaction::where('supplier_id',$value->id)->update(['customer_id'=>$customer->id, "supplier_id"=>null]);
            //Stock Log
            StockManage::where('supplier_id',$value->id)->update(['customer_id'=>$customer->id, "supplier_id"=>null]);
            //transfer supplier price record to customer id
            SupplierPriceRecord::where('supplier_id',$value->id)->update(['supplier_id'=>$customer->id]);
        }
    }
}
