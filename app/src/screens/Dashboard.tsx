import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiPembayaran } from '../lib/normalizers'
import { updateSession } from '../lib/session'
import { daysLabel, money, waLink } from '../lib/format'
import { AlertBanner, Card, IconButton, PageHeader, SectionHeader, statusTone } from '../components/Ui'
import { Icon, type IconName } from '../components/Icon'

const actions: { to: string; label: string; sub: string; icon: IconName; tone: string }[] = [
  { to: '/bayar', label: 'Pembayaran', sub: 'Lihat riwayat', icon: 'wallet', tone: 'bg-primary-container text-on-primary-container' },
  { to: '/info', label: 'Pengumuman', sub: 'Kabar kost', icon: 'megaphone', tone: 'bg-secondary-container text-on-secondary-container' },
  { to: '/keluhan', label: 'Keluhan', sub: 'Lapor kendala', icon: 'comment', tone: 'bg-tertiary-container text-on-tertiary-container' },
]

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
  const tone = statusTone(b?.statusbayar)

  return (
    <div>
      <PageHeader title={p.namakost || 'PondokHuda'} sub={`Kamar ${p.nomorkamar} · ${p.nama}`} />
      <div className="page-gutter content-stack">
        <section className="relative overflow-hidden rounded-[1.75rem] bg-primary px-5 py-5 text-on-primary shadow-[0_20px_55px_color-mix(in_srgb,var(--ph-primary)_30%,transparent)]">
          <div className="pointer-events-none absolute -right-12 -top-20 size-56 rounded-full border-[34px] border-white/8" />
          <div className="pointer-events-none absolute -bottom-16 left-24 size-40 rounded-full bg-white/6 blur-2xl" />

          <div className="relative flex items-start justify-between gap-3">
            <div>
              <p className="text-xs font-semibold text-on-primary/70">Halo, {p.nama.split(' ')[0]}</p>
              <h2 className="mt-1 text-[1.65rem] font-black tracking-[-.045em]">{b?.statusbayar || 'Status tagihan'}</h2>
              {b && <p className="mt-1 text-sm font-medium text-on-primary/80">{daysLabel(b.sisaharibayar)}</p>}
            </div>
            <IconButton
              icon="refresh"
              label="Muat ulang pembayaran"
              onClick={refresh}
              disabled={refreshing}
              className={`bg-white/12 text-on-primary hover:bg-white/20 ${refreshing ? 'animate-spin' : ''}`}
            />
          </div>

          {b && (
            <div className="relative mt-6 grid grid-cols-2 gap-3">
              <div className="rounded-[1.1rem] border border-white/12 bg-white/10 p-3 backdrop-blur-sm">
                <p className="text-[10px] font-bold uppercase tracking-[.09em] text-on-primary/60">Total tagihan</p>
                <p className="money-value mt-1 text-lg font-black">{money(b.tagihantotal)}</p>
              </div>
              <div className="rounded-[1.1rem] border border-white/12 bg-white/10 p-3 backdrop-blur-sm">
                <p className="text-[10px] font-bold uppercase tracking-[.09em] text-on-primary/60">Jatuh tempo</p>
                <p className="mt-1 text-sm font-extrabold leading-snug">{b.nexttglbayar}</p>
              </div>
            </div>
          )}

          {b && (
            <div className="relative mt-4 flex items-center gap-2 text-xs font-semibold">
              <span className={`size-2 rounded-full ${tone === 'success' ? 'bg-[#a7f3b6]' : tone === 'error' ? 'bg-[#ffd0cc]' : 'bg-white/70'}`} />
              {tone === 'success' ? 'Pembayaran periode ini sudah aman' : 'Periksa detail pembayaran Anda'}
            </div>
          )}
        </section>

        {err && <AlertBanner action={<button onClick={refresh} className="font-bold underline">Coba lagi</button>}>{err}</AlertBanner>}

        <SectionHeader title="Akses cepat" sub="Yang paling sering Anda butuhkan" />
        <div className="grid grid-cols-3 gap-2.5">
          {actions.map((a) => (
            <Link
              key={a.to}
              to={a.to}
              className="group rounded-[1.25rem] bg-surface-lowest p-3 shadow-[0_8px_25px_rgba(15,45,42,.06)] transition duration-200 active:scale-[.97] focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/25"
            >
              <span className={`flex size-10 items-center justify-center rounded-[.9rem] ${a.tone}`}>
                <Icon name={a.icon} size={20} />
              </span>
              <p className="mt-3 truncate text-[12px] font-extrabold text-on-surface">{a.label}</p>
              <p className="mt-0.5 truncate text-[10px] text-on-surface-variant">{a.sub}</p>
            </Link>
          ))}
        </div>

        {r && (
          <>
            <SectionHeader title="Ringkasan tagihan" />
            <Card variant="elevated">
              <ul className="divide-y divide-outline-variant/45">
                {[r.line1, r.line2, r.line3, r.line4, r.line5, r.line6]
                  .filter(Boolean)
                  .map((line, index) => (
                    <li key={index} className="flex gap-3 py-2.5 first:pt-0 last:pb-0">
                      <span className="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
                        <Icon name="check" size={12} />
                      </span>
                      <span className="text-[13px] leading-relaxed text-on-surface-variant">{line}</span>
                    </li>
                  ))}
              </ul>
            </Card>
          </>
        )}

        {session.kontak && session.kontak.length > 0 && (
          <>
            <SectionHeader title="Butuh bantuan?" sub="Admin kost siap dihubungi" />
            <Card variant="outlined" className="!p-2">
              {session.kontak.map((contact, index) => (
                <a
                  key={index}
                  href={waLink(contact.wasap, `Halo ${contact.namaadmin}, saya ${p.nama} kamar ${p.nomorkamar}`)}
                  target="_blank"
                  rel="noreferrer"
                  className="flex min-h-16 items-center gap-3 rounded-[1rem] px-3 transition hover:bg-surface-low focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/20"
                >
                  <span className="flex size-10 items-center justify-center rounded-full bg-[#d8f4de] text-[#155d2a]">
                    <Icon name="phone" size={19} />
                  </span>
                  <span className="min-w-0 flex-1">
                    <span className="block truncate text-sm font-bold text-on-surface">{contact.namaadmin}</span>
                    <span className="block text-xs text-on-surface-variant">Chat via WhatsApp</span>
                  </span>
                  <Icon name="chevronRight" size={18} className="text-outline" />
                </a>
              ))}
            </Card>
          </>
        )}
      </div>
    </div>
  )
}
