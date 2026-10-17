import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useEffect, useState } from 'react';
import { Alert, Linking, View } from 'react-native';

import { Boton, Campo, Cargando, ErrorCarga, Heroe, Icono, Pantalla, Tarjeta, Texto } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useAuth } from '@/lib/auth';
import { useTema } from '@/lib/tema';
import type { Cuenta } from '@/lib/types';

export default function Perfil() {
  const t = useTema();
  const { taller, cerrarSesion, cambiarTaller } = useAuth();
  const cache = useQueryClient();
  const q = useQuery({ queryKey: ['perfil'], queryFn: () => api.get<Cuenta>('perfil') });
  const [nombre, setNombre] = useState('');
  const [correo, setCorreo] = useState('');
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (q.data) {
      setNombre(q.data.nombre ?? '');
      setCorreo(q.data.correo ?? '');
    }
  }, [q.data]);

  const guardar = useMutation({
    mutationFn: () => api.patch<Cuenta>('perfil', { nombre: nombre.trim() || null, correo: correo.trim() || null }),
    onSuccess: (c) => {
      cache.setQueryData(['perfil'], c);
      setError(null);
      Alert.alert('Listo', 'Guardamos tus datos.');
    },
    onError: (e) => setError(e instanceof ApiError ? (Object.values(e.fields)[0]?.[0] ?? e.message) : 'No se pudo guardar.'),
  });

  const contacto = taller?.taller.contacto;
  const iniciales = (q.data?.nombre ?? '').split(' ').filter(Boolean).slice(0, 2).map((p) => p[0]).join('').toUpperCase();

  return (
    <Pantalla
      sinPadding
      cabecera={
        <Heroe>
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: 14 }}>
            <View style={{ width: 64, height: 64, borderRadius: 32, backgroundColor: 'rgba(255,255,255,0.2)', alignItems: 'center', justifyContent: 'center' }}>
              {iniciales ? <Texto tipo="titulo" color="#fff">{iniciales}</Texto> : <Icono nombre="person" tam={30} color="#fff" />}
            </View>
            <View style={{ flex: 1 }}>
              <Texto tipo="titulo" color="#fff" lineas={1}>{q.data?.nombre || 'Mi perfil'}</Texto>
              <Texto tipo="cuerpo" color="rgba(255,255,255,0.85)">{q.data?.celular ?? ' '}</Texto>
            </View>
          </View>
        </Heroe>
      }>
      <View style={{ padding: 16, gap: 14 }}>
        {q.isLoading ? <Cargando /> : q.isError ? <ErrorCarga error={q.error} reintentar={() => void q.refetch()} /> : null}

        {q.data ? (
          <Tarjeta style={{ gap: 12 }}>
            <Texto tipo="etiqueta">Mis datos</Texto>
            <Campo etiqueta="Nombre" value={nombre} onChangeText={setNombre} maxLength={120} autoComplete="name" />
            <Campo etiqueta="Correo (opcional)" value={correo} onChangeText={setCorreo} maxLength={120} keyboardType="email-address" autoCapitalize="none" autoComplete="email" error={error ?? undefined} />
            <Boton titulo="Guardar cambios" onPress={() => guardar.mutate()} cargando={guardar.isPending} />
          </Tarjeta>
        ) : null}

        <Tarjeta style={{ gap: 10 }}>
          <Texto tipo="etiqueta">Mi taller</Texto>
          <Texto tipo="subtitulo">{taller?.taller.nombre}</Texto>
          {taller?.taller.sedes.map((s) => (
            <View key={s.id} style={{ flexDirection: 'row', gap: 8, alignItems: 'flex-start' }}>
              <Icono nombre="location-outline" tam={16} color={t.primario} />
              <Texto tipo="pequeno" style={{ flex: 1 }}>{s.nombre}{s.direccion ? ` · ${s.direccion}` : ''}</Texto>
            </View>
          ))}
          <View style={{ flexDirection: 'row', gap: 10, marginTop: 4 }}>
            {contacto?.whatsapp ? (
              <View style={{ flex: 1 }}>
                <Boton titulo="WhatsApp" icono="logo-whatsapp" variante="suave" onPress={() => void Linking.openURL(`https://wa.me/51${contacto.whatsapp!.replace(/\D/g, '').slice(-9)}`)} />
              </View>
            ) : null}
            {contacto?.telefono ? (
              <View style={{ flex: 1 }}>
                <Boton titulo="Llamar" icono="call" variante="suave" onPress={() => void Linking.openURL(`tel:${contacto.telefono!.replace(/[^\d+]/g, '')}`)} />
              </View>
            ) : null}
          </View>
        </Tarjeta>

        <Boton
          titulo="Cerrar sesión"
          icono="log-out-outline"
          variante="secundario"
          onPress={() => Alert.alert('¿Cerrar sesión?', undefined, [{ text: 'Cancelar', style: 'cancel' }, { text: 'Cerrar sesión', onPress: () => void cerrarSesion(true) }])}
        />
        <Boton
          titulo="Cambiar de taller"
          icono="swap-horizontal"
          variante="secundario"
          onPress={() => Alert.alert('¿Cambiar de taller?', 'Se cerrará tu sesión en este taller.', [{ text: 'Cancelar', style: 'cancel' }, { text: 'Cambiar', onPress: () => void cambiarTaller() }])}
        />
      </View>
    </Pantalla>
  );
}
