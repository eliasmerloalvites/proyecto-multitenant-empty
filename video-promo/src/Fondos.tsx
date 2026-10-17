import { AbsoluteFill, useCurrentFrame } from 'remotion';

import { C } from './estilo';

const CODIGO = [
  'const reserva = await api.post("reservas", { moto, turno, fecha });',
  'function estadoPublico(orden) { return orden.entregada ? "ENTREGADA" : "EN_TALLER"; }',
  'garaje.motos.map((m) => <Tarjeta placa={m.placa} servicios={m.total} />)',
  'Route::get("ordenes/{ref}", [OrdenController::class, "show"]);',
  'if (vinculo->alcance === COMPLETO) return $registros;',
  'const proximo = moto.proximo_servicio ?? "Sin programar";',
  'await sesion.guardarToken(token); // almacén seguro',
  '$taller = Tenant::where("data->codigo_app", $codigo)->first();',
  'turnos.filter((t) => t.libres > 0).map(renderHorario)',
  'notificar(cliente, "Tu moto está lista para recoger");',
  'SELECT placa, km, proximo_servicio FROM mantenimiento',
  'export default function MiGaraje() { const q = useQuery(...) }',
];

/** Claro con puntos, u oscuro con código desenfocado, como en el video de Kael Tech. */
export const Fondo: React.FC<{ oscuro: boolean }> = ({ oscuro }) => {
  const frame = useCurrentFrame();

  if (!oscuro) {
    return (
      <AbsoluteFill
        style={{
          backgroundColor: C.claro,
          backgroundImage: 'radial-gradient(rgba(100,116,139,0.22) 2px, transparent 2.5px)',
          backgroundSize: '44px 44px',
        }}
      />
    );
  }

  return (
    <AbsoluteFill style={{ backgroundColor: C.oscuro, overflow: 'hidden' }}>
      <div style={{ position: 'absolute', inset: -40, transform: `translateY(${-frame * 0.8}px)`, filter: 'blur(2.2px)', opacity: 0.32 }}>
        {Array.from({ length: 60 }, (_, i) => (
          <div key={i} style={{ fontFamily: 'Consolas, monospace', fontSize: 30, lineHeight: '46px', color: i % 5 === 0 ? '#7c8cff' : '#8fa3c7', whiteSpace: 'nowrap', marginLeft: (i * 37) % 160 }}>
            {CODIGO[i % CODIGO.length]}
          </div>
        ))}
      </div>
      <AbsoluteFill style={{ background: 'radial-gradient(ellipse at 50% 40%, rgba(52,97,240,0.28), transparent 60%)' }} />
    </AbsoluteFill>
  );
};
