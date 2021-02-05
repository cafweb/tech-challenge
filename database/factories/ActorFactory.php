<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models;
use Faker\Generator as Faker;

$factory->define(Models\Actor::class, function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->name,
        'bio' =>$faker->sentence,
        'born_at' =>$faker->date('Y-m-d', \Carbon\Carbon::now()->subYears(18)->format('Y-m-d')),
    ];
});
