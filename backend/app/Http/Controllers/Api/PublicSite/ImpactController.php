<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\ContentBlockResource;
use App\Http\Resources\ImpactMetricResource;
use App\Models\ContentBlock;
use App\Models\ImpactMetric;
use Illuminate\Http\JsonResponse;

class ImpactController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'data' => [
                'intro' => ContentBlockResource::collection(ContentBlock::where('context', 'impact')->orderBy('order')->get()),
                'metrics' => ImpactMetricResource::collection(ImpactMetric::orderBy('order')->get()),
            ],
        ]);
    }
}
