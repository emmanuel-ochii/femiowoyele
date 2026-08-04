<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\ContentBlockResource;
use App\Models\Book;
use App\Models\ContentBlock;
use Illuminate\Http\JsonResponse;

class LaunchController extends Controller
{
    /**
     * The dedicated launch page: the event block plus the book it unveils,
     * resolved from the block's own `book_slug` so the pairing stays editable.
     */
    public function __invoke(): JsonResponse
    {
        $event = ContentBlock::where('slug', 'home.launch')->first();

        abort_if($event === null, 404);

        $book = Book::where('slug', $event->meta['book_slug'] ?? 'entrusted')->first();

        return response()->json([
            'data' => [
                'event' => new ContentBlockResource($event),
                'book' => $book ? new BookResource($book) : null,
            ],
        ]);
    }
}
