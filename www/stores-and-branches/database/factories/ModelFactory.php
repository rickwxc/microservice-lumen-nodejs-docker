<?php
use App\Store;

$factory->define(Store::class, function (Faker\Generator $faker) {
  return [
    'name' => $faker->name
  ];
});

$factory->state(Store::class, 'pending_delete', [
  'status' => Store::PENDING_DELETE,
]);


