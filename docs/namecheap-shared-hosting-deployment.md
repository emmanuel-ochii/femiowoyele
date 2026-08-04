# Namecheap Shared Hosting Deployment Guide

This guide explains how to deploy the FemiOwoyele.com Laravel API and Vue/Vite frontend to Namecheap shared hosting in a clean, secure, repeatable way.

It is written for a non-technical person. Follow the steps in order. Do not skip the backup, environment, and verification steps.

## What You Are Deploying

The repository has two applications:

```text
backend/   Laravel JSON API and CMS backend
frontend/  Vue 3 + Vite public site and admin interface
```

On Namecheap shared hosting, the best simple setup is:

```text
https://femiowoyele.com        Vue website
https://femiowoyele.com/admin  Vue admin CMS screen
https://femiowoyele.com/api    Laravel JSON API
```

The Vue frontend is built into static files before upload. The Laravel backend runs through PHP on Namecheap.

## Important Hosting Reality Check

Namecheap shared hosting can launch this project, but it is not the same as a full application platform like Railway, Render, Forge, Ploi, or a VPS.

Shared hosting is acceptable for:

- A content-focused public website.
- A lightweight admin CMS.
- Moderate traffic.
- Simple contact/newsletter forms.
- Database-backed content.

Shared hosting is not ideal for:

- Persistent Laravel queue workers.
- Laravel Horizon.
- Redis or Memcached.
- WebSockets or real-time features.
- Background video/image processing.
- High traffic.
- Complex zero-downtime deploys.

This guide avoids those unsupported patterns.

## What You Need Before Starting

You need access to:

- The Namecheap account that owns the hosting plan.
- cPanel for the hosting account.
- The GitHub repository.
- A Mac Terminal or another terminal application.
- The project files on your computer.

This guide assumes the project is on your Mac at:

```text
/Users/emmanuelochubili/Desktop/dev/profem
```

If the project is in another location, replace that path whenever it appears.

## Words Used In This Guide

Use this short glossary if any term is unfamiliar:

```text
cPanel
  The web dashboard Namecheap provides for hosting management.

public_html
  The folder Namecheap usually uses as the public website folder.
  Files in this folder can be visited from the browser.

.env
  A private Laravel settings file. It contains passwords and secrets.
  It must never be uploaded to GitHub or exposed publicly.

SSH
  A secure way to connect to the hosting server from Terminal.

SFTP
  Secure file upload over SSH. It uses the same Namecheap port.

Composer
  PHP dependency installer used by Laravel.

npm
  JavaScript dependency installer used by Vue/Vite.

Vite build
  The command that turns the Vue app into static production files.

Release
  One deployed version of the project.

Rollback
  Switching back to the previous working release.
```

## Deployment Worksheet

Fill this out before you deploy. Do not commit passwords or secrets to GitHub.

```text
Domain:
  femiowoyele.com

Canonical domain:
  femiowoyele.com

cPanel username:
  ______________________________

Namecheap server hostname:
  ______________________________

SSH/SFTP port:
  21098

Remote app folder:
  /home/CPANEL_USER/apps/femiowoyele

Public website folder:
  /home/CPANEL_USER/public_html

Database name:
  ______________________________

Database username:
  ______________________________

Database password:
  stored privately, not written here

Production app key:
  generated later

SMTP host:
  ______________________________

SMTP username:
  ______________________________

SMTP password:
  stored privately, not written here
```

In the commands below, replace:

```text
CPANEL_USER      with your real cPanel username
SERVER_HOSTNAME  with your Namecheap server hostname
```

Example:

```text
CPANEL_USER      becomes femiowo
SERVER_HOSTNAME  becomes server123.web-hosting.com
```

## Recommended Deployment Shape

Use this structure on Namecheap:

