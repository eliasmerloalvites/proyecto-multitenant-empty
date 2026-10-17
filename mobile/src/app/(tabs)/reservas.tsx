import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { Alert, View } from 'react-native';

import { ReservaChip } from '@/components/estado';
import { Boton, Cargando, ErrorCarga, Heroe, Icono, Pantalla, Placa, Tarjeta, Texto, Vacio } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useRefrescarAlEnfocar } from '@/lib/enfoque';
import { fecha } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { Reserva } from '@/lib/types';

const MESES = ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];

export default function Reservas() {
  const t = useTema();
  const cache = useQueryClient();
  const [verTodas, setVerTodas] = useState(false);
  const q = useQuery({
    queryKey: ['reservas', verTodas],
    queryFn: () => api.get<Reserva[]>('reservas', { estado: verTodas ? 'todas' : 'proximas' }),
  });

  useRefrescarAlEnfocar(q.refetch);

  const cancelar = useMutation({
    mutationFn: (id: number) => api.delete(`reservas/${id}`),
    onSuccess: () => cache.invalidateQueries({ queryKey: ['reservas'] }),
    onError: (e) => Alert.alert('No se pudo cancelar', e instanceof ApiError ? e.message : 'Inténtalo de nuevo.'),
  });

  return (
    <Pantalla
      sinPadding
      refrescando={q.isRefetching}
      alRefrescar={() => void q.refetch()}
      cabecera={
        <Heroe>
          <Texto tipo="display" color="#fff">Citas</Texto>
          <Texto tipo="cuerpo" color="rgba(255,255,255,0.85)">Reserva y el taller te espera</Texto>
        </Heroe>
      }>
      <View style={{ padding: 16, gap: 12 }}>
        <Boton titulo="Reservar una cita" icono="add-circle" onPress={() => router.push('/reservar')} />

        {q.isLoading ? <Cargando /> : q.isError ? <ErrorCarga error={q.error} reintentar={() => void q.refetch()} /> : null}

        {q.data?.map((r) => {
          const d = new Date(r.fecha + 'T12:00:00');
          const inactiva = r.estado === 'CANCELADA' || r.estado === 'RECHAZADA';
          return (
            <Tarjeta key={r.id} style={{ opacity: inactiva ? 0.65 : 1 }}>
              <View style={{ flexDirection: 'row', gap: 14 }}>
                <View style={{ width: 58, borderRadius: 14, backgroundColor: inactiva ? t.fondo : t.primarioClaro, alignItems: 'center', justifyContent: 'center', paddingVertical: 8 }}>
                  <Texto tipo="etiqueta" color={inactiva ? t.suave : t.primario}>{MESES[d.getMonth()]}</Texto>
                  <Texto tipo="titulo" color={inactiva ? t.suave : t.primarioOscuro}>{d.getDate()}</Texto>
                </View>
                <View style={{ flex: 1, gap: 5 }}>
                  <ReservaChip estado={r.estado} />
                  <Texto tipo="subtitulo">{r.hora ?? r.turno ?? fecha(r.fecha)}</Texto>
                  <Placa valor={r.placa} tam="sm" />
                </View>
              </View>
              {r.sede ? (
                <View style={{ flexDirection: 'row', gap: 6, alignItems: 'center' }}>
                  <Icono nombre="location-outline" tam={15} color={t.suave} />
                  <Texto tipo="pequeno">{r.sede}</Texto>
                </View>
              ) : null}
              {r.detalle ? <Texto tipo="pequeno">“{r.detalle}”</Texto> : null}
              {r.cancelable ? (
                <Boton
                  titulo="Cancelar cita"
                  variante="secundario"
                  cargando={cancelar.isPending && cancelar.variables === r.id}
                  onPress={() =>
                    Alert.alert('¿Cancelar la cita?', `${fecha(r.fecha)}${r.hora ? ` a las ${r.hora}` : ''}`, [
                      { text: 'No', style: 'cancel' },
                      { text: 'Sí, cancelar', style: 'destructive', onPress: () => cancelar.mutate(r.id) },
                    ])
                  }
                />
              ) : null}
            </Tarjeta>
          );
        })}

        {q.isSuccess && q.data.length === 0 ? (
          <Vacio icono="calendar-outline" titulo={verTodas ? 'Aún no tienes citas' : 'No tienes citas próximas'} detalle="Reserva el día y la hora que prefieras; te mostramos solo los horarios con cupo." />
        ) : null}

        <Boton titulo={verTodas ? 'Ver solo próximas' : 'Ver historial de citas'} variante="secundario" onPress={() => setVerTodas((v) => !v)} />
      </View>
    </Pantalla>
  );
}
