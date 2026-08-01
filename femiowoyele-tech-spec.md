# Technical Specification: FemiOwoyele.com Web Application

## 1. Project Overview

FemiOwoyele.com is a professional, content-focused web application that serves as the digital home of an evolving body of work spanning enterprise, leadership, governance, sustainability, mentorship, scholarship, authorship, and public engagement.
The site must communicate clarity, substance, elegance, and longevity, with a design language closer to a premium institutional website than a personal branding site.

The implementation will use Vue.js for the frontend and Laravel for the backend, with MySQL as the primary data store for local development and production.

### 1.1 Core Goals

- Present a coherent philosophy and mission rather than a conventional personal résumé.
- Communicate an intelligent, timeless, purpose-driven, and calm brand personality.
- Provide a clean, minimalist, highly readable experience across devices.
- Enable scalable content management for articles, books, events, media, and impact metrics.
- Ensure maintainable, testable, and secure architecture suitable for future expansion.

### 1.2 Key Sections and Content Domains

From the design brief, the site will support the following major content areas:

- Homepage (Hero, Intro, Pillars, Featured, Quote, Footer statement)
- About (Who I Am, Guiding Convictions)
- My Work (Enterprise, Leadership, Governance, Sustainability, Mentorship, Speaking)
- Build Tomorrow (sub-brand with multiple sub-sections)
- Research & Ideas (articles grouped by categories)
- Books (current and future works; “Entrusted” as primary feature)
- Speaking (topics, audiences, engagements, media, enquiry form)
- Mentorship (Building Builders, resources, programmes, applications)
- Impact (evolving statistics and highlights)
- Media (interviews, TV, podcasts, videos, gallery, downloads)
- Journal (essays, reflections, insights)
- Contact (multi-purpose contact form, newsletter)


## 2. Technology Stack

### 2.1 Frontend

- Framework: Vue.js 3 (Composition API)
- Router: Vue Router 4
- State Management: Pinia (preferred) or Vuex 4
- UI Layer:
  - Tailwind CSS for utility-first, consistent styling
  - Minimal custom SCSS modules for global variables and overrides
  - Optional component library: Headless UI or Radix Vue for accessible primitives (modals, dialogs, dropdowns)
- Build Tool: Vite
- HTTP Client: Axios for REST API interaction
- Form Handling & Validation: VeeValidate or custom composables based on native constraints

### 2.2 Backend

- Framework: Laravel 11 (or latest LTS at project start)
- Language: PHP 8.2+
- API Style: RESTful JSON API
- Authentication: Laravel Sanctum (SPA token-based auth)
- Authorization: Laravel Gates/Policies
- Caching: Laravel Cache with file/Redis driver (configurable)
- Queue: Laravel Queues for email sending and background jobs (optional for v1, but structure endpoints for async capability)

### 2.3 Database & Storage

- Database: MySQL 8.x (local and production)
- ORM: Eloquent
- Migrations & Seeders: Laravel migrations and seed classes for initial content (e.g., pillars, categories)
- Storage:
  - Local filesystem in development
  - Cloud storage (e.g., AWS S3) in production via Laravel Filesystem abstraction

### 2.4 Tooling & DevOps

- Version Control: Git, GitHub repository
- Local Development:
  - Laravel Sail or Docker Compose for PHP, MySQL, and Node services
  - Node.js (LTS) for frontend build tooling
- Testing:
  - PHPUnit and PestPHP for backend
  - Vitest for frontend unit tests
  - Playwright or Cypress for end-to-end tests (critical flows only)


## 3. High-Level Architecture

### 3.1 Overall Architecture

- SPA/Front-end:
  - Vue SPA served via Laravel or separate Node-based static asset server.
  - Vue Router handles client-side navigation; Laravel serves API endpoints under /api.
- Backend API:
  - Laravel as REST API provider consumed by Vue.
  - Authentication via Sanctum for protected admin endpoints.
- Database Layer:
  - Eloquent models mapping directly to normalized MySQL tables.
  - Clear separation between content entities (articles, books, media, impact stats) and taxonomy entities (categories, tags).

### 3.2 Modules / Domains

Organize backend and frontend by domains matching major site areas:

- Core / Shared (navigation, footer, quotes, site settings)
- Identity (About, Guiding Convictions)
- Work (pillars and their pages: Enterprise, Leadership, etc.)
- BuildTomorrow
- ResearchIdeas
- Books
- Speaking
- Mentorship
- Impact
- Media
- Journal
- Contact & Newsletter
- Auth & Admin (protected content management area)


## 4. Development Phases

### Phase 1: Foundations & Infrastructure

