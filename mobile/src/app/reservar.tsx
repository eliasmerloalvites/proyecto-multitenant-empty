import { useQuery, useQueryClient } from '@tanstack/react-query';
import { router, useLocalSearchParams } from 'expo-router';
import { useEffect, useState, type ReactNode } from 'react';
import { Pressable, ScrollView, View } from 'react-native';

import { Boton, Campo, Cargando, ErrorCarga, Icono, Pantalla, Placa, Tarjeta, Texto, sombra } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { hoyMas } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { Garaje, Reserva, SedeReserva, Turno } from '@/lib/types';

const DIAS = ['DOM', 'LUN', 'MAR', 'MIÉ', 'JUE', 'VIE', 'SÁB'];
const MESES = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];

/** Moto → sede → día → horario. La bahía la asigna el taller; aquí no existe. */
export default function Reservar() {
  const t = useTema();
  const cache = useQueryClient();
  const params = useLocalSearchParams<{ moto?: string }>();

  const [motoId, setMotoId] = useState<number | null>(params.moto ? Number(params.moto) : null);
  const [sedeId, setSedeId] = useState<number | null>(null);
  const [dia, setDia] = useState<string>(hoyMas(1));
  const [turno, setTurno] = useState<number | null>(null);
  const [detalle, setDetalle] = useState('');
  const [enviando, setEnviando] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const garaje = useQuery({ queryKey: ['garaje'], queryFn: () => api.get<Garaje>('motos') });
  const sedes = useQuery({ queryKey: ['reservas-sedes'], queryFn: () => api.get<SedeReserva[]>('reservas/sedes') });

  useEffect(() => {
    if (sedeId === null && sedes.data?.length === 1) setSedeId(sedes.data[0].id);
  }, [sedes.data, sedeId]);

  useEffect(() => {
    if (motoId === null && garaje.data?.motos.length === 1) setMotoId(garaje.data.motos[0].id);
  }, [garaje.data, motoId]);

  const turnos = useQuery({
    queryKey: ['reservas-turnos', sedeId, dia],
    queryFn: () => api.get<Turno[]>('reservas/disponibilidad', { sede: sedeId!, fecha: dia }),
    enabled: sedeId !== null,
  });

  const dias = Array.from({ length: 14 }, (_, i) => hoyMas(i));
  const listo = motoId !== null && sedeId !== null && turno !== null;

  async function reservar() {
    setEnviando(true);
    setError(null);
    try {
      await api.post<Reserva>('reservas', { moto_id: motoId, sede: sedeId, turno, fecha: dia, detalle: detalle.trim() || undefined });
      await cache.invalidateQueries({ queryKey: ['reservas'] });
      router.back();
    } catch (e) {
      setError(e instanceof ApiError ? e.message : 'No pudimos crear la reserva.');
      if (e instanceof ApiError && (e.code === 'SIN_CUPO' || e.code === 'HORARIO_NO_DISPONIBLE')) {
        setTurno(null);
        void turnos.refetch();
      }
    } finally {
      setEnviando(false);
    }
  }

  if (garaje.isLoading || sedes.isLoading) return <Pantalla><Cargando /></Pantalla>;
  if (garaje.isError || sedes.isError) return <Pantalla><ErrorCarga error={garaje.error ?? sedes.error} reintentar={() => { void garaje.refetch(); void sedes.refetch(); }} /></Pantalla>;

  if (!garaje.data?.motos.length) {
    return (
      <Pantalla>
        <Tarjeta>
          <Texto tipo="subtitulo">Primero agrega tu moto</Texto>
          <Texto tipo="pequeno">Para reservar necesitamos saber qué moto llevarás.</Texto>
          <Boton titulo="Agregar una moto" onPress={() => router.replace('/vincular')} />
        </Tarjeta>
      </Pantalla>
    );
  }

  const seleccionado = turnos.data?.find((x) => x.id === turno);

  return (
    <Pantalla
      pie={
        <View style={{ padding: 16, gap: 10, backgroundColor: '#fff', borderTopWidth: 1, borderColor: t.borde }}>
          {listo ? (
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
              <Icono nombre="calendar-outline" tam={18} color={t.primario} />
              <Texto style={{ flex: 1, fontWeight: '600' }}>
                {fechaCorta(dia)} · {seleccionado?.hora ?? seleccionado?.nombre}
              </Texto>
            </View>
          ) : null}
          {error ? <Texto color={t.error}>{error}</Texto> : null}
          <Boton titulo="Confirmar cita" icono="checkmark-circle" onPress={reservar} cargando={enviando} deshabilitado={!listo} />
        </View>
      }>
      <Paso n={1} titulo="¿Qué moto llevas?">
        {garaje.data.motos.map((m) => (
          <Opcion key={m.id} activo={motoId === m.id} onPress={() => setMotoId(m.id)}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
              <Placa valor={m.placa} tam="sm" />
              <Texto tipo="pequeno" style={{ flex: 1 }}>{m.descripcion ?? 'Moto'}</Texto>
            </View>
          </Opcion>
        ))}
      </Paso>

      {(sedes.data?.length ?? 0) > 1 ? (
        <Paso n={2} titulo="¿En qué sede?">
          {sedes.data!.map((s) => (
            <Opcion key={s.id} activo={sedeId === s.id} onPress={() => { setSedeId(s.id); setTurno(null); }}>
              <Texto tipo="subtitulo">{s.nombre}</Texto>
              {s.direccion ? <Texto tipo="pequeno">{s.direccion}</Texto> : null}
            </Opcion>
          ))}
        </Paso>
      ) : null}

      <Paso n={3} titulo="¿Qué día?">
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={{ gap: 8, paddingVertical: 2 }}>
          {dias.map((d, i) => {
            const f = new Date(d + 'T12:00:00');
            const activo = dia === d;
            return (
              <Pressable
                key={d}
                onPress={() => { setDia(d); setTurno(null); }}
                accessibilityRole="button"
                accessibilityState={{ selected: activo }}
                style={[{ width: 62, paddingVertical: 12, borderRadius: 16, alignItems: 'center', gap: 2, backgroundColor: activo ? t.primario : '#fff' }, !activo && sombra]}>
                <Texto tipo="etiqueta" color={activo ? t.sobrePrimario : t.suave}>{i === 0 ? 'HOY' : DIAS[f.getDay()]}</Texto>
                <Texto tipo="titulo" color={activo ? t.sobrePrimario : t.texto}>{f.getDate()}</Texto>
                <Texto tipo="pequeno" color={activo ? t.sobrePrimario : t.suave}>{MESES[f.getMonth()]}</Texto>
              </Pressable>
            );
          })}
        </ScrollView>
      </Paso>

      <Paso n={4} titulo="¿A qué hora?">
        {sedeId === null ? <Texto tipo="pequeno">Elige una sede para ver los horarios.</Texto> : null}
        {turnos.isLoading ? <Cargando /> : null}
        {turnos.isError ? <ErrorCarga error={turnos.error} reintentar={() => void turnos.refetch()} /> : null}
        {turnos.data?.length === 0 ? <Texto tipo="pequeno">El taller no atiende ese día. Prueba con otra fecha.</Texto> : null}
        <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: 10 }}>
          {turnos.data?.map((tu) => {
            const activo = turno === tu.id;
            const lleno = tu.libres === 0;
            return (
              <Pressable
                key={tu.id}
                disabled={lleno}
                onPress={() => setTurno(tu.id)}
                accessibilityRole="button"
                accessibilityState={{ selected: activo, disabled: lleno }}
                style={[
                  { minWidth: 104, paddingVertical: 12, paddingHorizontal: 14, borderRadius: 14, alignItems: 'center', gap: 2, borderWidth: 1.5, borderColor: activo ? t.primario : t.borde, backgroundColor: activo ? t.primarioClaro : '#fff', opacity: lleno ? 0.4 : 1 },
                ]}>
                <Texto tipo="subtitulo" color={activo ? t.primarioOscuro : t.texto}>{tu.hora ?? tu.nombre}</Texto>
                <Texto tipo="pequeno" color={lleno ? t.error : activo ? t.primario : t.suave}>{lleno ? 'Sin cupo' : `${tu.libres} libre${tu.libres === 1 ? '' : 's'}`}</Texto>
              </Pressable>
            );
          })}
        </View>
      </Paso>

      <Campo etiqueta="¿Qué necesita tu moto? (opcional)" value={detalle} onChangeText={setDetalle} maxLength={250} multiline placeholder="Ej. cambio de aceite, ruido en el motor" />
    </Pantalla>
  );
}

