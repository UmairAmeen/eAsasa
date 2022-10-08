<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
 use Faker\Generator as Faker;
use Illuminate\Support\Str;

class AddCustomersForDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:customer {count}';

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
        // echo $count.PHP_EOL;
        for ($i=0; $i < $count; $i++) { 
            
            \App\Customer::insert( [
            'name' => $faker->name,
            'phone' => $faker->e164PhoneNumber,
            'type'=>'counter',
            'payment_notify'=>true,
            'after_last_payment'=>10,
            'city'=>$faker->city,
            'notes'=>$faker->address
        ]);
        }
        return true;
        
        
    }
}
