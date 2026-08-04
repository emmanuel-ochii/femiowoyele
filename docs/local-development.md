# Local Development Guide

Use this guide to run the Laravel backend and Vue frontend locally without port or address handshake issues.

## Local URLs

Use these URLs consistently:

```text
Frontend: http://127.0.0.1:5173
Backend:  http://127.0.0.1:8000
API:      http://127.0.0.1:5173/api
```

In local development, the browser calls `/api` on the Vite frontend server. Vite proxies those requests to Laravel on port `8000`. This keeps the browser on one origin and avoids most CORS problems.

## First-Time Setup

From the project root:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem
```

Install backend dependencies:

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
```

Install frontend dependencies:

```bash
cd ../frontend
npm install
cp .env.example .env
```

## Start The Database

From the project root:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem
docker compose up -d mysql
```

The local MySQL credentials are:

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=femiowoyele
DB_USERNAME=femiowoyele
DB_PASSWORD=secret
```

These match `backend/.env.example`.

## Prepare The Backend Database

Run:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem/backend
php artisan migrate --seed
```

Seeded local CMS login:

```text
admin@femiowoyele.com / password
```

## Start The Backend

Open a terminal tab and run:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem/backend
php artisan serve --host=127.0.0.1 --port=8000
```

Check the backend health endpoint:

```bash
curl http://127.0.0.1:8000/api/health
```

Expected result:

```text
"status":"ok"
```

## Start The Frontend

Open a second terminal tab and run:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem/frontend
npm run dev
```

Open:

```text
http://127.0.0.1:5173
```

The frontend `.env` should contain:

```dotenv
VITE_API_BASE_URL=/api
VITE_API_PROXY_TARGET=http://127.0.0.1:8000
```

That means frontend code calls `/api`, and Vite forwards the request to Laravel.

## Daily Startup

After first-time setup, use this order:

1. Start MySQL:

```bash
docker compose up -d mysql
```

2. Start Laravel:

```bash
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```

3. Start Vue:

```bash
cd frontend
npm run dev
```

4. Visit:

```text
http://127.0.0.1:5173
```

## If A Port Is Already In Use

If backend port `8000` is busy, start Laravel on another port:

```bash
php artisan serve --host=127.0.0.1 --port=8001
```

Then update `frontend/.env`:

```dotenv
VITE_API_PROXY_TARGET=http://127.0.0.1:8001
```

Restart the frontend dev server after changing `frontend/.env`.

If frontend port `5173` is busy, stop the existing Vite server or run:

```bash
npm run dev -- --port 5174
```

If you use port `5174`, make sure `backend/.env` allows it:

```dotenv
CORS_ALLOWED_ORIGINS=http://localhost:5173,http://127.0.0.1:5173,http://127.0.0.1:5174,http://127.0.0.1:5175
```

The default example already includes `5174` and `5175`.

## Verification

Backend tests:

```bash
cd backend
php artisan test
```

Frontend tests and build:

```bash
cd frontend
npm test
npm run build
```

End-to-end tests:

```bash
cd frontend
E2E_BASE_URL=http://127.0.0.1:5173 npm run test:e2e
```

## Troubleshooting

### The frontend says the API failed

Check Laravel is running:

```bash
curl http://127.0.0.1:8000/api/health
```

If that fails, start the backend first.

### Browser shows CORS errors

Use the Vite proxy setup:

```dotenv
VITE_API_BASE_URL=/api
VITE_API_PROXY_TARGET=http://127.0.0.1:8000
```

Then restart:

```bash
npm run dev
```

Avoid mixing these unless you know why:

```text
localhost
127.0.0.1
0.0.0.0
```

For this project, use `127.0.0.1` locally.

### Laravel cannot connect to MySQL

Start the database:

```bash
docker compose up -d mysql
```

Confirm `backend/.env` has:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=femiowoyele
DB_USERNAME=femiowoyele
DB_PASSWORD=secret
```

Then run:

```bash
php artisan migrate --seed
```

### Changes to .env are ignored

Clear Laravel config:

```bash
cd backend
php artisan config:clear
```

Restart the Laravel server.

For frontend `.env` changes, restart the Vite dev server.

### Admin login fails

Confirm the database was seeded:

```bash
cd backend
php artisan migrate --seed
```

Use:

```text
admin@femiowoyele.com / password
```

### Frontend still calls the wrong backend URL

Check:

```bash
cd frontend
cat .env
```

For local development, use:

```dotenv
VITE_API_BASE_URL=/api
VITE_API_PROXY_TARGET=http://127.0.0.1:8000
```

Restart `npm run dev` after editing the file.
