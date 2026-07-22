# Construction Company Website — Development Plan

Last updated: 2026-07-22

## Goals

- Local WordPress site (`construction.local` via Local app)
- Custom theme we own (no Elementor / marketplace theme lock-in)
- Multilingual: **Latvian (default)**, English, Russian via **Polylang** (free)
- Design direction: **Mockup 1** (Дом Строй style — split hero, services, quality grid, dark reviews, contact footer)
- **Responsive** on phone, tablet, and desktop
- Code workspace: `/Users/aigarspeda/Desktop/construction`
- Later: migrate Local → production server

## Placeholders (replace later)

| Item | Placeholder |
|---|---|
| Company name | **Construction** |
| Phone | `+371 2000 0000` |
| Email | `info@construction.lv` |
| Telegram | `@construction` |
| Domain (prod) | TBD |

## Architecture decision

### Recommended approach: **Custom block theme + Gutenberg patterns**

Not Elementor. Not a heavy page-builder stack.

**Stack:**

1. Custom WordPress **block theme** (`themes/construction/`)
2. **Custom block patterns** for each homepage section
3. **Polylang** for LV / EN / RU (already installed in Local)
4. Native contact form (or one lightweight form plugin later if needed)
5. Optional: custom post type `projects` when portfolio pages are added

### Why Gutenberg + custom patterns (advantages)

| Advantage | What it means for us |
|---|---|
| **You still own the theme** | Patterns are PHP/HTML/CSS in *our* theme. No Elementor data in the DB as the source of truth for layout. |
| **No page-builder dependency** | Site keeps working if plugins change. Migration is simpler (theme + content + Polylang). |
| **Editable in WP Admin** | Client/editor can change text, images, and section order in the block editor without touching code. |
| **Reusable sections** | “Hero”, “Services”, “Reviews” become insertable patterns — same look everywhere, one place to refine markup. |
| **Native WordPress** | Patterns, template parts, `theme.json` (fonts, colors, spacing) are core WP — fewer foreign APIs to learn later. |
| **Cleaner performance** | No Elementor CSS/JS overhead; we ship only what the design needs. |
| **Polylang-friendly** | Translate pages/posts normally; theme strings via `pll__()` / string translations. |
| **Server transfer friendly** | Theme is files in git/folder; content is standard WP blocks — standard migration tools work well. |

### Tradeoffs vs pure PHP templates

| | Gutenberg patterns | Hard-coded PHP templates only |
|---|---|---|
| Design fidelity | High (with custom CSS) | Highest control |
| Content edits without deploy | Yes | Mostly no (or ACF/options) |
| Build speed for landing page | Faster once patterns exist | More PHP wiring |
| Risk of editor breaking layout | Possible if editors misuse blocks | Low |
| “No foreign deps” purity | Theme-owned + core WP | Theme-owned only |

**Practical middle ground (what we’ll use):**  
Theme templates + locked-down patterns (or a front-page template composed of patterns) so the homepage looks like Mockup 1, but content remains editable. Critical layout CSS lives in the theme, not in random block markup.

### What we are *not* using

- Elementor / Bricks / Divi
- Marketplace “construction” themes
- Page-builder kits that store layout only in the database

Polylang is an intentional, replaceable plugin dependency for languages.

---

## Design scope (Mockup 1 → v1)

Homepage sections:

1. **Header** — logo, nav (Projekti / Foto / Par mums), phone, language switcher
2. **Hero** — split layout (copy left, house photo right), headline, short text, CTA (“Iziet testu” / placeholder), “building since …” line
3. **Services** — heading + intro + 3 service rows/cards (design, façade design, installation)
4. **Quality** — “Fast deadlines & high quality” + 4-item grid
5. **Reviews** — dark section, rating summary, review cards
6. **Contact / footer** — email, Telegram, lead input, secondary nav + CTA

Later pages (not required for first visual pass):

- Projects / portfolio
- About
- Photos
- Contact page
- Quiz/test flow (CTA can link to `#contact` until built)

---

## Languages (Polylang)

