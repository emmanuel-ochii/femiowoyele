<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\MediaItem;
use App\Models\NewsletterSubscriber;
use App\Models\Rsvp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_endpoints_require_authentication(): void
    {
        $this->getJson('/api/admin/overview')->assertUnauthorized();
        $this->getJson('/api/admin/rsvps/export')->assertUnauthorized();
    }

    public function test_admin_can_create_update_and_delete_articles(): void
    {
        $this->seed();

        Sanctum::actingAs(User::where('email', 'admin@femiowoyele.com')->first());

        $category = Category::where('slug', 'leadership')->firstOrFail();

        $create = $this->postJson('/api/admin/articles', [
            'category_id' => $category->id,
            'slug' => 'admin-created-article',
            'title' => 'Admin Created Article',
            'excerpt' => 'A CMS-created article for testing the content administration flow.',
            'body' => 'The admin endpoint should validate, persist, and shape article responses through a Laravel Resource.',
            'published_at' => now()->toDateString(),
            'is_featured' => false,
        ])->assertSuccessful()
            ->assertJsonPath('data.slug', 'admin-created-article');

        $id = $create->json('data.id');

        $this->patchJson('/api/admin/articles/'.$id, [
            'category_id' => $category->id,
            'slug' => 'admin-created-article',
            'title' => 'Updated Article',
            'excerpt' => 'An updated excerpt for the CMS-created article.',
            'body' => 'The updated body confirms edit support for the protected CMS API.',
            'published_at' => now()->toDateString(),
            'is_featured' => true,
        ])->assertOk()
            ->assertJsonPath('data.title', 'Updated Article')
            ->assertJsonPath('data.is_featured', true);

        $this->deleteJson('/api/admin/articles/'.$id)->assertOk();

        $this->assertDatabaseMissing(Article::class, ['id' => $id]);
    }

    public function test_admin_overview_returns_resource_slugs_for_navigation(): void
    {
        $this->seed();

        Sanctum::actingAs(User::where('email', 'admin@femiowoyele.com')->first());

        $this->getJson('/api/admin/overview')
            ->assertOk()
            ->assertJsonFragment(['slug' => 'articles', 'label' => 'Articles'])
            ->assertJsonFragment(['slug' => 'contact-messages', 'label' => 'Contact Messages'])
            ->assertJsonFragment(['slug' => 'gallery-items', 'label' => 'Gallery Items']);
    }

    public function test_public_submission_records_are_read_only_in_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $rsvp = Rsvp::create(['name' => 'Guest', 'email' => 'guest@example.com', 'attending' => true, 'guests' => 0]);
        $message = ContactMessage::create([
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'subject' => 'Speaking',
            'message' => 'I would like to enquire about a keynote.',
            'type' => 'speaking',
        ]);
        $subscriber = NewsletterSubscriber::create(['email' => 'reader@example.com', 'source' => 'footer']);

        $this->deleteJson('/api/admin/rsvps/'.$rsvp->id)->assertStatus(405);
        $this->deleteJson('/api/admin/contact-messages/'.$message->id)->assertStatus(405);
        $this->deleteJson('/api/admin/newsletter-subscribers/'.$subscriber->id)->assertStatus(405);

        $this->assertDatabaseHas(Rsvp::class, ['id' => $rsvp->id]);
        $this->assertDatabaseHas(ContactMessage::class, ['id' => $message->id]);
        $this->assertDatabaseHas(NewsletterSubscriber::class, ['id' => $subscriber->id]);
    }

    public function test_admin_can_export_all_rsvps_as_an_excel_file(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        Rsvp::create([
            'name' => 'Ada Builder',
            'email' => 'ada@example.com',
            'attending' => true,
            'guests' => 2,
            'note' => 'Careful & ready',
        ]);
        Rsvp::create([
            'name' => 'Tunde Steward',
            'email' => 'tunde@example.com',
            'attending' => false,
            'guests' => 0,
        ]);

        $this->get('/api/admin/rsvps/export')
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8')
            ->assertHeader('content-disposition', 'attachment; filename="femiowoyele-rsvps-'.now()->toDateString().'.xls"')
            ->assertSee('Ada Builder', false)
            ->assertSee('ada@example.com', false)
            ->assertSee('Careful &amp; ready', false)
            ->assertSee('Tunde Steward', false);
    }

    public function test_admin_can_create_update_and_delete_gallery_items(): void
    {
        $this->seed();

        Sanctum::actingAs(User::where('email', 'admin@femiowoyele.com')->first());

        $gallery = Gallery::create([
            'title' => 'CMS Test Gallery',
            'description' => 'A gallery created for admin API coverage.',
        ]);
        $mediaItem = MediaItem::firstOrFail();

        $create = $this->postJson('/api/admin/gallery-items', [
            'gallery_id' => $gallery->id,
            'media_item_id' => $mediaItem->id,
            'order' => 3,
        ])->assertSuccessful()
            ->assertJsonPath('data.gallery_id', $gallery->id)
            ->assertJsonPath('data.media_item_id', $mediaItem->id);

        $id = $create->json('data.id');

        $this->patchJson('/api/admin/gallery-items/'.$id, [
            'gallery_id' => $gallery->id,
            'media_item_id' => $mediaItem->id,
            'order' => 8,
        ])->assertOk()
            ->assertJsonPath('data.order', 8);

        $this->deleteJson('/api/admin/gallery-items/'.$id)->assertOk();

        $this->assertDatabaseMissing(GalleryItem::class, ['id' => $id]);
    }
}
