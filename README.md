# pondokhuda

Stack legacy pondokhuda.com — **Laravel 5.7 web** (`web/`) + **raw PHP API**
(`api/`) + **Aplikasi Mobile penghuni** (`app/`, React PWA), plus skema &
seed MySQL (`sql/`).

Working copy yang bisa diedit. Original milik root tetap utuh di
`/home/leafy/api-pondokhuda` dan `/home/leafy/pondokhuda.com`.

> Laravel tetap di 5.7. Tidak ada `composer update`. Tidak ada upgrade. Target:
> perbaikan in-place yang tetap kompatibel dengan cPanel/PHP 5.6–7.x.

---

## Arsitektur

```
HP/browser ──► app (React PWA) ──► /api/* ──► api/ (raw PHP) ──► MariaDB/MySQL
web (Laravel 5.7) ────────────────► /api/* ──► api/ (raw PHP) ──► MariaDB/MySQL
```

- **`api/`** — 82 endpoint PHP polos, POST-only, saling share `kon.php`
  (config, `esc()`, token gate, rate limiter).
- **`web/`** — Laravel 5.7.11, 6 controller yang memanggil API via
  `api_url()`/`api_header()`.
- **`app/`** — aplikasi penghuni: Vite 7 + React 19 + TS + Tailwind 4 +
  Material 3 token layer + PWA (Workbox). Dev memakai proxy `/api` → API lokal.

---

## Stack lokal (di box ini)

| Piece        | Versi / path |
|--------------|--------------|
| PHP          | 7.4.33 di `/home/leafy/.pondok/php/bin/php` |
| MariaDB      | 12.3.2, datadir `/home/leafy/.pondok/mysql`, socket `/home/leafy/.pondok/mysql.sock`, port 3306 |
| API server   | `php -S 0.0.0.0:8081` via `dev_api_server.sh` (router: hanya `/api/*`) |
| Web server   | `php artisan serve --host=0.0.0.0 --port=8000` |
| App (dev)    | `npm run dev -- --host 0.0.0.0 --port 5173` |

DB dev: `pondokhuda_dev` / user `pondokdev` / pass `Dev@Pondok!2026`.

### Kenapa bukan system PHP?
Arch mengirim PHP 8.5; Laravel 5.7 fatal (`ReflectionParameter::getClass()`
hilang). PHP 7.4.33 dibangun dari source dengan libs lokal di
`/home/leafy/.pondok/deps` (openssl 1.1.1w static, libxml2 2.12.7 static).

---

## Boot semuanya

```bash
mariadbd --datadir=/home/leafy/.pondok/mysql \
  --socket=/home/leafy/.pondok/mysql.sock --port=3306 \
  --bind-address=127.0.0.1 --user=leafy &

# API (bentuk URL prod: http://127.0.0.1:8081/api/...)
/home/leafy/projects/pondokhuda/dev_api_server.sh &
# log: /home/leafy/.pondok/api-server.log

# Web
cd /home/leafy/projects/pondokhuda/web
/home/leafy/.pondok/php/bin/php artisan serve --host=0.0.0.0 --port=8000 &
# log: /home/leafy/.pondok/web-server.log

# App (mobile PWA)
cd /home/leafy/projects/pondokhuda/app
npm run dev -- --host 0.0.0.0 --port 5173
```

Halaman: `http://<host>:8000/` (web) · App dev: `http://<host>:5173/` ·
API: `http://127.0.0.1:8081/api/...`.

### Rebuild DB dev dari nol

```bash
mariadb --socket=/home/leafy/.pondok/mysql.sock -u root -e \
  "DROP DATABASE IF EXISTS pondokhuda_dev; CREATE DATABASE pondokhuda_dev CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;"
mariadb --socket=/home/leafy/.pondok/mysql.sock -u root pondokhuda_dev < sql/schema.sql
mariadb --socket=/home/leafy/.pondok/mysql.sock -u root pondokhuda_dev < sql/seed.sql
```

MariaDB 12 memaksa koneksi ke `utf8mb4_uca1400_ai_ci`, jadi schema dev memakai
collation itu di mana-mana. View `tbv_status_bayar` dengan sengaja
`COLLATE utf8mb4_uca1400_ai_ci` pada literal `status_bayar` — kalau tidak,
literal-vs-literal tabrakan collation → `Illegal mix of collations`.

### Role login dev (seed) — PIN 6 digit

| Role         | kode    | pin     |
|--------------|---------|---------|
| Super owner  | `so001` | `123456`|
| Owner        | `o001w` | `123456`|
| Admin        | `a001d` | `123456`|
| Penyewa      | `pa0001`| `123456`|

Form login (web & app) memaksa `minlength=6 maxlength=6`.

---

## Konfigurasi

