import { apiPost, type ApiResult } from './api'
import type {
  Keluhan,
  KontakAdmin,
  PembayaranInfo,
  Pengumuman,
  PenyewaProfile,
  ResumePembayaran,
  WireKeluhanKategori,
  WireKeluhanList,
  WireKeluhanTambah,
  WireLogin,
  WirePembayaran,
  WirePengumuman,
  WireUbahPin,
} from './types'

export interface LoginSuccess {
  profile: PenyewaProfile
  bayar?: PembayaranInfo
  resume?: ResumePembayaran
  kontak: KontakAdmin[]
}
export type LoginResult = ApiResult<LoginSuccess>

export function normalizeLogin(raw: WireLogin): LoginResult {
  const profile = raw.personalinfopenyewa?.[0]
  if (!profile) {
    const err = raw.personalinfo
    return {
      ok: false,
      error:
        err === 'inputkurang'
          ? 'Kode dan PIN harus diisi.'
          : err === 'tidak ada data'
            ? 'Kode atau PIN salah.'
            : 'Data tidak ditemukan pada sistem.',
      rawText: '',
    }
  }
  return {
    ok: true,
    data: {
      profile,
      bayar: raw.personalinfobayar,
      resume: raw.resumepembayaran,
      kontak: raw.kontakadmin ?? [],
    },
    rawText: '',
  }
}

export async function apiLogin(kode: string, pin: string): Promise<LoginResult> {
  const r = await apiPost<WireLogin>('login_ph.php', { kode, pin })
  if (!r.ok) return r
  return normalizeLogin(r.data)
}

export async function apiPembayaran(
  kode: string,
  pin: string,
): Promise<ApiResult<{ bayar: PembayaranInfo; resume: ResumePembayaran }>> {
  const r = await apiPost<WirePembayaran>('penyewa_pembayaran_getdata.php', {
    _kodepenyewa: kode,
    _nomorpin: pin,
  })
  if (!r.ok) return r
  if (!r.data.personalinfobayar) {
    return { ok: false, error: 'User tidak diketahui. Silakan hubungi admin kost.', rawText: r.rawText }
  }
  const data = { bayar: r.data.personalinfobayar, resume: r.data.resumepembayaran! }
  pembayaranMemo.set(`${kode}|${pin}`, { at: Date.now(), data })
  return { ok: true, data, rawText: r.rawText }
}

// Dedupe burst: Dashboard dan Pembayaran sama-sama revalidasi saat dibuka.
// Silent refresh dalam 15 detik memakai hasil terakhir, tidak menembak lagi.
const pembayaranMemo = new Map<string, { at: number; data: { bayar: PembayaranInfo; resume: ResumePembayaran } }>()
const PEMBAYARAN_SILENT_TTL = 15_000

export async function apiPembayaranSilent(
  kode: string,
  pin: string,
): Promise<ApiResult<{ bayar: PembayaranInfo; resume: ResumePembayaran }>> {
  const hit = pembayaranMemo.get(`${kode}|${pin}`)
  if (hit && Date.now() - hit.at < PEMBAYARAN_SILENT_TTL) {
    return { ok: true, data: hit.data, rawText: '' }
  }
  return apiPembayaran(kode, pin)
}

export async function apiPengumuman(kode: string): Promise<ApiResult<Pengumuman[]>> {
  const r = await apiPost<WirePengumuman>('penyewa_pengumuman_getdata.php', { _kode: kode })
  if (!r.ok) return r
  const list = r.data.pengumuman
  if (!list || typeof list === 'string') {
    return { ok: true, data: [], rawText: r.rawText }
  }
  return { ok: true, data: list, rawText: r.rawText }
}

export async function apiPengumumanChat(
  kodeBerita: string,
  kodeUser: string,
  keyUser: string,
  message: string,
): Promise<ApiResult<boolean>> {
  const r = await apiPost<{ chat?: unknown }>('penyewa_pengumuman_chat.php', {
    _kodeberita: kodeBerita,
    _kodeuser: kodeUser,
    _keyuser: keyUser,
    _message: message,
  })
  if (!r.ok) return r
  return { ok: true, data: true, rawText: r.rawText }
}

export async function apiKeluhanList(kode: string, pin: string): Promise<ApiResult<Keluhan[]>> {
  const r = await apiPost<WireKeluhanList>('keluhan_getdata3.php', { _kode: kode, _pin: pin })
  if (!r.ok) return r
  const invalid = r.data.getdatakeluhan
  if (invalid) {
    return { ok: false, error: 'Member tidak valid. Silakan login ulang.', rawText: r.rawText }
  }
  const list = r.data.keluhan
  if (!list || typeof list === 'string') {
    // "tidak ada data keluhan" -> empty list is fine
    return { ok: true, data: [], rawText: r.rawText }
  }
  return { ok: true, data: list, rawText: r.rawText }
}

export async function apiKeluhanKategori(
  kode: string,
  pin: string,
): Promise<ApiResult<string[]>> {
  const r = await apiPost<WireKeluhanKategori>('keluhan_getdata.php', { _kode: kode, _pin: pin })
  if (!r.ok) return { ok: false, error: r.error, rawText: r.rawText }
  const cats = r.data.kategorikeluhan ?? []
  return { ok: true, data: cats.map((c) => c.kategori).filter(Boolean), rawText: r.rawText }
}

export async function apiKeluhanTambah(
  penyewa: string,
  judul: string,
  kategori: string,
  uraian: string,
  fotoBase64: string | null,
): Promise<ApiResult<string>> {
  const r = await apiPost<WireKeluhanTambah>('penyewa_keluhan_tambahdata.php', {
    _penyewa: penyewa,
    _judul: judul,
    _kategori: kategori,
    _uraian: uraian,
    _foto: fotoBase64 ?? '',
  })
  if (!r.ok) return r
  const msg = r.data.insertkeluhanbaru ?? ''
  if (msg.includes('sukses')) return { ok: true, data: msg, rawText: r.rawText }
  return { ok: false, error: msg || 'Gagal mengirim keluhan.', rawText: r.rawText }
}

export async function apiUbahPin(
  kode: string,
  pinLama: string,
  pinBaru: string,
): Promise<ApiResult<string>> {
  const r = await apiPost<WireUbahPin>('penyewa_ubahpin.php', {
    _kode: kode,
    _pinlama: pinLama,
    _pinbaru: pinBaru,
  })
  if (!r.ok) return r
  const msg = r.data.ubahpin ?? 'gagal'
  if (msg === 'pin berhasil diubah') return { ok: true, data: msg, rawText: r.rawText }
  if (msg === 'tidak ada data') {
    return { ok: false, error: 'PIN lama salah.', rawText: r.rawText }
  }
  return { ok: false, error: msg, rawText: r.rawText }
}