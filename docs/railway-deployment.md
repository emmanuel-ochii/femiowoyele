# Railway Deployment Guide

This guide explains how to deploy FemiOwoyele.com to Railway and point the Namecheap domain to Railway cleanly.

It is written as a step-by-step runbook. Follow it in order.

## Recommended Production Setup

Use Railway for hosting and Namecheap only for domain/DNS.

```text
Namecheap
  Domain registration
  DNS records
  Optional email records

Railway
  Frontend service: Vue/Vite static site
  Backend service: Laravel JSON API
  MySQL service: production database
  Optional worker service: Laravel queue worker
  Optional cron service: Laravel scheduler
```

Recommended public URLs:

```text
Frontend: https://femiowoyele.com
Admin:    https://femiowoyele.com/admin
Backend:  https://api.femiowoyele.com
API:      https://api.femiowoyele.com/api
```

Why this is the cleanest setup:

- The Vue frontend and Laravel backend are separated clearly.
- Railway handles HTTPS certificates.
- Railway handles GitHub-based deployments.
- The Laravel API has its own domain.
- The frontend knows exactly where the API lives.
- Namecheap remains simple: it only points DNS records to Railway.

## Files Added For Railway

This repository includes Railway-specific support files:

```text
backend/railway.json
backend/railway/init-app.sh
backend/railway/run-worker.sh
backend/railway/run-cron.sh
frontend/Dockerfile
frontend/Caddyfile
frontend/railway.json
```

What they do:

```text
backend/railway.json
  Configures Laravel pre-deploy migration/cache commands and /api/health healthcheck.

backend/railway/init-app.sh
  Runs optimize:clear, migrations, config cache, event cache, and view cache.

backend/railway/run-worker.sh
  Runs a Laravel queue worker. Use later when queues become important.

backend/railway/run-cron.sh
  Runs Laravel schedule:run every 60 seconds. Use later when scheduled jobs exist.

frontend/Dockerfile
  Builds the Vue app and serves the production dist folder with Caddy.
  It declares VITE_API_BASE_URL as a Docker build argument because Vite embeds
  frontend variables at build time.

frontend/Caddyfile
  Serves the Vue SPA and falls back to index.html for routes like /about and /admin.

frontend/railway.json
  Tells Railway to build the frontend with the Dockerfile and healthcheck /.
```

Do not delete these files unless the deployment strategy changes.

## Deployment Worksheet

Fill this in before starting.

```text
Railway account email:
  ______________________________

GitHub repository:
  https://github.com/emmanuel-ochii/femiowoyele.git

Railway project name:
  femiowoyele

Railway environment:
  production

Frontend service name:
  frontend

Backend service name:
  backend

Database service name:
  MySQL

Frontend domain:
  femiowoyele.com

Optional www domain:
  www.femiowoyele.com

Backend/API domain:
  api.femiowoyele.com

Canonical domain:
  femiowoyele.com

Primary region:
  EU West / Amsterdam

APP_KEY:
  generated later

SMTP provider:
  ______________________________
```

For a Nigeria/Africa-facing audience, start with Railway's EU West / Amsterdam region. It is geographically closer than US regions and usually a good default. After launch, measure real response times before changing regions.

## Important Rules

- Do not deploy secrets in source code.
- Do not commit `.env` files.
- Do not put production database credentials in GitHub.
- Use Railway service variables for secrets.
- Keep backend and MySQL in the same Railway project and region.
- Use `api.femiowoyele.com` for the Laravel backend.
- Use `femiowoyele.com` for the Vue frontend.
- Do not run `php artisan route:cache` yet because this project still has closure routes.
- Do not use Namecheap shared hosting for this Railway deployment.
- Do not use Namecheap masked redirects.
- Do not delete Namecheap MX/email records unless you intentionally want to break domain email.

## Phase 1: Prepare The Repository

### Step 1: Confirm The Project Is Pushed To GitHub

Railway will deploy from GitHub.

