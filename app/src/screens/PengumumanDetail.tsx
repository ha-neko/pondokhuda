import { useEffect, useState, type FormEvent } from 'react'
import { useNavigate, useParams } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiPengumuman, apiPengumumanChat } from '../lib/normalizers'
import { Button, Card, Empty, PageHeader, Spinner } from '../components/Ui'
import { Icon } from '../components/Icon'
import type { ChatRow, Pengumuman } from '../lib/types'

interface LocalChat extends ChatRow {
  _pending?: boolean
}

export default function PengumumanDetail() {
  const { kode } = useParams<{ kode: string }>()
  const nav = useNavigate()
  const { session } = useSessionContext()
  const [item, setItem] = useState<Pengumuman | null>(null)
  const [chats, setChats] = useState<LocalChat[]>([])
  const [err, setErr] = useState('')
  const [msg, setMsg] = useState('')
  const [sending, setSending] = useState(false)

  useEffect(() => {
    if (!session || !kode) return
    let alive = true
    apiPengumuman(session.kode).then((r) => {
      if (!alive) return
      if (!r.ok) {
        setErr(r.error)
        return
      }
      const found = r.data.find((p) => p.kode === kode)
      setItem(found ?? null)
      setChats((found?.chat ?? []) as LocalChat[])
      if (!found) setErr('Pengumuman tidak ditemukan.')
    })
    return () => {
      alive = false
    }
  }, [session?.kode, kode])

  async function send(e: FormEvent) {
    e.preventDefault()
    if (!session || !item || !msg.trim() || sending) return
    const text = msg.trim()
    setSending(true)
    setMsg('')
    setChats((c) => [
      ...c,
      {
        kodchat: 'local',
        kodberita: item.kode,
        koduser: session.kode,
        keyuser: session.pin,
        dttime: new Date().toISOString(),
        msg: text,
        _pending: true,
      },
    ])
    const r = await apiPengumumanChat(item.kode, session.kode, session.pin, text)
    setSending(false)
    if (!r.ok) {
      // roll back optimistic row
      setChats((c) => c.filter((x) => !x._pending))
      setMsg(text)
    }
  }

  if (!session) return null

  return (
    <div>
      <PageHeader title="Pengumuman" onBack={() => nav(-1)} />
      <div className="flex flex-col gap-3 px-4 pt-2">
        {err && !item && <div className="py-10 text-center text-sm text-error">{err}</div>}
        {item && (
          <>
            <Card className="!rounded-xl">
              <div className="mb-1 flex items-center gap-2">
                <p className="text-xs font-medium text-primary">{item.tglpublish}</p>
                {(item.chat?.length ?? 0) > 0 && (
                  <span className="text-xs text-on-surface-variant">
                    {item.chat!.length} komentar
                  </span>
                )}
              </div>
              <h2 className="text-lg font-bold text-on-surface">{item.judul}</h2>
              <p className="mt-2 whitespace-pre-line text-sm leading-relaxed text-on-surface-variant">
                {item.berita}
              </p>
            </Card>

            <div className="flex flex-col gap-2">
              <h3 className="px-1 text-sm font-semibold text-on-surface-variant">Diskusi</h3>
              {chats.length === 0 ? (
                <Empty text="Belum ada komentar. Jadilah yang pertama." />
              ) : (
                <ul className="flex flex-col gap-2">
                  {chats.map((c, i) => {
                    const mine = c.koduser === session.kode
                    return (
                      <li key={`${c.kodchat}-${i}`} className={mine ? 'flex justify-end' : 'flex justify-start'}>
                        <div
                          className={`max-w-[80%] rounded-2xl px-3.5 py-2 text-sm ${
                            mine
                              ? `rounded-br-sm bg-primary text-on-primary ${c._pending ? 'opacity-60' : ''}`
                              : 'rounded-bl-sm bg-surface-variant text-on-surface'
                          }`}
                        >
                          {!mine && (
                            <p className="text-[11px] font-semibold text-on-surface-variant">{c.koduser}</p>
                          )}
                          <p>{c.msg}</p>
                          <p className={`mt-0.5 text-right text-[10px] ${mine ? 'opacity-70' : 'text-on-surface-variant'}`}>
                            {fmt(dttime(c.dttime))}
                            {c._pending ? ' · mengirim…' : ''}
                          </p>
                        </div>
                      </li>
                    )
                  })}
                </ul>
              )}

              <form onSubmit={send} className="mt-1 flex items-center gap-2 rounded-full bg-surface-low p-1.5 pl-4">
                <input
                  value={msg}
                  onChange={(e) => setMsg(e.target.value)}
                  placeholder="Tulis komentar…"
                  className="min-w-0 flex-1 bg-transparent text-sm text-on-surface outline-none placeholder:text-on-surface-variant/70"
                />
                <Button type="submit" size="sm" loading={sending} disabled={!msg.trim()}>
                  <Icon name="send" size={16} />
                </Button>
              </form>
            </div>
          </>
        )}
        {!item && !err && (
          <div className="flex items-center justify-center gap-2 py-16 text-sm text-on-surface-variant">
            <Spinner className="size-5" /> memuat…
          </div>
        )}
      </div>
    </div>
  )
}

function dttime(s: string): string {
  const d = new Date(s)
  return Number.isNaN(d.getTime()) ? new Date().toISOString() : d.toISOString()
}

function fmt(iso: string): string {
  try {
    return new Date(iso).toLocaleString('id-ID', {
      day: '2-digit',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return ''
  }
}