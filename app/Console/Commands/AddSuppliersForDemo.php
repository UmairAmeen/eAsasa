<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
 use Faker\Generator as Faker;
use Illuminate\Support\Str;

class AddSuppliersForDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:supplier {count}';

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
            
            \App\Supplier::insert( [
            'name' => $faker->name,
            'phone' => $faker->e164PhoneNumber,
            // 'city'=>$faker->city,
            'address'=>$faker->address
        ]);
        }
        
        
    }
}
