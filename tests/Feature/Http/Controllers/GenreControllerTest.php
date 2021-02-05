<?php


namespace Tests\Feature\Http\Controllers;


use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testList()
    {
        $response = $this->json('get', route('genres.index'));

        $response->assertSuccessful()
            ->assertJsonFragment(['total'=>4])
            ->assertJsonCount(4, 'data')
            ->assertSee('id')
            ->assertSee('name')
            ;

        foreach (Genre::first()->toArray() as $attribute => $value) {
            $response->assertJsonFragment([$attribute => $value]);
        }
    }

    public function testShow()
    {
        $genre = Genre::inRandomOrder()->first();
        $response = $this->json('get', route('genres.show',[$genre]));

        $response->assertSuccessful()
            ->assertSee('id')
            ->assertSee('name')
            ;

        foreach ($genre->toArray() as $attribute => $value) {
            $response->assertJsonFragment([$attribute => $value]);
        }
    }

    public function testInvalidationData()
    {
        $response = $this->json('post', route('genres.store'),[]);
        $fields = [
            'name'
        ];

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $response->assertJsonFragment([
                \Lang::get('validation.required',['attribute'=>$field])
            ]);
        }

        $response = $this->json('post', route('genres.store'), [
            'name' => 'action'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields)
            ->assertJsonFragment([
                \Lang::get('validation.unique',['attribute'=>$field])
            ]);

        $genre = Genre::inRandomOrder()->first();

        $response = $this->json('put', route('genres.update',[$genre]), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $response->assertJsonFragment([
                \Lang::get('validation.required',['attribute'=>$field])
            ]);
        }
        $response = $this->json('put', route('genres.update',[$genre]), [
            'name' => 'horror'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields)
            ->assertJsonFragment([
                \Lang::get('validation.unique',['attribute'=>$field])
            ]);
    }

    public function testStore()
    {
        $response = $this->json('post', route('genres.store'), ['name'=>'romance']);
        $id = $response->json('data.id');

        $genre = Genre::find($id);

        $response->assertSuccessful()
            ->assertSee('created_at')
            ->assertSee('updated_at')
            ->assertJsonFragment([
                'name' => 'romance'
            ]);

        $this->assertEquals($id, $genre->id);
        $this->assertIsString($response->json('data.id'));
        $this->assertIsString($response->json('data.name'));
        $this->assertIsString($response->json('data.created_at'));
        $this->assertIsString($response->json('data.updated_at'));
        $this->assertNotNull($response->json('data.created_at'));
        $this->assertNotNull($response->json('data.updated_at'));

    }

    public function testUpdate()
    {
        $genre = Genre::inRandomOrder()->first();

        $response = $this->json('put', route('genres.update',[$genre]), ['name'=>'romance']);

        $response->assertSuccessful()
            ->assertSee('created_at')
            ->assertSee('updated_at')
            ->assertJsonFragment([
                'name' => 'romance'
            ]);

        $this->assertIsString($response->json('data.id'));
        $this->assertIsString($response->json('data.name'));
        $this->assertIsString($response->json('data.created_at'));
        $this->assertIsString($response->json('data.updated_at'));
        $this->assertNotNull($response->json('data.created_at'));
        $this->assertNotNull($response->json('data.updated_at'));
        $this->assertNotEquals($response->json('data.updated_at'),$genre->updated_at);

    }
    public function testDelete()
    {
        $genre = Genre::inRandomOrder()->first();

        $response = $this->json('delete', route('genres.destroy',[$genre]));

        $response
            ->assertStatus(422)
            ->assertJsonFragment(['This genre can\'t be removed because it has linked movies.'])
        ;

        $genre = factory(Genre::class)->create();

        $response = $this->json('delete', route('genres.destroy',[$genre]));

        $response
            ->assertNoContent();

    }

    public function testListActorsInGenre()
    {
        $genre = Genre::inRandomOrder()->first();

        $response = $this->json('get', route('genres.actors',[$genre]));

        $response->assertSuccessful()
            ->assertSee('id')
            ->assertSee('name')
            ->assertSee('bio')
            ->assertSee('born_at')
            ->assertJsonFragment(['total'=>$genre->actors()->get()->count()])
            ->assertJsonCount($genre->actors()->get()->count() < 15 ? $genre->actors()->get()->count() : 15 , 'data')
        ;
    }
}
