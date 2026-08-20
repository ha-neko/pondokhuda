import { NavLink, Outlet, useLocation } from 'react-router'
import { Icon, type IconName } from './Icon'
import { useSessionContext } from '../lib/session-context'

const tabs: { to: string; label: string; icon: IconName }[] = [
  { to: '/', label: 'Beranda', icon: 'home' },
  { to: '/bayar', label: 'Bayar', icon: 'wallet' },
  { to: '/info', label: 'Info', icon: 'megaphone' },
  { to: '/keluhan', label: 'Keluhan', icon: 'comment' },
  { to: '/saya', label: 'Saya', icon: 'person' },
]

export function AppShell() {
  const { pathname } = useLocation()
  const { session } = useSessionContext()
  const hideNav = pathname.startsWith('/keluhan/baru') || pathname.startsWith('/info/')

  return (
    <div className="app-frame mx-auto flex min-h-dvh w-full max-w-2xl flex-col bg-surface lg:my-4 lg:min-h-[calc(100dvh-2rem)] lg:overflow-hidden lg:rounded-[2rem] lg:shadow-[0_24px_80px_rgba(8,39,37,.16)]">
      <main className={`flex-1 ${!hideNav && session ? 'pb-28' : 'pb-8'}`}>
        <Outlet />
      </main>
      {!hideNav && session && (
        <nav aria-label="Navigasi utama" className="pointer-events-none fixed inset-x-0 bottom-0 z-30 px-3 pb-[max(.7rem,env(safe-area-inset-bottom))]">
          <div className="pointer-events-auto mx-auto grid max-w-[640px] grid-cols-5 items-center rounded-[1.35rem] border border-outline-variant/50 bg-surface-lowest/92 px-1.5 py-1.5 shadow-[0_16px_45px_rgba(8,39,37,.2)] backdrop-blur-xl">
            {tabs.map((t) => (
              <NavLink
                key={t.to}
                to={t.to}
                end={t.to === '/'}
                className={({ isActive }) =>
                  `group relative flex min-w-0 flex-col items-center gap-0.5 rounded-[1rem] py-1.5 text-[10px] font-semibold transition duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/30 ${
                    isActive ? 'text-primary' : 'text-on-surface-variant'
                  }`
                }
              >
                {({ isActive }) => (
                  <>
                    <span
                      className={`flex h-8 w-12 items-center justify-center rounded-full transition-all duration-200 ${
                        isActive ? 'bg-primary-container text-on-primary-container shadow-sm' : 'group-active:scale-90'
                      }`}
                    >
                      <Icon name={t.icon} size={22} />
                    </span>
                    <span>{t.label}</span>
                  </>
                )}
              </NavLink>
            ))}
          </div>
        </nav>
      )}
    </div>
  )
}
