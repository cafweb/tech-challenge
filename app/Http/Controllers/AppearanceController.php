<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasFetchAllRenderCapabilities;
use App\Http\Requests\AppearanceRequest;
use App\Http\Resources\AppearanceResource;
use App\Models\Actor;
use App\Models\Appearance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class AppearanceController extends Controller
{

    use HasFetchAllRenderCapabilities;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request, Actor $actor)
    {
        $this->setGetAllBuilder($actor->appearances()->getQuery());
        $this->setGetAllOrdering('created_at', 'asc');
        $this->parseRequestConditions($request);
        return AppearanceResource::collection($this->getAll()->paginate());
    }

    /**
     * @param AppearanceRequest $request
     * @param Actor $actor
     * @return \Illuminate\Http\Response
     */
    public function store(AppearanceRequest $request, Actor $actor)
    {
        $validatedData = $request->validated();
        $actor->appearances()->attach($validatedData['movie_id'],[
            'role' => $validatedData['role']
        ]);

        return response()->noContent();
    }

    /**
     * @param Appearance $appearance
     * @return AppearanceResource
     */
    public function show(Appearance $appearance)
    {
        return new AppearanceResource($appearance);
    }

    /**
     * @param AppearanceRequest $request
     * @param Appearance $appearance
     * @return AppearanceResource
     */
    public function update(AppearanceRequest $request, Appearance $appearance)
    {
        $appearance->update($request->validated());

        return $this->show($appearance);
    }

    /**
     * @param Appearance $appearance
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appearance $appearance)
    {
        $appearance->delete();

        return response()->noContent();
    }
}
