<?php

use Illuminate\Database\Seeder;
use App\Warehouse;


class WarehouseTableSeeder extends Seeder {

    public function run()
    {
        if (!Warehouse::first())
        {
        	$Warehouse = new Warehouse();
        	$Warehouse->name = "Default";
        	$Warehouse->save();
        }
    }

}