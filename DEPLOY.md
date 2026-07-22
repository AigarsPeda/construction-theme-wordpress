# Deploy: Local → production

Goal: move the **entire current site** from Local (`construction.local`) to a live server — theme, plugins, plugin settings, pages, menus, Media Library images, Polylang languages/translations, and **Appearance → Construction** options.

This is a **full site clone**, not “install theme only and rebuild by hand.”

---

## What gets migrated (and what does not)

| Included in a full migrate | Notes |
|---|---|
| Database (posts, pages, options, menus, Polylang data) | Core of content + settings |
| `wp-content/uploads` (all Media Library images) | Gallery, logos, etc. |
| `wp-content/plugins` + their options | e.g. Polylang |
| `wp-content/themes` | Includes Construction theme files |
| Users | Same admin login as Local after import (change password on prod) |

| Not automatic / check after | Notes |
|---|---|
| Domain / SSL | Host + DNS setup |
| PHP version / upload limits | Host must allow the import |
| Search engines “discourage” flag | Turn **off** on prod if it was on in Local |
| Licensed plugins | Re-enter keys if any (none required for current stack) |
| Theme **symlink** from Desktop | Prod must have real theme files, not a Mac symlink |

**Theme git repo** (`Desktop/construction`) remains the source of truth for *future* code changes. The migrate copies whatever is active in Local’s WordPress at export time.

---

## Recommended approach

**All-in-One WP Migration** (or **Duplicator**) from Local → fresh WordPress on the host.

Why: one export includes DB + uploads + plugins + themes + options. URL find/replace is handled for you. Polylang tables and language settings come along.

Alternative if the host blocks that plugin or the site is huge: **Duplicator Pro**, host migration tool, or manual (DB dump + files + Better Search Replace). Same checklist below still applies after restore.

---

## Before you start (fill these in)

| Item | Value |
|---|---|
| Local URL | `http://construction.local` (or `https://…`) |
| Production URL | `https://________________` |
| Host | ________________ (cPanel / VPS / managed WP) |
| PHP on prod | Prefer **8.1+** (match Local if possible) |
| Who has DNS access | ________________ |
| Go-live date | ________________ |

### Preflight on Local

- [ ] Site looks correct in **LV / EN / RU** (home, projects, contact, language switcher, logo → home stays in language).
- [ ] **Appearance → Construction**: final logo, phone, email, addresses.
- [ ] Media Library has all project images (not only files on Desktop).
- [ ] Polylang: LV default, EN, RU; front pages + projects pages translated and linked.
- [ ] Menus assigned per language (Primary).
- [ ] Note Local WordPress admin username (you will use it on prod after import).
- [ ] Optional: deactivate unused plugins so the export is smaller/cleaner.
- [ ] Optional: in Local, leave **Settings → Reading → Discourage search engines** as you prefer; you will set this correctly on prod after import.

### Prepare production host

- [ ] Domain points to the host (or use temporary host URL first, then switch DNS).
- [ ] SSL certificate active (`https://`).
- [ ] Empty / fresh WordPress installed (default theme is fine; import will overwrite).
- [ ] PHP upload / post / memory limits high enough for the `.wpress` file  
  (free All-in-One import is often capped ~**512 MB**; ask host to raise `upload_max_filesize` / `post_max_size`, or use a paid extension / Duplicator / SFTP upload method).
- [ ] You can log into prod WP Admin.

---

## Step-by-step (All-in-One WP Migration)

### 1. Export from Local

1. In Local WP Admin: install/activate **All-in-One WP Migration**.
2. **All-in-One WP Migration → Export → File**.
3. Add find/replace (if the UI offers it):
   - Find: `http://construction.local` (and `https://construction.local` if used)
   - Replace: `https://your-production-domain.tld`
4. Download the `.wpress` file to your computer. Keep it safe until import succeeds.

### 2. Import on production

1. On fresh prod WP: install/activate **All-in-One WP Migration**.
2. **Import → File** → upload the `.wpress` file.
3. Confirm overwrite when prompted. Wait until it finishes.
4. Log in again if asked (use the **Local** admin user/password that came with the import).

### 3. Immediate post-import fixes

