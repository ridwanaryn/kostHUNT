# Design System Specification: Neo-Bento Kost (NgeKost!)

## 1. Overview & Brand Identity
- **Product**: **NgeKost!** — A modern boarding house (kost-kostan) search & rental platform designed for college students and young professionals (Gen-Z & Millennials).
- **Aesthetic Direction**: **Neo-Brutalism paired with Bento Grid Layout**.
- **Key Brand Attributes**: Bold, playful, transparent, hassle-free, neatly structured, and tactile.
- **Core Visual Markers**:
  - Thick solid black borders (`4px` / `border-black`).
  - Hard solid drop shadows without blur (`shadow-[4px_4px_0px_#000]`, `shadow-[6px_6px_0px_#000]`, `shadow-[8px_8px_0px_#000]`).
  - High-contrast color palette: Off-white canvas, Electric Yellow, Vibrant Cyan, Bubblegum Pink, Neon Mint, Sunset Coral, and Dark Onyx (`#121212`).
  - Sticker badges, precisely spaced asymmetrical bento cards, and promo ticket elements with dashed borders.

---

## 2. Color Palette & Tokens

### Base & Backgrounds
| Token Name | Hex Code | Tailwind / Role | Usage |
|---|---|---|---|
| `bg-canvas` | `#F4F0EA` | Canvas base | Main page background (warm retro paper beige) |
| `surface-white` | `#FFFFFF` | Card background | Bento card background, input fields, secondary badges |
| `surface-dark` | `#121212` | Solid Black | Borders (`#000` / `#121212`), primary typography, hard shadows |

### Neo-Brutalist Accent Colors
| Color Name | Hex Code | CSS / Class | Usage |
|---|---|---|---|
| **Electric Yellow** | `#FFE600` | `bg-[#FFE600]` | Brand primary, hero highlights, room counters, alert badges |
| **Vibrant Cyan** | `#00F0FF` | `bg-[#00F0FF]` | Primary search button, map pin status, interactive actions |
| **Bubblegum Pink** | `#FF90E8` | `bg-[#FF90E8]` | Tag badges (Female-only Kost), promo banners, campus highlights (ITB) |
| **Neon Mint** | `#38EF7D` | `bg-[#38EF7D]` | Favorite campus card, verified badge, online status |
| **Sunset Coral** | `#FF6B4A` | `bg-[#FF6B4A]` | Student ticket discount card, hot promo badge |
| **Lilac Purple** | `#C4B5FD` | `bg-[#C4B5FD]` | Filter badge, map pins, secondary highlights |

---

## 3. Typography System

- **Primary Headline Font**: **Syne** / **Space Grotesk** / **Cabinet Grotesk** (Bold sans-serif, uppercase, high-impact font).
- **Body &Here is the complete English translation of your Design System Specification document, maintaining all Markdown structures, technical tokens, and formatting.

---

# Design System Specification: Neo-Bento Kost (NgeKost!)

## 1. Overview & Brand Identity
- **Product**: **NgeKost!** — Modern boarding house (kost) search & rental platform for college students and young professionals (Gen-Z & Millennials).
- **Aesthetic Direction**: **Neo-Brutalism paired with Bento Grid Layout**.
- **Key Brand Attributes**: Bold, playful, transparent, hassle-free, neatly structured, and tactile interaction.
- **Core Visual Markers**:
  - Thick, solid black borders (`4px` / `border-black`).
  - Hard solid drop shadows without blur (`shadow-[4px_4px_0px_#000]`, `shadow-[6px_6px_0px_#000]`, `shadow-[8px_8px_0px_#000]`).
  - High-contrast color palette: Off-white canvas, Electric Yellow, Vibrant Cyan, Bubblegum Pink, Neon Mint, Sunset Coral, and Dark Onyx (`#121212`).
  - Sticker badges, precisely spaced asymmetrical bento cards, and promo ticket elements with dashed borders.

---

## 2. Color Palette & Tokens

### Base & Backgrounds
| Token Name | Hex Code | Tailwind / Role | Usage |
|---|---|---|---|
| `bg-canvas` | `#F4F0EA` | Canvas base | Main page background (warm retro paper beige) |
| `surface-white` | `#FFFFFF` | Card background | Bento card background, input fields, secondary badges |
| `surface-dark` | `#121212` | Solid Black | Borders (`#000` / `#121212`), primary typography, hard shadows |

### Neo-Brutalist Accent Colors
| Color Name | Hex Code | CSS / Class | Usage |
|---|---|---|---|
| **Electric Yellow** | `#FFE600` | `bg-[#FFE600]` | Brand primary, hero highlights, room counters, alert badges |
| **Vibrant Cyan** | `#00F0FF` | `bg-[#00F0FF]` | Main search button, status map pins, interactive actions |
| **Bubblegum Pink** | `#FF90E8` | `bg-[#FF90E8]` | Tag badges (Female Kost), promo banners, ITB campus highlights |
| **Neon Mint** | `#38EF7D` | `bg-[#38EF7D]` | Favorite campus cards, verified badges, online status |
| **Sunset Coral** | `#FF6B4A` | `bg-[#FF6B4A]` | Student ticket discount cards, hot promo badges |
| **Lilac Purple** | `#C4B5FD` | `bg-[#C4B5FD]` | Filter badges, map pins, secondary highlights |

---

## 3. Typography System

- **Primary Headline Font**: **Syne** / **Space Grotesk** / **Cabinet Grotesk** (Heavyweight sans-serif font, uppercase, high impact).
- **Body & Interface Font**: **Plus Jakarta Sans** / **Inter** (Modern sans-serif with high legibility at small text sizes).

