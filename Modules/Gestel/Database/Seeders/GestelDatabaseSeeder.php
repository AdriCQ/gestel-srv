<?php

namespace Modules\Gestel\Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Modules\Gestel\Entities\Cargo;
use Modules\Gestel\Entities\Departamento;
use Modules\Gestel\Entities\Entidad;
use Modules\Gestel\Entities\Lugar;
use Modules\Gestel\Entities\Tel;

class GestelDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // Model::unguard();
    $this->seedEntidades();
    $this->seedLugares();
    $this->seedDepartamentos(10, 5);
    $this->seedCargos(10, 10);
    $this->seedTels(10, 10);
  }
  /**
   * seedCargos
   */
  private function seedCargos(int $limit = 10, int $repeats = 1)
  {
    $faker = Factory::create();
    for ($l = 0; $l < $limit; $l++) {
      $data = [];
      for ($r = 0; $r < $repeats; $r++) {
        array_push($data, [
          'nombre' => strtoupper($faker->name()),
          'dep_id' => $faker->numberBetween(1, Departamento::query()->count()),
        ]);
      }
      Cargo::query()->insert($data);
    }
  }
  /**
   * seedDepartamentos
   */
  private function seedDepartamentos(int $limit = 10, int $repeats = 1)
  {
    $faker = Factory::create();
    for ($l = 0; $l < $limit; $l++) {
      $data = [];
      for ($r = 0; $r < $repeats; $r++) {
        array_push($data, [
          'nombre' =>  strtoupper($faker->words(4, true)),
          'entidad_id' => $faker->numberBetween(1, Entidad::query()->count()),
          'lugar_id' => $faker->numberBetween(1, Lugar::query()->count()),
        ]);
      }
      Departamento::query()->insert($data);
    }
  }
  /**
   * seedEntidades
   */
  private function seedEntidades()
  {
    $faker = Factory::create();
    $data = [];
    for ($i = 0; $i < 10; $i++) {
      array_push($data, [
        'nombre' => strtoupper($faker->word()),
        'tipo' => 'MININT'
      ]);
    }
    Entidad::query()->insert($data);
  }
  /**
   * seedLugares
   */
  private function seedLugares()
  {
    $lugares = [
      'Cienfuegos', 'Palmira', 'Abreus', 'Aguadas', 'Rodas', 'Cruces', 'Lajas', 'Cumanayagua'
    ];
    $db = [];
    foreach ($lugares as $l) {
      array_push($db, ['nombre' => strtoupper($l)]);
    }
    Lugar::query()->insert($db);
  }
  /**
   * seedTels
   */
  private function seedTels(int $limit = 10, int $repeats  = 1)
  {
    $faker = Factory::create();
    for ($l = 0; $l < $limit; $l++) {
      $data = [];
      for ($r = 0; $r < $repeats; $r++) {
        array_push($data, [
          'cargo_id' => $faker->numberBetween(1, Cargo::query()->count()),
          'tel' => $faker->phoneNumber(),
          'servicio' => $faker->randomElement(['AUTOMATICO', 'EXTENSION']),
          'tipo' => $faker->randomElement(['PRIVADO', 'PUBLICO']),
          'presupuesto' => $faker->randomFloat(2, 1, 1000),
          'config' => json_encode([
            'comprado' => $faker->boolean,
            'entregado' => $faker->boolean
          ])
        ]);
      }
      Tel::query()->insert($data);
    }
  }
}
