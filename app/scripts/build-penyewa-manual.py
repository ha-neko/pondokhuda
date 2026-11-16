#!/usr/bin/env python3
"""Build the penyewa (tenant) app manual PDF.

Renders the app screens as static HTML using the app's own built Tailwind
CSS (app/dist/assets/index-*.css), screenshots them with headless Firefox,
composes A4 manual pages, then assembles everything into a multi-page PDF
at web/public/user-manual/penyewa.pdf (next to admin.pdf / owner.pdf).

Usage:
    python3 app/scripts/build-penyewa-manual.py

Requires: Pillow, Mozilla Firefox (headless screenshot), a prior
`npm run build` inside app/ (for the compiled CSS).
"""
import glob
import os
import shutil
import subprocess
import sys

from PIL import Image

ROOT = os.path.dirname(os.path.dirname(os.path.dirname(os.path.abspath(__file__))))  # repo root
OUT = os.environ.get('MANUAL_BUILD_DIR', '/tmp/opencode/ph-manual')
DEST = os.path.join(ROOT, 'web', 'public', 'user-manual', 'penyewa.pdf')
FIREFOX = shutil.which('firefox')

# ---------------------------------------------------------------- icons ----
ICONS = {
    'home': '<path d="M12 3l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1V9.5L12 3z" />',
    'wallet': (
        '<path d="M4 6a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6zm3 2v2h10V8H7zm0 5v2h4v-2H7zm8 0a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z" />'
    ),
    'notice': (
        '<path d="M6.5 3.75h11a2.75 2.75 0 0 1 2.75 2.75v11a2.75 2.75 0 0 1-2.75 2.75h-11a2.75 2.75 0 0 1-2.75-2.75v-11A2.75 2.75 0 0 1 6.5 3.75Zm1.25 5h8.5m-8.5 3.5h8.5m-8.5 3.5h5.5" fill="none" stroke="currentColor" strokeWidth="1.75" strokeLinecap="round" />'
    ),
    'comment': '<path d="M4 5h16v11H9l-5 4V5zm3 4v2h8V9H7zm0 3v2h5v-2H7z" />',
    'person': (
        '<path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm0 2c-4.4 0-8 2.2-8 5v1h16v-1c0-2.8-3.6-5-8-5z" />'
    ),
    'lock': (
        '<path d="M7 10V7a5 5 0 0 1 10 0v3h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h1zm2 0h6V7a3 3 0 0 0-6 0v3zm3 5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3z" />'
    ),
    'logout': (
        '<path d="M10 5H6a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h4" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" />'
        '<path d="M14 8l4 4-4 4m4-4H9" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />'
    ),
    'chevronRight': '<path d="M9.3 6.7L14.6 12l-5.3 5.3 1.4 1.4 6.7-6.7-6.7-6.7-1.4 1.4z" />',
    'chevronLeft': '<path d="M14.7 6.7L9.4 12l5.3 5.3-1.4 1.4-6.7-6.7 6.7-6.7 1.4 1.4z" />',
    'camera': (
        '<path d="M9 3l-1.5 2H4a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-3.5L15 3H9zm3 14a5 5 0 1 1 0-10 5 5 0 0 1 0 10zm0-2a3 3 0 1 0 0-6 3 3 0 0 0 0 6z" />'
    ),
    'send': '<path d="M3 20v-6l8-2-8-2V4l18 8-18 8z" />',
    'check': '<path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z" />',
    'close': '<path d="M6.4 4.7L12 10.3l5.6-5.6 1.4 1.4L13.4 12l5.6 5.6-1.4 1.4L12 13.4l-5.6 5.6-1.4-1.4 5.6-5.6-5.6-5.6 1.4-1.4z" />',
    'phone': (
        '<path d="M6.6 3h3l1.4 4-2 1.5a12 12 0 0 0 5.5 5.5l1.5-2 4 1.4v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.6 5.2 2 2 0 0 1 6.6 3z" />'
    ),
    'refresh': (
        '<path d="M12 5a7 7 0 1 0 6.9 5h2.1a9 9 0 1 1-9-9c2.5 0 4.8 1 6.4 2.7L21 2v6h-6l2.2-2.2A7 7 0 0 0 12 5z" />'
    ),
    'calendar': (
        '<path d="M7 3v2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2V3h-2v2H9V3H7zM5 9h14v10H5V9zm3 3v2h2v-2H8zm4 0v2h2v-2h-2zm4 0v2h2v-2h-2zm-8 4v2h2v-2H8zm4 0v2h2v-2h-2z" />'
    ),
    'edit': '<path d="M4 20h4l10.5-10.5-4-4L4 16v4zm4.5-1.5H6v-2.5l8.5-8.5 2.5 2.5-8.5 8.5z" />',
    'info': '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 5h-2v2h2V7zm0 4h-2v6h2v-6z" />',
    'moon': '<path d="M12 3a9 9 0 1 0 9 9c0-.5 0-1-.1-1.4A7 7 0 0 1 12 3z" />',
    'monitor': '<path d="M3 5h18v12H3V5zm2 2v8h14V7H5zm4 12h6v2H9v-2z" />',
    'sun': (
        '<path d="M12 5a7 7 0 1 0 0 14 7 7 0 0 0 0-14zm0-3l1.5 2h-3L12 2zm8 9l2 1.5-2 1.5V11zM4 11v3l-2-1.5L4 11zm13.7-5.7l.6 2.8-2.1-.1 1.5-2.7zM6.6 9.3l-2-1.6 2-3 .8 3.5-2.2 1.1zm10.2 5.6h2.9l-1 2.4-2.4-1 .5-1.4zM7.4 14.9l-2.3 1.2 1 2.5 3.5-1.6-2.2-2.1z" />'
    ),
}


def icon(name, size=24, cls=''):
    cls_attr = f' class="{cls}"' if cls else ''
    return (
        f'<svg viewBox="0 0 24 24" width="{size}" height="{size}" '
        f'fill="currentColor" aria-hidden="true"{cls_attr}>{ICONS[name]}</svg>'
    )


# ---------------------------------------------------------------- helpers ----
def field(label, inner, value, hint=None, error=None, extra_input=''):
    desc = ''
    if hint:
        desc = f'<p class="px-0.5 text-xs text-on-surface-variant">{hint}</p>'
    return (
        f'<div class="flex flex-col gap-2">'
        f'<label class="px-0.5 text-[13px] font-semibold text-on-surface-variant">{label}</label>'
        f'<div class="relative">{inner}'
        f'<input readonly value="{value}" class="min-h-12 w-full rounded-[.9rem] border bg-surface-lowest px-4 text-sm text-on-surface outline-none placeholder:text-on-surface-variant/55 border-outline-variant {extra_input}">'
        f'</div>{desc}</div>'
    )


def icon_field(label, icon_name, value, hint=None, extra_input=''):
    inner = (
        f'<span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-on-surface-variant">'
        f'{icon(icon_name, 20)}</span>'
    )
    return field(label, inner, value, hint, extra_input='pl-12 ' + extra_input)


def page_header(title, sub='', right='', back=False):
    backbtn = (
        f'<span class="-ml-2 inline-flex size-10 shrink-0 items-center justify-center rounded-full text-on-surface-variant">{icon("chevronLeft", 20)}</span>'
        if back else ''
    )
    subhtml = f'<p class="truncate text-xs text-on-surface-variant">{sub}</p>' if sub else ''
    return (
        f'<header class="sticky top-0 z-20 border-b border-outline-variant/40 bg-surface/90 px-4 backdrop-blur-xl">'
        f'<div class="mx-auto flex min-h-15 max-w-[608px] items-center gap-2">{backbtn}'
        f'<div class="min-w-0 flex-1 py-2"><h1 class="truncate text-[17px] font-semibold tracking-[-.01em] text-on-surface">{title}</h1>{subhtml}</div>'
        f'{right}</div></header>'
    )


def bottom_nav(active):
    tabs = [('Beranda', 'home'), ('Bayar', 'wallet'), ('Info', 'notice'),
            ('Keluhan', 'comment'), ('Saya', 'person')]
    items = ''
    for label, ic in tabs:
        is_active = label == active
        chip_cls = 'bg-primary-container text-on-primary-container' if is_active else ''
        text_cls = 'text-primary' if is_active else 'text-on-surface-variant'
        items += (
            f'<span class="group relative flex min-w-0 flex-col items-center gap-0.5 rounded-[.9rem] py-1.5 text-[11px] font-medium {text_cls}">'
            f'<span class="flex h-7 w-11 items-center justify-center rounded-full {chip_cls}">{icon(ic, 20)}</span>'
            f'<span>{label}</span></span>'
        )
    return (
        f'<nav aria-label="Navigasi utama" class="pointer-events-none fixed inset-x-0 bottom-0 z-30 px-3 pb-2.5">'
        f'<div class="pointer-events-auto mx-auto grid max-w-[616px] grid-cols-5 items-center rounded-[1.15rem] border border-outline-variant/45 bg-surface-lowest/95 px-1 py-1 shadow-[0_10px_32px_rgba(8,39,37,.15)] backdrop-blur-xl">{items}</div></nav>'
    )


