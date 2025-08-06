<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccountManagerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $adminPermission = [
         'user',
         'user:create',
         'user:edit',
         'user:show',
         'user:import',
         'user:export',
         'user:delete',
         'user:login tracking',
         'user:direct email sent',
          
         'event',
         'event:create',
         'event:edit',
         'event:show',
         'event:delete',

         'session',
         'session:create',
         'session:edit',
         'session:show',
         'session:delete',



         'role',
         'role:create',
         'role:edit',
         'role:delete',

         'permission',
         'permission:create',
         'permission:edit',
         'permission:delete',

         'page',
         'page:create',
         'page:edit',
         'page:delete',

         'menu',
         'menu:create',
         'menu:edit',
         'menu:delete',
        

         'speaker',
         'speaker:create',
         'speaker:edit',
         'speaker:delete',

         'media library',
         'media library:create',
         'media library:edit',
         'media library:delete',

         'newsletter',
         'newsletter:create',
         'newsletter:edit',
         'newsletter:delete',

         'survey',
         'survey:create',
         'survey:edit',
         'survey:delete',
        ];

        foreach ($adminPermission  as $permissionName) {
         Permission::create(['name' => $permissionName, 'guard_name'=>'web']);
        }

        $adminRole = Role::where('name', 'Admin')->first(); 
        $adminRole->givePermissionTo($adminPermission);

        $adminEventRole = Role::where('name', 'Event Admin')->first(); 
        $adminEventRole->givePermissionTo($adminPermission);


    }
}
