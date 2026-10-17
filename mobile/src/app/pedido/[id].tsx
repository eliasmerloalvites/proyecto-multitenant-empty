import { Ionicons } from '@expo/vector-icons';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useLocalSearchParams } from 'expo-router';
import { Alert, View } from 'react-native';

import { FotoProducto, PedidoChip } from '@/components/tienda';
import { Boton, Cargando, ErrorCarga, Icono, Pantalla, Tarjeta, Texto } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useRefrescarAlEnfocar } from '@/lib/enfoque';
import { fecha, soles } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { PedidoDetalle } from '@/lib/types';

export default function DetallePedido() {
  const t = useTema();
  const cache = useQueryClient();
  const { id, nuevo } = useLocalSearchParams<{ id: string; nuevo?: string }>();
  const q = useQuery({ queryKey: ['pedido', id], queryFn: () => api.get<PedidoDetalle>(`pedidos/${id}`) });
  useRefrescarAlEnfocar(q.refetch);

  const cancelar = useMutation({
    mutationFn: () => api.delete<PedidoDetalle>(`pedidos/${id}`),
    onSuccess: (p) => {
      cache.setQueryData(['pedido', id], p);
      void cache.invalidateQueries({ queryKey: ['pedidos'] });
    },
    onError: (e) => Alert.alert('No se pudo cancelar', e instanceof ApiError ? e.message : 'Inténtalo de nuevo.'),
  });

  if (q.isLoading) return <Pantalla><Cargando /></Pantalla>;
  if (q.isError || !q.data) return <Pantalla><ErrorCarga error={q.error} reintentar={() => void q.refetch()} /></Pantalla>;

  const p = q.data;
  const cancelado = p.estado === 'CANCELADO';
  const pasos = [
    { titulo: 'Pedido enviado', fecha: p.pasos.pedido, icono: 'paper-plane' as const },
    { titulo: 'Confirmado por el taller', fecha: p.pasos.confirmado, icono: 'checkmark-circle' as const },
    { titulo: 'Listo para recoger', fecha: p.pasos.listo, icono: 'bag-check' as const },
    { titulo: 'Entregado', fecha: p.pasos.entregado, icono: 'flag' as const },
  ];

  return (
    <Pantalla refrescando={q.isRefetching} alRefrescar={() => void q.refetch()}>
      {nuevo ? (
        <Tarjeta style={{ backgroundColor: t.ok + '14', borderWidth: 1.5, borderColor: t.ok + '55', flexDirection: 'row', gap: 10, alignItems: 'center' }}>
          <Ionicons name="checkmark-circle" size={28} color={t.ok} />
          <View style={{ flex: 1 }}>
            <Texto style={{ fontWeight: '700' }}>¡Pedido enviado!</Texto>
            <Texto tipo="pequeno">El taller lo revisará y aquí verás cuando esté listo para recoger.</Texto>
          </View>
        </Tarjeta>
      ) : null}

      <Tarjeta style={{ gap: 10 }}>
        <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
          <Texto tipo="titulo">{p.numero}</Texto>
          <PedidoChip estado={p.estado} />
        </View>
        <Texto tipo="pequeno">{p.estado_texto}</Texto>
        {p.sede ? (
          <View style={{ flexDirection: 'row', gap: 6, alignItems: 'flex-start' }}>
            <Icono nombre="location-outline" tam={16} color={t.primario} />
            <Texto tipo="pequeno" style={{ flex: 1 }}>Recojo en {p.sede}{p.sede_direccion ? ` · ${p.sede_direccion}` : ''}</Texto>
          </View>
        ) : null}

        {cancelado ? (
          <View style={{ backgroundColor: t.error + '12', borderRadius: 12, padding: 12 }}>
            <Texto color={t.error} style={{ fontWeight: '600' }}>
              Cancelado {p.cancelado_por === 'CLIENTE' ? 'por ti' : 'por el taller'} · {fecha(p.pasos.cancelado)}
            </Texto>
            {p.motivo_cancelacion ? <Texto tipo="pequeno">{p.motivo_cancelacion}</Texto> : null}
          </View>
        ) : (
          <View style={{ marginTop: 4 }}>
            {pasos.map((paso, i) => {
              const hecho = !!paso.fecha;
              return (
                <View key={paso.titulo} style={{ flexDirection: 'row', gap: 12 }}>
                  <View style={{ alignItems: 'center', width: 28 }}>
                    <View style={{ width: 28, height: 28, borderRadius: 14, backgroundColor: hecho ? t.ok : '#fff', borderWidth: 2, borderColor: hecho ? t.ok : t.borde, alignItems: 'center', justifyContent: 'center' }}>
                      <Ionicons name={hecho ? 'checkmark' : paso.icono} size={hecho ? 16 : 13} color={hecho ? '#fff' : '#94a3b8'} />
                    </View>
                    {i < pasos.length - 1 ? <View style={{ width: 2, flex: 1, minHeight: 18, backgroundColor: pasos[i + 1].fecha ? t.ok : t.borde }} /> : null}
                  </View>
                  <View style={{ flex: 1, paddingBottom: i < pasos.length - 1 ? 12 : 0, paddingTop: 3 }}>
                    <Texto style={{ fontWeight: '600' }} color={hecho ? t.texto : '#94a3b8'}>{paso.titulo}</Texto>
                    <Texto tipo="pequeno">{hecho ? fecha(paso.fecha) : 'Pendiente'}</Texto>
                  </View>
                </View>
              );
            })}
          </View>
        )}
      </Tarjeta>

      <Tarjeta style={{ gap: 12 }}>
        <Texto tipo="etiqueta">Productos</Texto>
        {p.items.map((i) => (
          <View key={i.producto_id} style={{ flexDirection: 'row', gap: 12, alignItems: 'center' }}>
            <FotoProducto uri={i.imagen} tam={52} />
            <View style={{ flex: 1 }}>
              <Texto style={{ fontWeight: '600' }} lineas={2}>{i.nombre}</Texto>
              <Texto tipo="pequeno">{i.cantidad} × {soles(i.precio)}</Texto>
            </View>
            <Texto style={{ fontWeight: '700' }}>{soles(i.subtotal)}</Texto>
          </View>
        ))}
        <View style={{ flexDirection: 'row', justifyContent: 'space-between', borderTopWidth: 1, borderColor: t.borde, paddingTop: 10 }}>
          <Texto tipo="subtitulo">Total</Texto>
          <Texto tipo="subtitulo" color={t.primario}>{soles(p.total)}</Texto>
        </View>
        <Texto tipo="pequeno">Pagas al recoger en el taller.</Texto>
      </Tarjeta>

      {p.nota ? (
        <Tarjeta style={{ gap: 4 }}>
          <Texto tipo="etiqueta">Tu nota</Texto>
          <Texto>{p.nota}</Texto>
        </Tarjeta>
      ) : null}

      {p.cancelable ? (
        <Boton
          titulo="Cancelar pedido"
          variante="secundario"
          cargando={cancelar.isPending}
          onPress={() =>
            Alert.alert('¿Cancelar el pedido?', p.numero, [
              { text: 'No', style: 'cancel' },
              { text: 'Sí, cancelar', style: 'destructive', onPress: () => cancelar.mutate() },
            ])
          }
        />
      ) : null}
    </Pantalla>
  );
}
