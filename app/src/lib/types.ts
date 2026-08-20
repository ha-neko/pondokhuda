// Types mirroring the documented API JSON shapes (see /tmp/api-inventory.md)

export interface PenyewaProfile {
  kode: string
  nomorktp: string
  nomorkamar: string
  nama: string
  tgllahir: string
  jeniskelamin: string
  nomorhp: string
  email: string
  urlfoto: string
  namaortu: string
  nomorhportu: string
  alamatrumah: string
  kelurahan: string
  kecamatan: string
  namakotakabupaten: string
  kodepos: string
  tempatkuliahkerja: string
  jurusankuliah: string
  periodebayar: string
  status: string
  namakost: string
  alamatkost: string
  emailkost: string
  primary: string
  logo: string
}

export interface HistoriBayar {
  bayarke?: number
  kode_bayar: string
  nomor_kamar: string
  tanggal_reminder: string
  tanggal_pembayaran: string
  tanggal_bayar: string
  after_duedate: number | string
  periode_bayar: string
  harga_perbulan: number | string
  denda: number | string
  diskon: number | string
  total_harga: number | string
  total_bayar: number | string
  metode: string
  periodesewa: string
  sisabayarcurr: number | string
  statusbayar: string
}

export interface PembayaranInfo {
  kumulasi: number | string
  tanggalbayar: string
  nexttglbayar: string
  sisaharibayar: number | string
  statusbayar: string
  tagihantotal: string
  sisabayarsebelumnya: string
  bayarsebelumnya: string
  alarm: string
  resumetagihan: string
  periodebayarbulan: string
  historibayar: HistoriBayar[]
}

export interface ResumePembayaran {
  line1: string
  line2: string
  line3: string
  line4: string
  line5: string
  line6: string
  remindertgl: string
}

export interface KontakAdmin {
  namaadmin: string
  wasap: string
}

export interface ChatRow {
  kodchat: string
  kodberita: string
  koduser: string
  keyuser: string
  dttime: string
  msg: string
}

export interface Pengumuman {
  kode: string
  judul: string
  berita: string
  tglpublish: string
  lastupdate: string
  chat?: ChatRow[]
}

export interface KeluhanChat extends ChatRow {
  kodkeluhan: string
}

export interface KeluhanFoto {
  id: string
  link: string
}

export interface KeluhanStatus {
  id: string
  status: string
  tgl: string
}

export interface KeluhanKomen {
  kode: string
  komen: string
  created: string
}

export interface Keluhan {
  kode: string
  judul: string
  kategori: string
  uraian: string
  tgl: string
  user: string
  nokamar?: string
  nama: string
  hp: string
  chat?: KeluhanChat[] | null
  foto?: KeluhanFoto[] | null
  status?: KeluhanStatus[] | null
  komen?: KeluhanKomen[] | null
}

export interface KategoriKeluhan {
  kodekategori: string
  kategori: string
}

export interface Session {
  kode: string
  pin: string
  profile: PenyewaProfile
  bayar?: PembayaranInfo
  resume?: ResumePembayaran
  kontak?: KontakAdmin[]
  savedAt: number
}

// --- API wire responses (raw, pre-normalization) ---

export interface WireLogin {
  personalinfopenyewa?: PenyewaProfile[]
  personalinfobayar?: PembayaranInfo
  resumepembayaran?: ResumePembayaran
  kontakadmin?: KontakAdmin[]
  personalinfo?: string
}

export interface WirePembayaran {
  personalinfobayar?: PembayaranInfo
  resumepembayaran?: ResumePembayaran
}

export interface WirePengumuman {
  pengumuman?: Pengumuman[] | string
}

export interface WireChat {
  chat?: ChatRow[] | string
}

export interface WireKeluhanList {
  keluhan?: Keluhan[] | string
  getdatakeluhan?: string
}

export interface WireKeluhanKategori {
  kategorikeluhan?: KategoriKeluhan[] | null
  keluhan?: Keluhan[] | string
}

export interface WireKeluhanTambah {
  insertkeluhanbaru?: string
}

export interface WireUbahPin {
  ubahpin?: string
}