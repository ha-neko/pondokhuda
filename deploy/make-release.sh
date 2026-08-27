#!/usr/bin/env bash
# make-release.sh — build cPanel-ready archives for pondokhuda.
#
# usage:
#   ./deploy/make-release.sh [path/to/prod.env]
#
# output: deploy/release/*.tar.gz  (one archive per cPanel location)
# requires the api DB credentials of the EXISTING production database —
# this script never touches the remote database, it only points new code at it.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
ENV_FILE="${1:-$ROOT/deploy/prod.env}"
RELEASE="$ROOT/deploy/release"
PHP="${PH_PHP:-$HOME/.pondok/php/bin/php}"
PYTHON="${PH_PYTHON:-$(command -v python)}"

if [[ ! -f "$ENV_FILE" ]]; then
    echo "error: $ENV_FILE not found."
    echo "       cp deploy/prod.env.example deploy/prod.env and fill it in."
    exit 1
fi
# shellcheck disable=SC1090
source "$ENV_FILE"
: "${API_DOMAIN:?API_DOMAIN empty in $ENV_FILE}"
: "${ADMIN_DOMAIN:?ADMIN_DOMAIN empty in $ENV_FILE}"
: "${DB_HOST:?DB_HOST empty in $ENV_FILE}"
: "${DB_NAME:?DB_NAME empty in $ENV_FILE}"
: "${DB_USER:?DB_USER empty in $ENV_FILE}"
: "${DB_PASS:?DB_PASS empty in $ENV_FILE}"
: "${API_TOKEN:?API_TOKEN empty in $ENV_FILE}"
: "${MAIL_FROM:=noreply@$API_DOMAIN}"
: "${MAIL_FROM_NAME:=Pondok Huda}"

if [[ ! -x "$PHP" ]]; then
    PHP=$(command -v php || true)
fi

copy_api_tree() {
    if command -v rsync >/dev/null 2>&1; then
        rsync -a --exclude 'config-db.php' "$ROOT/api/" "$1/"
    else
        cp -a "$ROOT/api/." "$1/"
        rm -f "$1/config-db.php"
    fi
}

