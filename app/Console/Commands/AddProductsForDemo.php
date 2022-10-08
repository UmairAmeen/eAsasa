<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
 use Faker\Generator as Faker;
use Illuminate\Support\Str;

class AddProductsForDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fake:products {count}';

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
        for ($i=0; $i < $count; $i++) { 
            
            \App\Products::insert( [
            'name' => $faker->productName,
            'barcode' => $faker->ean13,
            // 'translation'=>$faker_urdu->name,
            'brand'=>$faker->company,
            "notify"=>$faker->randomNumber(2),
            'type'=>"final",
            'salePrice'=>$faker->randomNumber(3),
            'min_sale_price'=>$faker->randomNumber(2),
            'added_by'=>\Auth::id(),
            // 'itemcode'=>$faker->deviceSerialNumber,
            // 'description'=>$faker->department
        ]);
        }
        
        
    }
}
