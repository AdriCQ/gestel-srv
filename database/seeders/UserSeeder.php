<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $user = new User([
      'nombre' => 'Administrator',
      'nick' => 'admin',
      'password' => bcrypt('c7ad44cbad762a5da0a452f9e854fdc1e0e7a52a38015f23f3eab1d80b931dd472634dfac71cd34ebc35d16ab7fb8a90c81f975113d6c7538dc69dd8de9077ec'),
      'created_at' => Carbon::now()
    ]);
    $user->save();
    $user->assignRole('DEVELOPER');
  }
}
