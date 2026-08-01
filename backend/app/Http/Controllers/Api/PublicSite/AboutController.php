<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentBlockResource;
use App\Http\Resources\ConvictionResource;
use App\Models\ContentBlock;
use App\Models\Conviction;
use Illuminate\Http\JsonResponse;

class AboutController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'narrative' => ContentBlockResource::collection(ContentBlock::where('context', 'about')->orderBy('order')->get()),
                'convictions' => ConvictionResource::collection(Conviction::orderBy('order')->get()),
            ],
        ]);
    }
}
