<?php

namespace Modules\HumanResource\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\Permission;

class HumanResourceDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        
        $permissions = Permission::firstOrNew(['name'=>'access-hr']);
        $permissions->display_name = "Access Human Resource";
        $permissions->description = "Complete Access to Human Resource Management";
        $permissions->save();

        // $this->call("OthersTableSeeder");
    }
}
