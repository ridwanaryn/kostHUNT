# KostHUNT: Product Requirements Document

Version 0.1 (draft) | November 5, 2026 | Solo portfolio project | Laravel + Blade + MySQL

## 1. Overview

### 1.1 Product summary

KostHUNT is a responsive web application for finding and renting kost (rental rooms). Owners publish their buildings and rooms. Tenants search for available rooms, book them instantly, and manage their stay (invoices, renewal, move-out, maintenance requests, chat) inside the app. Visitors who are not logged in can browse everything, but must log in to book, review, favorite, or chat.

### 1.2 Problem statement

- Tenants need one place to find kost rooms that are actually available, with reliable details: price, facilities, address, photos, and reviews.
- Owners with several buildings in different locations need one place to publish rooms, see which are occupied or vacant, track rent, and handle tenant requests.

### 1.3 Goals

1. Deliver the full rental lifecycle: list, discover, book, track rent, renew or end, review.
2. Meet a bare-minimum production-grade standard (security, validation, authorization, clean MVC code structure, automated tests, seeded demo data, documented setup), since this is a portfolio project.
3. Be fully usable on mobile, tablet, and desktop.

### 1.4 Non-goals (excluded by your answers)

- Payment gateway and online payment (no Midtrans/Xendit for now), deposit, or booking fee.
- Map view or map pin.
- Gender type on rooms or filters.
- Survey or visit scheduling, tenant ID upload.
- Platform Admin role and owner verification (owners publish immediately).
- Social login and phone OTP (email and password only).
- Email or WhatsApp notifications (in-web messages only).
- Monetization (commission, subscription, featured listings).
- Hosting beyond a local project (local server with phpMyAdmin). PWA.

### 1.5 Status of decisions

Everything written as a requirement comes from your answers. Where you did not specify something, I did not decide it: it is listed in Section 13 (Open items) with a proposed default so work can proceed, and referenced in the text as (O-n). Please confirm or change each one.

## 2. Users and roles

| Role | Description | Key capabilities |
| --- | --- | --- |
| Guest | Visitor, not logged in | Browse and search rooms, view room and building pages, read reviews, register, log in |
| Tenant | Registered renter | Everything a guest can, plus: book rooms (many at the same time, across different buildings), manage own bookings and invoices, renew, request termination, review, favorite, chat with owners, submit maintenance requests, receive notifications |
| Owner | Registered property owner | Manage own buildings (multiple, in different locations) and rooms, view bookings and tenants, record payments, decide on termination requests, handle maintenance requests, chat with tenants, view dashboard and income reports |

Illustrative personas:

- Owner: runs three kost buildings in two cities. Wants to see which rooms are empty, who has not paid, and what needs fixing.
- Tenant: a student or worker moving to a new city. Wants to filter by budget and facilities, book quickly, and keep track of rent. May rent a second room for a family member.

### Permission matrix

| Action | Guest | Tenant | Owner |
| --- | --- | --- | --- |
| Browse, search, view room and building details | Yes | Yes | Yes |
| Register, log in | Yes | n/a | n/a |
| Book a room | No (redirected to login) | Yes | No |
| Favorite a room | No (login) | Yes | No |
| Review a room | No (login) | Yes, if eligible (O-12) | No |
| Start a chat | No (login) | Yes | Reply only |
| Create and edit buildings, rooms | No | No | Yes, own only |
| Record payment, decide on termination, update maintenance requests | No | No | Yes, own only |
| Dashboard, tenant list, income report | No | No | Yes, own only |

## 3. Domain concepts and business rules

### Glossary

- Building: a physical property with an address (street, city, province), owned by exactly one owner.
- Room: an individual listing inside a building, with its own price, facilities, photos, and availability.
- Booking: one tenant renting one room for a period (monthly or yearly).
- Invoice: the rent bill for one billing period of a booking.

### Business rules

| ID | Rule |
| --- | --- |
| BR-1 | An owner has many buildings; a building has many rooms; every room is its own listing. |
| BR-2 | The owner picks facilities per room, so rooms in the same building can differ. |
| BR-3 | One tenant account can hold any number of bookings at the same time, including in different buildings. |
| BR-4 | Booking is instant: no owner approval. It succeeds only if the room is active and no other upcoming or active booking overlaps the requested dates. |
| BR-5 | Rental period is monthly (N months) or yearly (N years). The end date is calculated from the start date and duration. |
| BR-6 | Availability check and booking creation happen in one database transaction with a row lock, so two tenants can never double-book a room. |
| BR-7 | The rent price is copied onto the booking when it is created. Later price changes do not affect existing bookings. |
| BR-8 | There is no online payment. Invoices and their paid or unpaid status are tracked in the app (O-7). |
| BR-9 | Invoices are generated per billing period of a booking (O-8). |
| BR-10 | Renewal, early termination, and move-out are handled in the app (O-10, O-11). |
| BR-11 | Owners see and change only their own data; tenants see and change only their own data. Enforced server-side by policies, not only by hiding buttons. |
| BR-12 | A room is shown as Available when it is active and has no overlapping upcoming or active booking for the date being checked. |

## 4. Functional requirements

Priority: P0 = core flow, P1 = rental operations, P2 = engagement.

### 4.1 Authentication and accounts (P0)

Story: as a visitor I can create an account and log in with email and password.

