<?php
use App\Store;

$factory->define(Store::class, function (Faker\Generator $faker) {
  return [
    'name' => $faker->name,
    'status' => Store::ACTIVE,
    'has_branch' => false
  ];
});

