<?php


namespace Tests\Feature\Models;


use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreate()
    {
        $genre = Genre::create([
            'name' => 'Action'
        ]);

        $genre->refresh();

        $this->assertNotNull($genre->id);
        $this->assertIsString($genre->id);
        $this->assertEquals('Action',$genre->name);
        $this->assertEquals(0,$genre->movies()->count());
        $this->assertEquals(0,$genre->actors()->count());

        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();
        $genre->movies()->saveMany(factory(Movie::class,3)->create());
        $this->assertEquals(3,$genre->movies()->count());
    }

    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();

        $data = [
            'name' => 'Test update'
        ];

        $genre->update($data);

        $this->assertEquals('Test update',$genre->name);

    }

    public function testRemove()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create();

        $this->assertEquals(1, Genre::all()->count());

        $genre->delete();
        $this->assertEquals(0, Genre::all()->count());
    }
}
