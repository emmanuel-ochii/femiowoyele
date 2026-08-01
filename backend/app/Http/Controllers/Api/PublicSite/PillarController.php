<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\PillarResource;
use App\Models\Pillar;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PillarController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PillarResource::collection(Pillar::orderBy('order')->get());
    }

    public function show(Pillar $pillar): PillarResource
    {
        return new PillarResource($pillar->load(['projects', 'articles.category', 'mediaItems']));
    }
}
