<?php


namespace Tests\Feature\Http\Controllers;


use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Tests\TestCase;

class MovieControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testList()
    {
        $response = $this->json('get', route('movies.index'));

        $response->assertSuccessful()
            ->assertJsonFragment(['total'=>50])
            ->assertJsonCount(15, 'data')
            ->assertSee('id')
            ->assertSee('name')
            ;

        $movie = Movie::query()->orderBy('name','asc')->get()->first();
        foreach ($movie->makeHidden('genre_id')->toArray() as $attribute => $value) {
            $response->assertJsonFragment([$attribute => $value]);
        }

        $response->assertJsonFragment(['id'=>$movie->genre->id]);
    }

    public function testShow()
    {
        $movie = Movie::inRandomOrder()->first();
        $response = $this->json('get', route('movies.show',[$movie]));

        $response->assertSuccessful()
            ->assertSee('id')
            ->assertSee('name')
            ;

        foreach ($movie->makeHidden('genre_id')->toArray() as $attribute => $value) {
            $response->assertJsonFragment([$attribute => $value]);
        }
        $response->assertJsonFragment(['id'=>$movie->genre->id]);
    }

    public function testInvalidationData()
    {
        $response = $this->json('post', route('movies.store'),[]);
        $fields = [
            "year",
            "synopsis",
            "runtime",
            "released_at",
            "cost",
            "genre_id",
        ];

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $response->assertJsonFragment([
                \Lang::get('validation.required',['attribute'=>str_replace('_',' ', $field)])
            ]);
        }

        $response = $this->json('post', route('movies.store'), [
            'name' => 'ac'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields)
            ->assertJsonFragment([
                \Lang::get('validation.min.string',['attribute'=>'name','min'=> 3])
            ]);

        $movie = Movie::inRandomOrder()->first();

        $response = $this->json('put', route('movies.update',[$movie]), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $response->assertJsonFragment([
                \Lang::get('validation.required',['attribute'=>str_replace('_',' ', $field)])
            ]);
        }

    }

    public function testStore()
    {
        $genre =  Genre::inRandomOrder()->first();
        $response = $this->json('post', route('movies.store'), [
            'name' => "Test",
            'year' => 2012,
            'synopsis' => "Test Synopsis",
            'runtime' => 222,
            'released_at' => '2012-10-01',
            'cost' => 10,
            'genre_id' => $genre->id
        ]);

        $id = $response->json('data.id');

        $movie = Movie::find($id);

        $response->assertSuccessful()
            ->assertSee('created_at')
            ->assertSee('updated_at')
            ->assertJsonFragment([
                'name' => "Test",
                'year' => 2012,
                'synopsis' => "Test Synopsis",
                'runtime' => 222,
                'released_at' => '2012-10-01',
                'cost' => 10,
            ])
            ->assertJsonPath('data.genre.id',$genre->id)
        ;

        $this->assertEquals($id, $movie->id);
        $this->assertIsString($response->json('data.id'));
        $this->assertIsString($response->json('data.name'));
        $this->assertIsString($response->json('data.synopsis'));
        $this->assertIsInt($response->json('data.runtime'));
        $this->assertIsInt($response->json('data.cost'));
        $this->assertIsString($response->json('data.created_at'));
        $this->assertIsString($response->json('data.updated_at'));
        $this->assertNotNull($response->json('data.created_at'));
        $this->assertNotNull($response->json('data.updated_at'));

    }

    public function testUpdate()
    {
        $movie = Movie::inRandomOrder()->first();

        $response = $this->json('put', route('movies.update',[$movie]), [
            'name' => "Test Update",
            'year' => 2013,
            'synopsis' => "Test Update Synopsis",
            'runtime' => 200,
            'released_at' => '2013-10-01',
            'cost' => 100,
            'genre_id' => $movie->genre->id
        ]);

        $response->assertSuccessful()
            ->assertSee('created_at')
            ->assertSee('updated_at')
            ->assertJsonFragment([
                'name' => "Test Update",
                'year' => 2013,
                'synopsis' => "Test Update Synopsis",
                'runtime' => 200,
                'released_at' => '2013-10-01',
                'cost' => 100,
            ]);

        $this->assertIsString($response->json('data.id'));
        $this->assertIsString($response->json('data.name'));
        $this->assertIsString($response->json('data.synopsis'));
        $this->assertIsInt($response->json('data.runtime'));
        $this->assertIsInt($response->json('data.cost'));
        $this->assertIsString($response->json('data.created_at'));
        $this->assertIsString($response->json('data.updated_at'));
        $this->assertNotNull($response->json('data.created_at'));
        $this->assertNotNull($response->json('data.updated_at'));
        $this->assertNotEquals($response->json('data.updated_at'),$movie->updated_at);

    }
    public function testDelete()
    {
        $movie = Movie::inRandomOrder()->first();

        $response = $this->json('delete', route('movies.destroy',[$movie]));

        $response
            ->assertDontSee('id')
            ->assertNoContent();

    }
}
