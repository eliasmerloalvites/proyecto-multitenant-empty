import { Ionicons } from '@expo/vector-icons';
import * as Haptics from 'expo-haptics';
import { LinearGradient } from 'expo-linear-gradient';
import { useEffect, useRef, type ComponentProps, type ReactNode } from 'react';
import {
  ActivityIndicator,
  Animated,
  Platform,
  Pressable,
  RefreshControl,
  ScrollView,
  StyleSheet,
  Text,
  TextInput,
  View,
  type TextInputProps,
  type ViewStyle,
} from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';

import { ApiError } from '@/lib/api';
import { useTema } from '@/lib/tema';

export type NombreIcono = ComponentProps<typeof Ionicons>['name'];

export const sombra: ViewStyle = Platform.select({
  ios: { shadowColor: '#0f172a', shadowOpacity: 0.05, shadowRadius: 10, shadowOffset: { width: 0, height: 3 } },
  default: { elevation: 2 },
}) as ViewStyle;

const toque = () => {
  if (Platform.OS !== 'web') void Haptics.selectionAsync().catch(() => {});
};

export function Icono({ nombre, tam = 20, color }: { nombre: NombreIcono; tam?: number; color?: string }) {
  const t = useTema();
  return <Ionicons name={nombre} size={tam} color={color ?? t.texto} />;
}

export function Pantalla({
  children,
  refrescando,
  alRefrescar,
  pie,
  cabecera,
  sinPadding,
}: {
  children: ReactNode;
  refrescando?: boolean;
  alRefrescar?: () => void;
  pie?: ReactNode;
  /** Contenido fijo arriba del scroll, a todo el ancho (héroe). */
  cabecera?: ReactNode;
  sinPadding?: boolean;
}) {
  const t = useTema();
  return (
    <SafeAreaView style={{ flex: 1, backgroundColor: t.fondo }} edges={['left', 'right', 'bottom']}>
      <ScrollView
        contentContainerStyle={{ padding: sinPadding ? 0 : 16, gap: 14, flexGrow: 1, paddingBottom: 28 }}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
        refreshControl={alRefrescar ? <RefreshControl refreshing={!!refrescando} onRefresh={alRefrescar} tintColor={t.primario} /> : undefined}>
        {cabecera}
        {children}
      </ScrollView>
      {pie}
    </SafeAreaView>
  );
}

/** Banda superior con el color del taller. */
export function Heroe({ children, bajo }: { children: ReactNode; bajo?: ReactNode }) {
  const t = useTema();
  const { top } = useSafeAreaInsets();
  return (
    <LinearGradient
      colors={[t.primarioOscuro, t.primario]}
      start={{ x: 0, y: 0 }}
      end={{ x: 1, y: 1 }}
      style={{ paddingHorizontal: 20, paddingTop: top + 10, paddingBottom: 20, gap: 3, borderBottomLeftRadius: 24, borderBottomRightRadius: 24 }}>
      {children}
      {bajo}
    </LinearGradient>
  );
}

export function Tarjeta({ children, onPress, style }: { children: ReactNode; onPress?: () => void; style?: ViewStyle }) {
  const t = useTema();
  const base: ViewStyle = { backgroundColor: t.tarjeta, borderRadius: 16, padding: 14, gap: 6, ...sombra };
  return onPress ? (
    <Pressable
      onPress={() => {
        toque();
        onPress();
      }}
      accessibilityRole="button"
      style={({ pressed }) => [base, style, pressed && { transform: [{ scale: 0.985 }], opacity: 0.92 }]}>
      {children}
    </Pressable>
  ) : (
    <View style={[base, style]}>{children}</View>
  );
}

