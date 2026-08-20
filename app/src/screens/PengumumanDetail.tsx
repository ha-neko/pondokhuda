import { useEffect, useState, type FormEvent } from 'react'
import { useNavigate, useParams } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiPengumuman, apiPengumumanChat } from '../lib/normalizers'
import { AlertBanner, Empty, IconButton, LoadingState, PageHeader, SectionHeader } from '../components/Ui'
import { Icon } from '../components/Icon'
import type { ChatRow, Pengumuman } from '../lib/types'

interface LocalChat extends ChatRow { _pending?: boolean }

export default function PengumumanDetail() {
  const { kode } = useParams<{ kode: string }>()
  const nav = useNavigate()
  const { session } = useSessionContext()
  const [item, setItem] = useState<Pengumuman | null>(null)
  const [chats, setChats] = useState<LocalChat[]>([])
  const [err, setErr] = useState('')
  const [sendErr, setSendErr] = useState('')
  const [msg, setMsg] = useState('')
  const [sending, setSending] = useState(false)

  useEffect(() => {
    if (!session || !kode) return
    let alive = true
    apiPengumuman(session.kode).then((result) => {
      if (!alive) return
      if (!result.ok) {
        setErr(result.error)
        return
      }
      const found = result.data.find((announcement) => announcement.kode === kode)
      setItem(found ?? null)
      setChats((found?.chat ?? []) as LocalChat[])
      if (!found) setErr('Pengumuman tidak ditemukan.')
    })
    return () => { alive = false }
  }, [session?.kode, kode])

  async function send(e: FormEvent) {
    e.preventDefault()
    if (!session || !item || !msg.trim() || sending) return
    const text = msg.trim()
    setSending(true)
    setSendErr('')
    setMsg('')
    setChats((current) => [...current, {
      kodchat: 'local', kodberita: item.kode, koduser: session.kode,
      keyuser: session.pin, dttime: new Date().toISOString(), msg: text, _pending: true,
    }])
    const result = await apiPengumumanChat(item.kode, session.kode, session.pin, text)
    setSending(false)
    if (!result.ok) {
      setChats((current) => current.filter((chat) => !chat._pending))
      setMsg(text)
      setSendErr(result.error)
    }
  }

  if (!session) return null

  return (
    <div>
      <PageHeader title="Detail pengumuman" onBack={() => nav(-1)} />
      <div className="page-gutter content-stack pb-3">
        {!item && !err && <LoadingState label="Memuat pengumuman" />}
        {err && !item && <AlertBanner>{err}</AlertBanner>}
        {item && (
          <>
            <article className="rounded-[1.2rem] border border-outline-variant/35 bg-surface-lowest p-5 shadow-[0_7px_22px_rgba(15,45,42,.055)]">
              <div className="flex items-center gap-3">
                <span className="flex size-10 items-center justify-center rounded-[.85rem] bg-primary text-on-primary">
                  <Icon name="notice" size={20} />
                </span>
                <div>
                  <p className="text-xs font-extrabold text-on-surface">Admin kost</p>
                  <time className="text-[11px] text-on-surface-variant">{item.tglpublish}</time>
                </div>
              </div>
              <h1 className="mt-5 text-[1.4rem] font-semibold leading-snug tracking-[-.025em] text-on-surface">{item.judul}</h1>
              <p className="mt-4 whitespace-pre-line text-sm leading-7 text-on-surface-variant">{item.berita}</p>
            </article>

            <SectionHeader title="Diskusi" sub={`${chats.length} komentar`} />
            {chats.length === 0 ? (
              <Empty title="Belum ada komentar" text="Mulai diskusi jika ada hal yang ingin ditanyakan." icon="comment" />
            ) : (
              <ul className="flex flex-col gap-2.5">
                {chats.map((chat, index) => {
                  const mine = chat.koduser === session.kode
                  return (
                    <li key={`${chat.kodchat}-${index}`} className={`flex ${mine ? 'justify-end' : 'justify-start'}`}>
                      <div className={`max-w-[84%] rounded-[1rem] px-3.5 py-2.5 text-[13px] leading-relaxed ${mine ? `rounded-br-sm bg-primary text-on-primary ${chat._pending ? 'opacity-60' : ''}` : 'rounded-bl-sm border border-outline-variant/50 bg-surface-lowest text-on-surface'}`}>
                        {!mine && <p className="mb-0.5 text-[10px] font-bold text-primary">Admin kost</p>}
                        <p>{chat.msg}</p>
                        <p className={`mt-1 text-right text-[10px] ${mine ? 'text-on-primary/65' : 'text-on-surface-variant'}`}>
                          {fmt(chat.dttime)}{chat._pending ? ' · mengirim…' : ''}
                        </p>
                      </div>
                    </li>
                  )
                })}
              </ul>
            )}

            {sendErr && <AlertBanner>{sendErr}</AlertBanner>}

            <form onSubmit={send} className="sticky bottom-2 z-10 mt-1 flex items-center gap-2 rounded-[1.1rem] border border-outline-variant/60 bg-surface-lowest/94 p-2 pl-4 shadow-[0_8px_26px_rgba(8,39,37,.13)] backdrop-blur-xl">
              <label htmlFor="announcement-comment" className="sr-only">Tulis komentar</label>
              <input
                id="announcement-comment"
                value={msg}
                onChange={(e) => setMsg(e.target.value)}
                placeholder="Tulis komentar…"
                className="min-h-11 min-w-0 flex-1 bg-transparent text-sm text-on-surface outline-none placeholder:text-on-surface-variant/60"
              />
              <IconButton
                type="submit"
                icon="send"
                label="Kirim komentar"
                disabled={!msg.trim() || sending}
                className="bg-primary text-on-primary hover:bg-primary"
              />
            </form>
          </>
        )}
      </div>
    </div>
  )
}

function fmt(value: string): string {
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString('id-ID', { day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' })
}
