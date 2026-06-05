# Occasions (`vip_occasion` CPT)

Occasions (Wedding, Birthday, Corporate, etc.) are **detail-only** posts — there is no public CPT archive listing.

## Admin setup

1. **Settings → VIP Transits → Import ACF JSON** (syncs `group_vip_occasion_page` → **VIP Occasion** fields).
2. **Occasions → Add new** — title, excerpt, featured image, and ACF tabs (Hero, Why rent, Related articles, Vehicle grid, FAQ).
3. **Settings → Permalinks → Save** once after deploy (flushes `/occasions/{slug}/` URLs).
4. **Homepage → Rent by occasion** (flexible content): pick **Featured occasion** (large left card) and **Grid occasions** (up to 4). Order in the grid field = display order.
5. On each occasion, **Homepage card** tab: excerpt (intro), **Homepage button label**. If homepage picks are empty, **Featured on homepage** + menu order auto-fill the section instead.
6. Under **Vehicles**, assign **Occasion roles** (Bridal car, Groom / escort, etc.) for the extra filter on occasion detail pages.

## URLs

| URL | What |
|-----|------|
| `/#vip-occasions` | Homepage **Rent by occasion** section |
| `/occasions/wedding/` | Single occasion detail (slug = post slug) |

There is **no** `/occasions/` listing page — bare `/occasions/` redirects to `/#vip-occasions`.

Homepage **Rent by occasion** uses occasions you pick on the homepage section (or auto from published posts). Image, title, and button link to `/occasions/{slug}/`. Set the CTA text under **Homepage button label** on each occasion.

## Page sections

1. **Hero** — H1 (post title or Hero heading field), intro, image (50/50 in max-width container).
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
