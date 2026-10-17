import type { CSSProperties, ReactNode } from 'react';
import { interpolate, spring, useCurrentFrame, useVideoConfig } from 'remotion';

import { C, sombra } from './estilo';
import { Logo } from './Logo';

type Props = { duracion: number };

// ---------------------------------------------------------------- ayudas

/** 0 → 1 con resorte, empezando en `retraso` segundos. */
export function useEntra(retraso = 0, suave = false) {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  return spring({ frame: frame - Math.round(retraso * fps), fps, config: suave ? { damping: 200 } : { damping: 13, stiffness: 140 } });
}

export const fila: CSSProperties = { display: 'flex', alignItems: 'center' };

export const Tarjeta: React.FC<{ children: ReactNode; style?: CSSProperties }> = ({ children, style }) => (
  <div style={{ background: C.blanco, borderRadius: 28, boxShadow: sombra, padding: 24, ...style }}>{children}</div>
);

export const Placa: React.FC<{ valor: string; tam?: number }> = ({ valor, tam = 26 }) => (
  <div style={{ display: 'inline-block', position: 'relative', background: '#fff', border: '2.5px solid #334155', borderRadius: 8, padding: `${tam * 0.12}px ${tam * 0.6}px`, overflow: 'hidden' }}>
    <div style={{ position: 'absolute', left: 0, right: 0, top: 0, height: 4, background: C.azul }} />
    <span style={{ fontSize: tam, fontWeight: 700, letterSpacing: 2, color: '#1e293b' }}>{valor}</span>
  </div>
);

export const Chip: React.FC<{ texto: string; color?: string; fondo?: string; tam?: number }> = ({ texto, color = C.azul, fondo, tam = 22 }) => (
  <span style={{ display: 'inline-flex', alignItems: 'center', gap: 8, fontSize: tam, fontWeight: 600, color, background: fondo ?? color + '1f', padding: '6px 16px', borderRadius: 999 }}>
    <span style={{ width: 10, height: 10, borderRadius: 5, background: color }} />
    {texto}
  </span>
);

/** Teléfono con la pantalla de la app. */
const Telefono: React.FC<{ children: ReactNode; escala?: number; style?: CSSProperties }> = ({ children, escala = 1, style }) => (
  <div style={{ width: 470, height: 960, borderRadius: 70, background: '#0b1020', padding: 16, boxShadow: '0 50px 120px rgba(10,15,31,0.35)', transform: `scale(${escala})`, ...style }}>
    <div style={{ width: '100%', height: '100%', borderRadius: 56, background: C.claro, overflow: 'hidden', position: 'relative' }}>
      <div style={{ position: 'absolute', top: 16, left: '50%', marginLeft: -60, width: 120, height: 34, borderRadius: 20, background: '#0b1020', zIndex: 5 }} />
      {children}
    </div>
  </div>
);

/** Cabecera azul de las pantallas de la app. */
const Cabecera: React.FC<{ titulo: string; sub?: string; color?: string }> = ({ titulo, sub, color = C.azul }) => (
  <div style={{ background: `linear-gradient(135deg, #0f1d4d, ${color})`, padding: '78px 30px 30px', borderBottomLeftRadius: 34, borderBottomRightRadius: 34 }}>
    <div style={{ fontSize: 17, fontWeight: 700, letterSpacing: 2, color: 'rgba(255,255,255,0.7)' }}>MOTOS DEL VALLE</div>
    <div style={{ fontSize: 38, fontWeight: 700, color: '#fff' }}>{titulo}</div>
    {sub ? <div style={{ fontSize: 20, color: 'rgba(255,255,255,0.85)' }}>{sub}</div> : null}
  </div>
);

export const Aparece: React.FC<{ retraso: number; children: ReactNode; desde?: number; style?: CSSProperties }> = ({ retraso, children, desde = 40, style }) => {
  const e = useEntra(retraso);
  return <div style={{ opacity: Math.min(1, e * 1.4), transform: `translateY(${interpolate(e, [0, 1], [desde, 0])}px)`, ...style }}>{children}</div>;
};