- FR-A1: Register with name, email, password, and a chosen role of Owner or Tenant (O-22).
- FR-A2: Log in, log out, remember me, forgot and reset password, edit profile, change password (Laravel Breeze).
- FR-A3: Role-based access to owner and tenant areas. A guest who opens a protected page is sent to login and then returned to that page.
- FR-A4: Booking, favoriting, reviewing, and chat require a logged-in Tenant. Guests see a login prompt on those actions.
- FR-A5: Email verification and extra profile fields are decided in O-15 and O-21.

### 4.2 Owner: building management (P0)

Story: as an owner I can add several buildings in different locations and keep their details up to date.

- FR-B1: Create, view, edit, delete buildings with name, description, street, city, province, house rules (O-17), and multiple photos with a cover photo.
- FR-B2: Owner sees a list of only their buildings with room count, occupied count, and available count.
- FR-B3: Deleting a building or room with upcoming or active bookings is blocked (O-20).
- FR-B4: Photo upload validates type and size and stores files on disk (O-4).

### 4.3 Owner: room management (P0)

Story: as an owner I can list each room separately with its own facilities and price.

- FR-R1: Create, view, edit, delete rooms inside a building. Fields: room name or number (unique within the building), description, size in square meters, monthly price, yearly price (O-8), electricity included (yes/no), water included (yes/no), facilities, photos, active toggle (O-16).
- FR-R2: Facilities are chosen per room from a facility list (O-18), for example AC, private bathroom, Wi-Fi, bed, wardrobe, desk.
- FR-R3: Each room shows a computed status: Available, Occupied, Upcoming, or Hidden.
- FR-R4: Changing a room price never changes existing bookings (BR-7).

### 4.4 Public browsing and search (P0)

Story: as a visitor I can search for available kost and look at details without logging in.

- FR-S1: Home page with a search bar and a list of the latest available rooms.
- FR-S2: Search results page with filters: province, city, monthly price range, facilities (multiple), and availability from a chosen start date. No gender filter and no map.
- FR-S3: Sorting by price (low to high, high to low), newest, and rating (Proposed).
- FR-S4: Paginated result cards showing cover photo, room name, building name, city, monthly price, average rating, availability badge, and favorite button.
- FR-S5: Room detail page: photo gallery, description, size, price (monthly and yearly if set), facilities, utilities included, building address, house rules, reviews and average rating, booking form (or login prompt), and Chat with owner button.
- FR-S6: Building page: building info, photos, and all its visible rooms.
- FR-S7: Only active rooms appear in public results.

### 4.5 Booking (P0)

Story: as a tenant I can book an available room instantly for a monthly or yearly period.

- FR-K1: Booking form with start date, period type (monthly or yearly), and duration. The total price is calculated on the server and shown before confirming.
- FR-K2: Validation: logged-in Tenant, start date not in the past and within the allowed advance window (O-9), room active, no overlap with another upcoming or active booking.
- FR-K3: On success the booking is created immediately, the first invoice is generated, and both tenant and owner are notified.
- FR-K4: Booking status is Upcoming when the start date is in the future and Active when it has started. A daily scheduled job moves Upcoming to Active and Active to Completed on the correct dates.
- FR-K5: A tenant can hold multiple bookings at once. The tenant has a My bookings list and a booking detail page (room, dates, price, invoices, actions).
- FR-K6: The owner has a Bookings list filterable by building, room, and status.

### 4.6 Invoices and payment tracking (P1)

Story: as an owner I can see who has paid; as a tenant I can see what I owe.

- FR-I1: Invoices are generated per billing period (O-8) with invoice number, period, amount, due date, and status (Unpaid, Paid).
- FR-I2: Overdue is shown when an unpaid invoice is past its due date.
- FR-I3: The owner records that payment was received, which sets the invoice to Paid with a paid date (O-7).
- FR-I4: Billing reminders: a daily scheduled job notifies the tenant before the due date and when an invoice becomes overdue (O-14).
- FR-I5: Tenant sees all own invoices; owner sees invoices per building, room, and tenant, filterable by status.

### 4.7 Renewal, termination, and move-out (P1)

Story: as a tenant I can extend my stay or leave early; as an owner I can see and decide on these requests.

- FR-T1: Renewal: a tenant with an Active booking can extend it for a monthly or yearly period. It succeeds if no other booking conflicts with the new dates (O-11).
- FR-T2: Early termination: the tenant submits a request with a desired move-out date and optional reason. The owner approves or rejects it (O-10).
- FR-T3: Move-out: when a booking ends by date or approved termination, its status becomes Completed or Terminated and the room becomes available again from the end date.
- FR-T4: No deposit, penalty, or refund logic is included.

### 4.8 Reviews and ratings (P2)

- FR-V1: An eligible tenant can rate a room from 1 to 5 stars with an optional comment (O-12).
- FR-V2: Room and building pages show the average rating, review count, and review list.
- FR-V3: One review per booking.

### 4.9 Favorites (P2)

- FR-F1: A tenant can add or remove a room from favorites and view a Favorites page.
- FR-F2: A room can be favorited only once per tenant.

### 4.10 Chat (P2)

- FR-C1: A tenant can start a conversation with a room's owner from the room page. The owner can reply.
- FR-C2: Both sides have an inbox with conversations, unread indicators, and message history (O-13).
- FR-C3: Users can only open conversations they belong to.

### 4.11 Maintenance requests (P1)