```text
/home/CPANEL_USER/apps/femiowoyele/
|-- current -> releases/CURRENT_RELEASE
|-- releases/
|   `-- CURRENT_RELEASE/
|       |-- backend/
|       `-- public/
`-- shared/
    |-- .env
    `-- storage/

/home/CPANEL_USER/public_html/
|-- index.html
|-- index.php
|-- .htaccess
|-- assets/
|-- images/
|-- robots.txt
`-- sitemap.xml
```

Why this layout is used:

- Laravel source code stays outside `public_html`.
- The private `.env` file stays outside `public_html`.
- Uploaded files and logs stay in `shared/storage`.
- Each deployment gets its own release folder.
- Rollback is easier because old releases remain available.

## Safety Rules

Follow these rules throughout deployment:

- Never upload `backend/.env` from your computer.
- Never put `.env` inside GitHub.
- Never put database passwords in README files, GitHub issues, or chat messages.
- Keep Laravel source code outside `public_html`.
- Keep `APP_DEBUG=false` in production.
- Use HTTPS.
- Make a cPanel backup before changing `public_html`.
- Do not run destructive commands unless you understand what folder they affect.
- Do not use `php artisan route:cache` yet. The current project has closure routes.

## Phase 1: Prepare Namecheap

### Step 1: Log In To cPanel

1. Log in to your Namecheap account.
2. Open your hosting dashboard.
3. Find the hosting plan for `femiowoyele.com`.
4. Click the option to open cPanel.

Keep cPanel open in your browser.

### Step 2: Confirm The Domain Points To This Hosting Account

In cPanel:

1. Find the `Domains` section.
2. Open `Domains`.
3. Confirm that `femiowoyele.com` appears there.
4. Confirm the document root is `public_html` or another folder you recognize.

For the rest of this guide, we assume:

```text
/home/CPANEL_USER/public_html
```

If cPanel shows a different document root, write it in the worksheet and use that folder instead of `public_html`.

### Step 3: Enable SSL

The site must run on HTTPS.

In cPanel:

1. Search for `SSL`.
2. Open `SSL/TLS Status` or the Namecheap SSL tool available on your plan.
3. Enable SSL for:

```text
femiowoyele.com
www.femiowoyele.com
```

4. Wait until cPanel shows the domain is protected.
5. Visit this in a browser:

```text
https://femiowoyele.com
```

It is fine if the page is still a placeholder. The important part is that HTTPS works.

### Step 4: Enable SSH

Namecheap shared hosting has SSH disabled by default.

In cPanel:

1. Search for `Manage Shell`.
2. Open `Manage Shell`.
3. Turn SSH on.
4. Confirm the `Terminal` menu appears in cPanel under the `Advanced` section.

Namecheap shared hosting uses SSH/SFTP port:

```text
21098
```

### Step 5: Set PHP Version

Laravel requires PHP 8.2 or newer.

In cPanel:

1. Search for `Select PHP Version`.
2. Open `Select PHP Version`.
3. Select PHP `8.2`, `8.3`, or newer.
4. Click `Apply` or `Set as current`.

Use PHP 8.2 if you want the most conservative choice.

### Step 6: Enable PHP Extensions

In the same PHP selector screen, enable these extensions if available:

```text
bcmath
ctype
curl
dom
fileinfo
filter
hash
mbstring
openssl
pdo
pdo_mysql
session
tokenizer
xml
zip
```

If you cannot find an extension, do not guess. Contact Namecheap support and ask:

```text
Please enable the PHP extensions required for a Laravel 12 application on PHP 8.2:
bcmath, ctype, curl, dom, fileinfo, filter, hash, mbstring, openssl,
pdo, pdo_mysql, session, tokenizer, xml, and zip.
```

### Step 7: Increase PHP Limits

Still in cPanel's PHP settings, set these values if the interface allows it:

```text
memory_limit        512M
max_execution_time  300
max_input_time      300
post_max_size       64M
upload_max_filesize 64M
```

These values make Composer, Laravel, and CMS uploads less likely to fail.

### Step 8: Create The Database

In cPanel:

1. Search for `MySQL Database Wizard`.
2. Open it.
3. Create a database named:

```text
femiowoyele
```

cPanel will probably prefix it with your username. For example:

```text
CPANEL_USER_femiowoyele
```

4. Create a database user named:

```text
femiowoyele_user
```

cPanel will probably prefix that too:

```text
CPANEL_USER_femiowoyele_user
```

5. Generate a strong password.
6. Save the password somewhere private.
7. Add the user to the database.
8. Grant privileges to that user.

For most cPanel shared hosting setups, selecting `ALL PRIVILEGES` for this dedicated database user is normal.

Write the database name and username in the worksheet.

## Phase 2: Prepare The Server Folders

You can do this from your Mac Terminal or from cPanel Terminal. The cPanel Terminal is easier because you do not need to type the server hostname.

### Option A: Use cPanel Terminal

In cPanel:

1. Search for `Terminal`.
2. Open `Terminal`.
3. If a warning appears, accept it.

You should now see a command screen.

### Option B: Use Mac Terminal

Open Terminal on your Mac and run:

```bash
ssh -p 21098 CPANEL_USER@SERVER_HOSTNAME
```

Replace `CPANEL_USER` and `SERVER_HOSTNAME` first.

If asked:

- Type `yes` to trust the server the first time.
- Type your cPanel password when prompted.
- The password may look invisible while typing. That is normal.

### Step 1: Create App Folders

In the server terminal, paste this:

```bash
mkdir -p ~/apps/femiowoyele/releases
mkdir -p ~/apps/femiowoyele/shared/storage/app/public
mkdir -p ~/apps/femiowoyele/shared/storage/framework/cache
mkdir -p ~/apps/femiowoyele/shared/storage/framework/sessions
mkdir -p ~/apps/femiowoyele/shared/storage/framework/views
mkdir -p ~/apps/femiowoyele/shared/storage/logs
```

### Step 2: Create The Private Environment File

In the server terminal, paste:

```bash
touch ~/apps/femiowoyele/shared/.env
chmod 600 ~/apps/femiowoyele/shared/.env
```

This creates the private Laravel settings file.

### Step 3: Back Up public_html

This is important. Do not skip it.

In the server terminal, paste:

```bash
cp -a ~/public_html ~/public_html_backup_before_femiowoyele_$(date +%Y%m%d_%H%M%S)
```

This creates a backup copy of the current website folder.

If `public_html` contains another active website that must remain live, stop here and do not continue with the `public_html` deployment. Use a separate addon domain document root or ask the hosting administrator to confirm the correct folder.

## Phase 3: Create The Production .env File

The `.env` file controls Laravel in production.

### Step 1: Generate APP_KEY

On your Mac, open Terminal and run:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem/backend
php artisan key:generate --show
```