def gutter_start():
    return '<div class="page-gutter content-stack">'


def screen_shell(name, height, body):
    return (
        f'<!doctype html><html lang="id"><head><meta charset="utf-8">'
        f'<link rel="stylesheet" href="app.css"><style>'
        f'html,body{{margin:0}}body{{width:390px;height:{height}px;overflow:hidden;position:relative}}</style>'
        f'</head><body style="zoom:{SCALE}">{body}</body></html>'
    )


# ---------------------------------------------------------------- screens ----
KODE = 'pa0012'
NAMA = 'Sinta Rahma'
KAMAR = '12'
KOST = 'Pondok Huda'
ADMIN = 'Bu Rina'

SCREENS = {}

# --- login -------------------------------------------------------------------
SCREENS['login'] = (844, f'''
<div class="relative min-h-dvh overflow-hidden bg-[#06290f] text-white">
  <div class="pointer-events-none absolute -left-28 -top-24 size-80 rounded-full bg-[#55dce0]/14 blur-3xl"></div>
  <div class="pointer-events-none absolute -bottom-40 -right-24 size-96 rounded-full bg-[#a7f3d0]/10 blur-3xl"></div>
  <div class="relative mx-auto flex min-h-dvh max-w-[480px] flex-col px-5 pb-5 pt-6">
    <div class="flex items-center gap-3 px-1">
      <span class="grid size-11 place-items-center rounded-[.9rem] border border-white/15 bg-white/9 backdrop-blur">
        <img src="icon-512.png" alt="" class="size-7 object-contain" />
      </span>
      <div>
        <p class="text-base font-semibold tracking-[-.01em]">Pondok Huda</p>
        <p class="text-xs text-white/55">Ruang nyaman untuk penghuni</p>
      </div>
      <span class="ml-auto inline-flex min-h-10 items-center gap-1.5 rounded-full border border-white/15 bg-white/8 px-3 text-xs font-semibold text-white/80">{icon('info', 16)} Panduan</span>
    </div>
    <div class="mt-9 px-1">
      <span class="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/8 px-3 py-1 text-xs font-medium text-[#a8eff0]">{icon('check', 14)} Informasi kost dalam satu tempat</span>
      <h1 class="mt-4 max-w-sm text-[1.65rem] font-semibold leading-[1.16] tracking-[-.025em]">Pulang terasa lebih tenang.</h1>
      <p class="mt-3 max-w-sm text-sm leading-relaxed text-white/65">Pantau tagihan, baca kabar terbaru, dan sampaikan kendala tanpa perlu menunggu.</p>
    </div>
    <div class="mb-auto mt-7 rounded-[1.4rem] bg-surface px-5 py-5 text-on-surface shadow-[0_20px_56px_rgba(0,0,0,.22)]">
      <div class="mb-5">
        <p class="text-xs font-semibold uppercase tracking-[.1em] text-primary">Masuk akun</p>
        <h2 class="mt-1 text-xl font-semibold tracking-[-.02em]">Selamat datang</h2>
        <p class="mt-1 text-sm text-on-surface-variant">Gunakan kode penghuni dan PIN enam digit.</p>
      </div>
      <div class="flex flex-col gap-4">
        {icon_field('Kode penyewa', 'person', KODE)}
        <div class="relative">
          {icon_field('PIN', 'lock', '••••••', hint='PIN terdiri dari tepat 6 digit.', extra_input='pr-20 tracking-[.35em]')}
          <span class="absolute right-2 top-[31px] min-h-11 rounded-full px-3 text-xs font-bold text-primary">Lihat</span>
        </div>
        <span class="inline-flex min-h-11 items-center justify-center rounded-[.9rem] bg-primary text-on-primary shadow-[0_6px_16px_rgba(46,125,50,.28)] font-semibold text-sm mt-1 w-full">Masuk ke aplikasi</span>
      </div>
      <div class="mt-5 flex items-center justify-center gap-2 text-center text-xs text-on-surface-variant">
        {icon('lock', 14)} Data Anda hanya tersimpan di perangkat ini.
      </div>
    </div>
  </div>
</div>
''')

# --- dashboard ---------------------------------------------------------------
ringkasan_lines = [
    'Anda belum melakukan pembayaran sewa kost untuk periode 27 Okt 2026 s.d. 25 Nov 2026',
    'Sebesar Rp. 750000',
    'dari Tanggal 27 Okt 2026',
    '27 Okt 2026 s.d. 25 Nov 2026',
]
ringkasan_items = ''.join(
    f'<li class="flex gap-3 py-2.5 first:pt-0 last:pb-0">'
    f'<span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-primary-container text-on-primary-container">{icon("check", 12)}</span>'
    f'<span class="text-[13px] leading-relaxed text-on-surface-variant">{line}</span></li>'
    for line in ringkasan_lines
)

def quick_action(ic, tone, label, sub, tour=''):
    data = f' data-tour="{tour}"' if tour else ''
    return (
        f'<a{data} class="group rounded-[1rem] border border-outline-variant/35 bg-surface-lowest p-2.5">'
        f'<span class="flex size-9 items-center justify-center rounded-[.8rem] {tone}">{icon(ic, 18)}</span>'
        f'<p class="mt-2.5 truncate text-[11px] font-semibold text-on-surface">{label}</p>'
        f'<p class="mt-0.5 truncate text-[11px] text-on-surface-variant">{sub}</p></a>'
    )


dashboard_body = f'''
{page_header(KOST, f'Kamar {KAMAR} · {NAMA}', right='<span class="inline-flex min-h-10 items-center gap-1.5 rounded-[.9rem] bg-primary-container px-3.5 text-[13px] font-semibold text-on-primary-container">{ICON_PANDUAN}</span>'.replace('{ICON_PANDUAN}', icon('info', 18) + ' Panduan'))}
{gutter_start()}
<section data-tour="status" class="relative overflow-hidden rounded-[1.35rem] bg-primary px-4.5 py-4.5 text-on-primary shadow-[0_12px_34px_rgba(46,125,50,.22)]">
  <div class="pointer-events-none absolute -right-14 -top-20 size-52 rounded-full border-[30px] border-white/6"></div>
  <div class="relative flex items-start justify-between gap-3">
    <div>
      <p class="text-xs font-medium text-on-primary/70">Halo, {NAMA.split(' ')[0]}</p>
      <h2 class="mt-1 text-xl font-semibold tracking-[-.03em]">Belum Bayar</h2>
      <p class="mt-1 text-xs font-medium text-on-primary/80">Terlambat 3 hari</p>
    </div>
    <span class="inline-flex min-h-10 shrink-0 items-center gap-1.5 rounded-full border border-white/50 bg-white px-3 text-xs font-bold text-[#17351c] shadow-[0_5px_16px_rgba(0,0,0,.18)]">{icon('refresh', 17)} Perbarui</span>
  </div>
  <div class="relative mt-4 grid grid-cols-2 gap-2.5">
    <div class="rounded-[.95rem] border border-white/12 bg-linear-to-b from-white/16 to-white/6 p-3 shadow-[inset_0_1px_0_rgba(255,255,255,0.18),0_5px_14px_rgba(0,0,0,0.10)]">
      <p class="text-[11px] font-medium text-on-primary/65">Total tagihan</p>
      <p class="money-value mt-1 text-sm font-semibold">Rp 750.000</p>
    </div>
    <div class="rounded-[.95rem] border border-white/12 bg-linear-to-b from-white/16 to-white/6 p-3 shadow-[inset_0_1px_0_rgba(255,255,255,0.18),0_5px_14px_rgba(0,0,0,0.10)]">
      <p class="text-[11px] font-medium text-on-primary/65">Jatuh tempo</p>
      <p class="mt-1 text-sm font-semibold leading-snug">25 Nov 2026</p>
    </div>
  </div>
  <div class="relative mt-3 flex items-center gap-2 text-[11px] font-medium">
    <span class="size-2 rounded-full bg-error-container"></span>
    Periksa detail pembayaran Anda
  </div>
</section>
<section>
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1">
    <div><h2 class="text-[15px] font-semibold text-on-surface">Akses cepat</h2><p class="mt-0.5 text-xs text-on-surface-variant">Yang paling sering Anda butuhkan</p></div>
  </div>
  <div class="mt-3 grid grid-cols-3 gap-2">
    {quick_action('wallet', 'bg-primary-container text-on-primary-container', 'Pembayaran', 'Lihat riwayat', 'payment')}
    {quick_action('notice', 'bg-secondary-container text-on-secondary-container', 'Pengumuman', 'Kabar kost', 'notice')}
    {quick_action('comment', 'bg-tertiary-container text-on-tertiary-container', 'Keluhan', 'Lapor kendala', 'complaint')}
  </div>
</section>
<section>
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Ringkasan tagihan</h2></div></div>
  <div class="mt-3 rounded-[1.1rem] bg-surface-lowest px-4 py-4 shadow-[0_7px_22px_rgba(15,45,42,.065)]">
    <ul class="divide-y divide-outline-variant/45">{ringkasan_items}</ul>
  </div>
</section>
<section>
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1">
    <div><h2 class="text-[15px] font-semibold text-on-surface">Butuh bantuan?</h2><p class="mt-0.5 text-xs text-on-surface-variant">Admin kost siap dihubungi</p></div>
  </div>
  <div class="mt-3 rounded-[1.1rem] border border-outline-variant/70 bg-surface-lowest !p-2">
    <span class="flex min-h-16 items-center gap-3 rounded-[1rem] px-3">
      <span class="flex size-10 items-center justify-center rounded-full bg-success-container text-on-success-container">{icon('phone', 19)}</span>
      <span class="min-w-0 flex-1"><span class="block truncate text-sm font-bold text-on-surface">{ADMIN}</span><span class="block text-xs text-on-surface-variant">Chat via WhatsApp</span></span>
      {icon('chevronRight', 18, 'text-outline')}
    </span>
  </div>
</section>
</div>
{bottom_nav('Beranda')}
'''
SCREENS['dashboard'] = (1050, dashboard_body)

