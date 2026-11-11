import { useEffect, useLayoutEffect, useState } from 'react'
import { Button } from './Ui'
import { Icon, type IconName } from './Icon'

export const DASHBOARD_TOUR_KEY = 'pondok-huda-dashboard-tour-v1'

type TourStep = {
  target: string
  icon: IconName
  title: string
  description: string
  action?: string
}

const steps: TourStep[] = [
  {
    target: '[data-tour="status"]',
    icon: 'wallet',
    title: 'Cek status tagihan',
    description: 'Di sini terlihat status pembayaran, jumlah tagihan, dan tanggal jatuh tempo. Tombol di kanan atas memuat data terbaru.',
  },
  {
    target: '[data-tour="payment"]',
    icon: 'wallet',
    title: 'Lihat pembayaran',
    description: 'Buka menu Pembayaran untuk memeriksa rincian dan riwayat pembayaran kost.',
    action: 'Buka pembayaran',
  },
  {
    target: '[data-tour="notice"]',
    icon: 'notice',
    title: 'Baca pengumuman',
    description: 'Informasi terbaru dari pengelola kost tersedia di menu Pengumuman.',
    action: 'Buka pengumuman',
  },
  {
    target: '[data-tour="complaint"]',
    icon: 'comment',
    title: 'Laporkan kendala',
    description: 'Ada kerusakan atau masalah di kamar? Buka Keluhan, tulis keterangannya, lalu tambahkan foto jika diperlukan.',
    action: 'Buka keluhan',
  },
  {
    target: '[data-tour="navigation"]',
    icon: 'home',
    title: 'Berpindah halaman',
    description: 'Gunakan navigasi ini untuk membuka Beranda, Bayar, Info, Keluhan, atau profil Anda kapan saja.',
  },
]

type Box = { top: number; left: number; width: number; height: number }

function viewportSize() {
  return {
    width: document.documentElement.clientWidth,
    height: document.documentElement.clientHeight,
  }
}

