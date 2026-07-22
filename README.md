# Construction — WordPress theme

Custom **Gutenberg / block theme** for a construction company site.

- Works like a normal WordPress theme (Appearance → Themes → Activate)
- Uses the built-in block editor (Gutenberg) — **no Elementor**
- Polylang-ready: Latvian (default), English, Russian
- Source of truth for GitHub: this repo folder

## Repo layout

```
construction/
├── README.md
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

1. Push this repo to GitHub.
2. On the server, place `theme/construction` into `wp-content/themes/construction`  
   (via Git deploy, SFTP, or zip upload).
3. In WP Admin: **Appearance → Themes → Activate “Construction”**.
4. Install/activate **Polylang**, set languages: **LV (default)**, EN, RU.
5. Migrate content/DB from Local (All-in-One WP Migration, Duplicator, or similar), **or** rebuild pages on prod.

### Zip upload (no Git on server)

```bash
cd theme
zip -r construction.zip construction
```

Then: **Appearance → Themes → Add New → Upload Theme → construction.zip**.

## After activate

1. **Settings → Reading** — set a static front page (or keep `front-page.html` template; it renders homepage patterns automatically).
2. **Appearance → Editor** (Site Editor) — edit header/footer if needed.
3. **Appearance → Menus** / Navigation block — Projekti, Foto, Par mums.
4. **Languages (Polylang)** — LV / EN / RU + language switcher.

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
