export type Tema = {
  modo: string;
  color_principal: string | null;
  color_secundario: string | null;
  fondo: string | null;
  tarjeta: string | null;
};

export type Sede = {
  id: number;
  nombre: string;
  direccion: string | null;
  referencia: string | null;
  telefono: string | null;
  latitud: string | null;
  longitud: string | null;
};

export type Taller = {
  nombre: string | null;
  logo: string | null;
  tema: Tema;
  contacto: { telefono: string | null; whatsapp: string | null; correo: string | null; web: string | null; direccion: string | null };
  sedes: Sede[];
};

export type TallerGuardado = { codigo: string; base_url: string; taller: Taller };

export type Cuenta = { id: number; celular: string; nombre: string | null; correo: string | null; verificada: boolean };

export type Moto = {
  id: number;
  placa: string;
  descripcion: string | null;
  vinculo: { metodo: string; alcance: string };
  total_servicios: number;
  ultimo_servicio: { fecha: string | null; tipo: string; km: string | null } | null;
  proximo_servicio: string | null;
  proximo_cambio_aceite: string | null;
};

export type SolicitudMoto = { id: number; placa: string; estado: string; solicitada_el: string | null };
export type Garaje = { motos: Moto[]; solicitudes: SolicitudMoto[] };

export type EstadoOrden = 'EN_TALLER' | 'TRABAJO_TERMINADO' | 'ENTREGADA' | 'SIN_CERRAR';

export type OrdenResumen = {
  ref: string;
  numero: string;
  tipo: string;
  moto_id: number;
  placa: string;
  estado: EstadoOrden;
  estado_texto: string;
  fecha_ingreso: string | null;
  fecha_termino: string | null;
  fecha_entrega: string | null;
  km: string | null;
};

export type OrdenDetalle = OrdenResumen & {
  unidad: string | null;
  proximo_servicio: string | null;
  proximo_cambio_aceite: string | null;
  solicitado: string | null;
  realizado: string | null;
  recomendacion: string | null;
  recomendacion_prioridad: string | null;
  repuestos: { descripcion: string; precio?: string }[];
  fotos: { item: number; url: string; miniatura: string }[];
  pdf_disponible: boolean;
};

export type Turno = { id: number; nombre: string; hora: string | null; libres: number };

export type Reserva = {
  id: number;
  estado: 'PENDIENTE' | 'CONFIRMADA' | 'EN_ATENCION' | 'CANCELADA' | 'RECHAZADA';
  fecha: string;
  turno: string | null;
  hora: string | null;
  sede: string | null;
  placa: string;
  moto: string;
  detalle: string | null;
  cancelable: boolean;
};

export type SedeReserva = { id: number; nombre: string; direccion: string | null; distrito: string | null };
