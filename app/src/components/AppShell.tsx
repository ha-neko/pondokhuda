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

const noNavPaths = ['/login', '/keluhan/baru']

export function AppShell() {
  const { pathname } = useLocation()
  const { session } = useSessionContext()
  const hideNav = noNavPaths.some((p) => pathname.startsWith(p))

  return (
    <div className="mx-auto flex min-h-dvh w-full max-w-xl flex-col bg-surface">
      <main className="flex-1 pb-24">
        <Outlet />
      </main>
      {!hideNav && session && (
        <nav className="fixed inset-x-0 bottom-0 z-30 bg-surface pb-[env(safe-area-inset-bottom)]">
          <div className="mx-auto flex max-w-xl items-center justify-around rounded-t-xl bg-surface-low px-2 shadow-[0_-1px_0_var(--ph-outline-variant)]">
            {tabs.map((t) => (
              <NavLink
                key={t.to}
                to={t.to}
                end={t.to === '/'}
                className={({ isActive }) =>
                  `group relative flex min-w-16 flex-col items-center gap-0.5 py-2 text-[11px] font-medium transition-colors ${
                    isActive ? 'text-primary' : 'text-on-surface-variant'
                  }`
                }
              >
                {({ isActive }) => (
                  <>
                    <span
                      className={`flex h-8 w-14 items-center justify-center rounded-full transition-all ${
                        isActive ? 'bg-primary-container text-on-primary-container' : ''
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