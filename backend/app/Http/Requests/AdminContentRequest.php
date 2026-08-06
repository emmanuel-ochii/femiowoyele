<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-content') ?? false;
    }

    public function rules(): array
    {
        $resource = (string) $this->route('resource');
        $id = $this->route('id');

        return match ($resource) {
            'articles' => [
                'category_id' => ['nullable', 'exists:categories,id'],
                'pillar_id' => ['nullable', 'exists:pillars,id'],
                'slug' => ['required', 'string', 'max:180', Rule::unique('articles', 'slug')->ignore($id)],
                'title' => ['required', 'string', 'max:220'],
                'excerpt' => ['required', 'string', 'max:700'],
                'body' => ['required', 'string'],
                'published_at' => ['nullable', 'date'],
                'is_featured' => ['boolean'],
            ],
            'journal-entries' => [
                'category_id' => ['nullable', 'exists:categories,id'],
                'slug' => ['required', 'string', 'max:180', Rule::unique('journal_entries', 'slug')->ignore($id)],
                'title' => ['required', 'string', 'max:220'],
                'excerpt' => ['required', 'string', 'max:700'],
                'body' => ['required', 'string'],
                'published_at' => ['nullable', 'date'],
            ],
            'books' => [
                'slug' => ['required', 'string', 'max:180', Rule::unique('books', 'slug')->ignore($id)],
                'title' => ['required', 'string', 'max:220'],
                'subtitle' => ['nullable', 'string', 'max:220'],
                'description' => ['required', 'string'],
                'cover_image_path' => ['nullable', 'string', 'max:255'],
                'is_featured' => ['boolean'],
                'order' => ['integer', 'min:0'],
            ],
            'pillars' => [
                'slug' => ['required', 'string', 'max:180', Rule::unique('pillars', 'slug')->ignore($id)],
                'title' => ['required', 'string', 'max:180'],
                'subtitle' => ['nullable', 'string', 'max:220'],
                'description' => ['required', 'string'],
                'order' => ['integer', 'min:0'],
            ],
            'projects' => [
                'pillar_id' => ['required', 'exists:pillars,id'],
                'slug' => ['required', 'string', 'max:180', Rule::unique('projects', 'slug')->ignore($id)],
                'title' => ['required', 'string', 'max:220'],
                'summary' => ['required', 'string', 'max:700'],
                'content' => ['nullable', 'string'],
            ],
            'categories' => [
                'slug' => ['required', 'string', 'max:180', Rule::unique('categories', 'slug')->ignore($id)],
                'name' => ['required', 'string', 'max:160'],
                'description' => ['nullable', 'string'],
            ],
            'media-items' => [
                'pillar_id' => ['nullable', 'exists:pillars,id'],
                'type' => ['required', 'string', Rule::in(['interview', 'tv', 'podcast', 'video', 'image', 'download'])],
                'slug' => ['required', 'string', 'max:180', Rule::unique('media_items', 'slug')->ignore($id)],
                'title' => ['required', 'string', 'max:220'],
                'description' => ['nullable', 'string'],
                'url' => ['nullable', 'url', 'max:255'],
                'thumbnail_path' => ['nullable', 'string', 'max:255'],
                'published_at' => ['nullable', 'date'],
            ],
            'rsvps' => [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:180', Rule::unique('rsvps', 'email')->ignore($id)],
                'attending' => ['required', 'boolean'],
                'guests' => ['nullable', 'integer', 'min:0', 'max:10'],
                'note' => ['nullable', 'string', 'max:1000'],
            ],
            'impact-metrics' => [
                'slug' => ['required', 'string', 'max:180', Rule::unique('impact_metrics', 'slug')->ignore($id)],
                'label' => ['required', 'string', 'max:160'],
                'value' => ['required', 'string', 'max:80'],
                'description' => ['nullable', 'string'],
                'order' => ['integer', 'min:0'],
            ],
            'quotes' => [
                'text' => ['required', 'string', 'max:1200'],
                'source' => ['nullable', 'string', 'max:180'],
                'is_active' => ['boolean'],
            ],
            'convictions' => [
                'title' => ['required', 'string', 'max:180'],
                'description' => ['required', 'string'],
                'order' => ['integer', 'min:0'],
            ],
            'content-blocks' => [
                'slug' => ['required', 'string', 'max:180', Rule::unique('content_blocks', 'slug')->ignore($id)],
                'title' => ['required', 'string', 'max:220'],
                'body' => ['required', 'string'],
                'context' => ['required', 'string', 'max:180'],
                'meta' => ['nullable', 'array'],
                'order' => ['integer', 'min:0'],
            ],
            'contact-messages' => [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:180'],
                'subject' => ['required', 'string', 'max:180'],
                'message' => ['required', 'string'],
                'type' => ['required', 'string', 'max:80'],
            ],
            'newsletter-subscribers' => [
                'email' => ['required', 'email', 'max:180', Rule::unique('newsletter_subscribers', 'email')->ignore($id)],
                'name' => ['nullable', 'string', 'max:120'],
                'source' => ['nullable', 'string', 'max:120'],
            ],
            'galleries' => [
                'title' => ['required', 'string', 'max:180'],
                'description' => ['nullable', 'string'],
            ],
            'gallery-items' => [
                'gallery_id' => ['required', 'exists:galleries,id'],
                'media_item_id' => [
                    'required',
                    'exists:media_items,id',
                    Rule::unique('gallery_items', 'media_item_id')
                        ->where(fn ($query) => $query->where('gallery_id', $this->input('gallery_id')))
                        ->ignore($id),
                ],
                'order' => ['integer', 'min:0'],
            ],
            default => [],
        };
    }
}