You will see something like:

```text
base64:abc123...
```

Copy the full value. Keep it private.

### Step 2: Open The .env File In cPanel

In cPanel:

1. Open `File Manager`.
2. Click `Settings`.
3. Turn on `Show Hidden Files (dotfiles)`.
4. Navigate to:

```text
/home/CPANEL_USER/apps/femiowoyele/shared
```

5. Right-click `.env`.
6. Click `Edit`.

### Step 3: Paste The Production .env

Paste this into `.env`, then replace every placeholder.

```dotenv
APP_NAME="FemiOwoyele.com"
APP_ENV=production
APP_KEY=base64:REPLACE_WITH_GENERATED_APP_KEY
APP_DEBUG=false
APP_URL=https://femiowoyele.com
FRONTEND_URL=https://femiowoyele.com

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

BCRYPT_ROUNDS=12

LOG_CHANNEL=single
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=CPANEL_USER_femiowoyele
DB_USERNAME=CPANEL_USER_femiowoyele_user
DB_PASSWORD=REPLACE_WITH_STRONG_DATABASE_PASSWORD

SANCTUM_STATEFUL_DOMAINS=femiowoyele.com,www.femiowoyele.com
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

MAIL_MAILER=smtp
MAIL_HOST=REPLACE_WITH_SMTP_HOST
MAIL_PORT=587
MAIL_USERNAME=REPLACE_WITH_SMTP_USERNAME
MAIL_PASSWORD=REPLACE_WITH_SMTP_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@femiowoyele.com
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

Important replacements:

```text
REPLACE_WITH_GENERATED_APP_KEY
  Paste the APP_KEY generated with php artisan key:generate --show.

CPANEL_USER_femiowoyele
  Replace with the real database name from cPanel.

CPANEL_USER_femiowoyele_user
  Replace with the real database username from cPanel.

REPLACE_WITH_STRONG_DATABASE_PASSWORD
  Replace with the database password from cPanel.

SMTP values
  Replace with your email provider's SMTP settings.
```

If SMTP is not ready yet, temporarily use:

```dotenv
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

Contact and newsletter messages will still save in the database, but emails will not be sent.

### Step 4: Save The .env File

After saving, confirm:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://femiowoyele.com`
- The database values are real.
- No placeholder text remains in the file.

## Phase 4: Build The Project On Your Mac

This phase creates the production files you will upload.

### Step 1: Open The Project Folder

On your Mac, open Terminal and run:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem
```

Confirm you are in the right folder:

```bash
pwd
```

You should see:

```text
/Users/emmanuelochubili/Desktop/dev/profem
```

### Step 2: Confirm Git Is Clean Enough

Run:

```bash
git status --short
```

It is okay if the deployment guide is modified while you are editing it. Before a real production deployment, commit intentional changes first so you know exactly what version you deployed.

### Step 3: Run Backend Checks

Run:

```bash
cd backend
composer validate --no-check-publish
composer install
php artisan test
```

Expected result:

```text
PASS
```

If tests fail, stop and fix the failure before deploying.

### Step 4: Run Frontend Checks

Run:

```bash
cd ../frontend
npm ci
npm test
VITE_API_BASE_URL=/api npm run build
```

Expected result:

```text
built successfully
```

The build output will be created in:

```text
frontend/dist
```

### Step 5: Create A Release Folder On Your Mac

Run:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem
export RELEASE="$(git rev-parse --short HEAD)-$(date +%Y%m%d%H%M%S)"
echo "$RELEASE"
```

You should see a release name like:

```text
2d43060-20260803144530
```

Now create the local deployment artifact:

```bash
rm -rf /tmp/femiowoyele-release
mkdir -p /tmp/femiowoyele-release/backend
mkdir -p /tmp/femiowoyele-release/public

rsync -a \
  --exclude='.env' \
  --exclude='tests/' \
  --exclude='.phpunit.cache/' \
  --exclude='.phpunit.result.cache' \
  --exclude='storage/logs/*.log' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  backend/ /tmp/femiowoyele-release/backend/

rsync -a backend/public/ /tmp/femiowoyele-release/public/
rsync -a frontend/dist/ /tmp/femiowoyele-release/public/
```

