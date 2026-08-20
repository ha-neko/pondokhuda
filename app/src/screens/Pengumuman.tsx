import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiPengumuman } from '../lib/normalizers'
import { AlertBanner, Badge, Empty, LoadingState, PageHeader, SectionHeader } from '../components/Ui'
import { Icon } from '../components/Icon'
import type { Pengumuman as PengumumanType } from '../lib/types'

export default function Pengumuman() {
  const { session } = useSessionContext()
  const [list, setList] = useState<PengumumanType[] | null>(null)
  const [err, setErr] = useState('')

  async function load() {
    if (!session) return
    setErr('')
    setList(null)
    const result = await apiPengumuman(session.kode)
    if (!result.ok) {
      setErr(result.error)
      return
    }
    setList(result.data)
  }

  useEffect(() => {
    load()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [session?.kode])

  if (!session) return null

  return (
    <div>
      <PageHeader title="Pengumuman" sub={session.profile.namakost} />
      <div className="page-gutter content-stack">
        <SectionHeader title="Kabar dari kost" sub="Informasi terbaru untuk seluruh penghuni" />
        {!list && !err && <LoadingState label="Memuat pengumuman" />}
        {err && <AlertBanner action={<button onClick={load} className="font-bold underline">Coba lagi</button>}>{err}</AlertBanner>}
        {list && list.length === 0 && <Empty title="Belum ada pengumuman" text="Kabar baru dari admin akan tampil di sini." icon="notice" />}
        {list && (
          <ul className="flex flex-col gap-3">
            {list.map((item, index) => (
              <li key={item.kode}>
                <Link
                  to={`/info/${item.kode}`}
                  className="group block rounded-[1.1rem] border border-outline-variant/35 bg-surface-lowest p-4 transition duration-200 hover:bg-surface-low active:scale-[.995] focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/25"
                >
                  <div className="flex gap-3.5">
                    <span className={`flex size-10 shrink-0 items-center justify-center rounded-[.85rem] ${index === 0 ? 'bg-primary text-on-primary' : 'bg-primary-container text-on-primary-container'}`}>
                      <Icon name="notice" size={20} />
                    </span>
                    <div className="min-w-0 flex-1">
                      <div className="flex items-center justify-between gap-2">
                        <time className="text-[10px] font-bold uppercase tracking-[.06em] text-primary">{item.tglpublish}</time>
                        {(item.chat?.length ?? 0) > 0 && <Badge tone="neutral">{item.chat!.length} komentar</Badge>}
                      </div>
                      <h3 className="mt-1.5 text-[15px] font-semibold leading-snug text-on-surface">{item.judul}</h3>
                      <p className="mt-1.5 line-clamp-2 text-[13px] leading-relaxed text-on-surface-variant">{item.berita}</p>
                      <div className="mt-3 flex items-center justify-between border-t border-outline-variant/40 pt-2.5 text-xs font-semibold text-primary">
                        <span>Buka pengumuman</span>
                        <Icon name="chevronRight" size={17} className="transition-transform group-hover:translate-x-0.5" />
                      </div>
                    </div>
                  </div>
                </Link>
              </li>
            ))}
          </ul>
        )}
      </div>
    </div>
  )
}
