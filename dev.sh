#!/usr/bin/env bash
# Pondok Huda local development stack manager.
# Interactive with no arguments; scriptable with start/stop/restart/status/logs.

set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RUNTIME_DIR="$ROOT/.dev-runtime"
LOG_DIR="$RUNTIME_DIR/logs"
LOCK_FILE="$RUNTIME_DIR/manager.lock"
PHP="/home/leafy/.pondok/php/bin/php"
MYSQL_DIR="/home/leafy/.pondok/mysql"
MYSQL_SOCKET="/home/leafy/.pondok/mysql.sock"
MYSQL_PID="/home/leafy/.pondok/mysql.pid"
CLOUDFLARED="/home/leafy/.local/bin/cloudflared"
NGROK="/home/leafy/.local/bin/ngrok"
TUNNEL_ENV="$ROOT/.dev-tunnel.env"

PH_TUNNEL_PROVIDER="cloudflare"
PH_TUNNEL_MODE="quick"
PH_TUNNEL_NAME=""
PH_TUNNEL_URL=""
PH_TUNNEL_CONFIG="/home/leafy/.cloudflared/config.yml"
PH_TUNNEL_TOKEN_FILE=""
if [[ -f "$TUNNEL_ENV" ]]; then
    # Local-only configuration. Keep tokens outside the repository.
    # shellcheck disable=SC1090
    source "$TUNNEL_ENV"
fi

SERVICES=(db api web app tunnel)
CORE_SERVICES=(db api web app)

if [[ -t 1 ]]; then
    RESET=$'\033[0m'; BOLD=$'\033[1m'; DIM=$'\033[2m'
    GREEN=$'\033[32m'; YELLOW=$'\033[33m'; RED=$'\033[31m'; CYAN=$'\033[36m'
else
    RESET=''; BOLD=''; DIM=''; GREEN=''; YELLOW=''; RED=''; CYAN=''
fi

mkdir -p "$LOG_DIR"

pid_file() { printf '%s/%s.pid' "$RUNTIME_DIR" "$1"; }
log_file() { printf '%s/%s.log' "$LOG_DIR" "$1"; }

service_label() {
    case "$1" in
        db) printf 'MariaDB' ;;
        api) printf 'PHP API' ;;
        web) printf 'Laravel Web' ;;
        app) printf 'React App' ;;
        tunnel) printf 'HTTPS Tunnel' ;;
    esac
}

service_url() {
    case "$1" in
        db) printf '127.0.0.1:3306' ;;
        api) printf 'http://localhost:8081/api' ;;
        web) printf 'http://localhost:8000' ;;
        app) printf 'http://localhost:5173' ;;
        tunnel) tunnel_url ;;
    esac
}

read_pid() {
    local file pid _
    file="$(pid_file "$1")"
    [[ -f "$file" ]] || return 1
    read -r pid _ < "$file" || return 1
    [[ "$pid" =~ ^[1-9][0-9]*$ ]] || return 1
    printf '%s' "$pid"
}

process_start_time() {
    local pid="$1" stat rest fields
    [[ -r "/proc/$pid/stat" ]] || return 1
    IFS= read -r stat < "/proc/$pid/stat" || return 1
    rest="${stat##*) }"
    read -ra fields <<< "$rest"
    [[ "${fields[19]:-}" =~ ^[0-9]+$ ]] || return 1
    printf '%s' "${fields[19]}"
}

process_matches_service() {
    local service="$1" pid="$2" command
    [[ -r "/proc/$pid/cmdline" ]] || return 1
    command="$(tr '\0' ' ' < "/proc/$pid/cmdline")"
    case "$service" in
        db) [[ "$command" == *mariadbd*"--datadir=$MYSQL_DIR"* ]] ;;
        api) [[ "$command" == *"$PHP -S 0.0.0.0:8081"*"$ROOT/dev_router.php"* ]] ;;
        web) [[ "$command" == *"$PHP artisan serve"*"--port=8000"* ]] ;;
        app) [[ "$command" == *"npm run dev"*"--port=5173"* || "$command" == *vite*"--port=5173"* ]] ;;
        tunnel)
            if [[ "${PH_TUNNEL_PROVIDER:-cloudflare}" == ngrok ]]; then
                [[ "$command" == *ngrok*"$(ngrok_domain)"* ]]
            else
                [[ "$command" == *cloudflared*tunnel* ]]
            fi
            ;;
        *) return 1 ;;
    esac
}