# --- tour --------------------------------------------------------------------
tour_tooltip = f'''
<div class="absolute z-50 rounded-[1.25rem] bg-surface-lowest p-4 text-on-surface shadow-[0_18px_60px_rgba(0,0,0,.4)]" style="top:372px;left:16px;width:358px">
  <div class="flex items-start gap-3">
    <span class="flex size-10 shrink-0 items-center justify-center rounded-[.85rem] bg-primary-container text-on-primary-container">{icon('wallet', 20)}</span>
    <div class="min-w-0 flex-1">
      <p class="text-[10px] font-bold uppercase tracking-[.1em] text-primary">Demo langsung · 1/5</p>
      <h2 class="mt-1 text-base font-bold tracking-[-.01em]">Cek status tagihan</h2>
    </div>
    <span class="flex size-9 shrink-0 items-center justify-center rounded-full text-on-surface-variant">{icon('close', 18)}</span>
  </div>
  <p class="mt-3 text-[13px] leading-relaxed text-on-surface-variant">Di sini terlihat status pembayaran, jumlah tagihan, dan tanggal jatuh tempo. Tombol di kanan atas memuat data terbaru.</p>
  <div class="mt-4 flex items-center justify-between gap-3">
    <div class="flex gap-1"><span class="h-1.5 w-5 rounded-full bg-primary"></span><span class="h-1.5 w-1.5 rounded-full bg-outline-variant"></span><span class="h-1.5 w-1.5 rounded-full bg-outline-variant"></span><span class="h-1.5 w-1.5 rounded-full bg-outline-variant"></span><span class="h-1.5 w-1.5 rounded-full bg-outline-variant"></span></div>
    <span class="inline-flex min-h-10 items-center justify-center rounded-[.9rem] bg-primary px-4 text-sm gap-2 font-semibold text-on-primary">Lanjut</span>
  </div>
</div>
'''

tour_body = dashboard_body + f'''
<div class="absolute inset-0 z-40" style="height:844px">
  <div class="absolute rounded-[1.35rem] border-2 border-primary-container" style="top:71px;left:16px;width:358px;height:284px;box-shadow:0 0 0 9999px rgba(0,0,0,.72),0 0 32px rgba(46,125,50,.55)">
    <span class="absolute -right-2 -top-2 flex size-7 items-center justify-center rounded-full bg-primary-container text-on-primary-container shadow-lg">{icon('info', 16)}</span>
  </div>
  {tour_tooltip}
</div>
'''
SCREENS['tour'] = (844, tour_body)

# --- pembayaran --------------------------------------------------------------
def metric(label, value, danger=False, small=False):
    cls = 'text-sm leading-snug' if small else 'text-[15px]'
    color = 'text-error-container' if danger else 'text-surface'
    return (
        f'<div><p class="text-[10px] font-semibold text-surface/55">{label}</p>'
        f'<p class="money-value mt-1 font-semibold {cls} {color}">{value}</p></div>'
    )


def history_item(no, periode, tgl, status, tone_dot, total, extra=''):
    badge = (
        f'<span class="inline-flex min-h-6 items-center rounded-full px-2.5 py-0.5 text-[11px] font-semibold '
        + ('bg-success-container text-on-success-container' if tone_dot == 'ok' else 'bg-error-container text-on-error-container')
        + f'">{status}</span>'
    )
    return (
        f'<li class="relative pl-11">'
        f'<span class="absolute left-2.5 top-6 z-10 size-[1.05rem] rounded-full border-[3px] border-surface '
        + ('bg-success' if tone_dot == 'ok' else 'bg-error')
        + f'"></span>'
        f'<div class="rounded-[1.1rem] bg-surface-lowest px-4 py-4 shadow-[0_7px_22px_rgba(15,45,42,.065)]">'
        f'<div class="flex items-start justify-between gap-3"><div class="min-w-0">'
        f'<p class="text-[10px] font-bold uppercase tracking-[.08em] text-primary">{no}</p>'
        f'<h3 class="mt-1 truncate text-sm font-semibold text-on-surface">{periode}</h3>'
        f'<p class="mt-1 text-xs text-on-surface-variant">{tgl}</p></div>{badge}</div>'
        f'<div class="mt-4 flex items-end justify-between border-t border-outline-variant/45 pt-3">'
        f'<div><p class="text-[10px] font-semibold text-on-surface-variant">Total dibayar</p>'
        f'<p class="money-value text-base font-semibold text-on-surface">{total}</p></div>'
        f'<div class="flex flex-col items-end gap-1 text-[11px]">{extra}</div></div></div></li>'
    )


pembayaran_body = f'''
{page_header('Pembayaran', KOST, right=f'<span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full text-on-surface-variant">{icon("info", 20)}</span>')}
{gutter_start()}
<section data-tour="payment-summary" class="relative overflow-hidden rounded-[1.35rem] bg-on-surface px-4.5 py-4.5 text-surface shadow-[0_12px_34px_rgba(8,25,24,.16)]">
  <div class="absolute -right-12 -top-14 size-44 rounded-full bg-primary/25 blur-2xl"></div>
  <div class="relative flex items-start justify-between gap-3">
    <div><p class="text-[11px] font-medium text-surface/60">Status periode ini</p><h2 class="mt-1 text-xl font-semibold tracking-[-.025em]">Belum Bayar</h2></div>
    <span class="inline-flex min-h-6 items-center rounded-full bg-error-container px-2.5 py-0.5 text-[11px] font-semibold text-on-error-container">Belum Bayar</span>
  </div>
  <div class="relative mt-5 grid grid-cols-2 gap-x-4 gap-y-4 border-t border-surface/15 pt-4">
    {metric('Tagihan', 'Rp 750.000')}
    {metric('Sudah dibayar', 'Rp 0')}
    {metric('Sisa pembayaran', 'Rp 750.000', danger=True)}
    {metric('Jatuh tempo', '25 Nov 2026', small=True)}
  </div>
</section>
<div data-tour="payment-history" class="flex flex-col gap-3">
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Riwayat pembayaran</h2><p class="mt-0.5 text-xs text-on-surface-variant">2 transaksi tercatat</p></div></div>
  <ul class="relative flex flex-col gap-3 before:absolute before:bottom-7 before:left-[1.1rem] before:top-7 before:w-px before:bg-outline-variant/70">
    {history_item('Periode berjalan', 'Nov 2026', '25 Nov 2026', 'Belum Bayar', 'err', 'Rp 750.000')}
    {history_item('Pembayaran #3', 'Okt 2026', '24 Okt 2026 · Transfer', 'Lunas', 'ok', 'Rp 750.000', extra='<span class="text-success">Diskon Rp 25.000</span>')}
  </ul>
</div>
</div>
{bottom_nav('Bayar')}
'''
SCREENS['pembayaran'] = (900, pembayaran_body)

