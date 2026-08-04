<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Book;
use App\Models\Category;
use App\Models\ContentBlock;
use App\Models\Conviction;
use App\Models\Gallery;
use App\Models\ImpactMetric;
use App\Models\JournalEntry;
use App\Models\MediaItem;
use App\Models\Pillar;
use App\Models\Project;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@femiowoyele.com'],
            [
                'name' => 'FemiOwoyele Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ],
        );

        $categories = collect([
            ['slug' => 'leadership', 'name' => 'Leadership', 'description' => 'Leadership practice, stewardship, and institutions.'],
            ['slug' => 'enterprise', 'name' => 'Enterprise', 'description' => 'Company building, governance, and execution.'],
            ['slug' => 'sustainability', 'name' => 'Sustainability', 'description' => 'Long-term thinking for people, systems, and the environment.'],
            ['slug' => 'governance', 'name' => 'Governance', 'description' => 'Boards, policy, accountability, and civic life.'],
        ])->mapWithKeys(fn (array $category) => [
            $category['slug'] => Category::updateOrCreate(['slug' => $category['slug']], $category),
        ]);

        $pillars = collect([
            ['slug' => 'enterprise', 'title' => 'Enterprise', 'subtitle' => 'Building durable organisations', 'description' => 'Work across enterprise creation, operational clarity, and systems that can outlast individual momentum.', 'order' => 1],
            ['slug' => 'leadership', 'title' => 'Leadership', 'subtitle' => 'Responsible influence', 'description' => 'Leadership as discipline, stewardship, and the careful use of trust in complex environments.', 'order' => 2],
            ['slug' => 'governance', 'title' => 'Governance', 'subtitle' => 'Institutions that earn confidence', 'description' => 'Boards, civic structures, and decision systems that strengthen accountability and public value.', 'order' => 3],
            ['slug' => 'sustainability', 'title' => 'Sustainability', 'subtitle' => 'Growth with continuity', 'description' => 'Sustainability as a practical ethic for communities, companies, and future-facing economies.', 'order' => 4],
            ['slug' => 'mentorship', 'title' => 'Mentorship', 'subtitle' => 'Building builders', 'description' => 'Programmes, resources, and conversations that help emerging leaders build with depth.', 'order' => 5],
            ['slug' => 'speaking', 'title' => 'Speaking', 'subtitle' => 'Public thought and engagement', 'description' => 'Keynotes, panels, lectures, and moderated conversations for serious audiences.', 'order' => 6],
        ])->mapWithKeys(fn (array $pillar) => [
            $pillar['slug'] => Pillar::updateOrCreate(['slug' => $pillar['slug']], $pillar),
        ]);

        foreach ($pillars as $pillar) {
            Project::updateOrCreate(
                ['slug' => $pillar->slug.'-practice'],
                [
                    'pillar_id' => $pillar->id,
                    'title' => $pillar->title.' Practice',
                    'summary' => 'A focused body of work translating '.$pillar->title.' into practical systems, conversations, and field-tested initiatives.',
                    'content' => 'This project record exists as a CMS-managed anchor for the '.$pillar->title.' pillar. It can hold detailed case notes, galleries, media references, and related essays over time.',
                ],
            );
        }

        Article::updateOrCreate(
            ['slug' => 'the-long-work-of-institution-building'],
            [
                'category_id' => $categories['governance']->id,
                'pillar_id' => $pillars['governance']->id,
                'title' => 'The Long Work of Institution Building',
                'excerpt' => 'Why responsible leadership must treat institutions as living systems, not as ceremony or infrastructure alone.',
                'body' => 'Institutions endure when their habits are stronger than individual preference. The work is slow because trust accumulates slowly, but the return is civic, economic, and generational.',
                'published_at' => now()->subDays(24),
                'is_featured' => true,
            ],
        );

        Article::updateOrCreate(
            ['slug' => 'enterprise-as-stewardship'],
            [
                'category_id' => $categories['enterprise']->id,
                'pillar_id' => $pillars['enterprise']->id,
                'title' => 'Enterprise as Stewardship',
                'excerpt' => 'A reflection on building companies that serve markets without surrendering responsibility.',
                'body' => 'Enterprise becomes meaningful when it solves real problems with discipline and care. It asks leaders to think about customers, workers, communities, and continuity in the same breath.',
                'published_at' => now()->subDays(18),
                'is_featured' => true,
            ],
        );

        Article::updateOrCreate(
            ['slug' => 'what-young-builders-need-most'],
            [
                'category_id' => $categories['leadership']->id,
                'pillar_id' => $pillars['mentorship']->id,
                'title' => 'What Young Builders Need Most',
                'excerpt' => 'Mentorship works best when it offers standards, context, courage, and honest proximity to the work.',
                'body' => 'The next generation does not only need inspiration. It needs rooms where questions can mature, models that are honest about cost, and mentors who respect both ambition and formation.',
                'published_at' => now()->subDays(7),
                'is_featured' => true,
            ],
        );

        Book::updateOrCreate(
            ['slug' => 'entrusted'],
            [
                'title' => 'Entrusted',
                'subtitle' => 'Leadership, stewardship, and the work of responsibility',
                'description' => 'Entrusted is positioned as the primary authored work: a serious, accessible book on responsibility, leadership, and the moral architecture of influence.',
                'cover_image_path' => '/images/entrusted-cover.svg',
                'is_featured' => true,
                'order' => 1,
            ],
        );

        Book::updateOrCreate(
            ['slug' => 'notes-on-building-builders'],
            [
                'title' => 'Notes on Building Builders',
                'subtitle' => 'A future collection of mentorship essays',
                'description' => 'A planned work collecting reflections, frameworks, and field notes from mentorship conversations.',
                'cover_image_path' => null,
                'is_featured' => false,
                'order' => 2,
            ],
        );

        foreach ([
            ['slug' => 'builders-mentored', 'label' => 'Builders mentored', 'value' => '1,200+', 'description' => 'Emerging leaders reached through formal and informal mentorship.', 'order' => 1],
            ['slug' => 'public-engagements', 'label' => 'Public engagements', 'value' => '80+', 'description' => 'Lectures, panels, interviews, and strategy conversations.', 'order' => 2],
            ['slug' => 'communities-reached', 'label' => 'Communities reached', 'value' => '15+', 'description' => 'Communities touched by enterprise, leadership, and mentoring work.', 'order' => 3],
            ['slug' => 'research-themes', 'label' => 'Research themes', 'value' => '6', 'description' => 'Enterprise, governance, sustainability, leadership, mentorship, and public engagement.', 'order' => 4],
        ] as $metric) {
            ImpactMetric::updateOrCreate(['slug' => $metric['slug']], $metric);
        }

        foreach ([
            ['title' => 'Responsibility before visibility', 'description' => 'The work must be able to carry more weight than the platform that introduces it.', 'order' => 1],
            ['title' => 'Institutions matter', 'description' => 'A society grows stronger when its people build systems that survive personality and preference.', 'order' => 2],
            ['title' => 'Depth is a form of service', 'description' => 'Clear thinking, careful language, and patient formation are practical public goods.', 'order' => 3],
            ['title' => 'Africa is not a footnote', 'description' => 'African experience, intelligence, and aspiration should speak with confidence in global rooms.', 'order' => 4],
        ] as $conviction) {
            Conviction::updateOrCreate(['title' => $conviction['title']], $conviction);
        }

        Quote::updateOrCreate(
            ['text' => 'The task is not simply to build what is impressive, but what can be trusted.'],
            ['source' => 'Femi Owoyele', 'is_active' => true],
        );

        $media = collect([
            ['type' => 'video', 'slug' => 'leadership-and-trust-keynote', 'title' => 'Leadership and Trust Keynote', 'description' => 'A public lecture on responsible influence and durable institutions.', 'url' => 'https://example.com/leadership-keynote', 'pillar_id' => $pillars['speaking']->id, 'published_at' => now()->subDays(30)],
            ['type' => 'podcast', 'slug' => 'building-builders-conversation', 'title' => 'Building Builders Conversation', 'description' => 'A podcast discussion on mentorship, formation, and enterprise.', 'url' => 'https://example.com/building-builders', 'pillar_id' => $pillars['mentorship']->id, 'published_at' => now()->subDays(45)],
            ['type' => 'image', 'slug' => 'build-tomorrow-gallery-01', 'title' => 'Build Tomorrow Community Session', 'description' => 'A field image placeholder for Build Tomorrow community programming.', 'url' => null, 'pillar_id' => $pillars['mentorship']->id, 'published_at' => now()->subDays(60)],
        ])->map(fn (array $item) => MediaItem::updateOrCreate(['slug' => $item['slug']], $item));

        $gallery = Gallery::updateOrCreate(
            ['title' => 'Build Tomorrow Gallery'],
            ['description' => 'A curated record of community, conference, and mentorship moments.'],
        );

        $gallery->mediaItems()->sync(
            $media->pluck('id')->mapWithKeys(fn ($id, $index) => [$id => ['order' => $index + 1]])->all()
        );

        foreach ([
            ['slug' => 'journal-on-clarity-and-work', 'title' => 'On Clarity and Work', 'excerpt' => 'A short reflection on why clarity is often the beginning of serious work.', 'body' => 'Clarity does not remove difficulty, but it removes waste. It helps people know what deserves attention and what should be allowed to fall away.', 'category_id' => $categories['leadership']->id, 'published_at' => now()->subDays(10)],
            ['slug' => 'journal-notes-from-a-mentorship-room', 'title' => 'Notes from a Mentorship Room', 'excerpt' => 'What changes when young builders are given context instead of slogans.', 'body' => 'The most valuable conversations often begin quietly. A builder asks a precise question, and the room becomes a place where experience can become instruction.', 'category_id' => $categories['enterprise']->id, 'published_at' => now()->subDays(4)],
        ] as $entry) {
            JournalEntry::updateOrCreate(['slug' => $entry['slug']], $entry);
        }

        foreach ($this->contentBlocks() as $block) {
            ContentBlock::updateOrCreate(['slug' => $block['slug']], $block);
        }
    }

    private function contentBlocks(): array
    {
        return [
            ['context' => 'home', 'slug' => 'home.hero', 'title' => 'Femi Owoyele', 'body' => 'A considered home for enterprise, governance, sustainability, mentorship, authorship, and public engagement, shaped by a long view of responsibility and institutional trust.', 'meta' => ['kicker' => 'Enterprise. Leadership. Stewardship.', 'image' => '/images/profem.jpeg'], 'order' => 1],
            ['context' => 'home', 'slug' => 'home.intro', 'title' => 'A platform for serious work', 'body' => 'FemiOwoyele.com brings together ideas, initiatives, and records of practice across company building, public thought, mentorship, books, and civic imagination. It is designed to grow with the work: calm in tone, rigorous in substance, and global in conversation while remaining rooted in African experience.', 'meta' => [], 'order' => 2],
            ['context' => 'home', 'slug' => 'home.footer-statement', 'title' => 'The long view', 'body' => 'The work continues through enterprises, ideas, institutions, mentorship rooms, and public conversations shaped by responsibility.', 'meta' => [], 'order' => 3],
            ['context' => 'about', 'slug' => 'about.identity', 'title' => 'Who I Am', 'body' => 'Femi Owoyele works across enterprise, leadership, governance, sustainability, mentorship, scholarship, authorship, and public engagement. The connecting thread is a commitment to responsible building.', 'meta' => [], 'order' => 1],
            ['context' => 'about', 'slug' => 'about.outlook', 'title' => 'African in identity, global in outlook', 'body' => 'The work is rooted in African realities and aspirations while remaining open to global conversation, standards, and collaboration.', 'meta' => [], 'order' => 2],
            ['context' => 'build_tomorrow', 'slug' => 'build-tomorrow.vision', 'title' => 'Vision', 'body' => 'Build Tomorrow is a platform for emerging builders: a conference, community, and knowledge space for those shaping institutions, companies, and public life.', 'meta' => [], 'order' => 1],
            ['context' => 'build_tomorrow', 'slug' => 'build-tomorrow.conference', 'title' => 'Conference', 'body' => 'A convening designed for serious conversations, practical frameworks, and intergenerational exchange.', 'meta' => [], 'order' => 2],
            ['context' => 'build_tomorrow', 'slug' => 'build-tomorrow.community', 'title' => 'Community', 'body' => 'A growing network of builders who value discipline, service, and long-term contribution.', 'meta' => [], 'order' => 3],
            ['context' => 'build_tomorrow', 'slug' => 'build-tomorrow.publications', 'title' => 'Publications', 'body' => 'Essays, notes, and resources that extend the conversations beyond a single event.', 'meta' => [], 'order' => 4],
            ['context' => 'speaking', 'slug' => 'speaking.topics', 'title' => 'Speaking Topics', 'body' => 'Leadership and trust; enterprise as stewardship; institution building; mentorship and formation; sustainability and future readiness.', 'meta' => ['audiences' => ['boards', 'universities', 'founders', 'public institutions', 'faith and civic communities']], 'order' => 1],
            ['context' => 'speaking', 'slug' => 'speaking.engagements', 'title' => 'Engagements', 'body' => 'Available for keynotes, panels, moderated conversations, lectures, and private leadership sessions.', 'meta' => [], 'order' => 2],
            ['context' => 'mentorship', 'slug' => 'mentorship.philosophy', 'title' => 'Building Builders', 'body' => 'Mentorship is treated as formation: helping builders develop judgment, discipline, courage, and context for the responsibilities they want to carry.', 'meta' => [], 'order' => 1],
            ['context' => 'mentorship', 'slug' => 'mentorship.programmes', 'title' => 'Programmes and Resources', 'body' => 'Programmes include intimate cohorts, resource notes, builder conversations, and application-based mentorship opportunities.', 'meta' => [], 'order' => 2],
            ['context' => 'impact', 'slug' => 'impact.overview', 'title' => 'Impact', 'body' => 'Impact is presented as an evolving record, not a trophy case: numbers, stories, and signals from a body of work still being built.', 'meta' => [], 'order' => 1],
        ];
    }
}