export function DashboardTour({ onClose }: { onClose: () => void }) {
  const [step, setStep] = useState(0)
  const [box, setBox] = useState<Box | null>(null)
  const current = steps[step]

  function complete() {
    try {
      localStorage.setItem(DASHBOARD_TOUR_KEY, 'done')
    } catch {
      // The tour can still close when browser storage is unavailable.
    }
    onClose()
  }

  useLayoutEffect(() => {
    const target = document.querySelector<HTMLElement>(current.target)
    if (!target) return

    if (current.target !== '[data-tour="navigation"]') {
      target.scrollIntoView({ block: 'center', inline: 'nearest' })
    }

    const update = () => {
      const rect = target.getBoundingClientRect()
      const gap = 6
      const viewport = viewportSize()
      const left = Math.max(8, rect.left - gap)
      const top = Math.max(8, rect.top - gap)
      setBox({
        left,
        top,
        width: Math.min(viewport.width - left - 8, rect.width + gap * 2),
        height: Math.min(viewport.height - top - 8, rect.height + gap * 2),
      })
    }

    update()
    window.addEventListener('resize', update)
    window.addEventListener('scroll', update, true)
    return () => {
      window.removeEventListener('resize', update)
      window.removeEventListener('scroll', update, true)
    }
  }, [current.target])

  useEffect(() => {
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') complete()
      if (event.key === 'ArrowRight' && step < steps.length - 1) setStep((value) => value + 1)
      if (event.key === 'ArrowLeft' && step > 0) setStep((value) => value - 1)
    }
    window.addEventListener('keydown', onKeyDown)
    return () => window.removeEventListener('keydown', onKeyDown)
  })

  function tryTarget() {
    const target = document.querySelector<HTMLElement>(current.target)
    if (!target) return
    complete()
    target.click()
  }

  if (!box) return null
  const viewport = viewportSize()
  const tooltipWidth = Math.min(360, viewport.width - 24)
  const fitsBelow = box.top + box.height + 14 + 250 < viewport.height
  const tooltipTop = fitsBelow
    ? box.top + box.height + 12
    : Math.max(12, box.top - 262)
  const tooltipLeft = Math.min(
    viewport.width - tooltipWidth - 12,
    Math.max(12, box.left + box.width / 2 - tooltipWidth / 2),
  )

  return (
    <div role="dialog" aria-modal="true" aria-label="Panduan langsung aplikasi" className="pointer-events-none fixed inset-0 z-[80]">
      <div className="pointer-events-auto fixed inset-x-0 top-0 bg-black/72 backdrop-blur-[1px]" style={{ height: box.top }} />
      <div className="pointer-events-auto fixed inset-x-0 bottom-0 bg-black/72 backdrop-blur-[1px]" style={{ top: box.top + box.height }} />
      <div className="pointer-events-auto fixed left-0 bg-black/72 backdrop-blur-[1px]" style={{ top: box.top, width: box.left, height: box.height }} />
      <div className="pointer-events-auto fixed right-0 bg-black/72 backdrop-blur-[1px]" style={{ top: box.top, left: box.left + box.width, height: box.height }} />

      <div
        className="pointer-events-none fixed rounded-[1.25rem] border-2 border-primary-container shadow-[0_0_0_3px_color-mix(in_srgb,var(--ph-primary)_45%,transparent),0_0_32px_color-mix(in_srgb,var(--ph-primary)_55%,transparent)]"
        style={box}
      >
        <span className="absolute -right-2 -top-2 flex size-7 animate-pulse items-center justify-center rounded-full bg-primary-container text-on-primary-container shadow-lg">
          <Icon name="info" size={16} />
        </span>
      </div>

      <section
        className="pointer-events-auto animate-ph-dialog-in fixed rounded-[1.25rem] bg-surface-lowest p-4 text-on-surface shadow-[0_18px_60px_rgba(0,0,0,.4)]"
        style={{ top: tooltipTop, left: tooltipLeft, width: tooltipWidth }}
      >
        <div className="flex items-start gap-3">
          <span className="flex size-10 shrink-0 items-center justify-center rounded-[.85rem] bg-primary-container text-on-primary-container">
            <Icon name={current.icon} size={20} />
          </span>
          <div className="min-w-0 flex-1">
            <p className="text-[10px] font-bold uppercase tracking-[.1em] text-primary">Demo langsung · {step + 1}/{steps.length}</p>
            <h2 className="mt-1 text-base font-bold tracking-[-.01em]">{current.title}</h2>
          </div>
          <button type="button" onClick={complete} aria-label="Tutup panduan" className="flex size-9 shrink-0 items-center justify-center rounded-full text-on-surface-variant hover:bg-surface-low focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/30">
            <Icon name="close" size={18} />
          </button>
        </div>
        <p className="mt-3 text-[13px] leading-relaxed text-on-surface-variant">{current.description}</p>
        {current.action && (
          <button type="button" onClick={tryTarget} className="mt-2 text-xs font-bold text-primary underline decoration-primary/35 underline-offset-2">
            {current.action}
          </button>
        )}
        <div className="mt-4 flex items-center justify-between gap-3">
          <div className="flex gap-1" aria-hidden="true">
            {steps.map((_, index) => <span key={index} className={`h-1.5 rounded-full ${index === step ? 'w-5 bg-primary' : 'w-1.5 bg-outline-variant'}`} />)}
          </div>
          <div className="flex gap-1.5">
            {step > 0 && <Button type="button" size="sm" variant="text" onClick={() => setStep((value) => value - 1)}>Kembali</Button>}
            <Button type="button" size="sm" onClick={() => step === steps.length - 1 ? complete() : setStep((value) => value + 1)}>
              {step === steps.length - 1 ? 'Selesai' : 'Lanjut'}
            </Button>
          </div>
        </div>
      </section>
    </div>
  )
}
