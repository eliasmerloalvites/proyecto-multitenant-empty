import { useQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { View } from 'react-native';

import { EstadoChip } from '@/components/estado';
import { Boton, Cargando, Chip, ErrorCarga, Heroe, Icono, Pantalla, Placa, Tarjeta, Texto, Vacio } from '@/components/ui';
import { api } from '@/lib/api';
import { useAuth } from '@/lib/auth';
import { useRefrescarAlEnfocar } from '@/lib/enfoque';
import { fecha } from '@/lib/formato';
import { useTema } from '@/lib/tema';
import type { Cuenta, Garaje, OrdenResumen } from '@/lib/types';

export default function MiGaraje() {
  const t = useTema();
  const { taller } = useAuth();
  const q = useQuery({ queryKey: ['garaje'], queryFn: () => api.get<Garaje>('motos') });
  const perfil = useQuery({ queryKey: ['perfil'], queryFn: () => api.get<Cuenta>('perfil') });
  const activas = useQuery({ queryKey: ['ordenes-activas'], queryFn: () => api.get<OrdenResumen[]>('ordenes', { estado: 'activas', limite: 5 }) });
  useRefrescarAlEnfocar(q.refetch);
  useRefrescarAlEnfocar(activas.refetch);
  const g = q.data;
  const nombre = perfil.data?.nombre?.split(' ')[0];

  const refrescar = () => {
    void q.refetch();
    void activas.refetch();
  };

  return (
    <Pantalla
      refrescando={q.isRefetching}
      alRefrescar={refrescar}
      sinPadding
      cabecera={
        <Heroe>
          <Texto tipo='etiqueta' color='rgba(255,255,255,0.7)'>{taller?.taller.nombre}</Texto>
          <Texto tipo='display' color='#fff'>{nombre ? `Hola, ${nombre}` : 'Mi garaje'}</Texto>
          <Texto tipo='cuerpo' color='rgba(255,255,255,0.85)'>
            {g ? (g.motos.length === 0 ? 'Agrega tu primera moto' : `${g.motos.length} moto${g.motos.length === 1 ? '' : 's'} en tu garaje`) : ' '}
          </Texto>
        </Heroe>
      }>
      <View style={{ padding: 16, gap: 14 }}>
        {/* Lo más importante primero: motos que están ahora en el taller. */}
        {activas.data?.map((o) => (
          <Tarjeta key={o.ref} onPress={() => router.push({ pathname: '/orden/[ref]', params: { ref: o.ref } })} style={{ borderWidth: 1.5, borderColor: t.aviso + '55' }}>
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
              <Texto tipo='etiqueta' color={t.aviso}>Ahora en el taller</Texto>
              <EstadoChip estado={o.estado} texto={o.estado_texto} />
            </View>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
              <Placa valor={o.placa} />
              <Texto tipo='pequeno' style={{ flex: 1 }}>{o.tipo} · desde {fecha(o.fecha_ingreso)}</Texto>
              <Icono nombre='chevron-forward' color={t.suave} />
            </View>
          </Tarjeta>
        ))}

        {q.isLoading ? <Cargando /> : q.isError ? <ErrorCarga error={q.error} reintentar={() => void q.refetch()} /> : null}

        {g?.motos.map((m) => (
          <Tarjeta key={m.id} onPress={() => router.push({ pathname: '/moto/[id]', params: { id: String(m.id) } })} style={{ gap: 8 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
              <Placa valor={m.placa} />
              <View style={{ flex: 1 }}>
                <Texto tipo='subtitulo' lineas={1} style={{ fontSize: 15 }}>{m.descripcion ?? 'Mi moto'}</Texto>
                <Texto tipo='pequeno' lineas={1}>
                  {m.total_servicios === 0 ? 'Sin servicios' : `${m.total_servicios} servicio${m.total_servicios === 1 ? '' : 's'} · último ${fecha(m.ultimo_servicio?.fecha).replace(/ \d{4}$/, '')}`}
                  {m.ultimo_servicio?.km ? ` · ${m.ultimo_servicio.km} km` : ''}
                </Texto>
              </View>
              <Icono nombre='chevron-forward' tam={18} color={t.suave} />
            </View>

            {m.proximo_servicio || m.proximo_cambio_aceite ? (
              <View style={{ flexDirection: 'row', gap: 8, flexWrap: 'wrap' }}>
                {m.proximo_servicio ? <Chip texto={`Próx. servicio: ${m.proximo_servicio}`} tono='info' icono='notifications-outline' /> : null}
                {m.proximo_cambio_aceite ? <Chip texto={`Aceite: ${m.proximo_cambio_aceite}`} tono='info' icono='water-outline' /> : null}
              </View>
            ) : null}
          </Tarjeta>
        ))}

        {g?.solicitudes.map((s) => (
          <Tarjeta key={`s${s.id}`} style={{ borderStyle: 'dashed', borderWidth: 1.5, borderColor: t.borde, shadowOpacity: 0, elevation: 0 }}>
            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between' }}>
              <Placa valor={s.placa} />
              <Chip texto='El taller la está verificando' tono='aviso' icono='hourglass-outline' />
            </View>
          </Tarjeta>
        ))}

        {g && g.motos.length === 0 && g.solicitudes.length === 0 ? (
          <Vacio
            icono='bicycle-outline'
            titulo='Tu garaje está vacío'
            detalle='Si ya te atendieron con este celular, tus motos aparecerán solas. También puedes agregarla con un código del taller.'
          />
        ) : null}

        {g ? (
          <View style={{ gap: 10, marginTop: 4 }}>
            <Boton titulo='Agregar una moto' icono='add-circle-outline' variante='suave' onPress={() => router.push('/vincular')} />
            <Boton titulo='Buscar mis motos de nuevo' icono='refresh' variante='secundario' onPress={() => void api.post('motos/actualizar').then(() => q.refetch()).catch(() => {})} />
          </View>
        ) : null}
      </View>
    </Pantalla>
  );
}

