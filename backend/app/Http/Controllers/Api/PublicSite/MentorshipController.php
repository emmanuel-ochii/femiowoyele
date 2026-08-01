<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentBlockResource;
use App\Models\ContentBlock;
use Illuminate\Http\JsonResponse;

class MentorshipController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'content' => ContentBlockResource::collection(ContentBlock::where('context', 'mentorship')->orderBy('order')->get()),
                'application_type' => 'mentorship',
            ],
        ]);
    }
}
