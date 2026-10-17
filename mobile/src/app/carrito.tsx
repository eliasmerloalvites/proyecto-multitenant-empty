import { Ionicons } from '@expo/vector-icons';
import { useQueries, useQuery, useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useEffect, useState } from 'react';
import { Alert, Pressable, View } from 'react-native';

import { Cantidad, FotoProducto } from '@/components/tienda';
import { Boton, Campo, Cargando, Pantalla, Tarjeta, Texto, Vacio } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useCarrito } from '@/lib/carrito';
import { soles } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { InicioTienda, PedidoDetalle, ProductoDetalle } from '@/lib/types';

export default function Carrito() {
  const t = useTema();
  const cache = useQueryClient();
  const carrito = useCarrito();
  const [nota, setNota] = useState('');
  const [enviando, setEnviando] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const inicio = useQuery({ queryKey: ['tienda-inicio'], queryFn: () => api.get<InicioTienda>('catalogo/categorias') });
  const sede = carrito.sede ?? inicio.data?.sede_por_defecto ?? null;

  // Precio y disponibilidad actuales de cada producto del carrito, en la sede elegida.
  const detalles = useQueries({
    queries: carrito.lineas.map((l) => ({
      queryKey: ['producto', String(l.id), sede],
      queryFn: () => api.get<ProductoDetalle>(`catalogo/productos/${l.id}`, { sede: sede ?? undefined }),
      enabled: sede !== null,
    })),
  });

  // Quita del carrito lo que ya no esta en el catalogo.
  useEffect(() => {
    detalles.forEach((d, i) => {
      if (d.error instanceof ApiError && d.error.status === 404 && carrito.lineas[i]) carrito.quitar(carrito.lineas[i].id);
    });
  }, [detalles, carrito]);

  const filas = carrito.lineas.map((l, i) => ({ linea: l, p: detalles[i]?.data }));
  const cargando = detalles.some((d) => d.isLoading) || inicio.isLoading;
  const total = filas.reduce((s, f) => s + (f.p ? Number(f.p.precio) * f.linea.cantidad : 0), 0);
  const problemas = filas.filter((f) => f.p && f.linea.cantidad > f.p.maximo);

  async function confirmar() {
    if (!sede) return;
    setEnviando(true);
    setError(null);
    try {
      const pedido = await api.post<PedidoDetalle>('pedidos', {
        sede,
        nota: nota.trim() || undefined,
        items: carrito.lineas.map((l) => ({ producto_id: l.id, cantidad: l.cantidad })),
      });
      carrito.vaciar();
      await cache.invalidateQueries({ queryKey: ['pedidos'] });
      await cache.invalidateQueries({ queryKey: ['tienda-productos'] });
      router.replace({ pathname: '/pedido/[id]', params: { id: String(pedido.id), nuevo: '1' } });
    } catch (e) {
      setError(e instanceof ApiError ? e.message : 'No pudimos enviar tu pedido. Inténtalo de nuevo.');
    } finally {
      setEnviando(false);
    }
  }

  if (carrito.lineas.length === 0) {
    return (
      <Pantalla>
        <Vacio icono="cart-outline" titulo="Tu carrito está vacío" detalle="Agrega productos desde la tienda del taller." accion={<Boton titulo="Ir a la tienda" onPress={() => router.replace('/(tabs)/tienda')} />} />
      </Pantalla>
    );
  }

  return (
    <Pantalla
      pie={
        <View style={{ padding: 16, gap: 10, backgroundColor: '#fff', borderTopWidth: 1, borderColor: t.borde }}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
            <Texto tipo="subtitulo">Total</Texto>
            <Texto tipo="titulo" color={t.primario}>{soles(String(total))}</Texto>
          </View>
          <Texto tipo="pequeno" style={{ textAlign: 'center' }}>Pagas al recoger en el taller. El precio final lo confirma el taller.</Texto>
          {error ? <Texto color={t.error}>{error}</Texto> : null}
          <Boton titulo="Confirmar pedido" icono="checkmark-circle" onPress={() => void confirmar()} cargando={enviando} deshabilitado={cargando || problemas.length > 0 || !sede} />
        </View>
      }>
      {inicio.data && inicio.data.sedes.length > 1 ? (
        <Tarjeta style={{ gap: 8 }}>
          <Texto tipo="etiqueta">¿Dónde lo recoges?</Texto>
          {inicio.data.sedes.map((s) => {
            const activo = s.id === sede;
            return (
              <Pressable
                key={s.id}
                onPress={() => carrito.elegirSede(s.id)}
                accessibilityRole="radio"
                accessibilityState={{ checked: activo }}
                style={{ flexDirection: 'row', alignItems: 'center', gap: 10, padding: 12, borderRadius: 12, borderWidth: 1.5, borderColor: activo ? t.primario : t.borde, backgroundColor: activo ? t.primarioClaro : '#fff' }}>
                <Ionicons name={activo ? 'radio-button-on' : 'radio-button-off'} size={20} color={activo ? t.primario : t.suave} />
                <View style={{ flex: 1 }}>
                  <Texto style={{ fontWeight: '600' }}>{s.nombre}</Texto>
                  {s.direccion ? <Texto tipo="pequeno">{s.direccion}</Texto> : null}
                </View>
              </Pressable>
            );
          })}
        </Tarjeta>
      ) : null}

      {cargando ? <Cargando /> : null}

      {filas.map(({ linea, p }) => (
        <Tarjeta key={linea.id} style={{ flexDirection: 'row', gap: 12, alignItems: 'center' }}>
          <FotoProducto uri={p?.imagen ?? null} tam={68} />
          <View style={{ flex: 1, gap: 6 }}>
            <Texto style={{ fontWeight: '600' }} lineas={2}>{p?.nombre ?? '…'}</Texto>
            {p ? <Texto tipo="pequeno">{soles(p.precio)} c/u · <Texto tipo="pequeno" color={t.texto} style={{ fontWeight: '700' }}>{soles(String(Number(p.precio) * linea.cantidad))}</Texto></Texto> : null}
            {p && linea.cantidad > p.maximo ? (
              <Texto tipo="pequeno" color={t.error}>{p.maximo === 0 ? 'Agotado en esta sede' : `Solo hay ${p.maximo} disponibles`}</Texto>
            ) : null}
            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
              <Cantidad valor={linea.cantidad} max={Math.max(linea.cantidad, p?.maximo ?? linea.cantidad)} onCambio={(n) => carrito.cambiar(linea.id, n)} />
              <Pressable
                onPress={() => Alert.alert('¿Quitar del carrito?', p?.nombre, [{ text: 'Cancelar', style: 'cancel' }, { text: 'Quitar', style: 'destructive', onPress: () => carrito.quitar(linea.id) }])}
                accessibilityRole="button"
                accessibilityLabel="Quitar del carrito"
                style={{ padding: 6 }}>
                <Ionicons name="trash-outline" size={20} color={t.error} />
              </Pressable>
            </View>
          </View>
        </Tarjeta>
      ))}

      <Campo etiqueta="Nota para el taller (opcional)" value={nota} onChangeText={setNota} maxLength={250} multiline placeholder="Ej. paso a recogerlo en la tarde" />
    </Pantalla>
  );
}
