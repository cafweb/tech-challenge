<?php


namespace Tests\Feature\Http\Controllers;


use App\Models\Actor;
use App\Models\Movie;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class AppearanceControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
    }

    public function testList()
    {
        $actor = Actor::inRandomOrder()->first();
        $response = $this->json('get', route('actors.appearances.index',[$actor]));

        $response->assertSuccessful()
            ->assertJsonFragment(['total'=>$actor->appearances()->count()])
            ->assertJsonCount($actor->appearances()->count() < 15 ? $actor->appearances()->count() : 15, 'data')
            ->assertSee('id')
            ->assertSee('role')
            ->assertSee('movie')
            ;

        $movie = $actor->appearances()->orderBy('created_at','asc')->get()->first();

        $response->assertJsonFragment(['id' => $movie->appearance->id]);
        $response->assertJsonFragment(['role' => $movie->appearance->role]);

    }

    public function testShow()
    {
        $actor = Actor::inRandomOrder()->first();
        $movie = $actor->appearances()->inRandomOrder()->first();

        $response = $this->json('get', route('appearances.show',[$movie->appearance->id]));

        $response->assertSuccessful()
            ->assertSee('id')
            ->assertSee('role')
            ->assertSee('actor')
            ->assertSee('movie')
        ;

        $response->assertJsonFragment(['id' => $movie->appearance->id]);
        $response->assertJsonFragment(['role' => $movie->appearance->role]);
        $response->assertJsonFragment(['id' => $movie->id]);
        $response->assertJsonFragment(['id' => $actor->id]);

    }

    public function testInvalidationData()
    {
        $actor = Actor::inRandomOrder()->first();
        $response = $this->json('post', route('actors.appearances.store',[$actor]),[]);
        $fields = [
            "movie_id",
            "role",
        ];

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $response->assertJsonFragment([
                \Lang::get('validation.required',['attribute'=>str_replace('_',' ', $field)])
            ]);
        }

        $response = $this->json('post', route('actors.appearances.store',[$actor]), [
            'movie_id' => 'ac',
            'role' => 1
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields)
            ->assertJsonFragment([
                \Lang::get('validation.exists',['attribute'=>'movie id'])
            ])
            ->assertJsonFragment([
                \Lang::get('validation.string',['attribute'=>'role'])
            ]);


        $response = $this->json('post', route('actors.appearances.store',[$actor]), [
            'movie_id' => Movie::inRandomOrder()->first()->id,
            'role' => str_repeat ( 'a' , 256 )
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                \Lang::get('validation.max.string',['attribute'=>'role','max'=>255]),
            ]);


        $response = $this->json('put', route('appearances.update',[$actor->appearances()->first()->appearance->id]), []);

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
        $actor = Actor::inRandomOrder()->first();
        $movie = Movie::inRandomOrder()->first();
        $response = $this->json('post', route('actors.appearances.store',[$actor]), [
            'movie_id' => $movie->id,
            'role' => "Test Role",
        ]);

        $response->assertNoContent();

        $response = $this->json('get', route('actors.appearances.index',[$actor]));

        $response->assertSuccessful()
            ->assertJsonFragment([
                'id' => $movie->id,
                'role' => "Test Role"
            ])
        ;

        foreach ($response->json('data') as $appearance) {
            $this->assertIsString($appearance['id']);
            $this->assertIsString($appearance['role']);
            $this->assertIsArray($appearance['movie']);
            $this->assertIsString($appearance['created_at']);
            $this->assertIsString($appearance['updated_at']);
            $this->assertNotNull($appearance['created_at']);
            $this->assertNotNull($appearance['updated_at']);
        }


    }

    public function testUpdate()
    {
        $actor = Actor::inRandomOrder()->first();
        $movie = $actor->appearances()->inRandomOrder()->first();

        $response = $this->json('put', route('appearances.update',[$movie->appearance]), [
            'role' => "Test Role Updated",
            'movie_id' => $movie->id,
            'actor_id' => $actor->id,
        ]);

        $response->assertSuccessful()
            ->assertSee('created_at')
            ->assertSee('updated_at')
            ->assertSee('actor')
            ->assertJsonFragment([
                'role' => "Test Role Updated"
            ])
            ->assertJsonPath('data.movie.id',$movie->id)
            ->assertJsonPath('data.actor.id',$actor->id)
        ;

        $this->assertIsString($response->json('data.id'));
        $this->assertIsString($response->json('data.role'));
        $this->assertIsArray($response->json('data.actor'));
        $this->assertIsArray($response->json('data.movie'));
        $this->assertIsString($response->json('data.created_at'));
        $this->assertIsString($response->json('data.updated_at'));
        $this->assertNotNull($response->json('data.created_at'));
        $this->assertNotNull($response->json('data.updated_at'));
        $this->assertNotEquals($response->json('data.updated_at'),$actor->updated_at);

    }
    public function testDelete()
    {
        $actor = Actor::inRandomOrder()->first();
        $movie = $actor->appearances()->inRandomOrder()->first();

        $response = $this->json('delete', route('appearances.destroy',[$movie->appearance]));

        $response
            ->assertNoContent();

    }
}