- FR-M1: A tenant with an Active booking can submit a request for their room with title and description (O-19).
- FR-M2: The owner sees requests per building and room and updates status (Open, In progress, Resolved) with a note.
- FR-M3: The tenant is notified of status changes.

### 4.12 Owner dashboard, tenants, and reports (P1)

- FR-D1: Dashboard summary: number of buildings, rooms, occupancy rate, income this month, unpaid and overdue invoices, pending termination requests, open maintenance requests.
- FR-D2: Tenant list per building: current and past tenants with room, dates, and booking status.
- FR-D3: Income report: income from paid invoices by month or year, and by building, with a date range filter (O-23).
- FR-D4: Tenant dashboard: own bookings, unpaid invoices, notifications, favorites.

### 4.13 Notifications (P1)

- FR-N1: Action feedback appears as pop-up messages (toasts) in the web page after an action succeeds or fails.
- FR-N2: Notable events also create stored in-app notifications (O-14): new booking, payment recorded, billing reminder, overdue invoice, termination request and decision, maintenance update, new chat message.
- FR-N3: A notification bell shows the unread count and a list, with mark as read.
- FR-N4: No email or WhatsApp notifications.

## 5. Non-functional requirements

| Area | Requirement |
| --- | --- |
| Security | CSRF protection on all forms; Blade output escaping; passwords hashed; role middleware plus Laravel Policies for every owner and tenant resource; rate limiting on login and chat. |
| Validation | Server-side validation through Form Request classes; clear inline error messages. |
| Data integrity | Foreign keys, unique constraints (for example one review per booking, one favorite per tenant and room), and database transactions for booking, renewal, and invoice creation. |
| Performance | Eager loading to avoid N+1 queries, pagination on all lists, indexes on search and filter columns (city, province, price, room and booking dates). |
| Uploads | Image-only uploads with type and size limits, stored outside the web root path through the storage link, with generated file names. |
| Responsiveness | Mobile-first layout that works at phone, tablet, and desktop widths. |
| Accessibility | Semantic HTML, labels on form fields, alt text on photos, sufficient color contrast. |
| Error handling | Friendly 403, 404, 419, and 500 pages; user-friendly messages instead of stack traces. |
| Quality | Feature tests for booking, double-booking prevention, authorization, invoicing, and the scheduler; factories and seeders for demo data. |
| Maintainability | MVC with thin controllers; validation in Form Requests; authorization in Policies; business logic in service classes; enums for statuses; migrations for every schema change; code style enforced automatically (Sections 6.3 and 6.6). |
| Documentation | README with local setup (PHP, Composer, MySQL, phpMyAdmin), seeding, scheduler command, and demo accounts. |
| Localization | Currency, timezone, and UI language per O-1 and O-5. |

## 6. Technical stack and architecture

| Item | Decision |
| --- | --- |
| Backend | Laravel with PHP (version: O-3) |
| Views | Blade templates (server-rendered MVC) |
| Auth | Laravel Breeze, Blade stack |
| Database | MySQL, managed through phpMyAdmin |
| Styling | Breeze default (Tailwind CSS); you said styling is up to me |
| Interactivity | Alpine.js, which ships with Breeze; Livewire not used unless you decide otherwise (O-3) |
| Hosting | Local project only |
| Image storage | Local disk (O-4) |
| Background jobs | Laravel scheduler for daily status updates, invoice generation, and reminders; run locally with the schedule command |

### 6.1 MVC layers and request flow

KostHUNT follows Laravel's MVC pattern: Models hold the data, Views display it, and thin Controllers connect them. Form Requests, Policies, and Services support the pattern so controllers stay small.

&#91;embedded content: request flow through the MVC layers · 8 parts, 1 highlighted\]

A request enters at the Route, is validated and authorized, is handled by a thin Controller, and is processed by a Service that uses the Models. The Controller then returns a View or a redirect. The highlighted Service layer is where business rules live.

| Layer | Responsibility | Must not |
| --- | --- | --- |
| Model | Data, relationships, casts, query scopes, small accessors | Read the request, return responses, or send notifications directly |
| View | Show the data it is given using Blade layouts and components | Run queries or contain business rules |
| Controller | Authorize, take validated input, call a Service or Model, return a view or redirect | Hold business rules, transactions, or validation rules |
| Form Request | Validation rules, messages, and the first authorization check | Change data |
| Policy | Decide whether a user may act on a specific record | Query unrelated data |
| Service | Use-case logic across models, in a transaction when needed | Know about HTTP, views, or the session |

### 6.2 Folder structure

The project keeps the standard Laravel layout, with controllers, form requests, and views grouped by role so each area stays small and easy to find.