- `web/.env` — env dev (GITIGNORED). `APP_ENV=local`, DB lokal,
  `API_BASE_URL=http://127.0.0.1:8081/api`, `API_TOKEN=devtoken-pondokhuda-2026`.
- `web/.env.cpanel-backup` — env prod asli (GITIGNORED, jangan dipush).
- `config/config.local.php` — kredensial DB API lokal + token (GITIGNORED);
  dipakai `api/kon.php` dengan fallback ke `api/config-db.php` lalu env vars.
- `config/config.example.php` — template aman, tiruan untuk di-copy.
- `web/config/api.php` — base URL/token API sisi Laravel (fallback env).
- `web/app/helpers.php`: `api_url($endpoint)`, `api_header()`, `api_get()`.
- `dev_router.php` — router dev untuk `php -S`: hanya melayani `/api/*`,
  selain itu 404 (sehingga `web/.env`, `sql/`, dst tidak bisa diakses).
- `app/.env` — (opsional) `VITE_API_BASE` untuk override base API saat build.

---

## Aplikasi mobile (`app/`)

React PWA untuk penghuni kost. Material Design 3 (token layer di
`src/index.css`, light+dark, warna brand diambil dari `primary` tiap kost).

**Layar:** Login (kode + PIN 6 digit), Beranda (status tagihan, ringkasan,
kontak admin via WhatsApp), Pembayaran (histori + denda/diskon), Pengumuman
(list + detail + diskusi), Keluhan (list per-user + form baru + foto kamera),
Profil (data diri, ganti PIN, tema terang/gelap/sistem, logout).

**Jebakan API lama yang dinetralisir di `src/lib/`:**

- `api.ts` — transport POST form-urlencoded; toleran terhadap respons
  non-JSON (teks polos `user tidak diketahui`, empty body dari
  `login_ph.php`, 429 rate-limit).
- `normalizers.ts` — satu penormal per endpoint: bentuk polymorphic
  (objek/array/string/null) → tipe TS yang konsisten.
- Keluhan pakai `keluhan_getdata3.php` (per-user), **bukan**
  `penyewa_keluhan_getdata.php` yang hardcode `$user_log="uuu"` (bocor semua
  data keluhan semua penyewa).
- Kategori keluhan dikirim sebagai *nama*, bukan kode.
- Foto dikompres ke JPEG base64 (max 1200px) sebelum dikirim.

### Dev + uji di HP

```bash
cd app
npm install
npm run dev -- --host 0.0.0.0 --port 5173
```

Akses dari HP (PWA butuh HTTPS untuk bisa di-install): pakai tunnel

```bash
~/.local/bin/cloudflared tunnel --url http://127.0.0.1:5173
# → https://xxxx-xxx.trycloudflare.com  (buka di Chrome HP → Install app)
```

### Build produksi

```bash
cd app
VITE_API_BASE=/api npm run build   # atau VITE_API_BASE=https://api.pondokhuda.com/api
# hasil: app/dist → deploy statis + proxy /api
```

### CI/CD — build + auto release APK (GitHub Actions)

Workflow `.github/workflows/build-apk.yml`:

- **Trigger:** push ke `main` (`workflow_dispatch` manual juga bisa).
- **Alur:** install deps → build web (dengan `VITE_API_BASE` dari repo
  variable `API_BASE_URL`, fallback `https://api.pondokhuda.com/api`) →
  `cap add android` + `cap sync` → Gradle `assembleDebug` → upload artifact →
  **release otomatis** ke GitHub Releases.
- **Release:** push ke `main` = rolling release `dev` (di-update tiap push,
  tag lama dihapus); push **tag `v*`** = rilis versi bernama.
- **Setup yang dibutuhkan di repo (Settings → Secrets and variables → Actions):**
  - Variable `API_BASE_URL` → base URL API untuk APK
    (WebView origin-nya `https://localhost`, jadi harus absolut + CORS aktif).
- **APK** tersedia di: halaman Actions (artifact) atau Releases (`dev`).

### CORS

API (`api/kon.php` + `api/.htaccess`) mengirim
`Access-Control-Allow-Origin: *` — dibutuhkan WebView Capacitor (origin
`https://localhost`) dan klien browser lain. OPTIONS preflight ditangani
`kon.php` (204).

---

## Model keamanan API (diterapkan di working copy)

- **Endpoint mobile tetap bebas token** — allowlist di `api/kon.php`
  `_ph_allowlisted()` (login, lupapassword, ubahpin, pembayaran, pengumuman,
  keluhan, kamar kosong, wilayah).
- **Semua sisanya** (admin/owner/so/report/keu/log) butuh header
  `X-Api-Token` atau POST `_token`. Gagal → 401/429 JSON.
- **Hygiene PIN** — PIN dihapus dari semua respons JSON (`login_ph.php`
  semua 4 role, `so_owner-get-list.php`).
