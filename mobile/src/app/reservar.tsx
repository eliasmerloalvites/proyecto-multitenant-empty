import { useQuery, useQueryClient } from '@tanstack/react-query';
import { router, useLocalSearchParams } from 'expo-router';
import { useEffect, useState, type ReactNode } from 'react';
import { FlatList, Modal, Pressable, ScrollView, TextInput, View } from 'react-native';

import { Boton, Campo, Cargando, ErrorCarga, Icono, Pantalla, Placa, Tarjeta, Texto, sombra } from '@/components/ui';
import { api, ApiError } from '@/lib/api';
import { hoyMas } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { Garaje, MarcaVehiculo, Reserva, SedeReserva, Turno } from '@/lib/types';

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
  // undefined = no eligio (el taller usa la ultima marca de esa placa);
  // null = "no esta en la lista"; numero = marca elegida.
  const [marca, setMarca] = useState<MarcaVehiculo | null | undefined>(undefined);
  const [eligiendoMarca, setEligiendoMarca] = useState(false);
  const [enviando, setEnviando] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const garaje = useQuery({ queryKey: ['garaje'], queryFn: () => api.get<Garaje>('motos') });
  const sedes = useQuery({ queryKey: ['reservas-sedes'], queryFn: () => api.get<SedeReserva[]>('reservas/sedes') });
  const marcas = useQuery({ queryKey: ['reservas-marcas'], queryFn: () => api.get<MarcaVehiculo[]>('reservas/marcas'), staleTime: 60 * 60_000 });

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
      await api.post<Reserva>('reservas', {
        moto_id: motoId, sede: sedeId, turno, fecha: dia, detalle: detalle.trim() || undefined,
        ...(marca === undefined ? {} : { marca: marca?.id ?? null }),
      });
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

      {(marcas.data?.length ?? 0) > 0 ? (
        <View style={{ gap: 8 }}>
          <Texto tipo="etiqueta">Marca de la moto (opcional)</Texto>
          <Pressable
            onPress={() => setEligiendoMarca(true)}
            accessibilityRole="button"
            accessibilityLabel="Elegir la marca de la moto"
            style={{ flexDirection: 'row', alignItems: 'center', gap: 10, padding: 14, borderRadius: 16, borderWidth: 1.5, borderColor: marca ? t.primario : t.borde, backgroundColor: marca ? t.primarioClaro : '#fff' }}>
            <Icono nombre="pricetag-outline" tam={18} color={marca ? t.primario : t.suave} />
            <Texto style={{ flex: 1, fontWeight: marca ? '700' : '400' }} color={marca ? t.texto : t.suave}>
              {marca ? marca.nombre : marca === null ? 'No está en la lista' : 'Elegir marca'}
            </Texto>
            <Icono nombre="chevron-down" tam={18} color={t.suave} />
          </Pressable>
        </View>
      ) : null}

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

      <SelectorMarca
        visible={eligiendoMarca}
        marcas={marcas.data ?? []}
        onCerrar={() => setEligiendoMarca(false)}
        onElegir={(m) => { setMarca(m); setEligiendoMarca(false); }}
      />
    </Pantalla>
  );
}

/** Lista de marcas con buscador. Elegir "No está en la lista" guarda la reserva sin marca. */
function SelectorMarca({ visible, marcas, onCerrar, onElegir }: {
  visible: boolean;
  marcas: MarcaVehiculo[];
  onCerrar: () => void;
  onElegir: (m: MarcaVehiculo | null) => void;
}) {
  const t = useTema();
  const [buscar, setBuscar] = useState('');
  const q = buscar.trim().toLowerCase();
  const filtradas = q ? marcas.filter((m) => m.nombre.toLowerCase().includes(q)) : marcas;

  return (
    <Modal visible={visible} animationType="slide" onRequestClose={onCerrar}>
      <View style={{ flex: 1, backgroundColor: t.fondo, paddingTop: 48 }}>
        <View style={{ flexDirection: 'row', alignItems: 'center', paddingHorizontal: 20, marginBottom: 12 }}>
          <Texto tipo="titulo" style={{ flex: 1 }}>Marca de la moto</Texto>
          <Pressable onPress={onCerrar} accessibilityRole="button" accessibilityLabel="Cerrar" hitSlop={12}>
            <Icono nombre="close" tam={26} color={t.texto} />
          </Pressable>
        </View>
        <View style={{ marginHorizontal: 20, marginBottom: 8, flexDirection: 'row', alignItems: 'center', gap: 8, paddingHorizontal: 12, borderRadius: 14, borderWidth: 1, borderColor: t.borde, backgroundColor: '#fff' }}>
          <Icono nombre="search" tam={18} color={t.suave} />
          <TextInput
            value={buscar}
            onChangeText={setBuscar}
            placeholder="Buscar marca"
            placeholderTextColor={t.suave}
            autoCorrect={false}
            style={{ flex: 1, paddingVertical: 12, fontSize: 16, color: t.texto }}
            accessibilityLabel="Buscar marca"
          />
        </View>
        <FlatList
          data={filtradas}
          keyExtractor={(m) => String(m.id)}
          keyboardShouldPersistTaps="handled"
          contentContainerStyle={{ paddingHorizontal: 20, paddingBottom: 40 }}
          ListHeaderComponent={
            <Pressable onPress={() => onElegir(null)} accessibilityRole="button" style={{ paddingVertical: 14, borderBottomWidth: 1, borderColor: t.borde }}>
              <Texto color={t.suave}>No está en la lista / prefiero no indicarla</Texto>
            </Pressable>
          }
          ListEmptyComponent={<Texto tipo="pequeno" style={{ paddingVertical: 14 }}>No encontramos esa marca. Puedes elegir "No está en la lista".</Texto>}
          renderItem={({ item }) => (
            <Pressable onPress={() => onElegir(item)} accessibilityRole="button" style={{ paddingVertical: 14, borderBottomWidth: 1, borderColor: t.borde }}>
              <Texto style={{ fontWeight: '600' }}>{item.nombre}</Texto>
            </Pressable>
          )}
        />
      </View>
    </Modal>
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