```text
kosthunt/
├── app/
│   ├── Console/Commands/
│   │   ├── GenerateInvoices.php
│   │   ├── UpdateBookingStatuses.php
│   │   └── SendBillingReminders.php
│   ├── Enums/
│   │   ├── UserRole.php
│   │   ├── PeriodType.php
│   │   ├── BookingStatus.php
│   │   ├── InvoiceStatus.php
│   │   └── MaintenanceStatus.php
│   ├── Exceptions/
│   │   └── RoomNotAvailableException.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/            (Breeze)
│   │   │   ├── Catalog/         HomeController, RoomController, BuildingController
│   │   │   ├── Tenant/          DashboardController, BookingController, BookingRenewalController,
│   │   │   │                    BookingTerminationController, InvoiceController, ReviewController,
│   │   │   │                    FavoriteController, MaintenanceRequestController
│   │   │   ├── Owner/           DashboardController, BuildingController, RoomController, PhotoController,
│   │   │   │                    BookingController, TerminationDecisionController, InvoiceController,
│   │   │   │                    InvoicePaymentController, BuildingTenantController,
│   │   │   │                    IncomeReportController, MaintenanceRequestController
│   │   │   ├── Chat/            ConversationController, MessageController
│   │   │   ├── NotificationController.php
│   │   │   └── ProfileController.php   (Breeze)
│   │   ├── Middleware/
│   │   │   └── EnsureUserHasRole.php
│   │   └── Requests/            one Form Request per write action, grouped Owner/, Tenant/, Chat/, Catalog/
│   ├── Models/                  User, Building, Room, Facility, Photo, Booking, Invoice, Review,
│   │                            Conversation, Message, MaintenanceRequest
│   ├── Notifications/           BookingCreated, InvoicePaid, InvoiceDueSoon, InvoiceOverdue,
│   │                            TerminationRequested, TerminationDecided, MaintenanceStatusChanged,
│   │                            NewChatMessage
│   ├── Policies/                BuildingPolicy, RoomPolicy, BookingPolicy, InvoicePolicy, ReviewPolicy,
│   │                            ConversationPolicy, MaintenanceRequestPolicy, PhotoPolicy
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   └── Services/                AvailabilityService, BookingService, InvoiceService, TerminationService,
│                                PhotoService, OwnerDashboardService, IncomeReportService
├── bootstrap/
├── config/
│   └── kosthunt.php             app settings: booking advance window, reminder days, upload limits
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/                 DatabaseSeeder, FacilitySeeder, DemoSeeder
├── lang/en/
├── public/
├── resources/
│   ├── css/  js/
│   └── views/
│       ├── layouts/             app.blade.php, guest.blade.php
│       ├── components/          room-card, building-card, rating-stars, status-badge, toast,
│       │                        form fields, empty-state, confirm-modal
│       ├── catalog/             home, rooms/index, rooms/show, buildings/show
│       ├── tenant/              dashboard, bookings, invoices, favorites, maintenance-requests
│       ├── owner/               dashboard, buildings, rooms, bookings, invoices, tenants,
│       │                        reports, maintenance-requests
│       ├── chat/
│       ├── notifications/
│       ├── auth/  profile/      (Breeze)
│       └── errors/              403, 404, 419, 500
├── routes/
│   ├── web.php                  public and shared routes
│   ├── auth.php                 Breeze
│   ├── owner.php                prefix owner, middleware auth + role:owner
│   ├── tenant.php               prefix tenant, middleware auth + role:tenant
│   └── console.php              scheduled jobs
├── tests/
│   ├── Feature/                 booking, availability, invoices, authorization, search, ...
│   └── Unit/                    enums, services
├── .env.example
├── pint.json
├── phpstan.neon
└── README.md
```

- The same class name can exist in different role folders (Owner\\BuildingController and Catalog\\BuildingController). Routes use the full namespace or an import alias, never a bare name.
- Favorites have no model of their own: they are the favorites pivot table, reached through User::favoriteRooms().
- Controllers never open database transactions or contain business rules; that belongs in Services.
- Settings that may change (advance window, reminder days, upload limits) live in config/kosthunt.php and are read with config(), not hard-coded.

### 6.3 Coding standards

Code must be readable first: names explain intent, every class has one job, and the same pattern is used everywhere.

| Area | Standard |
| --- | --- |
| Code style | PSR-12, applied automatically by Laravel Pint. `declare(strict_types=1)` in every new PHP file; typed properties, parameters, and return types everywhere. |
| Naming | Models singular PascalCase (Booking); tables plural snake\_case (bookings); methods and variables camelCase; Blade files and components kebab-case; enum cases PascalCase; route names in dot notation (owner.buildings.index). |
| Controllers | RESTful and thin: only the seven resource methods (index, create, store, show, edit, update, destroy), one controller per resource, and a single-action controller for anything else (for example InvoicePaymentController). A controller method authorizes, takes validated input, calls a model or service, and returns a view or redirect. Target: about 15 lines per method. |
| Models | Hold relationships, casts, query scopes, and small accessors only. Explicit `$fillable` on every model, never an empty `$guarded`. Status values are backed enums cast on the model. Reusable queries are named scopes (for example Room::available(), Room::filter()). |
| Views | Blade only displays data. No database queries, no business rules, no raw PHP blocks. Repeated markup becomes a Blade component. Visibility of buttons uses @can, but the server-side Policy is the real protection. |
| Validation | Every write action has its own Form Request class holding the rules, custom messages, and the authorize() check. Controllers never call `$request->validate()` inline. |
| Authorization | One Policy per owned model, registered through auto-discovery, called with `$this->authorize()` in controllers. Route model binding is scoped so an owner can only resolve their own buildings and rooms. |
| Services | Business logic that spans several models or needs a transaction lives in a service class with one public method per use case (for example BookingService::create()). Services are injected through the constructor. |
| Errors | Domain failures are custom exceptions (RoomNotAvailableException). The controller catches them and redirects back with input and an error message. No silent catch blocks. |
| Configuration | No magic numbers in code. Advance window, reminder days, upload limits, and pagination size are read from config/kosthunt.php. |
| Money and dates | Amounts stored as whole integers (Rupiah has no decimals, O-5) and formatted by one helper; dates cast to Carbon and shown in the app timezone. |
| Database | Every schema change is a migration. Foreign keys, unique constraints, and indexes are declared in migrations. Seeders can be run repeatedly without duplicating data. |
| Performance | Eager load relationships with with(). In local development, `Model::preventLazyLoading()` is enabled so N+1 queries fail loudly. |
| Comments | Prefer clear names over comments. Add a short docblock only where the reason is not obvious, for example why a row lock is taken. |
| Version control | Small commits per milestone with clear messages. `.env` is never committed; `.env.example` is kept current. |

