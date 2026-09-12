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

## Deployment scripts

The repository includes three scripts for the current Local and DigitalOcean setup:

```bash
# Preview theme-file changes without uploading anything.
./scripts/sync-code-to-droplet.sh --dry-run

# Upload the local theme and make the remote theme directory match it.
./scripts/sync-code-to-droplet.sh

# Replace Local's database with the droplet database.
./scripts/pull-db-from-droplet.sh

# Replace the droplet database with Local's database.
./scripts/push-db-to-droplet.sh
```

The database scripts ask for an explicit confirmation. Pass `--yes` only when a deliberate non-interactive run is required. Both scripts perform serialized-safe URL replacement. Pulls retain the latest three compressed local rollback backups in `Desktop/construction-backups`. Pushes retain the latest three compressed production rollback backups in `/var/backups/construction`.

Database synchronization does not copy media files. Any files referenced from `wp-content/uploads` must already exist on both Local and the droplet.

The scripts use the current SSH key, server address, Local paths, and WordPress paths as defaults. Override settings through the environment when needed. Run any script with `--help` to see the available variables.

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
