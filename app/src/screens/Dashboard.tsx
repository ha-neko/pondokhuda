import { Link } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiPembayaran } from '../lib/normalizers'
import { updateSession } from '../lib/session'
import { daysLabel, money, waLink } from '../lib/format'
import { Badge, Button, Card, PageHeader, Spinner, statusTone } from '../components/Ui'
import { Icon } from '../components/Icon'
import { useEffect, useState } from 'react'

export default function Dashboard() {
  const { session, setSession } = useSessionContext()
  const [refreshing, setRefreshing] = useState(false)
  const [err, setErr] = useState('')

  async function refresh() {
    if (!session || refreshing) return
    setRefreshing(true)
    setErr('')
    const r = await apiPembayaran(session.kode, session.pin)
    setRefreshing(false)
    if (!r.ok) {
      setErr(r.error)
      return
    }
    const next = updateSession({ bayar: r.data.bayar, resume: r.data.resume })
    if (next) setSession(next)
  }

  useEffect(() => {
    if (session && !session.bayar) refresh()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [session?.kode])

  if (!session) return null
  const p = session.profile
  const b = session.bayar
  const r = session.resume

  return (
    <div>
      <PageHeader title={p.namakost || 'PondokHuda'} sub={`Kamar ${p.nomorkamar} · ${p.nama}`} />
      <div className="flex flex-col gap-3 px-4 pt-2">
        {/* status pembayaran hero */}
        <Card className="!bg-primary text-on-primary !rounded-xl relative overflow-hidden py-5">
          <div className="flex items-center justify-between gap-2">
            <div className="min-w-0">
              <p className="text-xs font-medium opacity-80">Halo, {p.nama.split(' ')[0]}</p>
              <p className="mt-1 text-xl font-bold">
                {b ? b.statusbayar : 'Status tagihan'}
              </p>
              {b && <p className="mt-1 text-sm opacity-90">{daysLabel(b.sisaharibayar)}</p>}
            </div>
            {b && (
              <Badge tone={statusTone(b.statusbayar)}>
                {String(b.statusbayar) === 'Lunas' ? 'Selesai' : 'Belum lunas'}
              </Badge>
            )}
          </div>
          {b && (
            <div className="mt-4 grid grid-cols-2 gap-3 border-t border-white/25 pt-4">
              <div>
                <p className="text-xs opacity-75">Tagihan</p>
                <p className="text-base font-semibold">{money(b.tagihantotal)}</p>
              </div>
              <div>
                <p className="text-xs opacity-75">Jatuh tempo</p>
                <p className="text-sm font-semibold">{b.nexttglbayar}</p>
              </div>
            </div>
          )}
          <Button
            variant="tonal"
            size="sm"
            className="absolute right-3 top-3 !bg-white/20 !text-on-primary"
            onClick={refresh}
            loading={refreshing}
            icon="refresh"
          >
            Muat ulang
          </Button>
        </Card>

        {err && <p className="text-sm text-error">{err}</p>}

        {/* ringkasan pembayaran */}
        {r && (
          <Card>
            <p className="mb-2 flex items-center gap-2 text-sm font-semibold text-on-surface">
              <Icon name="wallet" size={18} /> Ringkasan Tagihan
            </p>
            <ul className="flex flex-col gap-1.5 text-sm text-on-surface-variant">
              {[r.line1, r.line2, r.line3, r.line4, r.line5, r.line6]
                .filter(Boolean)
                .map((ln, i) => (
                  <li key={i} className="leading-snug">
                    {ln}
                  </li>
                ))}
            </ul>
          </Card>
        )}

        {/* aksi cepat */}
        <div className="grid grid-cols-2 gap-3">
          <Link to="/bayar">
            <Card className="flex items-center gap-3 !rounded-xl">
              <span className="flex size-11 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
                <Icon name="wallet" size={22} />
              </span>
              <div>
                <p className="font-semibold text-on-surface">Riwayat Bayar</p>
                <p className="text-xs text-on-surface-variant">Histori pembayaran</p>
              </div>
            </Card>
          </Link>
          <Link to="/info">
            <Card className="flex items-center gap-3 !rounded-xl">
              <span className="flex size-11 items-center justify-center rounded-full bg-secondary-container text-on-secondary-container">
                <Icon name="megaphone" size={22} />
              </span>
              <div>
                <p className="font-semibold text-on-surface">Pengumuman</p>
                <p className="text-xs text-on-surface-variant">Info terbaru</p>
              </div>
            </Card>
          </Link>
          <Link to="/keluhan">
            <Card className="flex items-center gap-3 !rounded-xl">
              <span className="flex size-11 items-center justify-center rounded-full bg-tertiary-container text-on-tertiary-container">
                <Icon name="comment" size={22} />
              </span>
              <div>
                <p className="font-semibold text-on-surface">Keluhan</p>
                <p className="text-xs text-on-surface-variant">Sampaikan masalah</p>
              </div>
            </Card>
          </Link>
          <Link to="/saya">
            <Card className="flex items-center gap-3 !rounded-xl">
              <span className="flex size-11 items-center justify-center rounded-full bg-surface-variant text-on-surface-variant">
                <Icon name="person" size={22} />
              </span>
              <div>
                <p className="font-semibold text-on-surface">Profil</p>
                <p className="text-xs text-on-surface-variant">Data diri & PIN</p>
              </div>
            </Card>
          </Link>
        </div>

        {/* kontak admin */}
        {session.kontak && session.kontak.length > 0 && (
          <Card className="!rounded-xl">
            <p className="mb-2 flex items-center gap-2 text-sm font-semibold text-on-surface">
              <Icon name="phone" size={18} /> Kontak Admin
            </p>
            <div className="flex flex-col gap-2">
              {session.kontak.map((k, i) => (
                <a
                  key={i}
                  href={waLink(k.wasap, `Halo ${k.namaadmin}, saya ${p.nama} kamar ${p.nomorkamar}`)}
                  target="_blank"
                  rel="noreferrer"
                  className="flex items-center justify-between rounded-lg bg-surface-variant px-3 py-2.5 text-sm"
                >
                  <span className="font-medium text-on-surface">{k.namaadmin}</span>
                  <span className="flex items-center gap-1 text-on-surface-variant">
                    {k.wasap} <Icon name="chevronRight" size={16} />
                  </span>
                </a>
              ))}
            </div>
          </Card>
        )}

        {refreshing && (
          <div className="flex items-center justify-center gap-2 py-2 text-sm text-on-surface-variant">
            <Spinner className="size-4" /> memuat data terbaru…
          </div>
        )}
      </div>
    </div>
  )
}