// ---------------------------------------------------------------- escenas

const Gancho: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const e = useEntra(0);
  const pulso = 1 + Math.sin(frame / 6) * 0.03;
  return (
    <div style={{ position: 'relative', width: 560, height: 560, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      {[0, 1, 2].map((i) => {
        const p = ((frame / 40 + i / 3) % 1);
        return <div key={i} style={{ position: 'absolute', width: 300 + p * 360, height: 300 + p * 360, borderRadius: '50%', border: `4px solid rgba(124,140,255,${0.5 * (1 - p)})` }} />;
      })}
      <div style={{ width: 340, height: 340, borderRadius: '50%', background: 'linear-gradient(135deg, #3461f0, #7c4dff)', display: 'flex', alignItems: 'center', justifyContent: 'center', transform: `scale(${e * pulso})`, boxShadow: '0 30px 90px rgba(52,97,240,0.55)' }}>
        <span style={{ fontSize: 190 }}>🏍️</span>
      </div>
    </div>
  );
};

const Llamadas: React.FC<Props> = ({ duracion }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const perdidas = Math.min(12, Math.floor(interpolate(frame, [0, duracion * fps], [1, 13])));
  const vibra = Math.sin(frame * 1.6) * 4;
  const mensajes = ['¿Ya está lista mi moto? 🙏', '¿A qué hora la recojo?', '¿Qué le cambiaron?', '¿Ya está lista? 😅'];
  return (
    <div style={{ ...fila, gap: 40 }}>
      <Telefono escala={0.82} style={{ transform: `scale(0.82) rotate(${vibra * 0.3}deg)` }}>
        <div style={{ height: '100%', background: 'linear-gradient(180deg,#111a3a,#0b1020)', display: 'flex', flexDirection: 'column', alignItems: 'center', paddingTop: 170, gap: 16 }}>
          <div style={{ fontSize: 26, color: 'rgba(255,255,255,0.7)' }}>Llamada entrante</div>
          <div style={{ fontSize: 46, fontWeight: 700, color: '#fff' }}>Cliente</div>
          <div style={{ width: 170, height: 170, borderRadius: '50%', background: 'rgba(255,255,255,0.12)', display: 'flex', alignItems: 'center', justifyContent: 'center', marginTop: 40, fontSize: 90 }}>👤</div>
          <div style={{ ...fila, gap: 90, marginTop: 'auto', marginBottom: 110 }}>
            <div style={{ width: 110, height: 110, borderRadius: '50%', background: C.rojo, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 50 }}>📵</div>
            <div style={{ width: 110, height: 110, borderRadius: '50%', background: C.verde, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 50, transform: `scale(${1 + Math.abs(Math.sin(frame / 5)) * 0.12})` }}>📞</div>
          </div>
        </div>
      </Telefono>
      <div style={{ display: 'flex', flexDirection: 'column', gap: 22, width: 420 }}>
        <Aparece retraso={0.1}>
          <div style={{ ...fila, gap: 14, background: C.rojo, color: '#fff', borderRadius: 999, padding: '12px 26px', alignSelf: 'flex-start', fontSize: 30, fontWeight: 700, boxShadow: '0 14px 40px rgba(239,68,68,0.4)' }}>
            📞 {perdidas} llamadas perdidas
          </div>
        </Aparece>
        {mensajes.map((m, i) => (
          <Aparece key={m} retraso={0.6 + i * 1.1} desde={30}>
            <div style={{ background: C.blanco, borderRadius: '28px 28px 28px 6px', padding: '18px 26px', fontSize: 30, fontWeight: 500, color: C.tinta, boxShadow: sombra }}>{m}</div>
          </Aparece>
        ))}
      </div>
    </div>
  );
};

const Marca: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const e = useEntra(0.1);
  const chip = useEntra(1.4);
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 50 }}>
      <div style={{ position: 'relative', width: 520, height: 520, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <div style={{ position: 'absolute', width: 520, height: 520, borderRadius: '50%', background: 'conic-gradient(from 0deg, #3461f0, #7c4dff, #3461f0)', filter: 'blur(40px)', opacity: 0.55, transform: `rotate(${frame * 2}deg)` }} />
        <Logo tam={420} style={{ transform: `scale(${e}) rotate(${interpolate(e, [0, 1], [-25, 0])}deg)` }} />
      </div>
      <div style={{ opacity: chip, transform: `translateY(${interpolate(chip, [0, 1], [30, 0])}px)`, ...fila, gap: 14, background: 'rgba(255,255,255,0.1)', border: '2px solid rgba(255,255,255,0.25)', borderRadius: 999, padding: '16px 34px', color: '#fff', fontSize: 34, fontWeight: 600 }}>
        📱 App para los clientes de tu taller
      </div>
    </div>
  );
};

