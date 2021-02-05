<?php

use Illuminate\Database\Seeder;

class MovieSeeder extends Seeder
{
    public function run()
    {
        factory(\App\Models\Movie::class,50)->create();
    }
}
