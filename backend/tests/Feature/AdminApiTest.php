<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
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
}