On your Mac:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem
git status --short --branch
```

If there are changes you want deployed, commit and push them first.

Example:

```bash
git add README.md docs backend frontend
git commit -m "Add Railway deployment configuration"
git push origin main
```

Only deploy from a commit that exists on GitHub.

### Step 2: Confirm Local Tests Pass

Backend:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem/backend
php artisan test
```

Frontend:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem/frontend
npm test
npm run build
```

Do not deploy if tests or the frontend build fail.

### Step 3: Generate The Laravel APP_KEY

On your Mac:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem/backend
php artisan key:generate --show
```

Copy the full output. It will look like:

```text
base64:long-random-string
```

You will paste this into Railway later as `APP_KEY`.

Keep this value private.

## Phase 2: Create The Railway Project

### Step 1: Create Or Log In To Railway

1. Go to:

```text
https://railway.com
```

2. Sign in.
3. Connect your GitHub account if Railway asks.
4. Make sure Railway can access:

```text
emmanuel-ochii/femiowoyele
```

### Step 2: Create A New Project

In Railway:

1. Click `New Project`.
2. Choose an empty project or deploy from GitHub and configure services manually.
3. Name the project:

```text
femiowoyele
```

4. Create or select the environment:

```text
production
```

### Step 3: Choose A Region

In the project or service settings, choose:

```text
EU West / Amsterdam
```

Use the same region for:

- Backend service.
- Frontend service.
- MySQL service.
- Future worker service.
- Future cron service.

Keeping services in the same region reduces latency between Laravel and MySQL.

## Phase 3: Add The MySQL Database

### Step 1: Add MySQL

In the Railway project canvas:

1. Click `+ New`.
2. Choose `Database`.
3. Select `MySQL`.
4. Deploy it.
5. Rename the service to:

```text
MySQL
```

Railway's MySQL service exposes these variables:

```text
MYSQLHOST
MYSQLPORT
MYSQLUSER
MYSQLPASSWORD
MYSQLDATABASE
MYSQL_URL
```

The backend will use `MYSQL_URL` through Laravel's `DB_URL`.

### Step 2: Confirm MySQL Has Persistent Storage

In the MySQL service:

1. Open the service.
2. Check the storage/volume settings.
3. Confirm there is a persistent volume attached.

Do not use a database service without persistent storage.

### Step 3: Backups

Railway database templates are infrastructure services you operate. Set a backup plan before the site becomes production-critical.

Minimum launch plan:

- Take manual backups before major releases.
- Export the database before destructive migrations.
- Add automated backups later.

## Phase 4: Deploy The Backend Service

### Step 1: Create The Backend Service

In the Railway project canvas:

1. Click `+ New`.
2. Choose `GitHub Repo`.
3. Select:

```text
emmanuel-ochii/femiowoyele
```

4. Name the service:

```text
backend
```

### Step 2: Set Backend Root Directory

Open the backend service:

1. Go to `Settings`.
2. Find `Root Directory`.
3. Set it to:

```text
/backend
```

This tells Railway to deploy only the Laravel app.

### Step 3: Set Backend Config File Path

In the backend service settings, if Railway asks for a config file path, set it to:

```text
/backend/railway.json
```

Railway monorepo config files do not automatically follow the root directory in every case, so use the absolute repo path above.

### Step 4: Set Backend Watch Paths

In backend service settings, set watch paths to:

```text
/backend/**
```

This prevents frontend-only changes from redeploying the backend.

### Step 5: Backend Build And Start Settings

For the backend:

```text
Build command:
  leave auto-detected unless Railway asks

Start command:
  leave auto-detected unless Railway asks

Pre-deploy command:
  sh ./railway/init-app.sh

Healthcheck path:
  /api/health

Healthcheck timeout:
  300
```

Railway's Laravel guide says Railway can automatically detect a Laravel app and run it with PHP-FPM and Caddy. Use the default first.

If Railway reports that no start command could be found, set a custom start command only after checking the deploy logs.

