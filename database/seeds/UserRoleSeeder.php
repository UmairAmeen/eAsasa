<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;
use App\Permission;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$user = User::first();
        if (!empty($user))
        {
            $role = Role::first();
            if (!$role)
            {
                $role = new Role();
                $role->name = "Admin";
                $role->display_name = "Admin";
                $role->description = "Admin";
                $role->save();
             
            }
    	
            try{
        	   $user->attachRole($role);
            }catch(\Exception $e)
            {
                return;
            }

        	$permissions = Permission::get();

        	if (!empty($permissions)) {
        		foreach ($permissions as $key => $value) {
		            $permission = Permission::whereId($value->id)->first();
		            $role->attachPermission($permission);
		        }
        		
        	}
        }
    }
}
