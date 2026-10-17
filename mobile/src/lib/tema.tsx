import { useMemo } from 'react';

import { useAuth } from './auth';

const HEX = /^#([0-9a-f]{3}|[0-9a-f]{6})$/i;
const color = (c: string | null | undefined, defecto: string) => (c && HEX.test(c.trim()) ? c.trim() : defecto);

function rgb(hex: string): [number, number, number] {
  const h = hex.replace('#', '');
  const f = h.length === 3 ? h.split('').map((c) => c + c).join('') : h;
  return [0, 2, 4].map((i) => parseInt(f.slice(i, i + 2), 16)) as [number, number, number];
}

/** Mezcla `a` con `b`; `t` es cuánto de `b` (0 a 1). */
export function mezclar(a: string, b: string, t: number) {
  const [r1, g1, b1] = rgb(a);
  const [r2, g2, b2] = rgb(b);
  const m = (x: number, y: number) => Math.round(x + (y - x) * t).toString(16).padStart(2, '0');
  return `#${m(r1, r2)}${m(g1, g2)}${m(b1, b2)}`;
}

/** Negro o blanco según el contraste con el color de fondo. */
function textoSobre(hex: string) {
  const [r, g, b] = rgb(hex);
  return (r * 299 + g * 587 + b * 114) / 1000 > 150 ? '#0f172a' : '#ffffff';
}

export function useTema() {
  const { taller } = useAuth();
  const t = taller?.taller.tema;

  return useMemo(() => {
    const primario = color(t?.color_principal, '#1d4ed8');
    return {
      primario,
      sobrePrimario: textoSobre(primario),
      // Variantes del color del taller: fondo de los héroes (oscuro) y tinte de los chips (claro).
      primarioOscuro: mezclar(primario, '#000000', 0.55),
      primarioClaro: mezclar(primario, '#ffffff', 0.9),
      fondo: '#f3f4f7',
      tarjeta: '#ffffff',
      texto: '#0f172a',
      suave: '#64748b',
      borde: '#e5e7ec',
      error: '#dc2626',
      ok: '#16a34a',
      aviso: '#d97706',
    };
  }, [t?.color_principal]);
}

export type Tema = ReturnType<typeof useTema>;
