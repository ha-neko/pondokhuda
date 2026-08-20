import { useEffect, useRef, useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiKeluhanKategori, apiKeluhanTambah } from '../lib/normalizers'
import { fileToBase64Jpeg } from '../lib/format'
import { Button, Field, PageHeader, TextFieldArea } from '../components/Ui'
import { Icon } from '../components/Icon'

const FALLBACK_KATEGORI = ['Fasilitas', 'Kebersihan', 'Keamanan', 'Lainnya']

export default function KeluhanForm() {
  const nav = useNavigate()
  const { session } = useSessionContext()
  const [kategori, setKategori] = useState('')
  const [kategoriList, setKategoriList] = useState<string[]>(FALLBACK_KATEGORI)
  const [judul, setJudul] = useState('')
  const [uraian, setUraian] = useState('')
  const [foto, setFoto] = useState<string | null>(null)
  const [fotoName, setFotoName] = useState('')
  const [err, setErr] = useState('')
  const [sending, setSending] = useState(false)
  const fileRef = useRef<HTMLInputElement>(null)

  useEffect(() => {
    if (!session) return
    apiKeluhanKategori(session.kode, session.pin).then((r) => {
      if (r.ok && r.data.length > 0) {
        setKategoriList(r.data)
        setKategori((prev) => (prev && r.data.includes(prev) ? prev : r.data[0]))
      } else {
        setKategori(FALLBACK_KATEGORI[0])
      }
    })
  }, [session?.kode, session?.pin])

  if (!session) return null

  async function onPick(file: File | undefined) {
    if (!file) return
    setErr('')
    try {
      const b64 = await fileToBase64Jpeg(file)
      if (!b64) {
        setErr('Gagal memproses foto.')
        return
      }
      setFoto(b64)
      setFotoName(file.name)
    } catch {
      setErr('Gagal membaca foto.')
    }
  }

  async function submit(e: FormEvent) {
    e.preventDefault()
    if (!session) return
    setErr('')
    if (!judul.trim() || !uraian.trim()) {
      setErr('Judul dan uraian wajib diisi.')
      return
    }
    if (!kategori) {
      setErr('Pilih kategori keluhan.')
      return
    }
    setSending(true)
    const r = await apiKeluhanTambah(session.kode, judul.trim(), kategori, uraian.trim(), foto)
    setSending(false)
    if (!r.ok) {
      setErr(r.error)
      return
    }
    nav('/keluhan', { replace: true })
  }

  return (
    <div>
      <PageHeader title="Keluhan Baru" onBack={() => nav(-1)} />
      <form onSubmit={submit} className="flex flex-col gap-4 px-4 pt-2">
        <div className="flex flex-col gap-1.5">
          <label className="text-sm font-medium text-on-surface-variant">Kategori</label>
          <div className="flex flex-wrap gap-2">
            {kategoriList.map((k) => (
              <button
                key={k}
                type="button"
                onClick={() => setKategori(k)}
                className={`rounded-full px-4 py-2 text-sm font-medium transition-colors ${
                  kategori === k
                    ? 'bg-primary text-on-primary'
                    : 'bg-surface-variant text-on-surface-variant hover:bg-surface-high'
                }`}
              >
                {k}
              </button>
            ))}
          </div>
        </div>

        <Field
          label="Judul"
          value={judul}
          onChange={(e) => setJudul(e.target.value)}
          placeholder="contoh: Air kamar mandi macet"
        />
        <TextFieldArea
          label="Uraian"
          value={uraian}
          onChange={(e) => setUraian(e.target.value)}
          placeholder="Jelaskan masalahnya secara singkat…"
          rows={4}
        />

        <div className="flex flex-col gap-1.5">
          <span className="text-sm font-medium text-on-surface-variant">Foto (opsional)</span>
          <input
            ref={fileRef}
            type="file"
            accept="image/*"
            capture="environment"
            className="hidden"
            onChange={(e) => onPick(e.target.files?.[0])}
          />
          {foto ? (
            <div className="flex items-center gap-3 rounded-md border border-outline-variant bg-surface-lowest p-2.5">
              <img
                src={`data:image/jpeg;base64,${foto}`}
                alt="lampiran"
                className="size-14 rounded-sm object-cover"
              />
              <div className="min-w-0 flex-1">
                <p className="truncate text-sm text-on-surface">{fotoName || 'foto terlampir'}</p>
                <p className="text-xs text-on-surface-variant">{Math.round(foto.length / 1333)} KB</p>
              </div>
              <button
                type="button"
                onClick={() => {
                  setFoto(null)
                  setFotoName('')
                  if (fileRef.current) fileRef.current.value = ''
                }}
                className="flex size-9 items-center justify-center rounded-full text-error hover:bg-error-container"
              >
                <Icon name="close" size={18} />
              </button>
            </div>
          ) : (
            <button
              type="button"
              onClick={() => fileRef.current?.click()}
              className="flex h-14 items-center justify-center gap-2 rounded-md border border-dashed border-outline text-sm font-medium text-on-surface-variant hover:border-primary hover:text-primary"
            >
              <Icon name="camera" size={20} /> Ambil foto / unggah
            </button>
          )}
        </div>

        {err && <p className="text-sm text-error">{err}</p>}

        <Button type="submit" size="lg" loading={sending} className="mt-2 w-full">
          Kirim Keluhan
        </Button>
      </form>
    </div>
  )
}