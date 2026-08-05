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

## Email notifications

RSVP and contact submissions are emailed to the team via [Resend](https://resend.com).
Delivery is best-effort: if the provider is unreachable the submission is still
stored and the failure is logged, so a guest never loses their RSVP.

```dotenv
MAIL_MAILER=resend                 # `log` locally writes the email to storage/logs
RESEND_API_KEY=re_xxxxxxxx
MAIL_FROM_ADDRESS="hello@femiowoyele.com"
MAIL_NOTIFY_TO=faithdolapo27@gmail.com
MAIL_NOTIFY_CC=profemative@gmail.com   # comma-separated for several people
```

Before switching `MAIL_MAILER` to `resend` in production, verify the sending
domain at <https://resend.com/domains>. Resend rejects a `MAIL_FROM_ADDRESS` on
an unverified domain, so `hello@femiowoyele.com` only works once
`femiowoyele.com` is verified there.

Preview an email locally without sending it:

```bash
cd backend
MAIL_MAILER=log php artisan serve   # submissions render into storage/logs/laravel.log
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
