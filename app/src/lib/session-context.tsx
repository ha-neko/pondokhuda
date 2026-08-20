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
    const mq = window.matchMedia('(prefers-color-scheme: dark)')
    const onChange = () => applyTheme(theme)
    mq.addEventListener('change', onChange)
    return () => mq.removeEventListener('change', onChange)
  }, [theme])

  useEffect(() => {
    if (session) applyKostColor(session.profile.primary)
  }, [session])

  const setTheme = (t: ThemeMode) => {
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