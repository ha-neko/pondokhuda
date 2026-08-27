# cPanel deployment guide

Deploys the new stack onto the existing hosting account, reusing the **existing
production database** — nothing in the old database is modified.

```
                        ┌────────────────────────────────┐
 api.pondokhuda.com ───► │ ~/api          raw PHP api     │──┐
 admin.pondokhuda.com ─► │ ~/laravel/public  dashboard    │  ├── same MySQL db
 app.pondokhuda.com ───► │ ~/tenant-app   PWA (optional)  │──┘   (old data)
        APK (phones) ─────────────────────── calls https://api.…
```

## 0. one-time on your machine

```bash
cp deploy/prod.env.example deploy/prod.env
# edit deploy/prod.env — real DB creds from the old project, domains, token
./deploy/make-release.sh
```

Produces `deploy/release/`:

| archive | upload target |
|---|---|
| `api-<domain>.tar.gz` | `~/api` |
| `admin-<domain>.tar.gz` | `~/laravel` |
| `app-<domain>.tar.gz` | `~/tenant-app` (if enabled) |

The script generates `config-db.php` and `.env` with matching credentials and a
fresh `APP_KEY`, so nothing needs hand-editing on the server.

Before building, create the `MAIL_FROM` address in cPanel and configure it in
`deploy/prod.env`. The API uses PHP's local cPanel mail transport; SPF and DKIM
must be valid for that sender domain. Receipt releases bundle TCPDF and the
invoice template from `web/public/` automatically.

## 1. create the subdomains

cPanel → **Domains → Create A New Domain** (or Subdomains):

| domain | document root |
|---|---|
| `api.pondokhuda.com` | `api` |
| `admin.pondokhuda.com` | `laravel/public` |
| `app.pondokhuda.com` *(optional)* | `tenant-app` |

Point the DNS records at the hosting server IP if they are not auto-created.
Wait for propagation before continuing.

## 2. set PHP version

cPanel → **MultiPHP Manager**: select **PHP 7.4** for all three domains.

> Laravel 5.7 does not run on PHP 8. If only 8.x is offered, ask the host to
> enable 7.4 — this is a hard requirement.

## 3. upload + extract

File Manager → for each row of the table above:

1. create the folder (e.g. `~/api`)
2. upload the matching `.tar.gz`
3. right-click → **Extract**
4. delete the archive afterwards

Laravel note: extract `admin-…tar.gz` into `~/laravel` — the archive already
contains `public/`; the subdomain docroot must be `laravel/public`.
The release also keeps `~/laravel/public/Assets/images/owner` writable by the
cPanel account so the API can store optional owner photos there.

## 4. SSL

cPanel → **SSL/TLS Status** → Run AutoSSL for the new subdomains.
Then enable *Force HTTPS Redirect* in the Domains panel for each.

## 5. smoke test

```bash
curl -X POST https://api.pondokhuda.com/login_ph.php \
     -d "kode=<real kode>&pin=<real pin>"
```

Expect JSON `personalinfopenyewa`. Then open
`https://admin.pondokhuda.com` and log into the dashboard.

If the API returns `unauthorized`: `config-db.php` tokens vs Laravel `.env`
`API_TOKEN` mismatch — both were generated from `deploy/prod.env`, re-run the
script if you changed one side.

## 6. rebuild the APK with production API

Edit `.github/workflows/build-apk.yml`:

```yaml
VITE_API_BASE: https://api.pondokhuda.com
```

push → download the new artifact. The tunnel (`*.ngrok-free.dev`) stays for
development only.

## notes

- the archives include `vendor/` — no composer needed on the server.
- `api/config-db.php` is generated, gitignored locally, never committed.
- dev seed accounts (`pa0001` / PIN `123456`) exist only in the local dev DB,
  not on production.
- to ship a fresh database dump anyway: `mysqldump --single-transaction -u…
  -p pondokhuda_dev > dump.sql` then strip `DEFINER=` clauses before importing
  via phpMyAdmin. Only needed when starting an empty database.
