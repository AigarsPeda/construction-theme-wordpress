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

To **reset** from theme seed (destroys edits): `?construction_rebuild_homes=1&force=1` — see “Where content lives” above.

---

## Projects gallery

Gallery pages: **projekti** (LV) / **projects** (EN) / **proekty** (RU).

Images come from the **Media Library**. Click a photo on the page to open the lightbox.

Edit gallery page text in **Pages**. Forced reseed: `?construction_rebuild_projects=1&force=1`.

---

## Menus (Projekti, etc.)

**Appearance → Menus** (or Site Editor navigation).

Assign a **Primary** menu for each Polylang language so EN/RU don’t show LV labels.

---

## Mobile menu

On small screens: hamburger → drawer with nav, languages, and contact.  
No extra setting — same logo / phone / email as desktop.

---

## Where content lives

**All page content lives in the WordPress database.** Edit it under **Pages → Edit** (each Polylang language separately).

| What | Where |
|---|---|
| Hero, services, FAQ, reviews, projects gallery text | **Pages → Edit** (block editor) |
| Browser tab title, meta, focus keyword | **Rank Math** on that page |
| Logo, phone, email (header / mobile menu) | **Appearance → Construction** |
| Site name | **Settings → General** |
| Header “Menu / Close” labels | Theme chrome strings (rarely changed) |

`construction_strings()` in the theme is a **seed only** (first install / forced reset). Changing PHP does **not** update live pages.

### Rebuild URLs (use carefully)

| URL | Effect |
|---|---|
| `?construction_rebuild_homes=1` | Create missing home pages only — **keeps** existing DB content |
| `?construction_rebuild_homes=1&force=1` | **Deletes and reseeds** homes from theme (destructive) |
| `?construction_rebuild_projects=1` | Create missing projects pages only |
| `?construction_rebuild_projects=1&force=1` | **Deletes and reseeds** projects pages |

Saving **Appearance → Construction** no longer rebuilds pages (so your edits stay safe).

---

## SEO / browser tab title (Rank Math)

Rank Math scores **do not** read the theme PHP file. They only look at:

1. The **Focus Keyword** field in Rank Math (must be filled in), and  
2. The page content already in WordPress, plus SEO title / meta description in Rank Math.

If the checklist says “Set a Focus Keyword…” nothing else will turn green until you set it.

### Steps

1. Open the page (e.g. **Sākums** / **Home**).
2. In the editor sidebar, open **Rank Math SEO** (or the Rank Math panel below the content).
3. Set **Focus Keyword**, e.g.:
   - LV: `būvniecības vadība un uzraudzība`
   - EN: `construction management and supervision`
   - RU: `управление и надзор в строительстве`
4. Set **SEO Title** so it **starts with** that phrase (or includes it near the start).
5. Set **SEO Meta Description** and include the phrase once.
6. Click **Update**, then reopen Rank Math Score — keyword checks should improve.

“Use Content AI” can stay red (upsell). Internal links / keyword in every H2 are optional.

Without Rank Math, the theme still has fallback SEO strings; with Rank Math active, the plugin wins.

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

Image markup improvements apply to **new** content or a **forced** page reseed (`&force=1`). Prefer editing images in the block editor so the database stays the source of truth.
