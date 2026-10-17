import { Ionicons } from '@expo/vector-icons';
import { Tabs } from 'expo-router';
import type { ColorValue } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { useTema } from '@/lib/tema';

type N = React.ComponentProps<typeof Ionicons>['name'];

const icono = (activo: N, inactivo: N) =>
  function Icono({ color, focused }: { color: ColorValue; focused: boolean }) {
    return <Ionicons name={focused ? activo : inactivo} size={22} color={color} />;
  };

export default function TabsLayout() {
  const t = useTema();
  const { bottom } = useSafeAreaInsets();
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
      <Tabs.Screen name="reservas" options={{ title: 'Citas', tabBarIcon: icono('calendar', 'calendar-outline') }} />
      <Tabs.Screen name="perfil" options={{ title: 'Perfil', tabBarIcon: icono('person-circle', 'person-circle-outline') }} />
    </Tabs>
  );
}
