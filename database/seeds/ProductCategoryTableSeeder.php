<?php

use Illuminate\Database\Seeder;
use App\ProductCategory;

// composer require laracasts/testdummy
use Laracasts\TestDummy\Factory as TestDummy;

class ProductCategoryTableSeeder extends Seeder {

    public function run()
    {
        $p = ProductCategory::firstOrNew(['id'=>1]);
        $p->name = "Uncategorized";
        $p->is_active = true;
        $p->description = "Auto Generated Category";
        $p->save();
    
        // TestDummy::times(20)->create('App\Post');
    }

}