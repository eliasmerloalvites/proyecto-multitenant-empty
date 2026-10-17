import type { CSSProperties, ReactNode } from 'react';
import { Img, interpolate, staticFile, useCurrentFrame, useVideoConfig } from 'remotion';

import { C, sombra } from './estilo';
import medidas from './capturas.json';
import { Logo } from './Logo';
import { Aparece, Chip, fila, Placa, Tarjeta, useEntra } from './visuales';

type Props = { duracion: number };

/**
 * Captura real del sistema dentro de una ventana de navegador, con un
 * acercamiento lento (de `desde` a `hasta`) hacia la parte que se está
 * explicando. x/y son el punto de la imagen que queda al centro (0 a 1).
 */
type Encuadre = { x: number; y: number; zoom: number };
export const Captura: React.FC<{ img: string; desde: Encuadre; hasta: Encuadre; duracion: number; titulo?: string; children?: ReactNode }> = ({ img, desde, hasta, duracion, titulo, children }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const entra = useEntra(0, true);
  const k = interpolate(frame, [0, duracion * fps], [0, 1], { extrapolateRight: 'clamp' });
  const suave = k * k * (3 - 2 * k);
  const x = interpolate(suave, [0, 1], [desde.x, hasta.x]);
  const y = interpolate(suave, [0, 1], [desde.y, hasta.y]);
  const zoom = interpolate(suave, [0, 1], [desde.zoom, hasta.zoom]);
  const W = 1000;
  // Proporción real de la captura (scripts/capturar.mjs guarda sus medidas); las de pantalla completa son 1440×900.
  const m = (medidas as Record<string, { w: number; h: number }>)[img];
  const aspecto = m ? m.h / m.w : 0.625;
  const ancho = W * zoom;
  const alto = ancho * aspecto;
  // Capturas bajas (una sola tarjeta): la ventana se ajusta a su altura para no dejar espacio en blanco.
  const H = Math.min(840, W * Math.min(desde.zoom, hasta.zoom) * aspecto + 64);
  const izq = Math.min(0, Math.max(W - ancho, W / 2 - x * ancho));
  const arriba = Math.min(0, Math.max(H - 64 - alto, (H - 64) / 2 - y * alto));
  return (
    <div style={{ position: 'relative', opacity: entra, transform: `translateY(${interpolate(entra, [0, 1], [60, 0])}px)` }}>
      <div style={{ width: W, height: H, borderRadius: 26, background: '#fff', boxShadow: '0 40px 110px rgba(15,23,42,0.28)', overflow: 'hidden' }}>
        <div style={{ ...fila, gap: 10, height: 64, padding: '0 22px', background: '#eef1f6', borderBottom: `1px solid ${C.borde}` }}>
          {['#ef4444', '#f59e0b', '#22c55e'].map((c) => <div key={c} style={{ width: 16, height: 16, borderRadius: 8, background: c }} />)}
          <div style={{ marginLeft: 18, flex: 1, background: '#fff', borderRadius: 10, padding: '8px 18px', fontSize: 20, color: C.suave }}>🔒 {titulo ?? 'Sistema de tu taller · Kael Tech'}</div>
        </div>
        <div style={{ position: 'relative', width: W, height: H - 64, overflow: 'hidden', background: '#f4f6f9' }}>
          <Img src={staticFile(`capturas/${img}.png`)} style={{ position: 'absolute', left: izq, top: arriba, width: ancho, height: alto }} />
        </div>
      </div>
      {children}
    </div>
  );
};

/** Globo o insignia que aparece sobre la captura. */
export const Insignia: React.FC<{ retraso: number; style: CSSProperties; children: ReactNode }> = ({ retraso, style, children }) => {
  const e = useEntra(retraso);
  return <div style={{ position: 'absolute', transform: `scale(${e})`, ...style }}>{children}</div>;
};

// ---------------------------------------------------------------- escenas

