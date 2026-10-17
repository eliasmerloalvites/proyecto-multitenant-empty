import { AbsoluteFill, Audio, Series, interpolate, spring, staticFile, useCurrentFrame, useVideoConfig } from 'remotion';

import { Avatar } from './Avatar';
import { Fondo } from './Fondos';
import { C, FPS, RESPIRO, fontFamily, sombra } from './estilo';
import type { Voz } from './Promo';
import { Subtitulos } from './Subtitulos';

export type EscenaAvatar = { id: string; fondo: string; etiqueta?: string; icono: string; titulo: string; detalle: string; texto: string; resaltar: string[] };

const framesDe = (voz: Voz[], id: string) => Math.ceil((voz.find((x) => x.id === id)!.duracion + RESPIRO) * FPS);
export const duracionAvatar = (escenas: EscenaAvatar[], voz: Voz[]) => escenas.reduce((s, e) => s + framesDe(voz, e.id), 0);

/** Video con el mecánico animado que cuenta los problemas de los talleres, escena por escena. */
export const PromoAvatar: React.FC<{ escenas: EscenaAvatar[]; voz: Voz[] }> = ({ escenas, voz }) => (
  <AbsoluteFill style={{ fontFamily, backgroundColor: C.claro }}>
    <Musica total={duracionAvatar(escenas, voz)} />
    <Series>
      {escenas.map((e) => (
        <Series.Sequence key={e.id} durationInFrames={framesDe(voz, e.id)}>
          <Escena escena={e} voz={voz.find((x) => x.id === e.id)!} />
        </Series.Sequence>
      ))}
    </Series>
  </AbsoluteFill>
);

const Escena: React.FC<{ escena: EscenaAvatar; voz: Voz }> = ({ escena, voz }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const oscuro = escena.fondo === 'oscuro';
  const entrada = spring({ frame, fps, config: { damping: 200 }, durationInFrames: 14 });
  const tarjeta = spring({ frame: frame - 4, fps, config: { damping: 13, stiffness: 150 } });

  return (
    <AbsoluteFill>
      <Fondo oscuro={oscuro} />
      <Audio src={staticFile(voz.audio)} />

      {escena.etiqueta ? (
        <div style={{ position: 'absolute', top: 120, width: '100%', display: 'flex', justifyContent: 'center', opacity: entrada }}>
          <div style={{ padding: '12px 32px', borderRadius: 999, border: `3px solid ${C.azul}`, color: oscuro ? '#9db3ff' : C.azul, background: oscuro ? 'rgba(10,15,31,0.6)' : C.blanco, fontWeight: 800, fontSize: 28, letterSpacing: 5 }}>{escena.etiqueta}</div>
        </div>
      ) : null}

      {/* Tarjeta del problema */}
      <div style={{ position: 'absolute', top: 210, left: 90, right: 90, display: 'flex', justifyContent: 'center', transform: `scale(${interpolate(tarjeta, [0, 1], [0.7, 1])})`, opacity: entrada }}>
        <div
          style={{
            width: '100%',
            display: 'flex',
            alignItems: 'center',
            gap: 34,
            padding: '38px 44px',
            borderRadius: 40,
            background: oscuro ? 'rgba(15,23,48,0.78)' : C.blanco,
            border: oscuro ? '2px solid rgba(142,166,255,0.35)' : `2px solid ${C.borde}`,
            boxShadow: sombra,
          }}>
          <div style={{ fontSize: 120, lineHeight: 1 }}>{escena.icono}</div>
          <div>
            <div style={{ fontSize: 58, fontWeight: 800, lineHeight: 1.1, color: oscuro ? '#fff' : C.tinta }}>{escena.titulo}</div>
            <div style={{ fontSize: 32, fontWeight: 500, marginTop: 12, color: oscuro ? '#b6c4e6' : C.suave }}>{escena.detalle}</div>
          </div>
        </div>
      </div>

      {/* Avatar que habla */}
      <div style={{ position: 'absolute', top: 580, left: 0, right: 0, display: 'flex', justifyContent: 'center', transform: `translateY(${interpolate(entrada, [0, 1], [40, 0])}px)` }}>
        <Avatar palabras={voz.palabras} ancho={560} />
      </div>

      <Subtitulos texto={escena.texto} palabras={voz.palabras} resaltar={escena.resaltar} oscuro={oscuro} />
    </AbsoluteFill>
  );
};

const Musica: React.FC<{ total: number }> = ({ total }) => (
  <Audio
    src={staticFile('musica/fondo.wav')}
    loop
    volume={(f) => 0.12 * interpolate(f, [0, 20, total - 60, total], [0, 1, 1, 0], { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' })}
  />
);
