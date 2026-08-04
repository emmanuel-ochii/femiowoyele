<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\ContentBlockResource;
use App\Models\Book;
use App\Models\ContentBlock;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return BookResource::collection(Book::orderBy('order')->get())->additional([
            'meta' => [
                // The launch block lives under the `home` context but belongs on
                // this page too, so the event is not only visible from the homepage.
                'launch' => new ContentBlockResource(ContentBlock::where('slug', 'home.launch')->first()),
            ],
        ]);
    }

    public function show(Book $book): BookResource
    {
        // Carried through so the detail page can state "launching on …" rather
        // than guessing a publication status from the featured flag.
        return (new BookResource($book))->additional([
            'meta' => [
                'launch' => new ContentBlockResource(ContentBlock::where('slug', 'home.launch')->first()),
            ],
        ]);
    }
}