write_pid_record() {
    local service="$1" pid="$2" started tmp
    started="$(process_start_time "$pid")" || return 1
    tmp="$(pid_file "$service").tmp.$$"
    printf '%s %s\n' "$pid" "$started" > "$tmp"
    mv -f "$tmp" "$(pid_file "$service")"
}

recorded_process_alive() {
    local service="$1" pid recorded_pid recorded_start current_start
    pid="$(read_pid "$service")" || return 1
    kill -0 "$pid" 2>/dev/null || return 1
    read -r recorded_pid recorded_start < "$(pid_file "$service")" || return 1
    [[ "$recorded_pid" == "$pid" && "${recorded_start:-}" =~ ^[0-9]+$ ]] || return 1
    current_start="$(process_start_time "$pid")" || return 1
    [[ "$recorded_start" == "$current_start" ]]
}

managed_running() {
    local service="$1" pid
    recorded_process_alive "$service" || return 1
    pid="$(read_pid "$service")" || return 1
    process_matches_service "$service" "$pid" || return 1
}

service_responding() {
    case "$1" in
        db) mariadb-admin --socket="$MYSQL_SOCKET" -u root ping --silent >/dev/null 2>&1 ;;
        api) curl -sS -o /dev/null --max-time 1 http://127.0.0.1:8081/api/login_ph.php 2>/dev/null ;;
        web) curl -sS -o /dev/null --max-time 1 http://127.0.0.1:8000/ 2>/dev/null ;;
        app) curl -sS -o /dev/null --max-time 1 http://127.0.0.1:5173/ 2>/dev/null ;;
        tunnel) tunnel_healthy ;;
    esac
}

listener_owned_by_service() {
    local service="$1" port="$2" root_pid root_pgid output candidate candidate_pgid
    managed_running "$service" || return 1
    root_pid="$(read_pid "$service")" || return 1
    root_pgid="$(ps -o pgid= -p "$root_pid" 2>/dev/null)"
    root_pgid="${root_pgid//[[:space:]]/}"
    [[ -n "$root_pgid" ]] || return 1
    output="$(ss -H -ltnp "sport = :$port" 2>/dev/null)"
    while [[ "$output" =~ pid=([0-9]+) ]]; do
        candidate="${BASH_REMATCH[1]}"
        candidate_pgid="$(ps -o pgid= -p "$candidate" 2>/dev/null)"
        candidate_pgid="${candidate_pgid//[[:space:]]/}"
        [[ "$candidate_pgid" == "$root_pgid" ]] && return 0
        output="${output#*pid=$candidate}"
    done
    return 1
}

service_healthy() {
    case "$1" in
        db) listener_owned_by_service db 3306 && service_responding db ;;
        api) listener_owned_by_service api 8081 && service_responding api ;;
        web) listener_owned_by_service web 8000 && service_responding web ;;
        app) listener_owned_by_service app 5173 && service_responding app ;;
        tunnel) managed_running tunnel && tunnel_healthy ;;
    esac
}

tunnel_url() {
    local log url=''
    if [[ -n "${PH_TUNNEL_URL:-}" ]]; then
        printf '%s' "$PH_TUNNEL_URL"
        return
    fi
    log="$(log_file tunnel)"
    if [[ -f "$log" ]]; then
        url="$(grep -Eo 'https://[a-z0-9-]+\.trycloudflare\.com' "$log" 2>/dev/null | tail -n 1 || true)"
    fi
    printf '%s' "${url:-waiting for URL}"
}

