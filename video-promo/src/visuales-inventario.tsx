import { interpolate, useCurrentFrame, useVideoConfig } from 'remotion';

import { C } from './estilo';
import { Logo } from './Logo';
import { Aparece, fila, Tarjeta, useEntra } from './visuales';
import { Captura, Cierre, Insignia } from './visuales-web';

type Props = { duracion: number };

const insignia = (color: string) => ({
  background: color,
  color: '#fff',
  borderRadius: 22,
  padding: '18px 28px',
  fontSize: 30,
  fontWeight: 800,
  boxShadow: `0 18px 44px ${color}73`,
});

const Gancho: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const signo = useEntra(1.2);
  return (
    <div style={{ position: 'relative', width: 560, height: 560, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      {[0, 1, 2].map((i) => {
        const p = (frame / 40 + i / 3) % 1;
        return <div key={i} style={{ position: 'absolute', width: 300 + p * 360, height: 300 + p * 360, borderRadius: '50%', border: `4px solid rgba(124,140,255,${0.5 * (1 - p)})` }} />;
      })}
      <div style={{ width: 340, height: 340, borderRadius: '50%', background: 'linear-gradient(135deg, #3461f0, #7c4dff)', display: 'flex', alignItems: 'center', justifyContent: 'center', boxShadow: '0 30px 90px rgba(52,97,240,0.55)' }}>
        <span style={{ fontSize: 170 }}>📦</span>
      </div>
      <div style={{ position: 'absolute', right: 40, top: 40, width: 150, height: 150, borderRadius: 75, background: C.ambar, color: '#fff', fontSize: 100, fontWeight: 800, display: 'flex', alignItems: 'center', justifyContent: 'center', transform: `scale(${signo}) rotate(${Math.sin(frame / 6) * 8}deg)`, boxShadow: '0 18px 50px rgba(245,158,11,0.55)' }}>?</div>
    </div>
  );
};

const Caos: React.FC<Props> = () => {
  const items = [
    { i: '🧮', t: 'Stock que no cuadra', d: 'El sistema dice 5, en el estante hay 2' },
    { i: '🚫', t: 'Se acaba sin avisar', d: '“Ese filtro justo se terminó”' },
    { i: '💸', t: 'Plata parada', d: 'Repuestos que nadie compra' },
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
          </Tarjeta>
        </Aparece>
      ))}
    </div>
  );
};

const Marca: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const e = useEntra(0.3);
  const chips = ['Productos', 'Lotes y kardex', 'Traslados', 'Ajustes', 'Inventario valorizado'];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 46 }}>
      <div style={{ position: 'relative' }}>
        <div style={{ position: 'absolute', inset: -60, borderRadius: '50%', background: 'conic-gradient(from 0deg, #3461f0, #7c4dff, #3461f0)', filter: 'blur(60px)', opacity: 0.5, transform: `rotate(${frame * 2}deg)` }} />
        <Logo tam={420} style={{ position: 'relative', transform: `scale(${e}) rotate(${interpolate(e, [0, 1], [-12, 0])}deg)` }} />
      </div>
      <div style={{ display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: 14, width: 920 }}>
        {chips.map((c, i) => (
          <Aparece key={c} retraso={1.8 + i * 0.25} desde={20}>
            <div style={{ background: 'rgba(255,255,255,0.1)', border: '2px solid rgba(255,255,255,0.22)', borderRadius: 999, padding: '12px 26px', color: '#fff', fontSize: 28, fontWeight: 600 }}>📦 {c}</div>
          </Aparece>
        ))}
      </div>
    </div>
  );
};

const Productos: React.FC<Props> = ({ duracion }) => (
  <Captura img="inv-productos" titulo="Productos · Kael Tech" duracion={duracion} desde={{ x: 0.45, y: 0.4, zoom: 1.15 }} hasta={{ x: 0.2, y: 0.85, zoom: 1.7 }}>
    <Insignia retraso={2.8} style={{ right: -20, bottom: 70, ...insignia(C.azul) }}>🔎 Código interno y de fabricación</Insignia>
  </Captura>
);

