# FemiOwoyele.com

Professional content platform for FemiOwoyele.com, implemented from `femiowoyele-tech-spec.md`.

## Structure

- `backend/` - Laravel JSON API under `/api`, Sanctum admin auth, migrations, seeders, feature tests.
- `frontend/` - Vue 3 + Vite SPA, Vue Router, Pinia, Tailwind CSS, Axios, VeeValidate forms, public pages, admin CMS shell.

## Local Setup

The most reliable local setup uses Vite's dev proxy: the browser talks to `http://127.0.0.1:5173`, and Vite forwards `/api` requests to Laravel at `http://127.0.0.1:8000`.

Detailed guide: [Local development guide](docs/local-development.md)

Backend:

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=127.0.0.1 --port=8000
```

Frontend:

```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

Seeded admin:

```text
admin@femiowoyele.com / password
```

## MySQL

The default backend environment is configured for MySQL 8.x. A local MySQL service is provided in `docker-compose.yml`.

```bash
docker compose up -d mysql
```

## Verification

```bash
cd backend && php artisan test
cd frontend && npm test && npm run build
E2E_BASE_URL=http://127.0.0.1:5173 npm run test:e2e
```

## Deployment

- [Railway deployment guide](docs/railway-deployment.md)
- [Namecheap shared hosting deployment guide](docs/namecheap-shared-hosting-deployment.md)

## Implementation Note

Laravel 11 could not be downloaded because Packagist DNS resolution was unavailable during implementation. The backend uses the locally available Laravel 12 + Sanctum scaffold, documented in `backend/docs/adr/0001-monorepo-laravel-vue.md`.
