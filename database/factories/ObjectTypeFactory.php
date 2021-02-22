<?php

/** @var Factory $factory */

use App\Models\ObjectType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(ObjectType::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'code' => Str::random(10),
    ];
});