tunnel_mode() {
    if [[ "${PH_TUNNEL_PROVIDER:-cloudflare}" == ngrok ]]; then
        printf 'ngrok/free-static'
        return
    fi
    case "${PH_TUNNEL_MODE:-quick}" in
        named) printf 'named/permanent' ;;
        token) printf 'token/permanent' ;;
        *) printf 'quick/temporary' ;;
    esac
}

ngrok_domain() {
    local domain="${PH_TUNNEL_URL:-}"
    domain="${domain#https://}"
    domain="${domain#http://}"
    printf '%s' "${domain%/}"
}

tunnel_healthy() {
    service_healthy app || return 1
    if [[ "${PH_TUNNEL_PROVIDER:-cloudflare}" == ngrok ]]; then
        curl -fsS http://127.0.0.1:4040/api/tunnels 2>/dev/null \
            | grep -Fq "$(ngrok_domain)"
    elif ! managed_running tunnel; then
        return 1
    elif [[ "${PH_TUNNEL_MODE:-quick}" == quick ]]; then
        [[ "$(tunnel_url)" != 'waiting for URL' ]]
    elif [[ -n "${PH_TUNNEL_URL:-}" ]]; then
        curl -sS -o /dev/null --max-time 2 "$PH_TUNNEL_URL" 2>/dev/null
    else
        return 0
    fi
}

cleanup_stale_pid() {
    local service="$1" file
    file="$(pid_file "$service")"
    if [[ -f "$file" ]] && ! managed_running "$service"; then
        rm -f "$file"
    fi
}

launch() {
    local service="$1"
    shift
    local log pid i
    log="$(log_file "$service")"
    : > "$log"
    # Never let managed children inherit the process-manager flock descriptor.
    nohup setsid "$@" 9>&- >> "$log" 2>&1 < /dev/null &
    pid=$!
    for ((i=0; i<20; i++)); do
        write_pid_record "$service" "$pid" && return 0
        sleep .01
    done
    printf '%s\n' "$pid" > "$(pid_file "$service")"
}

signal_managed() {
    local service="$1" signal="$2" pid pgid
    managed_running "$service" || return 1
    pid="$(read_pid "$service")" || return 1
    signal_pid "$pid" "$signal"
}

signal_pid() {
    local pid="$1" signal="$2" pgid
    pgid="$(ps -o pgid= -p "$pid" 2>/dev/null)"
    pgid="${pgid//[[:space:]]/}"
    if [[ "$pgid" == "$pid" ]]; then
        kill "-$signal" -- "-$pid" 2>/dev/null
    else
        kill "-$signal" "$pid" 2>/dev/null
    fi
}

wait_ready() {
    local service="$1" tries="${2:-30}" i
    if [[ "$service" == tunnel ]]; then
        for ((i=0; i<tries; i++)); do
            recorded_process_alive tunnel || return 1
            if [[ "${PH_TUNNEL_PROVIDER:-cloudflare}" == ngrok ]]; then
                curl -fsS http://127.0.0.1:4040/api/tunnels 2>/dev/null \
                    | grep -Fq "$(ngrok_domain)" && return 0
            elif [[ "${PH_TUNNEL_MODE:-quick}" == quick ]]; then
                [[ "$(tunnel_url)" != 'waiting for URL' ]] && return 0
            elif ((i >= 4)); then
                return 0
            fi
            sleep .25
        done
        return 1
    fi
    for ((i=0; i<tries; i++)); do
        if service_healthy "$service"; then return 0; fi
        if ! recorded_process_alive "$service"; then return 1; fi
        sleep .25
    done
    return 1
}

