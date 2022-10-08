<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Invoice;
use App\Transaction;
use App\StockManage;
use App\Rates;
use App\SaleOrder;
use App\Order;
use App\PriceRecord;
use App\Purchase;
use App\SupplierPriceRecord;
use App\Products;
use App\Refund;
use DB;

class MergeProductProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:product {existing_product_id} {new_product_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This Will merge given ids of Products, existing_product_id <- new product id, merge:product {existing_product_id} {new_product_id}';

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
        $existing_product_id = $this->argument('existing_product_id');
        $new_product_id = $this->argument('new_product_id');
        DB::beginTransaction();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        #change inventory information
        StockManage::where('product_id',$new_product_id)->update(['product_id'=>$existing_product_id]);
        #update order
        #update sale invoice
        #update sale orders
        Order::where('product_id',$new_product_id)->update(['product_id'=>$existing_product_id]);
        #update purchases
        Purchase::where('product_id',$new_product_id)->update(['product_id'=>$existing_product_id]);
        #update supplier record
        PriceRecord::where('product_id',$new_product_id)->update(['product_id'=>$existing_product_id]);
        SupplierPriceRecord::where('product_id',$new_product_id)->update(['product_id'=>$existing_product_id]);
        #update refund
        Refund::where('product_id',$new_product_id)->update(['product_id'=>$existing_product_id]);
        #update customer pricing record
        Rates::where('product_id',$new_product_id)->update(['product_id'=>$existing_product_id]);
        #update product groups
        #delete exiting product
        Products::whereId($new_product_id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::commit();
        //
    }
}
