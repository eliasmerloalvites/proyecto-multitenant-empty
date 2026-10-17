const MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

export function fecha(valor: string | null | undefined): string {
  if (!valor) return '—';
  const d = /^\d{4}-\d{2}-\d{2}$/.test(valor) ? new Date(valor + 'T12:00:00') : new Date(valor);
  if (Number.isNaN(d.getTime())) return '—';
  return `${d.getDate()} ${MESES[d.getMonth()]} ${d.getFullYear()}`;
}

export const soles = (v: string) => `S/ ${Number(v).toFixed(2)}`;

/** Las 9 cifras del celular, con espacios para leerlas: 987 654 321. */
export const celularLegible = (c: string) => c.replace(/\D/g, '').slice(-9).replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');

/** Saca el código del taller de lo que haya leído el QR (código solo o un enlace que lo contenga). */
export function codigoDeQr(texto: string): string | null {
  const limpio = texto.trim().toUpperCase();
  if (/^[A-Z0-9]{8}$/.test(limpio)) return limpio;
  const m = limpio.match(/\/T\/([A-Z0-9]{8})(?:[/?#]|$)/);
  return m ? m[1] : null;
}

export function hoyMas(dias: number): string {
  const d = new Date();
  d.setDate(d.getDate() + dias);
  return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
}
