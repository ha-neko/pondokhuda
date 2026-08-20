import { useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiUbahPin } from '../lib/normalizers'
import { clearSession, updateSession } from '../lib/session'
import { waLink } from '../lib/format'
import { AlertBanner, Button, Card, Field, PageHeader, SectionHeader } from '../components/Ui'
import { Icon } from '../components/Icon'
import type { ThemeMode } from '../lib/session'

const themes: { value: ThemeMode; label: string; icon: 'sun' | 'moon' | 'monitor' }[] = [
  { value: 'light', label: 'Terang', icon: 'sun' },
  { value: 'dark', label: 'Gelap', icon: 'moon' },
  { value: 'system', label: 'Sistem', icon: 'monitor' },
]

export default function Profil() {
  const nav = useNavigate()
  const { session, setSession, theme, setTheme } = useSessionContext()
  const [imageBroken, setImageBroken] = useState(false)
  const [pinLama, setPinLama] = useState('')
  const [pinBaru, setPinBaru] = useState('')
  const [pinErr, setPinErr] = useState('')
  const [pinOk, setPinOk] = useState('')
  const [savingPin, setSavingPin] = useState(false)

  if (!session) return null
  const profile = session.profile

  function logout() {
    clearSession()
    setSession(null)
    nav('/login', { replace: true })
  }

  async function changePin(e: FormEvent) {
    e.preventDefault()
    if (!session) return
    setPinErr('')
    setPinOk('')
    if (pinLama.length !== 6 || pinBaru.length !== 6) {
      setPinErr('PIN lama dan baru harus tepat 6 digit.')
      return
    }
    if (pinLama === pinBaru) {
      setPinErr('PIN baru harus berbeda dari PIN lama.')
      return
    }
    setSavingPin(true)
    const result = await apiUbahPin(session.kode, pinLama, pinBaru)
    setSavingPin(false)
    if (!result.ok) {
      setPinErr(result.error)
      return
    }
    const next = updateSession({ pin: pinBaru })
    if (next) setSession(next)
    setPinOk('PIN berhasil diperbarui.')
    setPinLama('')
    setPinBaru('')
  }

  const rows: Array<[string, string]> = [
    ['Kode penyewa', profile.kode], ['Nomor kamar', profile.nomorkamar],
    ['Nomor HP', profile.nomorhp], ['Email', profile.email],
    ['Periode bayar', profile.periodebayar], ['Tempat kuliah/kerja', profile.tempatkuliahkerja],
    ['Jurusan', profile.jurusankuliah], ['Alamat rumah', profile.alamatrumah],
    ['Nama kost', profile.namakost], ['Alamat kost', profile.alamatkost],
  ].filter((row): row is [string, string] => Boolean(row[1]) && row[1] !== '0')

  return (
    <div>
      <PageHeader title="Profil" sub="Akun dan preferensi" />
      <div className="page-gutter content-stack">
        <section className="relative overflow-hidden rounded-[1.65rem] bg-primary px-5 py-5 text-on-primary shadow-[0_18px_45px_color-mix(in_srgb,var(--ph-primary)_28%,transparent)]">
          <div className="absolute -right-16 -top-20 size-56 rounded-full border-[36px] border-white/8" />
          <div className="relative flex items-center gap-4">
            {profile.urlfoto && !imageBroken ? (
              <img src={profile.urlfoto} alt={`Foto ${profile.nama}`} className="size-20 rounded-[1.35rem] border-2 border-white/35 bg-white/10 object-cover shadow-xl" onError={() => setImageBroken(true)} />
            ) : (
              <span className="flex size-20 items-center justify-center rounded-[1.35rem] border border-white/20 bg-white/12">
                <Icon name="person" size={38} />
              </span>
            )}
            <div className="min-w-0">
              <p className="text-xs font-semibold text-on-primary/65">Penghuni kamar {profile.nomorkamar}</p>
              <h2 className="mt-1 truncate text-xl font-black tracking-[-.035em]">{profile.nama}</h2>
              <p className="mt-1 truncate text-xs text-on-primary/75">{profile.kode} · {profile.namakost}</p>
            </div>
          </div>
        </section>

        <SectionHeader title="Tampilan" sub="Pilih tema yang nyaman untuk mata" />
        <div className="grid grid-cols-3 gap-2 rounded-[1.25rem] bg-surface-low p-1.5">
          {themes.map((item) => (
            <button
              key={item.value}
              type="button"
              aria-pressed={theme === item.value}
              onClick={() => setTheme(item.value)}
              className={`flex min-h-16 flex-col items-center justify-center gap-1 rounded-[1rem] text-xs font-bold transition focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/25 ${theme === item.value ? 'bg-surface-lowest text-primary shadow-md' : 'text-on-surface-variant'}`}
            >
              <Icon name={item.icon} size={20} /> {item.label}
            </button>
          ))}
        </div>

        <SectionHeader title="Keamanan akun" sub="Perbarui PIN secara berkala" />
        <Card variant="elevated">
          <form onSubmit={changePin} className="flex flex-col gap-4">
            <Field label="PIN lama" leadingIcon="lock" type="password" inputMode="numeric" autoComplete="current-password" value={pinLama} onChange={(e) => setPinLama(e.target.value.replace(/\D/g, '').slice(0, 6))} placeholder="••••••" minLength={6} maxLength={6} />
            <Field label="PIN baru" leadingIcon="lock" type="password" inputMode="numeric" autoComplete="new-password" value={pinBaru} onChange={(e) => setPinBaru(e.target.value.replace(/\D/g, '').slice(0, 6))} placeholder="••••••" minLength={6} maxLength={6} />
            {pinErr && <AlertBanner>{pinErr}</AlertBanner>}
            {pinOk && <AlertBanner tone="success">{pinOk}</AlertBanner>}
            <Button type="submit" variant="tonal" loading={savingPin}>Simpan PIN baru</Button>
          </form>
        </Card>

        <SectionHeader title="Data diri" />
        <Card variant="outlined" className="!p-1.5">
          <dl className="divide-y divide-outline-variant/40">
            {rows.map(([label, value]) => (
              <div key={label} className="px-3 py-3">
                <dt className="text-[10px] font-bold uppercase tracking-[.07em] text-on-surface-variant">{label}</dt>
                <dd className="mt-1 break-words text-sm font-semibold leading-relaxed text-on-surface">{value}</dd>
              </div>
            ))}
          </dl>
        </Card>

        {profile.nomorhportu && (
          <a
            href={waLink(profile.nomorhportu, `Halo orang tua/wali ${profile.nama}, saya dari kost ${profile.namakost}`)}
            target="_blank"
            rel="noreferrer"
            className="flex min-h-16 items-center gap-3 rounded-[1.25rem] border border-outline-variant bg-surface-lowest px-4 focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/25"
          >
            <span className="flex size-10 items-center justify-center rounded-full bg-[#d8f4de] text-[#155d2a]"><Icon name="phone" size={19} /></span>
            <span className="min-w-0 flex-1">
              <span className="block text-sm font-bold text-on-surface">Hubungi orang tua / wali</span>
              <span className="block truncate text-xs text-on-surface-variant">{profile.namaortu || profile.nomorhportu}</span>
            </span>
            <Icon name="chevronRight" size={18} className="text-outline" />
          </a>
        )}

        <button
          type="button"
          onClick={logout}
          className="flex min-h-14 w-full items-center justify-center gap-2 rounded-[1.15rem] border border-error/25 bg-error-container/60 text-sm font-extrabold text-on-error-container transition active:scale-[.98] focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-error/25"
        >
          <Icon name="logout" size={18} /> Keluar dari akun
        </button>

        <p className="pb-3 text-center text-[10px] font-semibold uppercase tracking-[.12em] text-on-surface-variant/65">PondokHuda · aplikasi penghuni</p>
      </div>
    </div>
  )
}