const Qr: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const linea = interpolate(frame % 50, [0, 50], [0, 300]);
  const codigo = '482915'.slice(0, Math.max(0, Math.floor(interpolate(frame, [3.6 * fps, 5.6 * fps], [0, 6], { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' }))));
  // Patrón fijo con forma de QR.
  const celdas = Array.from({ length: 121 }, (_, i) => ((i * 7919) % 13) % 3 === 0 || [0, 1, 2, 11, 13, 22, 23, 24, 8, 9, 10, 19, 21, 30, 31, 32, 88, 89, 90, 99, 101, 110, 111, 112].includes(i));
  return (
    <div style={{ ...fila, gap: 50 }}>
      <Aparece retraso={0}>
        <Tarjeta style={{ padding: 34, display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 18 }}>
          <div style={{ position: 'relative', width: 300, height: 300, display: 'grid', gridTemplateColumns: 'repeat(11, 1fr)', gap: 3 }}>
            {celdas.map((on, i) => <div key={i} style={{ background: on ? C.tinta : 'transparent', borderRadius: 3 }} />)}
            <div style={{ position: 'absolute', left: -10, right: -10, top: linea, height: 6, background: C.azul, boxShadow: '0 0 24px 6px rgba(52,97,240,0.6)', borderRadius: 3 }} />
          </div>
          <div style={{ fontSize: 26, fontWeight: 700, color: C.tinta }}>Escanea el QR del taller</div>
        </Tarjeta>
      </Aparece>
      <Aparece retraso={1.6}>
        <Telefono escala={0.82}>
          <div style={{ background: 'linear-gradient(135deg,#0f1d4d,#3461f0)', height: 360, display: 'flex', flexDirection: 'column', alignItems: 'center', justifyContent: 'flex-end', paddingBottom: 34, gap: 10 }}>
            <div style={{ width: 120, height: 120, borderRadius: 32, background: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 64 }}>🏍️</div>
            <div style={{ fontSize: 36, fontWeight: 700, color: '#fff' }}>Motos del Valle</div>
          </div>
          <div style={{ padding: 28, display: 'flex', flexDirection: 'column', gap: 20 }}>
            <div style={{ fontSize: 30, fontWeight: 700, color: C.tinta }}>Ingresa con tu celular</div>
            <div style={{ border: `2px solid ${C.borde}`, borderRadius: 18, padding: '18px 22px', fontSize: 30, color: C.tinta, background: '#fff' }}>987 654 321</div>
            <div style={{ ...fila, gap: 10, justifyContent: 'space-between' }}>
              {Array.from({ length: 6 }, (_, i) => (
                <div key={i} style={{ width: 54, height: 70, borderRadius: 14, border: `2.5px solid ${codigo[i] ? C.azul : C.borde}`, background: '#fff', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 34, fontWeight: 700, color: C.tinta }}>
                  {codigo[i] ?? ''}
                </div>
              ))}
            </div>
            <div style={{ background: C.azul, color: '#fff', borderRadius: 18, padding: 20, textAlign: 'center', fontSize: 28, fontWeight: 700, opacity: codigo.length === 6 ? 1 : 0.5 }}>Ingresar</div>
          </div>
        </Telefono>
      </Aparece>
    </div>
  );
};

const motos = [
  { placa: 'TRD·452', modelo: 'Yamaha Z 250', info: '6 servicios · último 16 sep', chip: 'Próx. servicio: 15 000 km' },
  { placa: 'ABC·452', modelo: 'Pulsar NS 200', info: '3 servicios · último 8 sep', chip: 'Cambio de aceite: 20 oct' },
];

const Garaje: React.FC<Props> = () => (
  <Telefono>
    <Cabecera titulo="Hola, Carlos" sub="2 motos en tu garaje" />
    <div style={{ padding: 24, display: 'flex', flexDirection: 'column', gap: 18 }}>
      {motos.map((m, i) => (
        <Aparece key={m.placa} retraso={0.5 + i * 0.6}>
          <Tarjeta style={{ padding: 22, display: 'flex', flexDirection: 'column', gap: 14, borderRadius: 24 }}>
            <div style={{ ...fila, gap: 16 }}>
              <Placa valor={m.placa} />
              <div>
                <div style={{ fontSize: 24, fontWeight: 600, color: C.tinta }}>{m.modelo}</div>
                <div style={{ fontSize: 18, color: C.suave }}>{m.info}</div>
              </div>
            </div>
            <Aparece retraso={2.6 + i * 0.5} desde={10}>
              <Chip texto={m.chip} tam={19} />
            </Aparece>
          </Tarjeta>
        </Aparece>
      ))}
      <Aparece retraso={1.8}>
        <Tarjeta style={{ padding: 22, borderRadius: 24, border: `2px solid ${C.ambar}66` }}>
          <div style={{ fontSize: 16, fontWeight: 700, letterSpacing: 2, color: C.ambar }}>AHORA EN EL TALLER</div>
          <div style={{ ...fila, gap: 14, marginTop: 10 }}>
            <Placa valor="TRD·452" tam={20} />
            <span style={{ fontSize: 19, color: C.suave }}>Mantenimiento general</span>
          </div>
        </Tarjeta>
      </Aparece>
    </div>
  </Telefono>
);

const Avance: React.FC<Props> = ({ duracion }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const pasos = [
    { t: 'Ingresó al taller', f: '16 oct · 9:10 a. m.' },
    { t: 'Trabajo terminado', f: '16 oct · 4:30 p. m.' },
    { t: 'Lista para entregar', f: 'Te esperamos 🙌' },
  ];
  const hechos = Math.floor(interpolate(frame, [0.8 * fps, (duracion - 1) * fps], [1, 3.99], { extrapolateLeft: 'clamp', extrapolateRight: 'clamp' }));
  return (
    <Telefono>
      <Cabecera titulo="MGI-000008" sub="Mantenimiento general" />
      <div style={{ padding: 24 }}>
        <Tarjeta style={{ borderRadius: 24, padding: 28 }}>
          <div style={{ ...fila, justifyContent: 'space-between', marginBottom: 26 }}>
            <Placa valor="TRD·452" />
            <Chip texto={hechos >= 3 ? 'Lista' : hechos >= 2 ? 'Terminado' : 'En el taller'} color={hechos >= 2 ? C.verde : C.ambar} tam={19} />
          </div>
          {pasos.map((p, i) => {
            const ok = i < hechos;
            return (
              <div key={p.t} style={{ display: 'flex', gap: 20 }}>
                <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center' }}>
                  <div style={{ width: 52, height: 52, borderRadius: 26, background: ok ? C.verde : '#fff', border: `3px solid ${ok ? C.verde : C.borde}`, display: 'flex', alignItems: 'center', justifyContent: 'center', color: '#fff', fontSize: 28, fontWeight: 800, transform: `scale(${ok ? 1 : 0.9})` }}>
                    {ok ? '✓' : ''}
                  </div>
                  {i < 2 ? <div style={{ width: 4, height: 90, background: i < hechos - 1 ? C.verde : C.borde }} /> : null}
                </div>
                <div style={{ paddingTop: 8 }}>
                  <div style={{ fontSize: 27, fontWeight: 700, color: ok ? C.tinta : '#94a3b8' }}>{p.t}</div>
                  <div style={{ fontSize: 20, color: C.suave }}>{ok ? p.f : 'Pendiente'}</div>
                </div>
              </div>
            );
          })}
        </Tarjeta>
      </div>
    </Telefono>
  );
};

const Detalle: React.FC<Props> = ({ duracion }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const pdf = useEntra(duracion - 2.4);
  const fotos = ['linear-gradient(135deg,#334155,#64748b)', 'linear-gradient(135deg,#1e3a8a,#3461f0)', 'linear-gradient(135deg,#78350f,#f59e0b)'];
  return (
    <Telefono>
      <Cabecera titulo="Detalle del servicio" sub="TRD·452 · Yamaha Z 250" />
      <div style={{ padding: 22, display: 'flex', flexDirection: 'column', gap: 16 }}>
        <Tarjeta style={{ borderRadius: 24, padding: 20 }}>
          <div style={{ fontSize: 16, fontWeight: 700, letterSpacing: 2, color: C.suave, marginBottom: 12 }}>FOTOS DEL TRABAJO</div>
          <div style={{ ...fila, gap: 12 }}>
            {fotos.map((f, i) => (
              <Aparece key={i} retraso={0.3 + i * 0.35} desde={20}>
                <div style={{ width: 118, height: 118, borderRadius: 18, background: f, display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: 50 }}>{['🔧', '⚙️', '🛢️'][i]}</div>
              </Aparece>
            ))}
          </div>
        </Tarjeta>
        <Aparece retraso={2}>
          <Tarjeta style={{ borderRadius: 24, padding: 20, display: 'flex', flexDirection: 'column', gap: 12 }}>
            <div style={{ fontSize: 16, fontWeight: 700, letterSpacing: 2, color: C.suave }}>REPUESTOS USADOS</div>
            {[['Filtro de aceite', '25.50'], ['Aceite 20W50 sintético', '48.00'], ['Bujía NGK', '18.00']].map(([d, p]) => (
              <div key={d} style={{ ...fila, justifyContent: 'space-between', fontSize: 22, color: C.tinta }}>
                <span>{d}</span>
                <b>S/ {p}</b>
              </div>
            ))}
          </Tarjeta>
        </Aparece>
        <div style={{ opacity: pdf, transform: `scale(${interpolate(pdf, [0, 1], [0.9, 1]) + Math.sin(frame / 5) * 0.01 * pdf})`, background: C.azulClaro, color: C.azul, borderRadius: 20, padding: 22, textAlign: 'center', fontSize: 26, fontWeight: 700 }}>
          📄 Descargar orden en PDF
        </div>
      </div>
      <div style={{ display: 'none' }}>{fps}</div>
    </Telefono>
  );
};

const Reservas: React.FC<Props> = ({ duracion }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const elegido = frame > 1.6 * fps;
  const hora = frame > 2.8 * fps;
  const ok = useEntra(duracion - 1.6);
  const dias = [['HOY', '15'], ['VIE', '16'], ['SÁB', '17'], ['LUN', '19'], ['MAR', '20']];
  const horas = [['8:00 a. m.', '2 libres'], ['10:30 a. m.', '1 libre'], ['3:00 p. m.', 'Sin cupo']];
  return (
    <Telefono>
      <Cabecera titulo="Reservar cita" sub="Elige el día y la hora" />
      <div style={{ padding: 22, display: 'flex', flexDirection: 'column', gap: 20, position: 'relative', height: '100%' }}>
        <div style={{ fontSize: 24, fontWeight: 700, color: C.tinta }}>¿Qué día?</div>
        <div style={{ ...fila, gap: 10 }}>
          {dias.map(([d, n], i) => {
            const act = elegido ? i === 1 : i === 0;
            return (
              <div key={d} style={{ width: 74, padding: '12px 0', borderRadius: 18, textAlign: 'center', background: act ? C.azul : '#fff', color: act ? '#fff' : C.tinta, boxShadow: act ? '0 10px 24px rgba(52,97,240,0.35)' : sombra }}>
                <div style={{ fontSize: 15, fontWeight: 700, opacity: 0.8 }}>{d}</div>
                <div style={{ fontSize: 30, fontWeight: 700 }}>{n}</div>
              </div>
            );
          })}
        </div>
        <div style={{ fontSize: 24, fontWeight: 700, color: C.tinta }}>¿A qué hora?</div>
        <div style={{ display: 'flex', flexWrap: 'wrap', gap: 12 }}>
          {horas.map(([h, l], i) => {
            const act = hora && i === 1;
            const lleno = i === 2;
            return (
              <div key={h} style={{ width: 190, padding: '14px 0', borderRadius: 18, textAlign: 'center', border: `2.5px solid ${act ? C.azul : C.borde}`, background: act ? C.azulClaro : '#fff', opacity: lleno ? 0.4 : 1 }}>
                <div style={{ fontSize: 24, fontWeight: 700, color: C.tinta }}>{h}</div>
                <div style={{ fontSize: 17, color: lleno ? C.rojo : C.suave }}>{l}</div>
              </div>
            );
          })}
        </div>
        <div style={{ position: 'absolute', left: 22, right: 22, top: 470, opacity: ok, transform: `translateY(${interpolate(ok, [0, 1], [40, 0])}px)` }}>
          <div style={{ background: C.verde, color: '#fff', borderRadius: 22, padding: 24, fontSize: 26, fontWeight: 700, textAlign: 'center', boxShadow: '0 18px 40px rgba(31,157,85,0.4)' }}>
            ✓ Cita reservada · Vie 16, 10:30 a. m.
          </div>
        </div>
      </div>
    </Telefono>
  );
};

const Panel: React.FC<Props> = ({ duracion }) => {
  const frame = useCurrentFrame();
  const { fps } = useVideoConfig();
  const colores = [C.azul, '#dc2626', '#16a34a', '#7c4dff'];
  const color = colores[Math.min(colores.length - 1, Math.floor(interpolate(frame, [2.8 * fps, duracion * fps], [0, colores.length], { extrapolateLeft: 'clamp' })))];
  const filas = [['ABC·452', '995 562 651'], ['TRD·452', '987 654 321'], ['MTX·908', '912 445 870']];
  return (
    <div style={{ position: 'relative', width: 980, height: 820 }}>
      <Aparece retraso={0}>
        <div style={{ width: 900, background: '#1e293b', borderRadius: '26px 26px 0 0', padding: 16, boxShadow: '0 40px 100px rgba(0,0,0,0.5)' }}>
          <div style={{ display: 'flex', background: '#f4f6fa', borderRadius: 12, height: 500, overflow: 'hidden' }}>
            <div style={{ width: 190, background: '#fff', padding: 18, display: 'flex', flexDirection: 'column', gap: 14 }}>
              <div style={{ fontSize: 22, fontWeight: 800, color: C.tinta }}>DataValle</div>
              {['Inicio', 'Ventas', 'Mantenim.', 'App móvil', 'Reservas'].map((m) => (
                <div key={m} style={{ fontSize: 16, padding: '8px 12px', borderRadius: 10, color: m === 'App móvil' ? '#fff' : C.suave, background: m === 'App móvil' ? C.azul : 'transparent', fontWeight: 600 }}>{m}</div>
              ))}
            </div>
            <div style={{ flex: 1, padding: 24, display: 'flex', flexDirection: 'column', gap: 14 }}>
              <div style={{ fontSize: 24, fontWeight: 700, color: C.tinta }}>Clientes de la app</div>
              {filas.map(([p, c], i) => {
                const aprobado = frame > (0.9 + i * 0.5) * fps;
                return (
                  <div key={p} style={{ ...fila, background: '#fff', borderRadius: 14, padding: '14px 18px', justifyContent: 'space-between', boxShadow: '0 4px 14px rgba(15,23,42,0.06)' }}>
                    <Placa valor={p} tam={18} />
                    <span style={{ fontSize: 18, color: C.suave }}>{c}</span>
                    <span style={{ fontSize: 17, fontWeight: 700, padding: '8px 16px', borderRadius: 10, color: aprobado ? C.verde : '#fff', background: aprobado ? '#dcfce7' : C.azul }}>{aprobado ? '✓ Aprobada' : 'Aprobar'}</span>
                  </div>
                );
              })}
            </div>
          </div>
        </div>
        <div style={{ width: 980, marginLeft: -40, height: 30, background: 'linear-gradient(#cbd5e1,#94a3b8)', borderRadius: '0 0 30px 30px' }} />
      </Aparece>
      <Aparece retraso={2.4} style={{ position: 'absolute', right: -20, top: 250 }}>
        <Telefono escala={0.55} style={{ transformOrigin: 'top right' }}>
          <Cabecera titulo="Hola, Carlos" sub="Tu taller, tus colores" color={color} />
          <div style={{ padding: 24 }}>
            <Tarjeta style={{ borderRadius: 24 }}>
              <Placa valor="ABC·452" />
            </Tarjeta>
          </div>
        </Telefono>
      </Aparece>
    </div>
  );
};

const Beneficios: React.FC<Props> = () => {
  const items = [
    { i: '📵', t: 'Menos llamadas', d: 'El cliente ve el avance solo' },
    { i: '🤝', t: 'Más confianza', d: 'Fotos, repuestos y orden en PDF' },
    { i: '🔁', t: 'Clientes que vuelven', d: 'Recordatorios y citas en segundos' },
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

const Cierre: React.FC<Props> = () => {
  const frame = useCurrentFrame();
  const e = useEntra(0);
  const redes = [
    { i: '♪', t: 'TikTok', v: 'TikTok @kaeltech.pe' },
    { i: '🌐', t: 'Web', v: 'saas.kael.pe' },
  ];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 34 }}>
      <div style={{ position: 'relative', width: 400, height: 400, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
        <div style={{ position: 'absolute', inset: -20, borderRadius: '50%', background: 'radial-gradient(circle, rgba(124,77,255,0.55), transparent 65%)', transform: `scale(${1 + Math.sin(frame / 10) * 0.05})` }} />
        <Logo tam={340} style={{ transform: `scale(${e})` }} />
      </div>
      <Aparece retraso={0.8}>
        <div style={{ fontSize: 50, fontWeight: 700, color: '#fff', textAlign: 'center', lineHeight: 1.2 }}>
          ¿Listo para llevar tu<br />taller a lo <span style={{ color: '#8ea6ff' }}>digital</span>?
        </div>
      </Aparece>
      <Aparece retraso={3.6}>
        <div style={{ ...fila, gap: 16, background: C.azul, color: '#fff', borderRadius: 22, padding: '20px 44px', fontSize: 34, fontWeight: 700, boxShadow: '0 18px 50px rgba(52,97,240,0.5)' }}>Síguenos en redes →</div>
      </Aparece>
      <div style={{ ...fila, gap: 18 }}>
        {redes.map((r, k) => (
          <Aparece key={r.t} retraso={4.4 + k * 0.4} desde={20}>
            <div style={{ ...fila, gap: 12, background: 'rgba(255,255,255,0.1)', border: '2px solid rgba(255,255,255,0.22)', borderRadius: 999, padding: '14px 26px', color: '#fff', fontSize: 28, fontWeight: 600 }}>
              <span>{r.i}</span>
              {r.v}
            </div>
          </Aparece>
        ))}
      </div>
    </div>
  );
};

export const VISUALES: Record<string, React.FC<Props>> = {
  gancho: Gancho,
  llamadas: Llamadas,
  marca: Marca,
  qr: Qr,
  garaje: Garaje,
  avance: Avance,
  detalle: Detalle,
  reservas: Reservas,
  panel: Panel,
  beneficios: Beneficios,
  cierre: Cierre,
};