### Step 6: Add Backend Variables

Open the backend service:

1. Click `Variables`.
2. Use the `Raw Editor`.
3. Paste the block below.
4. Replace placeholders before saving.

```dotenv
APP_NAME="FemiOwoyele.com"
APP_ENV=production
APP_KEY=REPLACE_WITH_GENERATED_APP_KEY
APP_DEBUG=false
APP_URL=https://api.femiowoyele.com
FRONTEND_URL=https://femiowoyele.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

BCRYPT_ROUNDS=12

LOG_CHANNEL=stderr
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_URL=${{MySQL.MYSQL_URL}}

SANCTUM_STATEFUL_DOMAINS=femiowoyele.com,www.femiowoyele.com,api.femiowoyele.com
CORS_ALLOWED_ORIGINS=https://femiowoyele.com,https://www.femiowoyele.com
CORS_SUPPORTS_CREDENTIALS=true

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=.femiowoyele.com
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=hello@femiowoyele.com
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Replace:

```text
REPLACE_WITH_GENERATED_APP_KEY
  Paste the result of php artisan key:generate --show.
```

Important notes:

- `DB_URL=${{MySQL.MYSQL_URL}}` references the MySQL service.
- If you rename the database service from `MySQL` to something else, update the reference.
- `MAIL_MAILER=log` is safe for launch, but real email delivery needs SMTP variables later.
- `FILESYSTEM_DISK=local` is acceptable until CMS media uploads matter.
- Railway filesystems are ephemeral for app services, so production media should eventually use S3-compatible storage.

### Step 7: Deploy Backend

Click `Deploy`.

Watch the deployment logs.

Expected behavior:

1. Railway installs PHP dependencies.
2. Railway runs the Laravel build.
3. Railway runs:

```text
sh ./railway/init-app.sh
```

4. The script runs migrations.
5. The service starts.
6. Railway checks:

```text
/api/health
```

7. The deployment becomes active.

### Step 8: Generate Temporary Railway Backend Domain

Before adding the custom domain, generate a Railway domain:

1. Open backend service.
2. Go to `Settings`.
3. Go to `Networking`.
4. Click `Generate Domain`.

You will get something like:

```text
backend-production-xxxx.up.railway.app
```

Test it:

```text
https://backend-production-xxxx.up.railway.app/api/health
```

Expected response:

```json
{
  "data": {
    "status": "ok",
    "service": "femiowoyele-api",
    "timestamp": "..."
  }
}
```

Do not continue to frontend deployment until the backend health endpoint works.

## Phase 5: Deploy The Frontend Service

### Step 1: Create The Frontend Service

In the Railway project canvas:

1. Click `+ New`.
2. Choose `GitHub Repo`.
3. Select:

```text
emmanuel-ochii/femiowoyele
```

4. Name the service:

```text
frontend
```

### Step 2: Set Frontend Root Directory

Open frontend service settings.

Set root directory to:

```text
/frontend
```

### Step 3: Set Frontend Config File Path

If Railway asks for a config file path, set:

```text
/frontend/railway.json
```

This tells Railway to use the frontend Dockerfile.

### Step 4: Set Frontend Watch Paths

In frontend service settings, set watch paths:

```text
/frontend/**
```

This prevents backend-only changes from redeploying the frontend.

### Step 5: Add Frontend Variables

Open frontend service variables.

Paste:

```dotenv
VITE_APP_NAME="FemiOwoyele.com"
VITE_API_BASE_URL=https://api.femiowoyele.com/api
```

Do not set `VITE_API_PROXY_TARGET` in Railway. That variable is only for local development.

Important:

- Vite variables are baked into the production frontend during build.
- If `VITE_API_BASE_URL` changes, redeploy the frontend.
- The frontend Dockerfile declares `ARG VITE_API_BASE_URL` and `ENV VITE_API_BASE_URL=...` so Railway can inject that value during the Docker build.
- Until `api.femiowoyele.com` is active, you can temporarily use the Railway backend domain:

```dotenv
VITE_API_BASE_URL=https://backend-production-xxxx.up.railway.app/api
```

After the custom API domain works, change it back to:

```dotenv
VITE_API_BASE_URL=https://api.femiowoyele.com/api
```

Then redeploy frontend.

### Step 6: Deploy Frontend

Click `Deploy`.

Expected behavior:

1. Railway uses `frontend/Dockerfile`.
2. Docker installs frontend dependencies.
3. Docker runs:

```text
npm run build
```

4. Caddy serves the `dist` folder.
5. Railway healthchecks `/`.
6. The service becomes active.

### Step 7: Generate Temporary Railway Frontend Domain

In frontend service:

1. Go to `Settings`.
2. Go to `Networking`.
3. Click `Generate Domain`.

Open the generated Railway URL in your browser.

Check:

- Home page loads.
- Navigation works.
- `/about` works.
- `/admin` loads.
- Browser developer tools do not show API URL errors.

## Phase 6: Seed Initial Content

The first deployment runs migrations but does not automatically seed starter content.

The current seeder creates starter content and this local admin:

```text
admin@femiowoyele.com / password
```

That password is not safe for long-term production.

For first launch, you have two choices.

### Option A: Seed Temporarily

In Railway:

1. Open backend service.
2. Open a shell or command runner if available.
3. Run:

```bash
php artisan db:seed --force
```

Then make it a priority to add a secure admin-password reset/change flow.

### Option B: Wait For Secure Admin Creation

This is safer.

Recommended follow-up:

```text
Add a production-safe artisan command:
php artisan admin:create
```

Then create the first admin with a strong password.

For a public launch, do not leave the seeded `password` credential active.

## Phase 7: Configure Custom Domains In Railway

Do Railway first, then Namecheap.

### Backend API Domain

In Railway:

1. Open the backend service.
2. Go to `Settings`.
3. Go to `Networking`.
4. Click `+ Custom Domain`.
5. Enter:

```text
api.femiowoyele.com
```

6. Railway will show DNS records.
7. Keep that page open.

Railway normally provides:

- A `CNAME` record for routing.
- A `TXT` record for domain ownership verification.

Both are required.

### Frontend Root Domain

In Railway:

1. Open the frontend service.
2. Go to `Settings`.
3. Go to `Networking`.
4. Click `+ Custom Domain`.
5. Enter:

```text
femiowoyele.com
```

6. Railway will show DNS records.
7. Keep that page open.

For a root/apex domain like `femiowoyele.com`, Railway may require an `ALIAS`, flattened `CNAME`, or `A` record depending on the exact dashboard output.

Use Railway's displayed records as the source of truth.

### Optional www Domain

In Railway frontend service, optionally add:

```text
www.femiowoyele.com
```

This allows both:

```text
https://femiowoyele.com
https://www.femiowoyele.com
```

to reach the frontend.

If you care strongly about canonical SEO redirects from `www` to root, handle that later with Cloudflare or a dedicated redirect rule. For launch, serving the same frontend on both domains is acceptable.

## Phase 8: Configure Namecheap DNS

Namecheap should only point DNS to Railway. You do not need Namecheap shared hosting for this deployment.

### Step 1: Find Where DNS Is Managed

In Namecheap:

1. Log in.
2. Go to `Domain List`.
3. Click `Manage` next to `femiowoyele.com`.
4. Check `Nameservers`.

If nameservers are:

```text
Namecheap BasicDNS
Namecheap PremiumDNS
Namecheap FreeDNS
```

then edit records in:

```text
Advanced DNS
```

If nameservers are Namecheap hosting nameservers, such as:

```text
dns1.namecheaphosting.com
dns2.namecheaphosting.com
```

then DNS may be managed in cPanel Zone Editor instead.

If nameservers are Cloudflare or another provider, configure the DNS records there, not in Namecheap Advanced DNS.

### Step 2: Do Not Delete Email Records

Before changing anything, identify and keep these if email is active:

```text
MX records
SPF TXT records
DKIM TXT records
DMARC TXT records
mail-related CNAME records
```

Only change web records for:

```text
@
www
api
Railway verification TXT hosts
```

### Step 3: Remove Conflicting Web Records

In Namecheap `Advanced DNS` -> `Host Records`, check for existing records with these hosts:

```text
@
www
api
```

Remove old/conflicting records for the same host if they point to:

- Namecheap parking.
- Namecheap shared hosting.
- An old cPanel server.
- An old URL redirect.
- Another app platform.

Do not remove records unless you are sure they are for web routing.

### Step 4: Add API Domain Records

Use the exact records shown by Railway for:

```text
api.femiowoyele.com
```

Typical Namecheap setup:

```text
Type:  CNAME Record
Host:  api
Value: Railway-provided target, for example xxxx.up.railway.app
TTL:   Automatic
```

Add the Railway TXT verification record too.

For TXT records in Namecheap:

- Type: `TXT Record`
- Host: use exactly what Railway gives, but do not duplicate the domain name.
- Value: paste the Railway TXT value.
- TTL: Automatic.

Example:

```text
If Railway says host is:
  _railway.api.femiowoyele.com

Namecheap Host may need:
  _railway.api
```

Namecheap usually appends the domain automatically. If unsure, compare with Namecheap's TXT/CNAME instructions or ask Namecheap support.

### Step 5: Add Frontend Root Domain Records

Use the exact records Railway gives for:

```text
femiowoyele.com
```

For the root domain, the Namecheap host is:

```text
@
```

If Railway gives an `A` record:

```text
Type:  A Record
Host:  @
Value: Railway-provided IP address
TTL:   Automatic
```

If Railway gives a CNAME-like target for the root domain, use Namecheap `ALIAS Record` if available:

```text
Type:  ALIAS Record
Host:  @
Value: Railway-provided target
TTL:   Automatic
```

Add the Railway TXT verification record too.

Important:

- A normal CNAME cannot usually be used directly at the root of a domain.
- Namecheap supports `ALIAS` records for root-domain aliasing.
- Railway's dashboard output is the source of truth.

### Step 6: Add Optional www Records

If you added `www.femiowoyele.com` as a frontend custom domain in Railway, add the Railway records in Namecheap.

Typical setup:

```text
Type:  CNAME Record
Host:  www
Value: Railway-provided target
TTL:   Automatic
```

Add the Railway TXT verification record too.

### Step 7: Save All Changes

In Namecheap:

1. Click the green checkmark beside each record.
2. Click `Save All Changes` if the page shows that button.

DNS can start working in minutes, but global propagation can take up to 72 hours.

## Phase 9: Verify Domains In Railway

Go back to Railway.

For each custom domain:

```text
femiowoyele.com
www.femiowoyele.com
api.femiowoyele.com
```

Check the domain status.

You want Railway to show that:

- DNS is configured correctly.
- Ownership TXT verification passed.
- SSL certificate is issued.

If Railway shows pending:

- Wait a few minutes.
- Recheck the Namecheap host/value fields.
- Confirm there are no conflicting records.
- Confirm the TXT record exists.

Important Railway behavior:

- If the TXT verification record is missing, requests to the custom domain can return `404`.
- CNAME alone is not enough.
- DNS propagation can take up to 72 hours.

## Phase 10: Update Final Production Variables

After custom domains are verified:

### Backend Variables

Confirm backend has:

```dotenv
APP_URL=https://api.femiowoyele.com
FRONTEND_URL=https://femiowoyele.com
SANCTUM_STATEFUL_DOMAINS=femiowoyele.com,www.femiowoyele.com,api.femiowoyele.com
CORS_ALLOWED_ORIGINS=https://femiowoyele.com,https://www.femiowoyele.com
```

### Frontend Variables

Confirm frontend has:

```dotenv
VITE_API_BASE_URL=https://api.femiowoyele.com/api
```

Redeploy both services after variable changes:

1. Redeploy backend.
2. Redeploy frontend.

The frontend must redeploy because Vite variables are baked into the static build.

## Phase 11: Final Verification

### Backend Health

Open:

```text
https://api.femiowoyele.com/api/health
```

Expected:

```json
{
  "data": {
    "status": "ok",
    "service": "femiowoyele-api",
    "timestamp": "..."
  }
}
```

### Frontend

Open:

```text
https://femiowoyele.com
```

Check:

- Home page loads.
- Styling loads.
- Images load.
- Navigation works.

### Direct Route Refresh

Open these directly in the browser:

```text
https://femiowoyele.com/about
https://femiowoyele.com/research-ideas
https://femiowoyele.com/books
https://femiowoyele.com/admin
```

They should load correctly. If they 404, the frontend Caddy SPA fallback is not working.

### API From Browser

In the browser developer tools:

1. Open the `Network` tab.
2. Refresh the site.
3. Confirm API requests go to:

```text
https://api.femiowoyele.com/api
```

They should not go to:

```text
127.0.0.1
localhost
```

### Admin Login

Open:

```text
https://femiowoyele.com/admin
```

If seeded:

```text
admin@femiowoyele.com / password
```

Again: do not keep this password active for production.

### Command-Line Checks

From your Mac:

```bash
curl -I https://femiowoyele.com
curl https://api.femiowoyele.com/api/health
```

Expected:

- Frontend returns an HTTP success.
- API returns JSON.

## Phase 12: Configure GitHub Deploy Flow

Railway can auto-deploy on GitHub pushes.

Recommended production workflow:

```text
feature branch -> pull request -> tests -> merge to main -> Railway deploys main
```

In GitHub:

1. Protect `main`.
2. Require pull requests before merge.
3. Require tests before merge once GitHub Actions is configured.
4. Do not push directly to `main` unless this is still early solo development.

In Railway service settings:

```text
Backend autodeploy branch:  main
Frontend autodeploy branch: main
```

Keep watch paths:

```text
Backend:  /backend/**
Frontend: /frontend/**
```

This keeps deploys focused.

## Optional Phase: Worker Service

Do not add this until the CMS uses queues heavily.

When needed:

1. Create another service from the same GitHub repo.
2. Name it:

```text
worker
```

3. Root directory:

```text
/backend
```

4. Variables:

Use the same backend variables.

5. Start command:

```bash
sh ./railway/run-worker.sh
```

6. Do not add a public domain.

7. Do not expose this service publicly.

## Optional Phase: Cron Service

Do not add this until scheduled tasks exist.

When needed:

1. Create another service from the same GitHub repo.
2. Name it:

```text
cron
```

3. Root directory:

```text
/backend
```

4. Variables:

Use the same backend variables.

5. Start command:

```bash
sh ./railway/run-cron.sh
```

6. Do not add a public domain.

7. Do not expose this service publicly.

## Media Uploads And Storage

For launch:

```dotenv
FILESYSTEM_DISK=local
```

is acceptable if the CMS is not relying on user-uploaded production media.

For serious CMS media management, use S3-compatible storage such as:

- AWS S3.
- Cloudflare R2.
- DigitalOcean Spaces.
- Wasabi.

Then set:

```dotenv
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=...
AWS_BUCKET=...
AWS_URL=...
AWS_ENDPOINT=...
AWS_USE_PATH_STYLE_ENDPOINT=true
```

Railway app service filesystems are not a long-term media storage strategy.

## Email Setup

The launch-safe placeholder is:

```dotenv
MAIL_MAILER=log
```

This does not send email.

For production contact form emails, configure SMTP:

```dotenv
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-username
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@femiowoyele.com
MAIL_FROM_NAME="FemiOwoyele.com"
```

If using domain email, keep Namecheap MX/SPF/DKIM/DMARC records intact.

## Performance Checklist

Start with:

- Backend and MySQL in the same Railway region.
- Frontend in the same region for simplicity.
- `APP_DEBUG=false`.
- `LOG_CHANNEL=stderr`.
- `php artisan config:cache`.
- `php artisan event:cache`.
- `php artisan view:cache`.
- Frontend served by Caddy with gzip/zstd enabled.
- Static assets built by Vite with hashed filenames.
- Healthchecks enabled.

Do not enable `route:cache` yet.

Later optimizations:

- Add CDN/proxy such as Cloudflare if global latency matters.
- Move media to object storage.
- Add queue worker service when jobs grow.
- Add database indexes based on real query patterns.
- Scale backend replicas if traffic grows.
- Move to Pro plan when uptime and headroom matter more.

## Security Checklist

- Keep production variables only in Railway.
- Keep `.env` files out of GitHub.
- Use a strong `APP_KEY`.
- Use `APP_DEBUG=false`.
- Use HTTPS-only custom domains.
- Use `SESSION_SECURE_COOKIE=true`.
- Restrict CORS to real frontend domains.
- Do not expose MySQL publicly unless required for admin access.
- If external DB access is needed, use Railway TCP proxy only temporarily and protect credentials.
- Enable two-factor authentication on Railway.
- Enable two-factor authentication on GitHub.
- Enable two-factor authentication on Namecheap.
- Rotate seeded admin credentials.
- Add a production-safe admin creation/password reset flow before serious use.

## Rollback

Railway keeps deployment history.

To roll back:

1. Open the affected service.
2. Go to deployments.
3. Select the previous working deployment.
4. Use Railway's rollback/redeploy action.

Important:

- Rolling back code does not automatically roll back database migrations.
- Avoid destructive migrations in production.
- Prefer additive migrations so rollback remains safe.

## Troubleshooting

### Backend Deploy Fails During Migrations

Check:

- `APP_KEY` exists.
- `DB_CONNECTION=mysql`.
- `DB_URL=${{MySQL.MYSQL_URL}}`.
- MySQL service is deployed and healthy.
- Backend and MySQL are in the same environment.
- The MySQL service is named `MySQL`, or the variable reference matches the real service name.

### Backend Healthcheck Fails

Check:

- `/api/health` works on the Railway-generated backend domain.
- Backend service has healthcheck path `/api/health`.
- The app is listening on Railway's injected port.
- Deploy logs do not show PHP errors.

### Custom Domain Shows 404

Most likely cause:

```text
Railway TXT verification record is missing or wrong.
```

Fix:

- Add both the CNAME/ALIAS/A record and the TXT record Railway shows.
- Remove conflicting Namecheap records.
- Wait for DNS propagation.

### API Works On Railway Domain But Not api.femiowoyele.com

Check:

- `api` CNAME exists in Namecheap.
- API TXT verification record exists in Namecheap.
- Railway shows the custom domain as verified.
- No old `api` A record exists.

### Frontend Loads But API Calls Fail

Check frontend variable:

```dotenv
VITE_API_BASE_URL=https://api.femiowoyele.com/api
```

Then redeploy frontend.

Also check backend variables:

```dotenv
CORS_ALLOWED_ORIGINS=https://femiowoyele.com,https://www.femiowoyele.com
FRONTEND_URL=https://femiowoyele.com
```

Then redeploy backend.

### Frontend Direct Routes 404

Examples:

```text
/about
/admin
/research-ideas
```

Check:

- Frontend service uses `/frontend/Dockerfile`.
- Frontend service uses `/frontend/Caddyfile`.
- `try_files {path} /index.html` exists in `frontend/Caddyfile`.

### Frontend Still Calls localhost

Cause:

```text
The frontend was built with local environment variables.
```

Fix:

1. Set Railway frontend variable:

```dotenv
VITE_API_BASE_URL=https://api.femiowoyele.com/api
```

2. Redeploy frontend.

### Admin Login Fails

Check:

- Database was seeded or admin exists.
- API login request reaches `api.femiowoyele.com`.
- CORS variables include frontend domain.
- Password is correct.
- Backend logs do not show validation/database errors.

### Contact Form Does Not Send Email

If `MAIL_MAILER=log`, this is expected.

Set real SMTP variables before expecting email delivery.

## Namecheap-Specific Notes

- For root domain records, Namecheap uses host `@`.
- For `www.femiowoyele.com`, Namecheap host is `www`.
- For `api.femiowoyele.com`, Namecheap host is `api`.
- Namecheap automatically appends the root domain in many DNS fields.
- Do not enter `api.femiowoyele.com.femiowoyele.com`.
- Remove conflicting records for the same host.
- Keep email-related records.
- Use Automatic TTL unless you have a reason not to.
- DNS often works within 30 minutes, but allow up to 72 hours.

## First Launch Checklist

Before announcing the site:

- Backend Railway domain `/api/health` works.
- Frontend Railway domain works.
- `api.femiowoyele.com/api/health` works.
- `femiowoyele.com` works.
- `www.femiowoyele.com` works or intentionally redirects.
- `/about` direct refresh works.
- `/admin` direct refresh works.
- Frontend API calls use `api.femiowoyele.com`.
- No requests use `localhost` or `127.0.0.1`.
- Railway logs show no repeated errors.
- `APP_DEBUG=false`.
- MySQL has persistent storage.
- Backup plan exists.
- Seeded admin password is rotated or a secure admin creation flow is added.
- Namecheap DNS has no conflicting old web records.
- Namecheap email records are intact.

## Recommended Next Improvements

After first successful Railway deployment:

1. Add GitHub Actions CI before Railway deploys.
2. Add a production-safe `php artisan admin:create` command.
3. Refactor closure routes so `php artisan route:cache` can be enabled.
4. Add object storage for CMS media.
5. Add real SMTP.
6. Add automated database backups.
7. Add uptime monitoring.
8. Add Sentry or another error tracker.

## Official References

- Railway Laravel guide: https://docs.railway.com/guides/laravel
- Railway Vue guide: https://docs.railway.com/guides/vue
- Railway static hosting guide: https://docs.railway.com/guides/static-hosting
- Railway frontend environment variables: https://docs.railway.com/guides/frontend-environment-variables
- Railway Dockerfiles: https://docs.railway.com/builds/dockerfiles
- Railway monorepo docs: https://docs.railway.com/deployments/monorepo
- Railway monorepo guide: https://docs.railway.com/guides/deploying-a-monorepo
- Railway variables: https://docs.railway.com/variables
- Railway MySQL: https://docs.railway.com/databases/mysql
- Railway databases: https://docs.railway.com/databases
- Railway pre-deploy command: https://docs.railway.com/deployments/pre-deploy-command
- Railway start command: https://docs.railway.com/deployments/start-command
- Railway healthchecks: https://docs.railway.com/deployments/healthchecks
- Railway domains: https://docs.railway.com/networking/domains/working-with-domains
- Railway private networking: https://docs.railway.com/networking/private-networking
- Railway regions: https://docs.railway.com/deployments/regions
- Railway scaling: https://docs.railway.com/deployments/scaling
- Namecheap host records: https://www.namecheap.com/support/knowledgebase/article.aspx/434/2237/how-do-i-set-up-host-records-for-a-domain/
- Namecheap CNAME records: https://www.namecheap.com/support/knowledgebase/article.aspx/9646/2237/how-to-create-a-cname-record-for-your-domain/
- Namecheap TXT records: https://www.namecheap.com/support/knowledgebase/article.aspx/317/2237/how-do-i-add-txtspfdkimdmarc-records-for-my-domain/
- Namecheap ALIAS records: https://www.namecheap.com/support/knowledgebase/article.aspx/10128/2237/how-to-create-an-alias-record/
- Namecheap URL redirects: https://www.namecheap.com/support/knowledgebase/article.aspx/385/2237/how-to-set-up-a-url-redirect-for-a-domain/
