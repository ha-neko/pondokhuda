// Formatting helpers. Rupiah strings arrive inconsistent ("Rp ", "Rp. ", numbers).

export function money(v: number | string | undefined | null): string {
  if (v === undefined || v === null || v === '') return '-'
  if (typeof v === 'number') return formatRupiah(v)
  const s = String(v).trim()
  if (/^-?\d+$/.test(s)) return formatRupiah(Number(s))
  return s
}

export function formatRupiah(n: number): string {
  const sign = n < 0 ? '-' : ''
  const abs = Math.abs(Math.round(n))
  return `${sign}Rp ${abs.toLocaleString('id-ID')}`
}

export function stripRupiah(v: number | string | undefined | null): number {
  if (v === undefined || v === null || v === '') return 0
  if (typeof v === 'number') return v
  const n = parseFloat(String(v).replace(/[^\d.-]/g, ''))
  return Number.isFinite(n) ? n : 0
}

export function formatDate(s: string | undefined | null): string {
  if (!s) return '-'
  return s
}

export function daysLabel(n: number | string | undefined): string {
  const d = Number(n)
  if (!Number.isFinite(d)) return '-'
  if (d === 0) return 'Hari ini jatuh tempo'
  if (d > 0) return `${d} hari lagi`
  return `Terlambat ${Math.abs(d)} hari`
}

export const WA_BASE = 'https://wa.me/'

export function waLink(phone: string, text?: string): string {
  const p = phone.replace(/[^\d]/g, '')
  return `${WA_BASE}${p}${text ? `?text=${encodeURIComponent(text)}` : ''}`
}

/** Compress an image file to a base64 JPEG string (no data-URL prefix), max 1200px. */
export function fileToBase64Jpeg(file: File, maxDim = 1200, quality = 0.82): Promise<string> {
  return new Promise((resolve, reject) => {
    const url = URL.createObjectURL(file)
    const img = new Image()
    img.onload = () => {
      URL.revokeObjectURL(url)
      const scale = Math.min(1, maxDim / Math.max(img.width, img.height))
      const w = Math.round(img.width * scale)
      const h = Math.round(img.height * scale)
      const canvas = document.createElement('canvas')
      canvas.width = w
      canvas.height = h
      const ctx = canvas.getContext('2d')
      if (!ctx) return reject(new Error('canvas unavailable'))
      ctx.drawImage(img, 0, 0, w, h)
      resolve(canvas.toDataURL('image/jpeg', quality).split(',')[1] ?? '')
    }
    img.onerror = () => {
      URL.revokeObjectURL(url)
      reject(new Error('gagal membaca gambar'))
    }
    img.src = url
  })
}