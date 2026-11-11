import {
  createContext,
  useContext,
  useEffect,
  useState,
  type ReactNode,
} from 'react'
import { Button } from '../components/Ui'
import { Icon, type IconName } from '../components/Icon'

const TUTORIAL_VERSION = 'pondok-huda-tutorial-v1'

interface InstallPromptEvent extends Event {
  prompt: () => Promise<void>
  userChoice: Promise<{ outcome: 'accepted' | 'dismissed'; platform: string }>
}

interface TutorialContextValue {
  openTutorial: () => void
}

const TutorialContext = createContext<TutorialContextValue | null>(null)

const steps: Array<{
  icon: IconName
  eyebrow: string
  title: string
  body: string
  points: string[]
}> = [
  {
    icon: 'lock',
    eyebrow: 'Langkah 1',
    title: 'Masuk dengan akun penghuni',
    body: 'Gunakan kode penyewa dan PIN 6 digit yang diberikan oleh pengelola kost.',
    points: ['Kode biasanya diawali “pa”', 'Jangan bagikan PIN kepada orang lain'],
  },
  {
    icon: 'home',
    eyebrow: 'Langkah 2',
    title: 'Pantau kost dari Beranda',
    body: 'Beranda menampilkan status pembayaran, total tagihan, tanggal jatuh tempo, dan kontak admin.',
    points: ['Tekan ikon muat ulang untuk data terbaru', 'Buka Bayar untuk melihat riwayat lengkap'],
  },
  {
    icon: 'comment',
    eyebrow: 'Langkah 3',
    title: 'Informasi dan bantuan',
    body: 'Baca kabar kost di Info. Jika ada kerusakan atau kendala, kirim laporan melalui menu Keluhan.',
    points: ['Foto dapat dilampirkan pada keluhan', 'Pantau balasan dan status laporan di aplikasi'],
  },
  {
    icon: 'monitor',
    eyebrow: 'Langkah 4',
    title: 'Pasang untuk akses lebih cepat',
    body: 'Versi web dapat dipasang ke layar utama dan dibuka seperti aplikasi biasa.',
    points: ['Data utama tetap tersedia saat koneksi tidak stabil', 'Panduan ini bisa dibuka lagi dari menu Saya'],
  },
]

function hasSeenTutorial() {
  try {
    return localStorage.getItem(TUTORIAL_VERSION) === 'done'
  } catch {
    return false
  }
}

function isStandalone() {
  return window.matchMedia('(display-mode: standalone)').matches ||
    Boolean((navigator as Navigator & { standalone?: boolean }).standalone)
}

