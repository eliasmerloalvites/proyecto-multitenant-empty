import { useQuery, useQueryClient } from '@tanstack/react-query';
import { File, Paths } from 'expo-file-system';
import { useLocalSearchParams } from 'expo-router';
import * as Sharing from 'expo-sharing';
import { useState } from 'react';
import { Alert, Modal, Pressable, ScrollView, View } from 'react-native';

import { EstadoChip, PasosOrden } from '@/components/estado';
import { FirmaPad, puntosDe, type Trazos } from '@/components/firma-pad';
import { FotoSegura } from '@/components/foto-segura';
import { Boton, Cargando, Chip, ErrorCarga, Icono, Pantalla, Placa, Tarjeta, Texto } from '@/components/ui';
import { api, ApiError, cabeceras } from '@/lib/api';
import { fecha, soles } from '@/lib/formato';
import { sesion } from '@/lib/session';
import { useTema } from '@/lib/tema';
import type { FirmaRol, FirmasOrden, OrdenDetalle } from '@/lib/types';

export default function DetalleOrden() {
  const t = useTema();
  const { ref } = useLocalSearchParams<{ ref: string }>();
  const q = useQuery({ queryKey: ['orden', ref], queryFn: () => api.get<OrdenDetalle>(`ordenes/${ref}`) });
  const [foto, setFoto] = useState<{ url: string; clave: string } | null>(null);
  const [fallaFoto, setFallaFoto] = useState(false);
  const [descargando, setDescargando] = useState(false);
  const cache = useQueryClient();
  const [firmando, setFirmando] = useState(false);
  const [trazos, setTrazos] = useState<Trazos>([]);
  const [intento, setIntento] = useState(0);
  const [enviando, setEnviando] = useState(false);
  const [errorFirma, setErrorFirma] = useState<string | null>(null);

  function abrirFirma() {
    setTrazos([]);
    setIntento((n) => n + 1);
    setErrorFirma(null);
    setFirmando(true);
  }

  async function enviarFirma() {
    if (puntosDe(trazos) < 5) {
      setErrorFirma('Firma dentro del recuadro antes de enviar.');
      return;
    }
    setEnviando(true);
    setErrorFirma(null);
    try {
      const actualizada = await api.post<OrdenDetalle>(`ordenes/${ref}/firmar`, { trazos });
      cache.setQueryData(['orden', ref], actualizada);
      void cache.invalidateQueries({ queryKey: ['ordenes'] });
      setFirmando(false);
      Alert.alert('¡Gracias!', 'Tu firma de conformidad quedó registrada.');
    } catch (e) {
      setErrorFirma(e instanceof ApiError ? e.message : 'No se pudo enviar la firma. Inténtalo de nuevo.');
    } finally {
      setEnviando(false);
    }
  }

  async function descargarPdf(o: OrdenDetalle) {
    setDescargando(true);
    try {
      const destino = new File(Paths.cache, `${o.numero}.pdf`);
      const archivo = await File.downloadFileAsync(`${sesion.taller!.base_url}/api/v1/ordenes/${o.ref}/pdf`, destino, {
        headers: cabeceras(true),
        idempotent: true,
      });
      if (await Sharing.isAvailableAsync()) await Sharing.shareAsync(archivo.uri, { mimeType: 'application/pdf', dialogTitle: o.numero });
      else Alert.alert('PDF descargado', 'No hay una app para abrirlo en este dispositivo.');
    } catch {
      Alert.alert('No se pudo descargar', 'Inténtalo de nuevo en un momento.');
    } finally {
      setDescargando(false);
    }
  }

  if (q.isLoading) return <Pantalla><Cargando /></Pantalla>;
  if (q.isError || !q.data) return <Pantalla><ErrorCarga error={q.error} reintentar={() => void q.refetch()} /></Pantalla>;

  const o = q.data;
  const total = o.repuestos.reduce((s, r) => s + (r.precio ? Number(r.precio) : 0), 0);
  const hayPrecios = o.repuestos.some((r) => r.precio);

  return (
    <Pantalla refrescando={q.isRefetching} alRefrescar={() => void q.refetch()}>
      <Tarjeta style={{ gap: 14 }}>
        <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
          <Placa valor={o.placa} tam="lg" />
          <EstadoChip estado={o.estado} texto={o.estado_texto} />
        </View>
        <View>
          <Texto tipo="titulo">{o.tipo}</Texto>
          <Texto tipo="pequeno">{o.numero}{o.unidad ? ` · ${o.unidad}` : ''}{o.km ? ` · ${o.km} km` : ''}</Texto>
        </View>
        <View style={{ height: 1, backgroundColor: t.borde }} />
        <PasosOrden orden={o} />
      </Tarjeta>

      {o.solicitado ? <Bloque icono="chatbubble-ellipses-outline" titulo="Lo que pediste" texto={o.solicitado} /> : null}
      {o.realizado ? <Bloque icono="checkmark-done-outline" titulo="Trabajo realizado" texto={o.realizado} /> : null}

      {o.recomendacion ? (
        <Tarjeta style={{ backgroundColor: t.aviso + '12' }}>
          <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
            <Icono nombre="bulb-outline" tam={18} color={t.aviso} />
            <Texto tipo="etiqueta" color={t.aviso}>Recomendación del taller</Texto>
            {o.recomendacion_prioridad ? <Chip texto={o.recomendacion_prioridad} tono="aviso" /> : null}
          </View>
          <Texto>{o.recomendacion}</Texto>
        </Tarjeta>
      ) : null}

      {o.proximo_servicio || o.proximo_cambio_aceite ? (
        <Tarjeta style={{ gap: 10 }}>
          <Texto tipo="etiqueta">Lo que viene</Texto>
          {o.proximo_servicio ? <Fila icono="notifications-outline" titulo="Próximo servicio" valor={o.proximo_servicio} /> : null}
          {o.proximo_cambio_aceite ? <Fila icono="water-outline" titulo="Próximo cambio de aceite" valor={o.proximo_cambio_aceite} /> : null}
        </Tarjeta>
      ) : null}

      {o.repuestos.length > 0 ? (
        <Tarjeta style={{ gap: 10 }}>
          <Texto tipo="etiqueta">Repuestos usados</Texto>
          {o.repuestos.map((r, i) => (
            <View key={i} style={{ flexDirection: 'row', justifyContent: 'space-between', gap: 12 }}>
              <Texto style={{ flex: 1 }}>{r.descripcion}</Texto>
              {r.precio ? <Texto style={{ fontWeight: '700' }}>{soles(r.precio)}</Texto> : null}
            </View>
          ))}
          {hayPrecios ? (
            <View style={{ flexDirection: 'row', justifyContent: 'space-between', borderTopWidth: 1, borderColor: t.borde, paddingTop: 10 }}>
              <Texto tipo="subtitulo">Total repuestos</Texto>
              <Texto tipo="subtitulo">{soles(String(total))}</Texto>
            </View>
          ) : null}
        </Tarjeta>
      ) : null}

      {o.fotos.length > 0 ? (
        <Tarjeta style={{ gap: 10 }}>
          <Texto tipo="etiqueta">Fotos del trabajo</Texto>
          {fallaFoto ? <Texto tipo="pequeno" color={t.error}>No se pudieron cargar algunas fotos. Desliza hacia abajo para reintentar.</Texto> : null}
          <ScrollView horizontal contentContainerStyle={{ gap: 10 }} showsHorizontalScrollIndicator={false}>
            {o.fotos.map((f) => (
              <Pressable key={f.item} onPress={() => setFoto({ url: f.url, clave: `${o.ref}-${f.item}` })} accessibilityRole="imagebutton" accessibilityLabel={`Foto ${f.item}`}>
                <FotoSegura url={f.miniatura} clave={`${o.ref}-${f.item}-m`} alFallar={() => setFallaFoto(true)} style={{ width: 120, height: 120, borderRadius: 14 }} />
              </Pressable>
            ))}
          </ScrollView>
        </Tarjeta>
      ) : null}

      {o.firmas ? <SeccionFirmas firmas={o.firmas} alFirmar={abrirFirma} /> : null}

      {o.pdf_disponible ? <Boton titulo="Descargar orden en PDF" icono="document-text-outline" variante="suave" onPress={() => void descargarPdf(o)} cargando={descargando} /> : null}

      <Modal visible={firmando} animationType="slide" onRequestClose={() => setFirmando(false)}>
        <View style={{ flex: 1, backgroundColor: t.fondo, padding: 20, paddingTop: 48, gap: 14 }}>
          <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
            <Texto tipo="titulo">Firma de conformidad</Texto>
            <Pressable onPress={() => setFirmando(false)} accessibilityRole="button" accessibilityLabel="Cerrar" hitSlop={12}>
              <Icono nombre="close" tam={26} color={t.texto} />
            </Pressable>
          </View>
          <Texto tipo="pequeno">
            Con tu firma confirmas que revisaste el trabajo de la orden {o.numero} ({o.placa}) y estás conforme. Después de firmar, el taller ya no podrá modificar la orden.
          </Texto>
          <FirmaPad key={intento} trazos={trazos} onCambio={setTrazos} />
          {errorFirma ? <Texto color={t.error}>{errorFirma}</Texto> : null}
          <View style={{ flexDirection: 'row', gap: 10 }}>
            <View style={{ flex: 1 }}>
              <Boton titulo="Borrar" icono="refresh" variante="secundario" onPress={() => { setTrazos([]); setIntento((n) => n + 1); setErrorFirma(null); }} deshabilitado={enviando} />
            </View>
            <View style={{ flex: 1.4 }}>
              <Boton titulo="Enviar firma" icono="checkmark-circle" onPress={() => void enviarFirma()} cargando={enviando} />
            </View>
          </View>
        </View>
      </Modal>

      <Modal visible={!!foto} transparent animationType="fade" onRequestClose={() => setFoto(null)}>
        <Pressable onPress={() => setFoto(null)} style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.94)', justifyContent: 'center' }} accessibilityLabel="Cerrar foto">
          {foto ? <FotoSegura url={foto.url} clave={foto.clave} style={{ width: '100%', height: '80%' }} resizeMode="contain" /> : null}
        </Pressable>
      </Modal>
    </Pantalla>
  );
}

