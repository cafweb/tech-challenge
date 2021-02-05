<?php


namespace Tests\Feature\Http\Controllers;


use App\Models\Actor;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ActorControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testList()
    {
        $response = $this->json('get', route('actors.index'));

        $response->assertSuccessful()
            ->assertJsonFragment(['total'=>50])
            ->assertJsonCount(15, 'data')
            ->assertSee('id')
            ->assertSee('name')
            ;

        $actor = Actor::query()->orderBy('name','asc')->get()->first();
        foreach ($actor->toArray() as $attribute => $value) {
            $response->assertJsonFragment([$attribute => $value]);
        }

    }

    public function testShow()
    {
        $actor = Actor::inRandomOrder()->first();
        $response = $this->json('get', route('actors.show',[$actor]));

        $response->assertSuccessful()
            ->assertSee('id')
            ->assertSee('name')
            ->assertSee('bio')
            ->assertSee('born_at')
            ->assertSee('favourite_genre')
            ->assertSee('movies_by_genre')
            ;

        foreach ($actor->toArray() as $attribute => $value) {
            $response->assertJsonFragment([$attribute => $value]);
        }

        foreach ($response->json('data.movies_by_genre') as $genre => $moviesTotal) {
            $this->assertIsString($genre);
            $this->assertIsInt($moviesTotal);
            $this->assertDatabaseHas('genres',['name'=>$genre]);
            $this->assertContains($genre,$actor->movies_by_genre->keys());
        }

        $this->assertDatabaseHas('genres',['id'=>$response->json('data.favourite_genre.id')]);
    }

    public function testInvalidationData()
    {
        $response = $this->json('post', route('actors.store'),[]);
        $fields = [
            "name",
            "born_at",
            "bio",
        ];

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $response->assertJsonFragment([
                \Lang::get('validation.required',['attribute'=>str_replace('_',' ', $field)])
            ]);
        }

        $response = $this->json('post', route('actors.store'), [
            'name' => 'ac'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields)
            ->assertJsonFragment([
                \Lang::get('validation.min.string',['attribute'=>'name','min'=> 3])
            ]);

        $actor = Actor::inRandomOrder()->first();

        $response = $this->json('put', route('actors.update',[$actor]), []);

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
        $response = $this->json('post', route('actors.store'), [
            'name' => "Test",
            'bio' => "Test Bio",
            'born_at' => '1950-10-01',
        ]);

        $id = $response->json('data.id');

        $actor = Actor::find($id);

        $response->assertSuccessful()
            ->assertSee('created_at')
            ->assertSee('updated_at')
            ->assertJsonFragment([
                'name' => "Test",
                'bio' => "Test Bio",
                'born_at' => '1950-10-01',
            ])
        ;


        $this->assertEquals($id, $actor->id);
        $this->assertIsString($response->json('data.id'));
        $this->assertIsString($response->json('data.name'));
        $this->assertIsString($response->json('data.bio'));
        $this->assertNull($response->json('data.favourite_genre'));
        $this->assertIsArray($response->json('data.movies_by_genre'));
        $this->assertIsString($response->json('data.born_at'));
        $this->assertIsString($response->json('data.created_at'));
        $this->assertIsString($response->json('data.updated_at'));
        $this->assertNotNull($response->json('data.created_at'));
        $this->assertNotNull($response->json('data.updated_at'));

    }

    public function testUpdate()
    {
        $actor = Actor::inRandomOrder()->first();

        $response = $this->json('put', route('actors.update',[$actor]), [
            'name' => "Test Update",
            'bio' => "Test Update Bio",
            'born_at' => '1951-10-01',
        ]);

        $response->assertSuccessful()
            ->assertSee('created_at')
            ->assertSee('updated_at')
            ->assertJsonFragment([
                'name' => "Test Update",
                'bio' => "Test Update Bio",
                'born_at' => '1951-10-01',
            ]);

        $this->assertIsString($response->json('data.id'));
        $this->assertIsString($response->json('data.name'));
        $this->assertIsString($response->json('data.bio'));
        $this->assertNotNull($response->json('data.favourite_genre'));
        $this->assertNotNull($response->json('data.movies_by_genre'));
        $this->assertIsString($response->json('data.born_at'));
        $this->assertIsString($response->json('data.created_at'));
        $this->assertIsString($response->json('data.updated_at'));
        $this->assertNotNull($response->json('data.created_at'));
        $this->assertNotNull($response->json('data.updated_at'));
        $this->assertNotEquals($response->json('data.updated_at'),$actor->updated_at);

    }
    public function testDelete()
    {
        $actor = Actor::inRandomOrder()->first();

        $response = $this->json('delete', route('actors.destroy',[$actor]));

        $response
            ->assertDontSee('id')
            ->assertNoContent();

    }
}
