<?php

namespace App\Models;

use App\Models\Traits\PrimaryAsUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use PrimaryAsUuid;

    public $incrementing = false;

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = ['name','bio','born_at'];

    protected $casts = [
        'born_at' => 'datetime:Y-m-d'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed|null
     */
    public function getFavouriteGenreAttribute()
    {
        $movie = $this->movies()
            ->getQuery()
            ->select(
                'genre_id',
                \DB::raw('count(*) as total')
            )
            ->groupBy('genre_id')
            ->limit(1)
            ->first();
        return $movie->genre ?? null;
    }

    /**
     * @return Collection
     */
    public function getMoviesByGenreAttribute()
    {
        /** @var Collection $movies */
        $movies = $this->movies()
            ->getQuery()
            ->select(
                'genre_id',
                \DB::raw('count(*) as total')
            )
            ->groupBy('genre_id')
            ->get();

        return $movies->mapWithKeys(function ($movie) {
            return [$movie->genre->name => (int) $movie->total];
        });

    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function movies()
    {
        return $this->hasManyThrough(
            Movie::class,
            Appearance::class,
            'actor_id',
            'id',
            'id',
            'movie_id'
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function appearances()
    {
        return $this->belongsToMany(Movie::class, 'movie_actor')
            ->as('appearance')
            ->withPivot('id','role')
            ->using(Appearance::class)
            ->withTimestamps();
    }

    /**
     * @param Genre $genre
     * @return Builder
     */
    static function getActorsInGenre(Genre $genre)
    {
        return self::query()->whereHas('appearances', function (Builder  $query) use ($genre) {
            $query->where('genre_id','=', $genre->id);
        })
            ->withCount('appearances')
            ->orderBy('appearances_count','desc')
            ->groupBy('id');
    }
}
