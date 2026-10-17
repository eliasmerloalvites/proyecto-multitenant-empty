import { Ionicons } from '@expo/vector-icons';
import { CameraView, useCameraPermissions } from 'expo-camera';
import { LinearGradient } from 'expo-linear-gradient';
import { useRef, useState } from 'react';
import { KeyboardAvoidingView, Platform, ScrollView, View } from 'react-native';
import { SafeAreaView, useSafeAreaInsets } from 'react-native-safe-area-context';

import { Boton, Campo, Tarjeta, Texto } from '@/components/ui';
import { ApiError, buscarTaller } from '@/lib/api';
import { useAuth } from '@/lib/auth';
import { codigoDeQr } from '@/lib/formato';

/**
 * Primer paso: identificar el taller. El QR (o el código escrito a mano)
 * contiene solo el código público del taller, que no da acceso a nada; con él
 * el directorio responde a qué dirección conectarse.
 */
export default function ElegirTaller() {
  const { top } = useSafeAreaInsets();
  const { elegirTaller } = useAuth();
  const [permiso, pedirPermiso] = useCameraPermissions();
  const [escaneando, setEscaneando] = useState(false);
  const [codigo, setCodigo] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [buscando, setBuscando] = useState(false);
  const ocupado = useRef(false);

  async function conectar(texto: string) {
    if (ocupado.current) return;
    const c = codigoDeQr(texto);
    if (!c) {
      setError('Ese código no es de un taller. Revisa que sean 8 letras y números.');
      return;
    }
    ocupado.current = true;
    setBuscando(true);
    setError(null);
    try {
      await elegirTaller(await buscarTaller(c));
    } catch (e) {
      setError(e instanceof ApiError ? e.message : 'No pudimos conectar con el taller.');
      setEscaneando(false);
    } finally {
      setBuscando(false);
      ocupado.current = false;
    }
  }

  async function abrirCamara() {
    const r = permiso?.granted ? permiso : await pedirPermiso();
    if (r.granted) setEscaneando(true);
    else setError('Sin permiso de cámara puedes escribir el código de tu taller.');
  }

  if (escaneando) {
    return (
      <View style={{ flex: 1, backgroundColor: '#000' }}>
        <CameraView style={{ flex: 1 }} facing="back" barcodeScannerSettings={{ barcodeTypes: ['qr'] }} onBarcodeScanned={({ data }) => void conectar(data)} />
        <View pointerEvents="none" style={{ position: 'absolute', top: 0, bottom: 0, left: 0, right: 0, alignItems: 'center', justifyContent: 'center' }}>
          <View style={{ width: 240, height: 240, borderRadius: 28, borderWidth: 3, borderColor: '#fff' }} />
        </View>
        <SafeAreaView style={{ position: 'absolute', left: 16, right: 16, bottom: 16, gap: 10 }}>
          <Texto color="#fff" style={{ textAlign: 'center', fontWeight: '600' }}>{error ?? 'Apunta al código QR de tu taller'}</Texto>
          <Boton titulo="Escribir el código" variante="suave" onPress={() => setEscaneando(false)} />
        </SafeAreaView>
      </View>
    );
  }

  return (
    <KeyboardAvoidingView style={{ flex: 1, backgroundColor: '#f3f4f7' }} behavior={Platform.OS === 'ios' ? 'padding' : undefined}>
      <ScrollView contentContainerStyle={{ flexGrow: 1 }} keyboardShouldPersistTaps="handled" showsVerticalScrollIndicator={false}>
        <LinearGradient colors={['#0b1220', '#1d4ed8']} start={{ x: 0, y: 0 }} end={{ x: 1, y: 1 }} style={{ paddingTop: top + 40, paddingBottom: 48, paddingHorizontal: 24, gap: 14, borderBottomLeftRadius: 32, borderBottomRightRadius: 32 }}>
          <View style={{ width: 64, height: 64, borderRadius: 20, backgroundColor: 'rgba(255,255,255,0.16)', alignItems: 'center', justifyContent: 'center' }}>
            <Ionicons name="speedometer" size={34} color="#fff" />
          </View>
          <Texto tipo="display" color="#fff">KaelTech Moto</Texto>
          <Texto tipo="cuerpo" color="rgba(255,255,255,0.85)">
            El historial de tu moto, el avance de cada servicio y tus citas, siempre contigo.
          </Texto>
        </LinearGradient>

        <View style={{ padding: 16, gap: 14, marginTop: -22 }}>
          <Tarjeta style={{ gap: 14, padding: 20 }}>
            <Texto tipo="titulo">Conecta con tu taller</Texto>
            <Boton titulo="Escanear QR del taller" icono="qr-code" onPress={abrirCamara} cargando={buscando} />
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 10 }}>
              <View style={{ flex: 1, height: 1, backgroundColor: '#e5e7ec' }} />
              <Texto tipo="pequeno">o escribe su código</Texto>
              <View style={{ flex: 1, height: 1, backgroundColor: '#e5e7ec' }} />
            </View>
            <Campo
              etiqueta="Código del taller"
              value={codigo}
              onChangeText={(v) => setCodigo(v.toUpperCase())}
              autoCapitalize="characters"
              autoCorrect={false}
              maxLength={8}
              placeholder="Ej. B5CTY5KF"
              style={{ letterSpacing: 3, fontWeight: '700' }}
            />
            <Boton titulo="Continuar" variante="suave" onPress={() => void conectar(codigo)} cargando={buscando} deshabilitado={codigo.length < 8} />
            {error ? <Texto color="#dc2626">{error}</Texto> : null}
          </Tarjeta>
          <Texto tipo="pequeno" style={{ textAlign: 'center' }}>Pídele el QR a tu taller: lo tiene en el mostrador.</Texto>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
}
