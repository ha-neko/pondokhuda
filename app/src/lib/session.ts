import type { Session } from './types'

const KEY = 'ph_session'
const THEME_KEY = 'ph_theme'

export function loadSession(): Session | null {
  try {
    const raw = localStorage.getItem(KEY)
    if (!raw) return null
    const s = JSON.parse(raw) as Session
    if (!s?.kode || !s?.pin || !s?.profile) return null
    return s
  } catch {
    return null
  }
}

export function saveSession(s: Session): void {
  localStorage.setItem(KEY, JSON.stringify(s))
}

export function updateSession(patch: Partial<Session>): Session | null {
  const cur = loadSession()
  if (!cur) return null
  const next = { ...cur, ...patch }
  saveSession(next)
  return next
}

export function clearSession(): void {
  localStorage.removeItem(KEY)
}

export type ThemeMode = 'light' | 'dark' | 'system'

export function loadTheme(): ThemeMode {
  const t = localStorage.getItem(THEME_KEY) as ThemeMode | null
  return t === 'light' || t === 'dark' || t === 'system' ? t : 'system'
}

export function saveTheme(t: ThemeMode): void {
  localStorage.setItem(THEME_KEY, t)
}

export function applyTheme(t: ThemeMode): void {
  const dark =
    t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)
  document.documentElement.classList.toggle('dark', dark)
  const meta = document.querySelector('meta[name="theme-color"]')
  if (meta) meta.setAttribute('content', dark ? '#0e1514' : '#00696d')
}

/** Apply the kost's primary color as the brand seed override. */
export function applyKostColor(primaryHex?: string): void {
  if (!primaryHex || !/^#([0-9a-f]{6})$/i.test(primaryHex)) return
  const root = document.documentElement
  root.style.setProperty('--ph-primary', primaryHex)
  const dark = root.classList.contains('dark')
  const isLight = luminance(primaryHex) > 0.4
  // Keep tenant brand colors, but invert their foreground with the active
  // appearance so branded cards/buttons change together with every surface.
  root.style.setProperty('--ph-on-primary', isLight !== dark ? '#101414' : '#ffffff')
  // tone down for inverse/container so text stays legible
  root.style.setProperty('--ph-inverse-primary', lighten(dark ? primaryHex : primaryHex, dark))
}

function luminance(hex: string): number {
  const n = parseInt(hex.slice(1), 16)
  const r = (n >> 16) & 255
  const g = (n >> 8) & 255
  const b = n & 255
  return (0.299 * r + 0.587 * g + 0.114 * b) / 255
}

function lighten(hex: string, dark: boolean): string {
  // crude tone-map: dark theme wants a brighter version, light theme a muted one
  const n = parseInt(hex.slice(1), 16)
  let r = (n >> 16) & 255
  let g = (n >> 8) & 255
  let b = n & 255
  const amount = dark ? 120 : -60
  r = clamp(r + amount)
  g = clamp(g + amount)
  b = clamp(b + amount)
  return `#${((r << 16) | (g << 8) | b).toString(16).padStart(6, '0')}`
}

function clamp(v: number): number {
  return Math.max(0, Math.min(255, v))
}
