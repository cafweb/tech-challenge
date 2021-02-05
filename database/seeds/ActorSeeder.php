<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;


class ActorSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        factory(\App\Models\Actor::class,50)->create()->each(function (\App\Models\Actor $actor) use ($faker) {

            for ($i = 0; $i < rand(1,6); $i++) {
                $actor->appearances()->attach(\App\Models\Movie::inRandomOrder()->limit(1)->first()->id,[
                    'role' => $faker->jobTitle
                ]);
            }
        });
    }
}
