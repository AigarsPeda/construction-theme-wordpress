# Handoff — Construction WordPress theme

Read this first in a new conversation. Then skim `README.md` and `ADMIN_GUIDE.md`. Do **not** treat `DEVELOPMENT_PLAN.md` as current — it is from July 2026 and many checkboxes are stale.

Prior chat (long, optional): [Construction theme build](c7a42452-2868-4d4b-a671-0aa2b4cb04f4)

---

## Goal

Custom Gutenberg **block theme** for a construction company. Local now (`construction.local` via Local by Flywheel), production later. **Latvian default**, English and Russian via **Polylang**. No Elementor / page builders.

Editors must be able to change **all page copy and images in WordPress**. Theme PHP string tables are **seeds only**.

---

## How it works (architecture)zz

### Paths

| What | Where |
|---|---|
| Git / workspace | `/Users/aigarspeda/Desktop/construction` |
| Theme source | `theme/construction/` |
| Live Local WP | `/Users/aigarspeda/Local Sites/construction/app/public` |
| Theme on Local | symlink: `wp-content/themes/construction` → Desktop theme folder |
| Site URL | `http://construction.local` |

Theme version: **0.9.14** (`CONSTRUCTION_VERSION` in `functions.php` and `style.css`). Bump both when CSS/JS changes so browsers pick up assets.

### Content model (critical)

**Pages and menus use Polylang** (three pages each: home, projects, contacts).

**Projects CPT does not.** `construction_project` is excluded via `pll_get_post_types`. One post = one project. LV/EN/RU titles + descriptions live in meta `_construction_project_i18n`. Cover + gallery are shared. Sidebar **Language** panel switches the Gutenberg title + body (`assets/js/projects-editor.js`).

Front listings come from `construction_query_projects()` — published posts, **newest first** (`date` DESC, then `ID` DESC). Used by:

- Homepage marquee: `construction/home-projects` block
- Projects page grid: `construction/projects-grid` block

Disable = draft (hidden). Delete = trash. Cards without images use a placeholder.

**Never assume editing `inc/i18n.php` changes the live site.** Those strings are copied into page `post_content` only on first seed or `?construction_rebuild_*=1&force=1` (destructive). Edit live copy in **Pages → Edit** (per language).

### Key files

| File | Role |
|---|---|
| `theme/construction/functions.php` | Boot, assets, rebuild URL handlers |
| `inc/i18n.php` | Seed strings + `construction_current_lang()` |
| `inc/homepage-content.php` | Homepage Gutenberg seed markup |
| `inc/projects-cpt.php` | CPT, i18n meta, query, cards, migration |
| `inc/projects-content.php` | Projects **pages** seed |
| `inc/contacts-content.php` | Contacts pages seed |
| `inc/blocks.php` | Dynamic project blocks |
| `inc/settings.php` | Appearance → Construction (logo, phone, email, addresses) |
| `assets/js/main.js` | Front: marquee, project modal, GSAP image slide |
| `assets/js/projects-editor.js` | CPT editor language + visibility |
| `assets/js/blocks-editor.js` | Page-block add/edit/disable/remove projects |
| `assets/css/main.css` | All front CSS |
| `ADMIN_GUIDE.md` | Admin how-tos and rebuild URLs |

### Local DB (when you need it)

Local MySQL socket (site ID can change): under `~/Library/Application Support/Local/run/*/mysql/mysqld.sock`. WP CLI often fails unless PHP is Local’s binary **and** DB_HOST uses that socket. Prefer editing content in WP Admin when possible.

Do **not** force-rebuild homes/projects/contacts unless the user asks — it overwrites editor work.

---

## Current progress

Shipped and working on Local:

- Custom block theme, responsive, Mockup-1-inspired layout
- Polylang LV / EN / RU homes, projects pages, contacts pages, per-language menus
- Appearance → Construction for chrome (logo, phone, email, addresses)
- Rank Math on pages (plugin wins over theme fallback SEO)
- Projects CPT: single post, language switcher, gallery, share `#slug` URLs
- Project modal with directional GSAP image carousel
- Block-editor project management (MediaUpload, not `wp.media` inside the modal)
- New projects publish immediately; show even without images
- Homepage services intro copy updated in **DB** (LV/EN/RU) — theme seed also updated
- Project list order: **latest published first**

Latest user-facing copy (services intro):

- LV: `No idejas līdz nodošanai — trīs skaidri soļi. Katrā solī zināsiet, kas notiek un ko iegūstat.`
- EN/RU aligned (“steps / шага”). Latvian must use finite `zināsiet`, **not** infinitive `zināt`.

---

## What worked

- Gutenberg patterns + CSS in the theme; pages stay editable
- One CPT row + meta i18n (Polylang on projects caused triple posts and pain)
- `MediaUpload` in the block-editor modal (raw `wp.media` broke)
- Seed vs DB: change seed, then **edit the page** (or force rebuild only if the user wants a full reset)
- Bump theme version after CSS/JS so Local cache is not a mystery
- Verify UI in the browser after layout/behavior changes

---

## What did not work (do not repeat)

- Changing `i18n.php` and expecting the homepage to update
- Putting projects through Polylang (one project became three posts)
- `wp.media` inside Gutenberg modal for cover/gallery
- Overwriting LV description on quick-edit save when `post_content` is empty
- Logo linking to default-language home (must keep current language)
- Bare Latvian `Katrā zināt` — grammatically broken
- Ordering projects by `menu_order` / title (user wants newest first)

---

## Next steps (when the user asks)

Nothing is currently in-flight. Likely follow-ups:

1. More copy/design polish (real company name, photos, Unsplash credits already exist)
2. Contact form vs current mailto
3. Production host + domain (fill `DEPLOY.md` table; full-site migrate, not theme-only)
4. Keep `DEVELOPMENT_PLAN.md` in sync if you revive it
5. Optional: Sample Page cleanup / menu polish

When changing **page text**, edit WordPress (or update seed **and** DB if they want it live). When changing **layout/behavior**, change theme files, bump version, hard-refresh, verify in the browser.

---

## Skills in use

- Frontend UI: `/Users/aigarspeda/.agents/skills/impeccable/SKILL.md` and/or `/Users/aigarspeda/.agents/skills/design-taste-frontend/SKILL.md` when designing or polishing UI
- Marketing copy: `/Users/aigarspeda/.claude/skills/copywriting/SKILL.md`
- Handoff updates: `/Users/aigarspeda/.codex/skills/handoff/SKILL.md` (this file)
- User rules: commit only when asked; verify web UI in the browser; no force-push

---

## Agent rules of thumb

1. All visitor-facing page content is user-editable in the block editor.
2. Chrome (header phone, logo) = Appearance → Construction.
3. Do not create Polylang translations of `construction_project`.
4. Latvian copy: native grammar, not calques.
5. Rebuild `force=1` URLs destroy editor edits — ask first.
6. After CSS/JS: bump `0.9.14` → next patch in **both** `style.css` and `functions.php`.