- **Rate limit** — `ph_rate_limit($bucket,$max,$window,$lock)` di `kon.php`;
  login `30/900s` (lock 60s), lupapassword `10/900s` (lock 300s). File lock
  di `sys_get_temp_dir()/ph-rl/`.
- **SQLi** — semua `$_GET/$_POST/$_REQUEST` lewat `esc()`.
  `api/.htaccess` memblokir dotfiles/log.

### Sudah diverifikasi (curl)

- 4 login → 200 JSON tanpa PIN; tanpa token → 401; probe SQLi netral.
- Rate limit: >30 login cepat → 429 + `retry_after`, pulih setelah lock.
- Web flow: login POST → 302 → dashboard 200 semua role.
- App: build `tsc -b && vite build` bersih; dev proxy `/api` tembus API lokal.

---

## Struktur repo

```
api/                    endpoint PHP polos (working copy)
  kon.php               bootstrap keras: config, esc(), token gate, rate limiter
  _quarantine/          endpoint sampah (error_log, test*, gettime, login_client…) — GITIGNORED
web/                    Laravel 5.7.11
  app/helpers.php       api_url() / api_header() / api_get()
  config/api.php        base URL + token API
  .env                  env dev (GITIGNORED) + .env.cpanel-backup (GITIGNORED)
sql/
  schema.sql            29 tabel + view tbv_status_bayar
  seed.sql              role login, 1 kost, 2 kamar, sewa, bayar, keluhan, pengumuman
app/                    Aplikasi mobile penghuni (React Vite PWA)
  src/lib/              api transport + normalizers + types + session
  src/screens/          login, dashboard, pembayaran, pengumuman, keluhan, profil
  src/components/       Ui (M3), Icon, AppShell (bottom nav)
  public/icons/         ikon PWA (192/512/maskable)
config/
  config.example.php    TEMPLATE kredensial (aman, ter-commit)
  config.local.php      kredensial dev API (GITIGNORED)
  config-db*.php        kredensial prod API + token (GITIGNORED)
dev_api_server.sh       launcher server API
dev_router.php          router API-only untuk php -S
```

---

## Deploy ke cPanel (untuk owner)

1. **`web/`** — naikkan isi ke document root situs. `.env` tetap versi prod
   (`APP_ENV=production`, `API_BASE_URL` menunjuk API live).
2. **`api/`** — naikkan `kon.php` keras, `.htaccess`, file endpoint yang
   berubah (`login_ph.php`, `penyewa_lupapassword.php`, `so_owner-get-list.php`).
   Buat `api/config-db.php` dengan kredensial prod + token.
3. **DB** — skema di sini rekonstruksi: **jangan** jalankan `schema.sql`
   buta ke prod tanpa diff (`SHOW CREATE TABLE` per tabel).
4. **PHP version** — cPanel PHP 5.6/7.x; Laravel 5.7 butuh 7.1+.
5. **URL** — controller resolve API via `config/api.php`/`api_url()`,
   default `https://api.pondokhuda.com/api`. Cek `API_BASE_URL` di `.env` prod.
6. **Token** — endpoint gated butuh header. `login_ph.php` tetap allowlisted
   biar binary app lama tetap jalan.

---

## Gotcha yang sudah ketangkap

- Laravel 5.7 `PackageManifest` mau `installed.json` gaya lama (keyed);
  composer 2 menulis flat. Kalau `composer install` menulis ulang, konversi
  balik atau `artisan package:discover` mati `Undefined index: name`.
- `beyondcode/laravel-dump-server` (dev) tidak bisa install di setup
  composer-2-tanpa-network — pakai `composer install --no-dev` dan hapus
  `bootstrap/cache/packages.php` stale.
- `tb_beritakost` PK = `kode` (bukan `kode_berita`), status filter `'tampil'`
  (bukan `'Aktif'`) — rekonstruksi awal menebak salah, pengumuman diam-diam
  kosong.
- Literal kolom string di view `tbv_status_bayar` harus di-`COLLATE` eksplisit
  atau bandingkan dengan literal → illegal-mix.
- `Handler::render()` menelan semua exception ke view `error/500` dengan HTTP
  200 — debug lewat `storage/logs/laravel.log`, bukan response code.
- `get_report.php` `ConvertKeDuaDesimal()` pakai `substr` → fatal di PHP 8
  untuk nilai integer (`strpos(): needle not found`). Endpoint ini sengaja
  belum dipakai mobile app.
- `owner_keu-posisikeu.php` mengembalikan `{"kas":200000,"bank":100000}`
  hardcoded — bukan data asli (bug bawaan, dibiarkan).
- Foto/logo ditulis ke path absolut `/home/pondokhu/public_html/...` yang
  server-specific; `owner_kost-update.php` pakai `unlink("../Assets/...")`
  (relatif) yang kemungkinan gagal dari direktori api.