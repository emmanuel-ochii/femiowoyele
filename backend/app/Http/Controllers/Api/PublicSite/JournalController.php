<?php

namespace App\Http\Controllers\Api\PublicSite;

use App\Http\Controllers\Controller;
use App\Http\Resources\JournalEntryResource;
use App\Models\JournalEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JournalController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return JournalEntryResource::collection(
            JournalEntry::with('category')->latest('published_at')->paginate((int) $request->integer('per_page', 9))
        );
    }

    public function show(JournalEntry $journalEntry): JournalEntryResource
    {
        return new JournalEntryResource($journalEntry->load('category'));
    }
}