export function Texto({
  children,
  tipo = 'cuerpo',
  color,
  style,
  lineas,
}: {
  children: ReactNode;
  tipo?: 'display' | 'titulo' | 'subtitulo' | 'cuerpo' | 'pequeno' | 'etiqueta';
  color?: string;
  style?: object;
  lineas?: number;
}) {
  const t = useTema();
  const estilos = {
    display: { fontSize: 24, fontWeight: '700' as const, color: t.texto, letterSpacing: -0.5 },
    titulo: { fontSize: 20, fontWeight: '700' as const, color: t.texto, letterSpacing: -0.3 },
    subtitulo: { fontSize: 16, fontWeight: '600' as const, color: t.texto },
    cuerpo: { fontSize: 15, lineHeight: 21, color: t.texto },
    pequeno: { fontSize: 13, lineHeight: 18, color: t.suave },
    etiqueta: { fontSize: 11.5, fontWeight: '700' as const, color: t.suave, textTransform: 'uppercase' as const, letterSpacing: 0.9 },
  };
  return (
    <Text numberOfLines={lineas} style={[estilos[tipo], color ? { color } : null, style]}>
      {children}
    </Text>
  );
}

export function Boton({
  titulo,
  onPress,
  cargando,
  deshabilitado,
  variante = 'primario',
  icono,
}: {
  titulo: string;
  onPress: () => void;
  cargando?: boolean;
  deshabilitado?: boolean;
  variante?: 'primario' | 'secundario' | 'peligro' | 'suave';
  icono?: NombreIcono;
}) {
  const t = useTema();
  const inactivo = deshabilitado || cargando;
  const fondo = { primario: t.primario, peligro: t.error, secundario: 'transparent', suave: t.primarioClaro }[variante];
  const texto = { primario: t.sobrePrimario, peligro: '#fff', secundario: t.primario, suave: t.primario }[variante];
  return (
    <Pressable
      onPress={() => {
        toque();
        onPress();
      }}
      disabled={inactivo}
      accessibilityRole="button"
      accessibilityState={{ disabled: !!inactivo, busy: !!cargando }}
      style={({ pressed }) => [
        s.boton,
        variante === 'primario' && sombra,
        { backgroundColor: fondo, borderColor: variante === 'secundario' ? t.borde : fondo, opacity: inactivo ? 0.45 : 1, transform: [{ scale: pressed ? 0.98 : 1 }] },
      ]}>
      {cargando ? (
        <ActivityIndicator color={texto} />
      ) : (
        <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
          {icono ? <Ionicons name={icono} size={19} color={texto} /> : null}
          <Text style={{ color: texto, fontWeight: '600', fontSize: 15 }}>{titulo}</Text>
        </View>
      )}
    </Pressable>
  );
}

export function Campo({ etiqueta, error, ...props }: TextInputProps & { etiqueta: string; error?: string }) {
  const t = useTema();
  return (
    <View style={{ gap: 6 }}>
      <Texto tipo="etiqueta">{etiqueta}</Texto>
      <TextInput
        placeholderTextColor="#94a3b8"
        accessibilityLabel={etiqueta}
        selectionColor={t.primario}
        {...props}
        style={[s.campo, { borderColor: error ? t.error : t.borde, color: t.texto, backgroundColor: '#f8fafc' }, props.multiline && { minHeight: 84, textAlignVertical: 'top', paddingTop: 12 }]}
      />
      {error ? <Texto tipo="pequeno" color={t.error}>{error}</Texto> : null}
    </View>
  );
}

export function Chip({ texto, tono, icono }: { texto: string; tono: 'ok' | 'aviso' | 'error' | 'neutro' | 'info'; icono?: NombreIcono }) {
  const t = useTema();
  const c = { ok: t.ok, aviso: t.aviso, error: t.error, neutro: t.suave, info: t.primario }[tono];
  return (
    <View style={{ alignSelf: 'flex-start', flexDirection: 'row', alignItems: 'center', gap: 5, borderRadius: 999, paddingHorizontal: 10, paddingVertical: 5, backgroundColor: c + '1c' }}>
      {icono ? <Ionicons name={icono} size={13} color={c} /> : <View style={{ width: 6, height: 6, borderRadius: 3, backgroundColor: c }} />}
      <Text style={{ color: c, fontSize: 12, fontWeight: '700' }}>{texto}</Text>
    </View>
  );
}

