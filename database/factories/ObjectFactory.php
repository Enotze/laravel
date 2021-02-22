<?php

/** @var Factory $factory */

use App\Models\ObjectRecord;
use App\Models\ObjectType;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Str;

$factory->define(ObjectRecord::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'code' => Str::random(10),
        'object_type_id' => factory(ObjectType::class)->create()->getAttribute('id'),
        'level' => ObjectRecord::LVL_1,
    ];
});
