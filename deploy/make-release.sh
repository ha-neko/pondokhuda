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

[[ -x "$PHP" ]] || PHP=$(command -v php)

rm -rf "$RELEASE"
mkdir -p "$RELEASE"

echo "==> [1/3] api -> api-$API_DOMAIN.tar.gz"
STAGE="$RELEASE/.stage-api"
mkdir -p "$STAGE"
rsync -a --exclude 'config-db.php' "$ROOT/api/" "$STAGE/"
"$PHP" -r '
$c = array(
    "db" => array("host" => $argv[1], "user" => $argv[2], "pass" => $argv[3], "name" => $argv[4]),
    "tokens" => array($argv[5]),
);
file_put_contents($argv[6], "<?php\nreturn " . var_export($c, true) . ";\n");
' "$DB_HOST" "$DB_USER" "$DB_PASS" "$DB_NAME" "$API_TOKEN" "$STAGE/config-db.php"
cat > "$STAGE/.htaccess" <<'HT'
Options -Indexes
<Files "kon.php">
  Require all denied
</Files>
HT
tar -czf "$RELEASE/api-$API_DOMAIN.tar.gz" -C "$STAGE" .
rm -rf "$STAGE"

echo "==> [2/3] laravel dashboard -> admin-$ADMIN_DOMAIN.tar.gz"
STAGE="$RELEASE/.stage-laravel"
mkdir -p "$STAGE"
rsync -a \
    --exclude '.env' \
    --exclude 'storage/framework/views/*.php' \
    --exclude 'storage/framework/sessions/*' \
    --exclude 'storage/logs/*.log' \
    "$ROOT/web/" "$STAGE/"
mkdir -p "$STAGE/storage/framework/"{views,sessions,cache} "$STAGE/storage/logs"
APP_KEY=$("$PHP" "$STAGE/artisan" key:generate --show --no-ansi)
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
    echo "API_BASE_URL=https://$API_DOMAIN/api"
    echo "API_TOKEN=$API_TOKEN"
} > "$STAGE/.env"
chmod -R u+rwX "$STAGE/storage"
tar -czf "$RELEASE/admin-$ADMIN_DOMAIN.tar.gz" -C "$STAGE" .
rm -rf "$STAGE"

echo "==> [3/3] tenant pwa"
if [[ -n "${TENANT_APP_DOMAIN:-}" ]]; then
    (cd "$ROOT/app" && VITE_API_BASE="https://$API_DOMAIN/api" npm run build >/dev/null)
    STAGE="$RELEASE/.stage-app"
    mkdir -p "$STAGE"
    rsync -a "$ROOT/app/dist/" "$STAGE/"
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
echo "      VITE_API_BASE=https://$API_DOMAIN/api"
