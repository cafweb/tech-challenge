<?php


namespace Tests\Unit\Models;


use App\Models\Genre;
use App\Models\Traits\PrimaryAsUuid;
use Tests\TestCase;

class GenreUnitTest extends TestCase
{
    public function testFillableAttribute()
    {
        $fillable = ['name'];
        $genre = new Genre();
        $this->assertEquals($fillable, $genre->getFillable());
    }

    public function testIfUseUuidTrait()
    {
        $this->assertContains(PrimaryAsUuid::class,class_uses(Genre::class));
    }
}