copy_admin_tree() {
    if command -v rsync >/dev/null 2>&1; then
        rsync -a \
            --exclude '.env' \
            --exclude 'storage/framework/views/*.php' \
            --exclude 'storage/framework/sessions/*' \
            --exclude 'storage/logs/*.log' \
            "$ROOT/web/" "$1/"
    else
        cp -a "$ROOT/web/." "$1/"
        rm -f "$1/.env" "$1"/storage/framework/views/*.php \
            "$1"/storage/framework/sessions/* "$1"/storage/logs/*.log
    fi
}

rm -rf "$RELEASE"
mkdir -p "$RELEASE"

echo "==> [1/3] api -> api-$API_DOMAIN.tar.gz"
STAGE="$RELEASE/.stage-api"
mkdir -p "$STAGE"
copy_api_tree "$STAGE"
TCPDF_SOURCE="$ROOT/web/public/pdf/TCPDF-master"
INVOICE_TEMPLATE="$ROOT/web/public/kwitansi/template-invoice.jpg"
[[ -f "$TCPDF_SOURCE/tcpdf.php" ]] || { echo "error: missing $TCPDF_SOURCE/tcpdf.php"; exit 1; }
[[ -f "$INVOICE_TEMPLATE" ]] || { echo "error: missing $INVOICE_TEMPLATE"; exit 1; }
mkdir -p "$STAGE/pdf" "$STAGE/kwitansi"
if command -v rsync >/dev/null 2>&1; then
    rsync -a "$TCPDF_SOURCE/" "$STAGE/pdf/TCPDF-master/"
else
    cp -a "$TCPDF_SOURCE/." "$STAGE/pdf/TCPDF-master/"
fi
cp "$INVOICE_TEMPLATE" "$STAGE/kwitansi/template-invoice.jpg"
"$PYTHON" - "$DB_HOST" "$DB_USER" "$DB_PASS" "$DB_NAME" "$API_TOKEN" "$MAIL_FROM" "$MAIL_FROM_NAME" "$STAGE/config-db.php" <<'PY'
import sys

def q(value):
    return "'" + value.replace('\\', '\\\\').replace("'", "\\'") + "'"

host, user, password, name, token, sender, sender_name, output = sys.argv[1:]
config = """<?php
return array (
  'db' => array (
    'host' => %s,
    'user' => %s,
    'pass' => %s,
    'name' => %s,
  ),
  'tokens' => array (%s),
  'mail' => array (
    'from' => %s,
    'from_name' => %s,
  ),
);
""" % tuple(map(q, (host, user, password, name, token, sender, sender_name)))
with open(output, 'w') as handle:
    handle.write(config)
PY
# keep the existing api/.htaccess (CORS fallback), only add the kon.php guard
if [[ -f "$STAGE/.htaccess" ]] && ! grep -q 'kon.php' "$STAGE/.htaccess"; then
    cat >> "$STAGE/.htaccess" <<'HT'

<Files "kon.php">
  Require all denied
</Files>
HT
fi
tar -czf "$RELEASE/api-$API_DOMAIN.tar.gz" -C "$STAGE" .
rm -rf "$STAGE"

echo "==> [2/3] laravel dashboard -> admin-$ADMIN_DOMAIN.tar.gz"
STAGE="$RELEASE/.stage-laravel"
mkdir -p "$STAGE"
copy_admin_tree "$STAGE"
mkdir -p "$STAGE/storage/framework/"{views,sessions,cache} "$STAGE/storage/logs"
mkdir -p "$STAGE/public/Assets/images/owner" "$STAGE/public/Assets/images/user"
if [[ -n "$PHP" && -x "$PHP" ]]; then
    APP_KEY=$("$PHP" "$STAGE/artisan" key:generate --show --no-ansi)
else
    APP_KEY="base64:$(openssl rand -base64 32)"
fi
{
    echo 'APP_NAME="Pondok Huda"'
    echo 'APP_ENV=production'
    echo "APP_KEY=$APP_KEY"
    echo 'APP_DEBUG=false'
    echo "APP_URL=https://$ADMIN_DOMAIN"
    echo ''
    echo 'DB_CONNECTION=mysql'
    echo "DB_HOST=$DB_HOST"
    echo 'DB_PORT=3306'
    echo "DB_DATABASE=$DB_NAME"
    echo "DB_USERNAME=$DB_USER"
    echo "DB_PASSWORD=$DB_PASS"
    echo ''
    echo "API_BASE_URL=https://$API_DOMAIN"
    echo "API_TOKEN=$API_TOKEN"
} > "$STAGE/.env"
chmod -R u+rwX "$STAGE/storage"
chmod u+rwx "$STAGE/public/Assets/images/owner" "$STAGE/public/Assets/images/user"
tar -czf "$RELEASE/admin-$ADMIN_DOMAIN.tar.gz" -C "$STAGE" .
rm -rf "$STAGE"

echo "==> [3/3] tenant pwa"
if [[ -n "${TENANT_APP_DOMAIN:-}" ]]; then
    (cd "$ROOT/app" && VITE_API_BASE="https://$API_DOMAIN" npm run build >/dev/null)
    STAGE="$RELEASE/.stage-app"
    mkdir -p "$STAGE"
    if command -v rsync >/dev/null 2>&1; then
        rsync -a "$ROOT/app/dist/" "$STAGE/"
    else
        cp -a "$ROOT/app/dist/." "$STAGE/"
    fi
    tar -czf "$RELEASE/app-$TENANT_APP_DOMAIN.tar.gz" -C "$STAGE" .
    rm -rf "$STAGE"
else
    echo "    TENANT_APP_DOMAIN empty — skipped (APK does not need hosting)"
fi

echo
echo "release ready in deploy/release/:"
ls -lh "$RELEASE" | awk 'NR>1 {printf "    %-46s %s\n", $NF, $5}'
echo
echo "next: read deploy/README.md — create the subdomains, upload, extract,"
echo "      set PHP 7.4, enable AutoSSL. apk rebuild afterwards:"
echo "      VITE_API_BASE=https://$API_DOMAIN"
