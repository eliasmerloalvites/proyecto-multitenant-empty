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
  /** El taller tiene tienda (catalogo y pedidos) en la app. */
  tienda?: boolean;
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
  /** null si el taller no usa firmas digitales (plan sin firmas). */
  firmas?: FirmasOrden | null;
};

export type FirmaRol = 'TECNICO' | 'REPRESENTANTE' | 'CLIENTE';

export type FirmasOrden = {
  roles: { rol: FirmaRol; firmado: boolean; nombre: string | null; fecha: string | null; desde_app: boolean }[];
  cerrado: boolean;
  puede_firmar: boolean;
  mensaje: string;
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

// ---- Tienda
export type Disponibilidad = 'DISPONIBLE' | 'ULTIMAS' | 'AGOTADO';

export type Producto = {
  id: number;
  nombre: string;
  marca: string | null;
  precio: string;
  imagen: string | null;
  categoria: { id: number; nombre: string };
  disponibilidad: Disponibilidad;
};

export type ProductoDetalle = Producto & { descripcion: string | null; fotos: string[]; maximo: number };

export type CategoriaTienda = { id: number; nombre: string; productos: number };
export type SedeTienda = { id: number; nombre: string; direccion: string | null };
export type InicioTienda = { categorias: CategoriaTienda[]; sedes: SedeTienda[]; sede_por_defecto: number | null };

export type EstadoPedido = 'PENDIENTE' | 'CONFIRMADO' | 'LISTO' | 'ENTREGADO' | 'CANCELADO';

export type PedidoResumen = {
  id: number;
  numero: string;
  estado: EstadoPedido;
  estado_texto: string;
  total: string;
  productos: number;
  fecha: string | null;
  sede: string | null;
};

export type PedidoDetalle = PedidoResumen & {
  sede_direccion: string | null;
  nota: string | null;
  motivo_cancelacion: string | null;
  cancelado_por: 'CLIENTE' | 'TALLER' | null;
  cancelable: boolean;
  pasos: { pedido: string | null; confirmado: string | null; listo: string | null; entregado: string | null; cancelado: string | null };
  items: { producto_id: number; nombre: string; precio: string; cantidad: number; subtotal: string; imagen: string | null }[];
};

/** Marca de vehiculo para elegir (opcional) al reservar. */
export type MarcaVehiculo = { id: number; nombre: string };