### 6.4 Module map (Model, View, Controller)

Each module from Section 4 is built from the same set of parts, so a reader can find any feature by following one row.

| Module | Models | Controllers | Form Requests | Policy and Services | Views folder |
| --- | --- | --- | --- | --- | --- |
| Accounts (4.1) | User | Auth\*, ProfileController (Breeze) | Breeze requests, role added at registration | EnsureUserHasRole middleware | auth/, profile/ |
| Buildings (4.2) | Building, Photo | Owner\\BuildingController, Owner\\PhotoController | StoreBuildingRequest, UpdateBuildingRequest | BuildingPolicy, PhotoPolicy; PhotoService | owner/buildings/ |
| Rooms (4.3) | Room, Facility, Photo | Owner\\RoomController | StoreRoomRequest, UpdateRoomRequest | RoomPolicy; PhotoService | owner/rooms/ |
| Search and browsing (4.4) | Room, Building, Facility, Review | Catalog\\HomeController, Catalog\\RoomController, Catalog\\BuildingController | RoomSearchRequest | Room scopes (available, filter, sort) | catalog/ |
| Booking (4.5) | Booking, Room, Invoice | Tenant\\BookingController, Owner\\BookingController | StoreBookingRequest | BookingPolicy; BookingService, AvailabilityService | tenant/bookings/, owner/bookings/ |
| Invoices (4.6) | Invoice | Tenant\\InvoiceController, Owner\\InvoiceController, Owner\\InvoicePaymentController | none for read pages | InvoicePolicy; InvoiceService; commands GenerateInvoices, SendBillingReminders | tenant/invoices/, owner/invoices/ |
| Renewal and termination (4.7) | Booking | Tenant\\BookingRenewalController, Tenant\\BookingTerminationController, Owner\\TerminationDecisionController | RenewBookingRequest, RequestTerminationRequest, DecideTerminationRequest | BookingPolicy; BookingService, TerminationService; command UpdateBookingStatuses | tenant/bookings/, owner/bookings/ |
| Reviews (4.8) | Review | Tenant\\ReviewController | StoreReviewRequest | ReviewPolicy | catalog/rooms/show (reviews component) |
| Favorites (4.9) | User (favoriteRooms), Room | Tenant\\FavoriteController | none | none; route is behind the tenant role | tenant/favorites/ |
| Chat (4.10) | Conversation, Message | Chat\\ConversationController, Chat\\MessageController | StoreMessageRequest | ConversationPolicy | chat/ |
| Maintenance (4.11) | MaintenanceRequest | Tenant\\MaintenanceRequestController, Owner\\MaintenanceRequestController | StoreMaintenanceRequest, UpdateMaintenanceRequest | MaintenanceRequestPolicy | tenant/maintenance-requests/, owner/maintenance-requests/ |
| Dashboard and reports (4.12) | Reads Booking, Invoice, Room, MaintenanceRequest | Owner\\DashboardController, Tenant\\DashboardController, Owner\\BuildingTenantController, Owner\\IncomeReportController | IncomeReportRequest | OwnerDashboardService, IncomeReportService | owner/dashboard, owner/tenants, owner/reports, tenant/dashboard |
| Notifications (4.13) | Laravel DatabaseNotification | NotificationController | none | Notification classes listed in Section 6.2 | notifications/, toast and bell components |

### 6.5 Example: a booking request through the layers

Booking a room touches every layer, and each layer does only its own job.

1. Route: `POST /rooms/{room}/bookings` is named `tenant.bookings.store` and sits in the auth and tenant-role group.
2. Middleware: EnsureUserHasRole rejects anyone who is not a Tenant with a 403.
3. Form Request: StoreBookingRequest checks the input (start date, period type, duration, advance window from config) and calls authorize() through BookingPolicy.
4. Controller: Tenant\\BookingController@store passes the validated data and the Room model to BookingService::create(). It contains no rules of its own.
5. Service: BookingService opens a database transaction, locks the room row, asks AvailabilityService whether the dates are free, creates the Booking, asks InvoiceService for the first Invoice, and dispatches the BookingCreated notification.
6. Models: Booking, Invoice, and Room save the data, with statuses cast to enums.
7. Success: the controller redirects to `tenant.bookings.show` with a success message, which the toast component shows.
8. Failure: if the dates are taken, the service throws RoomNotAvailableException. The controller catches it and redirects back with the input and an error message.
9. View: `tenant/bookings/show.blade.php` only displays the booking passed to it and its invoices, using components such as status-badge.

### 6.6 Quality tooling and review checklist

Quality is checked by tools first and by a short checklist second (tool choices: O-24).

