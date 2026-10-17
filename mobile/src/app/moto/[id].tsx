import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router, useLocalSearchParams } from 'expo-router';
import { Alert, View } from 'react-native';

import { EstadoChip } from '@/components/estado';
import { Boton, Cargando, Chip, ErrorCarga, Icono, Pantalla, Placa, Tarjeta, Texto, Vacio } from '@/components/ui';
import { api } from '@/lib/api';
import { fecha } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { Moto, OrdenResumen } from '@/lib/types';

export default function DetalleMoto() {
  const t = useTema();
  const { id } = useLocalSearchParams<{ id: string }>();
  const cache = useQueryClient();

  const moto = useQuery({ queryKey: ['moto', id], queryFn: () => api.get<Moto>(`motos/${id}`) });
  const ordenes = useQuery({
    queryKey: ['ordenes-moto', id],
    queryFn: () => api.get<OrdenResumen[]>('ordenes', { moto: id, limite: 50 }),
  });

  const quitar = useMutation({
    mutationFn: () => api.delete(`motos/${id}/vinculo`),
    onSuccess: async () => {
      await cache.invalidateQueries({ queryKey: ['garaje'] });
      await cache.invalidateQueries({ queryKey: ['ordenes'] });
      router.back();
    },
    onError: () => Alert.alert('No se pudo quitar', 'Inténtalo de nuevo en un momento.'),
  });

  if (moto.isLoading) return <Pantalla><Cargando /></Pantalla>;
  if (moto.isError || !moto.data) return <Pantalla><ErrorCarga error={moto.error} reintentar={() => void moto.refetch()} /></Pantalla>;

  const m = moto.data;

  return (
    <Pantalla>
      <Tarjeta style={{ gap: 12 }}>
        <Placa valor={m.placa} tam="lg" />
        <Texto tipo="titulo">{m.descripcion ?? 'Mi moto'}</Texto>
        {m.ultimo_servicio ? (
          <Texto tipo="pequeno">Último servicio: {m.ultimo_servicio.tipo} · {fecha(m.ultimo_servicio.fecha)}{m.ultimo_servicio.km ? ` · ${m.ultimo_servicio.km} km` : ''}</Texto>
        ) : null}
        <View style={{ flexDirection: 'row', gap: 8, flexWrap: 'wrap' }}>
          {m.proximo_servicio ? <Chip texto={`Próx. servicio: ${m.proximo_servicio}`} tono="info" icono="notifications-outline" /> : null}
          {m.proximo_cambio_aceite ? <Chip texto={`Aceite: ${m.proximo_cambio_aceite}`} tono="info" icono="water-outline" /> : null}
        </View>
      </Tarjeta>

      <Boton titulo="Reservar una cita" icono="calendar" onPress={() => router.push({ pathname: '/reservar', params: { moto: String(m.id) } })} />

      <Texto tipo="etiqueta" style={{ marginTop: 6 }}>Historial de servicios</Texto>
      {ordenes.isLoading ? <Cargando /> : null}
      {ordenes.data?.map((o) => (
        <Tarjeta key={o.ref} onPress={() => router.push({ pathname: '/orden/[ref]', params: { ref: o.ref } })}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
            <Texto tipo="subtitulo">{o.tipo}</Texto>
            <EstadoChip estado={o.estado} texto={o.estado_texto} />
          </View>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
            <Texto tipo="pequeno">{o.numero} · {fecha(o.fecha_ingreso)}</Texto>
            <Icono nombre="chevron-forward" tam={18} color={t.suave} />
          </View>
        </Tarjeta>
      ))}
      {ordenes.isSuccess && ordenes.data.length === 0 ? <Vacio icono="construct-outline" titulo="Sin servicios todavía" detalle="Aquí aparecerá cada servicio que el taller le haga a esta moto." /> : null}

      <View style={{ marginTop: 10 }}>
        <Boton
          titulo="Quitar esta moto de mi garaje"
          variante="secundario"
          cargando={quitar.isPending}
          onPress={() =>
            Alert.alert('¿Quitar esta moto?', 'Dejarás de ver sus servicios en la app. El taller no pierde ningún dato.', [
              { text: 'Cancelar', style: 'cancel' },
              { text: 'Quitar', style: 'destructive', onPress: () => quitar.mutate() },
            ])
          }
        />
      </View>
    </Pantalla>
  );
}
