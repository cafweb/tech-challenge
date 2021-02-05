<?php


namespace Tests\Unit\Models;


use App\Models\Movie;
use App\Models\Traits\PrimaryAsUuid;
use Tests\TestCase;

class MovieUnitTest extends TestCase
{
    public function testFillableAttribute()
    {
        $fillable = ['name','year','synopsis','runtime','released_at','cost','genre_id'];
        $movie = new Movie();
        $this->assertEquals($fillable, $movie->getFillable());
    }

    public function testIfUseUuidTrait()
    {
        $this->assertContains(PrimaryAsUuid::class,class_uses(Movie::class));
    }

    public function testCasts()
    {
        $casts = [
            'runtime' => 'integer',
            'cost' => 'integer',
            'year' => 'integer',
            'released_at' => 'datetime:Y-m-d'
        ];

        $movie = new Movie();
        $modelCasts = $movie->getCasts();

        foreach ($casts as $cast) {
            $this->assertContains($cast,$modelCasts);
        }

        $this->assertCount(count($casts),$modelCasts);
    }
}