const Gancho: React.FC<Props> = () => {
  const tacha = useEntra(1.6);
  const frame = useCurrentFrame();
  return (
    <div style={{ ...fila, gap: 70 }}>
      {['📒', '📊'].map((e, i) => (
        <Aparece key={e} retraso={0.2 + i * 0.5}>
          <div style={{ position: 'relative', width: 300, height: 300, borderRadius: 60, background: 'rgba(255,255,255,0.08)', border: '2px solid rgba(255,255,255,0.18)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 170, transform: `rotate(${Math.sin(frame / 8 + i) * 4}deg)` }}>
            {e}
            <div style={{ position: 'absolute', width: 360 * tacha, height: 18, borderRadius: 9, background: C.rojo, transform: 'rotate(-35deg)', boxShadow: '0 0 30px rgba(239,68,68,0.7)' }} />
          </div>
        </Aparece>
      ))}
    </div>
  );
};

const Caos: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const items = [
    { i: '🏍️', t: 'Motos sin registro', d: '¿Qué se le hizo la última vez?' },
    { i: '📦', t: 'Stock que no cuadra', d: 'El sistema dice 5, hay 2' },
    { i: '💸', t: 'Caja descuadrada', d: 'Faltan S/ 120… otra vez' },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 26, width: 820 }}>
      {items.map((x, k) => (
        <Aparece key={x.t} retraso={0.2 + k * 1.3} desde={60}>
          <Tarjeta style={{ ...fila, gap: 26, padding: 28, border: `2px solid ${C.rojo}33`, transform: `translateX(${Math.sin(frame * 1.3 + k) * (frame > (0.2 + k * 1.3) * 30 + 20 ? 2 : 0)}px)` }}>
            <div style={{ width: 100, height: 100, borderRadius: 26, background: '#fee2e2', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 54 }}>{x.i}</div>
            <div style={{ flex: 1 }}>
              <div style={{ fontSize: 38, fontWeight: 700, color: C.tinta }}>{x.t}</div>
              <div style={{ fontSize: 26, color: C.suave }}>{x.d}</div>
            </div>
            <div style={{ fontSize: 48 }}>⚠️</div>
          </Tarjeta>
        </Aparece>
      ))}
    </div>
  );
};

const Marca: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const e = useEntra(0.6);
  const chips = ['Mantenimientos', 'Reservas', 'Ventas SUNAT', 'Inventario', 'Caja', 'Reportes'];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 46 }}>
      <div style={{ position: 'relative' }}>
        <div style={{ position: 'absolute', inset: -60, borderRadius: '50%', background: 'conic-gradient(from 0deg, #3461f0, #7c4dff, #3461f0)', filter: 'blur(60px)', opacity: 0.5, transform: `rotate(${frame * 2}deg)` }} />
        <Logo tam={460} style={{ position: 'relative', transform: `scale(${e}) rotate(${interpolate(e, [0, 1], [-12, 0])}deg)` }} />
      </div>
      <div style={{ display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: 14, width: 860 }}>
        {chips.map((c, i) => (
          <Aparece key={c} retraso={2.2 + i * 0.25} desde={20}>
            <div style={{ background: 'rgba(255,255,255,0.1)', border: '2px solid rgba(255,255,255,0.22)', borderRadius: 999, padding: '12px 26px', color: '#fff', fontSize: 28, fontWeight: 600 }}>✓ {c}</div>
          </Aparece>
        ))}
      </div>
    </div>
  );
};



export const Orden: React.FC<Props> = () => {
  const hoja = useEntra(0.2);
  const sello = useEntra(3.8);
  return (
    <div style={{ position: 'relative', width: 700, height: 880, transform: `translateY(${interpolate(hoja, [0, 1], [200, 0])}px)`, opacity: hoja }}>
      <div style={{ width: 700, height: 880, background: '#fff', borderRadius: 14, boxShadow: '0 40px 110px rgba(15,23,42,0.25)', padding: 44, display: 'flex', flexDirection: 'column', gap: 22 }}>
        <div style={{ ...fila, justifyContent: 'space-between', borderBottom: `4px solid ${C.azul}`, paddingBottom: 18 }}>
          <div>
            <div style={{ fontSize: 34, fontWeight: 800, color: C.tinta }}>Motos del Valle</div>
            <div style={{ fontSize: 18, color: C.suave }}>Taller de motos · RUC 20XXXXXXXXX</div>
          </div>
          <div style={{ textAlign: 'right' }}>
            <div style={{ fontSize: 18, color: C.suave }}>ORDEN DE SERVICIO</div>
            <div style={{ fontSize: 28, fontWeight: 800, color: C.azul }}>MGI-000008</div>
          </div>
        </div>
        <div style={{ ...fila, gap: 16 }}>
          <Placa valor="TRD·452" tam={22} />
          <span style={{ fontSize: 20, color: C.tinta }}>Yamaha Z 250 · 18 450 km</span>
        </div>
        <div style={{ fontSize: 17, fontWeight: 700, letterSpacing: 2, color: C.suave }}>REPUESTOS</div>
        {[['Filtro de aceite', '25.50'], ['Aceite 20W50 sintético', '48.00'], ['Pastillas de freno', '65.00'], ['Mano de obra', '60.00']].map(([d, p], i) => (
          <Aparece key={d} retraso={1 + i * 0.35} desde={14}>
            <div style={{ ...fila, justifyContent: 'space-between', fontSize: 22, color: C.tinta, borderBottom: `1px solid ${C.borde}`, paddingBottom: 10 }}>
              <span>{d}</span>
              <b>S/ {p}</b>
            </div>
          </Aparece>
        ))}
        <div style={{ ...fila, justifyContent: 'space-between', fontSize: 28, fontWeight: 800, color: C.tinta }}>
          <span>Total</span>
          <span>S/ 198.50</span>
        </div>
        <Aparece retraso={2.6}>
          <div style={{ background: '#fffbeb', border: `2px solid ${C.ambar}55`, borderRadius: 14, padding: 18, fontSize: 21, color: '#92400e' }}>
            💡 <b>Recomendación:</b> cambiar la cadena en el próximo servicio (20 000 km).
          </div>
        </Aparece>
      </div>
      <div style={{ position: 'absolute', right: -40, top: 120, transform: `scale(${sello}) rotate(-12deg)`, background: C.rojo, color: '#fff', borderRadius: 18, padding: '14px 26px', fontSize: 34, fontWeight: 800, boxShadow: '0 16px 40px rgba(239,68,68,0.45)' }}>PDF</div>
    </div>
  );
};





