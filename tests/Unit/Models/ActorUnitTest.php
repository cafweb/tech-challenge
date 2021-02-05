<?php


namespace Tests\Unit\Models;


use App\Models\Actor;
use App\Models\Traits\PrimaryAsUuid;
use Tests\TestCase;

class ActorUnitTest extends TestCase
{
    public function testFillableAttribute()
    {
        $fillable = ['name','bio','born_at'];
        $actor = new Actor();
        $this->assertEquals($fillable, $actor->getFillable());
    }

    public function testIfUseUuidTrait()
    {
        $this->assertContains(PrimaryAsUuid::class,class_uses(Actor::class));
    }

    public function testCasts()
    {
        $casts = [
            'born_at' => 'datetime:Y-m-d'
        ];

        $actor = new Actor();
        $modelCasts = $actor->getCasts();

        foreach ($casts as $cast) {
            $this->assertContains($cast,$modelCasts);
        }

        $this->assertCount(count($casts),$modelCasts);
    }
}
