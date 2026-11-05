# kostHUNT — Implementation Plan

Synthesized from `PRD.md` (functional spec) and `design.md` (visual spec).

## Stack

| Area | Decision |
| --- | --- |
| Backend | Laravel (latest stable) + PHP |
| Views | Blade (server-rendered MVC) + Alpine.js |
| Auth | Laravel Breeze (Blade stack) |
| Database | MySQL (via phpMyAdmin) |
| Styling | Tailwind CSS — **Neo-Brutalist Bento Grid** (design.md) |
| Quality | Pint, Larastan, PHPUnit, Debugbar (dev only) |
| Localization | English UI, IDR currency, Asia/Jakarta timezone |

## Design system (from design.md)

- **Aesthetic**: Neo-Brutalism + Bento Grid — bold, playful, tactile.
- **Palette**: Electric Yellow `#FFE600`, Vibrant Cyan `#00F0FF`, Bubblegum Pink `#FF90E8`, Neon Mint `#38EF7D`, Sunset Coral `#FF6B4A`, Lilac `#C4B5FD`, canvas `#F4F0EA`, onyx `#121212`.
- **Borders/shadows**: `border-[4px]`/`border-black`, hard shadows `shadow-[4px_4px_0px_#000]`, hover translate + shadow lift, active press.
- **Fonts**: Syne/Space Grotesk (headlines, uppercase) + Plus Jakarta Sans/Inter (body).
- **Components**: marquee bar, bento master card (2x2), campus card (1x2), live tracker (1x1), facility spotlight (2x1), promo ticket, map widget, search/filter bar, sticky price stamps.

## Milestones

| # | Milestone | Scope |
| --- | --- | --- |
| M0 | Setup | Laravel, Breeze, MySQL, design tokens, base layout + shared components, Pint/Larastan |
| M1 | Accounts | Register with role, role middleware, profile |
| M2 | Listings | Buildings, rooms, facilities (seeded), photos (owner) |
| M3 | Discovery | Home, search/filters, room & building pages (public) |
| M4 | Booking | Booking form, availability + double-booking protection, booking lists |
| M5 | Billing | Invoices, mark-paid, scheduler, reminders |
| M6 | Rental changes | Renewal, termination, move-out, status scheduler |
| M7 | Engagement | Reviews, favorites |
| M8 | Chat | Conversations + inbox (periodic refresh, no sockets) |
| M9 | Maintenance | Tenant requests + owner handling |
| M10 | Insight | Owner dashboard/reports/tenants, tenant dashboard, notification bell |
| M11 | Hardening | Authorization tests, N+1 review, error pages, seeders, README, responsive pass |

## Execution order

1. **Phase 0 (M0)** — environment + scaffold + design token layer.
2. **Phase 1 (M1–M4)** — accounts, listings, discovery, booking.
3. **Phase 2 (M5–M6, M9)** — billing, rental changes, maintenance.
4. **Phase 3 (M7–M8, M10)** — reviews/favorites, chat, dashboards/notifications.
5. **Phase 4 (M11)** — hardening.

## Folder structure, data model, routes, module map

Defined in `PRD.md` §6.2 (folder structure), §7 (data model), §9 (routes), §6.4 (module map). This plan defers to those sections rather than duplicating them.

## Open items

`PRD.md` §13 lists O-1…O-24 with defaults. All defaults accepted unless changed:
- O-22: one role per account (chosen at registration).
- O-8: monthly + optional yearly price; monthly = invoice/month, yearly = invoice/year.
- O-9: booking start date today…+90 days.
- O-11: renewal allowed in last 30 days of an Active booking.
- O-12: review requires Active or Completed booking; one per booking.

## Environment notes

PHP, Composer, and MySQL must be installed (Homebrew) before scaffolding. Node/npm already present.

## Progress

| Milestone | Status |
| --- | --- |
| M0 Setup | Done |
| M1 Accounts | Done |
| M2 Listings | Done |
| M3 Discovery | Done |
| M4 Booking | Done |
| M5 Billing | Done |
| M6 Rental changes | Done |
| M7 Engagement | Done |
| M8 Chat | Done |
| M9 Maintenance | Done |
| M10 Insight | Done |
| M11 Hardening | Done |