const Recepcion: React.FC<Props> = ({ duracion }) => (
  <Captura img="mantenimiento" duracion={duracion} desde={{ x: 0.3, y: 0.3, zoom: 1.25 }} hasta={{ x: 0.3, y: 0.55, zoom: 1.7 }}>
    <Insignia retraso={2.2} style={{ right: -20, bottom: 60, background: C.verde, color: '#fff', borderRadius: 22, padding: '18px 28px', fontSize: 30, fontWeight: 800, boxShadow: '0 18px 44px rgba(31,157,85,0.45)' }}>✓ Checklist + fotos + km</Insignia>
  </Captura>
);

const Tablero: React.FC<Props> = ({ duracion }) => (
  <Captura img="procesos" duracion={duracion} desde={{ x: 0.55, y: 0.3, zoom: 1.2 }} hasta={{ x: 0.6, y: 0.4, zoom: 1.45 }}>
    <Insignia retraso={2.4} style={{ left: -20, bottom: 70, background: C.azul, color: '#fff', borderRadius: 22, padding: '18px 28px', fontSize: 30, fontWeight: 800, boxShadow: '0 18px 44px rgba(52,97,240,0.45)' }}>🔧 Cada bahía, en vivo</Insignia>
  </Captura>
);

const Reservas: React.FC<Props> = ({ duracion }) => (
  <Captura img="web-reservar" titulo="La web de tu taller · Reserva tu cita" duracion={duracion} desde={{ x: 0.5, y: 0.45, zoom: 1.15 }} hasta={{ x: 0.42, y: 0.58, zoom: 1.55 }}>
    <Insignia retraso={duracion - 2.4} style={{ right: -30, bottom: -40, width: 520 }}>
      <div style={{ background: '#dcf8c6', borderRadius: '26px 26px 6px 26px', padding: '22px 26px', boxShadow: sombra, fontSize: 25, color: '#14532d', lineHeight: 1.35 }}>
        <div style={{ fontWeight: 800, marginBottom: 6 }}>💬 WhatsApp</div>
        ¡Hola! Te recordamos tu cita de mañana a las 10:30 a. m. ¡Te esperamos! 🏍️
      </div>
    </Insignia>
  </Captura>
);

const Ventas: React.FC<Props> = ({ duracion }) => (
  <Captura img="venta" duracion={duracion} desde={{ x: 0.55, y: 0.4, zoom: 1.15 }} hasta={{ x: 0.7, y: 0.42, zoom: 1.6 }}>
    <Insignia retraso={duracion - 2.6} style={{ left: '50%', bottom: -36, marginLeft: -330, width: 660, textAlign: 'center', background: C.verde, color: '#fff', borderRadius: 999, padding: '20px 30px', fontSize: 30, fontWeight: 800, boxShadow: '0 18px 50px rgba(31,157,85,0.5)' }}>✓ Boleta electrónica aceptada por SUNAT</Insignia>
  </Captura>
);

