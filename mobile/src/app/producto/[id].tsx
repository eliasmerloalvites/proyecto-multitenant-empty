import { useQuery } from '@tanstack/react-query';
import { router, useLocalSearchParams } from 'expo-router';
import { useEffect, useState } from 'react';
import { Dimensions, Image, ScrollView, View } from 'react-native';

import { BotonCarrito, Cantidad, DisponibilidadChip, FotoProducto } from '@/components/tienda';
import { Boton, Cargando, ErrorCarga, Pantalla, Tarjeta, Texto } from '@/components/ui';
import { api } from '@/lib/api';
import { useCarrito } from '@/lib/carrito';
import { soles } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { ProductoDetalle } from '@/lib/types';

export default function DetalleProducto() {
  const t = useTema();
  const { id } = useLocalSearchParams<{ id: string }>();
  const carrito = useCarrito();
  const [cantidad, setCantidad] = useState(1);
  const [foto, setFoto] = useState(0);
  const [agregado, setAgregado] = useState(false);

  const q = useQuery({
    queryKey: ['producto', id, carrito.sede],
    queryFn: () => api.get<ProductoDetalle>(`catalogo/productos/${id}`, { sede: carrito.sede ?? undefined }),
  });

  useEffect(() => {
    if (!agregado) return;
    const t1 = setTimeout(() => setAgregado(false), 2500);
    return () => clearTimeout(t1);
  }, [agregado]);

  if (q.isLoading) return <Pantalla><Cargando /></Pantalla>;
  if (q.isError || !q.data) return <Pantalla><ErrorCarga error={q.error} reintentar={() => void q.refetch()} /></Pantalla>;

  const p = q.data;
  const enCarrito = carrito.lineas.find((l) => l.id === p.id)?.cantidad ?? 0;
  const maxAgregar = Math.max(0, p.maximo - enCarrito);
  const ancho = Dimensions.get('window').width - 32;

  return (
    <View style={{ flex: 1 }}>
      <Pantalla
        pie={
          <View style={{ padding: 16, gap: 10, backgroundColor: '#fff', borderTopWidth: 1, borderColor: t.borde }}>
            {p.disponibilidad === 'AGOTADO' ? (
              <Texto tipo="pequeno" style={{ textAlign: 'center' }}>Este producto está agotado en la sede elegida.</Texto>
            ) : maxAgregar === 0 ? (
              <Texto tipo="pequeno" style={{ textAlign: 'center' }}>Ya tienes en el carrito todas las unidades disponibles.</Texto>
            ) : (
              <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', gap: 12 }}>
                <Cantidad valor={Math.min(cantidad, maxAgregar)} max={maxAgregar} onCambio={setCantidad} />
                <View style={{ flex: 1 }}>
                  <Boton
                    titulo={agregado ? 'Agregado ✓' : `Agregar · ${soles(String(Number(p.precio) * Math.min(cantidad, maxAgregar)))}`}
                    icono="cart"
                    onPress={() => {
                      carrito.agregar(p.id, Math.min(cantidad, maxAgregar));
                      setCantidad(1);
                      setAgregado(true);
                    }}
                  />
                </View>
              </View>
            )}
            {enCarrito > 0 ? <Boton titulo={`Ir al carrito (${carrito.unidades})`} variante="secundario" onPress={() => router.push('/carrito')} /> : null}
          </View>
        }>
        {p.fotos.length > 0 ? (
          <View style={{ gap: 8 }}>
            <ScrollView
              horizontal
              pagingEnabled
              showsHorizontalScrollIndicator={false}
              onMomentumScrollEnd={(e) => setFoto(Math.round(e.nativeEvent.contentOffset.x / ancho))}
              style={{ borderRadius: 18, backgroundColor: '#fff' }}>
              {p.fotos.map((f) => (
                <Image key={f} source={{ uri: f }} style={{ width: ancho, height: ancho * 0.85 }} resizeMode="contain" />
              ))}
            </ScrollView>
            {p.fotos.length > 1 ? (
              <View style={{ flexDirection: 'row', justifyContent: 'center', gap: 6 }}>
                {p.fotos.map((f, i) => (
                  <View key={f} style={{ width: i === foto ? 18 : 6, height: 6, borderRadius: 3, backgroundColor: i === foto ? t.primario : t.borde }} />
                ))}
              </View>
            ) : null}
          </View>
        ) : (
          <FotoProducto uri={null} tam="100%" />
        )}

        <Tarjeta style={{ gap: 10 }}>
          <Texto tipo="pequeno">{[p.marca, p.categoria.nombre].filter(Boolean).join(' · ')}</Texto>
          <Texto tipo="titulo">{p.nombre}</Texto>
          <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
            <Texto tipo="display" color={t.primario}>{soles(p.precio)}</Texto>
            <DisponibilidadChip valor={p.disponibilidad} />
          </View>
        </Tarjeta>

        {p.descripcion ? (
          <Tarjeta style={{ gap: 6 }}>
            <Texto tipo="etiqueta">Descripción</Texto>
            <Texto>{p.descripcion}</Texto>
          </Tarjeta>
        ) : null}

        <Tarjeta style={{ gap: 6, backgroundColor: t.primarioClaro }}>
          <Texto tipo="etiqueta" color={t.primario}>¿Cómo funciona?</Texto>
          <Texto tipo="pequeno">Agrega al carrito y confirma tu pedido. El taller lo prepara y te avisa en la app cuando esté listo: lo recoges y pagas en el taller.</Texto>
        </Tarjeta>
      </Pantalla>
      {enCarrito === 0 ? <BotonCarrito /> : null}
    </View>
  );
}