# --- pengumuman --------------------------------------------------------------
def notice_card(date, title, teaser, comments, first=False):
    chip = 'bg-primary text-on-primary' if first else 'bg-primary-container text-on-primary-container'
    badge = f'<span class="inline-flex min-h-6 items-center rounded-full bg-surface-variant px-2.5 py-0.5 text-[11px] font-semibold text-on-surface-variant">{comments} komentar</span>' if comments else ''
    return (
        f'<li><span class="group block rounded-[1.1rem] border border-outline-variant/35 bg-surface-lowest p-4">'
        f'<div class="flex gap-3.5"><span class="flex size-10 shrink-0 items-center justify-center rounded-[.85rem] {chip}">{icon("notice", 20)}</span>'
        f'<div class="min-w-0 flex-1"><div class="flex items-center justify-between gap-2">'
        f'<span class="text-[10px] font-bold uppercase tracking-[.06em] text-primary">{date}</span>{badge}</div>'
        f'<h3 class="mt-1.5 text-[15px] font-semibold leading-snug text-on-surface">{title}</h3>'
        f'<p class="mt-1.5 line-clamp-2 text-[13px] leading-relaxed text-on-surface-variant">{teaser}</p>'
        f'<div class="mt-3 flex items-center justify-between border-t border-outline-variant/40 pt-2.5 text-xs font-semibold text-primary">'
        f'<span>Buka pengumuman</span>{icon("chevronRight", 17)}</div></div></div></span></li>'
    )


pengumuman_body = f'''
{page_header('Pengumuman', KOST, right=f'<span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full text-on-surface-variant">{icon("info", 20)}</span>')}
{gutter_start()}
<div data-tour="notice-list"><div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Kabar dari kost</h2><p class="mt-0.5 text-xs text-on-surface-variant">Informasi terbaru untuk seluruh penghuni</p></div></div></div>
<ul class="flex flex-col gap-3">
  {notice_card('14 Nov 2026', 'Pemadaman listrik terjadwal', 'Halo penghuni, pada hari Jumat 20 November pukul 09.00–12.00 WIB akan ada pemadaman listrik terjadwal untuk perbaikan panel.', 3, first=True)}
  {notice_card('02 Nov 2026', 'Batas pembayaran November', 'Mohon pembayaran sewa bulan November diselesaikan sebelum tanggal 25. Pembayaran dapat dilakukan via transfer bank.', 0)}
</ul>
</div>
{bottom_nav('Info')}
'''
SCREENS['pengumuman'] = (640, pengumuman_body)

# --- pengumuman detail -------------------------------------------------------
chat_admin = (
    '<li class="flex justify-start"><div class="max-w-[84%] rounded-[1rem] rounded-bl-sm border border-outline-variant/50 bg-surface-lowest px-3.5 py-2.5 text-[13px] leading-relaxed text-on-surface">'
    '<p class="mb-0.5 text-[10px] font-bold text-primary">Admin kost</p><p>Siap, terima kasih infonya.</p>'
    '<p class="mt-1 text-right text-[10px] text-on-surface-variant">14 Nov, 10.05</p></div></li>'
)
chat_me = (
    '<li class="flex justify-end"><div class="max-w-[84%] rounded-[1rem] rounded-br-sm bg-primary px-3.5 py-2.5 text-[13px] leading-relaxed text-on-primary">'
    '<p>Kira-kira jam 12 sudah menyala kembali?</p><p class="mt-1 text-right text-[10px] text-on-primary/65">14 Nov, 09.58</p></div></li>'
)
detail_body = f'''
{page_header('Detail pengumuman', right=f'<span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full text-on-surface-variant">{icon("info", 20)}</span>', back=True)}
<div class="page-gutter content-stack pb-3">
  <article data-tour="notice-content" class="rounded-[1.2rem] border border-outline-variant/35 bg-surface-lowest p-5 shadow-[0_7px_22px_rgba(15,45,42,.055)]">
    <div class="flex items-center gap-3">
      <span class="flex size-10 items-center justify-center rounded-[.85rem] bg-primary text-on-primary">{icon('notice', 20)}</span>
      <div><p class="text-xs font-extrabold text-on-surface">Admin kost</p><span class="text-[11px] text-on-surface-variant">14 Nov 2026</span></div>
    </div>
    <h1 class="mt-5 text-[1.4rem] font-semibold leading-snug tracking-[-.025em] text-on-surface">Pemadaman listrik terjadwal</h1>
    <p class="mt-4 whitespace-pre-line text-sm leading-7 text-on-surface-variant">Halo penghuni, pada hari Jumat 20 November pukul 09.00–12.00 WIB akan ada pemadaman listrik terjadwal untuk perbaikan panel. Mohon simpan pekerjaan penting dan matikan peralatan elektronik. Terima kasih.</p>
  </article>
  <div data-tour="notice-discussion" class="flex flex-col gap-2.5">
    <div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Diskusi</h2><p class="mt-0.5 text-xs text-on-surface-variant">2 komentar</p></div></div>
    <ul class="flex flex-col gap-2.5">{chat_me}{chat_admin}</ul>
  </div>
  <form data-tour="notice-comment" class="sticky bottom-2 z-10 mt-1 flex items-center gap-2 rounded-[1.1rem] border border-outline-variant/60 bg-surface-lowest/95 p-2 pl-4 shadow-[0_8px_26px_rgba(8,39,37,.13)] backdrop-blur-xl">
    <input readonly placeholder="Tulis komentar…" class="min-h-11 min-w-0 flex-1 bg-transparent text-sm text-on-surface outline-none placeholder:text-on-surface-variant/60">
    <span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full bg-primary text-on-primary">{icon('send', 20)}</span>
  </form>
</div>
'''
SCREENS['detail'] = (820, detail_body)

# --- keluhan list ------------------------------------------------------------
def keluhan_card(kategori, tgl, judul, uraian, status, tone, replies):
    strip = {'ok': 'bg-success', 'err': 'bg-error', 'primary': 'bg-primary'}[tone]
    badge = {
        'ok': 'bg-success-container text-on-success-container',
        'err': 'bg-error-container text-on-error-container',
        'primary': 'bg-primary-container text-on-primary-container',
    }[tone]
    return (
        f'<li class="relative overflow-hidden rounded-[1.1rem] border border-outline-variant/35 bg-surface-lowest p-4">'
        f'<span class="absolute inset-y-0 left-0 w-1 {strip}"></span>'
        f'<div class="flex items-start justify-between gap-3 pl-1"><div class="min-w-0">'
        f'<div class="flex flex-wrap items-center gap-2"><span class="inline-flex min-h-6 items-center rounded-full bg-primary-container px-2.5 py-0.5 text-[11px] font-semibold text-on-primary-container">{kategori}</span>'
        f'<span class="text-[10px] text-on-surface-variant">{tgl}</span></div>'
        f'<h3 class="mt-2.5 text-[15px] font-semibold leading-snug text-on-surface">{judul}</h3>'
        f'<p class="mt-1 line-clamp-2 text-[13px] leading-relaxed text-on-surface-variant">{uraian}</p></div>'
        f'<span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-surface-low text-on-surface-variant">{icon("comment", 17)}</span></div>'
        f'<div class="mt-4 flex items-center justify-between border-t border-outline-variant/40 pl-1 pt-3">'
        f'<span class="inline-flex min-h-6 items-center rounded-full {badge} px-2.5 py-0.5 text-[11px] font-semibold">{status}</span>'
        f'<span class="flex items-center gap-1 text-[11px] text-on-surface-variant">{icon("comment", 13)} {replies} tanggapan</span></div></li>'
    )


keluhan_body = f'''
{page_header('Keluhan', KOST, right=(
    f'<div class="flex items-center gap-1">'
    f'<span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full text-on-surface-variant">{icon("info", 20)}</span>'
    f'<span class="inline-flex min-h-10 items-center gap-1.5 rounded-[.85rem] bg-primary px-3.5 text-xs font-semibold text-on-primary shadow-sm">{icon("edit", 15)} Baru</span></div>'))}
<div data-tour="complaint-list" class="page-gutter content-stack">
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Laporan Anda</h2><p class="mt-0.5 text-xs text-on-surface-variant">Pantau progres kendala yang sudah disampaikan</p></div></div>
  <ul class="flex flex-col gap-3">
    {keluhan_card('Fasilitas', '10 Nov 2026', 'Keran kamar mandi bocor', 'Keran kamar mandi di kamar 12 menetes terus sejak semalam.', 'Diproses', 'primary', 2)}
    {keluhan_card('Fasilitas', '28 Okt 2026', 'Lampu kamar mati', 'Lampu utama kamar tidak menyala setelah hujan sore.', 'Selesai', 'ok', 4)}
  </ul>
</div>
{bottom_nav('Keluhan')}
'''
SCREENS['keluhan'] = (600, keluhan_body)

# --- keluhan form ------------------------------------------------------------
def chip(label, selected=False):
    cls = 'bg-primary text-on-primary shadow-sm' if selected else 'border border-outline-variant bg-surface-lowest text-on-surface-variant'
    return f'<button class="min-h-10 rounded-full px-4 text-xs font-semibold {cls}">{label}</button>'


