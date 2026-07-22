# Construction site — Admin how-to

Short answers for common “how do I…?” questions.  
For install, see [README.md](README.md).  
For **Local → production** (full site: content, media, plugins), see [DEPLOY.md](DEPLOY.md).  
For build roadmap, see [DEVELOPMENT_PLAN.md](DEVELOPMENT_PLAN.md).

All site chrome settings live under **Appearance → Construction** in WP Admin.

---

## Change the logo

1. Go to **Appearance → Construction**.
2. Under **Logo**, click **Select logo**.
3. Upload or pick an image from the Media Library (SVG or PNG recommended).
4. Click **Save Changes**.

**Remove logo** restores the theme placeholder.  
The header shows the mark at about **40×40**; a square or near-square file looks best.

---

## Change phone, email, or address

1. **Appearance → Construction**.
2. Edit **Email**, **Phone (display)**, **Phone (tel link)**, and addresses (LV / EN / RU).
3. **Save Changes**.

These values appear in the header, mobile drawer, and contact section.  
Saving also refreshes the multilingual homepage contact text.

---

## Change the site / company name next to the logo

That text is the WordPress **Site Title**:

**Settings → General → Site Title** → Save.

---

## Languages (LV / EN / RU)

Languages are managed by **Polylang**, not the Construction settings page.

| Task | Where |
|---|---|
| Add / edit languages | **Languages** (Polylang menu) |
| Switch language on the site | Header language links (LV / EN / RU) |
| Translate a page | Edit the page → Polylang language box → “+” for other languages |
| Translate menus | One Primary menu per language (Polylang) |

### Language must stay the same when I navigate

Clicking the **logo** (or home) keeps the **current** language.  
Only the **language switcher** should change language.

If the logo jumps back to Latvian after a theme update, hard-refresh the browser (cached assets).

---

## Edit homepage content

1. Open the front page for that language (e.g. **Sākums** / **Home** / **Главная**).
2. Edit blocks in the block editor and update.

Homes are linked as translations in Polylang. Edit each language separately (or use Polylang’s translation workflow).

### Rebuild homepage content from the theme (dev)

If pages got out of sync with theme patterns:

`https://construction.local/?construction_rebuild_homes=1`  
(while logged in as an admin)

---

## Projects gallery

Gallery pages: **projekti** (LV) / **projects** (EN) / **proekty** (RU).

Images come from the **Media Library**. Click a photo on the page to open the lightbox.

### Rebuild projects pages (dev)

`https://construction.local/?construction_rebuild_projects=1`  
(while logged in as an admin)

---

## Menus (Projekti, etc.)

**Appearance → Menus** (or Site Editor navigation).

Assign a **Primary** menu for each Polylang language so EN/RU don’t show LV labels.

---

## Mobile menu

On small screens: hamburger → drawer with nav, languages, and contact.  
No extra setting — same logo / phone / email as desktop.

---

## SEO basics

Titles, meta description, Open Graph, and hreflang are handled by the theme (`inc/seo.php`).  
For richer SEO later, a plugin (e.g. Yoast / Rank Math) can be added carefully so it doesn’t fight Polylang.

---

## Theme files vs admin

| Change | Prefer |
|---|---|
| Logo, phone, email, address | **Appearance → Construction** |
| Site title | **Settings → General** |
| Page text / images | Block editor on that page |
| Languages | **Polylang** |
| Layout / CSS / new sections | Theme code in `theme/construction/` |

---

## Local development reminder

Theme path in this repo: `theme/construction/`  
Usually symlinked into Local’s `wp-content/themes/construction`.

After pulling CSS/JS changes, hard-refresh the browser (theme version is bumped in `functions.php` / `style.css`).

---

## Performance tips (Lighthouse)

See the table in [DEPLOY.md](DEPLOY.md) (“Performance / Lighthouse”).

After image-related theme updates, rebuild homepage content so the DB picks up new `<img>` markup:

`https://construction.local/?construction_rebuild_homes=1` (logged-in admin)
