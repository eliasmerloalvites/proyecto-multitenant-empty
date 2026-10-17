import { useQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { Pressable, View } from 'react-native';

import { PedidoChip } from '@/components/tienda';
import { Boton, Cargando, ErrorCarga, Icono, Pantalla, Tarjeta, Texto, Vacio } from '@/components/ui';
import { api } from '@/lib/api';
import { useRefrescarAlEnfocar } from '@/lib/enfoque';
import { fecha, soles } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { PedidoResumen } from '@/lib/types';

export default function Pedidos() {
  const t = useTema();
  const [soloActivos, setSoloActivos] = useState(true);
  const q = useQuery({
    queryKey: ['pedidos', soloActivos],
    queryFn: () => api.get<PedidoResumen[]>('pedidos', { estado: soloActivos ? 'activos' : 'todos' }),
  });
  useRefrescarAlEnfocar(q.refetch);

  return (
    <Pantalla refrescando={q.isRefetching} alRefrescar={() => void q.refetch()}>
      <View style={{ flexDirection: 'row', gap: 8 }}>
        {[
          { v: true, n: 'En curso' },
          { v: false, n: 'Todos' },
        ].map((o) => {
          const activo = soloActivos === o.v;
          return (
            <Pressable
              key={o.n}
              onPress={() => setSoloActivos(o.v)}
              accessibilityRole="button"
              accessibilityState={{ selected: activo }}
              style={{ paddingHorizontal: 16, paddingVertical: 8, borderRadius: 999, backgroundColor: activo ? t.primario : '#fff', borderWidth: 1, borderColor: activo ? t.primario : t.borde }}>
              <Texto color={activo ? t.sobrePrimario : t.texto} style={{ fontWeight: '600' }}>{o.n}</Texto>
            </Pressable>
          );
        })}
      </View>

      {q.isLoading ? <Cargando /> : q.isError ? <ErrorCarga error={q.error} reintentar={() => void q.refetch()} /> : null}

      {q.data?.map((p) => (
        <Tarjeta key={p.id} onPress={() => router.push({ pathname: '/pedido/[id]', params: { id: String(p.id) } })}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
            <Texto tipo="subtitulo">{p.numero}</Texto>
            <PedidoChip estado={p.estado} />
          </View>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
            <Texto tipo="pequeno">
              {p.productos} producto{p.productos === 1 ? '' : 's'} · {fecha(p.fecha)}{p.sede ? ` · ${p.sede}` : ''}
            </Texto>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 4 }}>
              <Texto style={{ fontWeight: '700' }}>{soles(p.total)}</Texto>
              <Icono nombre="chevron-forward" tam={16} color={t.suave} />
            </View>
          </View>
        </Tarjeta>
      ))}

      {q.isSuccess && q.data.length === 0 ? (
        <Vacio
          icono="receipt-outline"
          titulo={soloActivos ? 'No tienes pedidos en curso' : 'Aún no hiciste pedidos'}
          detalle="Pide repuestos y accesorios desde la tienda y recógelos en el taller."
          accion={<Boton titulo="Ir a la tienda" variante="suave" onPress={() => router.replace('/(tabs)/tienda')} />}
        />
      ) : null}
    </Pantalla>
  );
}
