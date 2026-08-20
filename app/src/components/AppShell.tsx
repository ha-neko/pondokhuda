import { NavLink, Outlet, useLocation } from 'react-router'
import { Icon, type IconName } from './Icon'
import { useSessionContext } from '../lib/session-context'

const tabs: { to: string; label: string; icon: IconName }[] = [
  { to: '/', label: 'Beranda', icon: 'home' },
  { to: '/bayar', label: 'Bayar', icon: 'wallet' },
  { to: '/info', label: 'Info', icon: 'notice' },
  { to: '/keluhan', label: 'Keluhan', icon: 'comment' },
  { to: '/saya', label: 'Saya', icon: 'person' },
]

export function AppShell() {
  const { pathname } = useLocation()
  const { session } = useSessionContext()
  const hideNav = pathname.startsWith('/keluhan/baru') || pathname.startsWith('/info/')

  return (
    <div className="app-frame mx-auto flex min-h-dvh w-full max-w-[640px] flex-col bg-surface lg:my-5 lg:min-h-[calc(100dvh-2.5rem)] lg:overflow-hidden lg:rounded-3xl lg:border lg:border-outline-variant/35 lg:shadow-[0_18px_60px_rgba(8,39,37,.12)]">
      <main className={`flex-1 ${!hideNav && session ? 'pb-24' : 'pb-7'}`}>
        <Outlet />
      </main>
      {!hideNav && session && (
        <nav aria-label="Navigasi utama" className="pointer-events-none fixed inset-x-0 bottom-0 z-30 px-3 pb-[max(.65rem,env(safe-area-inset-bottom))]">
          <div className="pointer-events-auto mx-auto grid max-w-[616px] grid-cols-5 items-center rounded-[1.15rem] border border-outline-variant/45 bg-surface-lowest/94 px-1 py-1 shadow-[0_10px_32px_rgba(8,39,37,.15)] backdrop-blur-xl">
            {tabs.map((t) => (
              <NavLink
                key={t.to}
                to={t.to}
                end={t.to === '/'}
                className={({ isActive }) =>
                  `group relative flex min-w-0 flex-col items-center gap-0.5 rounded-[.9rem] py-1.5 text-[11px] font-medium transition duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary/30 ${
                    isActive ? 'text-primary' : 'text-on-surface-variant'
                  }`
                }
              >
                {({ isActive }) => (
                  <>
                    <span
                      className={`flex h-7 w-11 items-center justify-center rounded-full transition-all duration-200 ${
                        isActive ? 'bg-primary-container text-on-primary-container' : 'group-active:scale-95'
                      }`}
                    >
                      <Icon name={t.icon} size={20} />
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