| Tool | Purpose | Rule |
| --- | --- | --- |
| Laravel Pint | Code style (PSR-12) | Runs before every commit; no violations |
| Larastan | Static analysis of types and relationships | Passes at the agreed level with no ignored errors added casually |
| PHPUnit feature tests | Booking, availability, invoicing, authorization, search, scheduler | Run before merging a milestone; all pass |
| PHPUnit unit tests | Enums, services, date and price calculations | All pass |
| Laravel Debugbar (development only) | Spot N+1 and slow queries | Not installed in production dependencies |
| Migrations and seeders | Rebuild the database from scratch | `migrate:fresh --seed` always works |

Checklist for every pull request or milestone:

- [ ] Controller methods are thin and contain no business rules or transactions.
- [ ] Every write action has a Form Request and every owned resource has a Policy check.
- [ ] Views contain no queries and no business logic; repeated markup is a component.
- [ ] No magic numbers; settings come from config/kosthunt.php.
- [ ] Lists are paginated and relationships are eager loaded.
- [ ] New behavior has a feature test, including a 403 test for other users.
- [ ] Pint and Larastan pass, and the README is still correct.

## 7. Data model

All tables use an auto-increment id and created\_at / updated\_at unless noted.

| Table | Main columns | Notes |
| --- | --- | --- |
| users | name, email, password, role (owner or tenant), email\_verified\_at | Breeze users table plus role. Extra profile fields: O-21 |
| buildings | owner\_id (FK users), name, description, street, city, province, house\_rules | Address fields: O-6. House rules level: O-17. Soft delete |
| rooms | building\_id (FK), name, description, size\_sqm, price\_monthly, price\_yearly (nullable), electricity\_included, water\_included, is\_active | Unique (building\_id, name). Soft delete |
| facilities | name, icon (optional) | Fixed list, seeded (O-18) |
| facility\_room | room\_id, facility\_id | Pivot. Unique pair |
| photos | imageable\_type, imageable\_id, path, is\_cover, sort\_order | Polymorphic: used by buildings and rooms |
| bookings | tenant\_id, room\_id, renewed\_from\_id (nullable), period\_type (monthly or yearly), duration, start\_date, end\_date, rent\_amount, status, termination\_requested\_at, requested\_move\_out\_date, termination\_reason, termination\_owner\_note, ended\_at | Index on (room\_id, start\_date, end\_date) for overlap checks |
| invoices | booking\_id, invoice\_number (unique), period\_start, period\_end, amount, due\_date, status (unpaid or paid), paid\_at | Overdue is derived from due\_date |
| reviews | booking\_id (unique), room\_id, tenant\_id, rating (1 to 5), comment | One review per booking |
| favorites | tenant\_id, room\_id | Unique pair |
| conversations | tenant\_id, owner\_id, room\_id (nullable) | One conversation per tenant, owner, and room |
| messages | conversation\_id, sender\_id, body, read\_at |  |
| maintenance\_requests | booking\_id, room\_id, tenant\_id, title, description, status (open, in\_progress, resolved), owner\_note, resolved\_at |  |
| notifications | Laravel database notifications table | Stored in-app notifications (O-14) |

### Relationships

- User (owner) has many Buildings; Building has many Rooms.
- Room belongs to many Facilities; Room has many Photos; Building has many Photos.
- Room has many Bookings; User (tenant) has many Bookings.
- Booking has many Invoices, has at most one Review, and has many Maintenance requests.
- Booking may point to the booking it renewed (renewed\_from\_id).
- User (tenant) and Room are linked many-to-many through Favorites.
- Conversation links one tenant and one owner (and optionally a room) and has many Messages.

### Booking status lifecycle

| Status | Meaning | Moves to |
| --- | --- | --- |
| Upcoming | Booked, start date in the future | Active (on start date), Termination requested |
| Active | Rental in progress | Completed (on end date), Termination requested |
| Termination requested | Tenant asked to leave early, waiting for owner | Terminated (approved, end date set to move-out date) or back to the previous status (rejected) |
| Completed | Ended on the planned end date | Final |
| Terminated | Ended early after approval | Final |

## 8. User flows

### 8.1 Guest to booked room

1. Guest opens the home page and searches by city, price range, facilities, and start date.
2. Guest opens a room detail page and reads the details and reviews.
3. Guest presses Book. Not logged in, so they are sent to login or register, then returned to the same room page.
4. Tenant chooses start date, period type, and duration. The page shows the calculated total.
5. Tenant confirms. In one transaction the server locks the room, checks for overlap, creates the booking, creates the first invoice, and creates notifications.
6. Tenant lands on the booking detail page with a success pop-up.
7. If the dates are taken, the form keeps the input and shows an error pop-up with a message to pick other dates.

### 8.2 Owner listing setup

1. Owner registers with the Owner role and opens an empty dashboard.
2. Owner adds a building: name, description, street, city, province, rules, photos.
3. Owner adds rooms to the building: name, size, prices, utilities, facilities, photos.
4. Active rooms appear in public search immediately.
5. Owner repeats for other buildings in other locations.

### 8.3 Rent cycle and payment tracking

1. The daily scheduler creates the next invoice when a billing period approaches.
2. The scheduler notifies the tenant before the due date.
3. Tenant pays the owner outside the app.
4. Owner opens the invoice and marks it Paid. The tenant is notified.
5. If the due date passes unpaid, the invoice shows Overdue and both sides are notified.

### 8.4 Renewal

1. Tenant opens an Active booking and chooses Renew.
2. Tenant picks monthly or yearly and a duration.
3. The server checks that no other booking conflicts with the new dates.
4. A new linked booking is created, with its own invoice. Otherwise an error pop-up explains the conflict.

