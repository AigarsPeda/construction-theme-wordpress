# Statistics and privacy

Implemented on Local only on 14 October 2026. Site Kit is active but has no connected Analytics property. No Google account connection or droplet deployment was performed.

The banner uses the site's existing mist background, black buttons, rounded controls and typography. Latvian, English and Russian copy is editable under **Settings → Privacy & Analytics**. The consent banner and footer settings button appear only when Analytics has a valid measurement ID and collection is enabled for that visitor. They remain hidden on Local and while setup or collection is disabled. The footer privacy link stays visible. Acceptance and rejection are remembered for 180 days. Withdrawal deletes accessible Analytics cookies, disables collection and reloads a page that had loaded the tag. Other open tabs receive the changed choice.

Analytics uses basic consent mode. The tag loads only after acceptance; advertising consent remains denied. Local, non-production environments and signed-in visitors are excluded. Collection is restricted to the public `oaksbuild.lv` domain and remains disabled until explicitly enabled in Privacy & Analytics. The connected Site Kit GA4 measurement ID is used automatically; an optional manual ID is available as a fallback. The theme loads one tag, sends page views and phone/email contact-click events, and removes query parameters and fragments from page and referrer URLs.

`mu-plugins/construction-site-kit-consent.php` prevents Site Kit's automatic Analytics and Tag Manager snippets from loading, while preserving its account setup and reporting. It is installed in Local's `wp-content/mu-plugins/`. Theme-only deployment does not transfer it, the privacy pages, or their settings. A later requested live release must include these explicitly and map the live policy page IDs. Complete Site Kit account setup on the public website. Keep Enhanced Measurement and plugin conversion tracking off so event collection matches the consent-controlled implementation and policy.

## Policy content

Local policy pages are LV **554** `/privatums/`, EN **555** `/en/privacy/`, and RU **556** `/ru/konfidencialnost/`. They are linked through Polylang, editable as native WordPress blocks, and marked `noindex` while placeholders remain. The requested local-draft introduction was removed from all three pages. Company identity, Analytics property retention and applicable international-transfer safeguards still need real values before public release. Contact/server-log retention uses purpose-based criteria rather than invented periods.

The policies retain controller/contact details, data categories, purposes and legal bases, voluntary provision of enquiry information, cookie purposes and lifetimes, data recipients, transfers where applicable, retention criteria, withdrawal, data rights and the complaint route. Implementation notes about Site Kit, Local, tag configuration and setup steps stay in this document. Google Fonts and jsDelivr are disclosed because the current site requests these resources independently of Analytics consent.

Sources reviewed: [GDPR Article 13](https://eur-lex.europa.eu/legal-content/ENG/ALL/?uri=celex%3A32016R0679), [DVI guidance on withdrawing cookie consent](https://www.dvi.gov.lv/lv/jaunums/dviskaidro-ka-nodrosinat-lietotajiem-iespeju-viegli-atsaukt-piekrisanu-sikdatnem), and [Site Kit consent documentation](https://sitekit.withgoogle.com/documentation/using-site-kit/consent-mode/).

`content/privacy-policies.json` contains the initial policy copy for new Local installations. Run `wp eval-file scripts/setup-local-privacy.php` in Local's site shell. Reruns preserve existing authored policy content and settings, including intentionally cleared page selections. The source JSON is not an automatic replacement for edited database content.

## Verification

- `node scripts/tests/privacy.cjs` checks tag gating, saved and invalid preferences, duplicate prevention, advertising denial, URL redaction, withdrawal, cross-tab withdrawal and disabled Local configuration without contacting Google.
- `wp eval-file scripts/tests/privacy.php` checks the installed Site Kit guard and Local environment exclusions with temporary in-memory fixtures. It does not change settings.
- Local HTTP checks covered all three homepages and policy pages, correct links, removed draft sections, policy `noindex`, and absent Analytics tags.
- Browser checks covered desktop and mobile layouts, acceptance, rejection, reload persistence and choice persistence across LV/EN/RU. No live telemetry was sent. Screenshots and rollback snapshots are in `.backups/privacy-20261014/`.
