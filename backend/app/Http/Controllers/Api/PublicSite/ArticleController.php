<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\CategoryResource;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ArticleController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $articles = Article::query()
            ->with(['category', 'pillar'])
            ->when($request->string('category')->isNotEmpty(), function ($query) use ($request): void {
                $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category')));
            })
            ->when($request->string('search')->isNotEmpty(), function ($query) use ($request): void {
                $term = '%'.$request->string('search')->toString().'%';
                $query->where(fn ($inner) => $inner->where('title', 'like', $term)->orWhere('excerpt', 'like', $term));
            })
            ->latest('published_at')
            ->paginate((int) $request->integer('per_page', 9));

        return ArticleResource::collection($articles)->additional([
            'meta' => [
                'categories' => CategoryResource::collection(Category::orderBy('name')->get()),
            ],
        ]);
    }

    public function show(Article $article): ArticleResource
    {
        return new ArticleResource($article->load(['category', 'pillar']));
    }
}
