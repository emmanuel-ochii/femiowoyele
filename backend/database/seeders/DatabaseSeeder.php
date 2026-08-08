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
use App\Models\PickupPoint;
use App\Models\Project;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Baseline editorial content for FemiOwoyele.com.
 *
 * Everything here is written as publishable copy rather than placeholder text:
 * the site should read as finished from the first deploy, and each record can
 * then be edited in the admin CMS without a code change.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => (string) env('ADMIN_EMAIL', 'admin@femiowoyele.com')],
            [
                'name' => 'FemiOwoyele Admin',
                'password' => $this->initialAdminPassword(),
                'role' => 'admin',
            ],
        );

        $categories = $this->seedCategories();
        $pillars = $this->seedPillars();

        $this->seedProjects($pillars);
        $this->seedArticles($categories, $pillars);
        $this->seedBooks();
        $this->seedImpactMetrics();
        $this->seedConvictions();
        $this->seedQuotes();
        $this->seedMedia($pillars);
        $this->seedJournal($categories);
        $this->seedPickupPoints();

        foreach ($this->contentBlocks() as $block) {
            ContentBlock::updateOrCreate(['slug' => $block['slug']], $block);
        }
    }

    private function initialAdminPassword(): string
    {
        $password = (string) env('ADMIN_INITIAL_PASSWORD', '');

        if ($password !== '') {
            return $password;
        }

        if (app()->isProduction()) {
            throw new RuntimeException('Set ADMIN_INITIAL_PASSWORD before seeding the first production admin.');
        }

        return 'password';
    }

    private function seedCategories()
    {
        return collect([
            ['slug' => 'leadership', 'name' => 'Leadership', 'description' => 'Judgment, stewardship, and the responsible use of influence.'],
            ['slug' => 'enterprise', 'name' => 'Enterprise', 'description' => 'Company building, operating discipline, and durable execution.'],
            ['slug' => 'governance', 'name' => 'Governance', 'description' => 'Boards, policy, accountability, and public trust.'],
            ['slug' => 'sustainability', 'name' => 'Sustainability', 'description' => 'Long-horizon thinking for people, systems, and the environment.'],
        ])->mapWithKeys(fn (array $category) => [
            $category['slug'] => Category::updateOrCreate(['slug' => $category['slug']], $category),
        ]);
    }

    private function seedPillars()
    {
        return collect([
            [
                'slug' => 'enterprise',
                'title' => 'Enterprise',
                'subtitle' => 'Building organisations that outlast their founders',
                'description' => 'Company building with an operating bias: clear structure, honest numbers, and systems strong enough to survive the day the founder is no longer in the room.',
                'order' => 1,
            ],
            [
                'slug' => 'leadership',
                'title' => 'Leadership',
                'subtitle' => 'Influence held as a responsibility',
                'description' => 'Leadership treated as a discipline rather than a personality — the careful, unglamorous work of spending trust well in complex environments.',
                'order' => 2,
            ],
            [
                'slug' => 'governance',
                'title' => 'Governance',
                'subtitle' => 'Institutions that earn confidence',
                'description' => 'Board practice, decision rights, and accountability structures that let organisations be trusted by the people who depend on them.',
                'order' => 3,
            ],
            [
                'slug' => 'sustainability',
                'title' => 'Sustainability',
                'subtitle' => 'Growth that can be sustained',
                'description' => 'A practical ethic rather than a report chapter: building enterprises and communities that are still standing, and still worth standing, in a generation.',
                'order' => 4,
            ],
            [
                'slug' => 'mentorship',
                'title' => 'Mentorship',
                'subtitle' => 'Building builders',
                'description' => 'Cohorts, conversations, and resources that help emerging leaders develop judgment before the stakes force them to.',
                'order' => 5,
            ],
            [
                'slug' => 'speaking',
                'title' => 'Speaking',
                'subtitle' => 'Public thought, taken seriously',
                'description' => 'Keynotes, lectures, panels, and closed sessions written for the specific room — boards, universities, founders, and public institutions.',
                'order' => 6,
            ],
        ])->mapWithKeys(fn (array $pillar) => [
            $pillar['slug'] => Pillar::updateOrCreate(['slug' => $pillar['slug']], $pillar),
        ]);
    }

    private function seedProjects($pillars): void
    {
        $projects = [
            ['pillar' => 'enterprise', 'slug' => 'operating-discipline-practice', 'title' => 'Operating discipline in growing companies', 'summary' => 'Working with founding teams on the transition from personal effort to institutional capability: structure, reporting rhythm, and decision rights.'],
            ['pillar' => 'leadership', 'slug' => 'leadership-formation-practice', 'title' => 'Leadership formation for senior teams', 'summary' => 'Closed sessions with executive teams on judgment under pressure, succession, and the honest use of authority.'],
            ['pillar' => 'governance', 'slug' => 'board-effectiveness-practice', 'title' => 'Board effectiveness and accountability', 'summary' => 'Advisory work on board composition, committee structure, and the reporting that lets non-executives govern rather than spectate.'],
            ['pillar' => 'sustainability', 'slug' => 'long-horizon-strategy-practice', 'title' => 'Long-horizon strategy', 'summary' => 'Helping organisations weigh decisions on a twenty-year clock: resource use, community relationships, and the cost of short-term wins.'],
            ['pillar' => 'mentorship', 'slug' => 'building-builders-cohorts', 'title' => 'Building Builders cohorts', 'summary' => 'Small, application-based mentorship groups for founders and emerging leaders already carrying real responsibility.'],
            ['pillar' => 'speaking', 'slug' => 'public-lectures-practice', 'title' => 'Lectures and public conversations', 'summary' => 'Addresses and moderated conversations for universities, industry convenings, and civic institutions.'],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['slug' => $project['slug']],
                [
                    'pillar_id' => $pillars[$project['pillar']]->id,
                    'title' => $project['title'],
                    'summary' => $project['summary'],
                    'content' => $project['summary'],
                ],
            );
        }
    }

    private function seedArticles($categories, $pillars): void
    {
        $articles = [
            [
                'slug' => 'the-long-work-of-institution-building',
                'category' => 'governance',
                'pillar' => 'governance',
                'title' => 'The Long Work of Institution Building',
                'excerpt' => 'Institutions are not built by announcements. They are built by habits that hold when the founder is tired, the market turns, and nobody is watching.',
                'body' => "Every institution begins as somebody's personal effort. A founder answers the phone, signs the cheques, remembers the exceptions, and holds the standard in their own head. For a while this works, and it works well enough that it is easy to mistake the person for the system.\n\nThe transition from person to institution is the hardest work in organisational life, because it asks the founder to give away the very things that made them useful. Decisions have to be written down. Judgment has to be taught rather than exercised. Standards have to survive disagreement, which means they have to be explicit enough to be argued with.\n\nThis is slow work, and it is rarely rewarded in the moment. Trust accumulates at the speed of demonstrated consistency, not at the speed of intention. An organisation earns the right to be called an institution only after it has been tested — by a bad year, a difficult succession, a decision that cost money to make correctly.\n\nWhat makes the work worth doing is that the return is generational. A company that can outlive its founder employs people who were not born when it started. A board that governs honestly protects savers who will never attend a meeting. A university that holds its standard shapes graduates who will never know whose discipline they inherited.\n\nThe question worth asking of any organisation is not how impressive it looks today. It is whether its habits are stronger than the preferences of the people currently running it.",
                'days_ago' => 24,
                'featured' => true,
            ],
            [
                'slug' => 'enterprise-as-stewardship',
                'category' => 'enterprise',
                'pillar' => 'enterprise',
                'title' => 'Enterprise as Stewardship',
                'excerpt' => 'Profit is a constraint, not a purpose. The companies worth building are the ones that can explain who they are holding something in trust for.',
                'body' => "There is a version of enterprise that treats the business as a machine for extracting value, and there is a version that treats it as something held in trust. Both can be profitable. Only one of them tends to still be there in thirty years.\n\nStewardship is not a softer form of commerce. It is a more demanding one. It asks a company to be solvent, competitive, and disciplined — and then asks it to be those things without quietly transferring the cost onto workers, customers, suppliers, or the place it operates in.\n\nIn practice this shows up in unglamorous decisions. Whether you pay suppliers on time when cash is tight. Whether the reporting you show a board is the reporting you actually manage with. Whether you take the contract that pays well and compromises a standard you have asked your team to hold.\n\nNone of these decisions make the news. Together they decide whether an organisation is trustworthy, and trustworthiness is the only durable competitive position I know of. Products are copied. Pricing is matched. A reputation for keeping your word under pressure is expensive to build and almost impossible to counterfeit.\n\nSo the practical test for enterprise is simple, if uncomfortable: if every decision this quarter became public in ten years, would the company still be proud of how it made money?",
                'days_ago' => 18,
                'featured' => true,
            ],
            [
                'slug' => 'what-young-builders-need-most',
                'category' => 'leadership',
                'pillar' => 'mentorship',
                'title' => 'What Young Builders Need Most',
                'excerpt' => 'Not inspiration. Standards, context, honest accounting of cost, and rooms where a difficult question can be asked without penalty.',
                'body' => "The most common thing offered to young builders is encouragement. It is the least useful thing they need.\n\nAmbition is rarely the shortage. What is usually missing is context: an honest account of what a decision costs, how long it takes to work, and what it looks like when it is going badly rather than when it is finished and photographed. Most public storytelling about building compresses years of ambiguity into a clean arc, and leaves people unprepared for the middle.\n\nWhat actually forms a builder is proximity — being close enough to real work to see the trade-offs being made in real time, and being asked to defend a judgment in front of someone who will push back without humiliating them.\n\nThat is why mentorship, done properly, is closer to apprenticeship than to advice. It requires a room where the question can be more precise than the slogan, where a wrong answer is instructive rather than disqualifying, and where the mentor is willing to say what a decision actually cost them.\n\nThe generation coming next is not short of talent or energy. It is short of rooms like that. Building more of them is some of the highest-leverage work available to anyone who has already been through the middle.",
                'days_ago' => 7,
                'featured' => true,
            ],
            [
                'slug' => 'sustainability-is-an-operating-decision',
                'category' => 'sustainability',
                'pillar' => 'sustainability',
                'title' => 'Sustainability Is an Operating Decision',
                'excerpt' => 'It is settled in procurement, hiring, and capital allocation long before it appears in a report.',
                'body' => "Most organisations encounter sustainability first as a disclosure problem: a report to produce, a framework to comply with, a page to add to the annual review. Framed that way, it becomes a communications exercise, and communications exercises are the first thing cut when the year gets hard.\n\nThe more useful framing is that sustainability is an operating decision, and it is made in ordinary places. In procurement, when you choose a supplier whose practices you would be comfortable explaining. In hiring, when you build a team you intend to still be employing through a downturn. In capital allocation, when you weigh a return that arrives in three years against one that arrives in fifteen.\n\nNone of those decisions require a framework to make. They require a time horizon, and the willingness to be judged on it.\n\nFor organisations operating in growing economies, the horizon question is not abstract. Infrastructure, talent, and community relationships are being formed now, and they will constrain or enable everything built on top of them for decades. Extracting maximum value from them today is possible. It is also a decision to make the next twenty years harder.\n\nThe test I find most clarifying: would this decision still look reasonable to the people who inherit its consequences?",
                'days_ago' => 40,
                'featured' => false,
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'category_id' => $categories[$article['category']]->id,
                    'pillar_id' => $pillars[$article['pillar']]->id,
                    'title' => $article['title'],
                    'excerpt' => $article['excerpt'],
                    'body' => $article['body'],
                    'published_at' => now()->subDays($article['days_ago']),
                    'is_featured' => $article['featured'],
                ],
            );
        }
    }

    private function seedBooks(): void
    {
        Book::updateOrCreate(
            ['slug' => 'entrusted'],
            [
                'title' => 'Entrusted',
                'subtitle' => 'Lessons, responsibilities and truths for a life of legacy',
                'description' => "Entrusted argues that the central question of leadership is not how much influence you can gather, but what you are prepared to be held accountable for.\n\nDrawing on years of building companies, sitting on boards, and mentoring founders, the book examines what changes when leaders treat authority as something held in trust rather than something earned and owned. It looks at succession, at the quiet decisions that determine whether an organisation is trustworthy, and at the cost of holding a standard when it would be cheaper not to.\n\nIt is written for people already carrying weight — founders, executives, board members, and those who will inherit institutions built by others.",
                'cover_image_path' => '/images/entrusted-mock.jpg',
                'is_featured' => true,
                'order' => 1,
            ],
        );

        Book::updateOrCreate(
            ['slug' => 'notes-on-building-builders'],
            [
                'title' => 'Notes on Building Builders',
                'subtitle' => 'Essays on mentorship, formation, and the middle of the work',
                'description' => "A collection in progress, gathering what has been learned across mentorship cohorts and private conversations with founders.\n\nThe essays deal with the part of building that is rarely documented: the middle, where the plan has met reality, the early energy has gone, and judgment has to carry the work. Expected to be published following Entrusted.",
                'cover_image_path' => null,
                'is_featured' => false,
                'order' => 2,
            ],
        );
    }

    private function seedImpactMetrics(): void
    {
        $metrics = [
            ['slug' => 'builders-mentored', 'label' => 'Builders mentored', 'value' => '1,200+', 'description' => 'Founders and emerging leaders reached through cohorts, sessions, and one-to-one conversations.', 'order' => 1],
            ['slug' => 'public-engagements', 'label' => 'Public engagements', 'value' => '80+', 'description' => 'Keynotes, lectures, panels, and interviews across industry, academic, and civic audiences.', 'order' => 2],
            ['slug' => 'communities-reached', 'label' => 'Communities reached', 'value' => '15+', 'description' => 'Communities touched through enterprise, mentorship, and Build Tomorrow programming.', 'order' => 3],
            ['slug' => 'research-themes', 'label' => 'Areas of practice', 'value' => '6', 'description' => 'Enterprise, leadership, governance, sustainability, mentorship, and public engagement.', 'order' => 4],
        ];

        foreach ($metrics as $metric) {
            ImpactMetric::updateOrCreate(['slug' => $metric['slug']], $metric);
        }
    }

    private function seedConvictions(): void
    {
        $convictions = [
            [
                'title' => 'Responsibility before visibility',
                'description' => 'A platform should never be heavier than the work behind it. Build the substance first; let attention follow it rather than replace it.',
                'order' => 1,
            ],
            [
                'title' => 'Institutions outlive individuals',
                'description' => 'A society grows stronger when its people build systems that survive personality, preference, and the founder\'s own energy.',
                'order' => 2,
            ],
            [
                'title' => 'Depth is a form of service',
                'description' => 'Clear thinking, careful language, and patient formation are practical public goods. Rigour is generosity, not decoration.',
                'order' => 3,
            ],
            [
                'title' => 'Africa is not a footnote',
                'description' => 'African experience, intelligence, and ambition belong in global rooms as a full contribution — not as context, and not as an exception.',
                'order' => 4,
            ],
        ];

        foreach ($convictions as $conviction) {
            Conviction::updateOrCreate(['title' => $conviction['title']], $conviction);
        }
    }

    private function seedQuotes(): void
    {
        $quotes = [
            ['text' => 'The task is not simply to build what is impressive, but what can be trusted.', 'source' => 'Femi Owoyele'],
            ['text' => 'Trust accumulates at the speed of demonstrated consistency. There is no faster route, and no substitute.', 'source' => 'Femi Owoyele'],
        ];

        foreach ($quotes as $quote) {
            Quote::updateOrCreate(['text' => $quote['text']], ['source' => $quote['source'], 'is_active' => true]);
        }
    }

    private function seedMedia($pillars): void
    {
        $media = collect([
            [
                'type' => 'video',
                'slug' => 'leadership-and-trust-keynote',
                'title' => 'Leadership and Trust',
                'description' => 'A public lecture on responsible influence, succession, and why durable institutions are built in the unglamorous decisions.',
                'url' => 'https://example.com/leadership-keynote',
                'pillar_id' => $pillars['speaking']->id,
                'published_at' => now()->subDays(30),
            ],
            [
                'type' => 'podcast',
                'slug' => 'building-builders-conversation',
                'title' => 'Building Builders: on mentorship and formation',
                'description' => 'A long-form conversation on what young founders actually need, and why encouragement is the least useful thing to offer them.',
                'url' => 'https://example.com/building-builders',
                'pillar_id' => $pillars['mentorship']->id,
                'published_at' => now()->subDays(45),
            ],
            [
                'type' => 'interview',
                'slug' => 'enterprise-and-the-long-horizon',
                'title' => 'Enterprise and the long horizon',
                'description' => 'An interview on stewardship in company building, and the cost of decisions made on a short clock.',
                'url' => 'https://example.com/long-horizon-interview',
                'pillar_id' => $pillars['enterprise']->id,
                'published_at' => now()->subDays(52),
            ],
            [
                'type' => 'image',
                'slug' => 'build-tomorrow-gallery-01',
                'title' => 'Build Tomorrow community session',
                'description' => 'From a community session with founders and emerging leaders.',
                'url' => null,
                'pillar_id' => $pillars['mentorship']->id,
                'published_at' => now()->subDays(60),
            ],
        ])->map(fn (array $item) => MediaItem::updateOrCreate(['slug' => $item['slug']], $item));

        $gallery = Gallery::updateOrCreate(
            ['title' => 'Build Tomorrow Gallery'],
            ['description' => 'A record of community, conference, and mentorship moments.'],
        );

        $gallery->mediaItems()->sync(
            $media->pluck('id')->mapWithKeys(fn ($id, $index) => [$id => ['order' => $index + 1]])->all()
        );
    }

    private function seedJournal($categories): void
    {
        $entries = [
            [
                'slug' => 'journal-on-clarity-and-work',
                'title' => 'On clarity and work',
                'excerpt' => 'Clarity does not remove difficulty. It removes waste, which is a different and more valuable thing.',
                'body' => "Clarity is often sold as a way of making work easier. It is not. Difficult work stays difficult once it is clearly defined; what changes is that the difficulty is now in the right place.\n\nWhat clarity actually removes is waste — the meetings that exist because nobody knows who decides, the projects that survive because no one wants to say they have stopped mattering, the effort spent maintaining an ambiguity that protects someone's comfort.\n\nThe test is simple. If you cannot say, in one sentence, what this work is for and who is accountable for it, the difficulty you are experiencing is probably not the work itself.",
                'category' => 'leadership',
                'days_ago' => 10,
            ],
            [
                'slug' => 'journal-notes-from-a-mentorship-room',
                'title' => 'Notes from a mentorship room',
                'excerpt' => 'What changes when young builders are given context instead of slogans.',
                'body' => "The most valuable moments in a mentorship room almost never arrive with volume. They begin quietly, when someone asks a question precise enough to be uncomfortable.\n\nA founder describes a decision they are avoiding. The room does not applaud, and it does not rescue them. Someone who has made that decision badly explains what it cost. Someone else asks what they would need to be true to move. In fifteen minutes the question has become smaller, sharper, and answerable.\n\nThat is the whole method. Not motivation — proximity to people who have already paid for the lesson, in a room where the honest answer is allowed.",
                'category' => 'enterprise',
                'days_ago' => 4,
            ],
            [
                'slug' => 'journal-the-cost-of-a-standard',
                'title' => 'The cost of a standard',
                'excerpt' => 'A standard nobody has ever paid for is not yet a standard. It is a preference.',
                'body' => "Every organisation says it has standards. The interesting question is when the standard last cost something.\n\nUntil a standard has caused a company to lose a contract, delay a launch, part with a talented person, or explain an uncomfortable decision to a board, it has not been tested. It is a stated preference, and stated preferences are abandoned quietly under pressure.\n\nThis is why standards should be written down before they are expensive, and defended the first time they are. The first defence is the one everyone remembers, and it sets the price of every defence after it.",
                'category' => 'governance',
                'days_ago' => 2,
            ],
        ];

        foreach ($entries as $entry) {
            JournalEntry::updateOrCreate(
                ['slug' => $entry['slug']],
                [
                    'title' => $entry['title'],
                    'excerpt' => $entry['excerpt'],
                    'body' => $entry['body'],
                    'category_id' => $categories[$entry['category']]->id,
                    'published_at' => now()->subDays($entry['days_ago']),
                ],
            );
        }
    }

    /**
     * Collection points for pre-ordered copies.
     *
     * The launch venue is confirmed; the rest are placeholders so the table has
     * shape. Replace them in the admin studio under Pickup Points before the
     * pre-order page goes live.
     */
    private function seedPickupPoints(): void
    {
        $points = [
            [
                'name' => 'Launch evening — Watercress Event Centre',
                'address' => '5 Alade Avenue, Allen, Ikeja',
                'city' => 'Lagos',
                'opening_hours' => 'Tuesday, 18 August 2026, from 4:00 p.m.',
                'note' => 'Collect your copy on the night of the unveiling.',
                'order' => 1,
            ],
            [
                'name' => 'Ikeja collection point',
                'address' => 'To be confirmed',
                'city' => 'Lagos',
                'opening_hours' => 'Weekdays, 10:00 a.m. – 5:00 p.m.',
                'note' => 'Placeholder — confirm address before launch.',
                'order' => 2,
            ],
            [
                'name' => 'Lekki collection point',
                'address' => 'To be confirmed',
                'city' => 'Lagos',
                'opening_hours' => 'Weekdays, 10:00 a.m. – 5:00 p.m.',
                'note' => 'Placeholder — confirm address before launch.',
                'order' => 3,
            ],
        ];

        foreach ($points as $point) {
            PickupPoint::updateOrCreate(['name' => $point['name']], $point);
        }
    }

    private function contentBlocks(): array
    {
        return [
            // ------------------------------------------------------------ home
            [
                'context' => 'home',
                'slug' => 'home.hero',
                'title' => 'Building what can be trusted.',
                'body' => 'Enterprise, governance, sustainability, mentorship, and authorship — one practice, approached from different rooms, and held together by a single conviction: institutions outlive individuals, and they deserve to be built well.',
                'meta' => [
                    'kicker' => 'Enterprise · Leadership · Stewardship',
                    'image' => '/images/femi-portrait.jpg',
                ],
                'order' => 1,
            ],
            [
                'context' => 'home',
                'slug' => 'home.intro',
                'title' => 'A platform for serious work.',
                'body' => 'This is where the work is gathered: the companies, the essays, the books, the mentorship rooms, and the public conversations. It is designed to grow with the work rather than around it — calm in tone, rigorous in substance, rooted in African experience, and open to the world.',
                'meta' => [],
                'order' => 2,
            ],
            [
                'context' => 'home',
                'slug' => 'home.launch',
                'title' => 'Entrusted',
                'body' => 'On the evening of his fortieth birthday, Femi Owoyele will unveil his first book. The two occasions belong together: forty years of being formed, and a book about what it means to be handed something worth keeping.',
                'meta' => [
                    // Swapped in automatically once `starts_at` has passed, so the
                    // section stops speaking in the future tense on its own.
                    'body_after' => 'Entrusted was unveiled on the evening of Femi Owoyele\'s fortieth birthday, before family, friends, mentors, and the builders who have shaped the work. The two occasions belonged together: forty years of being formed, and a book about what it means to be handed something worth keeping.',
                    'occasion' => 'Celebrating Forty Years',
                    'tagline' => 'An evening of gratitude and great beginnings',
                    'subtitle' => 'Lessons, responsibilities and truths for a life of legacy',
                    // ISO-8601 with the West Africa Time offset — the countdown and
                    // the "already launched" state are both derived from this.
                    'starts_at' => '2026-08-18T16:00:00+01:00',
                    'date_label' => 'Tuesday, 18 August 2026',
                    'time_label' => '4:00 p.m.',
                    'venue' => 'Watercress Event Centre',
                    'address' => '5 Alade Avenue, Allen, Ikeja, Lagos',
                    'image' => '/images/entrusted-mock.jpg',
                    'book_slug' => 'entrusted',
                    // Add 'rsvp_phone' => '...' here to publish a direct RSVP line;
                    // while it is absent, RSVPs are routed through the contact form.
                ],
                'order' => 4,
            ],
            [
                'context' => 'home',
                'slug' => 'home.footer-statement',
                'title' => 'The long view',
                'body' => 'The work continues. Follow it as it unfolds.',
                'meta' => [],
                'order' => 3,
            ],

            // ----------------------------------------------------------- about
            [
                'context' => 'about',
                'slug' => 'about.identity',
                'title' => 'One practice, several rooms',
                'body' => 'Femi Owoyele builds organisations, advises boards, mentors founders, writes, and speaks. On paper these look like separate careers. In practice they are one question asked in different settings: what does it take to build something that deserves to be trusted, and that will still be standing once the people who built it have gone?',
                'meta' => [],
                'order' => 1,
            ],
            [
                'context' => 'about',
                'slug' => 'about.formation',
                'title' => 'Where the conviction comes from',
                'body' => 'The through-line was formed in the ordinary work of building: watching organisations that depended entirely on one person, and organisations that had outgrown that dependence. The difference was almost never talent. It was whether standards had been written down, tested, and defended when defending them was expensive.',
                'meta' => [],
                'order' => 2,
            ],
            [
                'context' => 'about',
                'slug' => 'about.outlook',
                'title' => 'African in identity, global in outlook',
                'body' => 'The work is rooted in African realities — the institutions being built now, the talent arriving faster than the systems to hold it, the long horizon that growing economies cannot afford to ignore. It is also held to global standards, and offered into global conversation as a full contribution rather than as local colour.',
                'meta' => [],
                'order' => 3,
            ],

            // -------------------------------------------------- build tomorrow
            [
                'context' => 'build_tomorrow',
                'slug' => 'build-tomorrow.vision',
                'title' => 'Vision',
                'body' => "Build Tomorrow exists for the people who will run the institutions of the next thirty years, and who are currently being handed responsibility faster than they are being handed context.\n\nIt is a platform for builders: a convening, a community, and a growing body of resources for those shaping companies, institutions, and public life.",
                'meta' => [],
                'order' => 1,
            ],
            [
                'context' => 'build_tomorrow',
                'slug' => 'build-tomorrow.conference',
                'title' => 'Conference',
                'body' => "An annual convening designed around working sessions rather than showcases: practical frameworks, honest post-mortems, and intergenerational exchange between people who have built and people who are building.\n\nThe programme is deliberately small enough that attendees can be participants rather than an audience.",
                'meta' => [],
                'order' => 2,
            ],
            [
                'context' => 'build_tomorrow',
                'slug' => 'build-tomorrow.community',
                'title' => 'Community',
                'body' => "A network of builders who value discipline, service, and long-term contribution — and who are willing to be useful to each other between events.\n\nMembership is built around contribution rather than status: what you are working on, and what you can help someone else carry.",
                'meta' => [],
                'order' => 3,
            ],
            [
                'context' => 'build_tomorrow',
                'slug' => 'build-tomorrow.publications',
                'title' => 'Publications',
                'body' => "Essays, session notes, and resources that extend the conversation past a single event and make it useful to people who could not be in the room.\n\nEverything published is written to still be worth reading a year later.",
                'meta' => [],
                'order' => 4,
            ],

            // -------------------------------------------------------- speaking
            [
                'context' => 'speaking',
                'slug' => 'speaking.topics',
                'title' => 'Themes',
                'body' => 'Leadership and trust. Enterprise as stewardship. The long work of institution building. Mentorship and formation. Sustainability as an operating decision rather than a disclosure exercise. Each session is written for the specific room rather than delivered from a standing deck.',
                'meta' => [
                    'audiences' => ['boards and executive teams', 'universities', 'founders and operators', 'public institutions', 'faith and civic communities'],
                ],
                'order' => 1,
            ],
            [
                'context' => 'speaking',
                'slug' => 'speaking.formats',
                'title' => 'Formats',
                'body' => 'Keynote addresses, university lectures, moderated panels, fireside conversations, and closed leadership sessions with executive teams or boards. Sessions run from a twenty-minute address to a half-day working format, and can include a written follow-up for participants.',
                'meta' => [],
                'order' => 2,
            ],
            [
                'context' => 'speaking',
                'slug' => 'speaking.engagements',
                'title' => 'How engagements work',
                'body' => 'Invitations are accepted selectively so that each one can be properly prepared. Expect a short conversation before confirmation to understand the audience and the outcome, and a session shaped around that rather than a general talk with the organisation\'s name on the first slide.',
                'meta' => [],
                'order' => 3,
            ],

            // ------------------------------------------------------ mentorship
            [
                'context' => 'mentorship',
                'slug' => 'mentorship.philosophy',
                'title' => 'Mentorship as formation',
                'body' => 'Mentorship here is closer to apprenticeship than to advice. The aim is not encouragement — it is judgment: helping builders develop the discipline, context, and courage to carry the responsibility they are reaching for, before the stakes force the lesson.',
                'meta' => [],
                'order' => 1,
            ],
            [
                'context' => 'mentorship',
                'slug' => 'mentorship.programmes',
                'title' => 'Programmes and resources',
                'body' => 'Building Builders runs as small, application-based cohorts alongside one-to-one conversations, written resources, and open sessions. Cohorts are intentionally small so that every participant\'s actual work can be examined rather than discussed in the abstract.',
                'meta' => [],
                'order' => 2,
            ],
            [
                'context' => 'mentorship',
                'slug' => 'mentorship.expectations',
                'title' => 'What is expected',
                'body' => 'Participants bring real work and real questions, arrive prepared, and are willing to have a judgment pushed back on. In return, sessions are honest about cost, specific about trade-offs, and free of the compression that makes most public storytelling about building unusable.',
                'meta' => [],
                'order' => 3,
            ],

            // ---------------------------------------------------------- impact
            [
                'context' => 'impact',
                'slug' => 'impact.overview',
                'title' => 'Impact',
                'body' => 'Numbers are only ever part of the picture. Read together, these figures mark the shape of a body of work still being built — people formed, rooms addressed, and communities reached.',
                'meta' => [],
                'order' => 1,
            ],
        ];
    }
}