function fechaCorta(d: string) {
  const f = new Date(d + 'T12:00:00');
  return `${DIAS[f.getDay()]} ${f.getDate()} de ${MESES[f.getMonth()]}`;
}

function Paso({ n, titulo, children }: { n: number; titulo: string; children: ReactNode }) {
  const t = useTema();
  return (
    <View style={{ gap: 10 }}>
      <View style={{ flexDirection: 'row', alignItems: 'center', gap: 10 }}>
        <View style={{ width: 24, height: 24, borderRadius: 12, backgroundColor: t.primario, alignItems: 'center', justifyContent: 'center' }}>
          <Texto tipo="pequeno" color={t.sobrePrimario} style={{ fontWeight: '800' }}>{n}</Texto>
        </View>
        <Texto tipo="subtitulo">{titulo}</Texto>
      </View>
      {children}
    </View>
  );
}

function Opcion({ activo, onPress, children }: { activo: boolean; onPress: () => void; children: ReactNode }) {
  const t = useTema();
  return (
    <Pressable
      onPress={onPress}
      accessibilityRole="button"
      accessibilityState={{ selected: activo }}
      style={{ padding: 14, borderRadius: 16, gap: 4, borderWidth: 1.5, borderColor: activo ? t.primario : t.borde, backgroundColor: activo ? t.primarioClaro : '#fff' }}>
      {children}
    </Pressable>
  );
}
