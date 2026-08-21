#!/usr/bin/env bash
# Pondok Huda local development stack manager.
# Interactive with no arguments; scriptable with start/stop/restart/status/logs.

set -uo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
RUNTIME_DIR="$ROOT/.dev-runtime"
LOG_DIR="$RUNTIME_DIR/logs"
PHP="/home/leafy/.pondok/php/bin/php"
MYSQL_DIR="/home/leafy/.pondok/mysql"
MYSQL_SOCKET="/home/leafy/.pondok/mysql.sock"
MYSQL_PID="/home/leafy/.pondok/mysql.pid"
CLOUDFLARED="/home/leafy/.local/bin/cloudflared"

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
    local file
    file="$(pid_file "$1")"
    [[ -f "$file" ]] && tr -dc '0-9' < "$file"
}

managed_running() {
    local pid
    pid="$(read_pid "$1")"
    [[ -n "$pid" ]] && kill -0 "$pid" 2>/dev/null
}

port_open() {
    timeout 1 bash -c "</dev/tcp/127.0.0.1/$1" >/dev/null 2>&1
}

service_responding() {
    case "$1" in
        db) mariadb-admin --socket="$MYSQL_SOCKET" -u root ping --silent >/dev/null 2>&1 ;;
        api) port_open 8081 ;;
        web) port_open 8000 ;;
        app) port_open 5173 ;;
        tunnel) managed_running tunnel ;;
    esac
}

tunnel_url() {
    local log url=''
    log="$(log_file tunnel)"
    if [[ -f "$log" ]]; then
        url="$(grep -Eo 'https://[a-z0-9-]+\.trycloudflare\.com' "$log" 2>/dev/null | tail -n 1 || true)"
    fi
    printf '%s' "${url:-waiting for URL}"
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
    local log pid
    log="$(log_file "$service")"
    : > "$log"
    nohup setsid "$@" >> "$log" 2>&1 < /dev/null &
    pid=$!
    printf '%s\n' "$pid" > "$(pid_file "$service")"
}

wait_ready() {
    local service="$1" tries="${2:-30}" i
    for ((i=0; i<tries; i++)); do
        if service_responding "$service"; then return 0; fi
        if ! managed_running "$service"; then return 1; fi
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
            if [[ ! -x "$CLOUDFLARED" ]]; then
                printf '\r  %s×%s %-14s cloudflared not installed\n' "$RED" "$RESET" "$label"
                return 1
            fi
            launch tunnel "$CLOUDFLARED" tunnel --url http://127.0.0.1:5173 --no-autoupdate
            ;;
    esac

    if wait_ready "$service" "$([[ "$service" == tunnel ]] && printf 8 || printf 40)"; then
        printf '\r  %s●%s %-14s %s\n' "$GREEN" "$RESET" "$label" "$(service_url "$service")"
    else
        printf '\r  %s×%s %-14s failed, see %s\n' "$RED" "$RESET" "$label" "$(log_file "$service")"
        rm -f "$(pid_file "$service")"
        return 1
    fi
}

stop_one() {
    local service="$1" label pid i
    label="$(service_label "$service")"
    cleanup_stale_pid "$service"
    if ! managed_running "$service"; then
        printf '  %s○%s %-14s already stopped\n' "$DIM" "$RESET" "$label"
        return 0
    fi
    pid="$(read_pid "$service")"
    printf '  %s…%s stopping %-14s' "$YELLOW" "$RESET" "$label"

    if [[ "$service" == db ]] && command -v mariadb-admin >/dev/null 2>&1; then
        mariadb-admin --socket="$MYSQL_SOCKET" -u root shutdown >/dev/null 2>&1 || true
    else
        kill -TERM -- "-$pid" 2>/dev/null || kill -TERM "$pid" 2>/dev/null || true
    fi

    for ((i=0; i<40; i++)); do
        managed_running "$service" || break
        sleep .25
    done
    if managed_running "$service"; then
        kill -KILL -- "-$pid" 2>/dev/null || kill -KILL "$pid" 2>/dev/null || true
    fi
    rm -f "$(pid_file "$service")"
    printf '\r  %s○%s %-14s stopped\n' "$DIM" "$RESET" "$label"
}

start_all() {
    printf '\n%sStarting Pondok Huda development stack%s\n' "$BOLD" "$RESET"
    local service
    for service in "${CORE_SERVICES[@]}"; do start_one "$service" || true; done
}

stop_all() {
    printf '\n%sStopping Pondok Huda development stack%s\n' "$BOLD" "$RESET"
    local order=(tunnel app web api db) service
    for service in "${order[@]}"; do stop_one "$service"; done
}

status_line() {
    local service="$1" label state detail
    label="$(service_label "$service")"
    cleanup_stale_pid "$service"
    if managed_running "$service"; then
        state="${GREEN}RUNNING${RESET}"
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
    printf '\n%sPondok Huda development stack%s\n\n' "$BOLD" "$RESET"
    local service
    for service in "${SERVICES[@]}"; do status_line "$service"; done
    printf '\n  logs: %s\n' "$LOG_DIR"
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
    stop_target "$target"
    start_target "$target"
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

pause() {
    [[ -t 0 ]] || return 0
    printf '\n%sPress any key to continue.%s' "$DIM" "$RESET"
    read -rsn1
}

tui() {
    while true; do
        clear
        printf '%sPONDOK HUDA%s  %sdevelopment control%s\n' "$BOLD" "$RESET" "$DIM" "$RESET"
        show_status
        printf '\n  %s1%s  Start core stack      %s4%s  Show logs\n' "$CYAN" "$RESET" "$CYAN" "$RESET"
        printf '  %s2%s  Stop everything      %s5%s  Toggle HTTPS tunnel\n' "$CYAN" "$RESET" "$CYAN" "$RESET"
        printf '  %s3%s  Restart core stack    %sr%s  Refresh\n' "$CYAN" "$RESET" "$CYAN" "$RESET"
        printf '  %sq%s  Quit\n\n' "$CYAN" "$RESET"
        printf 'select: '
        read -rsn1 choice
        printf '\n'
        case "$choice" in
            1) start_all; pause ;;
            2) stop_all; pause ;;
            3) stop_all; start_all; pause ;;
            4) show_logs; pause ;;
            5) if managed_running tunnel; then stop_one tunnel; else start_one tunnel; fi; pause ;;
            r|R) ;;
            q|Q) clear; return 0 ;;
        esac
    done
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
USAGE
}

case "${1:-tui}" in
    tui) tui ;;
    start) start_target "${2:-all}" ;;
    stop) stop_target "${2:-all}" ;;
    restart) restart_target "${2:-all}" ;;
    status) show_status ;;
    logs) show_logs "${2:-}" ;;
    help|-h|--help) usage ;;
    *) usage; exit 2 ;;
esac
