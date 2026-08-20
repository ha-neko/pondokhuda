import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiKeluhanList } from '../lib/normalizers'
import { AlertBanner, Badge, Empty, LoadingState, PageHeader, SectionHeader, statusTone } from '../components/Ui'
import { Icon } from '../components/Icon'
import type { Keluhan as KeluhanType } from '../lib/types'

export default function Keluhan() {
  const { session } = useSessionContext()
  const [list, setList] = useState<KeluhanType[] | null>(null)
  const [err, setErr] = useState('')

  async function load() {
    if (!session) return
    setList(null)
    setErr('')
    const result = await apiKeluhanList(session.kode, session.pin)
    if (!result.ok) {
      setErr(result.error)
      return
    }
    setList(result.data)
  }

  useEffect(() => {
    load()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [session?.kode, session?.pin])

  if (!session) return null

  function latestStatus(item: KeluhanType): string {
    const states = item.status ?? []
    return states[states.length - 1]?.status || 'Diajukan'
  }

  return (
    <div>
      <PageHeader
        title="Keluhan"
        sub={session.profile.namakost}
        right={
          <Link to="/keluhan/baru" className="inline-flex min-h-10 items-center gap-1.5 rounded-full bg-primary px-4 text-xs font-bold text-on-primary shadow-md focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/30">
            <Icon name="edit" size={15} /> Baru
          </Link>
        }
      />
      <div className="page-gutter content-stack">
        <SectionHeader title="Laporan Anda" sub="Pantau progres kendala yang sudah disampaikan" />
        {!list && !err && <LoadingState label="Memuat keluhan" />}
        {err && <AlertBanner action={<button onClick={load} className="font-bold underline">Coba lagi</button>}>{err}</AlertBanner>}
        {list && list.length === 0 && (
          <Empty
            title="Semua baik-baik saja"
            text="Belum ada keluhan. Jika menemukan kendala, admin siap membantu."
            icon="comment"
            action={<Link to="/keluhan/baru" className="text-sm font-bold text-primary">Buat keluhan</Link>}
          />
        )}
        {list && (
          <ul className="flex flex-col gap-3">
            {list.map((item) => {
              const state = latestStatus(item)
              const tone = statusTone(state)
              return (
                <li key={item.kode} className="relative overflow-hidden rounded-[1.35rem] bg-surface-lowest p-4 shadow-[0_9px_28px_rgba(15,45,42,.07)]">
                  <span className={`absolute inset-y-0 left-0 w-1 ${tone === 'success' ? 'bg-[#31a852]' : tone === 'error' ? 'bg-error' : 'bg-primary'}`} />
                  <div className="flex items-start justify-between gap-3 pl-1">
                    <div className="min-w-0">
                      <div className="flex flex-wrap items-center gap-2">
                        <Badge tone="primary">{item.kategori}</Badge>
                        <time className="text-[10px] text-on-surface-variant">{item.tgl}</time>
                      </div>
                      <h3 className="mt-2.5 text-[15px] font-extrabold leading-snug text-on-surface">{item.judul}</h3>
                      <p className="mt-1 line-clamp-2 text-[13px] leading-relaxed text-on-surface-variant">{item.uraian}</p>
                    </div>
                    <span className="flex size-10 shrink-0 items-center justify-center rounded-full bg-surface-low text-on-surface-variant">
                      <Icon name="comment" size={18} />
                    </span>
                  </div>
                  <div className="mt-4 flex items-center justify-between border-t border-outline-variant/40 pl-1 pt-3">
                    <Badge tone={tone}>{state}</Badge>
                    <span className="flex items-center gap-1 text-[11px] text-on-surface-variant">
                      <Icon name="comment" size={13} /> {(item.chat?.length ?? 0)} tanggapan
                    </span>
                  </div>
                </li>
              )
            })}
          </ul>
        )}
      </div>
    </div>
  )
}