start_one() {
    local service="$1" label
    label="$(service_label "$service")"
    cleanup_stale_pid "$service"

    if managed_running "$service"; then
        printf '  %s●%s %-14s already running\n' "$GREEN" "$RESET" "$label"
        return 0
    fi
    if service_responding "$service"; then
        printf '  %s◆%s %-14s port used by an external process\n' "$YELLOW" "$RESET" "$label"
        return 0
    fi

    printf '  %s…%s starting %-14s' "$CYAN" "$RESET" "$label"
    case "$service" in
        db)
            rm -f "$MYSQL_PID"
            launch db /usr/sbin/mariadbd \
                --datadir="$MYSQL_DIR" --socket="$MYSQL_SOCKET" \
                --pid-file="$MYSQL_PID" --bind-address=127.0.0.1 \
                --port=3306 --user="$(id -un)" --log-error="$(log_file db)"
            ;;
        api)
            launch api "$ROOT/dev_api_server.sh"
            ;;
        web)
            launch web bash -c "cd \"$ROOT/web\" && exec \"$PHP\" artisan serve --host=0.0.0.0 --port=8000"
            ;;
        app)
            launch app bash -c "cd \"$ROOT/app\" && exec npm run dev -- --host=0.0.0.0 --port=5173"
            ;;
        tunnel)
            if ! service_responding app; then
                printf '\r  %s×%s %-14s app must be running first\n' "$RED" "$RESET" "$label"
                return 1
            fi
            if [[ "${PH_TUNNEL_PROVIDER:-cloudflare}" == ngrok ]]; then
                if [[ ! -x "$NGROK" ]]; then
                    printf '\r  %s×%s %-14s ngrok v3 is not installed\n' "$RED" "$RESET" "$label"
                    return 1
                fi
                if [[ -z "${PH_TUNNEL_URL:-}" ]]; then
                    printf '\r  %s×%s %-14s set PH_TUNNEL_URL first\n' "$RED" "$RESET" "$label"
                    return 1
                fi
                launch tunnel "$NGROK" http --url "$(ngrok_domain)" 5173
            else
                if [[ ! -x "$CLOUDFLARED" ]]; then
                    printf '\r  %s×%s %-14s cloudflared not installed\n' "$RED" "$RESET" "$label"
                    return 1
                fi
                case "${PH_TUNNEL_MODE:-quick}" in
                    named)
                        if [[ -z "${PH_TUNNEL_NAME:-}" || ! -f "${PH_TUNNEL_CONFIG:-}" ]]; then
                            printf '\r  %s×%s %-14s named tunnel config is incomplete\n' "$RED" "$RESET" "$label"
                            return 1
                        fi
                        launch tunnel "$CLOUDFLARED" tunnel --config "$PH_TUNNEL_CONFIG" --no-autoupdate run "$PH_TUNNEL_NAME"
                        ;;
                    token)
                        if [[ -z "${PH_TUNNEL_TOKEN_FILE:-}" || ! -f "$PH_TUNNEL_TOKEN_FILE" ]]; then
                            printf '\r  %s×%s %-14s token file is missing\n' "$RED" "$RESET" "$label"
                            return 1
                        fi
                        launch tunnel "$CLOUDFLARED" tunnel --no-autoupdate run --token-file "$PH_TUNNEL_TOKEN_FILE"
                        ;;
                    *)
                        launch tunnel "$CLOUDFLARED" tunnel --url http://127.0.0.1:5173 --no-autoupdate
                        ;;
                esac
            fi
            ;;
    esac

    if wait_ready "$service" 40; then
        printf '\r  %s●%s %-14s %s\n' "$GREEN" "$RESET" "$label" "$(service_url "$service")"
    else
        printf '\r  %s×%s %-14s failed, see %s\n' "$RED" "$RESET" "$label" "$(log_file "$service")"
        if recorded_process_alive "$service"; then
            local failed_pid
            failed_pid="$(read_pid "$service")"
            signal_pid "$failed_pid" TERM || true
            sleep .5
            recorded_process_alive "$service" && signal_pid "$failed_pid" KILL || true
            for ((i=0; i<20; i++)); do
                recorded_process_alive "$service" || break
                sleep .1
            done
            if recorded_process_alive "$service"; then
                printf '  %s×%s %-14s could not terminate pid %s\n' "$RED" "$RESET" "$label" "$failed_pid"
                return 1
            fi
        fi
        rm -f "$(pid_file "$service")"
        return 1
    fi
}