const NOMBRE_ROL: Record<FirmaRol, string> = { TECNICO: 'Técnico', REPRESENTANTE: 'Taller', CLIENTE: 'Tú (cliente)' };

function SeccionFirmas({ firmas, alFirmar }: { firmas: FirmasOrden; alFirmar: () => void }) {
  const t = useTema();
  return (
    <Tarjeta style={{ gap: 12, borderWidth: firmas.puede_firmar ? 1.5 : 0, borderColor: t.primario }}>
      <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
        <Icono nombre={firmas.cerrado ? 'shield-checkmark' : 'create-outline'} tam={18} color={firmas.cerrado ? t.ok : t.primario} />
        <Texto tipo="etiqueta">Firmas de la orden</Texto>
      </View>
      {firmas.roles.map((r) => (
        <View key={r.rol} style={{ flexDirection: 'row', alignItems: 'center', gap: 10 }}>
          <Icono nombre={r.firmado ? 'checkmark-circle' : 'ellipse-outline'} tam={20} color={r.firmado ? t.ok : t.suave} />
          <View style={{ flex: 1 }}>
            <Texto style={{ fontWeight: '600' }}>{NOMBRE_ROL[r.rol]}</Texto>
            <Texto tipo="pequeno">
              {r.firmado ? `${r.nombre ?? ''} · ${fecha(r.fecha)}${r.desde_app ? ' · desde la app' : ''}` : 'Pendiente'}
            </Texto>
          </View>
        </View>
      ))}
      <Texto tipo="pequeno">{firmas.mensaje}</Texto>
      {firmas.puede_firmar ? <Boton titulo="Firmar conformidad" icono="create-outline" onPress={alFirmar} /> : null}
    </Tarjeta>
  );
}

function Bloque({ icono, titulo, texto }: { icono: React.ComponentProps<typeof Icono>['nombre']; titulo: string; texto: string }) {
  const t = useTema();
  return (
    <Tarjeta style={{ gap: 8 }}>
      <View style={{ flexDirection: 'row', alignItems: 'center', gap: 8 }}>
        <Icono nombre={icono} tam={18} color={t.primario} />
        <Texto tipo="etiqueta">{titulo}</Texto>
      </View>
      <Texto>{texto}</Texto>
    </Tarjeta>
  );
}

function Fila({ icono, titulo, valor }: { icono: React.ComponentProps<typeof Icono>['nombre']; titulo: string; valor: string }) {
  const t = useTema();
  return (
    <View style={{ flexDirection: 'row', alignItems: 'center', gap: 12 }}>
      <View style={{ width: 36, height: 36, borderRadius: 18, backgroundColor: t.primarioClaro, alignItems: 'center', justifyContent: 'center' }}>
        <Icono nombre={icono} tam={18} color={t.primario} />
      </View>
      <View style={{ flex: 1 }}>
        <Texto tipo="pequeno">{titulo}</Texto>
        <Texto style={{ fontWeight: '700' }}>{valor}</Texto>
      </View>
    </View>
  );
}
