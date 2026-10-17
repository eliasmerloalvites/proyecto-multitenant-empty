import { focusManager, QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Stack } from 'expo-router';
import * as SplashScreen from 'expo-splash-screen';
import { StatusBar } from 'expo-status-bar';
import { useEffect } from 'react';
import { AppState, Platform } from 'react-native';

import { ApiError } from '@/lib/api';
import { AuthProvider, useAuth } from '@/lib/auth';
import { useTema } from '@/lib/tema';

SplashScreen.preventAutoHideAsync().catch(() => {});

const cliente = new QueryClient({
  defaultOptions: {
    queries: {
      staleTime: 30_000,
      // No se reintenta lo que el servidor ya respondió con un error de cliente.
      retry: (n, e) => !(e instanceof ApiError && e.status >= 400 && e.status < 500) && n < 2,
    },
  },
});

// Al volver a la app desde segundo plano, las consultas activas se refrescan.
if (Platform.OS !== 'web') {
  focusManager.setEventListener((alCambiar) => {
    const s = AppState.addEventListener('change', (e) => alCambiar(e === 'active'));
    return () => s.remove();
  });
}

function Rutas() {
  const { listo, taller, conSesion } = useAuth();
  const t = useTema();

  useEffect(() => {
    if (listo) SplashScreen.hideAsync().catch(() => {});
  }, [listo]);

  if (!listo) return null;

  return (
    <>
      <StatusBar style="dark" />
      <Stack screenOptions={{ headerStyle: { backgroundColor: t.tarjeta }, headerTintColor: t.texto, headerTitleStyle: { fontWeight: '700' } }}>
        <Stack.Protected guard={!taller}>
          <Stack.Screen name="taller" options={{ title: 'Elige tu taller', headerShown: false }} />
        </Stack.Protected>
        <Stack.Protected guard={!!taller && !conSesion}>
          <Stack.Screen name="login" options={{ title: 'Ingresar', headerShown: false }} />
        </Stack.Protected>
        <Stack.Protected guard={!!taller && conSesion}>
          <Stack.Screen name="(tabs)" options={{ headerShown: false }} />
          <Stack.Screen name="moto/[id]" options={{ title: 'Mi moto' }} />
          <Stack.Screen name="vincular" options={{ title: 'Agregar una moto' }} />
          <Stack.Screen name="orden/[ref]" options={{ title: 'Orden de servicio' }} />
          <Stack.Screen name="reservar" options={{ title: 'Reservar cita' }} />
        </Stack.Protected>
      </Stack>
    </>
  );
}

export default function Raiz() {
  return (
    <QueryClientProvider client={cliente}>
      <AuthProvider>
        <Rutas />
      </AuthProvider>
    </QueryClientProvider>
  );
}
