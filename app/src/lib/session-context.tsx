import { createContext, useContext, useEffect, useMemo, useState, type ReactNode } from 'react'
import type { Session } from './types'
import { applyKostColor, applyTheme, loadSession, loadTheme, saveTheme, type ThemeMode } from './session'

interface Ctx {
  session: Session | null
  setSession: (s: Session | null) => void
  theme: ThemeMode
  setTheme: (t: ThemeMode) => void
}

const SessionCtx = createContext<Ctx | null>(null)

export function SessionProvider({ children }: { children: ReactNode }) {
  const [session, setSession] = useState<Session | null>(() => loadSession())
  const [theme, setThemeState] = useState<ThemeMode>(() => loadTheme())

  useEffect(() => {
    applyTheme(theme)
    if (session) applyKostColor(session.profile.primary)
    const mq = window.matchMedia('(prefers-color-scheme: dark)')
    const onChange = () => {
      applyTheme(theme)
      if (session) applyKostColor(session.profile.primary)
    }
    mq.addEventListener('change', onChange)
    return () => mq.removeEventListener('change', onChange)
  }, [theme, session])

  const setTheme = (t: ThemeMode) => {
    // Apply before React's next paint so every semantic foreground changes
    // together instead of leaving custom kost colors one frame behind.
    applyTheme(t)
    if (session) applyKostColor(session.profile.primary)
    setThemeState(t)
    saveTheme(t)
  }

  const value = useMemo(
    () => ({ session, setSession, theme, setTheme }),
    [session, theme],
  )

  return <SessionCtx.Provider value={value}>{children}</SessionCtx.Provider>
}

export function useSessionContext(): Ctx {
  const ctx = useContext(SessionCtx)
  if (!ctx) throw new Error('useSessionContext must be used within SessionProvider')
  return ctx
}
