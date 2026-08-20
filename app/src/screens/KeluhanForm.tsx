import { useEffect, useRef, useState, type FormEvent } from 'react'
import { useNavigate } from 'react-router'
import { useSessionContext } from '../lib/session-context'
import { apiKeluhanKategori, apiKeluhanTambah } from '../lib/normalizers'
import { fileToBase64Jpeg } from '../lib/format'
import { AlertBanner, Button, Card, Field, IconButton, PageHeader, SectionHeader, TextFieldArea } from '../components/Ui'
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
  const [fieldErr, setFieldErr] = useState<{ judul?: string; uraian?: string }>({})
  const [sending, setSending] = useState(false)
  const [processingPhoto, setProcessingPhoto] = useState(false)
  const fileRef = useRef<HTMLInputElement>(null)

  useEffect(() => {
    if (!session) return
    apiKeluhanKategori(session.kode, session.pin).then((result) => {
      const categories = result.ok && result.data.length ? result.data : FALLBACK_KATEGORI
      setKategoriList(categories)
      setKategori((current) => current && categories.includes(current) ? current : categories[0])
    })
  }, [session?.kode, session?.pin])

  if (!session) return null

  async function onPick(file: File | undefined) {
    if (!file) return
    setErr('')
    setProcessingPhoto(true)
    try {
      const value = await fileToBase64Jpeg(file)
      if (!value) throw new Error('empty')
      setFoto(value)
      setFotoName(file.name)
    } catch {
      setErr('Foto tidak dapat diproses. Coba gambar lain.')
    } finally {
      setProcessingPhoto(false)
    }
  }

  async function submit(e: FormEvent) {
    e.preventDefault()
    if (!session) return
    const errors = {
      judul: judul.trim() ? undefined : 'Judul keluhan wajib diisi.',
      uraian: uraian.trim() ? undefined : 'Ceritakan kendala yang Anda alami.',
    }
    setFieldErr(errors)
    setErr('')
    if (errors.judul || errors.uraian) return
    if (!kategori) {
      setErr('Pilih kategori keluhan.')
      return
    }
    setSending(true)
    const result = await apiKeluhanTambah(session.kode, judul.trim(), kategori, uraian.trim(), foto)
    setSending(false)
    if (!result.ok) {
      setErr(result.error)
      return
    }
    nav('/keluhan', { replace: true })
  }

  function removePhoto() {
    setFoto(null)
    setFotoName('')
    if (fileRef.current) fileRef.current.value = ''
  }

  return (
    <div>
      <PageHeader title="Keluhan baru" sub={`Kamar ${session.profile.nomorkamar}`} onBack={() => nav(-1)} />
      <form onSubmit={submit} className="page-gutter content-stack">
        <Card variant="elevated">
          <SectionHeader title="Apa yang terjadi?" sub="Pilih kategori yang paling sesuai" />
          <div className="mt-4 flex flex-wrap gap-2">
            {kategoriList.map((item) => (
              <button
                key={item}
                type="button"
                aria-pressed={kategori === item}
                onClick={() => setKategori(item)}
                className={`min-h-10 rounded-full px-4 text-xs font-semibold transition focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/25 ${kategori === item ? 'bg-primary text-on-primary shadow-sm' : 'border border-outline-variant bg-surface-lowest text-on-surface-variant'}`}
              >
                {item}
              </button>
            ))}
          </div>
        </Card>

        <Card variant="outlined" className="flex flex-col gap-4">
          <Field
            label="Judul keluhan"
            value={judul}
            onChange={(e) => { setJudul(e.target.value); setFieldErr((v) => ({ ...v, judul: undefined })) }}
            placeholder="Contoh: Air kamar mandi macet"
            error={fieldErr.judul}
          />
          <TextFieldArea
            label="Ceritakan kendalanya"
            value={uraian}
            onChange={(e) => { setUraian(e.target.value); setFieldErr((v) => ({ ...v, uraian: undefined })) }}
            placeholder="Jelaskan lokasi, waktu, dan kondisi kendala…"
            rows={5}
            error={fieldErr.uraian}
            hint="Semakin jelas informasinya, semakin cepat admin membantu."
          />
        </Card>

        <Card variant="outlined">
          <SectionHeader title="Foto pendukung" sub="Opsional · gambar akan dikompres otomatis" />
          <input ref={fileRef} type="file" accept="image/*" capture="environment" className="hidden" onChange={(e) => onPick(e.target.files?.[0])} />
          {foto ? (
            <div className="mt-4 flex items-center gap-3 rounded-[.9rem] bg-surface-low p-2.5">
              <img src={`data:image/jpeg;base64,${foto}`} alt="Pratinjau foto keluhan" className="size-16 rounded-[.8rem] object-cover" />
              <div className="min-w-0 flex-1">
                <p className="truncate text-sm font-bold text-on-surface">{fotoName || 'Foto keluhan'}</p>
                <p className="mt-0.5 text-xs text-on-surface-variant">{Math.round(foto.length / 1333)} KB · siap dikirim</p>
              </div>
              <IconButton type="button" icon="close" label="Hapus foto" onClick={removePhoto} className="text-error hover:bg-error-container" />
            </div>
          ) : (
            <button
              type="button"
              disabled={processingPhoto}
              onClick={() => fileRef.current?.click()}
              className="mt-4 flex min-h-22 w-full flex-col items-center justify-center gap-1.5 rounded-[.9rem] border border-dashed border-outline bg-surface-low/45 text-sm font-semibold text-on-surface-variant transition hover:border-primary hover:text-primary focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-primary/25"
            >
              <span className="flex size-10 items-center justify-center rounded-full bg-primary-container text-on-primary-container">
                <Icon name="camera" size={20} />
              </span>
              {processingPhoto ? 'Memproses foto…' : 'Ambil atau pilih foto'}
            </button>
          )}
        </Card>

        {err && <AlertBanner>{err}</AlertBanner>}
        <div className="sticky bottom-2 z-10 rounded-[1.05rem] bg-surface/92 p-2 backdrop-blur-xl">
          <Button type="submit" size="lg" loading={sending} className="w-full">Kirim keluhan</Button>
        </div>
      </form>
    </div>
  )
}
