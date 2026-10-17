import { AbsoluteFill, Audio, Series, interpolate, spring, staticFile, useCurrentFrame, useVideoConfig } from 'remotion';

import { Fondo } from './Fondos';
import { C, FPS, RESPIRO, fontFamily } from './estilo';
import { Subtitulos } from './Subtitulos';
import { VISUALES } from './visuales';
import { VISUALES_BAHIA } from './visuales-bahia';
import { VISUALES_INVENTARIO } from './visuales-inventario';
import { VISUALES_MANTENIMIENTO } from './visuales-mantenimiento';
import { VISUALES_WEB } from './visuales-web';

export type Escena = { id: string; visual: string; fondo: string; etiqueta?: string; texto: string; resaltar: string[] };
export type Voz = { id: string; audio: string; duracion: number; palabras: { texto: string; inicio: number; fin: number }[] };
// Las props de una composición se pasan como JSON: los componentes se eligen aquí por nombre.
const VISUALES_POR_VIDEO = { app: VISUALES, web: VISUALES_WEB, bahia: VISUALES_BAHIA, mantenimiento: VISUALES_MANTENIMIENTO, inventario: VISUALES_INVENTARIO } as const;

const framesDe = (voz: Voz[], id: string) => Math.ceil((voz.find((x) => x.id === id)!.duracion + RESPIRO) * FPS);

export const duracionTotal = (escenas: Escena[], voz: Voz[]) => escenas.reduce((s, e) => s + framesDe(voz, e.id), 0);

/** Video armado escena por escena a partir de un guion y su narración. */
export const Promo: React.FC<{ escenas: Escena[]; voz: Voz[]; video: keyof typeof VISUALES_POR_VIDEO }> = ({ escenas, voz, video }) => (
  <AbsoluteFill style={{ fontFamily, backgroundColor: C.claro }}>
    {/* Música de fondo (scripts/generar-musica.mjs), bajo la voz y con salida suave. */}
    <MusicaFondo total={duracionTotal(escenas, voz)} />
    <Series>
      {escenas.map((e) => (
        <Series.Sequence key={e.id} durationInFrames={framesDe(voz, e.id)}>
          <EscenaVideo escena={e} voz={voz.find((x) => x.id === e.id)!} Visual={VISUALES_POR_VIDEO[video][e.visual]} />
        </Series.Sequence>
      ))}
    </Series>
  </AbsoluteFill>
);

const EscenaVideo: React.FC<{ escena: Escena; voz: Voz; Visual?: React.FC<{ duracion: number }> }> = ({ escena, voz, Visual }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const oscuro = escena.fondo === 'oscuro';
  const entrada = spring({ frame, fps, config: { damping: 200 }, durationInFrames: 14 });

  return (
    <AbsoluteFill>
      <Fondo oscuro={oscuro} />
      <Audio src={staticFile(voz.audio)} />

      <AbsoluteFill style={{ opacity: entrada }}>
        {escena.etiqueta ? (
          <div style={{ position: 'absolute', top: 150, width: '100%', display: 'flex', justifyContent: 'center' }}>
            <div
              style={{
                padding: '12px 30px',
                borderRadius: 999,
                border: `3px solid ${C.azul}`,
                color: oscuro ? '#9db3ff' : C.azul,
                background: oscuro ? 'rgba(10,15,31,0.6)' : C.blanco,
                fontWeight: 700,
                fontSize: 26,
                letterSpacing: 4,
                transform: `translateY(${interpolate(entrada, [0, 1], [-30, 0])}px)`,
              }}>
              {escena.etiqueta}
            </div>
          </div>
        ) : null}

        <div style={{ position: 'absolute', top: 260, left: 0, right: 0, height: 900, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
          {Visual ? <Visual duracion={voz.duracion} /> : null}
        </div>
      </AbsoluteFill>

      <Subtitulos texto={escena.texto} palabras={voz.palabras} resaltar={escena.resaltar} oscuro={oscuro} />
    </AbsoluteFill>
  );
};

const MusicaFondo: React.FC<{ total: number }> = ({ total }) => (
  <Audio
    src={staticFile('musica/fondo.wav')}
    loop
    volume={(f) => 0.14 * interpolate(f, [0, 20, total - 60, total], [0, 1, 1, 0], { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' })}
  />
);