form_body = f'''
{page_header('Keluhan baru', f'Kamar {KAMAR}', right=f'<span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full text-on-surface-variant">{icon("info", 20)}</span>', back=True)}
<form class="page-gutter content-stack">
  <div data-tour="complaint-category">
    <div class="rounded-[1.1rem] bg-surface-lowest px-4 py-4 shadow-[0_7px_22px_rgba(15,45,42,.065)]">
      <div class="flex items-end justify-between gap-3 px-0.5"><div><h2 class="text-[15px] font-semibold text-on-surface">Apa yang terjadi?</h2><p class="mt-0.5 text-xs text-on-surface-variant">Pilih kategori yang paling sesuai</p></div></div>
      <div class="mt-4 flex flex-wrap gap-2">{chip('Fasilitas', True)}{chip('Kebersihan')}{chip('Keamanan')}{chip('Lainnya')}</div>
    </div>
  </div>
  <div data-tour="complaint-description">
    <div class="rounded-[1.1rem] border border-outline-variant/70 bg-surface-lowest flex flex-col gap-4" style="border-radius:1.1rem;padding:1rem">
      <div class="flex flex-col gap-2">
        <label class="px-0.5 text-[13px] font-semibold text-on-surface-variant">Judul keluhan</label>
        <input readonly value="Keran kamar mandi bocor" class="min-h-12 w-full rounded-[.9rem] border bg-surface-lowest px-4 text-sm text-on-surface outline-none border-outline-variant">
      </div>
      <div class="flex flex-col gap-2">
        <label class="px-0.5 text-[13px] font-semibold text-on-surface-variant">Ceritakan kendalanya</label>
        <textarea readonly rows="4" class="rounded-[.9rem] border bg-surface-lowest px-4 py-3 text-sm leading-relaxed text-on-surface outline-none border-outline-variant">Keran kamar mandi di kamar 12 menetes terus sejak semalam. Sudah saya kencangkan tetapi tetap bocor. Mohon dicek, terima kasih.</textarea>
        <p class="px-0.5 text-xs text-on-surface-variant">Semakin jelas informasinya, semakin cepat admin membantu.</p>
      </div>
    </div>
  </div>
  <div data-tour="complaint-photo">
    <div class="rounded-[1.1rem] border border-outline-variant/70 bg-surface-lowest px-4 py-4">
      <div class="flex items-end justify-between gap-3 px-0.5"><div><h2 class="text-[15px] font-semibold text-on-surface">Foto pendukung</h2><p class="mt-0.5 text-xs text-on-surface-variant">Opsional · gambar akan dikompres otomatis</p></div></div>
      <div class="mt-4 flex min-h-22 w-full flex-col items-center justify-center gap-1.5 rounded-[.9rem] border border-dashed border-outline bg-surface-low/45 text-sm font-semibold text-on-surface-variant">
        <span class="flex size-10 items-center justify-center rounded-full bg-primary-container text-on-primary-container">{icon('camera', 20)}</span>
        Ambil atau pilih foto
      </div>
    </div>
  </div>
  <div data-tour="complaint-submit" class="rounded-[1.05rem] bg-surface/90 p-2">
    <span class="inline-flex min-h-12 w-full items-center justify-center rounded-[.9rem] bg-primary text-on-primary font-semibold text-sm gap-2 shadow-[0_6px_16px_rgba(46,125,50,.28)]">Kirim keluhan</span>
  </div>
</form>
'''
SCREENS['form'] = (980, form_body)

# --- profil ------------------------------------------------------------------
def data_row(label, value):
    return (
        f'<div class="px-3 py-3"><dt class="text-[10px] font-bold uppercase tracking-[.07em] text-on-surface-variant">{label}</dt>'
        f'<dd class="mt-1 break-words text-sm font-semibold leading-relaxed text-on-surface">{value}</dd></div>'
    )


def theme_btn(ic, label, selected=False):
    cls = 'bg-surface-lowest text-primary shadow-sm' if selected else 'text-on-surface-variant'
    return f'<button class="flex min-h-14 flex-col items-center justify-center gap-1 rounded-[.85rem] text-xs font-semibold {cls}">{icon(ic, 20)} {label}</button>'


profil_body = f'''
{page_header('Profil', 'Akun dan preferensi', right=f'<span class="inline-flex size-10 shrink-0 items-center justify-center rounded-full text-on-surface-variant">{icon("info", 20)}</span>')}
{gutter_start()}
<section data-tour="profile-summary" class="relative overflow-hidden rounded-[1.35rem] bg-primary px-4.5 py-4.5 text-on-primary shadow-[0_12px_34px_rgba(46,125,50,.22)]">
  <div class="absolute -right-16 -top-20 size-52 rounded-full border-[30px] border-white/6"></div>
  <div class="relative flex items-center gap-4">
    <span class="flex size-16 items-center justify-center rounded-[1rem] border border-white/20 bg-white/10">{icon('person', 30)}</span>
    <div class="min-w-0">
      <p class="text-xs font-semibold text-on-primary/65">Penghuni kamar {KAMAR}</p>
      <h2 class="mt-1 truncate text-lg font-semibold tracking-[-.02em]">{NAMA}</h2>
      <p class="mt-1 truncate text-xs text-on-primary/75">{KODE} · {KOST}</p>
    </div>
  </div>
</section>
<div data-tour="profile-theme" class="flex flex-col gap-2">
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Tampilan</h2><p class="mt-0.5 text-xs text-on-surface-variant">Pilih tema yang nyaman untuk mata</p></div></div>
  <div class="grid grid-cols-3 gap-1.5 rounded-[1.05rem] bg-surface-low p-1.5">{theme_btn('sun', 'Terang', True)}{theme_btn('moon', 'Gelap')}{theme_btn('monitor', 'Sistem')}</div>
</div>
<div>
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Bantuan</h2><p class="mt-0.5 text-xs text-on-surface-variant">Pelajari kembali cara memakai aplikasi</p></div></div>
  <div class="mt-3 flex min-h-16 w-full items-center gap-3 rounded-[1.15rem] border border-outline-variant bg-surface-lowest px-4">
    <span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary-container text-on-primary-container">{icon('info', 20)}</span>
    <span class="min-w-0 flex-1"><span class="block text-sm font-bold text-on-surface">Panduan penggunaan</span><span class="block text-xs text-on-surface-variant">Buka tutorial dari awal</span></span>
    {icon('chevronRight', 18, 'text-outline')}
  </div>
</div>
<div data-tour="profile-security" class="flex flex-col gap-2">
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Keamanan akun</h2><p class="mt-0.5 text-xs text-on-surface-variant">Perbarui PIN secara berkala</p></div></div>
  <div class="rounded-[1.1rem] bg-surface-lowest px-4 py-4 shadow-[0_7px_22px_rgba(15,45,42,.065)]">
    <div class="flex flex-col gap-4">
      {icon_field('PIN lama', 'lock', '••••••', extra_input='tracking-[.35em]')}
      {icon_field('PIN baru', 'lock', '••••••', extra_input='tracking-[.35em]')}
      <span class="inline-flex min-h-11 items-center justify-center rounded-[.9rem] bg-primary-container text-on-primary-container font-semibold text-sm gap-2">Simpan PIN baru</span>
    </div>
  </div>
</div>
<div data-tour="profile-data" class="flex flex-col gap-2">
  <div class="flex items-end justify-between gap-3 px-0.5 pt-1"><div><h2 class="text-[15px] font-semibold text-on-surface">Data diri</h2></div></div>
  <div class="rounded-[1.1rem] border border-outline-variant/70 bg-surface-lowest !p-1.5">
    <dl class="divide-y divide-outline-variant/40">
      {data_row('Kode penyewa', KODE)}
      {data_row('Nomor kamar', KAMAR)}
      {data_row('Nomor HP', '0812-3456-7890')}
      {data_row('Periode bayar', 'Bulanan')}
      {data_row('Nama kost', KOST)}
    </dl>
  </div>
</div>
<button data-tour="profile-logout" class="flex min-h-12 w-full items-center justify-center gap-2 rounded-[.95rem] border border-error/25 bg-error-container/60 text-sm font-semibold text-on-error-container">{icon('logout', 18)} Keluar dari akun</button>
<p class="pb-3 text-center text-[11px] font-medium text-on-surface-variant/65">Pondok Huda · aplikasi penghuni</p>
</div>
{bottom_nav('Saya')}
'''
SCREENS['profil'] = (1360, profil_body)

# ---------------------------------------------------------------- build ----
# Firefox 159 headless ignores layout.css.devPixelsPerPx, so density is
# achieved with html{zoom:SCALE} and a SCALE× larger window — identical
# CSS layout, 2× pixel density.
SCALE = 2


def firefox_shot(png, url, css_w, css_h):
    subprocess.run(
        [FIREFOX, '--headless', '--screenshot', png,
         f'--window-size={int(css_w * SCALE)},{int(css_h * SCALE)}', url],
        check=True, timeout=90,
        stdout=subprocess.DEVNULL, stderr=subprocess.DEVNULL,
        env={**os.environ, 'HOME': os.environ.get('HOME', '/home/leafy')},
    )


