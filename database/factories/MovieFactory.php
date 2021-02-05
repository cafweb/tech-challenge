<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models;
use Faker\Generator as Faker;

$factory->define(Models\Movie::class, function (Faker $faker) {
    return [
        'id' => $faker->uuid,
        'name' => $faker->words(rand(3,6), true),
        'year' =>$faker->year("now"),
        'synopsis' =>$faker->sentence,
        'runtime' =>$faker->numberBetween(60,200),
        'released_at' => $faker->date('Y-m-d', \Carbon\Carbon::now()->format('Y-m-d')),
        'cost' =>$faker->numberBetween(10000,9000000000),
        'genre_id' => Models\Genre::inRandomOrder()->limit(1)->first()->id
    ];
});