| Code | Language | Role |
|---|---|---|
| `lv` | Latviešu | **Default** |
| `en` | English | Secondary |
| `ru` | Русский | Secondary |

Setup notes:

- URL mode: language in path preferred (`/`, `/en/`, `/ru/`) or subdirectory — decide at Polylang wizard
- Translate: Home page × 3, menus × 3, theme strings (buttons, labels)
- Language switcher in header (and optionally footer)

Copy for v1: Latvian first (real-ish placeholder marketing copy), then EN/RU equivalents.

---

## Repository / folder layout (GitHub-ready)

Workspace / git root: `/Users/aigarspeda/Desktop/construction`

```
construction/
├── DEVELOPMENT_PLAN.md
├── README.md                    ← Local + prod install instructions
├── .gitignore
└── theme/
    └── construction/                 ← the WordPress theme (commit this)
        ├── style.css            ← theme header (required)
        ├── theme.json           ← block theme settings
        ├── functions.php
        ├── templates/           ← FSE templates (front-page, page, …)
        ├── parts/               ← header / footer
        ├── patterns/            ← Gutenberg patterns (hero, services, …)
        └── assets/css|js|images
```

**How it works with Gutenberg:** this is a normal block theme. WordPress already includes the block editor; we do not install a “Gutenberg” theme from wordpress.org.

**Local (dev):** symlink Desktop theme → Local themes folder (already set up for daily work).

**GitHub:** commit `/Users/aigarspeda/Desktop/construction` (theme lives under `theme/construction`).

**Production:** copy/deploy `theme/construction` → `wp-content/themes/construction`, then Activate in WP Admin (or upload `construction.zip`).

---

## Implementation phases

### Phase 0 — Foundations
- [x] Placeholder brand **Construction**
- [x] Theme scaffold in Desktop + symlink into Local
- [x] Homepage as real Polylang pages: **Sākums (LV)**, **Home (EN)**, **Главная (RU)** with language-specific content
- [x] Language switcher links between translated front pages
- [ ] Create LV/EN/RU menus (optional cleanup: remove Sample Page)

### Phase 1 — Theme shell
- [x] `theme.json` (colors, fonts, spacing — Mockup 1 palette)
- [x] Header / footer template parts
- [x] Front-page template
- [x] Base CSS (layout, typography, responsive)
- [ ] Real photos (currently CSS placeholders)

### Phase 2 — Homepage patterns (Mockup 1)
- [x] Hero pattern
- [x] Services pattern
- [x] Quality grid pattern
- [x] Reviews pattern
- [x] Contact strip pattern
- [x] Assemble on front-page template
- [ ] Polish copy + real images

### Phase 3 — Multilingual content
- [ ] Duplicate/translate front page EN + RU in Polylang
- [ ] Translate menus and theme strings
- [ ] Verify language switcher + URLs

### Phase 4 — Forms & polish
- [ ] Contact / lead capture (mailto or simple form handler)
- [ ] Quiz CTA → placeholder destination
- [ ] Mobile pass, accessibility basics, performance check

### Phase 5 — Server transfer
- [ ] Choose host
- [ ] Migrate (All-in-One WP Migration / Duplicator / manual)
- [ ] SSL, search-replace URLs, re-check Polylang

---

## Success criteria (v1)

- Homepage on `construction.local` matches Mockup 1 structure and feel
- Theme code lives under Desktop `construction/theme/construction`
- No Elementor (or other page builder) required
- Site opens in **LV** by default; EN and RU switch correctly via Polylang
- Header shows placeholder phone; footer shows placeholder email + Telegram
- Can migrate later with standard WP tools without builder lock-in

---

## Open decisions (non-blocking)

1. Final company name (placeholder: Construction)
2. Real photos vs temporary Unsplash/local placeholders
3. Quiz feature: skip for v1 vs simple multi-step form later
4. Production host (cPanel, VPS, WP Engine, etc.)

---

## Next action

After this plan is accepted: scaffold the `construction` block theme, symlink into Local, and build the Mockup 1 homepage shell in Latvian.
