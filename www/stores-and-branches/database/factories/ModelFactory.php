<?php
use App\Store;

$factory->define(Store::class, function (Faker\Generator $faker) {
  return [
    'name' => $faker->name
  ];
});