### 8.5 Early termination and move-out

1. Tenant opens the booking and requests termination with a move-out date and optional reason.
2. Owner is notified and opens the request.
3. Owner approves or rejects, optionally with a note.
4. If approved, the end date becomes the move-out date, the status becomes Terminated, and the room becomes available again after that date.
5. If rejected, the booking returns to its previous status and the tenant is notified.
6. Bookings that reach their end date are set to Completed by the scheduler.

### 8.6 Maintenance request

1. Tenant with an Active booking submits a title and description.
2. Owner sees it in the maintenance list and sets Open, In progress, or Resolved with a note.
3. Tenant is notified of each change.

### 8.7 Chat and review

- Chat: tenant presses Chat with owner on a room page, sends a message, and the owner replies from the inbox. Both see unread counts.
- Review: after a booking becomes eligible (O-12), the tenant gives a star rating and comment on the room. It appears on the room and building pages and updates the average rating.

## 9. Pages, routes, and endpoints

The app uses server-rendered Blade pages, so there is no separate JSON API. Each row below is a page (GET) or a form endpoint (POST, PATCH, DELETE). Role middleware is applied by area. Routes are named in dot notation (for example owner.buildings.index) and grouped in routes/web.php, routes/owner.php, and routes/tenant.php; Section 6.4 maps each module to its controllers.

### 9.1 Public and authentication

| Method | URI | Access | Purpose |
| --- | --- | --- | --- |
| GET | / | Public | Home with search and latest rooms |
| GET | /rooms | Public | Search results with filters and sorting |
| GET | /rooms/{room} | Public | Room detail |
| GET | /buildings/{building} | Public | Building detail with its rooms |
| GET, POST | /register | Guest | Sign up with role |
| GET, POST | /login | Guest | Log in |
| POST | /logout | Logged in | Log out |
| GET, POST | /forgot-password, /reset-password | Guest | Password reset |
| GET, PATCH | /profile | Logged in | View and edit profile |
| PUT | /password | Logged in | Change password |

### 9.2 Tenant area (auth + role tenant)

| Method | URI | Purpose |
| --- | --- | --- |
| GET | /tenant/dashboard | Overview of bookings, unpaid invoices, notifications |
| POST | /rooms/{room}/bookings | Create a booking |
| GET | /tenant/bookings | My bookings list |
| GET | /tenant/bookings/{booking} | Booking detail |
| POST | /tenant/bookings/{booking}/renew | Renew a booking |
| POST | /tenant/bookings/{booking}/termination | Request early termination |
| GET | /tenant/invoices | My invoices |
| GET | /tenant/invoices/{invoice} | Invoice detail |
| POST | /rooms/{room}/reviews | Submit a review |
| POST, DELETE | /rooms/{room}/favorite | Add or remove favorite |
| GET | /tenant/favorites | Favorites list |
| GET | /tenant/maintenance-requests | My requests |
| GET, POST | /tenant/maintenance-requests/create, /tenant/maintenance-requests | New request form and submit |
| GET | /tenant/maintenance-requests/{request} | Request detail |

### 9.3 Owner area (auth + role owner)

| Method | URI | Purpose |
| --- | --- | --- |
| GET | /owner/dashboard | Summary figures |
| GET, POST | /owner/buildings, /owner/buildings/create | Building list, create |
| GET, PATCH, DELETE | /owner/buildings/{building}, /owner/buildings/{building}/edit | Building detail, edit, delete |
| GET, POST | /owner/buildings/{building}/rooms, /owner/buildings/{building}/rooms/create | Room list and create for a building |
| GET, PATCH, DELETE | /owner/rooms/{room}, /owner/rooms/{room}/edit | Room detail, edit, delete |
| PATCH | /owner/rooms/{room}/toggle-active | Show or hide a room |
| POST | /owner/buildings/{building}/photos, /owner/rooms/{room}/photos | Upload photos |
| PATCH, DELETE | /owner/photos/{photo}/cover, /owner/photos/{photo} | Set cover, delete photo |
| GET | /owner/bookings | Bookings list with filters |
| GET | /owner/bookings/{booking} | Booking detail |
| POST | /owner/bookings/{booking}/termination/approve | Approve termination |
| POST | /owner/bookings/{booking}/termination/reject | Reject termination |
| GET | /owner/invoices | Invoices with filters |
| POST | /owner/invoices/{invoice}/mark-paid | Record payment received |
| GET | /owner/buildings/{building}/tenants | Tenant list for a building |
| GET | /owner/reports/income | Income report |
| GET | /owner/maintenance-requests | Requests list |
| PATCH | /owner/maintenance-requests/{request} | Update status and note |

### 9.4 Shared logged-in features

| Method | URI | Purpose |
| --- | --- | --- |
| GET | /chat | Inbox |
| GET | /chat/{conversation} | Conversation |
| POST | /rooms/{room}/chat | Tenant starts a conversation |
| POST | /chat/{conversation}/messages | Send a message |
| GET | /notifications | Notification list |
| POST | /notifications/{id}/read, /notifications/read-all | Mark as read |

Error pages: 403, 404, 419, 500.

## 10. Success metrics (proposed targets)