stop_one() {
    local service="$1" label pid i
    label="$(service_label "$service")"
    cleanup_stale_pid "$service"
    if ! managed_running "$service"; then
        if service_responding "$service"; then
            printf '  %s◆%s %-14s external process left untouched\n' "$YELLOW" "$RESET" "$label"
        else
            printf '  %s○%s %-14s already stopped\n' "$DIM" "$RESET" "$label"
        fi
        return 0
    fi
    if [[ "$service" == app ]] && managed_running tunnel; then
        stop_one tunnel || return 1
    fi
    pid="$(read_pid "$service")"
    printf '  %s…%s stopping %-14s' "$YELLOW" "$RESET" "$label"

    if [[ "$service" == db ]] && command -v mariadb-admin >/dev/null 2>&1; then
        mariadb-admin --socket="$MYSQL_SOCKET" -u root shutdown >/dev/null 2>&1 || true
    else
        signal_managed "$service" TERM || true
    fi

    for ((i=0; i<40; i++)); do
        managed_running "$service" || break
        sleep .25
    done
    if managed_running "$service"; then
        signal_managed "$service" KILL || true
        for ((i=0; i<20; i++)); do
            recorded_process_alive "$service" || break
            sleep .1
        done
    fi
    if recorded_process_alive "$service"; then
        printf '\r  %s×%s %-14s could not terminate pid %s\n' "$RED" "$RESET" "$label" "$pid"
        return 1
    fi
    rm -f "$(pid_file "$service")"
    printf '\r  %s○%s %-14s stopped\n' "$DIM" "$RESET" "$label"
}

start_all() {
    printf '\n%sStarting Pondok Huda development stack%s\n' "$BOLD" "$RESET"
    local service rc=0
    for service in "${CORE_SERVICES[@]}"; do start_one "$service" || rc=1; done
    return "$rc"
}

stop_all() {
    printf '\n%sStopping Pondok Huda development stack%s\n' "$BOLD" "$RESET"
    local order=(tunnel app web api db) service rc=0
    for service in "${order[@]}"; do stop_one "$service" || rc=1; done
    return "$rc"
}

status_line() {
    local service="$1" label state detail
    label="$(service_label "$service")"
    if managed_running "$service"; then
        if service_healthy "$service"; then
            state="${GREEN}RUNNING${RESET}"
        else
            state="${YELLOW}DEGRADED${RESET}"
        fi
        detail="pid $(read_pid "$service") · $(service_url "$service")"
    elif service_responding "$service"; then
        state="${YELLOW}EXTERNAL${RESET}"
        detail="responding, not managed by dev.sh"
    else
        state="${DIM}STOPPED${RESET}"
        detail="$(service_url "$service")"
    fi
    printf '  %-14s %-18b %s\n' "$label" "$state" "$detail"
}

show_status() {
    printf '\n%sPondok Huda development stack%s  %stunnel: %s%s\n\n' "$BOLD" "$RESET" "$DIM" "$(tunnel_mode)" "$RESET"
    local service
    for service in "${SERVICES[@]}"; do status_line "$service"; done
    printf '\n  logs: %s\n' "$LOG_DIR"
}

