<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\ContentBlockResource;
use App\Http\Resources\ImpactMetricResource;
use App\Http\Resources\MediaItemResource;
use App\Http\Resources\PillarResource;
use App\Http\Resources\QuoteResource;
use App\Models\Article;
use App\Models\Book;
use App\Models\ContentBlock;
use App\Models\ImpactMetric;
use App\Models\MediaItem;
use App\Models\Pillar;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $blocks = ContentBlock::where('context', 'home')->orderBy('order')->get()->keyBy('slug');

        return response()->json([
            'data' => [
                'hero' => new ContentBlockResource($blocks->get('home.hero')),
                'intro' => new ContentBlockResource($blocks->get('home.intro')),
                'footer_statement' => new ContentBlockResource($blocks->get('home.footer-statement')),
                'pillars' => PillarResource::collection(Pillar::orderBy('order')->get()),
                'featured' => [
                    'articles' => ArticleResource::collection(Article::with(['category', 'pillar'])->where('is_featured', true)->latest('published_at')->take(3)->get()),
                    'books' => BookResource::collection(Book::where('is_featured', true)->orderBy('order')->take(2)->get()),
                    'media' => MediaItemResource::collection(MediaItem::latest('published_at')->take(3)->get()),
                ],
                'quote' => new QuoteResource(Quote::where('is_active', true)->inRandomOrder()->first()),
                'impact_metrics' => ImpactMetricResource::collection(ImpactMetric::orderBy('order')->take(4)->get()),
            ],
        ]);
    }
}
