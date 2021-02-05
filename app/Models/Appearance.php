<?php


namespace App\Models;


use App\Models\Traits\PrimaryAsUuid;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Appearance extends Pivot
{
    use PrimaryAsUuid;

    protected $table = 'movie_actor';

    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actor()
    {
        return $this->belongsTo(Actor::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOneThrough
     */
    public function genre()
    {
        return $this->hasOneThrough(Genre::class, Movie::class);
    }

}
