#!/usr/bin/env python3
"""Flicker-free development dashboard for Pondok Huda.

Process lifecycle stays in dev.sh. This module only gathers telemetry, renders
with ncurses' virtual-screen diffing, and dispatches CLI actions asynchronously.
"""

from __future__ import annotations

import curses
import json
import os
import re
import shlex
import signal
import subprocess
import time
import urllib.request
from dataclasses import dataclass
from datetime import datetime
from pathlib import Path


ROOT = Path(__file__).resolve().parent
RUNTIME = ROOT / ".dev-runtime"
LOG_DIR = RUNTIME / "logs"
DEV_SH = ROOT / "dev.sh"
ANSI_RE = re.compile(r"\x1b(?:\[[0-?]*[ -/]*[@-~]|\][^\x07]*(?:\x07|\x1b\\))")
SPARKS = "▁▂▃▄▅▆▇█"


@dataclass(frozen=True)
class Service:
    key: str
    label: str
    endpoint: str
    port: int | None


@dataclass
class Snapshot:
    service: Service
    state: str
    pid: int | None
    uptime: str = "—"
    cpu: float = 0.0
    rss_kb: int = 0


SERVICES = (
    Service("db", "MariaDB", "127.0.0.1:3306", 3306),
    Service("api", "PHP API", "http://localhost:8081/api", 8081),
    Service("web", "Laravel Web", "http://localhost:8000", 8000),
    Service("app", "React App", "http://localhost:5173", 5173),
    Service("tunnel", "HTTPS Tunnel", "waiting for URL", None),
)


def read_tunnel_config() -> dict[str, str]:
    config = {"PH_TUNNEL_PROVIDER": "cloudflare", "PH_TUNNEL_MODE": "quick"}
    path = ROOT / ".dev-tunnel.env"
    if not path.exists():
        return config
    try:
        raw_lines = path.read_text(errors="replace").splitlines()
    except OSError:
        return config
    for raw in raw_lines:
        line = raw.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        key, value = line.split("=", 1)
        if not key.startswith("PH_TUNNEL_"):
            continue
        try:
            parsed = shlex.split(value, comments=True)
            config[key] = parsed[0] if parsed else ""
        except ValueError:
            continue
    return config


def read_pid(service: str) -> int | None:
    try:
        fields = (RUNTIME / f"{service}.pid").read_text().split()
        if len(fields) != 2 or not all(value.isdigit() for value in fields):
            return None
        pid, recorded_start = map(int, fields)
        stat = Path(f"/proc/{pid}/stat").read_text()
        tail = stat.rsplit(") ", 1)[1].split()
        current_start = int(tail[19])
        return pid if current_start == recorded_start else None
    except (OSError, ValueError, IndexError):
        return None


def process_alive(pid: int | None) -> bool:
    if not pid:
        return False
    try:
        os.kill(pid, 0)
        tail = Path(f"/proc/{pid}/stat").read_text().rsplit(") ", 1)[1].split()
        return bool(tail) and tail[0] != "Z"
    except (OSError, IndexError):
        return False


def port_open(port: int) -> bool:
    wanted = f"{port:04X}"
    for table in (Path("/proc/net/tcp"), Path("/proc/net/tcp6")):
        try:
            rows = table.read_text().splitlines()[1:]
        except OSError:
            continue
        for row in rows:
            fields = row.split()
            if len(fields) > 3 and fields[1].rsplit(":", 1)[-1] == wanted and fields[3] == "0A":
                return True
    return False


def listening_socket_inodes(port: int) -> set[str]:
    wanted = f"{port:04X}"
    inodes: set[str] = set()
    for table in (Path("/proc/net/tcp"), Path("/proc/net/tcp6")):
        try:
            rows = table.read_text().splitlines()[1:]
        except OSError:
            continue
        for row in rows:
            fields = row.split()
            if len(fields) > 9 and fields[1].rsplit(":", 1)[-1] == wanted and fields[3] == "0A":
                inodes.add(fields[9])
    return inodes


