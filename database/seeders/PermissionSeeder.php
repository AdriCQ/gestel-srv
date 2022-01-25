<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
  // private  $BASE_PERMISSIONS = ['list', 'get'];
  // private  $ADVANCE_PERMISSIONS = ['create', 'update', 'destroy'];
  // private  $ALL_PERMISSIONS = ['list', 'get', 'create', 'update', 'destroy'];

  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    app()[PermissionRegistrar::class]->forgetCachedPermissions();

    /**
     * -----------------------------------------
     *	Roles
     * -----------------------------------------
     */

    // Developer
    Role::create(['name' => 'DEVELOPER']);
    // Admin
    Role::create(['name' => 'ADMIN']);
    // Guest
    Role::create(['name' => 'GUEST']);
    // Vendor
  }
}
