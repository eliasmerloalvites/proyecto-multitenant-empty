import { interpolate, useCurrentFrame, useVideoConfig } from 'remotion';

import { C } from './estilo';

type Palabra = { texto: string; inicio: number; fin: number };

/** Apertura de la boca (0 a 1) según la palabra que la voz está diciendo en el instante t. */
const apertura = (t: number, palabras: Palabra[]) => {
  const p = palabras.find((w) => t >= w.inicio - 0.02 && t <= w.fin + 0.05);
  if (!p) return 0;
  const k = (t - p.inicio) / Math.max(p.fin - p.inicio, 0.08);
  return 0.25 + 0.75 * Math.abs(Math.sin(k * Math.PI * 1.6 + p.inicio * 13));
};

/**
 * Mecánico de Kael Tech: personaje dibujado en SVG. La boca se abre al ritmo de
 * las palabras de la narración, parpadea cada par de segundos y mueve la cabeza
 * y el brazo mientras habla.
 */
export const Avatar: React.FC<{ palabras: Palabra[]; ancho?: number }> = ({ palabras, ancho = 520 }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const t = frame / fps;
  const abre = apertura(t, palabras);
  const hablando = abre > 0 ? 1 : 0;

  const respira = Math.sin(t * 2.2) * 4;
  const cabeceo = Math.sin(t * 3.1) * (1.2 + hablando * 1.6);
  const parpadeo = frame % 66 > 3 ? 1 : 0.08;
  const ceja = hablando * Math.sin(t * 7) * 3;
  const brazo = interpolate(Math.sin(t * 4.2), [-1, 1], [-8, 14]) * (hablando ? 1 : 0.2);
  const boca = 4 + abre * 30;

  return (
    <svg viewBox="0 0 400 450" width={ancho} height={(ancho * 450) / 400} style={{ overflow: 'visible' }}>
      <ellipse cx="200" cy="438" rx="150" ry="12" fill="rgba(15,23,42,0.18)" />

      {/* Cuerpo: overol azul */}
      <g transform={`translate(0 ${respira})`}>
        <path d="M70 450 C70 350 110 318 200 318 C290 318 330 350 330 450 Z" fill={C.azul} />
        <path d="M150 322 L200 380 L250 322 Z" fill="#e2e8f0" />
        <rect x="150" y="395" width="100" height="55" rx="10" fill="#2a4fd0" />
        <text x="200" y="432" textAnchor="middle" fontSize="26" fontWeight="800" fill="#fff" fontFamily="Poppins, sans-serif">KT</text>

        {/* Brazo con llave inglesa */}
        <g transform={`rotate(${brazo} 318 360)`}>
          <path d="M300 345 C345 350 372 300 372 255" stroke={C.azul} strokeWidth="38" strokeLinecap="round" fill="none" />
          <circle cx="372" cy="246" r="22" fill="#f1c9a0" />
          <rect x="365" y="170" width="14" height="78" rx="6" fill="#94a3b8" />
          <path d="M350 158 a22 22 0 1 1 44 0 l-12 0 a10 10 0 1 0 -20 0 z" fill="#94a3b8" />
        </g>

        {/* Cabeza */}
        <g transform={`rotate(${cabeceo} 200 300)`}>
          <rect x="178" y="290" width="44" height="38" rx="12" fill="#e8b98a" />
          <ellipse cx="102" cy="215" rx="20" ry="28" fill="#f1c9a0" />
          <ellipse cx="298" cy="215" rx="20" ry="28" fill="#f1c9a0" />
          <ellipse cx="200" cy="205" rx="98" ry="112" fill="#f6d2ab" />

          {/* Gorra */}
          <path d="M100 178 C100 98 150 70 200 70 C250 70 300 98 300 178 C260 160 140 160 100 178 Z" fill={C.azul} />
          <path d="M92 182 C150 162 250 162 308 182 C300 200 270 192 200 190 C130 192 100 200 92 182 Z" fill="#2a4fd0" />
          <circle cx="200" cy="110" r="22" fill="#fff" opacity="0.95" />
          <text x="200" y="119" textAnchor="middle" fontSize="23" fontWeight="800" fill={C.azul} fontFamily="Poppins, sans-serif">KT</text>

          {/* Cejas y ojos */}
          <path d={`M138 ${202 - ceja} Q160 ${190 - ceja} 182 ${202 - ceja}`} stroke="#3b2a20" strokeWidth="8" strokeLinecap="round" fill="none" />
          <path d={`M218 ${202 - ceja} Q240 ${190 - ceja} 262 ${202 - ceja}`} stroke="#3b2a20" strokeWidth="8" strokeLinecap="round" fill="none" />
          <g transform={`translate(0 ${(1 - parpadeo) * 0})`}>
            <ellipse cx="160" cy="226" rx="17" ry={19 * parpadeo} fill="#fff" />
            <ellipse cx="240" cy="226" rx="17" ry={19 * parpadeo} fill="#fff" />
            <ellipse cx="163" cy="228" rx="8" ry={9 * parpadeo} fill="#1e293b" />
            <ellipse cx="237" cy="228" rx="8" ry={9 * parpadeo} fill="#1e293b" />
          </g>

          {/* Nariz, mejillas y bigote */}
          <path d="M194 240 Q200 262 210 254" stroke="#d49a6a" strokeWidth="6" strokeLinecap="round" fill="none" />
          <circle cx="132" cy="268" r="14" fill="#f4a58a" opacity="0.45" />
          <circle cx="268" cy="268" r="14" fill="#f4a58a" opacity="0.45" />
          <path d="M150 288 Q175 268 200 282 Q225 268 250 288 Q225 300 200 292 Q175 300 150 288 Z" fill="#3b2a20" />

          {/* Boca sincronizada con la voz */}
          <g>
            <ellipse cx="200" cy={314 + boca / 2 - 6} rx={30 - abre * 4} ry={boca / 2 + 2} fill="#7a1f2b" />
            <ellipse cx="200" cy={314 + boca - 6} rx={18 - abre * 3} ry={Math.max(2, boca / 4)} fill="#e86a7c" />
            <path d={`M170 ${312} Q200 ${322 - abre * 10} 230 ${312}`} stroke="#3b2a20" strokeWidth="5" strokeLinecap="round" fill="none" opacity={abre > 0.1 ? 0 : 1} />
          </g>
        </g>
      </g>
    </svg>
  );
};
