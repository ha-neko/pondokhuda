import { useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router'
import { apiLogin } from '../lib/normalizers'
import { useSessionContext } from '../lib/session-context'
import { saveSession } from '../lib/session'
import { AlertBanner, Button, Field } from '../components/Ui'
import { Icon } from '../components/Icon'

export default function Login() {
  const nav = useNavigate()
  const { setSession } = useSessionContext()
  const [kode, setKode] = useState('')
  const [pin, setPin] = useState('')
  const [showPin, setShowPin] = useState(false)
  const [error, setError] = useState('')
  const [loading, setLoading] = useState(false)

  async function submit(e: FormEvent) {
    e.preventDefault()
    setError('')
    if (!kode.trim() || pin.length !== 6) {
      setError('Isi kode penyewa dan PIN 6 digit.')
      return
    }
    setLoading(true)
    const r = await apiLogin(kode.trim(), pin)
    setLoading(false)
    if (!r.ok) {
      setError(r.error)
      return
    }
    const s = {
      kode: kode.trim(),
      pin,
      profile: r.data.profile,
      bayar: r.data.bayar,
      resume: r.data.resume,
      kontak: r.data.kontak,
      savedAt: Date.now(),
    }
    saveSession(s)
    setSession(s)
    nav('/', { replace: true })
  }

  return (
    <div className="relative min-h-dvh overflow-hidden bg-[#062f30] text-white">
      <div className="pointer-events-none absolute -left-28 -top-24 size-80 rounded-full bg-[#55dce0]/14 blur-3xl" />
      <div className="pointer-events-none absolute -bottom-40 -right-24 size-96 rounded-full bg-[#a7f3d0]/10 blur-3xl" />

      <div className="relative mx-auto flex min-h-dvh max-w-[480px] flex-col px-5 pb-5 pt-[calc(1.5rem+env(safe-area-inset-top))] sm:pt-[calc(1.75rem+env(safe-area-inset-top))]">
        <div className="flex items-center gap-3 px-1">
          <span className="grid size-11 place-items-center rounded-[.9rem] border border-white/15 bg-white/9 backdrop-blur">
            <img src="/brand-logo-white.png" alt="" className="size-7 object-contain" />
          </span>
          <div>
            <p className="text-base font-semibold tracking-[-.01em]">Pondok Huda</p>
            <p className="text-xs text-white/55">Ruang nyaman untuk penghuni</p>
          </div>
        </div>

        <div className="mt-9 px-1">
          <span className="inline-flex items-center gap-1.5 rounded-full border border-white/10 bg-white/8 px-3 py-1 text-xs font-medium text-[#a8eff0]">
            <Icon name="check" size={14} /> Informasi kost dalam satu tempat
          </span>
          <h1 className="mt-4 max-w-sm text-[1.85rem] font-semibold leading-[1.14] tracking-[-.035em] sm:text-[2.1rem]">
            Pulang terasa lebih tenang.
          </h1>
          <p className="mt-3 max-w-sm text-sm leading-relaxed text-white/65">
            Pantau tagihan, baca kabar terbaru, dan sampaikan kendala tanpa perlu menunggu.
          </p>
        </div>

        <div className="mb-auto mt-7 rounded-[1.4rem] bg-surface px-5 py-5 text-on-surface shadow-[0_20px_56px_rgba(0,0,0,.22)]">
          <div className="mb-5">
            <p className="text-xs font-semibold uppercase tracking-[.1em] text-primary">Masuk akun</p>
            <h2 className="mt-1 text-xl font-semibold tracking-[-.02em]">Selamat datang</h2>
            <p className="mt-1 text-sm text-on-surface-variant">Gunakan kode penghuni dan PIN enam digit.</p>
          </div>

          <form onSubmit={submit} className="flex flex-col gap-4">
            <Field
              label="Kode penyewa"
              leadingIcon="person"
              value={kode}
              onChange={(e) => setKode(e.target.value)}
              placeholder="Contoh: pa0001"
              autoCapitalize="none"
              autoComplete="username"
              inputMode="text"
            />

            <div className="relative">
              <Field
                label="PIN"
                leadingIcon="lock"
                type={showPin ? 'text' : 'password'}
                value={pin}
                onChange={(e) => setPin(e.target.value.replace(/\D/g, '').slice(0, 6))}
                placeholder="••••••"
                inputMode="numeric"
                autoComplete="current-password"
                minLength={6}
                maxLength={6}
                className="pr-20 tracking-[.35em]"
                hint="PIN terdiri dari tepat 6 digit."
              />
              <button
                type="button"
                onClick={() => setShowPin((v) => !v)}
                className="absolute right-2 top-[31px] min-h-11 rounded-full px-3 text-xs font-bold text-primary focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/25"
                aria-label={showPin ? 'Sembunyikan PIN' : 'Tampilkan PIN'}
              >
                {showPin ? 'Sembunyi' : 'Lihat'}
              </button>
            </div>

            {error && <AlertBanner>{error}</AlertBanner>}

            <Button type="submit" size="lg" loading={loading} className="mt-1 w-full">
              Masuk ke aplikasi
            </Button>
          </form>

          <div className="mt-5 flex items-center justify-center gap-2 text-center text-xs text-on-surface-variant">
            <Icon name="lock" size={14} /> Data Anda hanya tersimpan di perangkat ini.
          </div>
        </div>
      </div>
    </div>
  )
}