### Typography Scale
- **Display / Hero H1**: `text-4xl` to `text-6xl`, font-black (`font-extrabold` / `font-black`), tracking-tight, uppercase, compact line-height.
- **Section Heading H2**: `text-2xl` to `text-3xl`, font-black, uppercase, tracking-tight.
- **Card Title H3**: `text-xl` to `text-2xl`, font-extrabold, uppercase.
- **Badge & Tag Label**: `text-xs` to `text-sm`, font-bold, uppercase, tracking-wider.
- **Body Regular**: `text-sm` to `text-base`, font-medium, text-neutral-800 (`#262626`).
- **Caption / Meta**: `text-xs`, font-bold, text-neutral-600.

---

## 4. Spacing, Elevation & Component Shapes

### Border & Radius Rules
- **Border Weight**:
  - Main Container & Bento Cards: `border-[4px] border-black` (or `border-[3px] border-black`).
  - Badges & Filter Chips: `border-[2px] border-black`.
  - Input Fields & Dropdowns: `border-[3px] border-black`.
- **Corner Radius**:
  - Bento Container & Cards: `rounded-2xl` (16px) to `rounded-3xl` (24px).
  - Buttons, Badges, & Chips: `rounded-xl` (12px) or `rounded-full` (capsule style).

### Elevation & Hard Shadow Tokens
- **Elevation 1 (Small / Pills / Chips)**:
  `shadow-[2px_2px_0px_#000]`
- **Elevation 2 (Standard Button / Cards)**:
  `shadow-[4px_4px_0px_#000]`
- **Elevation 3 (Hero Containers / Bento Master Card)**:
  `shadow-[6px_6px_0px_#000]` or `shadow-[8px_8px_0px_#000]`
- **Interactive Hover State**:
  `hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0px_#000] active:translate-x-0 active:translate-y-0 active:shadow-none transition-all duration-150`

---

## 5. UI Components Specification

### 1. Navigation & Marquee Header
- **Top Announcement Bar**: Ticker strip in Electric Yellow (`#FFE600`) with a 3px bottom border, running promo & benefit text (`360° ONLINE SURVEY • STUDENT CASHBACK • NO DEPOSIT REQUIRED`).
- **Main Navbar**:
  - Logo Box: Yellow box with black border containing bold uppercase text `NGEKOST!`.
  - Navigation Links: Bold font with hover underline / translate offset.
  - Action Button: Chunky button in Pink/Yellow with hard shadow `shadow-[3px_3px_0px_#000]`.

### 2. Search & Filter Bar (Neo-Brutalist Quick Filter)
- Thick white container with header `SUPER FAST SEARCH FILTER`.
- 3 Input Columns framed in 3px solid borders:
  1. Location / target campus input with pin icon.
  2. Resident type pill toggle button (`All`, `Female`, `Male`).
  3. Budget selector dropdown.
  4. Bright Cyan submit button with search icon `SEARCH ROOMS`.
- Quick Filter Tags: White pills `border-2 border-black` with checkmarks (`✓ AC Included`, `✓ Private Bathroom`, `✓ 100Mbps WiFi`, etc.).

### 3. Bento Grid Section (Asymmetrical 3-Column Layout)
- **Master Card (2x2)**:
  - Aesthetic room hero image with a `Top Recommendation` tag.
  - Price Tag Stamp: Floating Electric Yellow box (`IDR 2,450,000 / MO`).
  - Amenities Grid: 6 micro chips (`1 HP AC`, `Private Bathroom + Heater`, `100 Mbps WiFi`, `Cleaning Service`, etc.).
  - Action buttons: Secondary button `360° Survey` and primary yellow button `Book Now`.
- **Vertical Campus Card (1x2)**:
  - Neon Mint background (`#38EF7D`), list of favorite campuses (UI, ITB, UGM, UMN) with available room counters framed in black.
- **Metric / Live Tracker Card (1x1)**:
  - Electric Yellow background, flashing live status indicator (`12 Exclusive Rooms Left!`).
- **Facility Spotlight Card (2x1)**:
  - Lilac / Pastel Pink background, grid of 4 essential student room amenities with retro icons.
- **Promo Ticket Card (1x1)**:
  - Coral background (`#FF6B4A`), torn-ticket design featuring voucher code `NGEKOST20` and a *Copy Code* button.

### 4. Interactive Map Widget
- Interactive map box with room distribution pins shaped like price pills (`IDR 1.8M`, `IDR 2.45M`, `IDR 3.0M`).
- Area switch selector tabs (`Tebet`, `Kuningan`, `Grogol`, `Depok`, `Dago Bandung`).
- Mini listing card on the right side of the map for quick room details.

### 5. Value Proposition & Footer
- **3 Bento Feature Cards**: Guarantee cards for `100% Verified & Real Survey`, `Zero Admin Fees`, and `Fun Roommate Community`.
- **Newsletter Box**: Wide yellow box featuring a student email form with a thick border.
- **Footer**: Light gray/cream background with a 4px thick top border, navigation link grid, favorite student cities, and social media badges.

---

## 6. Implementation Guidelines
- Use Tailwind CSS with custom utility classes for hard shadows (e.g., `shadow-[4px_4px_0px_#000]`).
- Maintain solid black contrast (`#000000` / `#121212`) for all borders (strokes) and typography.
- All interactive elements (buttons, cards, chips) must feature a tactile response (translate offset & shadow reduction upon clicking/pressing).