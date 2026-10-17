import { interpolate, spring, useCurrentFrame, useVideoConfig } from 'remotion';

import { C } from './estilo';

type Palabra = { texto: string; inicio: number; fin: number };

const normal = (s: string) => s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '').replace(/[^a-z0-9ñ]/g, '');

/**
 * Subtítulos palabra por palabra: se muestra la frase corta en curso (hasta
 * 4 palabras o hasta un signo de puntuación); cada palabra se enciende cuando
 * la voz la dice y las palabras clave llevan una pastilla azul.
 */
export const Subtitulos: React.FC<{ texto: string; palabras: Palabra[]; resaltar: string[]; oscuro: boolean }> = ({ texto, palabras, resaltar, oscuro }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const t = frame / fps;

  // El texto del guion conserva la puntuación; la voz da los tiempos.
  const fichas = texto.split(/\s+/).filter(Boolean);
  const items = palabras.map((p, i) => ({ ...p, ver: fichas.length === palabras.length ? fichas[i] : p.texto }));
  const claves = new Set(resaltar.map(normal));

  const grupos: (typeof items)[] = [];
  let actual: typeof items = [];
  for (const it of items) {
    actual.push(it);
    if (actual.length >= 4 || /[.,:?!]$/.test(it.ver)) {
      grupos.push(actual);
      actual = [];
    }
  }
  if (actual.length) grupos.push(actual);

  const idx = Math.max(0, grupos.findIndex((g, i) => t < (grupos[i + 1]?.[0].inicio ?? Infinity)));
  const grupo = grupos[idx] ?? [];
  const inicioGrupo = Math.round((grupo[0]?.inicio ?? 0) * fps);

  return (
    <div style={{ position: 'absolute', top: 1270, left: 70, right: 70, display: 'flex', flexWrap: 'wrap', justifyContent: 'center', alignItems: 'center', columnGap: 18, rowGap: 10 }}>
      {grupo.map((p, i) => {
        const dicho = t >= p.inicio - 0.03;
        const clave = claves.has(normal(p.ver));
        const aparece = spring({ frame: frame - Math.round(p.inicio * fps), fps, config: { damping: 14, stiffness: 180 } });
        const sube = spring({ frame: frame - inicioGrupo, fps, config: { damping: 200 }, durationInFrames: 8 });
        const color = clave && dicho ? '#fff' : dicho ? (oscuro ? '#fff' : C.tinta) : oscuro ? 'rgba(255,255,255,0.35)' : 'rgba(15,23,42,0.28)';
        return (
          <span
            key={`${idx}-${i}`}
            style={{
              fontSize: 64,
              fontWeight: 700,
              color,
              padding: clave ? '2px 18px 6px' : 0,
              borderRadius: 16,
              background: clave ? (dicho ? C.azul : 'rgba(52,97,240,0.18)') : 'transparent',
              boxShadow: clave && dicho ? '0 10px 30px rgba(52,97,240,0.45)' : 'none',
              transform: `translateY(${interpolate(sube, [0, 1], [24, 0])}px) scale(${clave && dicho ? interpolate(aparece, [0, 1], [0.85, 1], { extrapolateRight: 'clamp' }) : 1})`,
              rotate: clave ? '-1.5deg' : '0deg',
              opacity: sube,
            }}>
            {p.ver.replace(/^¿/, '¿')}
          </span>
        );
      })}
    </div>
  );
};