| Metric | Target |
| --- | --- |
| End-to-end demo | A scripted demo covering owner setup, tenant booking, invoice payment, renewal, termination, review, chat, and maintenance runs without errors |
| Double-booking protection | In a concurrency test, exactly one of several simultaneous bookings for the same room and dates succeeds |
| Automated tests | All P0 and P1 flows covered by feature tests, all passing |
| Authorization | Every owner and tenant route has a test proving another user gets 403 |
| Responsiveness | No broken layout or sideways scrolling at 360, 768, and 1280 pixel widths |
| Query efficiency | No N+1 queries on list and search pages |
| Setup | A new machine can run the app from the README, with demo data, in a short, documented sequence of steps |
| Code quality | Pint reports no violations, Larastan passes, and the Section 6.6 checklist is satisfied: thin controllers, Form Requests, Policies, no logic in views |

## 11. Milestones (no dates, since no timeline was given: O-2)

| Milestone | Scope |
| --- | --- |
| M0 Setup | Laravel project, Breeze, MySQL connection, base layout, toast component, README skeleton, folder structure (Section 6.2), Pint and Larastan configured |
| M1 Accounts | Registration with role, role middleware, profile |
| M2 Listings | Buildings, rooms, facilities, photos (owner side) |
| M3 Discovery | Home, search, filters, room and building pages (public) |
| M4 Booking | Booking form, availability and double-booking protection, booking lists |
| M5 Billing | Invoices, mark as paid, scheduler, billing reminders |
| M6 Rental changes | Renewal, termination, move-out, status scheduler |
| M7 Engagement | Reviews, favorites |
| M8 Chat | Conversations and inbox |
| M9 Maintenance | Tenant requests and owner handling |
| M10 Insight | Owner dashboard, tenant list, income report, tenant dashboard, notifications bell |
| M11 Hardening | Authorization tests, N+1 review, error pages, seeders, demo data, README, final responsive pass |

## 12. Risks

| Risk | Mitigation |
| --- | --- |
| Double booking under simultaneous requests | Transaction with row lock plus a concurrency test (BR-6) |
| Scheduler not running on a local machine, so statuses, invoices, and reminders go stale | Document the schedule command in the README and provide a manual artisan command to run the daily jobs |
| Scope is large for one developer | Build in priority order (P0, P1, P2); chat and reports come last |
| No admin or moderation, so fake or abusive listings are possible | Accepted for this version; out of scope by your decision |
| No payment gateway, so Paid status depends on the owner recording it honestly | Accepted for this version; owner-only action with timestamp |
| Uploaded images grow on local disk | Size limits per image and number limit per room (O-4) |

## 13. Open items

These were not covered by your answers. Each row has a proposed default so development can continue; please confirm or change.

| ID | Topic | Proposed default (not final until you confirm) |
| --- | --- | --- |
| O-1 | UI language | English interface, with text kept in Laravel language files so Indonesian can be added later. You said location detail matters more than language |
| O-2 | Timeline | None given. Work is planned by milestone without dates |
| O-3 | Laravel version and Livewire | Latest stable Laravel supported by Breeze at project start; Blade with Alpine.js, no Livewire |
| O-4 | Image storage | Local public disk with the storage link, because the project runs locally. Limits: image types only, a maximum size per file, a maximum number per room and building |
| O-5 | Currency and timezone | Indonesian Rupiah (IDR) and Asia/Jakarta timezone |
| O-6 | Address input | Free-text street, city, and province, matched with LIKE in search. Alternative: dropdowns from seeded province and city tables |
| O-7 | Recording payment | Owner marks an invoice as Paid after receiving money offline. Tenant cannot self-confirm and no proof is uploaded |
| O-8 | Yearly pricing and billing | Owner sets a monthly price and an optional yearly price. If no yearly price is set, yearly booking is not offered for that room. Monthly bookings get one invoice per month; yearly bookings get one invoice per year of the term |
| O-9 | Booking start date | Today or later, up to 90 days ahead |
| O-10 | Early termination | Tenant requests a move-out date; owner approves or rejects. No notice period, penalty, or refund |
| O-11 | Renewal | Tenant can renew an Active booking within the last 30 days before it ends; instant if there is no conflict. Creates a new linked booking |
| O-12 | Review eligibility | Tenants with an Active or Completed booking for the room; one review per booking; not editable after submission; no owner replies |
| O-13 | Chat delivery | No real-time sockets. Messages appear on page load, with simple periodic refresh on the conversation page |
| O-14 | Notifications | Pop-up toasts for immediate feedback, plus stored in-app notifications so billing reminders are not lost if the tenant is offline. Reminder sent 3 days before the due date and when overdue |
| O-15 | Email verification | Off. Password reset mail uses the log mail driver locally |
| O-16 | Hiding a room | Owner can toggle a room active or hidden, for example during renovation |
| O-17 | House rules | Stored at building level and shown on room pages |
| O-18 | Facility list | Fixed seeded list; owners choose from it but cannot add new facilities |
| O-19 | Maintenance requests | Title and description only, no photo; status Open, In progress, Resolved with an owner note |
| O-20 | Deleting data | Buildings and rooms with upcoming or active bookings cannot be deleted; others are soft-deleted. Same rule for account deletion |
| O-21 | Profile fields | Name and email only; no phone number or avatar |
| O-22 | Roles per account | One role per account chosen at registration; an account cannot be both owner and tenant |
| O-23 | Income report | Based on invoices marked Paid, grouped by month or year and by building, with a date range filter; no export |
| O-24 | Code quality tooling | Laravel Pint for PSR-12 style, Larastan for static analysis (level to be set at project start), PHPUnit for tests, Laravel Debugbar in development only |