- Initialize Laravel and Vue codebases (monorepo with /backend and /frontend or unified Laravel+Vite setup).
- Configure MySQL, environment variables, and connection.
- Set up authentication (Sanctum) and basic user model (admin only for v1).
- Implement core layout shell and base styles (colors, typography, spacing).
- Define routing structure (frontend and backend endpoints).

### Phase 2: Core Content & Public Pages

- Implement homepage sections:
  - Hero, Intro, Pillars, Featured, Quote, Footer statement.
- Implement About (Who I Am, Guiding Convictions).
- Implement My Work index and individual pillar pages.
- Implement Research & Ideas listing and article detail pages.
- Implement Books section with “Entrusted” as featured book.

### Phase 3: Extended Sections & Interactions

- Implement Build Tomorrow microsite structure.
- Implement Speaking and Mentorship pages (including enquiry/programme forms).
- Implement Impact, Media, and Journal sections.
- Implement Contact and newsletter subscription flows.
- Add site-wide search (optional v2, but design API contracts early).

### Phase 4: Admin CMS & Content Management

- Build a minimal, secure admin dashboard (protected routes) for:
  - Managing articles, books, media.
  - Managing pillars, categories, convictions, quotes.
  - Managing impact metrics and featured content.
- Implement role-based access (admin, editor if needed later).

### Phase 5: Optimization, Testing & Deployment

- Frontend performance pass (lazy loading, code splitting, caching).
- Backend query optimization and caching.
- Comprehensive testing of core flows.
- Production build, environment configuration, and deployment pipeline setup.


## 5. UI/UX Guidelines

### 5.1 Brand Personality & Tone

- Intelligent, calm, and refined; avoid loud, promotional patterns.
- Scholarly without appearing academic; professional without appearing corporate.
- African in identity but global in outlook; subtle cues in imagery and microcopy.

### 5.2 Visual Design Language

- Prefer minimalist layouts with large white space and clear hierarchy.
- Avoid unnecessary icons, flashy graphics, and excessive colours.
- Use authentic photography that reflects real engagements (speaking, teaching, mentorship, community).

### 5.3 Colour Palette

Implement the following palette in Tailwind config (custom theme):

