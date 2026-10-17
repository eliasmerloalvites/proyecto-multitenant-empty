import { useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { View } from 'react-native';

import { Boton, Campo, Icono, Pantalla, Tarjeta, Texto } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { useTema } from '@/lib/tema';

/**
 * Dos caminos: el código que da el taller en el mostrador (queda vinculada al
 * instante) o pedir que el taller la verifique (queda pendiente de aprobación).
 */
export default function Vincular() {
  const t = useTema();
  const cache = useQueryClient();
  const [codigo, setCodigo] = useState('');
  const [placa, setPlaca] = useState('');
  const [errorCodigo, setErrorCodigo] = useState<string | null>(null);
  const [errorPlaca, setErrorPlaca] = useState<string | null>(null);
  const [aviso, setAviso] = useState<string | null>(null);
  const [ocupado, setOcupado] = useState<'codigo' | 'placa' | null>(null);

  async function conCodigo() {
    setOcupado('codigo');
    setErrorCodigo(null);
    try {
      await api.post('motos/vincular-invitacion', { codigo: codigo.trim() });
      await cache.invalidateQueries({ queryKey: ['garaje'] });
      await cache.invalidateQueries({ queryKey: ['ordenes'] });
      router.back();
    } catch (e) {
      setErrorCodigo(e instanceof ApiError ? e.message : 'No pudimos validar el código.');
    } finally {
      setOcupado(null);
    }
  }

  async function solicitar() {
    setOcupado('placa');
    setErrorPlaca(null);
    setAviso(null);
    try {
      await api.post('motos/solicitar-vinculo', { placa: placa.trim() });
      await cache.invalidateQueries({ queryKey: ['garaje'] });
      setAviso('Enviamos tu solicitud al taller. Cuando la aprueben, la moto aparecerá en tu garaje.');
      setPlaca('');
    } catch (e) {
      setErrorPlaca(e instanceof ApiError ? (e.fields.placa?.[0] ?? e.message) : 'No pudimos enviar la solicitud.');
    } finally {
      setOcupado(null);
    }
  }

  return (
    <Pantalla>
      <Tarjeta style={{ gap: 12 }}>
        <View style={{ flexDirection: 'row', alignItems: 'center', gap: 10 }}>
          <Icono nombre="key" tam={20} color={t.primario} />
          <Texto tipo="subtitulo">Tengo un código del taller</Texto>
        </View>
        <Texto tipo="pequeno">Pídelo en el mostrador: es la forma más rápida. Sirve una sola vez y vence en pocos días.</Texto>
        <Campo etiqueta="Código" value={codigo} onChangeText={(v) => setCodigo(v.toUpperCase())} autoCapitalize="characters" autoCorrect={false} error={errorCodigo ?? undefined} />
        <Boton titulo="Vincular moto" icono="link" onPress={conCodigo} cargando={ocupado === 'codigo'} deshabilitado={codigo.trim().length < 4} />
      </Tarjeta>

      <Tarjeta style={{ gap: 12 }}>
        <View style={{ flexDirection: 'row', alignItems: 'center', gap: 10 }}>
          <Icono nombre="shield-checkmark" tam={20} color={t.primario} />
          <Texto tipo="subtitulo">No tengo código</Texto>
        </View>
        <Texto tipo="pequeno">Escribe la placa y el taller verificará que la moto es tuya.</Texto>
        <Campo etiqueta="Placa" value={placa} onChangeText={(v) => setPlaca(v.toUpperCase())} autoCapitalize="characters" autoCorrect={false} maxLength={10} placeholder="ABC-123" error={errorPlaca ?? undefined} />
        <Boton titulo="Pedir verificación" variante="suave" icono="send" onPress={solicitar} cargando={ocupado === 'placa'} deshabilitado={placa.trim().length < 5} />
        {aviso ? <Texto tipo="pequeno" color={t.ok}>{aviso}</Texto> : null}
      </Tarjeta>
    </Pantalla>
  );
}
