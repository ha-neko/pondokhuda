import { BrowserRouter, Navigate, Route, Routes } from 'react-router'
import { SessionProvider, useSessionContext } from './lib/session-context'
import { AppShell } from './components/AppShell'
import Login from './screens/Login'
import Dashboard from './screens/Dashboard'
import Pembayaran from './screens/Pembayaran'
import Pengumuman from './screens/Pengumuman'
import PengumumanDetail from './screens/PengumumanDetail'
import Keluhan from './screens/Keluhan'
import KeluhanForm from './screens/KeluhanForm'
import Profil from './screens/Profil'

function Guard({ children }: { children: React.ReactNode }) {
  const { session } = useSessionContext()
  if (!session) return <Navigate to="/login" replace />
  return <>{children}</>
}

function LoginRedirect() {
  const { session } = useSessionContext()
  if (session) return <Navigate to="/" replace />
  return <Login />
}

export default function App() {
  return (
    <SessionProvider>
      <BrowserRouter>
        <Routes>
          <Route path="/login" element={<LoginRedirect />} />
          <Route
            element={
              <Guard>
                <AppShell />
              </Guard>
            }
          >
            <Route path="/" element={<Dashboard />} />
            <Route path="/bayar" element={<Pembayaran />} />
            <Route path="/info" element={<Pengumuman />} />
            <Route path="/info/:kode" element={<PengumumanDetail />} />
            <Route path="/keluhan" element={<Keluhan />} />
            <Route path="/keluhan/baru" element={<KeluhanForm />} />
            <Route path="/saya" element={<Profil />} />
          </Route>
          <Route path="*" element={<Navigate to="/" replace />} />
        </Routes>
      </BrowserRouter>
    </SessionProvider>
  )
}