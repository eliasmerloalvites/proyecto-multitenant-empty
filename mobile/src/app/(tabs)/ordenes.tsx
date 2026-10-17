import { useInfiniteQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { Pressable, View } from 'react-native';

import { EstadoChip } from '@/components/estado';
import { Boton, Cargando, ErrorCarga, Heroe, Icono, Pantalla, Placa, Tarjeta, Texto, Vacio } from '@/components/ui';
import { api } from '@/lib/api';
import { useRefrescarAlEnfocar } from '@/lib/enfoque';
import { fecha } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { OrdenResumen } from '@/lib/types';

export default function Ordenes() {
  const t = useTema();
  const [soloActivas, setSoloActivas] = useState(false);

  const q = useInfiniteQuery({
    queryKey: ['ordenes', soloActivas],
    initialPageParam: undefined as string | undefined,
    queryFn: async ({ pageParam }) => {
      const r = await api.getConMeta<OrdenResumen[]>('ordenes', { estado: soloActivas ? 'activas' : 'todas', cursor: pageParam, limite: 20 });
      return { filas: r.data, siguiente: (r.meta.siguiente_cursor as string | null) ?? undefined };
    },
    getNextPageParam: (p) => p.siguiente,
  });

  useRefrescarAlEnfocar(q.refetch);
  const filas = q.data?.pages.flatMap((p) => p.filas) ?? [];

  return (
    <Pantalla
      sinPadding
      refrescando={q.isRefetching && !q.isFetchingNextPage}
      alRefrescar={() => void q.refetch()}
      cabecera={
        <Heroe
          bajo={
            <View style={{ flexDirection: 'row', gap: 8, marginTop: 14 }}>
              {[
                { v: false, n: 'Todos' },
                { v: true, n: 'En el taller' },
              ].map((o) => {
                const activo = soloActivas === o.v;
                return (
                  <Pressable
                    key={o.n}
                    onPress={() => setSoloActivas(o.v)}
                    accessibilityRole="button"
                    accessibilityState={{ selected: activo }}
                    style={{ paddingHorizontal: 16, paddingVertical: 9, borderRadius: 999, backgroundColor: activo ? '#fff' : 'rgba(255,255,255,0.18)' }}>
                    <Texto color={activo ? t.primarioOscuro : '#fff'} style={{ fontWeight: '700' }}>{o.n}</Texto>
                  </Pressable>
                );
              })}
            </View>
          }>
          <Texto tipo="display" color="#fff">Servicios</Texto>
          <Texto tipo="cuerpo" color="rgba(255,255,255,0.85)">Sigue el avance de tus motos</Texto>
        </Heroe>
      }>
      <View style={{ padding: 16, gap: 12 }}>
        {q.isLoading ? <Cargando /> : q.isError ? <ErrorCarga error={q.error} reintentar={() => void q.refetch()} /> : null}

        {filas.map((o) => (
          <Tarjeta key={o.ref} onPress={() => router.push({ pathname: '/orden/[ref]', params: { ref: o.ref } })}>
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
              <Placa valor={o.placa} />
              <EstadoChip estado={o.estado} texto={o.estado_texto} />
            </View>
            <Texto tipo="subtitulo" style={{ marginTop: 4 }}>{o.tipo}</Texto>
            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
              <Texto tipo="pequeno">{o.numero} · {fecha(o.fecha_ingreso)}</Texto>
              <Icono nombre="chevron-forward" tam={18} color={t.suave} />
            </View>
          </Tarjeta>
        ))}

        {q.isSuccess && filas.length === 0 ? (
          <Vacio
            icono="construct-outline"
            titulo={soloActivas ? 'Ninguna moto en el taller' : 'Aún no tienes servicios'}
            detalle="Cuando dejes tu moto en el taller, aquí verás cada paso del trabajo."
          />
        ) : null}

        {q.hasNextPage ? <Boton titulo="Ver más" variante="suave" onPress={() => void q.fetchNextPage()} cargando={q.isFetchingNextPage} /> : null}
      </View>
    </Pantalla>
  );
}