export function TutorialProvider({ children }: { children: ReactNode }) {
  const [open, setOpen] = useState(() => !hasSeenTutorial())
  const [step, setStep] = useState(0)
  const [installPrompt, setInstallPrompt] = useState<InstallPromptEvent | null>(null)
  const [installed, setInstalled] = useState(() => isStandalone())

  useEffect(() => {
    const capturePrompt = (event: Event) => {
      event.preventDefault()
      setInstallPrompt(event as InstallPromptEvent)
    }
    const markInstalled = () => {
      setInstalled(true)
      setInstallPrompt(null)
    }
    window.addEventListener('beforeinstallprompt', capturePrompt)
    window.addEventListener('appinstalled', markInstalled)
    return () => {
      window.removeEventListener('beforeinstallprompt', capturePrompt)
      window.removeEventListener('appinstalled', markInstalled)
    }
  }, [])

  useEffect(() => {
    if (!open) return
    const previousOverflow = document.body.style.overflow
    document.body.style.overflow = 'hidden'
    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === 'Escape') finish()
    }
    window.addEventListener('keydown', onKeyDown)
    return () => {
      document.body.style.overflow = previousOverflow
      window.removeEventListener('keydown', onKeyDown)
    }
  }, [open, step])

  function finish() {
    try {
      localStorage.setItem(TUTORIAL_VERSION, 'done')
    } catch {
      // Storage can be unavailable in private browser modes. Closing still works.
    }
    setOpen(false)
  }

  function openTutorial() {
    setStep(0)
    setOpen(true)
  }

  async function install() {
    if (!installPrompt) return
    await installPrompt.prompt()
    const choice = await installPrompt.userChoice
    if (choice.outcome === 'accepted') setInstalled(true)
    setInstallPrompt(null)
  }

  const current = steps[step]
  const lastStep = step === steps.length - 1
  const isiOS = /iphone|ipad|ipod/i.test(navigator.userAgent)

  return (
    <TutorialContext.Provider value={{ openTutorial }}>
      {children}
      {open && (
        <div className="fixed inset-0 z-[100] flex items-end justify-center bg-black/55 p-0 backdrop-blur-[2px] sm:items-center sm:p-5">
          <section
            role="dialog"
            aria-modal="true"
            aria-labelledby="tutorial-title"
            aria-describedby="tutorial-description"
            className="safe-bottom animate-ph-dialog-in w-full max-w-[460px] overflow-hidden rounded-t-[1.75rem] bg-surface-lowest text-on-surface shadow-[0_24px_80px_rgba(0,0,0,.35)] sm:rounded-[1.75rem]"
          >
            <div className="relative overflow-hidden bg-primary px-5 pb-6 pt-5 text-on-primary">
              <div className="pointer-events-none absolute -right-12 -top-16 size-44 rounded-full border-[26px] border-white/8" />
              <div className="relative flex items-start justify-between gap-4">
                <span className="flex size-12 items-center justify-center rounded-[1rem] border border-white/15 bg-white/12">
                  <Icon name={current.icon} size={25} />
                </span>
                <button
                  type="button"
                  onClick={finish}
                  className="min-h-10 rounded-full px-3 text-xs font-semibold text-on-primary/75 transition hover:bg-white/10 hover:text-on-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                >
                  Lewati
                </button>
              </div>
              <p className="relative mt-5 text-[11px] font-bold uppercase tracking-[.12em] text-on-primary/65">{current.eyebrow}</p>
              <h2 id="tutorial-title" className="relative mt-1.5 text-2xl font-semibold leading-tight tracking-[-.025em]">{current.title}</h2>
            </div>

            <div className="px-5 pb-5 pt-5">
              <p id="tutorial-description" className="text-sm leading-relaxed text-on-surface-variant">{current.body}</p>
              <ul className="mt-4 space-y-2.5">
                {current.points.map((point) => (
                  <li key={point} className="flex items-start gap-2.5 text-[13px] leading-relaxed text-on-surface">
                    <span className="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
                      <Icon name="check" size={12} />
                    </span>
                    {point}
                  </li>
                ))}
              </ul>

              {lastStep && !installed && installPrompt && (
                <Button type="button" variant="tonal" icon="monitor" onClick={install} className="mt-5 w-full">
                  Pasang Pondok Huda
                </Button>
              )}
              {lastStep && !installed && !installPrompt && isiOS && (
                <p className="mt-5 rounded-[.9rem] bg-surface-low px-3.5 py-3 text-xs leading-relaxed text-on-surface-variant">
                  Di iPhone atau iPad, tekan tombol Bagikan di Safari lalu pilih “Tambahkan ke Layar Utama”.
                </p>
              )}
              {lastStep && installed && (
                <p className="mt-5 flex items-center gap-2 rounded-[.9rem] bg-success-container px-3.5 py-3 text-xs font-semibold text-on-success-container">
                  <Icon name="check" size={17} /> Aplikasi sudah terpasang di perangkat ini.
                </p>
              )}

              <div className="mt-6 flex items-center justify-between gap-4">
                <div className="flex gap-1.5" aria-label={`Halaman ${step + 1} dari ${steps.length}`}>
                  {steps.map((_, index) => (
                    <span key={index} className={`h-1.5 rounded-full transition-all ${index === step ? 'w-6 bg-primary' : 'w-1.5 bg-outline-variant'}`} />
                  ))}
                </div>
                <div className="flex gap-2">
                  {step > 0 && (
                    <Button type="button" variant="text" onClick={() => setStep((value) => value - 1)}>
                      Kembali
                    </Button>
                  )}
                  <Button
                    autoFocus
                    type="button"
                    onClick={() => lastStep ? finish() : setStep((value) => value + 1)}
                  >
                    {lastStep ? 'Mulai gunakan' : 'Lanjut'}
                  </Button>
                </div>
              </div>
            </div>
          </section>
        </div>
      )}
    </TutorialContext.Provider>
  )
}

export function useTutorial() {
  const context = useContext(TutorialContext)
  if (!context) throw new Error('useTutorial must be used inside TutorialProvider')
  return context
}
