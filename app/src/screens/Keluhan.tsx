import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiKeluhanList } from '../lib/normalizers'
import { Badge, Card, Empty, PageHeader, Spinner, statusTone } from '../components/Ui'
import { Icon } from '../components/Icon'
import type { Keluhan } from '../lib/types'

export default function Keluhan() {
  const { session } = useSessionContext()
  const [list, setList] = useState<Keluhan[] | null>(null)
  const [err, setErr] = useState('')

  useEffect(() => {
    if (!session) return
    let alive = true
    apiKeluhanList(session.kode, session.pin).then((r) => {
      if (!alive) return
      if (!r.ok) {
        setErr(r.error)
        return
      }
      setList(r.data)
    })
    return () => {
      alive = false
    }
  }, [session?.kode, session?.pin])

  if (!session) return null

  const latestStatus = (k: Keluhan): string | undefined => {
    const arr = k.status ?? []
    const last = arr[arr.length - 1]
    return last?.status
  }

  return (
    <div>
      <PageHeader
        title="Keluhan"
        sub={session.profile.namakost}
        right={
          <Link
            to="/keluhan/baru"
            className="flex items-center gap-1.5 rounded-full bg-primary px-4 py-2 text-sm font-semibold text-on-primary"
          >
            <Icon name="edit" size={16} /> Baru
          </Link>
        }
      />
      <div className="flex flex-col gap-3 px-4 pt-2">
        {!list && !err && (
          <div className="flex items-center justify-center gap-2 py-16 text-sm text-on-surface-variant">
            <Spinner className="size-5" /> memuat keluhan…
          </div>
        )}
        {err && <div className="py-10 text-center text-sm text-error">{err}</div>}
        {list && list.length === 0 && (
          <Empty text="Belum ada keluhan. Sampaikan jika ada yang perlu diperbaiki." />
        )}
        {list &&
          list.map((k) => {
            const st = latestStatus(k)
            return (
              <Card key={k.kode} className="!rounded-xl">
                <div className="flex items-center justify-between gap-2">
                  <Badge tone="primary">{k.kategori}</Badge>
                  <span className="text-xs text-on-surface-variant">{k.tgl}</span>
                </div>
                <h3 className="mt-2 font-semibold text-on-surface">{k.judul}</h3>
                <p className="mt-1 line-clamp-2 text-sm text-on-surface-variant">{k.uraian}</p>
                <div className="mt-2.5 flex items-center justify-between border-t border-outline-variant/50 pt-2.5">
                  {st ? (
                    <Badge tone={statusTone(String(st).toLowerCase().includes('selesai') ? 'success' : st)}>
                      {st}
                    </Badge>
                  ) : (
                    <Badge tone="neutral">Diajukan</Badge>
                  )}
                  {(k.chat?.length ?? 0) > 0 && (
                    <span className="flex items-center gap-1 text-xs text-on-surface-variant">
                      <Icon name="comment" size={14} /> {k.chat!.length} tanggapan
                    </span>
                  )}
                </div>
              </Card>
            )
          })}
      </div>
    </div>
  )
}