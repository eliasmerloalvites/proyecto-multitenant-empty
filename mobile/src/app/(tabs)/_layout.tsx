import { Ionicons } from '@expo/vector-icons';
import { useQuery } from '@tanstack/react-query';
import { Tabs } from 'expo-router';
import type { ColorValue } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { api } from '@/lib/api';
import { useAuth } from '@/lib/auth';
import { useTema } from '@/lib/tema';
import type { Taller } from '@/lib/types';

type N = React.ComponentProps<typeof Ionicons>['name'];

const icono = (activo: N, inactivo: N) =>
  function Icono({ color, focused }: { color: ColorValue; focused: boolean }) {
    return <Ionicons name={focused ? activo : inactivo} size={22} color={color} />;
  };

export default function TabsLayout() {
  const t = useTema();
  const { bottom } = useSafeAreaInsets();
  const { taller } = useAuth();
  // Datos frescos del taller: si activa o desactiva la tienda, la pestana aparece o se va.
  const info = useQuery({ queryKey: ['taller-info'], queryFn: () => api.get<Taller>('taller'), staleTime: 5 * 60_000 });
  const tienda = info.data?.tienda ?? taller?.taller.tienda ?? false;
  return (
    <Tabs
      screenOptions={{
        headerShown: false,
        tabBarActiveTintColor: t.primario,
        tabBarInactiveTintColor: '#94a3b8',
        tabBarLabelStyle: { fontSize: 11, fontWeight: '600' },
        tabBarStyle: { backgroundColor: '#fff', borderTopColor: t.borde, height: 56 + bottom, paddingTop: 6, paddingBottom: bottom + 4 },
      }}>
      <Tabs.Screen name="index" options={{ title: 'Garaje', tabBarIcon: icono('speedometer', 'speedometer-outline') }} />
      <Tabs.Screen name="ordenes" options={{ title: 'Servicios', tabBarIcon: icono('construct', 'construct-outline') }} />
      <Tabs.Screen name="tienda" options={{ title: 'Tienda', href: tienda ? undefined : null, tabBarIcon: icono('storefront', 'storefront-outline') }} />
      <Tabs.Screen name="reservas" options={{ title: 'Citas', tabBarIcon: icono('calendar', 'calendar-outline') }} />
      <Tabs.Screen name="perfil" options={{ title: 'Perfil', tabBarIcon: icono('person-circle', 'person-circle-outline') }} />
    </Tabs>
  );
}