tunnel_help() {
    cat <<EOF

Free permanent tunnel setup (no owned domain required)

  1. Create a free account and claim its free static domain:
       https://dashboard.ngrok.com/signup
       https://dashboard.ngrok.com/domains

  2. Authenticate locally. Never paste the token into Git or chat:
       $NGROK config add-authtoken YOUR_TOKEN

  3. Copy .dev-tunnel.env.example to .dev-tunnel.env and replace
     PH_TUNNEL_URL with the claimed https://*.ngrok-free.dev hostname.

The TUI Toggle HTTPS tunnel action will then reuse that hostname every time.

Cloudflare named tunnel alternative (requires an owned domain)

  1. Authenticate once:
       $CLOUDFLARED tunnel login

  2. Create and route a named tunnel:
       $CLOUDFLARED tunnel create pondokhuda-dev
       $CLOUDFLARED tunnel route dns pondokhuda-dev dev-app.your-domain.com

  3. Create ~/.cloudflared/config.yml with tunnel UUID, credentials-file,
     and ingress service http://127.0.0.1:5173.

  4. Copy .dev-tunnel.env.example to .dev-tunnel.env and fill its hostname.

Cloudflare quick mode remains available as a fallback, but its
trycloudflare.com address is temporary.
EOF
}

validate_service() {
    local candidate="$1" service
    [[ "$candidate" == all ]] && return 0
    for service in "${SERVICES[@]}"; do [[ "$candidate" == "$service" ]] && return 0; done
    printf 'unknown service: %s\nvalid services: all, %s\n' "$candidate" "${SERVICES[*]}" >&2
    return 1
}

start_target() {
    local target="${1:-all}"
    validate_service "$target" || return 2
    [[ "$target" == all ]] && start_all || start_one "$target"
}

stop_target() {
    local target="${1:-all}"
    validate_service "$target" || return 2
    [[ "$target" == all ]] && stop_all || stop_one "$target"
}

restart_target() {
    local target="${1:-all}"
    local restore_tunnel=0 rc=0
    if [[ "$target" == app || "$target" == all ]]; then
        managed_running tunnel && restore_tunnel=1
    fi
    stop_target "$target" || return $?
    start_target "$target" || rc=$?
    if ((restore_tunnel)) && ! managed_running tunnel; then
        start_one tunnel || rc=$?
    fi
    return "$rc"
}

with_manager_lock() {
    (
        if ! flock -w 90 9; then
            printf 'could not acquire process-manager lock\n' >&2
            return 75
        fi
        "$@"
    ) 9>"$LOCK_FILE"
}

show_logs() {
    local service="${1:-}"
    if [[ -z "$service" ]]; then
        printf 'service for logs (%s): ' "${SERVICES[*]}"
        read -r service
    fi
    validate_service "$service" || return 2
    if [[ "$service" == all ]]; then
        local item
        for item in "${SERVICES[@]}"; do
            printf '\n%s== %s ==%s\n' "$BOLD" "$(service_label "$item")" "$RESET"
            [[ -f "$(log_file "$item")" ]] && tail -n 20 "$(log_file "$item")" || printf 'no log yet\n'
        done
    else
        touch "$(log_file "$service")"
        printf '%sFollowing %s. Ctrl-C returns.%s\n' "$DIM" "$(log_file "$service")" "$RESET"
        trap ':' INT
        tail -n 80 -f "$(log_file "$service")"
        trap - INT
    fi
}

tui() {
    if [[ ! -t 0 || ! -t 1 ]]; then
        printf 'interactive dashboard requires a terminal; use ./dev.sh status instead.\n' >&2
        return 1
    fi
    exec python3 "$ROOT/dev_tui.py"
}

usage() {
    cat <<'USAGE'
Usage:
  ./dev.sh                         interactive TUI
  ./dev.sh start [all|db|api|web|app|tunnel]
  ./dev.sh stop [all|db|api|web|app|tunnel]
  ./dev.sh restart [all|db|api|web|app|tunnel]
  ./dev.sh status
  ./dev.sh logs [all|db|api|web|app|tunnel]
  ./dev.sh tunnel-help
USAGE
}

case "${1:-tui}" in
    tui) tui ;;
    start) with_manager_lock start_target "${2:-all}" ;;
    stop) with_manager_lock stop_target "${2:-all}" ;;
    restart) with_manager_lock restart_target "${2:-all}" ;;
    status) show_status ;;
    logs) show_logs "${2:-}" ;;
    tunnel-help) tunnel_help ;;
    help|-h|--help) usage ;;
    *) usage; exit 2 ;;
esac