def write_and_shot(name, height, html):
    path = os.path.join(OUT, f'shot-{name}.html')
    with open(path, 'w', encoding='utf-8') as fh:
        fh.write(screen_shell(name, height, html))
    png = os.path.join(OUT, f'shot-{name}.png')
    firefox_shot(png, 'file://' + path, 390, height)
    return png


def main():
    if not FIREFOX:
        sys.exit('firefox not found')
    os.makedirs(OUT, exist_ok=True)

    css_candidates = sorted(glob.glob(os.path.join(ROOT, 'app', 'dist', 'assets', 'index-*.css')))
    if not css_candidates:
        sys.exit('app/dist assets missing — run `npm run build` in app/ first')
    shutil.copy(css_candidates[-1], os.path.join(OUT, 'app.css'))
    shutil.copy(os.path.join(ROOT, 'app', 'public', 'icons', 'icon-512.png'), os.path.join(OUT, 'icon-512.png'))

    shots = {}
    for name, (height, html) in SCREENS.items():
        shots[name] = write_and_shot(name, height, html)

# ---------------------------------------------------------------- manual ----
LOGO = 'icon-512.png'

PAGE_W, PAGE_H = 794, 1123
GREEN = '#2e7d32'
INK = '#1e2620'
MUTED = '#57645a'
LINE = '#d8e2d5'
SOFT = '#f2f7ef'


def phone_shot(name, width=300):
    return (
        f'<img src="shot-{name}.png" alt="" '
        f'style="width:{width}px;border:2px solid {LINE};border-radius:22px;'
        f'box-shadow:0 14px 40px rgba(15,45,42,.14);display:block">'
    )


def page_html(chapter, body_html, number, total):
    return f'''<!doctype html><html lang="id"><head><meta charset="utf-8"><style>
      html,body{{margin:0}}
      body{{width:{PAGE_W}px;height:{PAGE_H}px;overflow:hidden;position:relative;
        background:#fbfdf9;font-family:ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,Arial,sans-serif;color:{INK}}}
      .band{{position:absolute;top:0;left:0;right:0;height:56px;display:flex;align-items:center;gap:10px;
        padding:0 46px;border-bottom:1px solid {LINE}}}
      .band .dot{{width:26px;height:26px;border-radius:9px;background:{GREEN};display:grid;place-items:center;color:#fff}}
      .band .name{{font-size:13px;font-weight:700}}
      .band .chap{{margin-left:auto;font-size:11px;font-weight:600;color:{MUTED};letter-spacing:.08em;text-transform:uppercase}}
      .foot{{position:absolute;bottom:0;left:0;right:0;height:44px;display:flex;align-items:center;
        justify-content:space-between;padding:0 46px;border-top:1px solid {LINE};font-size:11px;color:{MUTED}}}
      .content{{position:absolute;top:56px;bottom:44px;left:0;right:0;padding:30px 46px}}
      h2.sec{{font-size:22px;margin:0 0 6px;letter-spacing:-.02em}}
      p.lead{{font-size:13.5px;line-height:1.65;color:{MUTED};margin:0 0 14px}}
      ol.steps{{margin:10px 0 0;padding-left:0;list-style:none;counter-reset:st}}
      ol.steps li{{counter-increment:st;position:relative;padding-left:34px;margin-bottom:10px;
        font-size:13px;line-height:1.55}}
      ol.steps li::before{{content:counter(st);position:absolute;left:0;top:0;width:22px;height:22px;
        border-radius:50%;background:{GREEN};color:#fff;font-size:11px;font-weight:700;
        display:grid;place-items:center}}
      ul.bul{{margin:10px 0 0;padding-left:18px}}
      ul.bul li{{font-size:13px;line-height:1.6;margin-bottom:7px}}
      .tip{{background:{SOFT};border:1px solid {LINE};border-radius:12px;padding:12px 16px;
        font-size:12.5px;line-height:1.55;color:{MUTED};margin-top:14px}}
      .tip b{{color:{INK}}}
    </style></head><body style="zoom:2">
    <div class="band"><span class="dot"><svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor"><path d="M12 3l8 6.5V20a1 1 0 0 1-1 1h-5v-6h-4v6H5a1 1 0 0 1-1-1V9.5L12 3z"/></svg></span><span class="name">Pondok Huda</span><span class="chap">{chapter}</span></div>
    <div class="content">{body_html}</div>
    <div class="foot"><span>Panduan Aplikasi Penghuni · PWA &amp; APK</span><span>halaman {number} dari {total}</span></div>
    </body></html>'''


TOTAL_PAGES = 13
PAGES = []


def add_page(chapter, body):
    PAGES.append(page_html(chapter, body, len(PAGES) + 1, TOTAL_PAGES))


