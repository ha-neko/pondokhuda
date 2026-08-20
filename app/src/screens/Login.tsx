import { useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router'
import { apiLogin } from '../lib/normalizers'
import { useSessionContext } from '../lib/session-context'
import { saveSession } from '../lib/session'
import { Button, Field } from '../components/Ui'
import { Icon } from '../components/Icon'

export default function Login() {
  const nav = useNavigate()
  const { setSession } = useSessionContext()
  const [kode, setKode] = useState('')
  const [pin, setPin] = useState('')
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
    <div className="flex min-h-dvh flex-col px-6 pb-10">
      <div className="flex-1 flex flex-col justify-center">
        <div className="mb-8 flex flex-col items-center gap-3 text-center">
          <span className="flex size-20 items-center justify-center rounded-xl bg-primary text-on-primary shadow-md">
            <Icon name="home" size={44} />
          </span>
          <div>
            <h1 className="text-2xl font-bold text-on-surface">PondokHuda</h1>
            <p className="text-sm text-on-surface-variant">Masuk untuk melihat tagihan, pengumuman & keluhan</p>
          </div>
        </div>

        <form onSubmit={submit} className="flex flex-col gap-4">
          <Field
            label="Kode Penyewa"
            value={kode}
            onChange={(e) => setKode(e.target.value)}
            placeholder="contoh: pa0001"
            autoCapitalize="none"
            autoComplete="username"
            inputMode="text"
          />
          <Field
            label="PIN"
            type="password"
            value={pin}
            onChange={(e) => setPin(e.target.value.replace(/\D/g, '').slice(0, 6))}
            placeholder="6 digit"
            inputMode="numeric"
            autoComplete="current-password"
            minLength={6}
            maxLength={6}
          />
          {error && <p className="text-sm text-error">{error}</p>}
          <Button type="submit" size="lg" loading={loading} className="mt-2 w-full">
            Masuk
          </Button>
        </form>
      </div>

      <p className="mt-8 text-center text-xs text-on-surface-variant">
        Butuh bantuan? Hubungi admin kost Anda.
      </p>
    </div>
  )
}