### Step 6: Install Production PHP Dependencies Into The Release

Run:

```bash
cd /tmp/femiowoyele-release/backend
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
```

This prepares Laravel for production.

### Step 7: Replace public/index.php For Namecheap public_html

Run:

```bash
cat > /tmp/femiowoyele-release/public/index.php <<'PHP'
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$backendPath = dirname(__DIR__).'/apps/femiowoyele/current/backend';

if (file_exists($maintenance = $backendPath.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $backendPath.'/vendor/autoload.php';

/** @var Application $app */
$app = require_once $backendPath.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
PHP
```

This tells `public_html/index.php` where the Laravel backend lives.

### Step 8: Replace public/.htaccess For Vue And Laravel

Run:

```bash
cat > /tmp/femiowoyele-release/public/.htaccess <<'HTACCESS'
<IfModule mod_negotiation.c>
    Options -MultiViews -Indexes
</IfModule>

DirectoryIndex index.html index.php

<IfModule mod_rewrite.c>
    RewriteEngine On

    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    RewriteCond %{HTTP_HOST} ^www\.femiowoyele\.com$ [NC]
    RewriteRule ^ https://femiowoyele.com%{REQUEST_URI} [L,R=301]

    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    RewriteCond %{REQUEST_URI} ^/(api|sanctum)(/|$)
    RewriteRule ^ index.php [L]

    RewriteCond %{REQUEST_URI} ^/storage/
    RewriteCond %{REQUEST_FILENAME} -f
    RewriteRule ^ - [L]

    RewriteCond %{REQUEST_FILENAME} -f [OR]
    RewriteCond %{REQUEST_FILENAME} -d
    RewriteRule ^ - [L]

    RewriteRule ^ index.html [L]
</IfModule>
HTACCESS
```

This does four things:

- Forces HTTPS.
- Redirects `www.femiowoyele.com` to `femiowoyele.com`.
- Sends `/api/*` requests to Laravel.
- Sends normal website routes like `/about` and `/admin` to Vue.

If you want `www.femiowoyele.com` to be the main domain instead, change the two canonical-domain lines before deploying.

### Step 9: Confirm The Release Artifact Looks Right

Run:

```bash
find /tmp/femiowoyele-release -maxdepth 2 -type d | sort
```

You should see folders similar to:

```text
/tmp/femiowoyele-release
/tmp/femiowoyele-release/backend
/tmp/femiowoyele-release/backend/app
/tmp/femiowoyele-release/backend/bootstrap
/tmp/femiowoyele-release/backend/config
/tmp/femiowoyele-release/backend/vendor
/tmp/femiowoyele-release/public
/tmp/femiowoyele-release/public/assets
/tmp/femiowoyele-release/public/images
```

Check that `.env` is not inside the artifact:

```bash
find /tmp/femiowoyele-release -name ".env" -print
```

Expected result:

```text

```

No output is good.

## Phase 5: Upload The Release To Namecheap

This uploads the release from your Mac to the Namecheap server.

On your Mac, still inside Terminal, run:

```bash
cd /Users/emmanuelochubili/Desktop/dev/profem
echo "$RELEASE"
```

If nothing prints, recreate the release variable:

```bash
export RELEASE="PASTE_THE_RELEASE_NAME_YOU_CREATED"
```

Now upload:

```bash
rsync -az --delete -e "ssh -p 21098" \
  /tmp/femiowoyele-release/ \
  CPANEL_USER@SERVER_HOSTNAME:/home/CPANEL_USER/apps/femiowoyele/releases/$RELEASE/
```

Replace all placeholders before pressing Enter.

Example shape:

```bash
rsync -az --delete -e "ssh -p 21098" \
  /tmp/femiowoyele-release/ \
  mycpaneluser@server123.web-hosting.com:/home/mycpaneluser/apps/femiowoyele/releases/$RELEASE/
```

If asked for a password, enter your cPanel password. It may appear invisible while typing.

## Phase 6: Activate The Release On The Server

Connect to the server:

```bash
ssh -p 21098 CPANEL_USER@SERVER_HOSTNAME
```

Run:

```bash
export RELEASE="PASTE_THE_RELEASE_NAME_YOU_UPLOADED"
```

Example:

```bash
export RELEASE="2d43060-20260803144530"
```

### Step 1: Link Shared Files

Run:

```bash
cd ~/apps/femiowoyele/releases/$RELEASE/backend
rm -rf storage
ln -s ~/apps/femiowoyele/shared/storage storage
rm -f .env
ln -s ~/apps/femiowoyele/shared/.env .env
```

### Step 2: Point current To This Release

Run:

```bash
ln -sfn ~/apps/femiowoyele/releases/$RELEASE ~/apps/femiowoyele/current
```

