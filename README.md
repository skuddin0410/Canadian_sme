# Canadian SME / Eventzen

A Laravel event management application for organizing events, selling tickets, managing exhibitors and attendees, and supporting event networking. It includes a Blade web interface, administration tools, and a JWT-authenticated API for client applications.

## Features

- **Events:** sessions, tracks, speakers, sponsors, floor plans, galleries, and event guides.
- **Registration and ticketing:** registration forms, ticket pricing, inventory, promo codes, waitlists, Stripe payments, and PDF invoices.
- **People and access:** attendees, exhibitors, representatives, support staff, registration desk users, and role-based permissions.
- **Exhibitor tools:** companies, booths, products, services, technical specifications, and training information.
- **Networking:** QR codes, connections, favorites, lead capture, and exports.
- **Engagement:** polls, newsletters, email templates, scheduled messages, and push notification integrations.
- **Administration:** landing page content, subscriptions, analytics, audit logs, and support inquiries.

## Technology stack

| Component | Technology |
| --- | --- |
| Backend | PHP, Laravel 11 |
| Frontend | Blade, Vite 5, Bootstrap 5, Tailwind CSS 3, Sass |
| Database | MySQL |
| Authentication | Laravel sessions and JWT (`tymon/jwt-auth`) |
| Authorization | Spatie Laravel Permission |
| Payments | Stripe |
| Documents and data | Dompdf, Laravel Excel |
| Images and QR codes | Intervention Image, BaconQrCode |
| Background processing | Laravel queues; database driver by default |
| Testing and formatting | PHPUnit 11, Laravel Pint |

Dependency versions are defined in `composer.json`, `package.json`, and their lock files.

## Requirements

- PHP **8.2 or later**, compatible with `composer.lock`.
- Composer 2.
- Node.js and npm compatible with `package-lock.json`. Vite 5 declares Node.js `^18.0.0 || >=20.0.0`; use a maintained release satisfying that constraint.
- MySQL and a database user with permission to create and alter tables.
- PHP extensions required by Composer, including PDO MySQL, Mbstring, XML/DOM, cURL, ZIP, and GD for image/QR processing.
- Write access for the application user to `storage/`, `bootstrap/cache/`, and `public/qrcodes/`. Other upload features may also write under `public/`.

Use MySQL for the full application: some migrations contain MySQL-specific SQL. SQLite is used by isolated QR tests but is not a verified replacement for the complete schema.

## Local setup

Run commands from the repository root.

### 1. Configure the environment

For a new checkout without an existing `.env`:

```bash
cp .env.example .env
```

The example file contains project-specific values. Replace its database and mail credentials and generate fresh application and JWT keys for your installation. Do not reuse the example secrets.

Update these values in `.env`, substituting your database credentials:

```dotenv
APP_NAME="Canadian SME"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
APP_TIMEZONE=America/Toronto

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=canadian_sme
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

MAIL_MAILER=log
MAIL_FROM_ADDRESS=hello@example.com
MAIL_FROM_NAME="${APP_NAME}"
```

Choose the timezone appropriate to your installation. With `MAIL_MAILER=log`, emails are written to the configured Laravel log. Configure a mail provider to receive OTPs and other messages in an inbox.

### 2. Install dependencies and generate keys

```bash
composer install
npm ci
php artisan key:generate
php artisan jwt:secret
composer check-platform-reqs
```

Generate keys once for a new installation. Preserve existing keys when updating an installation; replacing them affects encrypted data and authentication tokens.

### 3. Prepare the database and storage

Create the MySQL database named in `DB_DATABASE`, then run:

```bash
php artisan migrate
php artisan storage:link
php artisan db:seed
```

`DatabaseSeeder` creates roles, development accounts, sample events, categories, settings, templates, forms, and permissions. Seeders also invoke application helpers, including notification and QR generation; configure the integrations needed by those helpers before seeding.

Use the full seed for a new development database. Some seeders create records without checking existing entries, so repeating them can create duplicates or fail. Review individual seeders before running them against existing data. `migrate:fresh --seed` drops all tables and is suitable only for a disposable database.

### 4. Start development services

```bash
composer run dev
```

This starts the Laravel server, queue listener, log viewer, and Vite server together. Open **http://127.0.0.1:8000**.

Alternatively, run these in separate terminals:

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

## Development accounts

After a successful full seed, `AdminSeeder` creates these accounts with the password `password`:

| Role | Email |
| --- | --- |
| Super Admin | `admin@admin.com` |
| Event Admin | `event@admin.com` |
| Support Staff / Helpdesk | `support@staff.com` |
| Registration Desk | `registration@desk.com` |

Use `/admin/login` for administrator login and `/login` for general login. Replace development credentials before exposing an installation to other users. `AdminSeeder` also overwrites the user with ID `1`; review it before running against existing data.

## Integrations

Configure services required by the features you use. Several variables below are absent from `.env.example` and must be added manually.