/** Vista previa de la carga masiva (recreada): marca los que ya existen antes de importar. */
const Importar: React.FC<Props> = ({ duracion }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const subida = interpolate(frame, [0.4 * fps, 2 * fps], [0, 1], { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' });
  const filas = [
    { n: 'Filtro de aire Honda XR150', c: 'INT-0231', e: 'nuevo' },
    { n: 'Pastillas de freno Bajaj NS200', c: 'INT-0118', e: 'existe' },
    { n: 'Cadena 428H 120L', c: 'INT-0302', e: 'nuevo' },
    { n: 'Bujía NGK CR7HSA', c: 'INT-0045', e: 'existe' },
    { n: 'Aceite Motul 5100 15W50', c: 'INT-0410', e: 'nuevo' },
  ];
  const resumen = useEntra(duracion - 2.2);
  return (
    <div style={{ display: 'flex', flexDirection: 'column', gap: 22, width: 940 }}>
      <Aparece retraso={0}>
        <Tarjeta style={{ ...fila, gap: 22, padding: 24 }}>
          <div style={{ width: 84, height: 84, borderRadius: 20, background: '#16a34a', color: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 30, fontWeight: 800 }}>XLS</div>
          <div style={{ flex: 1 }}>
            <div style={{ fontSize: 28, fontWeight: 700, color: C.tinta }}>productos-taller.xlsx</div>
            <div style={{ height: 14, borderRadius: 7, background: C.borde, marginTop: 10 }}>
              <div style={{ height: 14, borderRadius: 7, width: `${subida * 100}%`, background: C.verde }} />
            </div>
          </div>
          <div style={{ fontSize: 26, fontWeight: 700, color: subida >= 1 ? C.verde : C.suave }}>{subida >= 1 ? '✓ Listo' : `${Math.round(subida * 100)}%`}</div>
        </Tarjeta>
      </Aparece>
      <Tarjeta style={{ padding: 0, overflow: 'hidden', gap: 0 }}>
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 170px 230px', padding: '16px 24px', background: '#e9edf5', fontSize: 19, fontWeight: 700, color: C.suave }}>
          <span>PRODUCTO</span>
          <span>CÓD. INTERNO</span>
          <span>VISTA PREVIA</span>
        </div>
        {filas.map((x, i) => (
          <Aparece key={x.n} retraso={2.2 + i * 0.35} desde={14}>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 170px 230px', padding: '16px 24px', borderTop: `1px solid ${C.borde}`, fontSize: 22, color: C.tinta, alignItems: 'center', background: x.e === 'existe' ? '#fffbeb' : '#fff' }}>
              <span>{x.n}</span>
              <span style={{ color: C.suave }}>{x.c}</span>
              {x.e === 'existe' ? (
                <span style={{ fontSize: 19, fontWeight: 700, color: '#b45309' }}>⚠️ Ya existe · ☑ Sumar stock</span>
              ) : (
                <span style={{ fontSize: 19, fontWeight: 700, color: C.verde }}>✓ Nuevo</span>
              )}
            </div>
          </Aparece>
        ))}
      </Tarjeta>
      <div style={{ ...fila, gap: 14, justifyContent: 'center', opacity: resumen, transform: `scale(${interpolate(resumen, [0, 1], [0.9, 1])})` }}>
        <div style={{ ...insignia(C.verde), fontSize: 26 }}>3 nuevos</div>
        <div style={{ ...insignia(C.ambar), fontSize: 26 }}>2 repetidos</div>
      </div>
    </div>
  );
};

const Detalle: React.FC<Props> = ({ duracion }) => (
  <Captura img="inv-detalle" titulo="Ficha del producto · Kael Tech" duracion={duracion} desde={{ x: 0.5, y: 0.3, zoom: 1 }} hasta={{ x: 0.62, y: 0.55, zoom: 1.35 }}>
    <Insignia retraso={2.4} style={{ left: -20, bottom: 70, ...insignia(C.verde) }}>💰 Margen: 133%</Insignia>
  </Captura>
);

const Minimo: React.FC<Props> = ({ duracion }) => (
  <Captura img="inv-control" titulo="Control de inventario · Kael Tech" duracion={duracion} desde={{ x: 0.5, y: 0.05, zoom: 1.2 }} hasta={{ x: 0.45, y: 0.15, zoom: 1.6 }}>
    <Insignia retraso={1.4} style={{ right: -20, bottom: 70, ...insignia(C.ambar) }}>⚠️ 7 productos por reponer</Insignia>
  </Captura>
);

const Kardex: React.FC<Props> = ({ duracion }) => (
  <Captura img="inv-kardex" titulo="Kardex · Kael Tech" duracion={duracion} desde={{ x: 0.5, y: 0.25, zoom: 1.05 }} hasta={{ x: 0.5, y: 0.5, zoom: 1.35 }}>
    <Insignia retraso={2.2} style={{ right: -20, top: 110, ...insignia(C.azul) }}>📈 Entradas y salidas</Insignia>
  </Captura>
);

const Traslado: React.FC<Props> = ({ duracion }) => (
  <Captura img="inv-traslado" titulo="Traslado de stock · Kael Tech" duracion={duracion} desde={{ x: 0.45, y: 0.35, zoom: 1.15 }} hasta={{ x: 0.62, y: 0.4, zoom: 1.6 }}>
    <Insignia retraso={2.2} style={{ left: -20, bottom: 70, ...insignia(C.morado) }}>🚚 Sede central → Sucursal</Insignia>
  </Captura>
);

const Ajuste: React.FC<Props> = ({ duracion }) => (
  <Captura img="inv-ajuste" titulo="Ajuste de inventario · Kael Tech" duracion={duracion} desde={{ x: 0.45, y: 0.35, zoom: 1.15 }} hasta={{ x: 0.42, y: 0.45, zoom: 1.5 }}>
    <Insignia retraso={2.2} style={{ right: -20, bottom: 70, ...insignia(C.rojo) }}>📝 Merma, rotura o vencimiento</Insignia>
  </Captura>
);

const Valorizado: React.FC<Props> = ({ duracion }) => (
  <Captura img="inv-valorizado" titulo="Inventario valorizado · Kael Tech" duracion={duracion} desde={{ x: 0.55, y: 0.45, zoom: 1.15 }} hasta={{ x: 0.58, y: 0.6, zoom: 1.45 }}>
    <Insignia retraso={2.4} style={{ left: -20, bottom: 70, ...insignia(C.verde) }}>💰 Ganancia potencial a la vista</Insignia>
  </Captura>
);

const Beneficios: React.FC<Props> = () => {
  const items = [
    { i: '📉', t: 'Menos pérdidas', d: 'Mermas y ajustes con su motivo' },
    { i: '🛒', t: 'Compras inteligentes', d: 'Repones justo lo que se acaba' },
    { i: '🗂️', t: 'Almacén ordenado', d: 'Stock real en todas tus sedes' },
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

export const VISUALES_INVENTARIO: Record<string, React.FC<Props>> = {
  gancho: Gancho,
  caos: Caos,
  marca: Marca,
  productos: Productos,
  importar: Importar,
  detalle: Detalle,
  minimo: Minimo,
  kardex: Kardex,
  traslado: Traslado,
  ajuste: Ajuste,
  valorizado: Valorizado,
  beneficios: Beneficios,
  cierre: Cierre,
};
