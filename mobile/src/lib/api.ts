import { Platform } from 'react-native';

import { sesion } from './session';
import type { TallerGuardado } from './types';

export class ApiError extends Error {
  constructor(
    public code: string,
    message: string,
    public status: number,
    public fields: Record<string, string[]> = {},
  ) {
    super(message);
  }
}

type Opciones = {
  method?: string;
  body?: unknown;
  base?: string;
  auth?: boolean;
  query?: Record<string, string | number | undefined>;
};

export const DIRECTORIO_URL = (process.env.EXPO_PUBLIC_DIRECTORIO_URL ?? '').replace(/\/$/, '');

function url(base: string, path: string, query?: Opciones['query']) {
  const q = Object.entries(query ?? {})
    .filter(([, v]) => v !== undefined && v !== '')
    .map(([k, v]) => `${encodeURIComponent(k)}=${encodeURIComponent(String(v))}`)
    .join('&');
  return `${base}/api/v1/${path}${q ? `?${q}` : ''}`;
}

export function cabeceras(auth: boolean): Record<string, string> {
  const h: Record<string, string> = { Accept: 'application/json', 'Content-Type': 'application/json' };
  if (auth && sesion.token) h.Authorization = `Bearer ${sesion.token}`;
  return h;
}

async function pedir<T>(path: string, o: Opciones = {}): Promise<{ data: T; meta: Record<string, any> }> {
  const base = o.base ?? sesion.taller?.base_url;
  if (!base) throw new ApiError('SIN_TALLER', 'Primero elige tu taller.', 0);

  let res: Response;
  try {
    res = await fetch(url(base, path, o.query), {
      method: o.method ?? 'GET',
      headers: cabeceras(o.auth ?? true),
      body: o.body === undefined ? undefined : JSON.stringify(o.body),
    });
  } catch {
    throw new ApiError('SIN_CONEXION', 'No hay conexión. Revisa tu internet e inténtalo de nuevo.', 0);
  }

  const json = await res.json().catch(() => null);

  if (!res.ok) {
    if (res.status === 401 && (o.auth ?? true)) sesion.alExpirar();
    const e = json?.error;
    throw new ApiError(e?.code ?? 'ERROR', e?.message ?? 'Ocurrió un error. Inténtalo de nuevo.', res.status, e?.fields ?? {});
  }

  return { data: json?.data as T, meta: json?.meta ?? {} };
}

export const api = {
  get: async <T>(path: string, query?: Opciones['query']) => (await pedir<T>(path, { query })).data,
  getConMeta: <T>(path: string, query?: Opciones['query']) => pedir<T>(path, { query }),
  post: async <T>(path: string, body?: unknown) => (await pedir<T>(path, { method: 'POST', body: body ?? {} })).data,
  patch: async <T>(path: string, body?: unknown) => (await pedir<T>(path, { method: 'PATCH', body })).data,
  delete: async <T>(path: string) => (await pedir<T>(path, { method: 'DELETE' })).data,
  /** Sin sesión y contra el taller elegido (OTP). */
  publico: async <T>(path: string, body: unknown) => (await pedir<T>(path, { method: 'POST', body, auth: false })).data,
};

/** Directorio central: traduce el código del QR en la dirección y marca del taller. */
export async function buscarTaller(codigo: string): Promise<TallerGuardado> {
  if (!DIRECTORIO_URL) throw new ApiError('SIN_CONFIG', 'La app no tiene configurado el directorio de talleres.', 0);
  const { data } = await pedir<TallerGuardado>(`talleres/${encodeURIComponent(codigo)}`, { base: DIRECTORIO_URL, auth: false });
  return data;
}

export const nombreDispositivo = `${Platform.OS === 'ios' ? 'iPhone' : Platform.OS === 'android' ? 'Android' : 'Web'} (app)`;
