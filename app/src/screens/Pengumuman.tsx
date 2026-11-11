import { useEffect, useState } from 'react'
import { Link } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiPengumuman } from '../lib/normalizers'
import { AlertBanner, Badge, Empty, IconButton, LoadingState, PageHeader, SectionHeader } from '../components/Ui'
import { Icon } from '../components/Icon'
import type { Pengumuman as PengumumanType } from '../lib/types'
import { GuidedTour, shouldShowTour, type TourStep } from '../components/DashboardTour'

const TOUR_KEY = 'pondok-huda-tour-pengumuman-v1'
const tourSteps: TourStep[] = [
  { target: '[data-tour="notice-list"]', icon: 'notice', title: 'Daftar pengumuman', description: 'Kabar terbaru dari pengelola kost muncul di sini. Pengumuman paling baru berada di bagian atas.' },
  { target: '[data-tour="notice-item"]', icon: 'chevronRight', title: 'Buka informasi lengkap', description: 'Tekan salah satu pengumuman untuk membaca isi lengkap dan melihat diskusi penghuni.', action: 'Buka pengumuman' },
]

export default function Pengumuman() {
  const { session } = useSessionContext()
  const [list, setList] = useState<PengumumanType[] | null>(null)
  const [err, setErr] = useState('')
  const [showTour, setShowTour] = useState(() => shouldShowTour(TOUR_KEY))

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
      <PageHeader title="Pengumuman" sub={session.profile.namakost} right={<IconButton icon="info" label="Panduan pengumuman" onClick={() => setShowTour(true)} />} />
      <div className="page-gutter content-stack">
        <div data-tour="notice-list"><SectionHeader title="Kabar dari kost" sub="Informasi terbaru untuk seluruh penghuni" /></div>
        {!list && !err && <LoadingState label="Memuat pengumuman" />}
        {err && <AlertBanner action={<button onClick={load} className="font-bold underline">Coba lagi</button>}>{err}</AlertBanner>}
        {list && list.length === 0 && <Empty title="Belum ada pengumuman" text="Kabar baru dari admin akan tampil di sini." icon="notice" />}
        {list && (
          <ul className="flex flex-col gap-3">
            {list.map((item, index) => (
              <li key={item.kode}>
                <Link
                  to={`/info/${item.kode}`}
                  data-tour={index === 0 ? 'notice-item' : undefined}
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
      {showTour && list && <GuidedTour steps={list.length > 0 ? tourSteps : tourSteps.slice(0, 1)} storageKey={TOUR_KEY} onClose={() => setShowTour(false)} />}
    </div>
  )
}
