import { Ionicons } from '@expo/vector-icons';
import { router } from 'expo-router';
import { Image, Pressable, Text, View } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { Chip, sombra } from '@/components/ui';
import { useCarrito } from '@/lib/carrito';
import { useTema } from '@/lib/tema';
import type { Disponibilidad, EstadoPedido } from '@/lib/types';

export function DisponibilidadChip({ valor }: { valor: Disponibilidad }) {
  const d = {
    DISPONIBLE: { texto: 'Disponible', tono: 'ok' as const },
    ULTIMAS: { texto: 'Últimas unidades', tono: 'aviso' as const },
    AGOTADO: { texto: 'Agotado', tono: 'neutro' as const },
  }[valor];
  return <Chip texto={d.texto} tono={d.tono} />;
}

export function PedidoChip({ estado, texto }: { estado: EstadoPedido; texto?: string }) {
  const d = {
    PENDIENTE: { texto: 'Por confirmar', tono: 'aviso' as const, icono: 'hourglass' as const },
    CONFIRMADO: { texto: 'Preparando', tono: 'info' as const, icono: 'cube' as const },
    LISTO: { texto: 'Listo para recoger', tono: 'ok' as const, icono: 'bag-check' as const },
    ENTREGADO: { texto: 'Entregado', tono: 'neutro' as const, icono: 'checkmark-done' as const },
    CANCELADO: { texto: 'Cancelado', tono: 'error' as const, icono: 'close-circle' as const },
  }[estado];
  return <Chip texto={texto && estado === 'LISTO' ? d.texto : d.texto} tono={d.tono} icono={d.icono} />;
}

/** Foto del producto o un marcador si no tiene. */
export function FotoProducto({ uri, tam }: { uri: string | null; tam: number | '100%' }) {
  const t = useTema();
  const estilo = { width: tam, height: typeof tam === 'number' ? tam : undefined, aspectRatio: typeof tam === 'number' ? undefined : 1, borderRadius: 14, backgroundColor: '#f1f5f9' } as const;
  return uri ? (
    <Image source={{ uri }} style={estilo} resizeMode="contain" />
  ) : (
    <View style={[estilo, { alignItems: 'center', justifyContent: 'center' }]}>
      <Ionicons name="cube-outline" size={typeof tam === 'number' ? tam * 0.4 : 40} color={t.suave} />
    </View>
  );
}

/** Boton flotante del carrito con el numero de unidades. */
export function BotonCarrito() {
  const t = useTema();
  const { unidades } = useCarrito();
  const { bottom } = useSafeAreaInsets();
  if (unidades === 0) return null;
  return (
    <Pressable
      onPress={() => router.push('/carrito')}
      accessibilityRole="button"
      accessibilityLabel={`Ver carrito, ${unidades} productos`}
      style={({ pressed }) => [
        { position: 'absolute', right: 18, bottom: 18 + Math.max(0, bottom - 34), backgroundColor: t.primario, borderRadius: 30, paddingHorizontal: 20, height: 56, flexDirection: 'row', alignItems: 'center', gap: 10, opacity: pressed ? 0.85 : 1 },
        sombra,
      ]}>
      <Ionicons name="cart" size={22} color={t.sobrePrimario} />
      <Text style={{ color: t.sobrePrimario, fontWeight: '700', fontSize: 15 }}>Ver carrito</Text>
      <View style={{ minWidth: 26, height: 26, borderRadius: 13, backgroundColor: t.sobrePrimario, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 6 }}>
        <Text style={{ color: t.primario, fontWeight: '800', fontSize: 13 }}>{unidades}</Text>
      </View>
    </Pressable>
  );
}

/** Selector de cantidad: - n + */
export function Cantidad({ valor, min = 1, max, onCambio }: { valor: number; min?: number; max: number; onCambio: (n: number) => void }) {
  const t = useTema();
  const btn = (icono: 'remove' | 'add', deshabilitado: boolean, n: number) => (
    <Pressable
      onPress={() => onCambio(n)}
      disabled={deshabilitado}
      accessibilityRole="button"
      accessibilityLabel={icono === 'add' ? 'Aumentar' : 'Disminuir'}
      style={{ width: 36, height: 36, borderRadius: 18, borderWidth: 1.5, borderColor: t.borde, alignItems: 'center', justifyContent: 'center', opacity: deshabilitado ? 0.35 : 1, backgroundColor: '#fff' }}>
      <Ionicons name={icono} size={18} color={t.texto} />
    </Pressable>
  );
  return (
    <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
      {btn('remove', valor <= min, valor - 1)}
      <Text style={{ minWidth: 22, textAlign: 'center', fontSize: 17, fontWeight: '700', color: t.texto }}>{valor}</Text>
      {btn('add', valor >= max, valor + 1)}
    </View>
  );
}
