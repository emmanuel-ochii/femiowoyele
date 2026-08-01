<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\MediaItemResource;
use App\Models\Gallery;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MediaController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $items = MediaItem::query()
            ->with('pillar')
            ->when($request->string('type')->isNotEmpty(), fn ($query) => $query->where('type', $request->string('type')))
            ->latest('published_at')
            ->paginate((int) $request->integer('per_page', 12));

        return MediaItemResource::collection($items)->additional([
            'meta' => [
                'galleries' => GalleryResource::collection(Gallery::with('mediaItems')->get()),
            ],
        ]);
    }
}