- Primary:
  - Deep Navy (e.g., #0B1C32 – confirm exact hex during design exploration)
  - White (#FFFFFF)
  - Forest Green (e.g., #114B3A)
- Secondary:
  - Warm Grey (e.g., #C0B6A4)
  - Very Light Sand (e.g., #F4EFE7)
- Accent:
  - Muted Gold (e.g., #C9A45C), used sparingly for highlights, buttons, and small UI accents.

Colours should subtly communicate leadership, growth, peace, wisdom, stewardship, and hope.

### 5.4 Typography

- Headings: Modern serif font (e.g., "Georgia", "Cormorant Garamond", or similar, loaded via webfont).
- Body: Clean sans-serif (e.g., "Inter", "Source Sans 3", or similar).
- Headlines should feel editorial and considered.
- Avoid decorative fonts and avoid all-caps except for small labels or navigation.

### 5.5 Layout & Spacing

- Use a responsive grid (e.g., max-width 1200–1400px content width, 16–24px base spacing scale).
- Maintain generous vertical spacing between sections to let content “breathe”.
- On desktop, prefer 2–3 column layouts for content-heavy sections (e.g., Research & Ideas list, Media gallery).
- On mobile, stack sections with consistent padding and avoid overly long blocks of text.

### 5.6 Interaction & Animation

- Subtle motion only (fade-ins, gentle translateY, opacity transitions at low durations, e.g., 200–300ms).
- Avoid parallax, excessive scroll-jacking, or distracting animations.
- Hover states: small colour/contrast changes and subtle scale (max 1.02) where appropriate.

### 5.7 Responsive Design

- Mobile-first approach in CSS and layout.
- Ensure all typography scales appropriately from mobile to desktop (e.g., clamp() for fluid type).
- Navigation:
  - Desktop: horizontal menu with clear hierarchy.
  - Mobile: accessible hamburger menu (focus states, ARIA attributes, keyboard navigation).
- Test across key breakpoints: 360px, 768px, 1024px, 1280px.


## 6. Frontend Structure

### 6.1 High-Level Routing (Vue Router)

Example routes (to be refined):

- `/` – Homepage
- `/about` – About & Guiding Convictions
- `/work` – My Work overview
- `/work/:pillarSlug` – Enterprise, Leadership, Governance, Sustainability, Mentorship, Speaking
- `/build-tomorrow` – Build Tomorrow overview
- `/build-tomorrow/:section` – Vision, Conference, Community, Gallery, Media, Publications, Partners, Future Plans
- `/research-ideas` – Articles listing
- `/research-ideas/:slug` – Article detail
- `/books` – Books listing
- `/books/:slug` – Book detail (Entrusted as primary)
- `/speaking` – Speaking page
- `/mentorship` – Mentorship page
- `/impact` – Impact metrics
- `/media` – Media hub (interviews, videos, gallery)
- `/journal` – Journal listing
- `/journal/:slug` – Journal entry detail
- `/contact` – Contact page
- `/admin/*` – Admin routes (protected)

### 6.2 Component Hierarchy

- Layout Components:
  - `AppLayout.vue` (header, footer, global messages)
  - `PublicLayout.vue` (for all public pages)
  - `AdminLayout.vue` (for admin dashboard)
- Shared Components:
  - `SiteHeader.vue`, `SiteFooter.vue`, `PrimaryButton.vue`, `SecondaryButton.vue`
  - `SectionWrapper.vue` (consistent padding and max-width)
  - `Card.vue`, `PillarCard.vue`, `ArticleCard.vue`, `BookCard.vue`, `StatisticCard.vue`
  - `QuoteBanner.vue`
  - `MediaGallery.vue`, `Pagination.vue`, `TagList.vue`
- Page Components:
  - `HomePage.vue`, `AboutPage.vue`, `WorkOverviewPage.vue`, `PillarPage.vue`, etc.

Components should be designed for reuse across domains (e.g., cards for articles vs journal entries with minor variants).

### 6.3 State Management

Use Pinia stores (or Vuex modules if preferred) per domain:

- `useSiteStore` (navigation items, quotes, global settings)
- `useContentStore` (articles, books, media, impact stats)
- `useAuthStore` (admin user, tokens)

Prefer server-driven pagination and filtering for lists (articles, media) to avoid loading large datasets.


## 7. Backend API Design

### 7.1 Conventions

- Base URL: `/api`
- JSON:API-inspired structure but simplified (no need for full JSON:API spec).
- Naming:
  - Resources plural: `/api/articles`, `/api/books`.
  - Individual resources: `/api/articles/{id}` or `/api/articles/{slug}` (slug-based for public consumption where appropriate).

### 7.2 Example Endpoints

Public endpoints (no auth):

- `GET /api/home` – returns hero content, intro, pillars, featured items, quote, footer statement.
- `GET /api/about` – returns core identity narrative and guiding convictions.
- `GET /api/pillars` – list of pillars.
- `GET /api/pillars/{slug}` – detailed pillar page (overview, projects, gallery, articles, testimonials).
- `GET /api/research-ideas` – paginated list of articles with filters (category, search).
- `GET /api/research-ideas/{slug}` – single article detail.
- `GET /api/books` – list of books (with featured flag and order).
- `GET /api/books/{slug}` – book detail.
- `GET /api/build-tomorrow` – Build Tomorrow overview & sub-sections.
- `GET /api/speaking` – speaking topics, audiences, previous engagements, media.
- `GET /api/mentorship` – mentorship philosophy, resources, programmes.
- `GET /api/impact` – current impact statistics.
- `GET /api/media` – media items (interviews, podcasts, videos, gallery).
- `GET /api/journal` – paginated journal entries.
- `GET /api/journal/{slug}` – journal entry detail.
- `POST /api/contact` – contact form submissions.
- `POST /api/newsletter/subscribe` – newsletter subscription.

Admin endpoints (auth required via Sanctum):

- CRUD endpoints for:
  - Articles, Journal entries, Books
  - Pillars, Projects, Impact stats
  - Media items, Quotes, Convictions
  - Build Tomorrow content blocks
- Example:
  - `POST /api/admin/articles`, `PUT /api/admin/articles/{id}`, `DELETE /api/admin/articles/{id}`

### 7.3 Request/Response Patterns

- Standard structure:
  - Success: `{ "data": <payload>, "meta": { ...optional } }`
  - Error: `{ "message": "Error description", "errors": { field: [messages] } }`
- Validation errors return HTTP 422 with field-level details.


## 8. Database Schema Considerations

### 8.1 Core Entities (Indicative)

- `users`
  - `id`, `name`, `email`, `password`, `role` (admin/editor), timestamps.

- `pillars`
  - `id`, `slug`, `title`, `subtitle`, `description`, `order`, timestamps.

- `projects`
  - `id`, `pillar_id`, `title`, `slug`, `summary`, `content`, timestamps.

- `articles`
  - `id`, `slug`, `title`, `excerpt`, `body`, `category_id`, `published_at`, `is_featured`, timestamps.

- `categories`
  - `id`, `slug`, `name`, `description`, timestamps.

- `books`
  - `id`, `slug`, `title`, `subtitle`, `description`, `cover_image_path`, `is_featured`, `order`, timestamps.

- `media_items`
  - `id`, `type` (interview, tv, podcast, video, image, download), `title`, `slug`, `description`, `url`, `thumbnail_path`, timestamps.

- `impact_metrics`
  - `id`, `slug`, `label`, `value`, `description`, `order`, timestamps.

- `quotes`
  - `id`, `text`, `source`, `is_active`, timestamps.

- `convictions`
  - `id`, `title`, `description`, `order`, timestamps.

- `journal_entries`
  - `id`, `slug`, `title`, `excerpt`, `body`, `published_at`, timestamps.

- `contact_messages`
  - `id`, `name`, `email`, `subject`, `message`, `type` (speaking, consulting, research, partnership, media, general), timestamps.

- `newsletter_subscribers`
  - `id`, `email`, `name`, `source`, timestamps.

- `galleries`
  - `id`, `title`, `description`, timestamps.

- `gallery_items`
  - `id`, `gallery_id`, `media_item_id`, `order`, timestamps.

- `content_blocks`
  - `id`, `slug`, `title`, `body`, `context` (home_about, build_tomorrow_vision, etc.), timestamps.

### 8.2 Relationships

- `Pillar` hasMany `Project`, `Article`, `MediaItem` (through an optional pivot or direct FK).
- `Category` hasMany `Article` and `JournalEntry`.
- `Gallery` hasMany `MediaItem` through `gallery_items` pivot.
- `User` creates/updates content via `created_by`, `updated_by` fields (optional for v1).

### 8.3 Indexing & Performance

- Index on `slug` for all public-facing resources.
- Index on `published_at` for ordering and filtering.
- Index on `type` and `created_at` for media.
- Ensure foreign keys with cascading deletes where appropriate.


## 9. Security Best Practices

- Use Laravel Sanctum for SPA authentication; CSRF protection for browser-based interactions.
- Implement role-based authorization using policies/gates.
- Validate all incoming requests using Laravel Form Requests.
- Enforce HTTPS in production; use HSTS headers via middleware.
- Rate-limit sensitive endpoints (login, contact form, newsletter) to prevent abuse.
- Sanitize rich-text inputs server-side and encode outputs properly; avoid arbitrary HTML injection.
- Store credentials and secrets in environment variables, never in source control.
- Use parameterized queries via Eloquent/Query Builder to avoid SQL injection.
- Implement proper file upload validation (mime-type, size, storage path) for media.


## 10. Testing Strategy

### 10.1 Backend Testing

- Feature tests for all public API endpoints (happy path + error conditions).
- Feature tests for admin CRUD operations with authentication and authorization checks.
- Unit tests for domain logic (e.g., selecting featured items, rotating quotes).

### 10.2 Frontend Testing

- Unit tests for core components (cards, forms, layout components) using Vitest.
- Snapshot tests for key views to detect unexpected layout changes.
- E2E tests for critical flows:
  - Viewing homepage and navigation between major sections.
  - Submitting contact and speaking enquiry forms.
  - Viewing Research & Ideas article list and details.

### 10.3 Performance & Accessibility

- Run Lighthouse audits and address major issues (performance, accessibility, SEO, best practices).
- Ensure semantic HTML and ARIA attributes for navigation, modals, and interactive elements.


## 11. Performance & Optimization

- Use Vite code splitting and dynamic imports for route-based chunking.
- Lazy-load heavy sections (e.g., media galleries) and large images with responsive `srcset`.
- Use caching headers and Laravel response caching where safe (e.g., home content, lists).
- Optimize DB queries with eager loading and pagination.
- Minimize global state; prefer local state where possible in components.


## 12. Maintainability & Code Quality

- Enforce coding standards:
  - PHP-CS-Fixer or Laravel Pint for backend.
  - ESLint + Prettier for frontend.
- Adopt a clear folder-by-domain structure on both frontend and backend.
- Keep components and controllers small and focused; follow single responsibility principle.
- Use DTOs or Resources (Laravel API Resources) to shape API responses consistently.
- Document key architectural decisions in a short ADR (Architecture Decision Record) format.


## 13. Deployment Considerations

- Environments: local, staging, production.
- Use `.env` per environment with separate DB credentials and API keys.
- Build steps:
  - `composer install --no-dev` and `php artisan config:cache` for backend.
  - `npm install` and `npm run build` for frontend assets.
- Database migrations and seeders run automatically or via CI/CD before deploying new releases.


## 14. Future Enhancements (Out of Scope v1, but Supported by Design)

- Full-text search across articles, journal, and media.
- Multi-language support (e.g., English + additional languages).
- Event/calendar module for conferences and community programmes.
- User accounts for community features (e.g., Build Tomorrow community portal).

This specification is intended as a clear, implementation-focused guide for a coding agent to build and maintain the FemiOwoyele.com platform using Vue.js, Laravel, and MySQL while adhering closely to the design and brand brief.