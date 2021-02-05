<?php


namespace Tests\Feature\Models;


use App\Models\Genre;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class MovieTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreate()
    {
        $genre = factory(Genre::class)->create();

        /** @var Movie $movie */
        $movie = Movie::create([
            'name' => 'Test Movie',
            'year' => 2012,
            'synopsis' => 'Test Synopsis',
            'runtime' => 160,
            'released_at' => '2020-12-01',
            'cost' => 10,
            'genre_id' => $genre->id
        ]);

        $movie->refresh();

        $this->assertNotNull($movie->id);
        $this->assertIsString($movie->id);
        $this->assertEquals('Test Movie',$movie->name);
        $this->assertIsString($movie->name);
        $this->assertEquals('Test Synopsis',$movie->synopsis);
        $this->assertIsString($movie->synopsis);
        $this->assertEquals('2012',$movie->year);
        $this->assertEquals(160,$movie->runtime);
        $this->assertInstanceOf(Carbon::class,$movie->released_at);
        $this->assertEquals(10,$movie->cost);
        $this->assertInstanceOf(Genre::class,$movie->genre);
        $this->assertEquals($genre->id,$movie->genre->id);

    }

    public function testUpdate()
    {
        /** @var Movie $movie */
        $movie = factory(Movie::class)->create([
            'genre_id' => factory(Genre::class)->create()->id
        ]);
        $genre = factory(Genre::class)->create([
            'name' => 'Prevent duplicated'
        ]);
        $data = [
            'name' => 'Tested Movie',
            'year' => 2014,
            'synopsis' => 'Tested Synopsis',
            'runtime' => 161,
            'released_at' => '2020-12-02',
            'cost' => 11,
            'genre_id' => $genre->id
        ];

        $movie->update($data);

        $this->assertEquals('Tested Movie',$movie->name);
        $this->assertEquals('Tested Synopsis',$movie->synopsis);
        $this->assertEquals(2014,$movie->year);
        $this->assertEquals(161,$movie->runtime);
        $this->assertEquals('2020-12-02',$movie->released_at->format('Y-m-d'));
        $this->assertEquals(11,$movie->cost);
        $this->assertEquals($genre->id,$movie->genre->id);

    }

    public function testRemove()
    {
        /** @var Movie $movie */
        $movie = factory(Movie::class)->create(['genre_id' => factory(Genre::class)->create()->id]);

        $this->assertEquals(1, Movie::all()->count());

        $movie->delete();
        $this->assertEquals(0, Movie::all()->count());
    }
}
