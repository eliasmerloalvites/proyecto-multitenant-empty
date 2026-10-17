import { Ionicons } from '@expo/vector-icons';
import { View } from 'react-native';

import { Chip, Texto } from '@/components/ui';
import { fecha } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { EstadoOrden, OrdenResumen, Reserva } from '@/lib/types';

const ORDEN: Record<EstadoOrden, { tono: 'ok' | 'aviso' | 'neutro' | 'info'; icono: React.ComponentProps<typeof Ionicons>['name'] }> = {
  EN_TALLER: { tono: 'aviso', icono: 'construct' },
  TRABAJO_TERMINADO: { tono: 'ok', icono: 'checkmark-circle' },
  ENTREGADA: { tono: 'neutro', icono: 'flag' },
  SIN_CERRAR: { tono: 'neutro', icono: 'time' },
};

export function EstadoChip({ estado, texto }: { estado: EstadoOrden; texto: string }) {
  const e = ORDEN[estado] ?? ORDEN.SIN_CERRAR;
  return <Chip texto={texto} tono={e.tono} icono={e.icono} />;
}

const RESERVA: Record<Reserva['estado'], { texto: string; tono: 'ok' | 'aviso' | 'error' | 'neutro' | 'info'; icono: React.ComponentProps<typeof Ionicons>['name'] }> = {
  PENDIENTE: { texto: 'Por confirmar', tono: 'aviso', icono: 'hourglass' },
  CONFIRMADA: { texto: 'Confirmada', tono: 'ok', icono: 'checkmark-circle' },
  EN_ATENCION: { texto: 'En atención', tono: 'info', icono: 'construct' },
  CANCELADA: { texto: 'Cancelada', tono: 'neutro', icono: 'close-circle' },
  RECHAZADA: { texto: 'No aceptada', tono: 'error', icono: 'alert-circle' },
};

export function ReservaChip({ estado }: { estado: Reserva['estado'] }) {
  const r = RESERVA[estado];
  return <Chip texto={r.texto} tono={r.tono} icono={r.icono} />;
}

/**
 * Línea de tiempo de la orden: Ingreso → Trabajo terminado → Entregada.
 * "Sin cerrar" (registro viejo que el taller no cerró) solo marca el ingreso:
 * no se promete un avance que no se conoce.
 */
export function PasosOrden({ orden }: { orden: Pick<OrdenResumen, 'estado' | 'fecha_ingreso' | 'fecha_termino' | 'fecha_entrega'> }) {
  const t = useTema();
  const hecho = { EN_TALLER: 1, TRABAJO_TERMINADO: 2, ENTREGADA: 3, SIN_CERRAR: 1 }[orden.estado] ?? 1;
  const pasos = [
    { titulo: 'Ingresó al taller', fecha: orden.fecha_ingreso, icono: 'enter' as const },
    { titulo: 'Trabajo terminado', fecha: orden.fecha_termino, icono: 'construct' as const },
    { titulo: 'Entregada', fecha: orden.fecha_entrega, icono: 'flag' as const },
  ];

  return (
    <View>
      {pasos.map((p, i) => {
        const cumplido = i < hecho;
        const actual = i === hecho - 1 && orden.estado !== 'SIN_CERRAR' && orden.estado !== 'ENTREGADA';
        const color = cumplido ? t.ok : t.borde;
        return (
          <View key={p.titulo} style={{ flexDirection: 'row', gap: 14 }}>
            <View style={{ alignItems: 'center', width: 30 }}>
              <View
                style={{
                  width: 30,
                  height: 30,
                  borderRadius: 15,
                  backgroundColor: cumplido ? t.ok : '#fff',
                  borderWidth: 2,
                  borderColor: color,
                  alignItems: 'center',
                  justifyContent: 'center',
                }}>
                <Ionicons name={cumplido ? 'checkmark' : p.icono} size={cumplido ? 17 : 14} color={cumplido ? '#fff' : '#94a3b8'} />
              </View>
              {i < pasos.length - 1 ? <View style={{ width: 2, flex: 1, minHeight: 22, backgroundColor: i < hecho - 1 ? t.ok : t.borde }} /> : null}
            </View>
            <View style={{ flex: 1, paddingBottom: i < pasos.length - 1 ? 14 : 0, paddingTop: 4 }}>
              <Texto tipo="subtitulo" color={cumplido ? t.texto : '#94a3b8'}>{p.titulo}</Texto>
              <Texto tipo="pequeno">
                {p.fecha ? fecha(p.fecha) : actual ? 'El taller está trabajando en tu moto' : i === 1 && orden.estado === 'SIN_CERRAR' ? 'Sin información del avance' : 'Pendiente'}
              </Texto>
            </View>
          </View>
        );
      })}
    </View>
  );
}
