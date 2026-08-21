import { useEffect, useState } from 'react'
import { useSessionContext } from '../lib/session-context'
import { apiPembayaran, apiPembayaranSilent } from '../lib/normalizers'
import { updateSession } from '../lib/session'
import { money, stripRupiah } from '../lib/format'
import { AlertBanner, Badge, Card, Empty, LoadingState, PageHeader, SectionHeader, statusTone } from '../components/Ui'

export default function Pembayaran() {
  const { session, setSession } = useSessionContext()
  const [loading, setLoading] = useState(!session?.bayar)
  const [err, setErr] = useState('')

  async function load(silent = false) {
    if (!session) return
    if (!silent) setLoading(true)
    setErr('')
    const result = silent
      ? await apiPembayaranSilent(session.kode, session.pin)
      : await apiPembayaran(session.kode, session.pin)
    if (!result.ok) {
      if (!silent) setErr(result.error)
      return
    }
    const next = updateSession({ bayar: result.data.bayar, resume: result.data.resume })
    if (next) setSession(next)
  }

  useEffect(() => {
    // stale-while-revalidate: cache langsung tampil, status segarkan di belakang
    load(!session?.bayar)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [session?.kode])

  if (!session) return null
  const bayar = session.bayar
  const history = bayar?.historibayar ?? []
  const remaining = stripRupiah(bayar?.sisabayarsebelumnya)

  return (
    <div>
      <PageHeader title="Pembayaran" sub={session.profile.namakost} />
      <div className="page-gutter content-stack">
        {loading ? (
          <LoadingState label="Memuat riwayat pembayaran" />
        ) : err ? (
          <AlertBanner action={<button onClick={() => load(false)} className="font-bold underline">Coba lagi</button>}>{err}</AlertBanner>
        ) : !bayar ? (
          <Empty title="Pembayaran belum tersedia" text="Hubungi admin kost jika data belum muncul." icon="wallet" />
        ) : (
          <>
            <section className="relative overflow-hidden rounded-[1.35rem] bg-on-surface px-4.5 py-4.5 text-surface shadow-[0_12px_34px_rgba(8,25,24,.16)]">
              <div className="absolute -right-12 -top-14 size-44 rounded-full bg-primary/25 blur-2xl" />
              <div className="relative flex items-start justify-between gap-3">
                <div>
                  <p className="text-[11px] font-medium text-surface/60">Status periode ini</p>
                  <h2 className="mt-1 text-xl font-semibold tracking-[-.025em]">{bayar.statusbayar}</h2>
                </div>
                <Badge tone={statusTone(bayar.statusbayar)}>{bayar.statusbayar}</Badge>
              </div>
              <div className="relative mt-5 grid grid-cols-2 gap-x-4 gap-y-4 border-t border-surface/15 pt-4">
                <Metric label="Tagihan" value={money(bayar.tagihantotal)} />
                <Metric label="Sudah dibayar" value={money(bayar.bayarsebelumnya)} />
                <Metric label="Sisa pembayaran" value={money(bayar.sisabayarsebelumnya)} danger={remaining > 0} />
                <Metric label="Jatuh tempo" value={bayar.nexttglbayar || '-'} small />
              </div>
            </section>

            <SectionHeader title="Riwayat pembayaran" sub={`${history.filter((i) => i.kode_bayar !== 'current').length} transaksi tercatat`} />
            {history.length === 0 ? (
              <Empty text="Belum ada transaksi yang tercatat pada akun ini." icon="wallet" />
            ) : (
              <ul className="relative flex flex-col gap-3 before:absolute before:bottom-7 before:left-[1.1rem] before:top-7 before:w-px before:bg-outline-variant/70">
                {history.map((item, index) => (
                  <li key={item.kode_bayar ?? index} className="relative pl-11">
                    <span className={`absolute left-2.5 top-6 z-10 size-[1.05rem] rounded-full border-[3px] border-surface ${statusTone(item.statusbayar) === 'success' ? 'bg-success' : 'bg-error'}`} />
                    <Card variant="elevated" className="!p-4">
                      <div className="flex items-start justify-between gap-3">
                        <div className="min-w-0">
                          <p className="text-[10px] font-bold uppercase tracking-[.08em] text-primary">
                            {item.kode_bayar === 'current'
                              ? 'Periode berjalan'
                              : `Pembayaran #${item.bayarke ?? history.length - index}`}
                          </p>
                          <h3 className="mt-1 truncate text-sm font-semibold text-on-surface">{item.periode_bayar || item.periodesewa}</h3>
                          <p className="mt-1 text-xs text-on-surface-variant">
                            {item.tanggal_bayar || item.tanggal_pembayaran}{item.metode ? ` · ${item.metode}` : ''}
                          </p>
                        </div>
                        <Badge tone={statusTone(item.statusbayar)}>{item.statusbayar}</Badge>
                      </div>
                      <div className="mt-4 flex items-end justify-between border-t border-outline-variant/45 pt-3">
                        <div>
                          <p className="text-[10px] font-semibold text-on-surface-variant">{item.tanggal_bayar ? 'Total dibayar' : 'Total tagihan'}</p>
                          <p className="money-value text-base font-semibold text-on-surface">{money(item.total_bayar)}</p>
                        </div>
                        <div className="flex flex-col items-end gap-1 text-[11px]">
                          {Number(item.denda) > 0 && <span className="text-error">Denda {money(item.denda)}</span>}
                          {Number(item.diskon) > 0 && <span className="text-success">Diskon {money(item.diskon)}</span>}
                        </div>
                      </div>
                    </Card>
                  </li>
                ))}
              </ul>
            )}
          </>
        )}
      </div>
    </div>
  )
}

function Metric({ label, value, danger, small }: { label: string; value: string; danger?: boolean; small?: boolean }) {
  return (
    <div>
      <p className="text-[10px] font-semibold text-surface/55">{label}</p>
      <p className={`money-value mt-1 font-semibold ${small ? 'text-sm leading-snug' : 'text-[15px]'} ${danger ? 'text-error-container' : 'text-surface'}`}>{value}</p>
    </div>
  )
}
