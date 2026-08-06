<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminContentRequest;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\BookResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ContactMessageResource;
use App\Http\Resources\ContentBlockResource;
use App\Http\Resources\ConvictionResource;
use App\Http\Resources\GalleryItemResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\ImpactMetricResource;
use App\Http\Resources\JournalEntryResource;
use App\Http\Resources\MediaItemResource;
use App\Http\Resources\NewsletterSubscriberResource;
use App\Http\Resources\PillarResource;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\QuoteResource;
use App\Http\Resources\RsvpResource;
use App\Models\Article;
use App\Models\Book;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\ContentBlock;
use App\Models\Conviction;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\ImpactMetric;
use App\Models\JournalEntry;
use App\Models\MediaItem;
use App\Models\NewsletterSubscriber;
use App\Models\Pillar;
use App\Models\Project;
use App\Models\Quote;
use App\Models\Rsvp;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

class AdminContentController extends Controller
{
    public static function resources(): array
    {
        return array_keys(self::map());
    }

    public function overview(): JsonResponse
    {
        return response()->json([
            'data' => collect(self::map())
                ->map(fn (array $definition, string $slug) => [
                    'slug' => $slug,
                    'label' => $definition['label'],
                    'count' => $definition['model']::count(),
                ])
                ->all(),
            'meta' => [
                'rsvps' => [
                    'attending' => Rsvp::where('attending', true)->count(),
                    'declined' => Rsvp::where('attending', false)->count(),
                    // This is a closed event: each attending RSVP represents one seat.
                    'seats' => Rsvp::where('attending', true)->count(),
                ],
            ],
        ]);
    }