### Step 3: Publish Web Files To public_html

Only run this if `public_html` is dedicated to FemiOwoyele.com.

Run:

```bash
rsync -a --delete ~/apps/femiowoyele/current/public/ ~/public_html/
```

If `rsync` is not available on the server, use this fallback:

```bash
cp -a ~/apps/femiowoyele/current/public/. ~/public_html/
```

The fallback does not delete old files, so old unused assets may remain. That is acceptable for a first deployment.

### Step 4: Set Permissions

Run:

```bash
find ~/apps/femiowoyele/current/backend -type d -exec chmod 755 {} \;
find ~/apps/femiowoyele/current/backend -type f -exec chmod 644 {} \;
chmod 600 ~/apps/femiowoyele/shared/.env
chmod -R 755 ~/apps/femiowoyele/shared/storage
```

### Step 5: Run Laravel Deployment Commands

Run:

```bash
cd ~/apps/femiowoyele/current/backend
php artisan optimize:clear
php artisan migrate --force
php artisan config:cache
php artisan view:cache
php artisan event:cache
```

Do not run:

```bash
php artisan route:cache
```

The current project has closure routes in `backend/routes/api.php` and `backend/routes/web.php`. Laravel route caching will fail until those routes are moved into controllers.

## Phase 7: Add Initial Production Content

The database starts empty after migrations.

The current local seeder creates starter content and this admin user:

```text
admin@femiowoyele.com / password
```

That password is not safe for production.

Use one of these options.

### Option A: Seed Temporarily And Immediately Change Password

Run:

```bash
cd ~/apps/femiowoyele/current/backend
php artisan db:seed --force
```

Then immediately log in to the admin CMS and change the password once that feature exists.

If the password-change feature is not implemented yet, do not keep this seeded account in production longer than necessary.

### Option B: Create A Strong Admin Manually

This is safer, but currently requires a Laravel command or Tinker workflow that has not yet been added to the project.

Recommended future improvement:

```text
Add a custom artisan command:
php artisan admin:create
```

Until that exists, Option A is the practical first-deploy path, followed by immediate password rotation work in the CMS.

## Phase 8: Verify The Live Site

Use a browser first.

Open:

```text
https://femiowoyele.com
```

Check:

- The home page loads.
- The styling looks correct.
- Images load.
- Navigation works.

Now test direct page refreshes:

```text
https://femiowoyele.com/about
https://femiowoyele.com/research-ideas
https://femiowoyele.com/books
https://femiowoyele.com/admin
```

These should load the Vue app. If they show a 404 page, the `.htaccess` fallback is not working.

### API Health Check

Open:

```text
https://femiowoyele.com/api/health
```

Expected result:

```json
{
  "data": {
    "status": "ok",
    "service": "femiowoyele-api",
    "timestamp": "..."
  }
}
```

### Command-Line Verification

On your Mac:

```bash
curl -I https://femiowoyele.com
curl https://femiowoyele.com/api/health
```

Expected:

- The first command returns an HTTP success or redirect to HTTPS.
- The second command returns JSON.

### Security Verification

These URLs must not expose private files:

```bash
curl -I https://femiowoyele.com/.env
curl -I https://femiowoyele.com/composer.json
curl -I https://femiowoyele.com/backend/.env
curl -I https://femiowoyele.com/package.json
```

Expected result:

```text
404
```

or:

```text
403
```

If any private file downloads or displays, take the site offline and fix the document-root/public-folder setup before continuing.

## Phase 9: Set Up Cron Only If Needed

Do not add cron jobs until the application needs them.

Namecheap shared hosting does not allow cron intervals below 5 minutes.

If future scheduler tasks are added, use cPanel:

1. Open `Cron Jobs`.
2. Add this command:

```cron
*/5 * * * * /usr/local/bin/php /home/CPANEL_USER/apps/femiowoyele/current/backend/artisan schedule:run >/dev/null 2>&1
```

If future queued jobs are added, shared hosting can only drain jobs periodically:

```cron
*/5 * * * * /usr/local/bin/php /home/CPANEL_USER/apps/femiowoyele/current/backend/artisan queue:work --stop-when-empty --tries=3 --timeout=60 >/dev/null 2>&1
```

Do not design production-critical work around this. It is a shared-hosting compromise.

## Phase 10: Optional GitHub Actions Deployment

Manual deployment is the safest first deployment because you can see each step.

After the first deployment works, you can automate future deployments from GitHub.

The recommended policy is:

- Pull requests run tests only.
- Pushes to `main` build and deploy.
- Production deployment uses a GitHub `production` environment.
- Production deployment requires approval.
- Secrets are stored in GitHub environment secrets, not in the repository.

### GitHub Environment Setup

In GitHub:

1. Open the repository.
2. Go to `Settings`.
3. Go to `Environments`.
4. Create an environment named:

```text
production
```

