import { Img, staticFile } from 'remotion';

/** Logo de Kael Tech (imagen cuadrada con su fondo azul noche) en una tarjeta redondeada. */
export const Logo: React.FC<{ tam: number; style?: React.CSSProperties }> = ({ tam, style }) => (
  <div style={{ width: tam, height: tam, borderRadius: tam * 0.16, overflow: 'hidden', boxShadow: '0 30px 90px rgba(52,97,240,0.45), 0 0 0 2px rgba(142,166,255,0.35)', ...style }}>
    <Img src={staticFile('img/logo-kaeltech.jpg')} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
  </div>
);