def build_pages():
    # ---- 1 · cover ----
    PAGES.append(f'''<!doctype html><html lang="id"><head><meta charset="utf-8"><style>
      html,body{{margin:0}}
      body{{width:{PAGE_W}px;height:{PAGE_H}px;overflow:hidden;position:relative;
        font-family:ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,Arial,sans-serif;color:#fff;
        background:linear-gradient(160deg,#0b3a14 0%,{GREEN} 70%,#3f9c52 100%)}}
      .wrap{{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:0 70px}}
      .logo{{width:132px;height:132px;border-radius:32px;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.25);
        display:grid;place-items:center;margin-bottom:30px}}
      h1{{font-size:38px;margin:0 0 12px;letter-spacing:-.03em}}
      .sub{{font-size:16px;color:rgba(255,255,255,.75);line-height:1.6;margin:0}}
      .chips{{display:flex;gap:10px;margin-top:34px}}
      .chip{{border:1px solid rgba(255,255,255,.35);border-radius:999px;padding:7px 18px;font-size:12.5px;font-weight:600;color:rgba(255,255,255,.92)}}
      .ver{{position:absolute;bottom:54px;left:0;right:0;text-align:center;font-size:12.5px;color:rgba(255,255,255,.65)}}
    </style></head><body style="zoom:2">
    <div class="wrap">
      <div class="logo"><img src="{LOGO}" style="width:84px;height:84px"></div>
      <h1>Panduan Aplikasi Penghuni</h1>
      <p class="sub">Petunjuk penggunaan aplikasi Pondok Huda untuk penyewa kost —<br>versi PWA (web installable) maupun APK (Android).</p>
      <div class="chips"><span class="chip">PWA</span><span class="chip">APK</span><span class="chip">Panduan interaktif</span></div>
    </div>
    <div class="ver">Versi 1.0 · November 2026 · dokumen pendamping web admin (admin.pdf)</div>
    </body></html>''')

    # ---- 2 · intro + daftar isi ----
    add_page('Pengantar', f'''
      <h2 class="sec">Tentang aplikasi ini</h2>
      <p class="lead">Aplikasi Penghuni Pondok Huda membantu penyewa memantau tagihan, membaca
      pengumuman kost, dan melaporkan keluhan langsung dari ponsel. Tersedia dalam dua bentuk:</p>
      <ul class="bul">
        <li><b>PWA</b> — dipasang langsung dari browser (Chrome/Safari) tanpa unduh APK.</li>
        <li><b>APK</b> — berkas Android dari GitHub Releases, dipasang manual.</li>
      </ul>
      <p class="lead" style="margin-top:16px">Keduanya tampilan dan fiturnya sama persis, memakai akun penyewa:</p>
      <div class="tip"><b>Kebutuhan akun:</b> kode penyewa (contoh: <b>pa0012</b>) dan <b>PIN 6 digit</b>
      yang diberikan pengelola kost. Lupa PIN? Hubungi admin kost melalui WhatsApp.</div>
      <h2 class="sec" style="margin-top:24px">Daftar isi</h2>
      <ol class="steps">
        <li><b>Pasang aplikasi</b> — langkah install PWA &amp; APK.</li>
        <li><b>Masuk ke aplikasi</b> — halaman login.</li>
        <li><b>Tutorial interaktif</b> — panduan demo saat pertama masuk.</li>
        <li><b>Beranda</b> — status tagihan dan akses cepat.</li>
        <li><b>Pembayaran</b> — ringkasan dan riwayat transaksi.</li>
        <li><b>Pengumuman</b> — kabar kost dan diskusi.</li>
        <li><b>Keluhan</b> — daftar laporan &amp; membuat keluhan baru.</li>
        <li><b>Profil</b> — tema, PIN, data diri, keluar akun.</li>
        <li><b>Offline, pembaruan &amp; tanya jawab</b>.</li>
      </ol>
    ''')

    # ---- 3 · pasang aplikasi ----
    def browser_bar():
        return f'''<div style="border:1px solid {LINE};border-radius:14px;overflow:hidden;background:#fff;box-shadow:0 6px 20px rgba(15,45,42,.08)">
          <div style="display:flex;align-items:center;gap:8px;background:#f1f4f0;padding:8px 12px;border-bottom:1px solid {LINE}">
            <span style="font-size:11px;color:{MUTED}">🔒</span>
            <span style="flex:1;background:#fff;border:1px solid {LINE};border-radius:999px;padding:5px 12px;font-size:11.5px;color:{MUTED}">app-pondokhuda.jannahku.com</span>
          </div>
          <div style="display:flex;align-items:center;gap:10px;padding:12px 14px">
            <img src="{LOGO}" style="width:30px;height:30px;border-radius:8px">
            <div style="flex:1;font-size:11.5px;line-height:1.35"><b>Pondok Huda</b><br><span style="color:{MUTED}">Terpasang cepat</span></div>
            <span style="background:{GREEN};color:#fff;font-size:11px;font-weight:700;padding:8px 14px;border-radius:999px">Install</span>
          </div>
        </div>'''

    add_page('Pasang aplikasi', f'''
      <h2 class="sec">Pasang aplikasi</h2>
      <p class="lead">Aplikasi butuh HTTPS untuk bisa dipasang. Buka alamat berikut di ponsel Anda:</p>
      <div class="tip" style="text-align:center;font-size:14px"><b>https://app-pondokhuda.jannahku.com</b></div>
      <div style="display:flex;gap:26px;margin-top:18px">
        <div style="flex:1">
          <h3 style="font-size:14px;margin:0 0 8px">Android · Chrome</h3>
          <ol class="steps">
            <li>Buka alamat di atas menggunakan Chrome.</li>
            <li>Ketuk ikon ⋮ (tiga titik) di pojok kanan atas.</li>
            <li>Pilih <b>Install app</b> atau <b>Tambahkan ke layar utama</b>.</li>
            <li>Konfirmasi — ikon Pondok Huda muncul di layar utama.</li>
          </ol>
        </div>
        <div style="flex:1">
          <h3 style="font-size:14px;margin:0 0 8px">iPhone · Safari</h3>
          <ol class="steps">
            <li>Buka alamat di atas menggunakan Safari.</li>
            <li>Ketuk tombol <b>Bagikan</b> (kotak dengan panah).</li>
            <li>Pilih <b>Tambahkan ke Layar Utama</b>.</li>
            <li>Ketuk <b>Tambah</b> — ikon muncul di layar utama.</li>
          </ol>
        </div>
      </div>
      <div style="margin-top:22px">{browser_bar()}</div>
      <h3 style="font-size:14px;margin:20px 0 8px">Alternatif: APK Android</h3>
      <ol class="steps">
        <li>Buka <b>Releases</b> di repositori GitHub Pondok Huda.</li>
        <li>Unduh <b>app-debug.apk</b> dari rilis terbaru.</li>
        <li>Buka berkasnya dan izinkan "instal dari sumber tidak dikenal".</li>
        <li>APK memakai URL API produksi yang sama dengan PWA.</li>
      </ol>
    ''')

    # ---- 4 · masuk ----
    add_page('Masuk ke aplikasi', f'''
      <h2 class="sec">Masuk ke aplikasi</h2>
      <p class="lead">Gunakan kode penyewa dan PIN 6 digit dari pengelola kost. Data sesi hanya
      tersimpan di perangkat Anda.</p>
      <div style="display:flex;gap:26px;align-items:flex-start">
        <div>{phone_shot('login', 272)}</div>
        <div style="flex:1">
          <ol class="steps">
            <li>Isi <b>kode penyewa</b>, contoh: pa0012.</li>
            <li>Isi <b>PIN</b> tepat 6 digit. Ketuk <b>Lihat</b> untuk memeriksa.</li>
            <li>Tekan <b>Masuk ke aplikasi</b>.</li>
          </ol>
          <div class="tip">Lupa PIN? Hubungi admin kost via WhatsApp — tautan kontak tersedia di Beranda setelah masuk.</div>
          <div class="tip"><b>Catatan keamanan:</b> jangan bagikan PIN kepada siapa pun, termasuk teman satu kamar. PIN bersifat pribadi seperti password.</div>
        </div>
      </div>
    ''')

    # ---- 5 · tutorial interaktif ----
    add_page('Tutorial interaktif', f'''
      <h2 class="sec">Tutorial interaktif</h2>
      <p class="lead">Saat pertama kali masuk, aplikasi memandu Anda langkah demi langkah dengan
      <b>demo langsung</b> — setiap bagian disorot dengan penjelasan singkat. Tutorial tampil
      otomatis satu kali dan dapat dibuka kapan saja dari tombol <b>Panduan</b>.</p>
      <div style="display:flex;gap:26px;align-items:flex-start">
        <div>{phone_shot('tour', 272)}</div>
        <div style="flex:1">
          <ul class="bul">
            <li><b>5 langkah:</b> status tagihan → Pembayaran → Pengumuman → Keluhan → navigasi bawah.</li>
            <li>Tekan <b>Lanjut / Kembali</b> untuk berpindah langkah.</li>
            <li>Tautan <b>Buka pembayaran</b> dkk. langsung mencoba fiturnya.</li>
            <li><b>Lewati</b> menutup panduan kapan pun.</li>
          </ul>
          <div class="tip">Setiap halaman juga punya tombol panduan masing-masing — ikon <b>info</b> di kanan atas, atau tombol <b>Panduan</b> di Beranda dan halaman login.</div>
        </div>
      </div>
    ''')

    # ---- 6 · beranda ----
    add_page('Beranda', f'''
      <h2 class="sec">Beranda</h2>
      <p class="lead">Halaman pertama setelah masuk. Berisi ringkasan kondisi sewa Anda.</p>
      <div style="display:flex;gap:26px;align-items:flex-start">
        <div>{phone_shot('dashboard', 290)}</div>
        <div style="flex:1">
          <ul class="bul">
            <li><b>Kartu status</b> — status pembayaran, total tagihan, dan tanggal jatuh tempo. Kartu berwarna merah bila terlambat.</li>
            <li><b>Perbarui</b> — memuat data pembayaran terbaru dari server.</li>
            <li><b>Akses cepat</b> — jalan pintas ke Pembayaran, Pengumuman, dan Keluhan.</li>
            <li><b>Ringkasan tagihan</b> — rincian periode sewa berjalan.</li>
            <li><b>Butuh bantuan?</b> — tombol WhatsApp langsung ke admin kost.</li>
          </ul>
          <div class="tip">Data terbaru dimuat otomatis di belakang layar setiap kali Beranda dibuka — tanpa perlu tarik-ulang.</div>
        </div>
      </div>
    ''')

    # ---- 7 · pembayaran ----
    add_page('Pembayaran', f'''
      <h2 class="sec">Pembayaran</h2>
      <p class="lead">Menu <b>Bayar</b> pada navigasi bawah. Dua bagian utama:</p>
      <div style="display:flex;gap:26px;align-items:flex-start">
        <div>{phone_shot('pembayaran', 285)}</div>
        <div style="flex:1">
          <ul class="bul">
            <li><b>Ringkasan periode ini</b> — status, tagihan, sudah dibayar, sisa pembayaran, dan jatuh tempo.</li>
            <li><b>Riwayat pembayaran</b> — semua transaksi tercatat: periode, tanggal, metode, total, denda, dan diskon.</li>
            <li>Titik hijau berarti <b>Lunas</b>; titik merah berarti <b>Belum Bayar</b>.</li>
          </ul>
          <div class="tip">Selesaikan pembayaran melalui admin kost — aplikasi memantau statusnya, bukan memproses transfer.</div>
        </div>
      </div>
    ''')

    # ---- 8 · pengumuman ----
    add_page('Pengumuman', f'''
      <h2 class="sec">Pengumuman</h2>
      <p class="lead">Menu <b>Info</b> pada navigasi bawah — kabar resmi dari pengelola kost.</p>
      <div style="display:flex;gap:20px;align-items:flex-start">
        <div>{phone_shot('pengumuman', 248)}</div>
        <div>{phone_shot('detail', 252)}</div>
        <div style="flex:1">
          <ol class="steps">
            <li>Ketuk salah satu pengumuman untuk membaca isi lengkap.</li>
            <li>Bagian <b>Diskusi</b> menampilkan komentar penghuni dan jawaban admin.</li>
            <li>Tulis pertanyaan pada kotak bawah, lalu ketuk ikon <b>kirim</b>.</li>
          </ol>
          <div class="tip">Komentar terkirim langsung ke server — jika koneksi terputus, pesan tidak hilang dan dapat dikirim ulang.</div>
        </div>
      </div>
    ''')

    # ---- 9 · keluhan daftar ----
    add_page('Keluhan', f'''
      <h2 class="sec">Keluhan — melaporkan kendala</h2>
      <p class="lead">Menu <b>Keluhan</b> pada navigasi bawah. Semua laporan Anda dan progresnya tampil di sini.</p>
      <div style="display:flex;gap:26px;align-items:flex-start">
        <div>{phone_shot('keluhan', 262)}</div>
        <div style="flex:1">
          <ul class="bul">
            <li><b>Baru</b> (kanan atas) — membuat laporan baru (dijelaskan pada halaman berikutnya).</li>
            <li>Setiap kartu menampilkan kategori, tanggal, status penanganan, dan jumlah tanggapan admin.</li>
            <li>Garis berwarna di kiri kartu menandai status: <b>Diproses</b> (biru), <b>Selesai</b> (hijau), perlu perhatian (merah).</li>
            <li>Ketuk kartu untuk membaca tanggapan admin dan melanjutkan percakapan.</li>
          </ul>
          <div class="tip">Cek halaman ini setelah melapor — tanggapan admin dan perubahan status muncul di sini tanpa perlu ditawar ulang.</div>
        </div>
      </div>
    ''')

    # ---- 10 · keluhan form ----
    add_page('Membuat keluhan baru', f'''
      <h2 class="sec">Membuat keluhan baru</h2>
      <p class="lead">Ketuk <b>Baru</b> pada halaman Keluhan — formulir laporan terbuka.</p>
      <div style="display:flex;gap:26px;align-items:flex-start">
        <div>{phone_shot('form', 265)}</div>
        <div style="flex:1">
          <ol class="steps">
            <li>Pilih <b>kategori</b> yang sesuai (Fasilitas, Kebersihan, Keamanan, Lainnya).</li>
            <li>Tulis <b>judul</b> singkat dan <b>uraian</b> selengkap mungkin: lokasi, waktu, kondisi.</li>
            <li>Lampirkan <b>foto</b> bila perlu — otomatis dikompres sebelum dikirim.</li>
            <li>Tekan <b>Kirim keluhan</b>, lalu pantau statusnya di daftar Keluhan.</li>
          </ol>
          <div class="tip">Semakin lengkap uraian dan foto, semakin cepat admin memahami dan menindak kendala Anda.</div>
        </div>
      </div>
    ''')

    # ---- 11 · profil ----
    add_page('Profil', f'''
      <h2 class="sec">Profil</h2>
      <p class="lead">Menu <b>Saya</b> pada navigasi bawah — akun dan preferensi.</p>
      <div style="display:flex;gap:26px;align-items:flex-start">
        <div>{phone_shot('profil', 260)}</div>
        <div style="flex:1">
          <ul class="bul">
            <li><b>Tampilan</b> — tema Terang, Gelap, atau mengikuti Sistem.</li>
            <li><b>Panduan penggunaan</b> — membuka kembali tutorial interaktif.</li>
            <li><b>Keamanan akun</b> — ganti PIN (lama &amp; baru, masing-masing 6 digit, harus berbeda).</li>
            <li><b>Data diri</b> — identitas penyewa dan kost. Hubungi admin bila ada yang perlu diubah.</li>
            <li><b>Keluar dari akun</b> — wajib dilakukan saat meminjam perangkat orang lain.</li>
          </ul>
        </div>
      </div>
    ''')

    # ---- 12 · offline + update ----
    add_page('Offline & pembaruan', f'''
      <h2 class="sec">Menjalankan tanpa koneksi</h2>
      <p class="lead">Aplikasi menyimpan <b>shell</b> (tampilan dan data terakhir) di perangkat.</p>
      <ul class="bul">
        <li>Halaman yang sudah dibuka tetap tampil saat sinyal lemah.</li>
        <li>Jika halaman butuh server dan koneksi hilang, muncul halaman <b>"Koneksi terputus"</b> dengan tombol muat ulang.</li>
        <li>Begitu koneksi kembali, ketuk <b>Perbarui</b> di Beranda atau buka ulang halaman.</li>
      </ul>
      <h2 class="sec" style="margin-top:22px">Pembaruan otomatis</h2>
      <p class="lead">Versi baru diterapkan otomatis — <b>tanpa perlu install ulang</b>.</p>
      <ul class="bul">
        <li><b>PWA</b>: service worker mengunduh versi baru di latar belakang; cukup tutup dan buka aplikasi.</li>
        <li><b>APK</b>: unduh rilis terbaru dari GitHub Releases bila ada pembaruan.</li>
      </ul>
      <div class="tip"><b>Ciri versi baru aktif:</b> tampilan sedikit berbeda atau fitur baru muncul. Bila tampilan terasa "terjebak", tutup aplikasi lalu buka lagi sekali.</div>
      <h2 class="sec" style="margin-top:22px">Tanya jawab singkat</h2>
      <ul class="bul">
        <li><b>Tombol Install tidak muncul?</b> Pastikan lewat HTTPS dan belum terpasang. Di iPhone gunakan menu Bagikan Safari.</li>
        <li><b>Tagihan tidak muncul?</b> Tekan <b>Perbarui</b>; bila tetap kosong hubungi admin — data dari pengelola.</li>
        <li><b>Foto keluhan gagal?</b> Pilih ukuran lebih kecil; aplikasi memampatkan hingga lebar 1200px.</li>
        <li><b>PIN salah berulang?</b> Menunggu sejenak (pembatasan percobaan), atau minta reset PIN ke admin.</li>
      </ul>
    ''')

    # ---- 13 · penutup ----
    add_page('Penutup', f'''
      <h2 class="sec">Kontak dan dukungan</h2>
      <p class="lead">Semua pertanyaan teknis seputar akun dan tagihan dijawab oleh admin kost,
      bukan oleh aplikasi.</p>
      <ul class="bul">
        <li><b>WhatsApp admin</b> — tombol "Butuh bantuan?" di Beranda.</li>
        <li><b>Reset PIN</b> — minta langsung ke admin kost.</li>
        <li><b>Manual web admin</b> — dokumen admin.pdf / owner.pdf di situs yang sama.</li>
      </ul>
      <div class="tip" style="margin-top:24px">
        <b>Ringkas sehari pakai:</b> buka Beranda pagi → cek status kartu tagihan → ada kendala?
        Tulis di Keluhan (sertakan foto) → ada info baru? cek Pengumuman &amp; ikut diskusi →
        tanggal 25? pastikan Pembayaran "Lunas".
      </div>
      <div style="display:flex;justify-content:center;margin-top:44px">
        <img src="{LOGO}" style="width:96px;height:96px;border-radius:24px;border:1px solid {LINE};padding:14px;background:#fff">
      </div>
      <p style="text-align:center;color:{MUTED};font-size:12.5px;margin-top:14px">
        Pondok Huda · aplikasi penghuni<br>Dibuat dengan perhatian — selamat menempati kamar Anda.
      </p>
    ''')


