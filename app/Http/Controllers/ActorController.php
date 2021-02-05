<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasFetchAllRenderCapabilities;
use App\Http\Requests\ActorRequest;
use App\Http\Resources\ActorResource;
use App\Models\Actor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ActorController extends Controller
{

    use HasFetchAllRenderCapabilities;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $this->setGetAllBuilder(Actor::query());
        $this->setGetAllOrdering('name', 'asc');
        $this->parseRequestConditions($request);
        return ActorResource::collection($this->getAll()->paginate());
    }

    /**
     * @param ActorRequest $request
     * @return ActorResource
     */
    public function store(ActorRequest $request)
    {
        $actor = Actor::create($request->validated());

        return $this->show($actor);
    }

    /**
     * @param Actor $actor
     * @return ActorResource
     */
    public function show(Actor $actor)
    {
        return new ActorResource($actor);
    }

    /**
     * @param Actor $actor
     * @param ActorRequest $request
     * @return ActorResource
     */
    public function update(ActorRequest $request, Actor $actor)
    {
        $actor->update($request->validated());

        return $this->show($actor);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Actor $actor
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Actor $actor)
    {
        $actor->delete();

        return response()->noContent();
    }
}
