import { useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiUbahPin } from '../lib/normalizers'
import { clearSession, updateSession } from '../lib/session'
import { waLink } from '../lib/format'
import { Button, Card, Field, PageHeader } from '../components/Ui'
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

  const [pinLama, setPinLama] = useState('')
  const [pinBaru, setPinBaru] = useState('')
  const [pinErr, setPinErr] = useState('')
  const [pinOk, setPinOk] = useState('')
  const [savingPin, setSavingPin] = useState(false)

  if (!session) return null
  const p = session.profile

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
      setPinErr('PIN lama dan baru harus 6 digit.')
      return
    }
    if (pinLama === pinBaru) {
      setPinErr('PIN baru harus berbeda dari PIN lama.')
      return
    }
    setSavingPin(true)
    const r = await apiUbahPin(session.kode, pinLama, pinBaru)
    setSavingPin(false)
    if (!r.ok) {
      setPinErr(r.error)
      return
    }
    const next = updateSession({ pin: pinBaru })
    if (next) setSession(next)
    setPinOk('PIN berhasil diubah.')
    setPinLama('')
    setPinBaru('')
  }

  const rows: Array<[string, string]> = [
    ['Kode', p.kode],
    ['Kamar', p.nomorkamar],
    ['No. HP', p.nomorhp],
    ['Email', p.email],
    ['Periode bayar', p.periodebayar],
    ['Tempat kuliah/kerja', p.tempatkuliahkerja],
    ['Jurusan', p.jurusankuliah],
    ['Alamat rumah', p.alamatrumah],
    ['Kost', p.namakost],
    ['Alamat kost', p.alamatkost],
  ].filter((row): row is [string, string] => {
    const v = row[1]
    return Boolean(v) && v !== '0'
  })

  return (
    <div>
      <PageHeader title="Profil" sub={p.namakost} />
      <div className="flex flex-col gap-4 px-4 pt-2">
        {/* identitas */}
        <Card className="flex items-center gap-4 !rounded-xl">
          {p.urlfoto ? (
            <img
              src={p.urlfoto}
              alt={p.nama}
              className="size-16 rounded-full bg-surface-variant object-cover"
              onError={(e) => ((e.target as HTMLImageElement).style.display = 'none')}
            />
          ) : (
            <span className="flex size-16 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
              <Icon name="person" size={32} />
            </span>
          )}
          <div className="min-w-0">
            <h2 className="truncate text-lg font-bold text-on-surface">{p.nama}</h2>
            <p className="text-sm text-on-surface-variant">
              {p.kode} · Kamar {p.nomorkamar}
            </p>
            {p.email && (
              <a href={`mailto:${p.email}`} className="text-xs text-primary">
                {p.email}
              </a>
            )}
          </div>
        </Card>

        <Card className="!rounded-xl">
          <h3 className="mb-1 text-sm font-semibold text-on-surface">Mode tampilan</h3>
          <div className="grid grid-cols-3 gap-2">
            {themes.map((t) => (
              <button
                key={t.value}
                onClick={() => setTheme(t.value)}
                className={`flex flex-col items-center gap-1 rounded-lg py-2.5 text-sm font-medium transition-colors ${
                  theme === t.value
                    ? 'bg-primary-container text-on-primary-container'
                    : 'bg-surface-variant text-on-surface-variant'
                }`}
              >
                <Icon name={t.icon} size={20} />
                {t.label}
              </button>
            ))}
          </div>
        </Card>

        {/* ganti pin */}
        <Card className="!rounded-xl">
          <h3 className="mb-3 flex items-center gap-2 text-sm font-semibold text-on-surface">
            <Icon name="lock" size={16} /> Ganti PIN
          </h3>
          <form onSubmit={changePin} className="flex flex-col gap-3">
            <Field
              label="PIN lama"
              type="password"
              inputMode="numeric"
              value={pinLama}
              onChange={(e) => setPinLama(e.target.value.replace(/\D/g, '').slice(0, 6))}
              placeholder="6 digit"
            />
            <Field
              label="PIN baru"
              type="password"
              inputMode="numeric"
              value={pinBaru}
              onChange={(e) => setPinBaru(e.target.value.replace(/\D/g, '').slice(0, 6))}
              placeholder="6 digit"
            />
            {pinErr && <p className="text-sm text-error">{pinErr}</p>}
            {pinOk && <p className="text-sm text-primary">{pinOk}</p>}
            <Button type="submit" variant="tonal" loading={savingPin}>
              Simpan PIN Baru
            </Button>
          </form>
        </Card>

        {/* data diri */}
        <Card className="!rounded-xl">
          <h3 className="mb-2 text-sm font-semibold text-on-surface">Data Diri</h3>
          <dl className="flex flex-col gap-1.5">
            {rows.map(([k, v]) => (
              <div key={k} className="flex justify-between gap-4 text-sm">
                <dt className="shrink-0 text-on-surface-variant">{k}</dt>
                <dd className="text-right font-medium text-on-surface">{v}</dd>
              </div>
            ))}
          </dl>
          {p.nomorhportu && (
            <a
              href={waLink(p.nomorhportu, `Halo orang tua/wali ${p.nama}, saya dari kost ${p.namakost}`)}
              target="_blank"
              rel="noreferrer"
              className="mt-3 flex items-center gap-2 rounded-lg bg-surface-variant px-3 py-2.5 text-sm font-medium text-on-surface"
            >
              <Icon name="phone" size={16} /> Hubungi orang tua/wali ({p.namaortu ?? 'wali'})
            </a>
          )}
        </Card>

        <Card className="!rounded-xl">
          <button
            onClick={logout}
            className="flex w-full items-center justify-center gap-2 rounded-lg py-2.5 text-sm font-semibold text-error hover:bg-error-container"
          >
            <Icon name="logout" size={18} /> Keluar
          </button>
        </Card>

        <p className="pb-4 text-center text-xs text-on-surface-variant">
          PondokHuda — aplikasi penghuni · v0.1.0
        </p>
      </div>
    </div>
  )
}