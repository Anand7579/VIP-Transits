# Occasions (`vip_occasion` CPT)

Occasions (Wedding, Birthday, Corporate, etc.) are **detail-only** posts — there is no public CPT archive listing.

## Admin setup

1. **Settings → VIP Transits → Import ACF JSON** (syncs `group_vip_occasion_page` → **VIP Occasion** fields).
2. **Occasions → Add new** — title, excerpt, featured image, and ACF tabs (Hero, Why rent, Related articles, Vehicle grid, FAQ).
3. **Settings → Permalinks → Save** once after deploy (flushes `/occasions/{slug}/` URLs).
4. Under **Vehicles**, assign **Occasion roles** (Bridal car, Groom / escort, etc.) for the extra filter on occasion detail pages.

## URLs

| URL | What |
|-----|------|
| `/#vip-occasions` | Homepage **Rent by occasion** section (breadcrumb “Occasions” link) |
| `/occasions/wedding/` | Single occasion (slug = post slug) |

Point homepage occasion cards at `/occasions/{slug}/`.

## Page sections

1. **Hero** — breadcrumb, H1 (post title or Hero heading field), intro, image.
2. **Why rent** — same two-column layout as vehicle detail (`main_content_wrap` + `vip-vdetail__intro`); related articles in the right column.
3. **Fleet** — same filters and behaviour as homepage / Our fleet, plus **Car role for {occasion}** at the top of the sidebar.
4. **FAQ** — questions on the left, image on the right.

## Legacy WordPress pages

Old pages using template **VIP Occasion detail** still work. Prefer migrating content into **Occasions** posts.

## Files

- `inc/occasions-cpt.php` — CPT registration, helpers
- `templates/single-vip_occasion.html` — single template
- `template-parts/occasion/single-content.php` — detail markup
- `acf-json/group_vip_occasion_page.json` — ACF (post type + legacy page locations)