5. Add required reviewers if your GitHub plan supports it.
6. Restrict deployment branches to:

```text
main
```

### GitHub Secrets

Add these environment secrets to `production`:

```text
NAMECHEAP_HOST
NAMECHEAP_PORT
NAMECHEAP_USERNAME
NAMECHEAP_SSH_KEY
NAMECHEAP_KNOWN_HOSTS
NAMECHEAP_DEPLOY_PATH
```

Use these values:

```text
NAMECHEAP_HOST
  Your server hostname, for example server123.web-hosting.com.

NAMECHEAP_PORT
  21098

NAMECHEAP_USERNAME
  Your cPanel username.

NAMECHEAP_SSH_KEY
  A private SSH key allowed to deploy to the hosting account.

NAMECHEAP_KNOWN_HOSTS
  The verified SSH host key line for the Namecheap server.

NAMECHEAP_DEPLOY_PATH
  /home/CPANEL_USER/apps/femiowoyele
```

### GitHub Variables

Add these environment variables:

```text
PRODUCTION_URL=https://femiowoyele.com
PRODUCTION_API_BASE_URL=/api
```

### Workflow Example

Create this file later when you are ready for automated deployment:

```text
.github/workflows/namecheap-deploy.yml
```

Workflow:

```yaml
name: Deploy to Namecheap

on:
  pull_request:
    branches: [main]
  push:
    branches: [main]
  workflow_dispatch:

permissions:
  contents: read

concurrency:
  group: namecheap-${{ github.ref }}
  cancel-in-progress: true

jobs:
  backend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - uses: shivammathur/setup-php@v2
        with:
          php-version: "8.2"
          extensions: bcmath, ctype, curl, dom, fileinfo, mbstring, openssl, pdo_mysql, tokenizer, xml, zip
          coverage: none

      - name: Install backend dependencies
        working-directory: backend
        run: composer install --no-interaction --prefer-dist

      - name: Validate Composer
        working-directory: backend
        run: composer validate --no-check-publish

      - name: Run backend tests
        working-directory: backend
        run: php artisan test

  frontend:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4

      - uses: actions/setup-node@v4
        with:
          node-version: 20
          cache: npm
          cache-dependency-path: frontend/package-lock.json

      - name: Install frontend dependencies
        working-directory: frontend
        run: npm ci

      - name: Run frontend tests
        working-directory: frontend
        run: npm test

      - name: Build frontend
        working-directory: frontend
        run: npm run build
        env:
          VITE_API_BASE_URL: ${{ vars.PRODUCTION_API_BASE_URL }}

  deploy:
    if: github.event_name != 'pull_request' && github.ref == 'refs/heads/main'
    needs: [backend, frontend]
    runs-on: ubuntu-latest
    environment:
      name: production
      url: ${{ vars.PRODUCTION_URL }}
    steps:
      - uses: actions/checkout@v4

      - uses: shivammathur/setup-php@v2
        with:
          php-version: "8.2"
          extensions: bcmath, ctype, curl, dom, fileinfo, mbstring, openssl, pdo_mysql, tokenizer, xml, zip
          coverage: none

      - uses: actions/setup-node@v4
        with:
          node-version: 20
          cache: npm
          cache-dependency-path: frontend/package-lock.json

      - name: Build release artifact
        run: |
          set -euo pipefail

          npm ci --prefix frontend
          VITE_API_BASE_URL="${{ vars.PRODUCTION_API_BASE_URL }}" npm run build --prefix frontend

          mkdir -p release/backend release/public

          rsync -a \
            --exclude='.env' \
            --exclude='tests/' \
            --exclude='.phpunit.cache/' \
            --exclude='.phpunit.result.cache' \
            --exclude='storage/logs/*.log' \
            backend/ release/backend/

          composer install \
            --working-dir=release/backend \
            --no-dev \
            --no-interaction \
            --prefer-dist \
            --optimize-autoloader

          rsync -a backend/public/ release/public/
          rsync -a frontend/dist/ release/public/

          cat > release/public/index.php <<'PHP'
          <?php

          use Illuminate\Foundation\Application;
          use Illuminate\Http\Request;

          define('LARAVEL_START', microtime(true));

          $backendPath = dirname(__DIR__).'/apps/femiowoyele/current/backend';

          if (file_exists($maintenance = $backendPath.'/storage/framework/maintenance.php')) {
              require $maintenance;
          }

          require $backendPath.'/vendor/autoload.php';

          /** @var Application $app */
          $app = require_once $backendPath.'/bootstrap/app.php';

          $app->handleRequest(Request::capture());
          PHP

          cat > release/public/.htaccess <<'HTACCESS'
          <IfModule mod_negotiation.c>
              Options -MultiViews -Indexes
          </IfModule>

          DirectoryIndex index.html index.php

          <IfModule mod_rewrite.c>
              RewriteEngine On

              RewriteCond %{HTTPS} !=on
              RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

              RewriteCond %{HTTP_HOST} ^www\.femiowoyele\.com$ [NC]
              RewriteRule ^ https://femiowoyele.com%{REQUEST_URI} [L,R=301]

              RewriteCond %{HTTP:Authorization} .
              RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

              RewriteCond %{HTTP:x-xsrf-token} .
              RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

              RewriteCond %{REQUEST_URI} ^/(api|sanctum)(/|$)
              RewriteRule ^ index.php [L]

              RewriteCond %{REQUEST_URI} ^/storage/
              RewriteCond %{REQUEST_FILENAME} -f
              RewriteRule ^ - [L]

              RewriteCond %{REQUEST_FILENAME} -f [OR]
              RewriteCond %{REQUEST_FILENAME} -d
              RewriteRule ^ - [L]

              RewriteRule ^ index.html [L]
          </IfModule>
          HTACCESS

      - name: Configure SSH
        run: |
          set -euo pipefail
          mkdir -p ~/.ssh
          chmod 700 ~/.ssh
          printf '%s\n' "${{ secrets.NAMECHEAP_SSH_KEY }}" > ~/.ssh/namecheap
          chmod 600 ~/.ssh/namecheap
          printf '%s\n' "${{ secrets.NAMECHEAP_KNOWN_HOSTS }}" > ~/.ssh/known_hosts

      - name: Upload release
        run: |
          set -euo pipefail
          RELEASE="${GITHUB_SHA}"
          SSH="ssh -i ~/.ssh/namecheap -p ${{ secrets.NAMECHEAP_PORT }}"
          DEST="${{ secrets.NAMECHEAP_USERNAME }}@${{ secrets.NAMECHEAP_HOST }}"
          BASE="${{ secrets.NAMECHEAP_DEPLOY_PATH }}"

          $SSH "$DEST" "mkdir -p '$BASE/releases/$RELEASE'"
          rsync -az --delete -e "$SSH" release/ "$DEST:$BASE/releases/$RELEASE/"

      - name: Activate release
        run: |
          set -euo pipefail
          RELEASE="${GITHUB_SHA}"
          SSH="ssh -i ~/.ssh/namecheap -p ${{ secrets.NAMECHEAP_PORT }}"
          DEST="${{ secrets.NAMECHEAP_USERNAME }}@${{ secrets.NAMECHEAP_HOST }}"
          BASE="${{ secrets.NAMECHEAP_DEPLOY_PATH }}"

          $SSH "$DEST" "set -e
            cd '$BASE/releases/$RELEASE/backend'
            rm -rf storage
            ln -s '$BASE/shared/storage' storage
            rm -f .env
            ln -s '$BASE/shared/.env' .env
            ln -sfn '$BASE/releases/$RELEASE' '$BASE/current'
            rsync -a --delete '$BASE/current/public/' ~/public_html/
            cd '$BASE/current/backend'
            php artisan optimize:clear
            php artisan migrate --force
            php artisan config:cache
            php artisan view:cache
            php artisan event:cache
          "

      - name: Verify production health
        run: |
          set -euo pipefail
          curl --fail --silent --show-error "${{ vars.PRODUCTION_URL }}/api/health"
```