/** Placa estilizada. */
export function Placa({ valor, tam = 'md' }: { valor: string; tam?: 'sm' | 'md' | 'lg' }) {
  const t = useTema();
  const f = { sm: 12, md: 15, lg: 22 }[tam];
  const limpia = valor.length > 3 ? `${valor.slice(0, 3)}·${valor.slice(3)}` : valor;
  return (
    <View style={{ alignSelf: 'flex-start', backgroundColor: '#fff', borderRadius: 6, borderWidth: 1.5, borderColor: '#334155', paddingHorizontal: f * 0.65, paddingVertical: f * 0.12, overflow: 'hidden' }}>
      <View style={{ position: 'absolute', left: 0, right: 0, top: 0, height: 2.5, backgroundColor: t.primario }} />
      <Text style={{ fontSize: f, fontWeight: '700', letterSpacing: 1.5, color: '#1e293b', fontVariant: ['tabular-nums'] }}>{limpia}</Text>
    </View>
  );
}

/** Bloque de carga con forma de contenido en lugar de un círculo suelto. */
export function Esqueleto({ alto = 90, cantidad = 2 }: { alto?: number; cantidad?: number }) {
  const op = useRef(new Animated.Value(0.45)).current;
  useEffect(() => {
    const a = Animated.loop(
      Animated.sequence([Animated.timing(op, { toValue: 1, duration: 700, useNativeDriver: true }), Animated.timing(op, { toValue: 0.45, duration: 700, useNativeDriver: true })]),
    );
    a.start();
    return () => a.stop();
  }, [op]);
  return (
    <View style={{ gap: 12 }}>
      {Array.from({ length: cantidad }, (_, i) => (
        <Animated.View key={i} style={{ height: alto, borderRadius: 18, backgroundColor: '#e2e8f0', opacity: op }} />
      ))}
    </View>
  );
}

export function Cargando() {
  return <Esqueleto />;
}

export function Vacio({ titulo, detalle, accion, icono = 'sparkles-outline' }: { titulo: string; detalle?: string; accion?: ReactNode; icono?: NombreIcono }) {
  const t = useTema();
  return (
    <View style={{ padding: 28, alignItems: 'center', gap: 10 }}>
      <View style={{ width: 64, height: 64, borderRadius: 32, backgroundColor: t.primarioClaro, alignItems: 'center', justifyContent: 'center' }}>
        <Ionicons name={icono} size={28} color={t.primario} />
      </View>
      <Texto tipo="subtitulo" style={{ textAlign: 'center' }}>{titulo}</Texto>
      {detalle ? <Texto tipo="pequeno" style={{ textAlign: 'center', maxWidth: 280 }}>{detalle}</Texto> : null}
      {accion}
    </View>
  );
}

export function ErrorCarga({ error, reintentar }: { error: unknown; reintentar?: () => void }) {
  const t = useTema();
  const msg = error instanceof ApiError ? error.message : 'No se pudo cargar la información.';
  return (
    <View style={{ padding: 24, alignItems: 'center', gap: 12 }}>
      <Ionicons name="cloud-offline-outline" size={34} color={t.error} />
      <Texto color={t.error} style={{ textAlign: 'center' }}>{msg}</Texto>
      {reintentar ? <Boton titulo="Reintentar" variante="suave" icono="refresh" onPress={reintentar} /> : null}
    </View>
  );
}

const s = StyleSheet.create({
  boton: { minHeight: 48, borderRadius: 12, borderWidth: 1.5, alignItems: 'center', justifyContent: 'center', paddingHorizontal: 18 },
  campo: { minHeight: 52, borderWidth: 1.5, borderRadius: 14, paddingHorizontal: 16, fontSize: 16 },
});
