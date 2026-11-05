# kostHUNT

A modern boarding-house (kost) search and rental platform for students and young
professionals. Search verified rooms, book instantly, track rent, renew or end your
stay, chat with owners, and leave reviews — all in one place.

Built with **Laravel 13 + Blade + Alpine.js**, a **Neo-Brutalist Bento** Tailwind design,
and **MySQL**.

## Features

- **Roles**: Guest, Tenant, Owner (one role per account, chosen at registration).
- **Owners**: manage multiple buildings and rooms, facilities, photos (cover + gallery).
- **Discovery**: home, search with filters (city, province, price range, facilities,
  availability date), sorting, room and building pages with ratings.
- **Booking**: instant booking with overlap protection (transaction + row lock), monthly
  or yearly terms, first invoice generated automatically.
- **Billing**: invoices per billing period, owner marks payments as paid, overdue
  tracking, due-soon and overdue reminders.
- **Rental changes**: renewal (last 30 days of an active booking), early termination
  requests with owner approval/rejection, automatic status updates.
- **Engagement**: reviews (one per eligible booking), favorites.
- **Chat**: tenant/owner conversations with unread counts and 15s polling refresh.
- **Maintenance**: tenants report issues; owners update status and add notes.
- **Insight**: owner dashboard, tenant list per building, income report, tenant
  dashboard, and an in-app notification bell.

## Requirements

- PHP 8.3+ (tested on PHP 8.5)
- Composer
- Node.js 20+ and npm
- MySQL 8+ (phpMyAdmin optional)

## Local setup

```sh
git clone <repo-url> kostHUNT
cd kostHUNT

composer install
cp .env.example .env
php artisan key:generate
```

Create the database and configure `.env` (the defaults already point at the values
below):

```sh
mysql -u root -e "CREATE DATABASE kosthunt CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -e "CREATE USER 'kosthunt'@'localhost' IDENTIFIED BY 'kosthunt'; GRANT ALL PRIVILEGES ON kosthunt.* TO 'kosthunt'@'localhost';"
```

`.env` database keys:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kosthunt
DB_USERNAME=kosthunt
DB_PASSWORD=kosthunt
```

Then migrate, seed demo data, link storage, and build the frontend:

```sh
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
```

Run the app:

```sh
php artisan serve
# or run server + queue + Vite together:
composer run dev
```

Open http://localhost:8000.

## Demo accounts

| Role   | Email                 | Password |
| ------ | --------------------- | -------- |
| Owner  | owner@kostHUNT.test    | password |
| Tenant | tenant@kostHUNT.test   | password |

The demo seeder creates two buildings, rooms with facilities, placeholder gallery
photos, an active booking with a paid + unpaid invoice, an upcoming booking, a review, a
favorite, a chat conversation, and a maintenance request.

## Scheduled jobs

Three jobs run daily (see `routes/console.php`):

- `kosthunt:generate-invoices` — create invoices as billing periods approach.
- `kosthunt:send-billing-reminders` — notify tenants of due-soon/overdue invoices.
- `kosthunt:update-booking-statuses` — move Upcoming → Active and Active → Completed.

Run the scheduler locally:

```sh
php artisan schedule:work
```

Or run the jobs manually:

```sh
php artisan kosthunt:generate-invoices
php artisan kosthunt:send-billing-reminders
php artisan kosthunt:update-booking-statuses
```

In production, a single cron entry is enough:

```cron
* * * * * cd /path/to/kostHUNT && php artisan schedule:run >> /dev/null 2>&1
```

## Testing & code quality

```sh
php artisan test
vendor/bin/pint --test
vendor/bin/phpstan analyse --memory-limit=1G
```

The suite covers authentication, role authorization (403s), building/room/photo
management, search/filters, booking and double-booking prevention, invoices and the
scheduler, renewal/termination, reviews, favorites, chat, maintenance, dashboards,
reports, notifications, and a query-efficiency (N+1) guard.

## Configuration

Adjustable settings live in `config/kosthunt.php`:

- `uploads` — disk, per-file size limit, max photos per building/room.
- `pagination.per_page` — list page size.
- `booking.advance_days` — how far ahead a tenant can book (default 90).
- `booking.max_duration` — maximum periods per booking.
- `billing.reminder_days` — days before due date to remind.
- `billing.generate_days_before` — lead time for invoice generation.

Currency is Indonesian Rupiah (IDR) and the timezone is `Asia/Jakarta`.

## Project layout

```text
app/
├── Console/Commands/     GenerateInvoices, SendBillingReminders, UpdateBookingStatuses
├── Enums/                UserRole, PeriodType, BookingStatus, InvoiceStatus, ...
├── Http/Controllers/     Auth, Catalog, Tenant, Owner, Chat, NotificationController
├── Http/Middleware/      EnsureUserHasRole
├── Http/Requests/        Form Requests grouped by role
├── Models/               User, Building, Room, Facility, Photo, Booking, Invoice, ...
├── Notifications/        Booking, invoice, termination, chat, maintenance
├── Policies/             Per-resource authorization
└── Services/             Booking, Availability, Invoice, Termination, Photo, reports
```

## Notes

- No payment gateway: owners record payments received; the app tracks paid/unpaid.
- Chat is not real-time: the conversation page polls for new messages every 15 seconds.
- Photos are stored on the public disk (requires `php artisan storage:link`).
- Email verification is disabled; password-reset mail uses the `log` driver locally.