def port_owned_by_process_group(port: int, root_pid: int | None) -> bool:
    if not root_pid:
        return False
    wanted = listening_socket_inodes(port)
    if not wanted:
        return False
    try:
        group = os.getpgid(root_pid)
    except OSError:
        return False
    for entry in Path("/proc").iterdir():
        if not entry.name.isdigit():
            continue
        try:
            pid = int(entry.name)
            if os.getpgid(pid) != group:
                continue
            for descriptor in (entry / "fd").iterdir():
                target = os.readlink(descriptor)
                if target.startswith("socket:[") and target[8:-1] in wanted:
                    return True
        except (OSError, ValueError):
            continue
    return False


def clean_line(value: str) -> str:
    value = ANSI_RE.sub("", value).replace("\r", "").replace("\t", "  ")
    return "".join(char if char.isprintable() else " " for char in value)


def tail_lines(path: Path, count: int, offset: int = 0) -> list[str]:
    if count <= 0:
        return []
    try:
        with path.open("rb") as handle:
            handle.seek(0, os.SEEK_END)
            size = handle.tell()
            handle.seek(max(0, size - 262_144))
            data = handle.read().decode("utf-8", errors="replace")
    except OSError:
        return []
    lines = [clean_line(line) for line in data.splitlines()]
    end = max(0, len(lines) - offset)
    start = max(0, end - count)
    return lines[start:end]


def format_kb(kb: int) -> str:
    if kb >= 1_048_576:
        return f"{kb / 1_048_576:.1f}G"
    if kb >= 1024:
        return f"{kb / 1024:.0f}M"
    return f"{kb}K"


def fit(text: str, width: int) -> str:
    if width <= 0:
        return ""
    if len(text) <= width:
        return text
    if width == 1:
        return "…"
    return text[: width - 1] + "…"


def sparkline(values: list[float], width: int) -> str:
    values = values[-width:]
    if not values:
        return " " * width
    ceiling = max(max(values), 1.0)
    graph = "".join(SPARKS[min(7, int(value / ceiling * 7))] for value in values)
    return graph.rjust(width)


