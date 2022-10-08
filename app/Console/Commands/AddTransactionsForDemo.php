<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
 use Faker\Generator as Faker;
use Illuminate\Support\Str;

class AddTransactionsForDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:transaction {count}';

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
        $count = $this->argument('count');
        for ($i=0; $i < $count; $i++) { 



                    $transactions = new \App\Transaction;
                    $transactions->date = $faker->dateTimeBetween("-1 years")->format('Y-m-d');

                    if ($faker->boolean)
                    {
                        //for suppliers
                        $transactions->type = "out";
                        $transactions->supplier_id = \App\Supplier::all()->random()->id;
                    }else{
                        //for customers
                        $transactions->type = "in";
                        $transactions->customer_id = \App\Customer::all()->random()->id;
                    }
                    
                    // $transactions->invoice_id = $invoice->id;
                    $transactions->amount = $faker->randomNumber(5);
                    $transactions->payment_type = $faker->randomElement(["cash","cheque","online"]);
                    if ($transactions->payment_type == "cheque")
                    {
                        $transactions->release_date = $faker->dateTimeBetween("now","+ 6 months")->format('Y-m-d');
                    }
                    $transactions->transaction_id = $faker->swiftBicNumber;
                    $transactions->bank = $faker->city." Bank";
                    // $transactions->supplier_id = $invoice->supplier_id;
                    $transactions->added_by = \App\User::all()->random()->id;
                    $transactions->save();
        }
        
        
    }
}