def shot_page(idx, html):
    path = os.path.join(OUT, f'page-{idx:02d}.html')
    with open(path, 'w', encoding='utf-8') as fh:
        fh.write(html)
    png = os.path.join(OUT, f'page-{idx:02d}.png')
    firefox_shot(png, 'file://' + path, PAGE_W, PAGE_H)
    return png


def assemble(shots):
    build_pages()
    page_pngs = [shot_page(i + 1, html) for i, html in enumerate(PAGES)]
    pages = [Image.open(p).convert('RGB') for p in page_pngs]
    os.makedirs(os.path.dirname(DEST), exist_ok=True)
    # PNGs are captured at SCALE× the 96-dpi layout; telling Pillow the
    # effective DPI keeps the physical page size A4 while storing
    # SCALE× pixel density — sharp when zoomed or printed.
    pages[0].save(
        DEST, save_all=True, append_images=pages[1:], resolution=96.0 * SCALE,
        title='Panduan Aplikasi Penghuni Pondok Huda', author='Pondok Huda',
    )
    size_kb = os.path.getsize(DEST) // 1024
    print(f'PDF ready: {DEST} ({len(pages)} pages, {size_kb} KB)')
    for name, png in shots.items():
        im = Image.open(png)
        print(f'  shot {name}: {im.size[0]}x{im.size[1]}')


def main():
    if not FIREFOX:
        sys.exit('firefox not found')
    os.makedirs(OUT, exist_ok=True)

    css_candidates = sorted(glob.glob(os.path.join(ROOT, 'app', 'dist', 'assets', 'index-*.css')))
    if not css_candidates:
        sys.exit('app/dist assets missing — run `npm run build` in app/ first')
    shutil.copy(css_candidates[-1], os.path.join(OUT, 'app.css'))
    shutil.copy(os.path.join(ROOT, 'app', 'public', 'icons', 'icon-512.png'), os.path.join(OUT, 'icon-512.png'))

    shots = {}
    for name, (height, html) in SCREENS.items():
        shots[name] = write_and_shot(name, height, html)

    assemble(shots)


if __name__ == '__main__':
    main()
