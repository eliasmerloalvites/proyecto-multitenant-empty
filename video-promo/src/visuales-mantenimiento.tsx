import { interpolate, useCurrentFrame } from 'remotion';

import { C } from './estilo';
import { Logo } from './Logo';
import { Aparece, fila, Tarjeta, useEntra } from './visuales';
import { Captura, Cierre, Insignia, Orden } from './visuales-web';

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
  const signo = useEntra(1.4);
  return (
    <div style={{ position: 'relative', width: 560, height: 560, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
      {[0, 1, 2].map((i) => {
        const p = (frame / 40 + i / 3) % 1;
        return <div key={i} style={{ position: 'absolute', width: 300 + p * 360, height: 300 + p * 360, borderRadius: '50%', border: `4px solid rgba(124,140,255,${0.5 * (1 - p)})` }} />;
      })}
      <div style={{ width: 340, height: 340, borderRadius: '50%', background: 'linear-gradient(135deg, #3461f0, #7c4dff)', display: 'flex', alignItems: 'center', justifyContent: 'center', boxShadow: '0 30px 90px rgba(52,97,240,0.55)' }}>
        <span style={{ fontSize: 180 }}>🏍️</span>
      </div>
      <div style={{ position: 'absolute', right: 40, top: 40, width: 150, height: 150, borderRadius: 75, background: C.ambar, color: '#fff', fontSize: 100, fontWeight: 800, display: 'flex', alignItems: 'center', justifyContent: 'center', transform: `scale(${signo}) rotate(${Math.sin(frame / 6) * 8}deg)`, boxShadow: '0 18px 50px rgba(245,158,11,0.55)' }}>?</div>
    </div>
  );
};

const Caos: React.FC<Props> = () => {
  const items = [
    { i: '😠', t: '“Ese rayón no estaba”', d: 'Y no tienes cómo demostrarlo' },
    { i: '📝', t: 'Trabajos sin registrar', d: '¿Qué se le cambió la vez pasada?' },
    { i: '🤷', t: 'Nada que mostrar', d: 'Ni fotos, ni orden, ni checklist' },
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
  const chips = ['Preventivo', 'General', 'Carburada e inyectada', 'Actividades'];
  return (
    <div style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: 46 }}>
      <div style={{ position: 'relative' }}>
        <div style={{ position: 'absolute', inset: -60, borderRadius: '50%', background: 'conic-gradient(from 0deg, #3461f0, #7c4dff, #3461f0)', filter: 'blur(60px)', opacity: 0.5, transform: `rotate(${frame * 2}deg)` }} />
        <Logo tam={440} style={{ position: 'relative', transform: `scale(${e}) rotate(${interpolate(e, [0, 1], [-12, 0])}deg)` }} />
      </div>
      <div style={{ display: 'flex', flexWrap: 'wrap', justifyContent: 'center', gap: 14, width: 900 }}>
        {chips.map((c, i) => (
          <Aparece key={c} retraso={2 + i * 0.3} desde={20}>
            <div style={{ background: 'rgba(255,255,255,0.1)', border: '2px solid rgba(255,255,255,0.22)', borderRadius: 999, padding: '12px 26px', color: '#fff', fontSize: 30, fontWeight: 600 }}>🔧 {c}</div>
          </Aparece>
        ))}
      </div>
    </div>
  );
};

const Lista: React.FC<Props> = ({ duracion }) => (
  <Captura img="mto-lista" titulo="Mantenimientos · Kael Tech" duracion={duracion} desde={{ x: 0.5, y: 0.35, zoom: 1.15 }} hasta={{ x: 0.62, y: 0.6, zoom: 1.6 }}>
    <Insignia retraso={2.4} style={{ right: -20, bottom: 70, ...insignia(C.verde) }}>✓ Pendientes y aprobados</Insignia>
  </Captura>
);

const Recepcion: React.FC<Props> = ({ duracion }) => (
  <Captura img="mto-recepcion" titulo="Estado de recepción · Kael Tech" duracion={duracion} desde={{ x: 0.5, y: 0.08, zoom: 1 }} hasta={{ x: 0.5, y: 0.55, zoom: 1.15 }}>
    <Insignia retraso={1.6} style={{ right: -20, top: 110, ...insignia(C.morado) }}>92 puntos revisados</Insignia>
  </Captura>
);

const Servicio: React.FC<Props> = ({ duracion }) => (
  <Captura img="mto-servicio" titulo="Checklist del servicio · Kael Tech" duracion={duracion} desde={{ x: 0.5, y: 0.05, zoom: 1.1 }} hasta={{ x: 0.5, y: 0.3, zoom: 1.2 }}>
    <Insignia retraso={1.8} style={{ left: -20, bottom: 70, ...insignia(C.azul) }}>✓ Tareas con sus medidas</Insignia>
  </Captura>
);

const Fotos: React.FC<Props> = ({ duracion }) => (
  <Captura img="mto-fotos" titulo="Galería de evidencias · Kael Tech" duracion={duracion} desde={{ x: 0.5, y: 0.4, zoom: 1.05 }} hasta={{ x: 0.22, y: 0.82, zoom: 1.9 }}>
    <Insignia retraso={2.2} style={{ right: -20, top: 110, ...insignia(C.verde) }}>📸 Antes y después</Insignia>
  </Captura>
);

const Repuestos: React.FC<Props> = ({ duracion }) => (
  <Captura img="mto-repuestos" titulo="Repuestos · Kael Tech" duracion={duracion} desde={{ x: 0.28, y: 0.55, zoom: 1.6 }} hasta={{ x: 0.3, y: 0.78, zoom: 1.8 }}>
    <Insignia retraso={2.6} style={{ right: -20, bottom: -40, ...insignia('#0ea5b7') }}>🔗 Sincronizado con la bahía</Insignia>
  </Captura>
);

const Verificacion: React.FC<Props> = ({ duracion }) => (
  <Captura img="mto-verificacion" titulo="Verificación final · Kael Tech" duracion={duracion} desde={{ x: 0.28, y: 0.45, zoom: 1.45 }} hasta={{ x: 0.3, y: 0.72, zoom: 1.6 }}>
    <Insignia retraso={2.4} style={{ right: -20, bottom: -40, ...insignia(C.verde) }}>✓ Conforme para entrega</Insignia>
  </Captura>
);

const Medida: React.FC<Props> = ({ duracion }) => (
  <Captura img="mto-config-recepcion" titulo="Configuración · Kael Tech" duracion={duracion} desde={{ x: 0.5, y: 0.3, zoom: 1.15 }} hasta={{ x: 0.6, y: 0.5, zoom: 1.6 }}>
    <Insignia retraso={2.4} style={{ left: -20, bottom: 70, ...insignia(C.morado) }}>⚙️ Tu checklist, tus reglas</Insignia>
  </Captura>
);

const Beneficios: React.FC<Props> = () => {
  const items = [
    { i: '🤝', t: 'Más confianza', d: 'El cliente ve todo lo que se hizo' },
    { i: '🛡️', t: 'Menos reclamos', d: 'Recepción y fotos como respaldo' },
    { i: '🔁', t: 'Clientes que vuelven', d: 'Próximo servicio ya agendado' },
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

export const VISUALES_MANTENIMIENTO: Record<string, React.FC<Props>> = {
  gancho: Gancho,
  caos: Caos,
  marca: Marca,
  lista: Lista,
  recepcion: Recepcion,
  servicio: Servicio,
  fotos: Fotos,
  repuestos: Repuestos,
  verificacion: Verificacion,
  orden: Orden,
  medida: Medida,
  beneficios: Beneficios,
  cierre: Cierre,
};
