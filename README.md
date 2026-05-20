# VIP Transits

WordPress site for **VIP Transits** (luxury car rental, Dubai), built with [WordPress Studio](https://developer.wordpress.com/studio/) and the **Tenku** block theme + **tenku-child** custom theme.

## Stack

- WordPress (local Studio site)
- ACF Pro — homepage flexible sections (`acf/vip-home` block)
- Custom post type: `vip_vehicle` (fleet)
- Child theme: `wp-content/themes/tenku-child`

## Local development

See [STUDIO.md](STUDIO.md) for Studio CLI (`studio wp`, `studio site start`, etc.).

## GitHub backup

Remote: **https://github.com/Anand7579/VIP-Transits.git**

After you change theme, ACF JSON, or plugins, push an update:

```powershell
cd "e:\VIP Transits\vip-transits"
.\scripts\push-to-github.ps1
```

Or with a custom message:

```powershell
.\scripts\push-to-github.ps1 -Message "Fix FAQ accordion and image URLs"
```

### First-time setup on a new machine

```powershell
git clone https://github.com/Anand7579/VIP-Transits.git
cd VIP-Transits
```

Open the folder in **WordPress Studio** and start the site. Sync ACF field groups under **Custom Fields → Field Groups** if needed.

### Not in this repo

| Path | Reason |
|------|--------|
| `wp-content/database/` | Local SQLite file (Studio-specific) |
| `wp-content/cache/` | Generated cache |
| `.cursor/` | IDE-local |

Uploads **are** included so media URLs stay consistent across clones.

## Security note

`wp-config.php` contains local development salts. Treat this repo as **private** unless you rotate keys before any public deploy.
