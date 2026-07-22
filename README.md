# Construction — WordPress theme

Custom **Gutenberg / block theme** for a construction company site.

- Works like a normal WordPress theme (Appearance → Themes → Activate)
- Uses the built-in block editor (Gutenberg) — **no Elementor**
- Polylang-ready: Latvian (default), English, Russian
- Source of truth for GitHub: this repo folder

**Day-to-day admin answers** (logo, contact, languages, rebuilds): see **[ADMIN_GUIDE.md](ADMIN_GUIDE.md)**.  
**Move Local → live server** (full site: content, media, plugins, settings): see **[DEPLOY.md](DEPLOY.md)**.

## Repo layout

```
construction/
├── README.md
├── ADMIN_GUIDE.md             ← how-to for WP Admin questions
├── DEPLOY.md                  ← Local → production (full site migrate)
├── DEVELOPMENT_PLAN.md
└── theme/
    └── construction/          ← install this folder as the WP theme
        ├── style.css
        ├── screenshot.png     ← Themes admin preview
        ├── theme.json
        ├── functions.php
        ├── templates/
        ├── parts/
        ├── patterns/
        └── assets/
```

## Install in Local (development)

### Option A — symlink (recommended while developing)

```bash
ln -s "/Users/aigarspeda/Desktop/construction/theme/construction" \
  "/Users/aigarspeda/Local Sites/construction/app/public/wp-content/themes/construction"
```

Then in WP Admin: **Appearance → Themes → Activate “Construction”**.

### Option B — copy

Copy `theme/construction` into:

`Local Sites/construction/app/public/wp-content/themes/construction`

## Install on production

For a **full copy** of the current Local site (pages, images, Polylang, plugin settings, Construction options), follow **[DEPLOY.md](DEPLOY.md)**.

Theme-only install (empty WP, then rebuild content by hand):

1. Push this repo to GitHub.
2. On the server, place `theme/construction` into `wp-content/themes/construction`  
   (via Git deploy, SFTP, or zip upload).
3. In WP Admin: **Appearance → Themes → Activate “Construction”**.
4. Install/activate **Polylang**, set languages: **LV (default)**, EN, RU.
5. Prefer migrating from Local ([DEPLOY.md](DEPLOY.md)) instead of rebuilding everything manually.

### Zip upload (no Git on server)

```bash
cd theme
zip -r construction.zip construction
```

Then: **Appearance → Themes → Add New → Upload Theme → construction.zip**.

## After activate

1. **Appearance → Construction** — upload logo; set phone, email, addresses.
2. **Settings → Reading** — set a static front page (or keep `front-page.html` template; it renders homepage patterns automatically).
3. **Appearance → Editor** (Site Editor) — edit header/footer if needed.
4. **Appearance → Menus** / Navigation block — Projekti, Foto, Par mums.
5. **Languages (Polylang)** — LV / EN / RU + language switcher.

More how-tos: **[ADMIN_GUIDE.md](ADMIN_GUIDE.md)**.

## What “Gutenberg” means here

You do **not** install a theme named Gutenberg.  
Gutenberg is WordPress’s block editor. This theme is built *for* it (templates + patterns).

## Placeholders

| | |
|---|---|
| Brand | Construction |
| Phone | +371 2000 0000 |
| Email | info@construction.lv |
| Telegram | @construction |

Override phone/email/address/logo in **Appearance → Construction** (see [ADMIN_GUIDE.md](ADMIN_GUIDE.md)).
