<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
 use Faker\Generator as Faker;
use Illuminate\Support\Str;

use App\Customer;
use App\Supplier;
use App\Invoice;
use App\Order;
use App\Products;
use App\Warehouse;
use App\Rates;
use App\Transaction;
use App\User;
use App\StockManage;

class AddSaleForDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:invoices {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        if (\App\User::first()->email != "0345-4777487")
        {
            echo "Command Not Allowed".PHP_EOL;
            return false;
        }
        //

        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Device($faker));
        // $faker_urdu = Faker\Factory::create('pk_UR'); // create a French faker

        $count = $this->argument('count');
        for ($ix=0; $ix < $count; $ix++) { 

            $product_ids = [];
            $warehouse_ids = [];
            $quantity = [];
            $saleprice = [];
            $stype = [];

            $invoice_length = $faker->randomNumber(2);
            for ($i=0; $i < $invoice_length; $i++) { 
                # code...
                $product_ids[] = Products::all()->random()->id;
                $warehouse_ids[] = Warehouse::all()->random()->id;
                $quantity[] = $faker->randomNumber(2)+1;
                $saleprice[] = $faker->randomNumber(3);
                $stype[] = $faker->randomElement(["sale"]); 
            }
            
           
            
            $invoice = new Invoice();
            $invoice->description = $faker->realText;
            $invoice->shipping = $faker->numberBetween(0, 120000);
            $invoice->customer_id = Customer::all()->random()->id;
            $invoice->discount = $faker->numberBetween(0,10);
            $invoice->total = 0;
            $invoice->type="sale";
            $invoice->bill_number = $faker->randomNumber();
            $invoice->date = $faker->dateTimeBetween("-1 years")->format('Y-m-d');
            $invoice->added_by = User::all()->random()->id;
            $invoice->save();

            $invoice_id = $invoice->id;

            echo $invoice_id.PHP_EOL;
            

            $worth = 0;

            foreach ($product_ids as $key => $value) {
                
                $order = new Order();
                $order->invoice_id = $invoice_id;
                $order->product_id = $product_ids[$key];
                $order->salePrice = $saleprice[$key];
                $order->quantity = $quantity[$key];
                $order->save();

                $sp_type = "sale";

                if($order->quantity < 0)
                {
                    $sp_type = "in";
                }


                    $worth += $order->salePrice*$order->quantity;



                //update rate for user
                $rate = Rates::FirstOrNew(['customer_id'=>$invoice->customer_id, 'product_id'=>$value]);
                $rate->salePrice = $saleprice[$key];
                $rate->save();
                //end update rate for user
                if (strcasecmp($stype[$key] ,"damage") != 0)
                {
                    $sale = new StockManage();
                    $sale->date = $invoice->date;
                    $sale->type = $sp_type;
                    $sale->customer_id = $invoice->customer_id;
                    $sale->sale_id = $order->id;
                    $sale->product_id = $product_ids[$key];
                    $sale->warehouse_id = $warehouse_ids[$key];
                    $sale->quantity = abs($quantity[$key]);
                    $sale->added_by = User::all()->random()->id;
                    //Stock Change Disabled by Ali Shan on 04 Sep 2019
                    //Stock Change Enabled by Ali Shan on 23 Dec 2019
                    $sale->save();
                }
                

            }//endforeach here

            if(!$invoice->shipping)
                { $invoice->shipping = 0;}
            if(!$invoice->tax)
                { $invoice->tax = 0;}
            if(!$invoice->discount)
                { $invoice->discount = 0;}

            $worth += $invoice->shipping + $invoice->tax - $invoice->discount;

            $invoice->total = $worth;
            $invoice->save();

// \Log::info("T".$worth);
            $transaction = new Transaction;
            $transaction->date = $invoice->date;
            $transaction->customer_id = $invoice->customer_id;
            $transaction->amount = abs($worth);
            $transaction->type = ($worth>0)?"out":"in";//credit
            $transaction->invoice_id = $invoice_id;
            $transaction->added_by = User::all()->random()->id;
            $transaction->save();

            if ($faker->boolean){
                    $transactions = new Transaction;
                    $transactions->date = $invoice->date;
                    $transactions->type = "in";
                    $transactions->invoice_id = $invoice->id;
                    $transactions->amount = $faker->randomNumber(3);
                    $transactions->payment_type = "cash";
                    $transactions->customer_id = $invoice->customer_id;
                    $transactions->added_by = User::all()->random()->id;
                    $transactions->save();

                }else if ($faker->boolean) //mark paid
            {

                $transactions = new Transaction;
                $transactions->date = $invoice->date;
                $transactions->type = ($worth>0)?"in":"out";//debit
                $transactions->invoice_id = $invoice->id;
                $transactions->amount = abs($worth);
                $transactions->payment_type = "cash";
                $transactions->customer_id = $invoice->customer_id;
                $transactions->added_by = User::all()->random()->id;
                $transactions->save();

            }   //end 


           }    //end for     
        
        
    }//end function
}