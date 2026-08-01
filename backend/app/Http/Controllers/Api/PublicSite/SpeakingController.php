<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentBlockResource;
use App\Http\Resources\MediaItemResource;
use App\Models\ContentBlock;
use App\Models\MediaItem;
use Illuminate\Http\JsonResponse;

class SpeakingController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'content' => ContentBlockResource::collection(ContentBlock::where('context', 'speaking')->orderBy('order')->get()),
                'media' => MediaItemResource::collection(MediaItem::whereIn('type', ['video', 'tv', 'podcast'])->latest('published_at')->take(6)->get()),
                'enquiry_type' => 'speaking',
            ],
        ]);
    }
}