    public function exportRsvps(): Response
    {
        $filename = 'femiowoyele-rsvps-'.now()->toDateString().'.xls';
        $headers = [
            'Full Name',
            'Email Address',
            'Attending',
            'Note',
            'Event Slug',
            'Source',
            'Submitted At',
        ];

        $rows = Rsvp::query()
            ->latest()
            ->get()
            ->map(fn (Rsvp $rsvp) => [
                ['value' => $rsvp->name],
                ['value' => $rsvp->email],
                ['value' => $rsvp->attending ? 'Yes' : 'No'],
                ['value' => $rsvp->note],
                ['value' => $rsvp->event_slug],
                ['value' => $rsvp->source],
                ['value' => $rsvp->created_at?->toDateTimeString()],
            ])
            ->all();

        return response($this->excelXml('Launch RSVPs', $headers, $rows), 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function index(Request $request, string $resource): mixed
    {
        $definition = $this->definition($resource);

        $query = $definition['model']::query();
        $this->applyEagerLoads($query, $resource);
        $this->applyOrdering($query, $resource);

        return $definition['resource']::collection(
            $query->paginate((int) $request->integer('per_page', 15))
        );
    }

    public function store(AdminContentRequest $request, string $resource): JsonResource
    {
        $this->abortIfReadOnly($resource);

        $model = $this->definition($resource)['model']::create($request->validated());

        return $this->resource($resource, $model->fresh());
    }

    public function show(string $resource, int $id): JsonResource
    {
        return $this->resource($resource, $this->find($resource, $id));
    }

    public function update(AdminContentRequest $request, string $resource, int $id): JsonResource
    {
        $this->abortIfReadOnly($resource);

        $model = $this->find($resource, $id);
        $model->fill($request->validated());
        $model->save();

        return $this->resource($resource, $model->fresh());
    }

    public function destroy(string $resource, int $id): JsonResponse
    {
        $this->abortIfReadOnly($resource);

        $this->find($resource, $id)->delete();

        return response()->json(['data' => ['deleted' => true]]);
    }

    private static function map(): array
    {
        return [
            'articles' => ['label' => 'Articles', 'model' => Article::class, 'resource' => ArticleResource::class],
            'journal-entries' => ['label' => 'Journal Entries', 'model' => JournalEntry::class, 'resource' => JournalEntryResource::class],
            'books' => ['label' => 'Books', 'model' => Book::class, 'resource' => BookResource::class],
            'pillars' => ['label' => 'Pillars', 'model' => Pillar::class, 'resource' => PillarResource::class],
            'projects' => ['label' => 'Projects', 'model' => Project::class, 'resource' => ProjectResource::class],
            'categories' => ['label' => 'Categories', 'model' => Category::class, 'resource' => CategoryResource::class],
            'media-items' => ['label' => 'Media Items', 'model' => MediaItem::class, 'resource' => MediaItemResource::class],
            'impact-metrics' => ['label' => 'Impact Metrics', 'model' => ImpactMetric::class, 'resource' => ImpactMetricResource::class],
            'quotes' => ['label' => 'Quotes', 'model' => Quote::class, 'resource' => QuoteResource::class],
            'convictions' => ['label' => 'Convictions', 'model' => Conviction::class, 'resource' => ConvictionResource::class],
            'content-blocks' => ['label' => 'Content Blocks', 'model' => ContentBlock::class, 'resource' => ContentBlockResource::class],
            'rsvps' => ['label' => 'Launch RSVPs', 'model' => Rsvp::class, 'resource' => RsvpResource::class],
            'contact-messages' => ['label' => 'Contact Messages', 'model' => ContactMessage::class, 'resource' => ContactMessageResource::class],
            'newsletter-subscribers' => ['label' => 'Newsletter Subscribers', 'model' => NewsletterSubscriber::class, 'resource' => NewsletterSubscriberResource::class],
            'galleries' => ['label' => 'Galleries', 'model' => Gallery::class, 'resource' => GalleryResource::class],
            'gallery-items' => ['label' => 'Gallery Items', 'model' => GalleryItem::class, 'resource' => GalleryItemResource::class],
        ];
    }

    private static function readOnlyResources(): array
    {
        return ['rsvps', 'contact-messages', 'newsletter-subscribers'];
    }

    private function definition(string $resource): array
    {
        abort_unless(array_key_exists($resource, self::map()), 404);

        return self::map()[$resource];
    }

    private function find(string $resource, int $id): Model
    {
        $query = $this->definition($resource)['model']::query();
        $this->applyEagerLoads($query, $resource);

        return $query->findOrFail($id);
    }

    private function abortIfReadOnly(string $resource): void
    {
        abort_if(
            in_array($resource, self::readOnlyResources(), true),
            405,
            'This resource is managed by public submissions and is read-only in the CMS.'
        );
    }

    private function excelXml(string $sheetName, array $headers, array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ';
        $xml .= 'xmlns:o="urn:schemas-microsoft-com:office:office" ';
        $xml .= 'xmlns:x="urn:schemas-microsoft-com:office:excel" ';
        $xml .= 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'."\n";
        $xml .= '<Worksheet ss:Name="'.$this->xml($sheetName).'"><Table>'."\n";
        $xml .= $this->excelRow(array_map(fn (string $header) => ['value' => $header], $headers));

        foreach ($rows as $row) {
            $xml .= $this->excelRow($row);
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return $xml;
    }

    private function excelRow(array $cells): string
    {
        $xml = '<Row>';

        foreach ($cells as $cell) {
            $type = $cell['type'] ?? 'String';
            $xml .= '<Cell><Data ss:Type="'.$this->xml($type).'">'.$this->xml($cell['value'] ?? '').'</Data></Cell>';
        }

        return $xml.'</Row>'."\n";
    }

    private function xml(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }

    private function resource(string $resource, Model $model): JsonResource
    {
        $resourceClass = $this->definition($resource)['resource'];

        return new $resourceClass($model);
    }

    private function applyEagerLoads(Builder $query, string $resource): void
    {
        match ($resource) {
            'articles' => $query->with(['category', 'pillar']),
            'journal-entries' => $query->with('category'),
            'pillars' => $query->with(['projects', 'articles', 'mediaItems']),
            'media-items' => $query->with('pillar'),
            'galleries' => $query->with('mediaItems'),
            'gallery-items' => $query->with(['gallery', 'mediaItem']),
            default => null,
        };
    }

    private function applyOrdering(Builder $query, string $resource): void
    {
        match ($resource) {
            'articles', 'journal-entries', 'media-items' => $query->latest('published_at'),
            'books', 'pillars', 'impact-metrics', 'convictions', 'content-blocks' => $query->orderBy('order'),
            'gallery-items' => $query->orderBy('gallery_id')->orderBy('order'),
            default => $query->latest(),
        };
    }
}
