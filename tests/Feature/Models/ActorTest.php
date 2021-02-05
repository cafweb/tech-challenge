<?php


namespace Tests\Feature\Models;


use App\Models\Actor;
use App\Models\Genre;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ActorTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreate()
    {
        $actor = Actor::create([
            'name' => 'Actor Test',
            'bio' => 'Bio Test',
            'born_at' => '1953-02-03'
        ]);

        $actor->refresh();

        $this->assertNotNull($actor->id);
        $this->assertIsString($actor->id);
        $this->assertEquals('Actor Test',$actor->name);
        $this->assertEquals('Bio Test',$actor->bio);
        $this->assertInstanceOf(Carbon::class,$actor->born_at);
        $this->assertEquals('1953-02-03',$actor->born_at->format('Y-m-d'));
        $this->assertEquals(0,$actor->movies()->count());

        /** @var Actor $actor */
        $actor = factory(Actor::class)->create();
        $appearances = [
           [
               'movie' => factory(Movie::class)->create(['genre_id'=>factory(Genre::class)->create()->id]),
               'role' => 'Role A'
           ],
           [
               'movie' => factory(Movie::class)->create(['genre_id'=>factory(Genre::class)->create()->id]),
               'role' => 'Role B'
           ],
           [
               'movie' => factory(Movie::class)->create(['genre_id'=>factory(Genre::class)->create()->id]),
               'role' => 'Role C'
           ],
        ];
        foreach ($appearances as $appearance) {
            $actor->appearances()->attach($appearance['movie']->id,['role'=>$appearance['role']]);
        }
        $this->assertEquals(3,$actor->appearances()->count());
        $this->assertEquals(1,Actor::getActorsInGenre(Genre::inRandomOrder()->get()->first())->count());
    }

    public function testUpdate()
    {
        /** @var Actor $actor */
        $actor = factory(Actor::class)->create();

        $data = [
            'name' => 'Actor Test',
            'bio' => 'Bio Test',
            'born_at' => '1953-02-03'
        ];

        $actor->update($data);

        $this->assertNotNull($actor->id);
        $this->assertIsString($actor->id);
        $this->assertEquals('Actor Test',$actor->name);
        $this->assertEquals('Bio Test',$actor->bio);
        $this->assertInstanceOf(Carbon::class,$actor->born_at);
        $this->assertEquals('1953-02-03',$actor->born_at->format('Y-m-d'));
        $this->assertEquals(0,$actor->movies()->count());

    }

    public function testRemove()
    {
        /** @var Actor $actor */
        $actor = factory(Actor::class)->create();

        $this->assertEquals(1, Actor::all()->count());

        $actor->delete();
        $this->assertEquals(0, Actor::all()->count());
    }
}