Before using this workflow in production:

- Confirm the first manual deployment works.
- Confirm SSH key authentication works.
- Confirm `NAMECHEAP_KNOWN_HOSTS` is copied from a trusted host-key verification process.
- Confirm `~/public_html` belongs only to this project.
- Pin third-party actions to commit SHAs after initial validation if you want stricter supply-chain security.

## Rollback Guide

Rollback means switching back to a previous release.

Connect to the server:

```bash
ssh -p 21098 CPANEL_USER@SERVER_HOSTNAME
```

List releases:

```bash
ls -1 ~/apps/femiowoyele/releases
```

Choose the previous working release and run:

```bash
export PREVIOUS_RELEASE="PASTE_PREVIOUS_RELEASE_NAME"
ln -sfn ~/apps/femiowoyele/releases/$PREVIOUS_RELEASE ~/apps/femiowoyele/current
rsync -a --delete ~/apps/femiowoyele/current/public/ ~/public_html/
cd ~/apps/femiowoyele/current/backend
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan event:cache
```

Do not automatically roll back database migrations in production. Database rollback can delete or corrupt real content. Prefer forward fixes or additive migrations.

## Troubleshooting

### The Home Page Shows A 500 Error

Check the Laravel log:

```bash
tail -n 80 ~/apps/femiowoyele/shared/storage/logs/laravel.log
```

Common causes:

- `.env` is missing.
- `APP_KEY` is missing.
- Database credentials are wrong.
- `vendor/` was not uploaded.
- PHP version is too old.
- PHP extension is missing.
- `storage` is not writable.

### /api/health Shows The Vue App Instead Of JSON

Cause:

```text
.htaccess is not sending /api requests to index.php.
```

Fix:

