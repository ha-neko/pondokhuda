import { useEffect, useState } from 'react'
import { useSessionContext } from '../lib/session-context'
import { apiPembayaran } from '../lib/normalizers'
import { updateSession } from '../lib/session'
import { money } from '../lib/format'
import { Badge, Card, Empty, PageHeader, Spinner, statusTone } from '../components/Ui'
import { Icon } from '../components/Icon'

export default function Pembayaran() {
  const { session, setSession } = useSessionContext()
  const [loading, setLoading] = useState(!session?.bayar)
  const [err, setErr] = useState('')

  useEffect(() => {
    let alive = true
    async function run() {
      if (!session) return
      if (session.bayar) return
      setLoading(true)
      const r = await apiPembayaran(session.kode, session.pin)
      if (!alive) return
      setLoading(false)
      if (!r.ok) {
        setErr(r.error)
        return
      }
      const next = updateSession({ bayar: r.data.bayar, resume: r.data.resume })
      if (next) setSession(next)
    }
    run()
    return () => {
      alive = false
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [session?.kode])

  if (!session) return null
  const b = session.bayar
  const history = b?.historibayar ?? []
  const len = history.length

  return (
    <div>
      <PageHeader title="Pembayaran" sub={session.profile.namakost} />
      <div className="flex flex-col gap-3 px-4 pt-2">
        {loading ? (
          <div className="flex items-center justify-center gap-2 py-16 text-sm text-on-surface-variant">
            <Spinner className="size-5" /> memuat riwayat…
          </div>
        ) : err ? (
          <div className="py-10 text-center text-sm text-error">{err}</div>
        ) : !b ? (
          <Empty text="Data pembayaran belum tersedia. Tarik untuk memuat ulang." />
        ) : (
          <>
            {/* summary header */}
            <Card className="!rounded-xl">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-xs text-on-surface-variant">Status saat ini</p>
                  <p className="text-lg font-bold text-on-surface">{b.statusbayar}</p>
                </div>
                <Badge tone={statusTone(b.statusbayar)}>{b.statusbayar}</Badge>
              </div>
              <div className="mt-3 grid grid-cols-2 gap-3 border-t border-outline-variant/50 pt-3 text-sm">
                <div>
                  <p className="text-xs text-on-surface-variant">Tagihan</p>
                  <p className="font-semibold text-on-surface">{money(b.tagihantotal)}</p>
                </div>
                <div>
                  <p className="text-xs text-on-surface-variant">Total bayar</p>
                  <p className="font-semibold text-on-surface">{money(b.bayarsebelumnya)}</p>
                </div>
                <div>
                  <p className="text-xs text-on-surface-variant">Sisa bayar</p>
                  <p className="font-semibold text-error">{money(b.sisabayarsebelumnya)}</p>
                </div>
                <div>
                  <p className="text-xs text-on-surface-variant">Periode</p>
                  <p className="font-medium text-on-surface">{b.periodebayarbulan}</p>
                </div>
              </div>
            </Card>

            {/* history */}
            <h2 className="mt-1 px-1 text-sm font-semibold text-on-surface-variant">
              Riwayat <span className="font-normal">({len || '—'})</span>
            </h2>
            {len === 0 ? (
              <Empty text="Belum ada histori pembayaran." />
            ) : (
              <ul className="flex flex-col gap-2.5">
                {history.map((h, i) => {
                  const idxLabel = h.bayarke ? `#${h.bayarke}` : `#${len - i}`
                  return (
                    <li key={h.kode_bayar ?? i}>
                      <Card className="!rounded-xl">
                        <div className="flex items-start justify-between gap-2">
                          <div className="min-w-0">
                            <p className="flex items-center gap-1.5 font-semibold text-on-surface">
                              <span className="text-on-surface-variant">{idxLabel}</span> · {h.periode_bayar}
                            </p>
                            <p className="mt-0.5 text-xs text-on-surface-variant">
                              Dibayar {h.tanggal_bayar || h.tanggal_pembayaran}
                              {h.metode ? ` · ${h.metode}` : ''}
                            </p>
                          </div>
                          <Badge tone={statusTone(h.statusbayar)}>{h.statusbayar}</Badge>
                        </div>
                        <div className="mt-2.5 flex flex-wrap items-center gap-x-4 gap-y-1 border-t border-outline-variant/50 pt-2.5 text-sm">
                          <span className="text-on-surface-variant">
                            Total <b className="text-on-surface">{money(h.total_bayar)}</b>
                          </span>
                          {Number(h.denda) > 0 && (
                            <span className="flex items-center gap-1 text-error">
                              <Icon name="alert" size={14} /> denda {money(h.denda)}
                            </span>
                          )}
                          {(Number(h.diskon) || 0) > 0 && (
                            <span className="text-on-surface-variant">diskon {money(h.diskon)}</span>
                          )}
                        </div>
                      </Card>
                    </li>
                  )
                })}
              </ul>
            )}
          </>
        )}
      </div>
    </div>
  )
}