1. **Settings → Permalinks** → Save (no changes needed) — flushes rewrite rules.
2. **Settings → General** — confirm WordPress Address / Site Address are the prod `https://` URLs.
3. **Settings → Reading** — uncheck **Discourage search engines** when you are ready to be public.
4. Confirm **Appearance → Themes**: **Construction** is active.
5. Confirm **Plugins**: **Polylang** active (and any others you need).
6. **Appearance → Construction** — spot-check logo / phone / email (should match Local).

If the theme is missing or broken: upload `theme/construction` from this repo (zip or Git/SFTP) into `wp-content/themes/construction` and activate it. Do **not** rely on a Local Desktop symlink on the server.

### 4. Polylang / language check

1. **Languages** — LV, EN, RU present; LV is default.
2. URL mode (directories vs subdomain) matches what you want for prod.
3. Front pages open correctly per language.
4. Projects pages open with images.
5. Language switcher switches content (not only the URL).
6. Logo / home link **keeps** the current language.

### 5. Content & media check

- [ ] Homepage sections (hero, services, process, quality, reviews, FAQ, contact)
- [ ] Projects gallery + lightbox
- [ ] Header phone + mobile drawer
- [ ] Footer
- [ ] Sample/test pages removed or unpublished if not wanted
- [ ] Media Library thumbnails load (if broken, run a “Regenerate Thumbnails” plugin once)

### 6. Security & cleanup

- [ ] Change the migrated admin password (and email) on prod.
- [ ] Remove or restrict any Local-only rebuild URL habits on prod  
  (`?construction_rebuild_homes=1`, etc. — only use when intentionally rebuilding).
- [ ] Delete the `.wpress` file from the server if the plugin left a copy.
- [ ] Optional: delete All-in-One WP Migration after a successful go-live (or keep for backups).
- [ ] Confirm backups are enabled on the host.

### 7. DNS cutover (if you imported on a temp URL first)

1. Point domain A/AAAA (or nameservers) to the host.
2. Wait for DNS; confirm SSL.
3. If URLs still wrong, run a careful search-replace (`construction.local` / temp host → final domain) with a tool that handles serialized data (All-in-One again, or Better Search Replace).

---

## If import fails (size / timeout)

1. Ask the host to raise PHP `upload_max_filesize`, `post_max_size`, `max_execution_time`, `memory_limit`.
2. Or use **Duplicator** / host “migrate” tool / SFTP the archive.
3. Or: export DB + `wp-content` manually:
   - Copy `wp-content/uploads`, `plugins`, `themes`
   - Import SQL
   - Run serialized-safe URL replace
   - Soften: this is more error-prone; prefer plugin migrate when possible.

---

## After go-live: how you update the site

| Change type | How |
|---|---|
| Text, phone, logo, pages, images | Edit on **production** WP Admin (see [ADMIN_GUIDE.md](ADMIN_GUIDE.md)) |
| Theme code (CSS/PHP/patterns) | Edit in this git repo → deploy `theme/construction` to prod (SFTP/Git) → hard-refresh |
| Full Local → prod again | Only if you intentionally rebuild on Local and want to **overwrite** prod (destructive) |

Avoid developing on Local and re-importing over a live site that already has new content, unless you know you will wipe prod.

---

## Smoke-test checklist (print / tick on go-live day)

- [ ] `https://prod` loads over SSL
- [ ] LV home OK
- [ ] EN home OK
- [ ] RU home OK
- [ ] Language switcher OK
- [ ] Logo keeps language
- [ ] Projects gallery + images + lightbox
- [ ] Phone `tel:` and email `mailto:` work
- [ ] Mobile menu / drawer OK
- [ ] Contact details match **Appearance → Construction**
- [ ] Search engines allowed (when public)
- [ ] Admin login works; password changed

---

## Related docs

- [README.md](README.md) — theme install / zip
- [ADMIN_GUIDE.md](ADMIN_GUIDE.md) — day-to-day edits after launch
- [DEVELOPMENT_PLAN.md](DEVELOPMENT_PLAN.md) — Phase 5 (host choice still TBD)

---

## Status

| Step | Status |
|---|---|
| Host / domain chosen | TBD |
| SSL | TBD |
| Full migrate rehearsed (staging) | Recommended before public launch |
| Public go-live | TBD |