class Dashboard:
    def __init__(self, stdscr: curses.window):
        self.screen = stdscr
        self.selected = 0
        self.snapshots: list[Snapshot] = []
        self.cpu_history: dict[str, list[float]] = {service.key: [] for service in SERVICES}
        self.load_history: list[float] = []
        self.config = read_tunnel_config()
        self.message = "ready"
        self.message_error = False
        self.paused = False
        self.help_open = False
        self.detail_open = False
        self.log_offset = 0
        self.action: subprocess.Popen[str] | None = None
        self.action_label = ""
        self.action_started = 0.0
        self.last_sample = 0.0
        self.spinner_index = 0
        self.service_rows: dict[int, int] = {}
        self.log_cache: dict[str, tuple[int, int, list[str]]] = {}
        self.colors: dict[str, int] = {}
        self.setup_terminal()

    def setup_terminal(self) -> None:
        self.screen.keypad(True)
        self.screen.timeout(100)
        try:
            curses.curs_set(0)
        except curses.error:
            pass
        try:
            curses.mousemask(curses.ALL_MOUSE_EVENTS)
        except curses.error:
            pass
        if curses.has_colors():
            curses.start_color()
            try:
                curses.use_default_colors()
            except curses.error:
                pass
            palette = {
                "cyan": 81 if curses.COLORS >= 256 else curses.COLOR_CYAN,
                "blue": 75 if curses.COLORS >= 256 else curses.COLOR_BLUE,
                "green": 114 if curses.COLORS >= 256 else curses.COLOR_GREEN,
                "yellow": 221 if curses.COLORS >= 256 else curses.COLOR_YELLOW,
                "red": 203 if curses.COLORS >= 256 else curses.COLOR_RED,
                "magenta": 176 if curses.COLORS >= 256 else curses.COLOR_MAGENTA,
                "muted": 250 if curses.COLORS >= 256 else curses.COLOR_WHITE,
                "text": 255 if curses.COLORS >= 256 else curses.COLOR_WHITE,
            }
            for index, (name, foreground) in enumerate(palette.items(), 1):
                try:
                    curses.init_pair(index, foreground, -1)
                    self.colors[name] = curses.color_pair(index)
                except curses.error:
                    self.colors[name] = 0
            try:
                selection_fg = 16 if curses.COLORS >= 256 else curses.COLOR_BLACK
                selection_bg = palette["cyan"]
                curses.init_pair(8, selection_fg, selection_bg)
                self.colors["selected"] = curses.color_pair(8) | curses.A_BOLD
            except curses.error:
                pass
        for name in ("cyan", "blue", "green", "yellow", "red", "magenta", "muted", "text", "selected"):
            self.colors.setdefault(name, 0)
        self.colors["selected"] |= curses.A_REVERSE | curses.A_BOLD

    def logs(self, service: str, count: int, offset: int = 0) -> list[str]:
        path = LOG_DIR / f"{service}.log"
        try:
            stat = path.stat()
            signature = (stat.st_mtime_ns, stat.st_size)
        except OSError:
            signature = (0, 0)
        cached = self.log_cache.get(service)
        if cached is None or cached[:2] != signature:
            lines = tail_lines(path, 2000)
            self.log_cache[service] = (signature[0], signature[1], lines)
        else:
            lines = cached[2]
        end = max(0, len(lines) - offset)
        return lines[max(0, end - count) : end]

    @property
    def tunnel_url(self) -> str:
        configured = self.config.get("PH_TUNNEL_URL", "")
        if configured:
            return configured
        lines = tail_lines(LOG_DIR / "tunnel.log", 80)
        for line in reversed(lines):
            match = re.search(r"https://[a-z0-9-]+\.trycloudflare\.com", line)
            if match:
                return match.group(0)
        return "waiting for URL"

    @property
    def tunnel_mode(self) -> str:
        if self.config.get("PH_TUNNEL_PROVIDER") == "ngrok":
            return "ngrok · static"
        mode = self.config.get("PH_TUNNEL_MODE", "quick")
        return "cloudflare · temporary" if mode == "quick" else f"cloudflare · {mode}"

    def tunnel_healthy(self, pid: int | None) -> bool:
        if not process_alive(pid) or not port_open(5173):
            return False
        if self.config.get("PH_TUNNEL_PROVIDER") != "ngrok":
            return self.tunnel_url != "waiting for URL"
        try:
            with urllib.request.urlopen("http://127.0.0.1:4040/api/tunnels", timeout=0.15) as response:
                payload = json.loads(response.read())
            expected = self.tunnel_url.rstrip("/")
            return any(str(item.get("public_url", "")).rstrip("/") == expected for item in payload.get("tunnels", []))
        except (OSError, ValueError, json.JSONDecodeError):
            return False

    def process_metrics(self, pids: list[int]) -> dict[int, tuple[str, float, int]]:
        if not pids:
            return {}
        try:
            result = subprocess.run(
                ["ps", "-o", "pid=,etime=,%cpu=,rss=", "-p", ",".join(map(str, pids))],
                text=True,
                capture_output=True,
                timeout=0.4,
                check=False,
            )
        except (OSError, subprocess.TimeoutExpired):
            return {}
        metrics: dict[int, tuple[str, float, int]] = {}
        for line in result.stdout.splitlines():
            parts = line.split()
            if len(parts) != 4:
                continue
            try:
                metrics[int(parts[0])] = (parts[1], float(parts[2]), int(parts[3]))
            except ValueError:
                continue
        return metrics

    def collect(self) -> None:
        self.config = read_tunnel_config()
        pids = {service.key: read_pid(service.key) for service in SERVICES}
        active_pids = [pid for pid in pids.values() if process_alive(pid)]
        metrics = self.process_metrics([pid for pid in active_pids if pid is not None])
        snapshots: list[Snapshot] = []
        for service in SERVICES:
            pid = pids[service.key]
            managed = process_alive(pid)
            if service.key == "tunnel":
                healthy = self.tunnel_healthy(pid)
            elif managed and service.port:
                healthy = port_owned_by_process_group(service.port, pid)
            else:
                healthy = bool(service.port and port_open(service.port))
            if managed:
                state = "online" if healthy else "degraded"
            elif healthy:
                state = "external"
                pid = None
            else:
                state = "offline"
                pid = None
            snapshot = Snapshot(service, state, pid)
            if pid in metrics:
                snapshot.uptime, snapshot.cpu, snapshot.rss_kb = metrics[pid]
            history = self.cpu_history[service.key]
            history.append(snapshot.cpu)
            del history[:-32]
            snapshots.append(snapshot)
        self.snapshots = snapshots
        try:
            load = os.getloadavg()[0]
        except OSError:
            load = 0.0
        self.load_history.append(load)
        del self.load_history[:-32]
        self.last_sample = time.monotonic()

    def memory_summary(self) -> tuple[int, int, int]:
        values: dict[str, int] = {}
        try:
            for line in Path("/proc/meminfo").read_text().splitlines():
                key, raw = line.split(":", 1)
                values[key] = int(raw.strip().split()[0])
        except (OSError, ValueError, IndexError):
            return 0, 0, 0
        total = values.get("MemTotal", 0)
        used = max(0, total - values.get("MemAvailable", 0))
        return used, total, int(used * 100 / total) if total else 0

    def add(self, y: int, x: int, text: str, attr: int = 0, width: int | None = None) -> None:
        height, columns = self.screen.getmaxyx()
        if y < 0 or y >= height or x < 0 or x >= columns:
            return
        available = columns - x
        if y == height - 1:
            available -= 1
        limit = max(0, min(available, width if width is not None else available))
        if not limit:
            return
        try:
            self.screen.addnstr(y, x, text, limit, attr)
        except curses.error:
            pass

    def hline(self, y: int, x: int, width: int, attr: int = 0) -> None:
        self.add(y, x, "─" * max(0, width), attr, width)

    def box(self, y: int, x: int, height: int, width: int, title: str, selected: bool = False) -> None:
        if height < 2 or width < 4:
            return
        border = self.colors["cyan"] | (curses.A_BOLD if selected else 0)
        self.add(y, x, "┌" + "─" * (width - 2) + "┐", border, width)
        for row in range(y + 1, y + height - 1):
            self.add(row, x, "│", border)
            self.add(row, x + width - 1, "│", border)
        self.add(y + height - 1, x, "└" + "─" * (width - 2) + "┘", border, width)
        self.add(y, x + 2, f" {fit(title, width - 6)} ", border | curses.A_BOLD, width - 4)

    def state_attr(self, state: str) -> int:
        return {
            "online": self.colors["green"],
            "degraded": self.colors["yellow"],
            "external": self.colors["magenta"],
            "offline": self.colors["muted"] | curses.A_DIM,
        }[state]

    def draw_header(self, width: int) -> None:
        now = datetime.now().strftime("%H:%M:%S")
        title = " PONDOK HUDA  /  DEV CONTROL "
        self.add(0, 0, title, self.colors["cyan"] | curses.A_BOLD)
        self.add(0, max(len(title) + 1, width - len(now) - 1), now, self.colors["muted"])
        online = sum(snapshot.state == "online" for snapshot in self.snapshots)
        used, total, percent = self.memory_summary()
        try:
            load = os.getloadavg()[0]
        except OSError:
            load = 0.0
        summary = (
            f" ● {online}/{len(SERVICES)} online   "
            f"load {load:.2f} {sparkline(self.load_history, 10)}   "
            f"ram {format_kb(used)}/{format_kb(total)} {percent}%   "
            f"{self.tunnel_mode}"
        )
        self.add(1, 1, fit(summary, width - 2), self.colors["green"] if online == len(SERVICES) else self.colors["yellow"])
        selected = self.snapshots[self.selected] if self.snapshots else None
        endpoint = self.tunnel_url if selected and selected.service.key == "tunnel" else (selected.service.endpoint if selected else "")
        self.add(2, 1, fit(f"selected  {selected.service.label if selected else '—'}  ·  {endpoint}", width - 2), self.colors["muted"] | curses.A_DIM)

    def draw_services(self, y: int, width: int) -> int:
        height = 8
        self.box(y, 0, height, width, "services")
        wide = width >= 108
        trend = width >= 126
        header_attr = self.colors["muted"] | curses.A_DIM
        if wide:
            self.add(y + 1, 2, "SERVICE         STATE       PID     UPTIME     CPU     MEM", header_attr)
            endpoint_x = 67
        else:
            self.add(y + 1, 2, "SERVICE         STATE       PID     UPTIME", header_attr)
            endpoint_x = 48
        if trend:
            self.add(y + 1, 66, "TREND", header_attr)
            endpoint_x = 80
        self.add(y + 1, endpoint_x, "ENDPOINT", header_attr, width - endpoint_x - 2)
        self.service_rows.clear()
        for index, snapshot in enumerate(self.snapshots):
            row_y = y + 2 + index
            self.service_rows[row_y] = index
            selected = index == self.selected
            row_attr = self.colors["selected"] if selected else 0
            if selected:
                self.add(row_y, 1, " " * (width - 2), row_attr, width - 2)
            self.add(row_y, 2, f"{snapshot.service.label:<15}", row_attr or curses.A_BOLD, 15)
            state_text = f"● {snapshot.state.upper():<8}"
            self.add(row_y, 18, state_text, row_attr or self.state_attr(snapshot.state), 10)
            self.add(row_y, 30, f"{snapshot.pid or '—':<7}", row_attr, 7)
            self.add(row_y, 38, f"{snapshot.uptime:<10}", row_attr, 10)
            if wide:
                self.add(row_y, 49, f"{snapshot.cpu:>5.1f}%", row_attr, 6)
                self.add(row_y, 57, f"{format_kb(snapshot.rss_kb):>7}", row_attr, 7)
            if trend:
                self.add(row_y, 66, sparkline(self.cpu_history[snapshot.service.key], 11), row_attr or self.colors["blue"], 11)
            endpoint = self.tunnel_url if snapshot.service.key == "tunnel" else snapshot.service.endpoint
            self.add(row_y, endpoint_x, fit(endpoint, width - endpoint_x - 2), row_attr or self.colors["muted"], width - endpoint_x - 2)
        return y + height

    def draw_log_card(self, index: int, y: int, x: int, height: int, width: int) -> None:
        snapshot = self.snapshots[index]
        title = f"{snapshot.service.label}  ·  {snapshot.state}"
        self.box(y, x, height, width, title, index == self.selected)
        lines = self.logs(snapshot.service.key, max(1, height - 2))
        if not lines:
            lines = ["no log output"]
        lines = lines[-max(1, height - 2) :]
        for offset, line in enumerate(lines):
            self.add(y + 1 + offset, x + 2, fit(line, width - 4), self.colors["text"], width - 4)

    def draw_log_grid(self, y: int, height: int, width: int) -> None:
        gap = 1
        column_width = (width - gap) // 2
        row_heights = [height // 3] * 3
        for index in range(height % 3):
            row_heights[index] += 1
        current_y = y
        positions: list[tuple[int, int, int, int]] = []
        for row in range(2):
            positions.append((current_y, 0, row_heights[row], column_width))
            positions.append((current_y, column_width + gap, row_heights[row], width - column_width - gap))
            current_y += row_heights[row]
        positions.append((current_y, 0, row_heights[2], width))
        for index, position in enumerate(positions):
            self.draw_log_card(index, *position)

    def draw_compact_logs(self, y: int, height: int, width: int) -> None:
        self.box(y, 0, height, width, "component logs")
        interior = max(0, height - 2)
        if interior < len(SERVICES):
            return
        allocations = [1] * len(SERVICES)
        allocations[self.selected] += interior - len(SERVICES)
        row = y + 1
        for index, (snapshot, allocation) in enumerate(zip(self.snapshots, allocations)):
            lines = self.logs(snapshot.service.key, allocation)
            if not lines:
                lines = ["no log output"]
            if allocation == 1:
                text = f"{snapshot.service.label:<14} │ {lines[-1]}"
                attr = self.colors["selected"] if index == self.selected else self.state_attr(snapshot.state)
                self.add(row, 1, " " * (width - 2), attr, width - 2)
                self.add(row, 2, fit(text, width - 4), attr, width - 4)
                row += 1
                continue
            attr = self.colors["cyan"] | curses.A_BOLD
            self.add(row, 2, fit(f"{snapshot.service.label}  ·  {snapshot.state}", width - 4), attr, width - 4)
            row += 1
            for line in lines[-(allocation - 1) :]:
                self.add(row, 4, fit(line, width - 6), self.colors["text"], width - 6)
                row += 1

    def draw_footer(self, height: int, width: int) -> None:
        spinner = "◐◓◑◒"[self.spinner_index % 4]
        if self.action and self.action.poll() is None:
            status = f"{spinner} {self.action_label}"
            self.spinner_index += 1
            attr = self.colors["yellow"]
        elif self.paused:
            status = "Ⅱ paused · press p to resume"
            attr = self.colors["yellow"] | curses.A_BOLD
        else:
            status = self.message
            attr = self.colors["red"] if self.message_error else self.colors["muted"]
        self.add(height - 2, 1, fit(status, width - 2), attr, width - 2)
        keys = "↑↓ select  enter toggle  s start  x stop  r restart  a core  z all  t tunnel  l log  p pause  ? help  q quit"
        self.add(height - 1, 0, " " * max(0, width - 1), self.colors["blue"] | curses.A_REVERSE, width - 1)
        self.add(height - 1, 1, fit(keys, width - 2), self.colors["blue"] | curses.A_REVERSE | curses.A_BOLD, width - 2)

    def draw_detail(self, height: int, width: int) -> None:
        snapshot = self.snapshots[self.selected]
        panel_y, panel_x = 2, 2
        panel_h, panel_w = height - 4, width - 4
        self.box(panel_y, panel_x, panel_h, panel_w, f"full log · {snapshot.service.label}", True)
        count = max(1, panel_h - 3)
        lines = self.logs(snapshot.service.key, count, self.log_offset)
        if not lines:
            lines = ["no log output"]
        start_y = panel_y + 1
        for index, line in enumerate(lines[-count:]):
            self.add(start_y + index, panel_x + 2, fit(line, panel_w - 4), self.colors["text"], panel_w - 4)
        mode = "following" if self.log_offset == 0 else f"-{self.log_offset} lines"
        hint = f"{mode}  ·  ↑↓/PgUp/PgDn scroll  ·  End follow  ·  l/Esc close"
        self.add(panel_y + panel_h - 2, panel_x + 2, fit(hint, panel_w - 4), self.colors["cyan"], panel_w - 4)

    def draw_help(self, height: int, width: int) -> None:
        lines = [
            ("NAVIGATION", "↑/↓ or j/k select · mouse click supported"),
            ("SERVICE", "Enter toggle · s start · x stop · r restart"),
            ("STACK", "a start core · z stop all · t toggle HTTPS tunnel"),
            ("LOGS", "l full log · c clear selected · PgUp/PgDn scroll"),
            ("DISPLAY", "p pause telemetry · ?/Esc close help · q quit"),
        ]
        panel_w = min(76, width - 4)
        panel_h = len(lines) + 5
        panel_y = max(1, (height - panel_h) // 2)
        panel_x = max(2, (width - panel_w) // 2)
        for row in range(panel_y, panel_y + panel_h):
            self.add(row, panel_x, " " * panel_w, curses.A_REVERSE, panel_w)
        self.box(panel_y, panel_x, panel_h, panel_w, "keyboard help", True)
        self.add(panel_y + 1, panel_x + 2, "Pondok Huda development control", self.colors["cyan"] | curses.A_BOLD)
        for index, (heading, text) in enumerate(lines):
            row = panel_y + 3 + index
            self.add(row, panel_x + 3, f"{heading:<11}", self.colors["yellow"] | curses.A_BOLD, 11)
            self.add(row, panel_x + 15, fit(text, panel_w - 18), self.colors["muted"], panel_w - 18)

    def render(self) -> None:
        height, width = self.screen.getmaxyx()
        self.screen.erase()
        if height < 20 or width < 72:
            message = f"terminal too small: {width}×{height} · minimum 72×20"
            self.add(max(0, height // 2), max(0, (width - len(message)) // 2), message, self.colors["red"] | curses.A_BOLD)
            self.screen.noutrefresh()
            curses.doupdate()
            return
        self.draw_header(width)
        log_y = self.draw_services(3, width)
        log_height = height - log_y - 2
        if width >= 110 and log_height >= 12:
            self.draw_log_grid(log_y, log_height, width)
        else:
            self.draw_compact_logs(log_y, log_height, width)
        self.draw_footer(height, width)
        if self.detail_open:
            self.draw_detail(height, width)
        if self.help_open:
            self.draw_help(height, width)
        self.screen.noutrefresh()
        curses.doupdate()

    def start_action(self, command: str, target: str) -> None:
        if self.action and self.action.poll() is None:
            self.message = "an action is already running"
            self.message_error = True
            return
        self.action_label = f"{command} {target}"
        self.message_error = False
        try:
            self.action = subprocess.Popen(
                [str(DEV_SH), command, target],
                cwd=ROOT,
                text=True,
                stdout=subprocess.PIPE,
                stderr=subprocess.PIPE,
                start_new_session=True,
            )
            self.action_started = time.monotonic()
        except OSError as error:
            self.action = None
            self.message = f"{self.action_label} failed · {error}"
            self.message_error = True

    def terminate_action(self) -> None:
        if not self.action or self.action.poll() is not None:
            return
        try:
            os.killpg(self.action.pid, signal.SIGTERM)
            self.action.wait(timeout=2)
        except (OSError, subprocess.TimeoutExpired):
            try:
                os.killpg(self.action.pid, signal.SIGKILL)
            except OSError:
                pass

    def poll_action(self) -> None:
        if not self.action:
            return
        if self.action.poll() is None:
            if time.monotonic() - self.action_started <= 90:
                return
            self.terminate_action()
            self.message = f"{self.action_label} timed out"
            self.message_error = True
            self.action = None
            return
        try:
            stdout, stderr = self.action.communicate(timeout=0.2)
            output = [clean_line(line).strip() for line in stdout.splitlines() if clean_line(line).strip()]
            if self.action.returncode == 0:
                self.message = f"{self.action_label} complete" + (f" · {output[-1]}" if output else "")
                self.message_error = False
            else:
                error = stderr.strip() or (output[-1] if output else "unknown error")
                self.message = f"{self.action_label} failed · {clean_line(error)}"
                self.message_error = True
        except (OSError, subprocess.TimeoutExpired) as error:
            self.message = f"{self.action_label} failed · {error}"
            self.message_error = True
        self.action = None
        self.last_sample = 0.0

    def clear_log(self) -> None:
        service = SERVICES[self.selected]
        try:
            (LOG_DIR / f"{service.key}.log").write_text("")
            self.message = f"{service.label} log cleared"
            self.message_error = False
        except OSError as error:
            self.message = f"cannot clear log · {error}"
            self.message_error = True

    def handle_mouse(self) -> None:
        try:
            _, mouse_x, mouse_y, _, state = curses.getmouse()
        except curses.error:
            return
        del mouse_x
        if state & (curses.BUTTON1_CLICKED | curses.BUTTON1_PRESSED) and mouse_y in self.service_rows:
            self.selected = self.service_rows[mouse_y]
            self.log_offset = 0

    def handle_key(self, key: int) -> bool:
        height, width = self.screen.getmaxyx()
        if height < 20 or width < 72:
            return key not in (ord("q"), ord("Q"))
        if key == curses.KEY_MOUSE:
            self.handle_mouse()
            return True
        if key in (ord("q"), ord("Q")):
            if self.action and self.action.poll() is None:
                self.message = "wait for the active action before quitting"
                self.message_error = True
                return True
            return False
        if self.help_open:
            if key in (27, ord("?"), curses.KEY_F1):
                self.help_open = False
            return True
        if self.detail_open:
            if key in (27, ord("l"), ord("L")):
                self.detail_open = False
            elif key in (curses.KEY_UP, ord("k")):
                self.log_offset += 1
            elif key in (curses.KEY_DOWN, ord("j")):
                self.log_offset = max(0, self.log_offset - 1)
            elif key == curses.KEY_PPAGE:
                self.log_offset += max(5, self.screen.getmaxyx()[0] - 10)
            elif key == curses.KEY_NPAGE:
                self.log_offset = max(0, self.log_offset - max(5, self.screen.getmaxyx()[0] - 10))
            elif key in (curses.KEY_END, ord("f"), ord("F")):
                self.log_offset = 0
            return True
        if key in (curses.KEY_UP, ord("k"), ord("K")):
            self.selected = (self.selected - 1) % len(SERVICES)
        elif key in (curses.KEY_DOWN, ord("j"), ord("J")):
            self.selected = (self.selected + 1) % len(SERVICES)
        elif key in (curses.KEY_LEFT,):
            self.selected = (self.selected - 1) % len(SERVICES)
        elif key in (curses.KEY_RIGHT,):
            self.selected = (self.selected + 1) % len(SERVICES)
        elif key in (10, 13, ord(" ")):
            snapshot = self.snapshots[self.selected]
            self.start_action("stop" if snapshot.pid else "start", snapshot.service.key)
        elif key in (ord("s"), ord("S")):
            self.start_action("start", SERVICES[self.selected].key)
        elif key in (ord("x"), ord("X")):
            self.start_action("stop", SERVICES[self.selected].key)
        elif key in (ord("r"), ord("R")):
            self.start_action("restart", SERVICES[self.selected].key)
        elif key in (ord("a"), ord("A")):
            self.start_action("start", "all")
        elif key in (ord("z"), ord("Z")):
            self.start_action("stop", "all")
        elif key in (ord("t"), ord("T")):
            self.selected = 4
            snapshot = self.snapshots[4]
            self.start_action("stop" if snapshot.pid else "start", "tunnel")
        elif key in (ord("c"), ord("C")):
            self.clear_log()
        elif key in (ord("l"), ord("L")):
            self.detail_open = True
            self.log_offset = 0
        elif key in (ord("p"), ord("P")):
            self.paused = not self.paused
            self.message = "telemetry paused" if self.paused else "telemetry resumed"
        elif key in (ord("?"), curses.KEY_F1):
            self.help_open = True
        return True

    def run(self) -> None:
        self.collect()
        running = True
        try:
            while running:
                self.poll_action()
                if not self.paused and time.monotonic() - self.last_sample >= 0.75:
                    self.collect()
                self.render()
                key = self.screen.getch()
                if key != -1:
                    running = self.handle_key(key)
        finally:
            self.terminate_action()


def main(stdscr: curses.window) -> None:
    Dashboard(stdscr).run()


if __name__ == "__main__":
    curses.wrapper(main)
