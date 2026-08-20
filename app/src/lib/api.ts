// Low-level API transport for the legacy POST-only PHP backend.

export type ApiResult<T> =
  | { ok: true; data: T; rawText: string }
  | { ok: false; error: string; retryAfter?: number; rawText?: string }

const BASE = (import.meta.env.VITE_API_BASE as string | undefined) ?? '/api'

// Most endpoints read $_POST (form data), not JSON bodies.
export async function apiPost<T>(
  path: string,
  params: Record<string, string | number>,
): Promise<ApiResult<T>> {
  const body = new URLSearchParams()
  for (const [k, v] of Object.entries(params)) {
    if (v !== undefined && v !== null && v !== '') body.append(k, String(v))
  }

  let res: Response
  try {
    res = await fetch(`${BASE}/${path}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    })
  } catch {
    return { ok: false, error: 'Tidak dapat terhubung ke server. Periksa koneksi Anda.' }
  }

  const rawText = await res.text()

  if (res.status === 429) {
    let retryAfter: number | undefined
    try {
      retryAfter = Number(JSON.parse(rawText).retry_after)
    } catch {
      /* ignore */
    }
    return {
      ok: false,
      error: 'Terlalu banyak percobaan. Tunggu sebentar lalu coba lagi.',
      retryAfter,
      rawText,
    }
  }
  if (res.status === 401) {
    return { ok: false, error: 'Sesi kedaluwarsa. Silakan login ulang.', rawText }
  }
  if (!res.ok) {
    return { ok: false, error: `Server error (${res.status}).`, rawText }
  }

  const trimmed = rawText.trim()
  if (!trimmed) {
    // login_ph returns an EMPTY body when the biodata lookup fails.
    return { ok: false, error: 'Data tidak ditemukan pada sistem.', rawText }
  }

  // Responses are text/html by default; some endpoints echo plain text.
  let parsed: unknown
  try {
    parsed = JSON.parse(trimmed)
  } catch {
    return { ok: false, error: 'Gagal memproses respons server.', rawText }
  }
  return { ok: true, data: parsed as T, rawText }
}