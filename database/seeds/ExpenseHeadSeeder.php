<?php

use Illuminate\Database\Seeder;
use App\ExpenseHead;

class ExpenseHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	if (!ExpenseHead::whereId(1)->first())
        {
            $po = new ExpenseHead();
            $po->id = 1;
            $po->name = "Misc";
        	$po->deleteable = false;
            $po->save();
        }
    }
}
