# WordPress Studio — admin URLs (no core changes)

Studio’s local proxy can return **Not Found** for `wp-admin/post.php` and bare `wp-admin/`. Do not edit core files; use these URLs instead:

| Task | URL |
|------|-----|
| Dashboard | `/wp-admin/index.php` |
| ACF field groups | `/wp-admin/edit.php?post_type=acf-field-group` |
| All pages | `/wp-admin/edit.php?post_type=page` |

If **Edit** on a page or field group still shows Not Found, restart the site in Studio (`studio site stop` then `studio site start`) or update WordPress Studio — that is a proxy issue, not missing ACF fields.

Homepage content is edited on the **Home** page under the **VIP Homepage** ACF box (field group location: front page).
