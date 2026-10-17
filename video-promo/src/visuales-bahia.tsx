import { interpolate, useCurrentFrame, useVideoConfig } from 'remotion';

import { C } from './estilo';
import { Logo } from './Logo';
import { Aparece, Chip, fila, Placa, Tarjeta, useEntra } from './visuales';
import { Captura, Cierre, Insignia } from './visuales-web';

type Props = { duracion: number };

/** true cuando la escena ya pasó ese segundo. */
function usePaso() {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  return (seg: number) => frame >= seg * fps;
}

const insignia = (color: string) => ({
  background: color,
  color: '#fff',
  borderRadius: 22,
  padding: '18px 28px',
  fontSize: 30,
  fontWeight: 800,
  boxShadow: `0 18px 44px ${color}73`,
});

// ---------------------------------------------------------------- escenas

const Gancho: React.FC<Props> = () => {
  const tacha = useEntra(2.4);
  const frame = useCurrentFrame();
  return (
    <div style={{ ...fila, gap: 70 }}>
      {['🧾', '✍️'].map((e, i) => (
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
  const items = [
    { i: '🔧', t: 'Repuestos sin cobrar', d: '“¿Le cobramos el filtro?”' },
    { i: '🧮', t: 'Cuentas que no cuadran', d: 'Lo anotado no coincide con la caja' },
    { i: '⏳', t: 'Clientes esperando', d: 'Su boleta todavía no sale' },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 26, width: 820 }}>
      {items.map((x, k) => (
        <Aparece key={x.t} retraso={0.2 + k * 1.2} desde={60}>
          <Tarjeta style={{ ...fila, gap: 26, padding: 28, border: `2px solid ${C.rojo}33` }}>
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
  const e = useEntra(0.3);
  const chips = ['Una cuenta por bahía', 'Boleta y factura', 'Enviado a SUNAT'];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 46 }}>
      <div style={{ position: 'relative' }}>
        <div style={{ position: 'absolute', inset: -60, borderRadius: '50%', background: 'conic-gradient(from 0deg, #3461f0, #7c4dff, #3461f0)', filter: 'blur(60px)', opacity: 0.5, transform: `rotate(${frame * 2}deg)` }} />
        <Logo tam={440} style={{ position: 'relative', transform: `scale(${e}) rotate(${interpolate(e, [0, 1], [-12, 0])}deg)` }} />
      </div>
      <div style={{ display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: 14, width: 900 }}>
        {chips.map((c, i) => (
          <Aparece key={c} retraso={1.6 + i * 0.35} desde={20}>
            <div style={{ background: 'rgba(255,255,255,0.1)', border: '2px solid rgba(255,255,255,0.22)', borderRadius: 999, padding: '12px 26px', color: '#fff', fontSize: 30, fontWeight: 600 }}>✓ {c}</div>
          </Aparece>
        ))}
      </div>
    </div>
  );
};

const Tablero: React.FC<Props> = ({ duracion }) => (
  <Captura img="bahia-tablero" titulo="Ventas por Bahía · Kael Tech" duracion={duracion} desde={{ x: 0.55, y: 0.3, zoom: 1.2 }} hasta={{ x: 0.66, y: 0.45, zoom: 1.75 }}>
    <Insignia retraso={2.8} style={{ left: -20, bottom: 70, ...insignia(C.azul) }}>🏍️ Bahía 2 · cuenta abierta</Insignia>
  </Captura>
);

const Vivo: React.FC<Props> = ({ duracion }) => (
  <Captura img="bahia-tablero-2" titulo="Ventas por Bahía · Kael Tech" duracion={duracion} desde={{ x: 0.42, y: 0.45, zoom: 1.5 }} hasta={{ x: 0.42, y: 0.62, zoom: 2 }}>
    <Insignia retraso={1.4} style={{ right: -20, bottom: 70, ...insignia(C.verde) }}>✓ Se guarda al instante</Insignia>
  </Captura>
);

const Cobro: React.FC<Props> = ({ duracion }) => (
  <Captura img="bahia-cobro-2" titulo="Punto de venta · Kael Tech" duracion={duracion} desde={{ x: 0.35, y: 0.25, zoom: 1.25 }} hasta={{ x: 0.2, y: 0.55, zoom: 1.7 }}>
    <Insignia retraso={2.6} style={{ right: -20, bottom: 70, ...insignia(C.morado) }}>⚡ Cliente, moto y productos listos</Insignia>
  </Captura>
);

/** Modal de cobro recreado (en el entorno local la facturación SUNAT no está configurada). */
const Comprobante: React.FC<Props> = ({ duracion }) => {
  const ya = usePaso();
  const ok = useEntra(duracion - 1.6);
  const tipo = ya(1.6) ? 'Factura' : ya(0.8) ? 'Boleta' : 'Nota Venta';
  const enviando = ya(2.2) && !ya(duracion - 1.6);
  const frame = useCurrentFrame();
  return (
    <div style={{ position: 'relative' }}>
      <Aparece retraso={0}>
        <div style={{ width: 900, background: '#fff', borderRadius: 28, boxShadow: '0 40px 110px rgba(0,0,0,0.45)', overflow: 'hidden' }}>
          <div style={{ ...fila, justifyContent: 'space-between', padding: '24px 32px', background: 'linear-gradient(135deg,#3461f0,#7c4dff)', color: '#fff' }}>
            <div style={{ fontSize: 32, fontWeight: 800 }}>💳 Cobrar</div>
            <div style={{ fontSize: 36, fontWeight: 800 }}>S/ 69.00</div>
          </div>
          <div style={{ padding: 32, display: 'flex', flexDirection: 'column', gap: 24 }}>
            <div style={{ fontSize: 20, fontWeight: 700, letterSpacing: 2, color: C.suave }}>TIPO DE COMPROBANTE</div>
            <div style={{ ...fila, gap: 14 }}>
              {['Nota Venta', 'Boleta', 'Factura'].map((t) => (
                <div key={t} style={{ flex: 1, padding: '20px 0', borderRadius: 16, textAlign: 'center', fontSize: 28, fontWeight: 700, background: tipo === t ? C.azul : C.claro, color: tipo === t ? '#fff' : C.tinta, boxShadow: tipo === t ? '0 12px 30px rgba(52,97,240,0.4)' : 'none' }}>{t}</div>
              ))}
            </div>
            <div style={{ ...fila, gap: 16, background: C.claro, borderRadius: 16, padding: '18px 22px', fontSize: 24, color: C.tinta }}>
              <span>🏢</span>
              <span>RUC 20XXXXXXXXX · Cliente empresa</span>
            </div>
            <div style={{ background: enviando ? C.ambar : C.verde, color: '#fff', borderRadius: 18, padding: 24, textAlign: 'center', fontSize: 30, fontWeight: 800 }}>
              {enviando ? `Enviando a SUNAT${'.'.repeat(1 + (Math.floor(frame / 6) % 3))}` : ya(2.2) ? '✓ Emitida' : 'Emitir comprobante'}
            </div>
          </div>
        </div>
      </Aparece>
      <div style={{ position: 'absolute', left: '50%', bottom: -60, transform: `translateX(-50%) scale(${ok})`, ...fila, gap: 14, background: C.verde, color: '#fff', borderRadius: 999, padding: '20px 40px', fontSize: 32, fontWeight: 800, boxShadow: '0 18px 50px rgba(31,157,85,0.5)', whiteSpace: 'nowrap' }}>
        ✓ Factura F001-00128 aceptada por SUNAT
      </div>
    </div>
  );
};

const Reintento: React.FC<Props> = ({ duracion }) => {
  const ya = usePaso();
  const pasos = [
    { t: 'Enviado a SUNAT', d: 'Boleta B001-00245', icono: '📤', color: C.azul, en: 0.2 },
    { t: 'SUNAT no responde', d: 'Queda pendiente, no se pierde', icono: '⚠️', color: C.ambar, en: 1.4 },
    { t: 'Reintento automático', d: 'El sistema lo vuelve a enviar solo', icono: '🔁', color: C.morado, en: 2.8 },
    { t: 'Aceptado por SUNAT', d: 'Comprobante en regla', icono: '✅', color: C.verde, en: duracion - 2.4 },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 0, width: 820 }}>
      {pasos.map((p, i) => {
        const visible = ya(p.en);
        return (
          <div key={p.t} style={{ display: 'flex', gap: 24, opacity: visible ? 1 : 0.25 }}>
            <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
              <div style={{ width: 84, height: 84, borderRadius: 42, background: visible ? p.color : '#fff', border: `3px solid ${visible ? p.color : C.borde}`, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 40, boxShadow: visible ? `0 12px 30px ${p.color}55` : 'none' }}>{p.icono}</div>
              {i < pasos.length - 1 ? <div style={{ width: 5, height: 70, background: ya(pasos[i + 1].en) ? p.color : C.borde }} /> : null}
            </div>
            <div style={{ paddingTop: 10 }}>
              <div style={{ fontSize: 38, fontWeight: 700, color: C.tinta }}>{p.t}</div>
              <div style={{ fontSize: 26, color: C.suave }}>{p.d}</div>
            </div>
          </div>
        );
      })}
    </div>
  );
};

const Conectado: React.FC<Props> = ({ duracion }) => {
  const ya = usePaso();
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const stock = Math.round(interpolate(frame, [1 * fps, 2 * fps], [10, 9], { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' }));
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 28, width: 860 }}>
      <Aparece retraso={0.2}>
        <Tarjeta style={{ padding: 30, display: 'flex', flexDirection: 'column', gap: 14 }}>
          <div style={{ ...fila, justifyContent: 'space-between' }}>
            <div style={{ fontSize: 20, fontWeight: 700, letterSpacing: 2, color: C.suave }}>📦 INVENTARIO</div>
            <Chip texto="Stock al día" color={C.verde} tam={22} />
          </div>
          <div style={{ ...fila, justifyContent: 'space-between', fontSize: 30, color: C.tinta }}>
            <span>Filtro de aceite Honda CBR</span>
            <b style={{ fontSize: 40, color: ya(1) ? C.azul : C.tinta }}>{stock} und.</b>
          </div>
        </Tarjeta>
      </Aparece>
      <Aparece retraso={duracion / 2}>
        <Tarjeta style={{ padding: 30, display: 'flex', flexDirection: 'column', gap: 14 }}>
          <div style={{ ...fila, justifyContent: 'space-between' }}>
            <div style={{ fontSize: 20, fontWeight: 700, letterSpacing: 2, color: C.suave }}>📋 ORDEN DE SERVICIO</div>
            <Placa valor="TF5·DSA" tam={22} />
          </div>
          {[['Filtro de aceite Honda CBR', '15.00'], ['Arandela de drenaje', '4.00'], ['Aceite 10W40 sintético', '50.00']].map(([d, p], i) => (
            <Aparece key={d} retraso={duracion / 2 + 0.4 + i * 0.3} desde={12}>
              <div style={{ ...fila, justifyContent: 'space-between', fontSize: 26, color: C.tinta, borderBottom: `1px solid ${C.borde}`, paddingBottom: 10 }}>
                <span>✓ {d}</span>
                <b>S/ {p}</b>
              </div>
            </Aparece>
          ))}
        </Tarjeta>
      </Aparece>
    </div>
  );
};

const Beneficios: React.FC<Props> = () => {
  const items = [
    { i: '💰', t: 'Cobras todo', d: 'Ningún repuesto se queda sin cobrar' },
    { i: '⚡', t: 'Sin olvidos ni demoras', d: 'La venta sale lista desde la bahía' },
    { i: '🧾', t: 'Comprobantes en regla', d: 'Boletas y facturas en SUNAT' },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 28, width: 820 }}>
      {items.map((x, k) => (
        <Aparece key={x.t} retraso={0.2 + k * 1} desde={60}>
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


export const VISUALES_BAHIA: Record<string, React.FC<Props>> = {
  gancho: Gancho,
  caos: Caos,
  marca: Marca,
  tablero: Tablero,
  vivo: Vivo,
  cobro: Cobro,
  comprobante: Comprobante,
  reintento: Reintento,
  conectado: Conectado,
  beneficios: Beneficios,
  cierre: Cierre,
};