| Service | Configuration |
| --- | --- |
| SMTP email | `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME` |
| Mailgun | `MAILGUN_DOMAIN`, `MAILGUN_SECRET`, `MAILGUN_ENDPOINT`; select the mailer in `config/mail.php` |
| Stripe | `STRIPE_SECRET`; use test credentials during development |
| CometChat | `COMETCHAT_APP_ID`, `COMETCHAT_API_KEY`, `COMETCHAT_REGION` |
| OneSignal | `ONESIGNAL_APP_ID`, `ONESIGNAL_REST_API_KEY`, `USER_AUTH_KEY`; see `config/onesignal.php` |
| Google Maps | `GOOGLE_MAPS_API_KEY` |
| Google reCAPTCHA v2 | `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY` |
| Amazon S3 | `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`; select the appropriate filesystem disk |
| Media limits | `MEDIA_STORAGE_LIMIT_MB`; see `config/media.php` |

Apple push credentials and certificate handling are configured directly in `app/Services/IOSPushService.php`. Review that service before enabling iOS notifications for a different app or environment.

After changing environment settings, run `php artisan config:clear`.

## Web and API routes

| Path | Purpose |
| --- | --- |
| `/` | Public landing page |
| `/admin/login` | Administrator login |
| `/login` | General user login |
| `/events/{event:slug}/login` | Event-specific login |
| `/events/{event:slug}/register` | Event-specific registration |
| `/up` | Laravel health endpoint |
| `/api/auth/login` | Request a login OTP (`POST`) |
| `/api/auth/verify-otp` | Verify an OTP (`POST`) |
| `/api/all-events` | List events (`GET`) |

Protected API routes use `auth:api` and the custom `jwtauth` middleware. Send the issued JWT in the `Authorization: Bearer <token>` header. Refer to `routes/api.php` and its controllers for endpoint payloads and responses.

```bash
php artisan route:list
php artisan route:list --path=api
```

## Queues and scheduled work

Keep a worker running for background jobs such as newsletters, scheduled bulk messages, notifications, and chat user provisioning:

```bash
php artisan queue:work --tries=3
```

Use a process supervisor for production workers and run `php artisan queue:restart` after deployment. Delayed messages require an active worker when their scheduled time arrives.

Console commands and scheduler definitions live in `routes/console.php`. Inspect tasks with `php artisan schedule:list`. The current `qr:update-user-qr-codes` command registers a schedule inside its command callback; it is not a top-level recurring task automatically registered by `schedule:run`.

## Tests and code style

Configure a dedicated database in `.env.testing` before running the full suite. `phpunit.xml` does not override the default database connection, so database-backed tests can otherwise inherit the local database settings.

```bash
php artisan test
```

Run the isolated QR download tests with:

```bash
php artisan test --filter=DownloadQrCodeTest
```

These tests create a temporary SQLite database and require PDO SQLite. The home page feature test depends on database-backed landing page data.

Check PHP formatting without changing files:

```bash
./vendor/bin/pint --test
```

## Project structure

```text
app/
  Console/       Custom Artisan commands
  Http/          Web/API controllers, middleware, and requests
  Jobs/          Queued background work
  Models/        Eloquent models
  Services/      Tickets, invoices, QR codes, newsletters, and integrations
  Support/       Audit and activity logging helpers
  helpers.php    Shared application functions
bootstrap/       Application setup and middleware registration
config/          Application and package configuration
database/        Migrations, seeders, and factories
public/          Web entry point, assets, and generated files
resources/       Blade views, JavaScript, and Sass
routes/          Web, API, admin, exhibitor, ticket, and other module routes
storage/         Logs, cache, sessions, and stored files
tests/           Unit and feature tests
```

## Deployment

Set the web server document root to `public/`. Configure the production `.env`, set `APP_ENV=production` and `APP_DEBUG=false`, and provide write permissions for cache, storage, QR, and upload directories. Preserve uploaded files and generated assets across releases.

A typical release includes:

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan queue:restart
```

Build assets in CI or another build environment if Node.js is unavailable on the server. Back up the database before migrations and seed only data explicitly required by the release.

The existing `.github/workflows/deploy.yml` deploys pushes to `main` over SSH using the GitHub secrets `PRIVATE_KEY`, `HOST`, `USERNAME`, and `DEPLOY_PATH`. It pulls code, runs Composer install **and update**, clears caches, and restarts PHP 8.2 FPM and Nginx. It does not build frontend assets or run migrations; account for those steps in your release process. Review the update step if releases must reproduce `composer.lock` exactly.

## Troubleshooting

| Problem | Checks |
| --- | --- |
| Missing application key or JWT secret | Generate keys for a new installation and clear cached configuration. |
| Database, cache, or session table errors | Check credentials, database availability, and `php artisan migrate:status`. |
| Vite manifest missing | Start `npm run dev` locally or run `npm run build`. |
| Uploaded files unavailable | Check `APP_URL`, the storage link, filesystem disk, and permissions. |
| QR generation fails | Check GD and write access to `public/qrcodes/` and `storage/framework/qr-locks/`. |
| Email does not arrive | Check mail settings, logs, and queue workers. The log mailer does not deliver email. |
| Queued work is delayed | Check the queue connection, workers, and `php artisan queue:failed`. |
| Environment changes have no effect | Run `php artisan config:clear` and restart long-running workers. |

## License

`composer.json` declares the project license as MIT. No standalone license file is currently included in the repository.
