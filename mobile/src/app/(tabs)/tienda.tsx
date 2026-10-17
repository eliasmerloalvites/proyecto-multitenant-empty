import { Ionicons } from '@expo/vector-icons';
import { useInfiniteQuery, useQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useEffect, useState } from 'react';
import { Pressable, ScrollView, TextInput, View } from 'react-native';

import { BotonCarrito, DisponibilidadChip, FotoProducto } from '@/components/tienda';
import { Boton, Cargando, ErrorCarga, Heroe, Pantalla, Tarjeta, Texto, Vacio } from '@/components/ui';
import { api } from '@/lib/api';
import { useCarrito } from '@/lib/carrito';
import { useRefrescarAlEnfocar } from '@/lib/enfoque';
import { soles } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { InicioTienda, Producto } from '@/lib/types';

export default function Tienda() {
  const t = useTema();
  const carrito = useCarrito();
  const [buscar, setBuscar] = useState('');
  const [busqueda, setBusqueda] = useState('');
  const [categoria, setCategoria] = useState<number | null>(null);

  // La busqueda se dispara al dejar de escribir.
  useEffect(() => {
    const id = setTimeout(() => setBusqueda(buscar.trim()), 400);
    return () => clearTimeout(id);
  }, [buscar]);

  const inicio = useQuery({ queryKey: ['tienda-inicio'], queryFn: () => api.get<InicioTienda>('catalogo/categorias') });
  const sede = carrito.sede ?? inicio.data?.sede_por_defecto ?? undefined;

  const q = useInfiniteQuery({
    queryKey: ['tienda-productos', sede, categoria, busqueda],
    enabled: inicio.isSuccess,
    initialPageParam: undefined as string | undefined,
    queryFn: async ({ pageParam }) => {
      const r = await api.getConMeta<Producto[]>('catalogo/productos', { sede, categoria: categoria ?? undefined, buscar: busqueda || undefined, cursor: pageParam, limite: 20 });
      return { filas: r.data, siguiente: (r.meta.siguiente_cursor as string | null) ?? undefined, total: r.meta.total as number };
    },
    getNextPageParam: (p) => p.siguiente,
  });
  useRefrescarAlEnfocar(q.refetch);

  const productos = q.data?.pages.flatMap((p) => p.filas) ?? [];
  const sedeNombre = inicio.data?.sedes.find((s) => s.id === sede)?.nombre;

  return (
    <View style={{ flex: 1 }}>
      <Pantalla
        sinPadding
        refrescando={q.isRefetching && !q.isFetchingNextPage}
        alRefrescar={() => void q.refetch()}
        cabecera={
          <Heroe
            bajo={
              <View style={{ marginTop: 12, flexDirection: 'row', alignItems: 'center', backgroundColor: '#fff', borderRadius: 14, paddingHorizontal: 14, height: 48, gap: 8 }}>
                <Ionicons name="search" size={18} color={t.suave} />
                <TextInput
                  value={buscar}
                  onChangeText={setBuscar}
                  placeholder="Buscar repuestos, aceites, marcas…"
                  placeholderTextColor="#94a3b8"
                  returnKeyType="search"
                  style={{ flex: 1, fontSize: 15, color: t.texto }}
                  accessibilityLabel="Buscar productos"
                />
                {buscar ? (
                  <Pressable onPress={() => setBuscar('')} accessibilityLabel="Borrar búsqueda">
                    <Ionicons name="close-circle" size={18} color={t.suave} />
                  </Pressable>
                ) : null}
              </View>
            }>
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
              <View style={{ flex: 1 }}>
                <Texto tipo="display" color="#fff">Tienda</Texto>
                <Texto tipo="pequeno" color="rgba(255,255,255,0.85)">
                  {sedeNombre ? `Recojo en ${sedeNombre} · pagas en el taller` : 'Pide y recoge en el taller'}
                </Texto>
              </View>
              <Pressable onPress={() => router.push('/pedidos')} accessibilityRole="button" style={{ alignItems: 'center', padding: 6 }}>
                <Ionicons name="receipt-outline" size={24} color="#fff" />
                <Texto tipo="pequeno" color="#fff" style={{ fontSize: 11 }}>Mis pedidos</Texto>
              </Pressable>
            </View>
          </Heroe>
        }>
        <View style={{ paddingTop: 12, gap: 12, paddingBottom: 90 }}>
          {inicio.data?.categorias.length ? (
            <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ gap: 8, paddingHorizontal: 16 }}>
              {[{ id: null as number | null, nombre: 'Todo' }, ...inicio.data.categorias].map((c) => {
                const activo = categoria === c.id;
                return (
                  <Pressable
                    key={String(c.id)}
                    onPress={() => setCategoria(c.id)}
                    accessibilityRole="button"
                    accessibilityState={{ selected: activo }}
                    style={{ paddingHorizontal: 14, paddingVertical: 8, borderRadius: 999, backgroundColor: activo ? t.primario : '#fff', borderWidth: 1, borderColor: activo ? t.primario : t.borde }}>
                    <Texto tipo="pequeno" color={activo ? t.sobrePrimario : t.texto} style={{ fontWeight: '600' }}>{c.nombre}</Texto>
                  </Pressable>
                );
              })}
            </ScrollView>
          ) : null}

          <View style={{ paddingHorizontal: 16, gap: 12 }}>
            {inicio.isError ? <ErrorCarga error={inicio.error} reintentar={() => void inicio.refetch()} /> : null}
            {q.isLoading || inicio.isLoading ? <Cargando /> : q.isError ? <ErrorCarga error={q.error} reintentar={() => void q.refetch()} /> : null}

            <View style={{ flexDirection: 'row', flexWrap: 'wrap', justifyContent: 'space-between', rowGap: 12 }}>
              {productos.map((p) => (
                <Tarjeta key={p.id} onPress={() => router.push({ pathname: '/producto/[id]', params: { id: String(p.id) } })} style={{ width: '48.5%', padding: 10, gap: 6 }}>
                  <FotoProducto uri={p.imagen} tam="100%" />
                  <Texto tipo="pequeno" style={{ fontSize: 11 }} lineas={1}>{p.marca ?? p.categoria.nombre}</Texto>
                  <Texto style={{ fontWeight: '600', fontSize: 14, minHeight: 38 }} lineas={2}>{p.nombre}</Texto>
                  <Texto tipo="subtitulo" color={t.primario}>{soles(p.precio)}</Texto>
                  <DisponibilidadChip valor={p.disponibilidad} />
                </Tarjeta>
              ))}
            </View>

            {q.isSuccess && productos.length === 0 ? (
              <Vacio icono="search-outline" titulo={busqueda ? 'Sin resultados' : 'Aún no hay productos'} detalle={busqueda ? `No encontramos "${busqueda}". Prueba con otra palabra.` : 'El taller todavía no publicó productos en su catálogo.'} />
            ) : null}

            {q.hasNextPage ? <Boton titulo="Ver más productos" variante="suave" onPress={() => void q.fetchNextPage()} cargando={q.isFetchingNextPage} /> : null}
          </View>
        </View>
      </Pantalla>
      <BotonCarrito />
    </View>
  );
}