- Recheck `public_html/.htaccess`.
- Confirm the `/api` rewrite block exists.
- Confirm hidden files are visible in cPanel File Manager.

### /about Or /admin Shows 404

Cause:

```text
The Vue SPA fallback is missing.
```

Fix:

- Recheck `public_html/.htaccess`.
- Confirm the final rule sends unknown routes to `index.html`.

### CSS Or Images Are Missing

Common causes:

- `frontend/dist/assets` was not uploaded.
- The upload stopped before completing.
- Browser cache is showing an older version.

Fix:

```bash
ls -la ~/public_html/assets
ls -la ~/public_html/images
```

### Database Error

If the browser or log shows `SQLSTATE`, check:

- Database name.
- Database username.
- Database password.
- Whether the database user was added to the database.
- Whether the database user has privileges.

### No Application Encryption Key Has Been Specified

Cause:

```text
APP_KEY is missing from shared/.env.
```

Fix:

- Generate a key locally.
- Paste it into `.env`.
- Run:

```bash
cd ~/apps/femiowoyele/current/backend
php artisan optimize:clear
php artisan config:cache
```

### Admin Login Fails

Check:

- The admin user exists.
- The password is correct.
- The `/api/auth/login` request reaches Laravel.
- Browser developer tools do not show a CORS error.
- The frontend was built with `VITE_API_BASE_URL=/api`.

### Contact Form Does Not Send Email

Check:

- `MAIL_MAILER` setting.
- SMTP host.
- SMTP username.
- SMTP password.
- SMTP port.
- SMTP encryption.

Even if email is not configured, contact messages should still be saved in the database if the API request succeeds.

## Final Production Checklist

Before calling the deployment complete, confirm:

- HTTPS works.
- `https://femiowoyele.com` loads.
- `https://femiowoyele.com/about` loads after direct refresh.
- `https://femiowoyele.com/admin` loads after direct refresh.
- `https://femiowoyele.com/api/health` returns JSON.
- `.env` is not publicly visible.
- Laravel source code is not publicly visible.
- `APP_DEBUG=false`.
- Production database credentials are not in GitHub.
- Admin password is not the seeded `password` value.
- cPanel account has two-factor authentication enabled.
- GitHub account has two-factor authentication enabled.
- A rollback release exists.
- A cPanel backup exists.

## Maintenance Rules

Use these rules after launch:

- Deploy only from the `main` branch.
- Run tests before every deployment.
- Keep release folders for rollback.
- Keep `.env` and `storage` in the `shared` folder.
- Do not edit live files manually except for emergency recovery.
- Document every new environment variable.
- Keep database migrations backward-compatible when possible.
- Avoid features that require persistent processes on shared hosting.
- Move to a VPS or managed Laravel platform when CMS usage becomes business-critical.

## Information Needed For The First Real Deployment

No clarification is required to write this guide, but these values are required before anyone can actually deploy:

```text
1. cPanel username
2. Namecheap server hostname
3. Confirmation that public_html is dedicated to femiowoyele.com
4. Production database name
5. Production database username
6. Production database password
7. SMTP provider details, or confirmation to use MAIL_MAILER=log temporarily
8. Whether femiowoyele.com or www.femiowoyele.com should be canonical
```

Use the worksheet near the top of this guide to collect them.

## Official References

- Namecheap SSH access: https://www.namecheap.com/support/knowledgebase/article.aspx/10040/2210/how-to-enable-ssh-shell-in-cpanel/
- Namecheap SSH/SFTP port and access notes: https://www.namecheap.com/support/knowledgebase/article.aspx/131/89/do-you-provide-ssh-if-yes-under-what-conditions/
- Namecheap Composer on shared hosting: https://www.namecheap.com/support/knowledgebase/article.aspx/9977/29/how-to-install-composer-on-shared-servers/
- Namecheap PHP selector: https://www.namecheap.com/support/knowledgebase/article.aspx/9417/2219/how-to-change-php-version-and-update-php-extensions-on-shared-servers/
- Namecheap PHP modules and limits: https://www.namecheap.com/support/knowledgebase/article.aspx/9697/2219/php-modules-limits-and-extensions-on-shared-hosting-servers/
- Namecheap cron limits: https://www.namecheap.com/support/knowledgebase/article.aspx/9453/29/how-to-run-scripts-via-cron-jobs/
- Namecheap shared hosting software limits: https://www.namecheap.com/support/knowledgebase/article.aspx/129/22/what-version-of-the-software-is-used-on-your-servers/
- GitHub deployment environments: https://docs.github.com/en/actions/reference/workflows-and-actions/deployments-and-environments
- GitHub environment management: https://docs.github.com/en/actions/how-tos/deploy/configure-and-manage-deployments/manage-environments
- GitHub Actions secrets: https://docs.github.com/en/actions/how-tos/write-workflows/choose-what-workflows-do/use-secrets