const Inventario: React.FC<Props> = ({ duracion }) => (
  <Captura img="inventario" duracion={duracion} desde={{ x: 0.5, y: 0.2, zoom: 1.3 }} hasta={{ x: 0.55, y: 0.38, zoom: 1.6 }}>
    <Insignia retraso={2.6} style={{ right: -20, bottom: 70, background: C.ambar, color: '#fff', borderRadius: 22, padding: '18px 28px', fontSize: 30, fontWeight: 800, boxShadow: '0 18px 44px rgba(245,158,11,0.45)' }}>⚠️ Te avisa del stock mínimo</Insignia>
  </Captura>
);

const CajaReportes: React.FC<Props> = ({ duracion }) => (
  <Captura img="caja" duracion={duracion} desde={{ x: 0.5, y: 0.25, zoom: 1.25 }} hasta={{ x: 0.62, y: 0.35, zoom: 1.65 }}>
    <Insignia retraso={2.4} style={{ left: -20, bottom: 70, background: C.azul, color: '#fff', borderRadius: 22, padding: '18px 28px', fontSize: 30, fontWeight: 800, boxShadow: '0 18px 44px rgba(52,97,240,0.45)' }}>📊 Apertura, cierre y diferencias</Insignia>
  </Captura>
);

const Beneficios: React.FC<Props> = () => {
  const items = [
    { i: '🗂️', t: 'Más orden', d: 'Cada moto con su historial completo' },
    { i: '📈', t: 'Más control', d: 'Stock, caja y ganancias al día' },
    { i: '⭐', t: 'Mejor imagen', d: 'Órdenes en PDF, web y reservas online' },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 28, width: 820 }}>
      {items.map((x, k) => (
        <Aparece key={x.t} retraso={0.2 + k * 1.1} desde={60}>
          <Tarjeta style={{ ...fila, gap: 28, padding: 30 }}>
            <div style={{ width: 110, height: 110, borderRadius: 30, background: k === 2 ? 'linear-gradient(135deg,#3461f0,#7c4dff)' : C.azulClaro, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 56 }}>{x.i}</div>
            <div>
              <div style={{ fontSize: 40, fontWeight: 700, color: C.tinta }}>{x.t}</div>
              <div style={{ fontSize: 26, color: C.suave }}>{x.d}</div>
            </div>
          </Tarjeta>
        </Aparece>
      ))}
    </div>
  );
};

export const Cierre: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const e = useEntra(0);
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 34 }}>
      <div style={{ position: 'relative' }}>
        <div style={{ position: 'absolute', inset: -50, borderRadius: '50%', background: 'radial-gradient(circle, rgba(124,77,255,0.55), transparent 65%)', transform: `scale(${1 + Math.sin(frame / 10) * 0.05})` }} />
        <Logo tam={380} style={{ position: 'relative', transform: `scale(${e})` }} />
      </div>
      <Aparece retraso={0.8}>
        <div style={{ fontSize: 50, fontWeight: 700, color: '#fff', textAlign: 'center', lineHeight: 1.2 }}>
          ¿Listo para ordenar<br />tu <span style={{ color: '#8ea6ff' }}>taller</span>?
        </div>
      </Aparece>
      <Aparece retraso={1.6}>
        <div style={{ ...fila, gap: 16, background: C.azul, color: '#fff', borderRadius: 22, padding: '20px 44px', fontSize: 34, fontWeight: 700, boxShadow: '0 18px 50px rgba(52,97,240,0.5)' }}>Escríbenos por redes →</div>
      </Aparece>
      <div style={{ ...fila, gap: 18 }}>
        {['♪ TikTok @kaeltech.pe', '🌐 saas.kael.pe'].map((r, k) => (
          <Aparece key={r} retraso={2.2 + k * 0.3} desde={20}>
            <div style={{ background: 'rgba(255,255,255,0.1)', border: '2px solid rgba(255,255,255,0.22)', borderRadius: 999, padding: '14px 26px', color: '#fff', fontSize: 28, fontWeight: 600 }}>{r}</div>
          </Aparece>
        ))}
      </div>
    </div>
  );
};

export const VISUALES_WEB: Record<string, React.FC<Props>> = {
  gancho: Gancho,
  caos: Caos,
  marca: Marca,
  recepcion: Recepcion,
  tablero: Tablero,
  orden: Orden,
  reservas: Reservas,
  ventas: Ventas,
  inventario: Inventario,
  caja: CajaReportes,
  beneficios: Beneficios,
  cierre: Cierre,
};
