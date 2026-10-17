import { useEffect, useState } from 'react';
import { Image, KeyboardAvoidingView, Platform, ScrollView, View } from 'react-native';

import { Boton, Campo, Heroe, Pantalla, Tarjeta, Texto } from '@/components/ui';
import { api, ApiError, nombreDispositivo } from '@/lib/api';
import { useAuth } from '@/lib/auth';
import { celularLegible } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { Cuenta } from '@/lib/types';

/** Ingreso con celular + código de un solo uso. No hay contraseñas. */
export default function Login() {
  const t = useTema();
  const { taller, iniciarSesion, cambiarTaller } = useAuth();
  const [celular, setCelular] = useState('');
  const [codigo, setCodigo] = useState('');
  const [enviado, setEnviado] = useState(false);
  const [espera, setEspera] = useState(0);
  const [cargando, setCargando] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (espera <= 0) return;
    const id = setTimeout(() => setEspera((s) => s - 1), 1000);
    return () => clearTimeout(id);
  }, [espera]);

  const nueve = celular.replace(/\D/g, '');
  const celularValido = /^9\d{8}$/.test(nueve);

  async function pedirCodigo() {
    setCargando(true);
    setError(null);
    try {
      const r = await api.publico<{ reenviar_en_segundos: number }>('auth/otp/solicitar', { celular: nueve });
      setEnviado(true);
      setEspera(r.reenviar_en_segundos ?? 60);
    } catch (e) {
      if (e instanceof ApiError && e.code === 'ESPERA_REENVIO') {
        setEnviado(true);
        setEspera(Number(e.fields.reenviar_en_segundos?.[0] ?? 60));
      }
      setError(e instanceof ApiError ? (e.fields.celular?.[0] ?? e.message) : 'No pudimos enviar el código.');
    } finally {
      setCargando(false);
    }
  }

  async function verificar() {
    setCargando(true);
    setError(null);
    try {
      const r = await api.publico<{ token: string; cuenta: Cuenta }>('auth/otp/verificar', { celular: nueve, codigo, dispositivo: nombreDispositivo });
      await iniciarSesion(r.token);
    } catch (e) {
      setError(e instanceof ApiError ? e.message : 'No pudimos verificar el código.');
    } finally {
      setCargando(false);
    }
  }

  return (
    <KeyboardAvoidingView style={{ flex: 1 }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <Pantalla
        sinPadding
        cabecera={
          <Heroe>
            <View style={{ alignItems: 'center', gap: 12, paddingTop: 12, paddingBottom: 8 }}>
              {taller?.taller.logo ? (
                <View style={{ width: 104, height: 104, borderRadius: 28, backgroundColor: '#fff', alignItems: 'center', justifyContent: 'center', padding: 10 }}>
                  <Image source={{ uri: taller.taller.logo }} style={{ width: '100%', height: '100%' }} resizeMode="contain" accessibilityLabel="Logo del taller" />
                </View>
              ) : null}
              <Texto tipo="display" color="#fff" style={{ textAlign: 'center' }}>{taller?.taller.nombre ?? 'Tu taller'}</Texto>
              <Texto tipo="cuerpo" color="rgba(255,255,255,0.85)" style={{ textAlign: 'center' }}>Tus motos, tus servicios y tus citas en un solo lugar</Texto>
            </View>
          </Heroe>
        }>
        <ScrollView scrollEnabled={false} contentContainerStyle={{ padding: 16, gap: 14 }} keyboardShouldPersistTaps="handled">
          <Tarjeta style={{ gap: 14, padding: 20 }}>
            {!enviado ? (
              <>
                <Texto tipo="titulo">Ingresa con tu celular</Texto>
                <Campo
                  etiqueta="Tu celular"
                  value={celular}
                  onChangeText={setCelular}
                  keyboardType="phone-pad"
                  autoComplete="tel"
                  maxLength={13}
                  placeholder="987 654 321"
                  error={celular && !celularValido ? 'Ingresa los 9 dígitos de tu celular.' : undefined}
                />
                <Boton titulo="Enviarme un código" icono="paper-plane" onPress={pedirCodigo} cargando={cargando} deshabilitado={!celularValido} />
              </>
            ) : (
              <>
                <Texto tipo="titulo">Revisa tus mensajes</Texto>
                <Texto tipo="pequeno">Ingresa el código de 6 dígitos que enviamos al {celularLegible(nueve)}.</Texto>
                <Campo
                  etiqueta="Código"
                  value={codigo}
                  onChangeText={(v) => setCodigo(v.replace(/\D/g, '').slice(0, 6))}
                  keyboardType="number-pad"
                  autoComplete="one-time-code"
                  textContentType="oneTimeCode"
                  maxLength={6}
                  placeholder="······"
                  style={{ fontSize: 26, letterSpacing: 10, textAlign: 'center', fontWeight: '700' }}
                />
                <Boton titulo="Ingresar" icono="log-in" onPress={verificar} cargando={cargando} deshabilitado={codigo.length !== 6} />
                <Boton titulo={espera > 0 ? `Reenviar código en ${espera} s` : 'Reenviar código'} variante="suave" onPress={pedirCodigo} deshabilitado={espera > 0} />
                <Boton titulo="Cambiar de número" variante="secundario" onPress={() => { setEnviado(false); setCodigo(''); setError(null); }} />
              </>
            )}
            {error ? <Texto color={t.error}>{error}</Texto> : null}
          </Tarjeta>

          <Boton titulo="No es mi taller" variante="secundario" onPress={() => void cambiarTaller()} />
        </ScrollView>
      </Pantalla>
    </KeyboardAvoidingView>
  );
}
