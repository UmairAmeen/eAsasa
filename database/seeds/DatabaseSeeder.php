<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(SettingTableSeeder::class);
        $this->call(WarehouseTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(UserRoleSeeder::class);
        $this->call(PermissionHolder::class);
        $this->call(ExpenseHeadSeeder::class);
        $this->call(ProductCategoryTableSeeder::class);
    }
}
