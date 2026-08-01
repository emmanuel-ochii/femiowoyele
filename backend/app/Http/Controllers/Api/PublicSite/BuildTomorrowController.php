<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentBlockResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\MediaItemResource;
use App\Models\ContentBlock;
use App\Models\Gallery;
use App\Models\MediaItem;
use Illuminate\Http\JsonResponse;

class BuildTomorrowController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'sections' => ContentBlockResource::collection(ContentBlock::where('context', 'build_tomorrow')->orderBy('order')->get()),
                'gallery' => GalleryResource::collection(Gallery::with('mediaItems')->get()),
                'media' => MediaItemResource::collection(MediaItem::where('type', 'video')->latest('published_at')->take(6)->get()),
            ],
        ]);
    }

    public function show(string $section): JsonResponse
    {
        $blocks = ContentBlock::where('context', 'build_tomorrow')
            ->where('slug', 'like', 'build-tomorrow.'.$section.'%')
            ->orderBy('order')
            ->get();

        abort_if($blocks->isEmpty(), 404);

        return response()->json(['data' => ContentBlockResource::collection($blocks)]);
    }
}
