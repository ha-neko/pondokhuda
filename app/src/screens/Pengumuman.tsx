import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiPengumuman } from '../lib/normalizers'
import { Badge, Card, Empty, PageHeader, Spinner } from '../components/Ui'
import { Icon } from '../components/Icon'
import type { Pengumuman } from '../lib/types'

export default function Pengumuman() {
  const { session } = useSessionContext()
  const [list, setList] = useState<Pengumuman[] | null>(null)
  const [err, setErr] = useState('')

  useEffect(() => {
    if (!session) return
    let alive = true
    apiPengumuman(session.kode).then((r) => {
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
  }, [session?.kode])

  if (!session) return null

  return (
    <div>
      <PageHeader title="Pengumuman" sub={session.profile.namakost} />
      <div className="flex flex-col gap-3 px-4 pt-2">
        {!list && !err && (
          <div className="flex items-center justify-center gap-2 py-16 text-sm text-on-surface-variant">
            <Spinner className="size-5" /> memuat pengumuman…
          </div>
        )}
        {err && <div className="py-10 text-center text-sm text-error">{err}</div>}
        {list && list.length === 0 && <Empty text="Belum ada pengumuman dari admin." />}
        {list &&
          list.map((p) => (
            <Link key={p.kode} to={`/info/${p.kode}`}>
              <Card className="!rounded-xl">
                <div className="mb-1 flex items-center justify-between gap-2">
                  <p className="text-xs font-medium text-primary">{p.tglpublish}</p>
                  {p.chat && p.chat.length > 0 && (
                    <Badge tone="neutral">{p.chat.length} komen</Badge>
                  )}
                </div>
                <h3 className="font-semibold text-on-surface">{p.judul}</h3>
                <p className="mt-1 line-clamp-2 text-sm text-on-surface-variant">{p.berita}</p>
                <div className="mt-2 flex items-center gap-1 text-xs font-medium text-primary">
                  Baca selengkapnya <Icon name="chevronRight" size={14} />
                </div>
              </Card>
            </Link>
          ))}
      </div>
    </div>
  )
}