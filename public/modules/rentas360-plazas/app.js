const PANEL_CONFIG = window.RENTAS360_PANEL || {};
const PANEL_SLUG = PANEL_CONFIG.slug || "local-accounting";
const STORAGE_KEY = `rentas360-${PANEL_SLUG}-state-v1`;
const SESSION_KEY = `rentas360-${PANEL_SLUG}-session-v1`;
const SELECTED_PROPERTY_KEY = `rentas360-${PANEL_SLUG}-selected-property-v1`;
const PLAZA_DRAFT_KEY = `rentas360-${PANEL_SLUG}-plaza-draft-v1`;
const DEMO_PASSWORD = "Demo360!";
const PANEL_USER_ID = PANEL_CONFIG.userId || "u-conta-local-rosa";
const PANEL_ROLE_ID = PANEL_CONFIG.roleId || "local_accounting";
const PANEL_DEFAULT_TAB = PANEL_CONFIG.defaultTab || "local_accounting_dashboard";

const icons = {
  alertCircle: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>',
  activity: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>',
  building: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>',
  briefcase: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/><path d="M12 12h.01"/></svg>',
  checkCircle: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>',
  chevronLeft: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>',
  chevronRight: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>',
  creditCard: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
  download: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>',
  upload: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>',
  eye: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M2.06 12.35a1 1 0 0 1 0-.7A10.75 10.75 0 0 1 12 5a10.75 10.75 0 0 1 9.94 6.65 1 1 0 0 1 0 .7A10.75 10.75 0 0 1 12 19a10.75 10.75 0 0 1-9.94-6.65Z"/><circle cx="12" cy="12" r="3"/></svg>',
  fileText: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>',
  filter: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54Z"/></svg>',
  home: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
  layoutDashboard: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
  lock: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
  receipt: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M16 8h-6"/><path d="M16 12h-6"/><path d="M16 16h-6"/></svg>',
  scale: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>',
  search: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
  settings: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.73l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.09a2 2 0 0 1-1-1.73v-.51a2 2 0 0 1 1-1.72l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2Z"/><circle cx="12" cy="12" r="3"/></svg>',
  shield: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1Z"/></svg>',
  users: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
  x: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>'
};

const roles = [
  {
    id: "superadmin",
    name: "Superadministrador",
    description: "Acceso total a propiedades, unidades, pagos, contratos y configuracion."
  },
  {
    id: "admin",
    name: "Administrador",
    description: "Visualiza toda la operacion de cobro y asigna roles, permisos y accesos."
  },
  {
    id: "project_manager",
    name: "Gerente de Propiedad",
    description: "Administra la propiedad asignada y la relacion diaria con arrendatarios."
  },
  {
    id: "local_accounting",
    name: "Contabilidad local",
    description: "Registra ingresos y revisa facturas de una propiedad especifica."
  },
  {
    id: "general_accounting",
    name: "Contabilidad General",
    description: "Opera la contabilidad y conciliacion de todas las propiedades."
  },
  {
    id: "legal",
    name: "Legal",
    description: "Administra contratos de arrendamiento de todas las propiedades."
  },
  {
    id: "tenant",
    name: "Arrendatario",
    description: "Realiza pagos en plataforma y descarga facturas automaticamente."
  }
];

const roleNames = Object.fromEntries(roles.map((role) => [role.id, role.name]));

const paymentConcepts = [
  { key: "rent", label: "Renta 1" },
  { key: "extraordinary", label: "Renta 2" },
  { key: "maintenance", label: "Mantenimiento 1" },
  { key: "services", label: "Mantenimiento 2" },
  { key: "advertising", label: "Publicidad" }
];

const paymentTotalColumns = [
  { key: "rentTotal", label: "Renta Total" },
  { key: "maintenanceTotal", label: "Mantenimiento Total" }
];

const propertyPaymentColumns = [
  { key: "rent", label: "Renta 1", totalKey: "rentTotal" },
  { key: "extraordinary", label: "Renta 2", totalKey: "rentTotal" },
  { key: "maintenance", label: "Mantenimiento 1", totalKey: "maintenanceTotal" },
  { key: "services", label: "Mantenimiento 2", totalKey: "maintenanceTotal" }
];

const paymentMethodValidationConcepts = ["rent", "extraordinary", "maintenance", "services"];

const plazaCreationSections = [
  { id: "general", label: "Generales", icon: "home" },
  { id: "location", label: "Ubicacion", icon: "building" },
  { id: "operation", label: "Operacion", icon: "settings" },
  { id: "commercial", label: "Comercial", icon: "briefcase" },
  { id: "legal", label: "Legal", icon: "scale" },
  { id: "contacts", label: "Contactos", icon: "users" },
  { id: "observations", label: "Observaciones", icon: "fileText" }
];

const plazaCreationBooleanFields = [
  "commonAreas",
  "security24",
  "cctv",
  "controlledAccess",
  "emergencyPlant",
  "internet",
  "trashCollection",
  "cleaning",
  "preventiveMaintenance",
  "correctiveMaintenance",
  "marketplaceEnabled"
];

const operatingCostMonths = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
const operatingCostRows = [
  { label: "Sueldo Gerente de Propiedad", amounts: [42000, 42000, 42000, 42000, 42000, 42000, 42000, 42000, 42000, 42000, 42000, 42000] },
  { label: "Sueldo Contabilidad Local", amounts: [26000, 26000, 26000, 26000, 26000, 26000, 26000, 26000, 26000, 26000, 26000, 26000] },
  { label: "Seguridad", amounts: [18000, 18000, 18000, 18000, 19000, 19000, 19000, 19000, 19000, 19000, 19000, 19000] },
  { label: "Predial", amounts: [65000, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0] },
  { label: "Agua", amounts: [4200, 4300, 4500, 4700, 4900, 5200, 5400, 5300, 5000, 4700, 4400, 4200] },
  { label: "Luz", amounts: [8900, 9100, 9400, 9800, 10400, 11200, 11800, 11600, 10700, 9900, 9300, 9000] },
  { label: "Telefono e Internet", amounts: [1800, 1800, 1800, 1800, 1800, 1800, 1800, 1800, 1800, 1800, 1800, 1800] },
  { label: "Telefonia Movil", amounts: [1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200, 1200] },
  { label: "Mantenimiento", amounts: [12500, 12500, 13000, 13000, 13500, 13500, 14000, 14000, 13500, 13000, 13000, 12500] },
  { label: "Publicidad", amounts: [6000, 6000, 7000, 7000, 8000, 8000, 6500, 6500, 7000, 7000, 9000, 9000] },
  { label: "Otros Gastos", amounts: [3000, 2500, 3200, 2800, 3500, 3000, 3300, 3100, 2900, 3400, 3600, 4000] }
];

const DEMO_PLAZA_ARGENTINA_CONTRACT_START = "2025-06-01";
const DEMO_PLAZA_ARGENTINA_CONTRACT_END = "2026-08-30";
const DEMO_PLAZA_ARGENTINA_PROPERTY_IDS = ["prop-plaza-norte"];
const DEMO_PLAZA_ARGENTINA_AVAILABLE_UNIT_NUMBERS = [11, 12];
const DEMO_PLAZA_ARGENTINA_TENANTS = {
  3: { id: "tp-plaza-argentina-03", name: "Luna Dental", rfc: "LDE250603A10", phone: "81 1000 3003", contact: "Diana Luna" },
  4: { id: "tp-plaza-argentina-04", name: "Optica Central", rfc: "OCE250604B11", phone: "81 1000 3004", contact: "Marco Elias" },
  5: { id: "tp-plaza-argentina-05", name: "Mini Market Delta", rfc: "MMD250605C12", phone: "81 1000 3005", contact: "Laura Vega" },
  6: { id: "tp-plaza-argentina-06", name: "Estetica Urbana", rfc: "EUR250606D13", phone: "81 1000 3006", contact: "Nadia Ruiz" },
  7: { id: "tp-plaza-argentina-07", name: "Papeleria Atlas", rfc: "PAT250607E14", phone: "81 1000 3007", contact: "Sergio Cano" },
  8: { id: "tp-plaza-argentina-08", name: "Sushi Kaito", rfc: "SKA250608F15", phone: "81 1000 3008", contact: "Akira Mora" },
  9: { id: "tp-plaza-argentina-09", name: "Green Fit", rfc: "GFI250609G16", phone: "81 1000 3009", contact: "Valeria Solis" },
  10: { id: "tp-plaza-argentina-10", name: "Farma Plus", rfc: "FPL250610H17", phone: "81 1000 3010", contact: "Hector Paredes" }
};

const seedState = {
  properties: [
    {
      id: "prop-plaza-norte",
      name: "Plaza Andares Norte",
      type: "Plaza comercial",
      location: "Monterrey, N.L.",
      managerUserId: "u-gerente-ana",
      localAccountingUserId: "u-conta-local-rosa",
      marketplaceEnabled: false
    },
    {
      id: "prop-bodega-sur",
      name: "Bodegas Industriales Sur",
      type: "Bodega",
      location: "Santa Catarina, N.L.",
      managerUserId: "u-gerente-luis",
      localAccountingUserId: "u-conta-local-mario",
      marketplaceEnabled: false
    },
    {
      id: "prop-vivienda-bosque",
      name: "Residencial Vista Bosque",
      type: "Vivienda",
      location: "San Pedro Garza Garcia, N.L.",
      managerUserId: "u-gerente-ana",
      localAccountingUserId: "u-conta-local-rosa",
      marketplaceEnabled: false
    }
  ],
  organizations: [
    {
      id: "org-garza-patrimonial",
      name: "Garza Patrimonial",
      plan: "Enterprise",
      status: "Activa",
      adminUserId: "u-admin-carlos",
      propertyIds: ["prop-plaza-norte", "prop-vivienda-bosque"],
      monthlyFee: 18900,
      billingStatus: "Al corriente",
      renewalDate: "2026-12-31",
      supportLevel: "Prioritario",
      notes: "Cuenta principal con plazas comerciales y vivienda."
    },
    {
      id: "org-industrial-regia",
      name: "Industrial Regia",
      plan: "Pro",
      status: "Activa",
      adminUserId: "u-admin-carlos",
      propertyIds: ["prop-bodega-sur"],
      monthlyFee: 9900,
      billingStatus: "Por facturar",
      renewalDate: "2026-10-31",
      supportLevel: "Estandar",
      notes: "Cartera industrial con cobranza mensual."
    },
    {
      id: "org-demo-expansion",
      name: "Expansion demo",
      plan: "Piloto",
      status: "Implementacion",
      adminUserId: "u-super-admin",
      propertyIds: [],
      monthlyFee: 0,
      billingStatus: "Sin cargo",
      renewalDate: "2026-08-15",
      supportLevel: "Onboarding",
      notes: "Espacio listo para incorporar nuevas propiedades."
    }
  ],
  platformModules: [
    {
      id: "module-portfolio",
      name: "Cartera y propiedades",
      owner: "Superadministrador",
      status: "Activo",
      coverage: "Propiedades, unidades, responsables y ocupacion",
      risk: "Normal"
    },
    {
      id: "module-billing",
      name: "Cobranza y facturacion",
      owner: "Contabilidad General",
      status: "Activo",
      coverage: "Pagos, facturas, adeudos y reportes CSV",
      risk: "Atencion"
    },
    {
      id: "module-contracts",
      name: "Legal y contratos",
      owner: "Legal",
      status: "Activo",
      coverage: "Machotes, contratos firmados y vencimientos",
      risk: "Atencion"
    },
    {
      id: "module-access",
      name: "Usuarios y permisos",
      owner: "Administrador",
      status: "Activo",
      coverage: "Roles, usuarios demo, propiedades asignadas",
      risk: "Normal"
    }
  ],
  users: [
    {
      id: "u-super-admin",
      name: "Sofia Alvarez",
      email: "sofia@rentas360.mx",
      username: "sofia.super",
      password: DEMO_PASSWORD,
      role: "superadmin",
      propertyIds: []
    },
    {
      id: "u-admin-carlos",
      name: "Carlos Medina",
      email: "carlos@rentas360.mx",
      username: "carlos.admin",
      password: DEMO_PASSWORD,
      role: "admin",
      propertyIds: []
    },
    {
      id: "u-gerente-ana",
      name: "Ana Torres",
      email: "ana@rentas360.mx",
      username: "ana.gerente",
      password: DEMO_PASSWORD,
      role: "project_manager",
      propertyIds: ["prop-plaza-norte", "prop-vivienda-bosque"]
    },
    {
      id: "u-gerente-luis",
      name: "Luis Romero",
      email: "luis@rentas360.mx",
      username: "luis.gerente",
      password: DEMO_PASSWORD,
      role: "project_manager",
      propertyIds: ["prop-bodega-sur"]
    },
    {
      id: "u-conta-local-rosa",
      name: "Rosa Ibarra",
      email: "rosa@rentas360.mx",
      username: "rosa.local",
      password: DEMO_PASSWORD,
      role: "local_accounting",
      propertyIds: ["prop-plaza-norte", "prop-vivienda-bosque"]
    },
    {
      id: "u-conta-local-mario",
      name: "Mario Vega",
      email: "mario@rentas360.mx",
      username: "mario.local",
      password: DEMO_PASSWORD,
      role: "local_accounting",
      propertyIds: ["prop-bodega-sur"]
    },
    {
      id: "u-conta-general",
      name: "Gabriela Peña",
      email: "gabriela@rentas360.mx",
      username: "gabriela.general",
      password: DEMO_PASSWORD,
      role: "general_accounting",
      propertyIds: []
    },
    {
      id: "u-legal",
      name: "Daniela Solis",
      email: "legal@rentas360.mx",
      username: "daniela.legal",
      password: DEMO_PASSWORD,
      role: "legal",
      propertyIds: []
    },
    {
      id: "tenant-aurora",
      name: "Cafe Aurora",
      email: "pagos@cafeaurora.mx",
      username: "cafe.aurora",
      password: DEMO_PASSWORD,
      role: "tenant",
      propertyIds: []
    },
    {
      id: "tenant-fitlab",
      name: "FitLab Studio",
      email: "admin@fitlab.mx",
      username: "fitlab.studio",
      password: DEMO_PASSWORD,
      role: "tenant",
      propertyIds: []
    },
    {
      id: "tenant-nova",
      name: "Nova Logistica",
      email: "rentas@novalogistica.mx",
      username: "nova.logistica",
      password: DEMO_PASSWORD,
      role: "tenant",
      propertyIds: []
    },
    {
      id: "tenant-casa",
      name: "Mariana Chavez",
      email: "mariana@email.com",
      username: "mariana.chavez",
      password: DEMO_PASSWORD,
      role: "tenant",
      propertyIds: []
    }
  ],
  tenantProfiles: [
    {
      id: "tp-aurora",
      userId: "tenant-aurora",
      name: "Cafe Aurora",
      type: "Persona moral",
      rfc: "CAU250101A1A",
      phone: "81 1100 2401",
      contact: "Elena Garza",
      status: "Activo",
      propertyIds: ["prop-plaza-norte"],
      notes: "Arrendatario con acceso activo a portal."
    },
    {
      id: "tp-fitlab",
      userId: "tenant-fitlab",
      name: "FitLab Studio",
      type: "Persona moral",
      rfc: "FST240815B2B",
      phone: "81 2100 7788",
      contact: "Ricardo Treviño",
      status: "Activo",
      propertyIds: ["prop-plaza-norte"],
      notes: "Factura mensual automatizada."
    },
    {
      id: "tp-nova",
      userId: "tenant-nova",
      name: "Nova Logistica",
      type: "Persona moral",
      rfc: "NLO231101C3C",
      phone: "81 3300 1290",
      contact: "Paola Rivera",
      status: "Activo",
      propertyIds: ["prop-bodega-sur"],
      notes: "Contrato industrial."
    },
    {
      id: "tp-mariana",
      userId: "tenant-casa",
      name: "Mariana Chavez",
      type: "Persona fisica",
      rfc: "CACM900701D4D",
      phone: "81 2210 4420",
      contact: "Mariana Chavez",
      status: "Activo",
      propertyIds: ["prop-vivienda-bosque"],
      notes: "Arrendatario residencial."
    },
    {
      id: "tp-almacenadora",
      userId: null,
      name: "Almacenadora Regia",
      type: "Persona moral",
      rfc: "ARE240501E5E",
      phone: "81 4500 8800",
      contact: "Jorge Villarreal",
      status: "Pendiente de portal",
      propertyIds: ["prop-bodega-sur"],
      notes: "Alta documental sin acceso de plataforma."
    },
    {
      id: "tp-hector",
      userId: null,
      name: "Hector Salinas",
      type: "Persona fisica",
      rfc: "SAHH880312F6F",
      phone: "81 1700 5511",
      contact: "Hector Salinas",
      status: "Pendiente de portal",
      propertyIds: ["prop-vivienda-bosque"],
      notes: "Alta documental sin acceso de plataforma."
    }
  ],
  units: [
    {
      id: "unit-and-l01",
      propertyId: "prop-plaza-norte",
      unit: "Local L-01",
      tenant: "Cafe Aurora",
      tenantUserId: "tenant-aurora",
      monthlyRent: 42000,
      extraordinary: 2500,
      services: 1800,
      maintenance: 3900,
      advertising: 1250,
      contractStart: DEMO_PLAZA_ARGENTINA_CONTRACT_START,
      contractEnd: DEMO_PLAZA_ARGENTINA_CONTRACT_END,
      templateContract: "Machote comercial v3",
      signedContract: "Contrato firmado CAF-AUR-L01.pdf",
      paymentStatus: {
        rent: "pending",
        extraordinary: "paid",
        services: "pending",
        maintenance: "paid",
        advertising: "paid"
      }
    },
    {
      id: "unit-and-l02",
      propertyId: "prop-plaza-norte",
      unit: "Local L-02",
      tenant: "FitLab Studio",
      tenantUserId: "tenant-fitlab",
      monthlyRent: 51500,
      extraordinary: 3000,
      services: 2100,
      maintenance: 4600,
      advertising: 1600,
      contractStart: DEMO_PLAZA_ARGENTINA_CONTRACT_START,
      contractEnd: DEMO_PLAZA_ARGENTINA_CONTRACT_END,
      templateContract: "Machote comercial v3",
      signedContract: "Contrato firmado FIT-L02.pdf",
      paymentStatus: {
        rent: "paid",
        extraordinary: "paid",
        services: "paid",
        maintenance: "paid",
        advertising: "paid"
      }
    },
    {
      id: "unit-and-l09",
      propertyId: "prop-plaza-norte",
      unit: "Local L-09",
      tenant: "Disponible",
      tenantUserId: null,
      monthlyRent: 38500,
      extraordinary: 0,
      services: 0,
      maintenance: 3400,
      advertising: 1100,
      contractStart: DEMO_PLAZA_ARGENTINA_CONTRACT_START,
      contractEnd: DEMO_PLAZA_ARGENTINA_CONTRACT_END,
      templateContract: "Machote comercial v3",
      signedContract: "Pendiente de firma",
      paymentStatus: {
        rent: "pending",
        extraordinary: "paid",
        services: "paid",
        maintenance: "pending",
        advertising: "pending"
      }
    },
    {
      id: "unit-bod-a12",
      propertyId: "prop-bodega-sur",
      unit: "Bodega A-12",
      tenant: "Nova Logistica",
      tenantUserId: "tenant-nova",
      monthlyRent: 78000,
      extraordinary: 4500,
      services: 3600,
      maintenance: 2200,
      advertising: 0,
      contractStart: "2023-11-01",
      contractEnd: "2026-10-31",
      templateContract: "Machote industrial v2",
      signedContract: "Contrato firmado NOV-A12.pdf",
      paymentStatus: {
        rent: "pending",
        extraordinary: "pending",
        services: "paid",
        maintenance: "paid",
        advertising: "paid"
      }
    },
    {
      id: "unit-bod-c04",
      propertyId: "prop-bodega-sur",
      unit: "Bodega C-04",
      tenant: "Almacenadora Regia",
      tenantUserId: null,
      monthlyRent: 69500,
      extraordinary: 3500,
      services: 3100,
      maintenance: 1900,
      advertising: 0,
      contractStart: "2024-05-01",
      contractEnd: "2028-04-30",
      templateContract: "Machote industrial v2",
      signedContract: "Contrato firmado ALM-C04.pdf",
      paymentStatus: {
        rent: "paid",
        extraordinary: "paid",
        services: "pending",
        maintenance: "paid",
        advertising: "paid"
      }
    },
    {
      id: "unit-res-204",
      propertyId: "prop-vivienda-bosque",
      unit: "Departamento 204",
      tenant: "Mariana Chavez",
      tenantUserId: "tenant-casa",
      monthlyRent: 24500,
      extraordinary: 0,
      services: 1600,
      maintenance: 2600,
      advertising: 0,
      contractStart: "2025-07-01",
      contractEnd: "2026-06-30",
      templateContract: "Machote vivienda v1",
      signedContract: "Contrato firmado CHA-204.pdf",
      paymentStatus: {
        rent: "pending",
        extraordinary: "paid",
        services: "pending",
        maintenance: "pending",
        advertising: "paid"
      }
    },
    {
      id: "unit-res-310",
      propertyId: "prop-vivienda-bosque",
      unit: "Departamento 310",
      tenant: "Hector Salinas",
      tenantUserId: null,
      monthlyRent: 26800,
      extraordinary: 0,
      services: 1450,
      maintenance: 2600,
      advertising: 0,
      contractStart: "2026-03-01",
      contractEnd: "2027-02-28",
      templateContract: "Machote vivienda v1",
      signedContract: "Contrato firmado SAL-310.pdf",
      paymentStatus: {
        rent: "paid",
        extraordinary: "paid",
        services: "paid",
        maintenance: "paid",
        advertising: "paid"
      }
    }
  ]
};

const tabs = [
  {
    id: "tenant_dashboard",
    label: "Inicio",
    icon: "layoutDashboard",
    roles: ["tenant"]
  },
  {
    id: "general_accounting_dashboard",
    label: "Resumen contable",
    icon: "layoutDashboard",
    roles: ["general_accounting"]
  },
  {
    id: "local_accounting_dashboard",
    label: "Contabilidad local",
    icon: "receipt",
    roles: ["local_accounting"]
  },
  {
    id: "contracts",
    label: "Contratos",
    icon: "scale",
    roles: ["admin", "project_manager", "legal", "tenant"]
  },
  {
    id: "units",
    label: "Unidades y rentas",
    icon: "building",
    roles: ["admin", "project_manager", "local_accounting", "general_accounting", "legal", "tenant"]
  },
  {
    id: "superadmin_dashboard",
    label: "Panel general",
    icon: "layoutDashboard",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "administration",
    label: "Administracion y Cobranza",
    icon: "settings",
    roles: ["superadmin"]
  },
  {
    id: "plaza_contracts",
    label: "Contratos",
    icon: "fileText",
    roles: ["superadmin"]
  },
  {
    id: "plaza_marketplace",
    label: "Marketplace",
    icon: "briefcase",
    roles: ["superadmin"]
  },
  {
    id: "properties",
    label: "Catalogo de unidades",
    icon: "home",
    roles: ["superadmin", "admin", "project_manager", "local_accounting", "general_accounting", "legal"]
  },
  {
    id: "property_detail",
    label: "Detalle de propiedad",
    icon: "home",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "property_balance",
    label: "Balance de propiedad",
    icon: "receipt",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "property_payment_method",
    label: "Metodo de pago",
    icon: "creditCard",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "property_advance_payments",
    label: "Registro de Anticipos",
    icon: "receipt",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "property_unit_status",
    label: "Estatus de unidad",
    icon: "receipt",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "property_legal_panel",
    label: "Panel Legal",
    icon: "scale",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "property_operating_costs",
    label: "Costos Operativos",
    icon: "receipt",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "property_tenants",
    label: "Catalogo de Arrendatarios",
    icon: "users",
    roles: ["superadmin"],
    hidden: true
  },
  {
    id: "user_new",
    label: "Alta de Usuarios",
    icon: "users",
    roles: ["superadmin"]
  },
  {
    id: "tenants",
    label: "Catalogo de Arrendatarios",
    icon: "users",
    roles: ["superadmin", "admin", "project_manager", "local_accounting", "general_accounting", "legal"]
  },
  {
    id: "tenant_new",
    label: "Alta arrendatario",
    icon: "shield",
    roles: ["superadmin", "admin", "project_manager"],
    navHiddenRoles: ["superadmin"]
  },
  {
    id: "invoices",
    label: "Pagos y facturas",
    icon: "receipt",
    roles: ["admin", "local_accounting", "general_accounting", "tenant"]
  },
  {
    id: "history",
    label: "Historial",
    icon: "fileText",
    roles: ["admin", "project_manager", "local_accounting", "general_accounting", "legal", "tenant"]
  },
  {
    id: "users",
    label: "Usuarios y accesos",
    icon: "users",
    roles: ["admin"]
  }
];

const state = loadState();

const configuredPanelUser = state.users.find((user) => user.id === PANEL_USER_ID);
if (configuredPanelUser && PANEL_CONFIG.displayName) {
  configuredPanelUser.name = PANEL_CONFIG.displayName;
}

const view = {
  loggedInUserId: null,
  roleId: PANEL_ROLE_ID,
  userId: PANEL_USER_ID,
  activeTab: PANEL_DEFAULT_TAB,
  search: "",
  propertyFilter: "all",
  statusFilter: "all",
  administrationPropertyId: loadSelectedPropertyId(),
  propertyAdministrationView: "units",
  legalReturnTab: "",
  propertyDetailId: "",
  propertyReturnTab: "",
  unitStatusId: "",
  tenantCatalogPropertyId: ""
};

const els = {
  loginView: document.querySelector("#loginView"),
  appShell: document.querySelector("#appShell"),
  loginForm: document.querySelector("#loginForm"),
  loginUserSelect: document.querySelector("#loginUserSelect"),
  loginUsername: document.querySelector("#loginUsername"),
  loginPassword: document.querySelector("#loginPassword"),
  loginError: document.querySelector("#loginError"),
  credentialsTable: document.querySelector("#credentialsTable"),
  logoutButton: document.querySelector("#logoutButton"),
  sessionSummary: document.querySelector("#sessionSummary"),
  roleSelect: document.querySelector("#roleSelect"),
  userSelect: document.querySelector("#userSelect"),
  navTabs: document.querySelector("#navTabs"),
  roleEyebrow: document.querySelector("#roleEyebrow"),
  viewTitle: document.querySelector("#viewTitle"),
  viewSubtitle: document.querySelector("#viewSubtitle"),
  scopeTitle: document.querySelector("#scopeTitle"),
  scopeDescription: document.querySelector("#scopeDescription"),
  currentMonth: document.querySelector("#currentMonth"),
  metricsGrid: document.querySelector("#metricsGrid"),
  searchInput: document.querySelector("#searchInput"),
  filtersBar: document.querySelector(".filters-bar"),
  propertyFilter: document.querySelector("#propertyFilter"),
  statusFilter: document.querySelector("#statusFilter"),
  contentArea: document.querySelector("#contentArea"),
  modalBackdrop: document.querySelector("#modalBackdrop"),
  modal: document.querySelector(".modal"),
  modalEyebrow: document.querySelector("#modalEyebrow"),
  modalTitle: document.querySelector("#modalTitle"),
  modalBody: document.querySelector("#modalBody"),
  modalClose: document.querySelector("#modalClose")
};

function loadState() {
  const saved = localStorage.getItem(STORAGE_KEY);
  if (!saved) return normalizeState(structuredClone(seedState));

  try {
    const parsed = JSON.parse(saved);
    const seed = structuredClone(seedState);
    return normalizeState({
      ...seed,
      ...parsed,
      properties: mergeById(seed.properties, parsed.properties),
      organizations: mergeById(seed.organizations, parsed.organizations),
      platformModules: mergeById(seed.platformModules, parsed.platformModules),
      users: mergeById(seed.users, parsed.users),
      tenantProfiles: mergeById(seed.tenantProfiles, parsed.tenantProfiles),
      units: mergeById(seed.units, parsed.units)
    });
  } catch {
    return normalizeState(structuredClone(seedState));
  }
}

function loadSelectedPropertyId() {
  try {
    return localStorage.getItem(SELECTED_PROPERTY_KEY) || "";
  } catch {
    return "";
  }
}

function rememberSelectedProperty(propertyId) {
  if (!propertyId || !state.properties.some((property) => property.id === propertyId)) return;
  view.administrationPropertyId = propertyId;

  try {
    localStorage.setItem(SELECTED_PROPERTY_KEY, propertyId);
  } catch {
    // Selection still works for the current page when storage is unavailable.
  }
}

function saveState() {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
}

function mergeById(seedItems, savedItems) {
  if (!Array.isArray(savedItems)) return seedItems;

  const savedById = new Map(savedItems.map((item) => [item.id, item]));
  const merged = seedItems.map((seedItem) => ({
    ...seedItem,
    ...(savedById.get(seedItem.id) || {})
  }));
  const seedIds = new Set(seedItems.map((item) => item.id));
  return merged.concat(savedItems.filter((item) => !seedIds.has(item.id)));
}

function normalizeState(nextState) {
  nextState.properties = (Array.isArray(nextState.properties) ? nextState.properties : []).map((property) => ({
    ...property,
    marketplaceEnabled: Boolean(property.marketplaceEnabled)
  }));
  nextState.organizations = Array.isArray(nextState.organizations) ? nextState.organizations : [];
  nextState.platformModules = Array.isArray(nextState.platformModules) ? nextState.platformModules : [];
  nextState.users = normalizeUsers(nextState.users || []);
  ensureDedicatedPanelUser(nextState);
  nextState.units = (nextState.units || []).map(normalizeUnitPayments);
  applyPlazaArgentinaDemoContractDates(nextState);
  applyPlazaArgentinaDemoTenants(nextState);
  syncExpiredContractValidations(nextState.units);
  nextState.tenantProfiles = normalizeTenantProfiles(nextState);
  return nextState;
}

function plazaArgentinaDemoPropertyIds(nextState) {
  const plazaArgentinaPropertyIds = new Set(DEMO_PLAZA_ARGENTINA_PROPERTY_IDS);
  (nextState.properties || []).forEach((property) => {
    if (normalizeText(property.name) === "plaza argentina") {
      plazaArgentinaPropertyIds.add(property.id);
    }
  });
  return plazaArgentinaPropertyIds;
}

function applyPlazaArgentinaDemoContractDates(nextState) {
  const plazaArgentinaPropertyIds = plazaArgentinaDemoPropertyIds(nextState);
  nextState.units = (nextState.units || []).map((unit) => {
    if (!plazaArgentinaPropertyIds.has(unit.propertyId)) return unit;
    const hasManualContractDates = Boolean(unit.contractTermManual) || (
      unit.contractStart &&
      unit.contractEnd &&
      (unit.contractStart !== DEMO_PLAZA_ARGENTINA_CONTRACT_START || unit.contractEnd !== DEMO_PLAZA_ARGENTINA_CONTRACT_END)
    );

    if (hasManualContractDates) {
      return {
        ...unit,
        contractTermManual: true
      };
    }

    return {
      ...unit,
      contractStart: DEMO_PLAZA_ARGENTINA_CONTRACT_START,
      contractEnd: DEMO_PLAZA_ARGENTINA_CONTRACT_END
    };
  });
}

function applyPlazaArgentinaDemoTenants(nextState) {
  const plazaArgentinaPropertyIds = plazaArgentinaDemoPropertyIds(nextState);
  const availableUnitNumbers = new Set(DEMO_PLAZA_ARGENTINA_AVAILABLE_UNIT_NUMBERS);
  const tenantEntries = Object.entries(DEMO_PLAZA_ARGENTINA_TENANTS).map(([unitNumber, tenant]) => [Number(unitNumber), tenant]);
  const tenantByUnitNumber = new Map(tenantEntries);

  nextState.units = (nextState.units || []).map((unit) => {
    if (!plazaArgentinaPropertyIds.has(unit.propertyId)) return unit;
    if (unit.tenantAssignmentManual) return unit;

    const unitNumber = unitSortNumber(unit);
    if (availableUnitNumbers.has(unitNumber)) {
      return {
        ...unit,
        tenant: "Disponible",
        tenantUserId: null,
        tenantProfileId: null
      };
    }

    const tenant = tenantByUnitNumber.get(unitNumber);
    if (!tenant) return unit;

    return {
      ...unit,
      tenant: tenant.name,
      tenantUserId: null,
      tenantProfileId: tenant.id
    };
  });

  const profilesById = new Map((nextState.tenantProfiles || []).map((profile) => [profile.id, profile]));
  tenantEntries.forEach(([, tenant]) => {
    profilesById.set(tenant.id, {
      ...(profilesById.get(tenant.id) || {}),
      id: tenant.id,
      userId: null,
      name: tenant.name,
      type: "Persona moral",
      rfc: tenant.rfc,
      phone: tenant.phone,
      contact: tenant.contact,
      status: "Activo",
      propertyIds: Array.from(plazaArgentinaPropertyIds),
      notes: "Arrendatario demo asignado a Plaza Argentina."
    });
  });
  nextState.tenantProfiles = Array.from(profilesById.values());
}

function ensureDedicatedPanelUser(nextState) {
  if (PANEL_ROLE_ID === "local_accounting") {
    const localAssignments = {
      "u-conta-local-rosa": ["prop-plaza-norte", "prop-vivienda-bosque"],
      "u-conta-local-mario": ["prop-bodega-sur"]
    };

    Object.entries(localAssignments).forEach(([userId, propertyIds]) => {
      const accountingUser = nextState.users.find((user) => user.id === userId);
      if (!accountingUser) return;

      accountingUser.role = PANEL_ROLE_ID;
      accountingUser.propertyIds = propertyIds;
      accountingUser.username = userId === PANEL_USER_ID && PANEL_CONFIG.username ? PANEL_CONFIG.username : accountingUser.username;
      accountingUser.password = DEMO_PASSWORD;
    });
    return;
  }

  if (PANEL_ROLE_ID !== "tenant") {
    const panelUser = nextState.users.find((user) => user.id === PANEL_USER_ID);
    if (!panelUser) return;

    panelUser.role = PANEL_ROLE_ID;
    panelUser.propertyIds = [];
    panelUser.username = PANEL_CONFIG.username || panelUser.username;
    panelUser.password = DEMO_PASSWORD;
    return;
  }

  const tenantUserIds = new Set((nextState.units || [])
    .map((unit) => unit.tenantUserId)
    .filter(Boolean));

  tenantUserIds.forEach((userId) => {
    const tenantUser = nextState.users.find((user) => user.id === userId);
    if (!tenantUser) return;

    tenantUser.role = PANEL_ROLE_ID;
    tenantUser.propertyIds = [];
    tenantUser.username = tenantUser.id === PANEL_USER_ID && PANEL_CONFIG.username ? PANEL_CONFIG.username : tenantUser.username;
    tenantUser.password = DEMO_PASSWORD;
  });
}

function normalizeUsers(users) {
  return users.map((user) => ({
    ...user,
    username: user.username || defaultUsername(user),
    password: DEMO_PASSWORD
  }));
}

function defaultUsername(user) {
  return slugify(user.name || user.email || user.id).replace(/-/g, ".") || user.id;
}

function defaultPassword(user) {
  return DEMO_PASSWORD;
}

function normalizeTenantProfiles(nextState) {
  const profiles = Array.isArray(nextState.tenantProfiles) ? nextState.tenantProfiles : [];
  const profilesByUserId = new Set(profiles.map((profile) => profile.userId).filter(Boolean));
  const profilesByName = new Set(profiles.map((profile) => normalizeText(profile.name)));
  const generated = [];

  (nextState.users || [])
    .filter((user) => user.role === "tenant" && !profilesByUserId.has(user.id))
    .forEach((user) => {
      generated.push({
        id: `tp-${user.id}`,
        userId: user.id,
        name: user.name,
        type: "Sin clasificar",
        rfc: "",
        phone: "",
        contact: user.name,
        status: "Activo",
        propertyIds: tenantPropertyIds(user.id, user.name, nextState.units),
        notes: "Perfil generado desde usuario de plataforma."
      });
    });

  (nextState.units || [])
    .filter((unit) => unit.tenant && unit.tenant !== "Disponible" && !unit.tenantUserId && !profilesByName.has(normalizeText(unit.tenant)))
    .forEach((unit) => {
      profilesByName.add(normalizeText(unit.tenant));
      generated.push({
        id: `tp-${slugify(unit.tenant)}`,
        userId: null,
        name: unit.tenant,
        type: "Sin clasificar",
        rfc: "",
        phone: "",
        contact: unit.tenant,
        status: "Pendiente de portal",
        propertyIds: [unit.propertyId],
        notes: "Perfil generado desde unidad rentada."
      });
    });

  return profiles.concat(generated).map((profile, index) => {
    const linkedUser = (nextState.users || []).find((user) => user.id === profile.userId);
    return {
      id: profile.id || `tp-${index + 1}`,
      userId: profile.userId || null,
      name: profile.name || linkedUser?.name || "Sin nombre",
      type: profile.type || "Sin clasificar",
      rfc: profile.rfc || "",
      phone: profile.phone || "",
      email: profile.email || linkedUser?.email || "",
      contact: profile.contact || profile.name || linkedUser?.name || "",
      fiscalAddress: profile.fiscalAddress || "",
      legalRepresentative: profile.legalRepresentative || "",
      bankName: profile.bankName || "",
      bankAccount: profile.bankAccount || "",
      bankClabe: profile.bankClabe || "",
      paymentReference: profile.paymentReference || "",
      status: profile.status || (profile.userId ? "Activo" : "Pendiente de portal"),
      propertyIds: Array.isArray(profile.propertyIds) ? profile.propertyIds : [],
      notes: profile.notes || ""
    };
  });
}

function tenantPropertyIds(userId, tenantName, units = state.units) {
  return [...new Set((units || [])
    .filter((unit) => unit.tenantUserId === userId || normalizeText(unit.tenant) === normalizeText(tenantName))
    .map((unit) => unit.propertyId)
    .filter(Boolean))];
}

function normalizeUnitPayments(unit) {
  const normalized = {
    ...unit,
    rentTotal: Number(unit.rentTotal ?? unit.monthlyRent ?? 0),
    maintenanceTotal: Number(unit.maintenanceTotal ?? unit.maintenance ?? 0),
    rentPart1: Number(unit.rentPart1 ?? unit.monthlyRent ?? 0),
    rentPart2: Number(unit.rentPart2 ?? unit.extraordinary ?? 0),
    maintenancePart1: Number(unit.maintenancePart1 ?? unit.maintenance ?? 0),
    maintenancePart2: Number(unit.maintenancePart2 ?? unit.services ?? 0),
    marketplaceEnabled: typeof unit.marketplaceEnabled === "boolean" ? unit.marketplaceEnabled : true,
    contractTermValidated: typeof unit.contractTermValidated === "boolean" ? unit.contractTermValidated : undefined,
    paymentStatus: normalizePaymentStatus(unit.paymentStatus, unit)
  };
  const generatedLedger = generatePaymentLedger(normalized);
  const savedLedger = unit.paymentLedger || {};

  normalized.paymentLedger = {
    ...generatedLedger,
    ...savedLedger
  };

  Object.keys(normalized.paymentLedger).forEach((monthKey) => {
    normalized.paymentLedger[monthKey] = normalizePaymentStatus(normalized.paymentLedger[monthKey], normalized);
  });

  normalized.paymentStatus = normalizePaymentStatus(normalized.paymentLedger[currentMonthKey()], normalized);
  return normalized;
}

function normalizePaymentStatus(status = {}, unit = {}) {
  return paymentConcepts.reduce((result, concept) => {
    result[concept.key] = conceptAmount(unit, concept.key) === 0 ? "paid" : status[concept.key] === "pending" ? "pending" : "paid";
    return result;
  }, {});
}

function conceptAmount(unit, conceptKey) {
  return conceptAmountForMonth(unit, conceptKey);
}

function conceptAmountForMonth(unit, conceptKey, monthKey = null) {
  const recordAmount = monthKey ? paymentRecord(unit, monthKey, conceptKey).amount : null;
  if (recordAmount !== null && recordAmount !== undefined && recordAmount !== "") {
    return Number(recordAmount) || 0;
  }

  if (conceptKey === "rent") return Number(unit.rentPart1 ?? unit.monthlyRent ?? 0);
  if (conceptKey === "extraordinary") return Number(unit.rentPart2 ?? unit.extraordinary ?? 0);
  if (conceptKey === "maintenance") return Number(unit.maintenancePart1 ?? unit.maintenance ?? 0);
  if (conceptKey === "services") return Number(unit.maintenancePart2 ?? unit.services ?? 0);
  return Number(unit[conceptKey] || 0);
}

function paymentTotalAmount(unit, totalKey) {
  if (totalKey === "rentTotal") return Number(unit.rentTotal ?? unit.monthlyRent ?? 0);
  if (totalKey === "maintenanceTotal") return Number(unit.maintenanceTotal ?? unit.maintenance ?? 0);
  return 0;
}

function buildUnitLabel(unitName, unitNumber) {
  return [unitName, unitNumber].map((value) => String(value || "").trim()).filter(Boolean).join(" ");
}

function unitIdentityParts(unit = {}) {
  unit = unit || {};
  const savedName = String(unit.unitName || "").trim();
  const savedNumber = String(unit.unitNumber || "").trim();
  if (savedName || savedNumber) {
    return { unitName: savedName, unitNumber: savedNumber };
  }

  const label = String(unit.unit || "").trim();
  if (!label) return { unitName: "", unitNumber: "" };
  const parts = label.split(/\s+/);
  if (parts.length === 1) return { unitName: label, unitNumber: "" };
  return {
    unitName: parts.slice(0, -1).join(" "),
    unitNumber: parts[parts.length - 1]
  };
}

function unitSortNumber(unit) {
  const { unitNumber } = unitIdentityParts(unit);
  const source = unitNumber || unit.unit || "";
  const match = String(source).match(/\d+/);
  return match ? Number(match[0]) : Number.MAX_SAFE_INTEGER;
}

function sortedUnitsByNumber(units) {
  return [...units].sort((first, second) => {
    const numberDiff = unitSortNumber(first) - unitSortNumber(second);
    if (numberDiff !== 0) return numberDiff;
    return String(first.unit || "").localeCompare(String(second.unit || ""), "es", { numeric: true });
  });
}

function propertyUnits(propertyId) {
  return sortedUnitsByNumber(state.units.filter((unit) => unit.propertyId === propertyId));
}

function generatePaymentLedger(unit) {
  const ledger = {};
  generatedLedgerMonthKeys().forEach((monthKey, index) => {
    ledger[monthKey] = index === 0 ? normalizePaymentStatus(unit.paymentStatus, unit) : normalizePaymentStatus({}, unit);
  });
  applySeedDelays(unit, ledger);
  return ledger;
}

function generatedLedgerMonthKeys() {
  return Array.from({ length: 15 }, (_, index) => monthKeyFromOffset(-index));
}

function applySeedDelays(unit, ledger) {
  const delays = {
    "unit-and-l01": [
      [1, "rent"],
      [2, "services"],
      [8, "rent"]
    ],
    "unit-and-l09": [
      [1, "rent"],
      [2, "rent"],
      [3, "maintenance"],
      [8, "advertising"]
    ],
    "unit-bod-a12": [
      [1, "rent"],
      [1, "extraordinary"],
      [2, "rent"],
      [7, "services"]
    ],
    "unit-bod-c04": [
      [4, "services"],
      [9, "maintenance"]
    ],
    "unit-res-204": [
      [1, "rent"],
      [1, "services"],
      [2, "services"],
      [3, "maintenance"],
      [10, "rent"]
    ]
  };

  (delays[unit.id] || []).forEach(([monthsBack, conceptKey]) => {
    const monthKey = monthKeyFromOffset(-monthsBack);
    if (ledger[monthKey] && conceptAmount(unit, conceptKey) > 0) {
      ledger[monthKey][conceptKey] = "pending";
    }
  });
}

function formatCurrency(value) {
  return new Intl.NumberFormat("es-MX", {
    style: "currency",
    currency: "MXN",
    maximumFractionDigits: 0
  }).format(value || 0);
}

function formatDate(value) {
  if (!value) return "Sin fecha";
  const [year, month, day] = value.split("-").map(Number);
  return new Intl.DateTimeFormat("es-MX", {
    day: "2-digit",
    month: "short",
    year: "numeric"
  }).format(new Date(year, month - 1, day));
}

function monthKeyFromDate(date) {
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}`;
}

function monthKeyFromOffset(offset) {
  const date = new Date();
  date.setDate(1);
  date.setMonth(date.getMonth() + offset);
  return monthKeyFromDate(date);
}

function currentMonthKey() {
  return monthKeyFromOffset(0);
}

function recentMonthKeys() {
  return Array.from({ length: 7 }, (_, index) => monthKeyFromOffset(-index));
}

function propertyBalanceMonthKeys() {
  return Array.from({ length: 12 }, (_, index) => monthKeyFromOffset(-(index + 1)));
}

function propertyBalanceDisplayMonthKeys() {
  return [currentMonthKey(), ...propertyBalanceMonthKeys()];
}

function previousMonthKey(monthKey = currentMonthKey()) {
  const date = monthKeyToDate(monthKey);
  date.setMonth(date.getMonth() - 1);
  return monthKeyFromDate(date);
}

function isUnitBillableForMonth(unit, monthKey) {
  if (!unit || isUnitAvailable(unit)) return false;

  const startMonth = unit.contractStart ? monthKeyFromDate(new Date(`${unit.contractStart}T00:00:00`)) : "";
  const endMonth = unit.contractEnd ? monthKeyFromDate(new Date(`${unit.contractEnd}T00:00:00`)) : "";

  if (startMonth && monthKey < startMonth) return false;
  if (endMonth && monthKey > endMonth) return false;
  return true;
}

function ensureMonthlyPaymentRollover(units, monthKey = currentMonthKey()) {
  const previousKey = previousMonthKey(monthKey);
  let changed = false;

  units.forEach((unit) => {
    changed = ensureUnitMonthlyPaymentRollover(unit, monthKey, previousKey) || changed;
  });

  if (changed) saveState();
  return changed;
}

function ensureUnitMonthlyPaymentRollover(unit, monthKey, previousKey) {
  if (!unit) return false;

  let changed = false;
  const isBillable = isUnitBillableForMonth(unit, monthKey);

  if (!unit.paymentLedger) {
    unit.paymentLedger = {};
    changed = true;
  }
  if (!unit.paymentLedger[monthKey]) {
    unit.paymentLedger[monthKey] = normalizePaymentStatus({}, unit);
    changed = true;
  }
  if (!unit.paymentRecords) {
    unit.paymentRecords = {};
    changed = true;
  }
  if (!unit.paymentRecords[monthKey]) {
    unit.paymentRecords[monthKey] = {};
    changed = true;
  }

  paymentMethodValidationConcepts.forEach((conceptKey) => {
    let record = unit.paymentRecords[monthKey][conceptKey];
    const createdRecord = !record;
    if (!record) {
      record = {};
      unit.paymentRecords[monthKey][conceptKey] = record;
      changed = true;
    }

    if (record.amount === undefined || record.amount === null || record.amount === "") {
      const previousRecord = paymentRecord(unit, previousKey, conceptKey);
      const previousAmount = previousRecord.amount !== undefined && previousRecord.amount !== null && previousRecord.amount !== ""
        ? Number(previousRecord.amount)
        : conceptAmountForMonth(unit, conceptKey, previousKey);

      record.amount = Number(previousAmount || 0);
      record.rolloverSourceMonth = previousKey;
      record.rolloverGeneratedAt = new Date().toISOString();
      changed = true;
    }

    const amount = Number(record.amount || 0);
    const currentStatus = getPaymentStatus(unit, monthKey, conceptKey);
    let nextStatus = currentStatus;

    if (!isBillable || amount <= 0 || record.validated) {
      nextStatus = "paid";
    } else if (createdRecord || record.rolloverSourceMonth === previousKey || currentStatus !== "paid") {
      nextStatus = "pending";
    }

    if (currentStatus !== nextStatus) {
      setPaymentStatus(unit, monthKey, conceptKey, nextStatus);
      changed = true;
    }
  });

  return changed;
}

function monthsBackFromCurrentMonth(monthKey) {
  const currentDate = monthKeyToDate(currentMonthKey());
  const targetDate = monthKeyToDate(monthKey);
  return (currentDate.getFullYear() - targetDate.getFullYear()) * 12 + currentDate.getMonth() - targetDate.getMonth();
}

function sortMonthKeysDesc(monthKeys) {
  return [...monthKeys].sort((a, b) => b.localeCompare(a));
}

function monthKeyToDate(monthKey) {
  const [year, month] = monthKey.split("-").map(Number);
  return new Date(year, month - 1, 1);
}

function formatMonthLabel(monthKey) {
  return new Intl.DateTimeFormat("es-MX", {
    month: "long",
    year: "numeric"
  }).format(monthKeyToDate(monthKey));
}

function formatMonthShort(monthKey) {
  return new Intl.DateTimeFormat("es-MX", {
    month: "short",
    year: "numeric"
  }).format(monthKeyToDate(monthKey));
}

function normalizeText(value) {
  return String(value || "")
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .trim();
}

function allLedgerMonthKeys(units = state.units) {
  const keys = new Set();
  units.forEach((unit) => {
    Object.keys(unit.paymentLedger || {}).forEach((monthKey) => keys.add(monthKey));
  });
  return sortMonthKeysDesc(keys);
}

function historyMonthKeys(units = visibleUnits({ ignoreStatusFilter: true })) {
  const recentKeys = new Set(recentMonthKeys());
  return allLedgerMonthKeys(units).filter((monthKey) => !recentKeys.has(monthKey));
}

function getProperty(propertyId) {
  return state.properties.find((property) => property.id === propertyId);
}

function getUser(userId) {
  return state.users.find((user) => user.id === userId);
}

function getActiveRole() {
  return roles.find((role) => role.id === view.roleId);
}

function getActiveUser() {
  return getUser(view.userId) || state.users.find((user) => user.role === view.roleId);
}

function canSeeEveryProperty(roleId = view.roleId) {
  return ["superadmin", "admin", "general_accounting", "legal"].includes(roleId);
}

function canManagePayments() {
  return ["superadmin", "admin", "local_accounting", "general_accounting"].includes(view.roleId);
}

function canManageAccess() {
  return ["superadmin", "admin"].includes(view.roleId);
}

function canViewTenants() {
  return ["superadmin", "admin", "project_manager", "local_accounting", "general_accounting", "legal"].includes(view.roleId);
}

function canManageTenants() {
  return ["superadmin", "admin", "project_manager"].includes(view.roleId);
}

function visiblePropertyIds() {
  const user = getActiveUser();
  if (canSeeEveryProperty()) return state.properties.map((property) => property.id);
  if (view.roleId === "tenant") {
    return [...new Set(state.units.filter((unit) => unit.tenantUserId === user?.id).map((unit) => unit.propertyId))];
  }
  return user?.propertyIds || [];
}

function visibleProperties() {
  const allowed = new Set(visiblePropertyIds());
  return state.properties.filter((property) => allowed.has(property.id));
}

function unitTotal(unit) {
  return paymentTotalAmount(unit, "rentTotal") + paymentTotalAmount(unit, "maintenanceTotal") + conceptAmount(unit, "advertising");
}

function getPaymentStatus(unit, monthKey, conceptKey) {
  const paymentGroup = paymentGroupForConcept(conceptKey);
  if (paymentGroup) {
    return paymentGroupStatusFromManualCapture(unit, monthKey, paymentGroup);
  }

  return unit.paymentLedger?.[monthKey]?.[conceptKey] || unit.paymentStatus?.[conceptKey] || "paid";
}

function setPaymentStatus(unit, monthKey, conceptKey, status) {
  if (!unit.paymentLedger) unit.paymentLedger = {};
  if (!unit.paymentLedger[monthKey]) {
    unit.paymentLedger[monthKey] = normalizePaymentStatus({}, unit);
  }
  unit.paymentLedger[monthKey][conceptKey] = status;
  if (monthKey === currentMonthKey()) {
    unit.paymentStatus = normalizePaymentStatus(unit.paymentLedger[monthKey], unit);
  }
}

function unitPendingTotal(unit, monthKeys = [currentMonthKey()]) {
  return monthKeys.reduce((total, monthKey) => {
    let monthPending = paymentGroupPendingAmount(unit, monthKey, "rent") + paymentGroupPendingAmount(unit, monthKey, "maintenance");
    const advertisingAmount = conceptAmountForMonth(unit, "advertising", monthKey);
    if (advertisingAmount > 0 && getPaymentStatus(unit, monthKey, "advertising") === "pending") {
      monthPending += advertisingAmount;
    }
    return total + monthPending;
  }, 0);
}

function unitPaidTotal(unit, monthKeys = [currentMonthKey()]) {
  return monthKeys.reduce((total, monthKey) => {
    if (!isUnitBillableForMonth(unit, monthKey)) return total;
    let monthPaid = paymentGroupPaidAmount(unit, monthKey, "rent") + paymentGroupPaidAmount(unit, monthKey, "maintenance");
    const advertisingAmount = conceptAmountForMonth(unit, "advertising", monthKey);
    if (advertisingAmount > 0 && getPaymentStatus(unit, monthKey, "advertising") === "paid") {
      monthPaid += advertisingAmount;
    }
    return total + monthPaid;
  }, 0);
}

function hasPendingPayments(unit, monthKeys = recentMonthKeys()) {
  return monthKeys.some((monthKey) => unitPendingTotal(unit, [monthKey]) > 0);
}

function paymentGroupForConcept(conceptKey) {
  if (["rent", "extraordinary"].includes(conceptKey)) return "rent";
  if (["maintenance", "services"].includes(conceptKey)) return "maintenance";
  return null;
}

function paymentGroupConceptKeys(groupKey) {
  if (groupKey === "rent") return ["rent", "extraordinary"];
  if (groupKey === "maintenance") return ["maintenance", "services"];
  return [];
}

function paymentGroupTotalAmount(unit, groupKey) {
  if (groupKey === "rent") return paymentTotalAmount(unit, "rentTotal");
  if (groupKey === "maintenance") return paymentTotalAmount(unit, "maintenanceTotal");
  return 0;
}

function paymentGroupCapturedAmount(unit, monthKey, groupKey) {
  return paymentGroupConceptKeys(groupKey).reduce((sum, conceptKey) => sum + conceptAmountForMonth(unit, conceptKey, monthKey), 0);
}

function paymentGroupPendingAmount(unit, monthKey, groupKey) {
  if (!isUnitBillableForMonth(unit, monthKey)) return 0;
  const total = paymentGroupTotalAmount(unit, groupKey);
  const captured = paymentGroupCapturedAmount(unit, monthKey, groupKey);
  return Math.max(total - captured, 0);
}

function paymentGroupPaidAmount(unit, monthKey, groupKey) {
  if (!isUnitBillableForMonth(unit, monthKey)) return 0;
  return Math.max(paymentGroupCapturedAmount(unit, monthKey, groupKey), 0);
}

function paymentGroupStatusFromManualCapture(unit, monthKey, groupKey) {
  return paymentGroupPendingAmount(unit, monthKey, groupKey) > 0 ? "pending" : "paid";
}

function syncManualPaymentGroupStatuses(unit, monthKey) {
  ["rent", "maintenance"].forEach((groupKey) => {
    const status = paymentGroupStatusFromManualCapture(unit, monthKey, groupKey);
    paymentGroupConceptKeys(groupKey).forEach((conceptKey) => setPaymentStatus(unit, monthKey, conceptKey, status));
  });
}

function visibleUnits(options = {}) {
  const propertyIds = new Set(visiblePropertyIds());
  const activeUser = getActiveUser();
  const query = view.search.trim().toLowerCase();
  const statusMonths = options.ignoreStatusFilter ? [] : view.activeTab === "history" ? historyMonthKeysForFilter() : recentMonthKeys();

  return state.units.filter((unit) => {
    const property = getProperty(unit.propertyId);
    const isVisible = view.roleId === "tenant" ? unit.tenantUserId === activeUser?.id : propertyIds.has(unit.propertyId);
    if (!isVisible) return false;
    if (view.propertyFilter !== "all" && unit.propertyId !== view.propertyFilter) return false;
    if (!options.ignoreStatusFilter && !unitMatchesStatusFilter(unit, statusMonths)) return false;
    if (!query) return true;

    const searchable = `${property?.name || ""} ${unit.unit} ${unit.tenant} ${unit.templateContract} ${unit.signedContract}`.toLowerCase();
    return searchable.includes(query);
  });
}

function unitMatchesStatusFilter(unit, statusMonths) {
  if (view.statusFilter === "all") return true;

  if (PANEL_ROLE_ID === "legal" && view.roleId === PANEL_ROLE_ID) {
    const status = contractStatus(unit);
    if (view.statusFilter === "attention") return status.kind !== "active";
    if (view.statusFilter === "active") return status.kind === "active" && !isContractUnsigned(unit);
    if (view.statusFilter === "unsigned") return isContractUnsigned(unit);
    return true;
  }

  if (view.statusFilter === "pending") return hasPendingPayments(unit, statusMonths);
  if (view.statusFilter === "paid") return !hasPendingPayments(unit, statusMonths);
  return true;
}

function isContractUnsigned(unit) {
  return normalizeText(unit.signedContract).includes("pendiente");
}

function historyMonthKeysForFilter() {
  const units = visibleUnits({ ignoreStatusFilter: true });
  return historyMonthKeys(units);
}

function visibleTenantRows() {
  const allowedProperties = new Set(visiblePropertyIds());
  const query = normalizeText(view.search);

  return tenantRows().filter((tenant) => {
    const visiblePropertyMatch = canSeeEveryProperty()
      ? true
      : tenant.propertyIds.some((propertyId) => allowedProperties.has(propertyId));
    if (!visiblePropertyMatch) return false;
    if (view.propertyFilter !== "all" && !tenant.propertyIds.includes(view.propertyFilter)) return false;
    if (!query) return true;

    const searchable = normalizeText(`${tenant.name} ${tenant.rfc} ${tenant.email} ${tenant.phone} ${tenant.contact} ${tenant.propertiesLabel}`);
    return searchable.includes(query);
  }).sort((a, b) => a.name.localeCompare(b.name, "es"));
}

function tenantRows() {
  return (state.tenantProfiles || []).map((profile) => {
    const user = profile.userId ? getUser(profile.userId) : null;
    const defaults = tenantProfileDefaults(profile);
    const assignedUnits = state.units.filter((unit) =>
      unit.tenantProfileId === profile.id ||
      (profile.userId && unit.tenantUserId === profile.userId) ||
      normalizeText(unit.tenant) === normalizeText(profile.name || user?.name)
    );
    const propertyIds = [...new Set([...(profile.propertyIds || []), ...assignedUnits.map((unit) => unit.propertyId)].filter(Boolean))];
    const propertiesLabel = propertyIds.map((propertyId) => getProperty(propertyId)?.name).filter(Boolean).join(", ") || "Sin propiedad";

    return {
      ...profile,
      name: user?.name || profile.name,
      email: user?.email || profile.email || "",
      fiscalAddress: profile.fiscalAddress || defaults.fiscalAddress,
      legalRepresentative: profile.legalRepresentative || defaults.legalRepresentative,
      bankName: profile.bankName || defaults.bankName,
      bankAccount: profile.bankAccount || defaults.bankAccount,
      bankClabe: profile.bankClabe || defaults.bankClabe,
      paymentReference: profile.paymentReference || defaults.paymentReference,
      assignedUnits,
      propertyIds,
      propertiesLabel,
      unitsLabel: assignedUnits.map((unit) => unit.unit).join(", ") || "Sin unidad asignada",
      hasPortalAccess: Boolean(user)
    };
  });
}

function tenantProfileDefaults(profile = {}) {
  const key = normalizeText(profile.id || profile.name);
  const known = {
    "tp-aurora": {
      fiscalAddress: "Av. Argentina 120, Centro, Monterrey, N.L.",
      legalRepresentative: "Elena Garza",
      bankName: "BBVA",
      bankAccount: "0112389401",
      bankClabe: "012580001123894019",
      paymentReference: "AUR-PLZA-L01"
    },
    "tp-fitlab": {
      fiscalAddress: "Calz. San Pedro 441, San Pedro Garza Garcia, N.L.",
      legalRepresentative: "Ricardo Trevino",
      bankName: "Santander",
      bankAccount: "6550024188",
      bankClabe: "014580655002418889",
      paymentReference: "FIT-PLZA-L02"
    },
    "tp-nova": {
      fiscalAddress: "Carretera Saltillo 900, Santa Catarina, N.L.",
      legalRepresentative: "Paola Rivera",
      bankName: "Banorte",
      bankAccount: "0724498001",
      bankClabe: "072580007244980013",
      paymentReference: "NOV-BOD-A12"
    },
    "tp-mariana": {
      fiscalAddress: "Priv. Bosque Real 310, San Pedro Garza Garcia, N.L.",
      legalRepresentative: "Mariana Chavez",
      bankName: "BBVA",
      bankAccount: "0112400310",
      bankClabe: "012580001124003108",
      paymentReference: "MAR-RES-310"
    }
  };

  const demoDefaults = tenantDemoProfileDefaults(profile);
  return known[key] || demoDefaults;
}

function tenantDemoProfileDefaults(profile = {}) {
  const name = String(profile.name || "Arrendatario");
  const idSeed = Math.abs([...String(profile.id || name)].reduce((sum, char) => sum + char.charCodeAt(0), 0));
  const account = String(1000000000 + idSeed * 97).slice(0, 10).padEnd(10, "0");
  const clabe = `01258000${account}`.slice(0, 18).padEnd(18, "0");
  const reference = name
    .split(/\s+/)
    .map((word) => word[0] || "")
    .join("")
    .toUpperCase()
    .slice(0, 4) || "ARR";

  return {
    fiscalAddress: "Domicilio fiscal registrado en expediente",
    legalRepresentative: profile.contact || name,
    bankName: "BBVA",
    bankAccount: account,
    bankClabe: clabe,
    paymentReference: `${reference}-${String(profile.id || "000").slice(-3).toUpperCase()}`
  };
}

function renderIcon(name) {
  return icons[name] || "";
}

function injectIcons(root = document) {
  root.querySelectorAll("[data-icon]").forEach((node) => {
    node.innerHTML = renderIcon(node.dataset.icon);
  });
}

function setButtonIcon(button, iconName) {
  button.insertAdjacentHTML("afterbegin", renderIcon(iconName));
}

function init() {
  els.currentMonth.textContent = formatMonthLabel(currentMonthKey());

  bindEvents();
  renderCredentialsTable();

  const sessionUser = loadSessionUser();
  if (sessionUser) {
    startSession(sessionUser, false);
  } else {
    showLogin();
  }
  injectIcons();
}

function loadSessionUser() {
  const userId = localStorage.getItem(SESSION_KEY);
  const user = userId ? getUser(userId) : null;
  if (!user || user.role !== PANEL_ROLE_ID) return null;
  return ["tenant", "local_accounting"].includes(PANEL_ROLE_ID) || user.id === PANEL_USER_ID ? user : null;
}

function getLoggedInUser() {
  return view.loggedInUserId ? getUser(view.loggedInUserId) : null;
}

function canSwitchUserContext() {
  return false;
}

function startSession(user, persist = true) {
  const canUseRequestedUser = user?.role === PANEL_ROLE_ID && (["tenant", "local_accounting"].includes(PANEL_ROLE_ID) || user.id === PANEL_USER_ID);
  const panelUser = canUseRequestedUser ? user : getUser(PANEL_USER_ID);
  if (!panelUser) return;

  view.loggedInUserId = panelUser.id;
  view.roleId = PANEL_ROLE_ID;
  view.userId = panelUser.id;
  view.activeTab = PANEL_DEFAULT_TAB;
  view.search = "";
  view.propertyFilter = "all";
  view.statusFilter = "all";
  view.tenantCatalogPropertyId = "";
  view.legalReturnTab = "";
  view.propertyReturnTab = "";
  if (els.searchInput) els.searchInput.value = "";
  if (els.statusFilter) els.statusFilter.value = "all";
  if (persist) localStorage.setItem(SESSION_KEY, panelUser.id);
  showApp();
  renderRoleOptions();
  render();
}

function endSession() {
  localStorage.removeItem(SESSION_KEY);
  view.loggedInUserId = null;
  closeModal();
  showLogin();
  toast("Sesion cerrada");
}

function showLogin() {
  els.loginView.hidden = false;
  els.appShell.hidden = true;
  els.loginError.hidden = true;
  els.loginForm.reset();
  renderCredentialsTable();
  selectDemoLoginUser(PANEL_USER_ID);
  injectIcons(els.loginView);
}

function showApp() {
  els.loginView.hidden = true;
  els.appShell.hidden = false;
}

function authenticate(username, password) {
  const normalizedUsername = normalizeText(username);
  return panelDemoUsers().find((user) =>
    [user.username, user.email].some((value) => normalizeText(value) === normalizedUsername) &&
    user.password === password
  );
}

function panelDemoUsers() {
  if (["tenant", "local_accounting"].includes(PANEL_ROLE_ID)) {
    return state.users.filter((user) => user.role === PANEL_ROLE_ID);
  }
  return state.users.filter((user) => user.id === PANEL_USER_ID && user.role === PANEL_ROLE_ID);
}

function renderCredentialsTable() {
  if (!els.credentialsTable) return;

  if (els.loginUserSelect) {
    els.loginUserSelect.innerHTML = panelDemoUsers()
      .map((user) => `<option value="${user.id}">${user.name} - ${roleNames[user.role] || user.role}</option>`)
      .join("");
  }

  els.credentialsTable.innerHTML = panelDemoUsers()
    .map((user) => `
      <tr>
        <td class="primary-cell">
          <strong>${user.name}</strong>
          <small>${user.email}</small>
        </td>
        <td>${roleNames[user.role] || user.role}</td>
        <td><code>${user.username}</code></td>
        <td><code>${user.password}</code></td>
      </tr>
    `)
    .join("");
}

function selectDemoLoginUser(userId) {
  const user = getUser(userId) || getUser(PANEL_USER_ID);
  if (!user) return;

  els.loginUserSelect.value = user.id;
  els.loginUsername.value = user.username;
  els.loginPassword.value = DEMO_PASSWORD;
  els.loginError.hidden = true;
}

function renderRoleOptions() {
  const availableRoles = roles.filter((role) => role.id === PANEL_ROLE_ID);

  els.roleSelect.innerHTML = roles
    .filter((role) => availableRoles.some((availableRole) => availableRole.id === role.id))
    .map((role) => `<option value="${role.id}">${role.name}</option>`)
    .join("");
  if (!availableRoles.some((role) => role.id === view.roleId)) {
    view.roleId = availableRoles[0]?.id || view.roleId;
  }
  els.roleSelect.value = view.roleId;
  els.roleSelect.disabled = !canSwitchUserContext();
  renderUserOptions();
}

function renderUserOptions() {
  const usersForRole = state.users.filter((user) => user.id === view.loggedInUserId);
  if (!usersForRole.some((user) => user.id === view.userId)) {
    view.userId = usersForRole[0]?.id || "";
  }

  els.userSelect.innerHTML = usersForRole
    .map((user) => `<option value="${user.id}">${user.name}</option>`)
    .join("");
  els.userSelect.value = view.userId;
  els.userSelect.disabled = !canSwitchUserContext();
  renderSessionSummary();
}

function renderSessionSummary() {
  const user = getLoggedInUser();
  if (!user) {
    els.sessionSummary.innerHTML = "";
    return;
  }

  els.sessionSummary.innerHTML = `
    <span>Sesion</span>
    <strong>${user.name}</strong>
    <small>${roleNames[user.role] || user.role}</small>
  `;
}

function bindEvents() {
  els.loginUserSelect.addEventListener("change", (event) => {
    selectDemoLoginUser(event.target.value);
  });

  els.loginForm.addEventListener("submit", (event) => {
    event.preventDefault();
    const user = authenticate(els.loginUsername.value, els.loginPassword.value);
    if (!user) {
      els.loginError.hidden = false;
      return;
    }
    startSession(user);
  });

  els.logoutButton.addEventListener("click", endSession);

  els.roleSelect.addEventListener("change", (event) => {
    if (!canSwitchUserContext()) return;
    view.roleId = event.target.value;
    view.propertyFilter = "all";
    view.statusFilter = "all";
    view.search = "";
    if (els.searchInput) els.searchInput.value = "";
    if (els.statusFilter) els.statusFilter.value = "all";
    renderUserOptions();
    ensureActiveTabAllowed();
    render();
  });

  els.userSelect.addEventListener("change", (event) => {
    if (!canSwitchUserContext()) return;
    view.userId = event.target.value;
    view.propertyFilter = "all";
    render();
  });

  els.searchInput?.addEventListener("input", (event) => {
    view.search = event.target.value;
    renderContent();
  });

  els.propertyFilter?.addEventListener("change", (event) => {
    view.propertyFilter = event.target.value;
    render();
  });

  els.statusFilter?.addEventListener("change", (event) => {
    view.statusFilter = event.target.value;
    render();
  });

  els.modalClose.addEventListener("click", closeModal);
  els.modalBackdrop.addEventListener("click", (event) => {
    if (event.target === els.modalBackdrop) closeModal();
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") closeModal();
  });
}

function defaultTabForRole(roleId) {
  const roleDefaults = {
    superadmin: "properties",
    general_accounting: "general_accounting_dashboard",
    local_accounting: "local_accounting_dashboard",
    legal: "contracts",
    tenant: "tenant_dashboard"
  };

  return roleDefaults[roleId] || PANEL_DEFAULT_TAB;
}

function ensureActiveTabAllowed() {
  const allowedTabs = tabs.filter((tab) => tab.roles.includes(view.roleId));
  if (!allowedTabs.some((tab) => tab.id === view.activeTab)) {
    view.activeTab = allowedTabs.some((tab) => tab.id === defaultTabForRole(view.roleId))
      ? defaultTabForRole(view.roleId)
      : allowedTabs[0]?.id || "units";
  }
}

function render() {
  if (syncExpiredContractValidations()) saveState();
  ensureActiveTabAllowed();
  renderHeader();
  renderNav();
  renderPropertyFilter();
  renderFilterVisibility();
  renderMetrics();
  renderContent();
  injectIcons();
}

function renderFilterVisibility() {
  if (!els.filtersBar) return;
  els.filtersBar.hidden = view.activeTab !== "properties";
}

function renderHeader() {
  const role = getActiveRole();
  const user = getActiveUser();
  const properties = visibleProperties();

  els.roleEyebrow.textContent = role.name;
  els.viewTitle.textContent = tabs.find((tab) => tab.id === view.activeTab)?.label || "Panel operativo";
  els.viewSubtitle.textContent = view.activeTab === "superadmin_dashboard"
    ? `${user?.name || "Usuario"} - Gobierno SaaS, cuentas, permisos, configuracion global y monitoreo operativo.`
    : view.roleId === "tenant"
      ? `${user?.name || "Usuario"} - Pagos, facturas, historial y contratos.`
      : `${user?.name || "Usuario"} - ${role.description}`;

  if (!els.scopeTitle || !els.scopeDescription) return;

  if (view.activeTab === "superadmin_dashboard") {
    els.scopeTitle.textContent = "Operacion SaaS completa";
    els.scopeDescription.textContent = "Centro de control para cuentas, cartera, usuarios, permisos, cobranza, contratos, modulos activos y riesgos operativos.";
  } else if (view.activeTab === "general_accounting_dashboard") {
    els.scopeTitle.textContent = "Conciliacion global";
    els.scopeDescription.textContent = "Contabilidad General concentra la cobranza de todas las propiedades, valida facturas, revisa atrasos y descarga reportes historicos.";
  } else if (view.activeTab === "local_accounting_dashboard") {
    els.scopeTitle.textContent = properties.map((property) => property.name).join(", ") || "Sin propiedad asignada";
    els.scopeDescription.textContent = "Contabilidad Local valida ingresos, facturas y conciliacion mensual solo de sus propiedades asignadas.";
  } else if (view.roleId === "legal") {
    els.scopeTitle.textContent = "Contratos de todas las propiedades";
    els.scopeDescription.textContent = "Legal tiene visibilidad sobre machotes, contratos firmados, fechas criticas y expedientes de arrendamiento.";
  } else if (canSeeEveryProperty()) {
    els.scopeTitle.textContent = "Todas las propiedades";
    els.scopeDescription.textContent = `${role.name} tiene visibilidad sobre plazas comerciales, bodegas, vivienda, facturas, historial y cobranza.`;
  } else if (view.roleId === "tenant") {
    els.scopeTitle.textContent = "Mis unidades y pagos";
    els.scopeDescription.textContent = "Portal para consultar saldos, registrar pagos, descargar facturas y revisar contratos vigentes.";
  } else {
    els.scopeTitle.textContent = properties.map((property) => property.name).join(", ") || "Sin propiedad asignada";
    els.scopeDescription.textContent = "Este panel se limita a las propiedades asignadas al usuario activo.";
  }
}

function renderNav() {
  els.navTabs.innerHTML = tabs
    .filter((tab) => tab.roles.includes(view.roleId) && !tab.hidden && !(tab.navHiddenRoles || []).includes(view.roleId))
    .map((tab) => `
      <button class="nav-tab ${isNavTabActive(tab.id) ? "is-active" : ""}" type="button" data-tab="${tab.id}">
        ${renderIcon(tab.icon)}
        <span>${tab.label}</span>
      </button>
    `)
    .join("");

  els.navTabs.querySelectorAll("[data-tab]").forEach((button) => {
    button.addEventListener("click", () => {
      view.activeTab = button.dataset.tab;
      if (view.activeTab === "tenants") {
        view.tenantCatalogPropertyId = "";
        view.propertyFilter = "all";
      }
      render();
    });
  });
}

function isNavTabActive(tabId) {
  return tabId === view.activeTab
    || (tabId === "properties" && ["property_detail", "property_balance", "property_payment_method", "property_advance_payments", "property_unit_status", "property_legal_panel", "property_operating_costs"].includes(view.activeTab))
    || (tabId === "tenants" && view.activeTab === "tenant_new");
}

function renderPropertyFilter() {
  if (!els.propertyFilter) return;
  const properties = visibleProperties();
  els.propertyFilter.innerHTML = `
    <option value="all">${view.roleId === "tenant" ? "Todas mis propiedades" : "Todas las propiedades visibles"}</option>
    ${properties.map((property) => `<option value="${property.id}">${property.name}</option>`).join("")}
  `;

  if (!properties.some((property) => property.id === view.propertyFilter)) {
    view.propertyFilter = "all";
  }

  els.propertyFilter.value = view.propertyFilter;
}

function renderSuperadminMetrics() {
  const stats = superadminStats();
  const metrics = [
    {
      label: "Cuentas SaaS",
      value: stats.organizations,
      note: `${stats.activeOrganizations} activas`
    },
    {
      label: "Ingreso plataforma",
      value: formatCurrency(stats.platformRevenue),
      note: "Mensualidad demo de planes"
    },
    {
      label: "Cartera supervisada",
      value: formatCurrency(stats.monthlyCollection),
      note: `${stats.properties} propiedades y ${stats.units} unidades`
    },
    {
      label: "Alertas criticas",
      value: stats.alerts,
      note: "Adeudos, contratos y accesos"
    }
  ];

  els.metricsGrid.innerHTML = metrics
    .map((metric) => `
      <article class="metric-tile">
        <span>${metric.label}</span>
        <strong>${metric.value}</strong>
        <small>${metric.note}</small>
      </article>
    `)
    .join("");
}

function renderGeneralAccountingMetrics() {
  const data = generalAccountingSummary();
  const metrics = [
    {
      label: "Ingresos conciliados",
      value: formatCurrency(data.paidTotal),
      note: `${data.reconciliationRate}% conciliado en la ventana operativa`
    },
    {
      label: "Cartera por cobrar",
      value: formatCurrency(data.pendingTotal),
      note: `${data.pendingInvoices} conceptos pendientes`
    },
    {
      label: "Adeudo vencido",
      value: formatCurrency(data.overduePending),
      note: "Meses anteriores al periodo actual"
    },
    {
      label: "Propiedades",
      value: data.properties,
      note: `${data.occupiedUnits}/${data.units} unidades ocupadas`
    }
  ];

  els.metricsGrid.innerHTML = metrics
    .map((metric) => `
      <article class="metric-tile">
        <span>${metric.label}</span>
        <strong>${metric.value}</strong>
        <small>${metric.note}</small>
      </article>
    `)
    .join("");
}

function renderLocalAccountingMetrics() {
  const data = localAccountingSummary();
  const metrics = [
    {
      label: "Ingreso del periodo",
      value: formatCurrency(data.expectedTotal),
      note: `${data.properties} propiedades asignadas`
    },
    {
      label: "Ingresado",
      value: formatCurrency(data.paidTotal),
      note: `${data.collectionRate}% del mes conciliado`
    },
    {
      label: "Pendiente local",
      value: formatCurrency(data.pendingTotal),
      note: `${data.pendingInvoices} conceptos por validar`
    },
    {
      label: "Facturas listas",
      value: data.readyInvoices,
      note: `${data.totalInvoices} conceptos facturables`
    }
  ];

  els.metricsGrid.innerHTML = metrics
    .map((metric) => `
      <article class="metric-tile">
        <span>${metric.label}</span>
        <strong>${metric.value}</strong>
        <small>${metric.note}</small>
      </article>
    `)
    .join("");
}

function renderLegalMetrics() {
  const units = view.roleId === "tenant" && view.activeTab === "tenant_dashboard"
    ? visibleUnits({ ignoreStatusFilter: true })
    : visibleUnits();
  const activeContracts = units.filter((unit) => contractStatus(unit).kind === "active" && !isContractUnsigned(unit)).length;
  const attentionContracts = units.filter((unit) => contractStatus(unit).kind !== "active").length;
  const unsignedContracts = units.filter(isContractUnsigned).length;
  const templateCount = new Set(units.map((unit) => unit.templateContract).filter(Boolean)).size;

  const metrics = [
    {
      label: "Expedientes",
      value: units.length,
      note: `${activeContracts} contratos activos documentados`
    },
    {
      label: "Vencimientos",
      value: attentionContracts,
      note: "Contratos vencidos o a 90 dias"
    },
    {
      label: "Pendientes de firma",
      value: unsignedContracts,
      note: "Documentos sin contrato firmado"
    },
    {
      label: "Machotes",
      value: templateCount,
      note: "Comercial, industrial y vivienda"
    }
  ];

  els.metricsGrid.innerHTML = metrics
    .map((metric) => `
      <article class="metric-tile">
        <span>${metric.label}</span>
        <strong>${metric.value}</strong>
        <small>${metric.note}</small>
      </article>
    `)
    .join("");
}

function renderMetrics() {
  if (!els.metricsGrid) return;

  if (view.activeTab === "superadmin_dashboard") {
    renderSuperadminMetrics();
    return;
  }
  if (view.activeTab === "general_accounting_dashboard") {
    renderGeneralAccountingMetrics();
    return;
  }
  if (view.activeTab === "local_accounting_dashboard") {
    renderLocalAccountingMetrics();
    return;
  }
  if (PANEL_ROLE_ID === "legal" && view.roleId === "legal") {
    renderLegalMetrics();
    return;
  }

  const units = visibleUnits();
  const periodKeys = view.activeTab === "history" ? historyMonthKeys(units) : recentMonthKeys();
  const pendingTotal = units.reduce((sum, unit) => sum + unitPendingTotal(unit, periodKeys), 0);
  const paidTotal = units.reduce((sum, unit) => sum + unitPaidTotal(unit, periodKeys), 0);
  const expiringContracts = units.filter((unit) => contractStatus(unit).kind !== "active").length;
  const occupiedUnits = units.filter((unit) => unit.tenant !== "Disponible").length;
  const periodNote = view.activeTab === "history" ? "historial" : "mes actual y seis meses atras";

  if (view.roleId === "tenant") {
    const currentPending = units.reduce((sum, unit) => sum + unitPendingTotal(unit, [currentMonthKey()]), 0);
    const overduePending = tenantOpenItems(units)
      .filter((item) => item.monthKey < currentMonthKey())
      .reduce((sum, item) => sum + item.amount, 0);
    const paidInvoices = tenantPaidItems(units).length;

    els.metricsGrid.innerHTML = [
      {
        label: "Mis unidades",
        value: units.length,
        note: visibleProperties().map((property) => property.name).join(", ") || "Sin propiedad"
      },
      {
        label: "Por pagar",
        value: formatCurrency(currentPending + overduePending),
        note: overduePending > 0 ? `${formatCurrency(overduePending)} vencido` : "Sin adeudos vencidos"
      },
      {
        label: "Facturas disponibles",
        value: paidInvoices,
        note: "Descarga inmediata despues del pago"
      },
      {
        label: "Contratos",
        value: units.length - expiringContracts,
        note: expiringContracts ? `${expiringContracts} en atencion` : "Vigentes"
      }
    ].map((metric) => `
      <article class="metric-tile">
        <span>${metric.label}</span>
        <strong>${metric.value}</strong>
        <small>${metric.note}</small>
      </article>
    `).join("");
    return;
  }

  const metrics = [
    {
      label: "Unidades visibles",
      value: units.length,
      note: `${occupiedUnits} ocupadas`
    },
    {
      label: "Por cobrar",
      value: formatCurrency(pendingTotal),
      note: `Importes por pagar en ${periodNote}`
    },
    {
      label: "Cobrado",
      value: formatCurrency(paidTotal),
      note: `Importes pagados en ${periodNote}`
    },
    {
      label: "Contratos en atencion",
      value: expiringContracts,
      note: "Vencidos o por vencer"
    }
  ];

  els.metricsGrid.innerHTML = metrics
    .map((metric) => `
      <article class="metric-tile">
        <span>${metric.label}</span>
        <strong>${metric.value}</strong>
        <small>${metric.note}</small>
      </article>
    `)
    .join("");
}

function renderContent() {
  const renderers = {
    tenant_dashboard: renderTenantDashboard,
    superadmin_dashboard: renderPlazaGeneralDashboard,
    general_accounting_dashboard: renderGeneralAccountingDashboard,
    local_accounting_dashboard: typeof renderLocalAccountingDashboard === "function" ? renderLocalAccountingDashboard : renderUnitsTable,
    units: renderUnitsTable,
    administration: renderPlazaAdministration,
    plaza_contracts: renderPlazaContracts,
    plaza_marketplace: renderPlazaMarketplace,
    properties: renderPropertiesCatalog,
    property_detail: renderPropertyDetailSection,
    property_balance: renderPropertyBalanceSection,
    property_payment_method: renderPropertyPaymentMethodSection,
    property_advance_payments: renderPropertyAdvancePaymentsSection,
    property_unit_status: renderPropertyUnitStatusSection,
    property_legal_panel: renderPropertyLegalPanelSection,
    property_operating_costs: renderPropertyOperatingCostsSection,
    property_tenants: renderPropertyTenantDirectorySection,
    tenants: renderTenantCatalog,
    tenant_new: renderTenantNew,
    user_new: renderUserNew,
    invoices: renderInvoices,
    history: renderHistory,
    contracts: renderContracts,
    users: renderUsers
  };

  els.contentArea.innerHTML = "";
  const renderer = renderers[view.activeTab] || renderUnitsTable;
  renderer();
  injectIcons(els.contentArea);
}

function renderGeneralAccountingDashboard() {
  const units = generalAccountingDashboardUnits();
  const summary = generalAccountingSummary(units);
  const propertyRows = generalAccountingPropertyRows(units);
  const conceptRows = generalAccountingConceptRows(units);
  const pendingRows = generalAccountingPendingRows(units).slice(0, 8);
  const agingRows = generalAccountingAgingRows(units);

  els.contentArea.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">Contabilidad General</p>
        <h3>Tablero de conciliacion global</h3>
        <p class="muted">Concentra la cobranza de todas las propiedades, identifica facturas pendientes y prioriza cartera vencida.</p>
      </div>
      <div class="section-actions">
        <button class="action-button" type="button" data-accounting-target="invoices" data-status-filter="pending">
          <span data-icon="receipt" aria-hidden="true"></span>
          Conciliar pendientes
        </button>
        <button class="secondary-button" type="button" data-accounting-action="download-report">
          <span data-icon="download" aria-hidden="true"></span>
          Reporte CSV
        </button>
        <button class="secondary-button" type="button" data-accounting-action="reset-demo">
          <span data-icon="filter" aria-hidden="true"></span>
          Restaurar demo
        </button>
      </div>
    </div>

    ${units.length ? `
      <section class="accounting-control-strip" aria-label="Estado contable">
        <article>
          <span>Periodo operativo</span>
          <strong>${formatMonthLabel(currentMonthKey())}</strong>
          <small>${recentMonthKeys().length} meses visibles para conciliacion</small>
        </article>
        <article>
          <span>Facturas pendientes</span>
          <strong>${summary.pendingInvoices}</strong>
          <small>${formatCurrency(summary.currentPending)} del mes actual</small>
        </article>
        <article>
          <span>Avance global</span>
          <strong>${summary.reconciliationRate}%</strong>
          <div class="progress-track" aria-hidden="true">
            <div style="width: ${summary.reconciliationRate}%"></div>
          </div>
        </article>
      </section>

      <div class="accounting-grid">
        <section class="table-panel accounting-section accounting-section-wide">
          <div class="month-panel-header">
            <div>
              <p class="eyebrow">Cartera</p>
              <h3>Resumen por propiedad</h3>
              <p class="muted">Comparativo de facturado, conciliado y por cobrar en la ventana operativa.</p>
            </div>
          </div>
          ${generalAccountingPropertyTable(propertyRows)}
        </section>

        <section class="table-panel accounting-section">
          <div class="month-panel-header">
            <div>
              <p class="eyebrow">Antiguedad</p>
              <h3>Cartera vencida</h3>
              <p class="muted">Prioridad por meses de atraso.</p>
            </div>
          </div>
          ${generalAccountingAgingMarkup(agingRows)}
        </section>

        <section class="table-panel accounting-section accounting-section-wide">
          <div class="month-panel-header">
            <div>
              <p class="eyebrow">Conciliacion</p>
              <h3>Pendientes principales</h3>
              <p class="muted">Conceptos por pagar ordenados por antiguedad y monto.</p>
            </div>
            <button class="secondary-button" type="button" data-accounting-target="invoices" data-status-filter="pending">
              <span data-icon="eye" aria-hidden="true"></span>
              Ver facturas
            </button>
          </div>
          ${pendingRows.length ? generalAccountingPendingTable(pendingRows) : emptyState("No hay conceptos pendientes con los filtros actuales.")}
        </section>

        <section class="table-panel accounting-section">
          <div class="month-panel-header">
            <div>
              <p class="eyebrow">Conceptos</p>
              <h3>Ingresos por rubro</h3>
              <p class="muted">Renta, extraordinarios, servicios, mantenimiento y publicidad.</p>
            </div>
          </div>
          ${generalAccountingConceptMarkup(conceptRows)}
        </section>
      </div>
    ` : emptyState("No hay movimientos contables con los filtros actuales.")}
  `;

  bindAccountingDashboardActions();
}

function generalAccountingDashboardUnits() {
  return visibleUnits({ ignoreStatusFilter: true });
}

function generalAccountingSummary(units = generalAccountingDashboardUnits()) {
  const periodKeys = recentMonthKeys();
  const allItems = generalAccountingInvoiceItems(units, periodKeys);
  const paidTotal = allItems.reduce((sum, item) => item.status === "paid" ? sum + item.amount : sum, 0);
  const pendingTotal = allItems.reduce((sum, item) => item.status === "pending" ? sum + item.amount : sum, 0);
  const pendingInvoices = allItems.filter((item) => item.status === "pending").length;
  const paidInvoices = allItems.filter((item) => item.status === "paid").length;
  const totalInvoices = allItems.length;
  const currentPending = units.reduce((sum, unit) => sum + unitPendingTotal(unit, [currentMonthKey()]), 0);
  const overduePending = units.reduce((sum, unit) =>
    sum + unitPendingTotal(unit, allLedgerMonthKeys([unit]).filter((monthKey) => monthKey < currentMonthKey())), 0);
  const propertyIds = new Set(units.map((unit) => unit.propertyId));
  const occupiedUnits = units.filter((unit) => unit.tenant !== "Disponible").length;

  return {
    paidTotal,
    pendingTotal,
    pendingInvoices,
    currentPending,
    overduePending,
    reconciliationRate: totalInvoices ? Math.round((paidInvoices / totalInvoices) * 100) : 100,
    properties: propertyIds.size,
    units: units.length,
    occupiedUnits
  };
}

function generalAccountingInvoiceItems(units, monthKeys = recentMonthKeys()) {
  return units.flatMap((unit) => {
    const property = getProperty(unit.propertyId);
    return monthKeys.flatMap((monthKey) =>
      paymentConcepts
        .filter((concept) => (unit[concept.key] || 0) > 0)
        .map((concept) => ({
          unit,
          property,
          concept,
          monthKey,
          amount: unit[concept.key] || 0,
          status: getPaymentStatus(unit, monthKey, concept.key)
        }))
    );
  });
}

function generalAccountingPropertyRows(units) {
  const periodKeys = recentMonthKeys();
  const propertyIds = new Set(units.map((unit) => unit.propertyId));
  return visibleProperties()
    .filter((property) => propertyIds.has(property.id))
    .map((property) => {
      const propertyUnits = units.filter((unit) => unit.propertyId === property.id);
      const billed = propertyUnits.reduce((sum, unit) => sum + unitTotal(unit) * periodKeys.length, 0);
      const paid = propertyUnits.reduce((sum, unit) => sum + unitPaidTotal(unit, periodKeys), 0);
      const pending = propertyUnits.reduce((sum, unit) => sum + unitPendingTotal(unit, periodKeys), 0);
      const localAccounting = getUser(property.localAccountingUserId);

      return {
        property,
        localAccounting,
        units: propertyUnits.length,
        occupied: propertyUnits.filter((unit) => unit.tenant !== "Disponible").length,
        billed,
        paid,
        pending,
        reconciliationRate: billed ? Math.round((paid / billed) * 100) : 100
      };
    });
}

function generalAccountingConceptRows(units) {
  const items = generalAccountingInvoiceItems(units, recentMonthKeys());
  return paymentConcepts.map((concept) => {
    const conceptItems = items.filter((item) => item.concept.key === concept.key);
    const billed = conceptItems.reduce((sum, item) => sum + item.amount, 0);
    const paid = conceptItems.reduce((sum, item) => item.status === "paid" ? sum + item.amount : sum, 0);
    const pending = conceptItems.reduce((sum, item) => item.status === "pending" ? sum + item.amount : sum, 0);
    const pendingCount = conceptItems.filter((item) => item.status === "pending").length;

    return {
      concept,
      billed,
      paid,
      pending,
      pendingCount,
      reconciliationRate: billed ? Math.round((paid / billed) * 100) : 100
    };
  });
}

function generalAccountingPendingRows(units) {
  return generalAccountingInvoiceItems(units, allLedgerMonthKeys(units))
    .filter((item) => item.status === "pending")
    .sort((a, b) => a.monthKey.localeCompare(b.monthKey) || b.amount - a.amount);
}

function generalAccountingAgingRows(units) {
  const buckets = [
    { label: "Mes actual", helper: "Dentro del periodo", amount: 0, count: 0 },
    { label: "1 a 2 meses", helper: "Seguimiento inmediato", amount: 0, count: 0 },
    { label: "3+ meses", helper: "Prioridad critica", amount: 0, count: 0 }
  ];

  generalAccountingPendingRows(units).forEach((item) => {
    const monthsBack = monthsBackFromCurrent(item.monthKey);
    const bucket = monthsBack <= 0 ? buckets[0] : monthsBack <= 2 ? buckets[1] : buckets[2];
    bucket.amount += item.amount;
    bucket.count += 1;
  });

  const maxAmount = Math.max(...buckets.map((bucket) => bucket.amount), 1);
  return buckets.map((bucket) => ({
    ...bucket,
    rate: Math.round((bucket.amount / maxAmount) * 100)
  }));
}

function monthsBackFromCurrent(monthKey) {
  const currentDate = monthKeyToDate(currentMonthKey());
  const targetDate = monthKeyToDate(monthKey);
  return (currentDate.getFullYear() - targetDate.getFullYear()) * 12 + currentDate.getMonth() - targetDate.getMonth();
}

function generalAccountingPropertyTable(rows) {
  if (!rows.length) return emptyState("No hay propiedades con los filtros actuales.");
  return `
    <div class="table-scroll">
      <table class="accounting-table">
        <thead>
          <tr>
            <th>Propiedad</th>
            <th>Contabilidad local</th>
            <th>Unidades</th>
            <th>Facturado</th>
            <th>Conciliado</th>
            <th>Por cobrar</th>
            <th>Avance</th>
            <th>Accion</th>
          </tr>
        </thead>
        <tbody>
          ${rows.map((row) => `
            <tr>
              <td class="primary-cell">
                <strong>${row.property.name}</strong>
                <small>${row.property.type} - ${row.property.location}</small>
              </td>
              <td>${row.localAccounting?.name || "Sin asignar"}</td>
              <td><strong>${row.occupied}/${row.units}</strong></td>
              <td><strong>${formatCurrency(row.billed)}</strong></td>
              <td>${formatCurrency(row.paid)}</td>
              <td>
                <span class="status-pill ${row.pending ? "status-pending" : "status-paid"}">
                  ${formatCurrency(row.pending)}
                </span>
              </td>
              <td>
                <div class="compact-progress">
                  <span>${row.reconciliationRate}%</span>
                  <div class="progress-track"><div style="width: ${row.reconciliationRate}%"></div></div>
                </div>
              </td>
              <td>
                <button class="icon-button" type="button" title="Ver facturas pendientes" aria-label="Ver facturas pendientes" data-accounting-target="invoices" data-status-filter="pending" data-property-id="${row.property.id}">
                  <span data-icon="eye" aria-hidden="true"></span>
                </button>
              </td>
            </tr>
          `).join("")}
        </tbody>
      </table>
    </div>
  `;
}

function generalAccountingPendingTable(rows) {
  return `
    <div class="table-scroll">
      <table class="accounting-table">
        <thead>
          <tr>
            <th>Mes</th>
            <th>Propiedad</th>
            <th>Unidad</th>
            <th>Arrendatario</th>
            <th>Concepto</th>
            <th>Importe</th>
            <th>Antiguedad</th>
          </tr>
        </thead>
        <tbody>
          ${rows.map((item) => {
            const monthsBack = monthsBackFromCurrent(item.monthKey);
            return `
              <tr>
                <td class="nowrap"><strong>${formatMonthShort(item.monthKey)}</strong></td>
                <td class="primary-cell">
                  <strong>${item.property?.name || "Sin propiedad"}</strong>
                  <small>${item.property?.type || ""}</small>
                </td>
                <td><strong>${item.unit.unit}</strong></td>
                <td>${item.unit.tenant}</td>
                <td>${item.concept.label}</td>
                <td><strong>${formatCurrency(item.amount)}</strong></td>
                <td>
                  <span class="status-pill ${monthsBack >= 3 ? "status-danger" : "status-pending"}">
                    ${monthsBack <= 0 ? "Mes actual" : `${monthsBack} ${monthsBack === 1 ? "mes" : "meses"}`}
                  </span>
                </td>
              </tr>
            `;
          }).join("")}
        </tbody>
      </table>
    </div>
  `;
}

function generalAccountingAgingMarkup(rows) {
  return `
    <div class="accounting-aging">
      ${rows.map((row) => `
        <article>
          <div>
            <span>${row.label}</span>
            <strong>${formatCurrency(row.amount)}</strong>
            <small>${row.count} conceptos - ${row.helper}</small>
          </div>
          <div class="progress-track" aria-hidden="true">
            <div style="width: ${row.rate}%"></div>
          </div>
        </article>
      `).join("")}
    </div>
  `;
}

function generalAccountingConceptMarkup(rows) {
  return `
    <div class="concept-stack">
      ${rows.map((row) => `
        <article>
          <div>
            <span>${row.concept.label}</span>
            <strong>${formatCurrency(row.paid)}</strong>
            <small>${row.reconciliationRate}% conciliado</small>
          </div>
          <span class="status-pill ${row.pending ? "status-pending" : "status-paid"}">
            ${row.pendingCount} pendientes
          </span>
        </article>
      `).join("")}
    </div>
  `;
}

function bindAccountingDashboardActions() {
  els.contentArea.querySelectorAll("[data-accounting-target]").forEach((button) => {
    button.addEventListener("click", () => {
      view.activeTab = button.dataset.accountingTarget || "invoices";
      view.propertyFilter = button.dataset.propertyId || "all";
      view.statusFilter = button.dataset.statusFilter || "all";
      if (els.statusFilter) els.statusFilter.value = view.statusFilter;
      render();
    });
  });

  els.contentArea.querySelector("[data-accounting-action='download-report']")?.addEventListener("click", downloadHistoricalReport);
  els.contentArea.querySelector("[data-accounting-action='reset-demo']")?.addEventListener("click", resetDemoData);
}

function localAccountingSummary(units = visibleUnits({ ignoreStatusFilter: true }), monthKey = currentMonthKey()) {
  const invoiceItems = localAccountingInvoiceItems(units, monthKey);
  const expectedTotal = units.reduce((sum, unit) => sum + unitTotal(unit), 0);
  const pendingTotal = units.reduce((sum, unit) => sum + unitPendingTotal(unit, [monthKey]), 0);
  const paidTotal = expectedTotal - pendingTotal;
  const overdueKeys = allLedgerMonthKeys(units).filter((key) => key < monthKey);

  return {
    expectedTotal,
    paidTotal,
    pendingTotal,
    overduePending: units.reduce((sum, unit) => sum + unitPendingTotal(unit, overdueKeys), 0),
    collectionRate: Math.round((paidTotal / Math.max(expectedTotal, 1)) * 100),
    properties: new Set(units.map((unit) => unit.propertyId)).size,
    units: units.length,
    readyInvoices: invoiceItems.filter((item) => item.isPaid).length,
    pendingInvoices: invoiceItems.filter((item) => !item.isPaid).length,
    totalInvoices: invoiceItems.length
  };
}

function renderLocalAccountingDashboard() {
  if (view.roleId !== PANEL_ROLE_ID) {
    els.contentArea.innerHTML = emptyState("Este panel solo esta disponible para Contabilidad Local.");
    return;
  }

  const monthKey = currentMonthKey();
  const units = visibleUnits();
  const rows = localAccountingRows(units, monthKey);
  const summary = localAccountingSummary(units, monthKey);
  const propertySummaries = localAccountingPropertySummaries(units, monthKey);
  const invoiceItems = localAccountingInvoiceItems(units, monthKey);
  const user = getActiveUser();

  els.contentArea.innerHTML = `
    <section class="accounting-hero">
      <div>
        <p class="eyebrow">Corte local</p>
        <h3>${formatMonthLabel(monthKey)}</h3>
        <p class="muted">${user?.name || "Contabilidad local"} - ${visibleProperties().map((property) => property.name).join(", ") || "Sin propiedades asignadas"}</p>
      </div>
      <div class="accounting-hero-metrics">
        <div>
          <span>Esperado</span>
          <strong>${formatCurrency(summary.expectedTotal)}</strong>
        </div>
        <div>
          <span>Conciliado</span>
          <strong>${summary.collectionRate}%</strong>
        </div>
        <div>
          <span>Facturas</span>
          <strong>${summary.readyInvoices}/${summary.totalInvoices}</strong>
        </div>
      </div>
    </section>

    ${units.length ? `
      <div class="local-accounting-layout">
        <div class="local-accounting-main">
          ${localAccountingCollectionsMarkup(rows, monthKey)}
          ${localAccountingPropertyMarkup(propertySummaries)}
        </div>
        <aside class="local-accounting-side">
          ${localAccountingCloseMarkup(summary)}
          ${localAccountingInvoicesQueueMarkup(invoiceItems)}
        </aside>
      </div>
    ` : emptyState("No hay unidades con los filtros actuales.")}
  `;

  bindLocalAccountingActions();
}

function localAccountingRows(units, monthKey) {
  return units
    .map((unit) => {
      const expected = unitTotal(unit);
      const pending = unitPendingTotal(unit, [monthKey]);
      const paid = expected - pending;
      const invoiceItems = localAccountingInvoiceItems([unit], monthKey);

      return {
        unit,
        property: getProperty(unit.propertyId),
        expected,
        paid,
        pending,
        progress: Math.round((paid / Math.max(expected, 1)) * 100),
        readyInvoices: invoiceItems.filter((item) => item.isPaid).length,
        totalInvoices: invoiceItems.length
      };
    })
    .sort((a, b) => b.pending - a.pending || (a.property?.name || "").localeCompare(b.property?.name || ""));
}

function localAccountingInvoiceItems(units, monthKey) {
  return units
    .flatMap((unit) =>
      paymentConcepts
        .filter((concept) => (unit[concept.key] || 0) > 0)
        .map((concept) => {
          const status = getPaymentStatus(unit, monthKey, concept.key);
          return {
            unit,
            property: getProperty(unit.propertyId),
            concept,
            monthKey,
            amount: unit[concept.key] || 0,
            status,
            isPaid: status === "paid"
          };
        })
    )
    .sort((a, b) => Number(a.isPaid) - Number(b.isPaid) || b.amount - a.amount);
}

function localAccountingPropertySummaries(units, monthKey) {
  return visibleProperties()
    .map((property) => {
      const propertyUnits = units.filter((unit) => unit.propertyId === property.id);
      const expected = propertyUnits.reduce((sum, unit) => sum + unitTotal(unit), 0);
      const pending = propertyUnits.reduce((sum, unit) => sum + unitPendingTotal(unit, [monthKey]), 0);
      const paid = expected - pending;

      return {
        property,
        units: propertyUnits.length,
        expected,
        paid,
        pending,
        progress: Math.round((paid / Math.max(expected, 1)) * 100)
      };
    })
    .filter((item) => item.units > 0);
}

function localAccountingCollectionsMarkup(rows, monthKey) {
  return `
    <section class="table-panel">
      <div class="month-panel-header">
        <div>
          <p class="eyebrow">Bandeja de cobros</p>
          <h3>Ingresos por unidad</h3>
          <p class="muted">${formatMonthLabel(monthKey)}</p>
        </div>
        <div class="section-actions">
          <button class="action-button" type="button" data-action="download-report">
            <span data-icon="download" aria-hidden="true"></span>
            Reporte
          </button>
          <button class="secondary-button" type="button" data-action="reset-demo">
            <span data-icon="filter" aria-hidden="true"></span>
            Restaurar datos demo
          </button>
        </div>
      </div>
      <div class="table-scroll">
        <table class="local-accounting-table">
          <thead>
            <tr>
              <th>Propiedad</th>
              <th>Unidad</th>
              <th>Nombre del arrendatario</th>
              <th>Esperado</th>
              <th>Ingresado</th>
              <th>Pendiente</th>
              <th>Avance</th>
              <th>Facturas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${rows.map((row) => localAccountingCollectionRowMarkup(row)).join("")}
          </tbody>
        </table>
      </div>
    </section>
  `;
}

function localAccountingCollectionRowMarkup(row) {
  const status = localAccountingStatus(row);

  return `
    <tr>
      <td class="primary-cell">
        <strong>${row.property?.name || "Sin propiedad"}</strong>
        <small>${row.property?.type || ""} - ${row.property?.location || ""}</small>
      </td>
      <td><strong>${row.unit.unit}</strong></td>
      <td class="stacked">
        <strong>${row.unit.tenant}</strong>
        <small>${tenantEmail(row.unit)}</small>
      </td>
      <td><strong>${formatCurrency(row.expected)}</strong></td>
      <td><strong>${formatCurrency(row.paid)}</strong></td>
      <td><strong>${formatCurrency(row.pending)}</strong></td>
      <td>
        <div class="progress-cell">
          <div class="progress-track" aria-hidden="true">
            <span style="width: ${row.progress}%"></span>
          </div>
          <small>${row.progress}%</small>
        </div>
      </td>
      <td>
        <span class="status-pill ${row.readyInvoices === row.totalInvoices ? "status-paid" : "status-pending"}">
          ${row.readyInvoices}/${row.totalInvoices}
        </span>
      </td>
      <td>
        <div class="section-actions">
          <button class="secondary-button" type="button" data-local-detail="${row.unit.id}">
            <span data-icon="eye" aria-hidden="true"></span>
            Detalle
          </button>
          ${row.pending > 0 ? `
            <button class="action-button" type="button" data-local-pay-unit="${row.unit.id}">
              <span data-icon="creditCard" aria-hidden="true"></span>
              Registrar
            </button>
          ` : `
            <span class="status-pill ${status.className}">
              ${renderIcon(status.icon)}
              ${status.label}
            </span>
          `}
        </div>
      </td>
    </tr>
  `;
}

function localAccountingStatus(row) {
  if (row.pending === 0) return { label: "Conciliado", className: "status-paid", icon: "checkCircle" };
  if (row.paid > 0) return { label: "Parcial", className: "status-pending", icon: "alertCircle" };
  return { label: "Pendiente", className: "status-danger", icon: "alertCircle" };
}

function localAccountingPropertyMarkup(items) {
  return `
    <section class="accounting-panel">
      <div class="accounting-panel-header">
        <div>
          <p class="eyebrow">Conciliacion</p>
          <h3>Propiedades asignadas</h3>
        </div>
      </div>
      <div class="local-property-list">
        ${items.map((item) => localAccountingPropertyRowMarkup(item)).join("")}
      </div>
    </section>
  `;
}

function localAccountingPropertyRowMarkup(item) {
  const isClosed = item.pending === 0;

  return `
    <article class="local-property-row">
      <div>
        <strong>${item.property.name}</strong>
        <small>${item.units} unidades - ${formatCurrency(item.expected)} esperado</small>
      </div>
      <div class="progress-cell">
        <div class="progress-track" aria-hidden="true">
          <span style="width: ${item.progress}%"></span>
        </div>
        <small>${formatCurrency(item.pending)} pendiente</small>
      </div>
      <button class="${isClosed ? "secondary-button" : "action-button"}" type="button" data-local-concile-property="${item.property.id}">
        <span data-icon="${isClosed ? "checkCircle" : "alertCircle"}" aria-hidden="true"></span>
        ${isClosed ? "Conciliada" : "Conciliar"}
      </button>
    </article>
  `;
}

function localAccountingCloseMarkup(summary) {
  const checks = [
    {
      label: "Cobranza del periodo",
      detail: `${summary.collectionRate}% conciliado`,
      done: summary.pendingTotal === 0
    },
    {
      label: "Facturas emitibles",
      detail: `${summary.readyInvoices}/${summary.totalInvoices} listas`,
      done: summary.readyInvoices === summary.totalInvoices
    },
    {
      label: "Atrasos vencidos",
      detail: formatCurrency(summary.overduePending),
      done: summary.overduePending === 0
    }
  ];

  return `
    <section class="accounting-panel">
      <div class="accounting-panel-header">
        <div>
          <p class="eyebrow">Cierre</p>
          <h3>Corte local</h3>
        </div>
      </div>
      <div class="closing-checklist">
        ${checks.map((item) => `
          <article class="closing-row ${item.done ? "is-done" : ""}">
            <span>${renderIcon(item.done ? "checkCircle" : "alertCircle")}</span>
            <div>
              <strong>${item.label}</strong>
              <small>${item.detail}</small>
            </div>
          </article>
        `).join("")}
      </div>
      <div class="accounting-panel-footer">
        <button class="action-button" type="button" data-local-close>
          <span data-icon="shield" aria-hidden="true"></span>
          Enviar corte
        </button>
      </div>
    </section>
  `;
}

function localAccountingInvoicesQueueMarkup(invoiceItems) {
  const items = invoiceItems.slice(0, 8);

  return `
    <section class="accounting-panel">
      <div class="accounting-panel-header">
        <div>
          <p class="eyebrow">Facturacion</p>
          <h3>Cola local</h3>
        </div>
        <button class="icon-button" type="button" title="Abrir facturas" aria-label="Abrir facturas" data-local-target="invoices">
          <span data-icon="receipt" aria-hidden="true"></span>
        </button>
      </div>
      <div class="invoice-queue">
        ${items.map((item) => `
          <article class="invoice-queue-row">
            <div>
              <strong>${item.unit.unit}</strong>
              <small>${item.concept.label} - ${formatCurrency(item.amount)}</small>
            </div>
            ${item.isPaid ? `
              <button class="icon-button" type="button" title="Descargar factura" aria-label="Descargar factura" data-download-invoice="${item.concept.key}" data-concept="${item.concept.key}" data-payment-month="${item.monthKey}" data-unit-id="${item.unit.id}">
                <span data-icon="download" aria-hidden="true"></span>
              </button>
            ` : `
              <button class="secondary-button" type="button" data-local-pay-concept="${item.concept.key}" data-concept="${item.concept.key}" data-payment-month="${item.monthKey}" data-unit-id="${item.unit.id}">
                <span data-icon="creditCard" aria-hidden="true"></span>
                Validar
              </button>
            `}
          </article>
        `).join("")}
      </div>
    </section>
  `;
}

function bindLocalAccountingActions() {
  bindReportActions();

  els.contentArea.querySelector("[data-action='reset-demo']")?.addEventListener("click", resetDemoData);

  els.contentArea.querySelectorAll("[data-local-detail]").forEach((button) => {
    button.addEventListener("click", () => openLocalAccountingDetail(button.dataset.localDetail));
  });

  els.contentArea.querySelectorAll("[data-local-pay-unit]").forEach((button) => {
    button.addEventListener("click", () => registerLocalUnitPayment(button.dataset.localPayUnit));
  });

  els.contentArea.querySelectorAll("[data-local-pay-concept]").forEach((button) => {
    button.addEventListener("click", () => payInvoice(button.dataset.unitId, button.dataset.concept, button.dataset.paymentMonth));
  });

  els.contentArea.querySelectorAll("[data-download-invoice]").forEach((button) => {
    button.addEventListener("click", () => downloadInvoice(button.dataset.unitId, button.dataset.concept, button.dataset.paymentMonth));
  });

  els.contentArea.querySelectorAll("[data-local-concile-property]").forEach((button) => {
    button.addEventListener("click", () => reconcileLocalProperty(button.dataset.localConcileProperty));
  });

  els.contentArea.querySelector("[data-local-close]")?.addEventListener("click", sendLocalAccountingClose);

  els.contentArea.querySelectorAll("[data-local-target]").forEach((button) => {
    button.addEventListener("click", () => {
      view.activeTab = button.dataset.localTarget;
      render();
    });
  });
}

function registerLocalUnitPayment(unitId) {
  const unit = state.units.find((item) => item.id === unitId);
  const monthKey = currentMonthKey();
  if (!unit) return;

  const pendingConcepts = paymentConcepts.filter((concept) =>
    (unit[concept.key] || 0) > 0 && getPaymentStatus(unit, monthKey, concept.key) === "pending"
  );

  if (!pendingConcepts.length) {
    toast("La unidad ya esta conciliada.");
    return;
  }

  pendingConcepts.forEach((concept) => setPaymentStatus(unit, monthKey, concept.key, "paid"));
  saveState();
  render();
  toast("Pago registrado y facturas disponibles.");
}

function reconcileLocalProperty(propertyId) {
  const propertyUnits = visibleUnits({ ignoreStatusFilter: true }).filter((unit) => unit.propertyId === propertyId);
  const pending = propertyUnits.reduce((sum, unit) => sum + unitPendingTotal(unit, [currentMonthKey()]), 0);

  toast(pending > 0 ? "La propiedad aun tiene importes pendientes." : "Propiedad conciliada localmente.");
}

function sendLocalAccountingClose() {
  const summary = localAccountingSummary(visibleUnits({ ignoreStatusFilter: true }), currentMonthKey());
  if (!summary.units) {
    toast("No hay unidades para enviar en este corte.");
    return;
  }

  toast(summary.pendingTotal > 0 ? "Corte preparado con pendientes visibles." : "Corte local enviado a Contabilidad General.");
}

function openLocalAccountingDetail(unitId) {
  const unit = state.units.find((item) => item.id === unitId);
  const property = getProperty(unit?.propertyId);
  const monthKey = currentMonthKey();
  if (!unit) return;

  const pending = unitPendingTotal(unit, [monthKey]);
  const paid = unitTotal(unit) - pending;

  els.modalEyebrow.textContent = "Detalle contable local";
  els.modalTitle.textContent = `${unit.unit} - ${unit.tenant}`;
  els.modalBody.innerHTML = `
    <div class="modal-grid">
      <div class="detail-box">
        <span>Propiedad</span>
        <strong>${property?.name || "Sin propiedad"}</strong>
      </div>
      <div class="detail-box">
        <span>Periodo</span>
        <strong>${formatMonthLabel(monthKey)}</strong>
      </div>
      <div class="detail-box">
        <span>Ingresado</span>
        <strong>${formatCurrency(paid)}</strong>
      </div>
      <div class="detail-box">
        <span>Pendiente</span>
        <strong>${formatCurrency(pending)}</strong>
      </div>
    </div>
    <div class="table-panel modal-table-panel">
      <div class="table-scroll">
        <table class="concept-detail-table">
          <thead>
            <tr>
              <th>Concepto</th>
              <th>Importe</th>
              <th>Estado</th>
              <th>Factura</th>
            </tr>
          </thead>
          <tbody>
            ${paymentConcepts
              .filter((concept) => (unit[concept.key] || 0) > 0)
              .map((concept) => localAccountingConceptDetailRow(unit, concept, monthKey))
              .join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;

  els.modalBody.querySelectorAll("[data-pay-invoice]").forEach((button) => {
    button.addEventListener("click", () => {
      payInvoice(button.dataset.unitId, button.dataset.concept, button.dataset.paymentMonth);
      closeModal();
    });
  });

  els.modalBody.querySelectorAll("[data-download-invoice]").forEach((button) => {
    button.addEventListener("click", () => downloadInvoice(button.dataset.unitId, button.dataset.concept, button.dataset.paymentMonth));
  });

  injectIcons(els.modalBody);
  openModal();
}

function localAccountingConceptDetailRow(unit, concept, monthKey) {
  const isPaid = getPaymentStatus(unit, monthKey, concept.key) === "paid";

  return `
    <tr>
      <td>${concept.label}</td>
      <td><strong>${formatCurrency(unit[concept.key])}</strong></td>
      <td>
        <span class="status-pill ${isPaid ? "status-paid" : "status-pending"}">
          ${renderIcon(isPaid ? "checkCircle" : "alertCircle")}
          ${isPaid ? "Pagado" : "Por pagar"}
        </span>
      </td>
      <td>
        ${isPaid ? `
          <button class="secondary-button" type="button" data-download-invoice="${concept.key}" data-concept="${concept.key}" data-payment-month="${monthKey}" data-unit-id="${unit.id}">
            <span data-icon="download" aria-hidden="true"></span>
            Factura
          </button>
        ` : `
          <button class="action-button" type="button" data-pay-invoice="${concept.key}" data-concept="${concept.key}" data-payment-month="${monthKey}" data-unit-id="${unit.id}">
            <span data-icon="creditCard" aria-hidden="true"></span>
            Registrar
          </button>
        `}
      </td>
    </tr>
  `;
}

function tenantOpenItems(units = visibleUnits({ ignoreStatusFilter: true })) {
  const currentKey = currentMonthKey();
  return units
    .flatMap((unit) => allLedgerMonthKeys([unit])
      .filter((monthKey) => monthKey <= currentKey)
      .flatMap((monthKey) => paymentConcepts
        .filter((concept) => (unit[concept.key] || 0) > 0 && getPaymentStatus(unit, monthKey, concept.key) === "pending")
        .map((concept) => ({
          unit,
          property: getProperty(unit.propertyId),
          concept,
          monthKey,
          amount: unit[concept.key] || 0
        }))))
    .sort((a, b) => a.monthKey.localeCompare(b.monthKey) || b.amount - a.amount);
}

function tenantPaidItems(units = visibleUnits({ ignoreStatusFilter: true })) {
  return units
    .flatMap((unit) => recentMonthKeys()
      .flatMap((monthKey) => paymentConcepts
        .filter((concept) => (unit[concept.key] || 0) > 0 && getPaymentStatus(unit, monthKey, concept.key) === "paid")
        .map((concept) => ({
          unit,
          property: getProperty(unit.propertyId),
          concept,
          monthKey,
          amount: unit[concept.key] || 0
        }))))
    .sort((a, b) => b.monthKey.localeCompare(a.monthKey));
}

function renderTenantDashboard() {
  const units = visibleUnits({ ignoreStatusFilter: true });
  const openItems = tenantOpenItems(units);
  const paidItems = tenantPaidItems(units).slice(0, 6);
  const profile = tenantRows().find((tenant) => tenant.userId === view.userId);
  const dueTotal = openItems.reduce((sum, item) => sum + item.amount, 0);
  const nextDue = openItems[0];

  els.contentArea.innerHTML = `
    <div class="tenant-dashboard">
      <section class="tenant-panel tenant-panel-wide">
        <div class="section-header">
          <div>
            <p class="eyebrow">Saldo actual</p>
            <h3>${formatCurrency(dueTotal)}</h3>
            <p class="muted">${nextDue ? `${nextDue.concept.label} de ${formatMonthLabel(nextDue.monthKey)} en ${nextDue.unit.unit}` : "Sin conceptos pendientes registrados."}</p>
          </div>
          <div class="section-actions">
            <button class="action-button" type="button" data-tenant-target="invoices" data-status-filter="pending">
              <span data-icon="creditCard" aria-hidden="true"></span>
              Pagar
            </button>
            <button class="secondary-button" type="button" data-tenant-target="contracts">
              <span data-icon="fileText" aria-hidden="true"></span>
              Contratos
            </button>
          </div>
        </div>
        ${tenantOpenItemsMarkup(openItems)}
      </section>

      <section class="tenant-panel">
        <div class="section-header">
          <div>
            <p class="eyebrow">Perfil fiscal</p>
            <h3>${profile?.name || getActiveUser()?.name || "Arrendatario"}</h3>
          </div>
        </div>
        <div class="tenant-profile-list">
          <div><span>RFC</span><strong>${profile?.rfc || "Sin RFC"}</strong></div>
          <div><span>Contacto</span><strong>${profile?.contact || getActiveUser()?.name || "Sin contacto"}</strong></div>
          <div><span>Correo</span><strong>${profile?.email || getActiveUser()?.email || "Sin correo"}</strong></div>
          <div><span>Estatus</span><strong>${profile?.status || "Activo"}</strong></div>
        </div>
      </section>

      <section class="tenant-panel tenant-panel-wide">
        <div class="section-header">
          <div>
            <p class="eyebrow">Unidades rentadas</p>
            <h3>Espacios activos</h3>
          </div>
          <button class="secondary-button" type="button" data-tenant-target="units">
            <span data-icon="building" aria-hidden="true"></span>
            Ver unidades
          </button>
        </div>
        ${tenantUnitCardsMarkup(units)}
      </section>

      <section class="tenant-panel">
        <div class="section-header">
          <div>
            <p class="eyebrow">Facturas</p>
            <h3>Disponibles</h3>
          </div>
          <button class="secondary-button" type="button" data-tenant-target="invoices">
            <span data-icon="receipt" aria-hidden="true"></span>
            Ver todas
          </button>
        </div>
        ${tenantPaidItemsMarkup(paidItems)}
      </section>
    </div>
  `;

  bindTenantDashboardActions();
}

function tenantOpenItemsMarkup(items) {
  if (!items.length) {
    return `
      <div class="empty-state compact-empty">
        <p class="eyebrow">Al corriente</p>
        <h3>No hay conceptos pendientes.</h3>
      </div>
    `;
  }

  return `
    <div class="tenant-payment-list">
      ${items.slice(0, 8).map((item) => `
        <article class="tenant-payment-row">
          <div>
            <strong>${item.concept.label}</strong>
            <small>${formatMonthShort(item.monthKey)} - ${item.property?.name || "Sin propiedad"} - ${item.unit.unit}</small>
          </div>
          <strong>${formatCurrency(item.amount)}</strong>
          <button class="action-button" type="button" data-tenant-pay="${item.concept.key}" data-concept="${item.concept.key}" data-payment-month="${item.monthKey}" data-unit-id="${item.unit.id}">
            <span data-icon="creditCard" aria-hidden="true"></span>
            Pagar
          </button>
        </article>
      `).join("")}
    </div>
  `;
}

function tenantUnitCardsMarkup(units) {
  if (!units.length) return emptyState("No hay unidades asignadas.");

  return `
    <div class="tenant-unit-grid">
      ${units.map((unit) => {
        const property = getProperty(unit.propertyId);
        const status = contractStatus(unit);
        return `
          <article class="tenant-unit-card">
            <div class="property-icon ${property?.type === "Bodega" ? "is-industrial" : property?.type === "Vivienda" ? "is-housing" : ""}" aria-hidden="true">
              ${renderIcon(property?.type === "Vivienda" ? "home" : "building")}
            </div>
            <div>
              <p class="eyebrow">${property?.type || "Propiedad"}</p>
              <h3>${unit.unit}</h3>
              <p class="muted">${property?.name || "Sin propiedad"} - ${property?.location || ""}</p>
            </div>
            <div class="tenant-unit-stats">
              <div><span>Renta</span><strong>${formatCurrency(unit.monthlyRent)}</strong></div>
              <div><span>Contrato</span><strong><span class="status-pill ${status.className}">${status.label}</span></strong></div>
            </div>
          </article>
        `;
      }).join("")}
    </div>
  `;
}

function tenantPaidItemsMarkup(items) {
  if (!items.length) return emptyState("Aun no hay facturas pagadas en la ventana reciente.");

  return `
    <div class="tenant-invoice-list">
      ${items.map((item) => `
        <article class="tenant-invoice-row">
          <div>
            <strong>${item.concept.label}</strong>
            <small>${formatMonthShort(item.monthKey)} - ${item.unit.unit}</small>
          </div>
          <button class="secondary-button" type="button" data-tenant-download="${item.concept.key}" data-concept="${item.concept.key}" data-payment-month="${item.monthKey}" data-unit-id="${item.unit.id}">
            <span data-icon="download" aria-hidden="true"></span>
            Factura
          </button>
        </article>
      `).join("")}
    </div>
  `;
}

function bindTenantDashboardActions() {
  els.contentArea.querySelectorAll("[data-tenant-target]").forEach((button) => {
    button.addEventListener("click", () => {
      view.activeTab = button.dataset.tenantTarget;
      view.statusFilter = button.dataset.statusFilter || "all";
      if (els.statusFilter) els.statusFilter.value = view.statusFilter;
      render();
    });
  });

  els.contentArea.querySelectorAll("[data-tenant-pay]").forEach((button) => {
    button.addEventListener("click", () => {
      payInvoice(button.dataset.unitId, button.dataset.concept, button.dataset.paymentMonth);
    });
  });

  els.contentArea.querySelectorAll("[data-tenant-download]").forEach((button) => {
    button.addEventListener("click", () => {
      downloadInvoice(button.dataset.unitId, button.dataset.concept, button.dataset.paymentMonth);
    });
  });
}

function superadminStats() {
  const units = visibleUnits({ ignoreStatusFilter: true });
  const periodKeys = recentMonthKeys();
  const organizations = state.organizations || [];
  const modules = state.platformModules || [];
  const paid = units.reduce((sum, unit) => sum + unitPaidTotal(unit, periodKeys), 0);
  const pending = units.reduce((sum, unit) => sum + unitPendingTotal(unit, periodKeys), 0);
  const contractsAtRisk = units.filter((unit) => contractStatus(unit).kind !== "active").length;
  const tenantsWithoutAccess = visibleTenantRows().filter((tenant) => !tenant.hasPortalAccess).length;
  const billingAlerts = organizations.filter((organization) =>
    ["Por facturar", "Vencido", "En revision"].includes(organization.billingStatus)
  ).length;
  const moduleAlerts = modules.filter((module) => module.status !== "Activo" || module.risk === "Atencion").length;

  return {
    organizations: organizations.length,
    activeOrganizations: organizations.filter((organization) => organization.status === "Activa").length,
    platformRevenue: organizations.reduce((sum, organization) =>
      organization.status === "Suspendida" ? sum : sum + (organization.monthlyFee || 0), 0),
    properties: visibleProperties().length,
    units: units.length,
    occupiedUnits: units.filter((unit) => unit.tenant !== "Disponible").length,
    users: state.users.length,
    monthlyCollection: units.reduce((sum, unit) => sum + unitTotal(unit), 0),
    paid,
    pending,
    contractsAtRisk,
    tenantsWithoutAccess,
    billingAlerts,
    moduleAlerts,
    alerts: contractsAtRisk + tenantsWithoutAccess + billingAlerts + moduleAlerts
  };
}

function renderSuperadminDashboard() {
  if (view.roleId !== "superadmin") {
    els.contentArea.innerHTML = emptyState("Este centro de mando solo esta disponible para Superadministrador.");
    return;
  }

  const stats = superadminStats();
  const health = superadminHealth(stats);
  const units = visibleUnits({ ignoreStatusFilter: true });
  const priorityUnits = units
    .map((unit) => ({ unit, pending: unitPendingTotal(unit, recentMonthKeys()) }))
    .filter((item) => item.pending > 0)
    .sort((a, b) => b.pending - a.pending);
  const contractWarnings = units
    .filter((unit) => contractStatus(unit).kind !== "active")
    .sort((a, b) => a.contractEnd.localeCompare(b.contractEnd));
  const tenantsWithoutAccess = visibleTenantRows().filter((tenant) => !tenant.hasPortalAccess);

  els.contentArea.innerHTML = `
    <section class="superadmin-command">
      <div class="superadmin-command-main">
        <p class="eyebrow">Superadministracion</p>
        <h3>Control total de Rentas 360</h3>
        <p class="muted">Vista consolidada de cuentas SaaS, cartera administrada, usuarios, permisos, cobranza, contratos, modulos y riesgos operativos.</p>
        <div class="superadmin-actions">
          <button class="action-button" type="button" data-superadmin-target="users">
            <span data-icon="shield" aria-hidden="true"></span>
            Usuarios
          </button>
          <button class="secondary-button" type="button" data-superadmin-target="invoices" data-status-filter="pending">
            <span data-icon="receipt" aria-hidden="true"></span>
            Cobranza
          </button>
          <button class="secondary-button" type="button" data-superadmin-target="contracts">
            <span data-icon="scale" aria-hidden="true"></span>
            Contratos
          </button>
        </div>
      </div>
      <div class="superadmin-command-aside">
        <span class="status-pill ${health.className}">${health.label}</span>
        <div class="command-stat-row">
          <span>Ocupacion</span>
          <strong>${percentage(stats.occupiedUnits, stats.units)}</strong>
        </div>
        <div class="command-stat-row">
          <span>Eficiencia de cobro</span>
          <strong>${percentage(stats.paid, stats.paid + stats.pending)}</strong>
        </div>
        <div class="command-stat-row">
          <span>Usuarios activos</span>
          <strong>${stats.users}</strong>
        </div>
      </div>
    </section>

    <div class="superadmin-grid">
      <section class="superadmin-section superadmin-section-wide">
        <div class="section-header">
          <div>
            <p class="eyebrow">Cuentas SaaS</p>
            <h3>Clientes, planes y cartera asignada</h3>
            <p class="muted">El Superadministrador supervisa las cuentas, su plan, responsable, facturacion y propiedades dentro de cada organizacion.</p>
          </div>
          <button class="secondary-button" type="button" data-superadmin-target="properties">
            <span data-icon="home" aria-hidden="true"></span>
            Propiedades
          </button>
        </div>
        <div class="organization-grid">
          ${(state.organizations || []).map((organization) => organizationCardMarkup(organization)).join("")}
        </div>
      </section>

      <section class="superadmin-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Prioridades</p>
            <h3>Riesgos abiertos</h3>
          </div>
        </div>
        ${priorityListMarkup(priorityUnits, contractWarnings, tenantsWithoutAccess)}
      </section>

      <section class="superadmin-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Gobierno</p>
            <h3>Modulos globales</h3>
          </div>
        </div>
        <div class="module-list">
          ${(state.platformModules || []).map((module) => moduleRowMarkup(module)).join("")}
        </div>
      </section>

      <section class="superadmin-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Permisos</p>
            <h3>Roles del sistema</h3>
          </div>
          <button class="secondary-button" type="button" data-superadmin-target="users">
            <span data-icon="users" aria-hidden="true"></span>
            Accesos
          </button>
        </div>
        ${roleGovernanceMarkup()}
      </section>

      <section class="superadmin-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Auditoria</p>
            <h3>Actividad reciente</h3>
          </div>
        </div>
        ${auditTimelineMarkup(stats)}
      </section>
    </div>
  `;

  bindSuperadminActions();
}

function percentage(value, total) {
  if (!total) return "0%";
  return `${Math.round((value / total) * 100)}%`;
}

function superadminHealth(stats) {
  if (stats.alerts >= 8) return { label: "Atencion alta", className: "status-danger" };
  if (stats.alerts >= 3) return { label: "Atencion operativa", className: "status-pending" };
  return { label: "Operacion estable", className: "status-paid" };
}

function organizationUnits(organization) {
  const propertyIds = new Set(organization.propertyIds || []);
  return state.units.filter((unit) => propertyIds.has(unit.propertyId));
}

function organizationCardMarkup(organization) {
  const units = organizationUnits(organization);
  const pending = units.reduce((sum, unit) => sum + unitPendingTotal(unit, recentMonthKeys()), 0);
  const occupied = units.filter((unit) => unit.tenant !== "Disponible").length;
  const health = organizationHealth(organization, pending);
  const admin = getUser(organization.adminUserId);
  const statusClass = organization.status === "Activa" ? "status-paid" : "status-pending";

  return `
    <article class="organization-card">
      <header>
        <div>
          <p class="eyebrow">${organization.plan}</p>
          <h4>${organization.name}</h4>
        </div>
        <span class="status-pill ${statusClass}">${organization.status}</span>
      </header>
      <div class="organization-health">
        <span class="status-pill ${health.className}">${health.label}</span>
        <strong>${formatCurrency(organization.monthlyFee)}</strong>
      </div>
      <div class="organization-stats">
        <div>
          <span>Propiedades</span>
          <strong>${organization.propertyIds.length}</strong>
        </div>
        <div>
          <span>Unidades</span>
          <strong>${units.length}</strong>
        </div>
        <div>
          <span>Ocupacion</span>
          <strong>${percentage(occupied, units.length)}</strong>
        </div>
        <div>
          <span>Por cobrar</span>
          <strong>${formatCurrency(pending)}</strong>
        </div>
      </div>
      <p class="muted">Administrador: ${admin?.name || "Sin administrador"}.</p>
      <div class="section-actions organization-actions">
        <button class="secondary-button" type="button" data-org-detail="${organization.id}">
          <span data-icon="eye" aria-hidden="true"></span>
          Detalle
        </button>
        ${organization.propertyIds.length ? `
          <button class="secondary-button" type="button" data-superadmin-target="units" data-property-id="${organization.propertyIds[0]}">
            <span data-icon="building" aria-hidden="true"></span>
            Cartera
          </button>
        ` : ""}
        ${organization.billingStatus !== "Al corriente" && organization.monthlyFee > 0 ? `
          <button class="action-button" type="button" data-billing-clear="${organization.id}">
            <span data-icon="creditCard" aria-hidden="true"></span>
            Facturar
          </button>
        ` : ""}
      </div>
    </article>
  `;
}

function organizationHealth(organization, pending) {
  if (organization.billingStatus === "Vencido" || pending >= 120000) {
    return { label: "Riesgo alto", className: "status-danger" };
  }
  if (organization.billingStatus !== "Al corriente" || pending > 0 || organization.status !== "Activa") {
    return { label: "Seguimiento", className: "status-pending" };
  }
  return { label: "Sano", className: "status-paid" };
}

function priorityListMarkup(priorityUnits, contractWarnings, tenantsWithoutAccess) {
  const billingAlerts = (state.organizations || [])
    .filter((organization) => organization.billingStatus !== "Al corriente" && organization.monthlyFee > 0)
    .map((organization) => ({
      type: "Facturacion SaaS",
      title: organization.name,
      detail: `${organization.billingStatus} - ${formatCurrency(organization.monthlyFee)}`,
      icon: "creditCard",
      target: "superadmin_dashboard"
    }));
  const collectionAlerts = priorityUnits.slice(0, 3).map(({ unit, pending }) => ({
    type: "Cobranza",
    title: `${unit.unit} - ${unit.tenant}`,
    detail: `${getProperty(unit.propertyId)?.name || "Sin propiedad"} - ${formatCurrency(pending)}`,
    icon: "receipt",
    target: "invoices",
    statusFilter: "pending",
    propertyId: unit.propertyId
  }));
  const contractAlerts = contractWarnings.slice(0, 2).map((unit) => ({
    type: "Legal",
    title: `${unit.unit} - ${unit.tenant}`,
    detail: `${contractStatus(unit).label} el ${formatDate(unit.contractEnd)}`,
    icon: "scale",
    target: "contracts",
    propertyId: unit.propertyId
  }));
  const accessAlerts = tenantsWithoutAccess.slice(0, 2).map((tenant) => ({
    type: "Acceso",
    title: tenant.name,
    detail: tenant.propertiesLabel,
    icon: "lock",
    target: "tenants"
  }));
  const items = [...billingAlerts, ...collectionAlerts, ...contractAlerts, ...accessAlerts].slice(0, 8);

  if (!items.length) return emptyState("No hay riesgos abiertos con los filtros actuales.");

  return `
    <div class="priority-list">
      ${items.map((item) => `
        <article class="priority-row">
          <div class="priority-icon">${renderIcon(item.icon)}</div>
          <div>
            <span>${item.type}</span>
            <strong>${item.title}</strong>
            <small>${item.detail}</small>
          </div>
          <button class="icon-button" type="button" title="Abrir" aria-label="Abrir" data-superadmin-target="${item.target}" ${item.propertyId ? `data-property-id="${item.propertyId}"` : ""} ${item.statusFilter ? `data-status-filter="${item.statusFilter}"` : ""}>
            <span data-icon="eye" aria-hidden="true"></span>
          </button>
        </article>
      `).join("")}
    </div>
  `;
}

function moduleRowMarkup(module) {
  const riskClass = module.risk === "Atencion" ? "status-pending" : "status-paid";
  const statusClass = module.status === "Activo" ? "status-paid" : "status-neutral";
  const nextLabel = module.status === "Activo" ? "Pausar" : "Activar";

  return `
    <article class="module-row">
      <div class="module-icon">${renderIcon(module.risk === "Atencion" ? "alertCircle" : "settings")}</div>
      <div>
        <strong>${module.name}</strong>
        <small>${module.coverage}</small>
        <div class="module-badges">
          <span class="status-pill ${statusClass}">${module.status}</span>
          <span class="status-pill ${riskClass}">${module.risk}</span>
        </div>
      </div>
      <button class="secondary-button" type="button" data-module-toggle="${module.id}">${nextLabel}</button>
    </article>
  `;
}

function roleGovernanceMarkup() {
  const roleNotes = {
    superadmin: "Control SaaS, datos globales y gobierno.",
    admin: "Operacion global por cliente.",
    project_manager: "Propiedades asignadas y arrendatarios.",
    local_accounting: "Cobranza local y facturas.",
    general_accounting: "Conciliacion de todas las propiedades.",
    legal: "Contratos y documentos.",
    tenant: "Pagos, facturas y contratos propios."
  };

  return `
    <div class="role-governance-list">
      ${roles.map((role) => {
        const count = state.users.filter((user) => user.role === role.id).length;
        return `
          <article class="role-governance-row">
            <div>
              <strong>${role.name}</strong>
              <small>${roleNotes[role.id] || role.description}</small>
            </div>
            <span class="role-count">${count}</span>
          </article>
        `;
      }).join("")}
    </div>
  `;
}

function auditTimelineMarkup(stats) {
  const items = [
    {
      label: "Gobierno de accesos",
      detail: `${stats.users} usuarios demo sincronizados con roles y propiedades.`,
      icon: "shield"
    },
    {
      label: "Cobranza consolidada",
      detail: `${formatCurrency(stats.pending)} por cobrar en la ventana operativa.`,
      icon: "receipt"
    },
    {
      label: "Control legal",
      detail: `${stats.contractsAtRisk} contratos vencidos o por vencer.`,
      icon: "scale"
    },
    {
      label: "Portal arrendatario",
      detail: `${stats.tenantsWithoutAccess} arrendatarios pendientes de acceso.`,
      icon: "lock"
    }
  ];

  return `
    <div class="audit-list">
      ${items.map((item) => `
        <article class="audit-row">
          <div class="audit-icon">${renderIcon(item.icon)}</div>
          <div>
            <strong>${item.label}</strong>
            <small>${item.detail}</small>
          </div>
        </article>
      `).join("")}
    </div>
  `;
}

function bindSuperadminActions() {
  els.contentArea.querySelectorAll("[data-superadmin-target]").forEach((button) => {
    button.addEventListener("click", () => {
      const target = button.dataset.superadminTarget;
      if (!target) return;

      view.activeTab = target;
      view.propertyFilter = button.dataset.propertyId || "all";
      view.statusFilter = button.dataset.statusFilter || "all";
      if (els.statusFilter) els.statusFilter.value = view.statusFilter;
      render();
    });
  });

  els.contentArea.querySelectorAll("[data-org-detail]").forEach((button) => {
    button.addEventListener("click", () => openOrganizationModal(button.dataset.orgDetail));
  });

  els.contentArea.querySelectorAll("[data-module-toggle]").forEach((button) => {
    button.addEventListener("click", () => {
      const module = (state.platformModules || []).find((item) => item.id === button.dataset.moduleToggle);
      if (!module) return;
      module.status = module.status === "Activo" ? "Pausado" : "Activo";
      module.risk = module.status === "Activo" ? "Normal" : "Atencion";
      saveState();
      render();
      toast(`Modulo ${module.status.toLowerCase()}`);
    });
  });

  els.contentArea.querySelectorAll("[data-billing-clear]").forEach((button) => {
    button.addEventListener("click", () => {
      const organization = (state.organizations || []).find((item) => item.id === button.dataset.billingClear);
      if (!organization) return;
      organization.billingStatus = "Al corriente";
      saveState();
      render();
      toast("Facturacion actualizada");
    });
  });
}

function openOrganizationModal(organizationId) {
  const organization = (state.organizations || []).find((item) => item.id === organizationId);
  if (!organization) return;

  const units = organizationUnits(organization);
  const pending = units.reduce((sum, unit) => sum + unitPendingTotal(unit, recentMonthKeys()), 0);
  const admin = getUser(organization.adminUserId);
  const properties = organization.propertyIds
    .map((propertyId) => getProperty(propertyId)?.name)
    .filter(Boolean)
    .join(", ") || "Sin propiedades asignadas";

  els.modalEyebrow.textContent = "Cuenta SaaS";
  els.modalTitle.textContent = organization.name;
  els.modalBody.innerHTML = `
    <div class="modal-grid">
      <div class="detail-box">
        <span>Plan</span>
        <strong>${organization.plan}</strong>
      </div>
      <div class="detail-box">
        <span>Estatus</span>
        <strong>${organization.status}</strong>
      </div>
      <div class="detail-box">
        <span>Mensualidad</span>
        <strong>${formatCurrency(organization.monthlyFee)}</strong>
      </div>
      <div class="detail-box">
        <span>Facturacion</span>
        <strong>${organization.billingStatus}</strong>
      </div>
      <div class="detail-box">
        <span>Renovacion</span>
        <strong>${formatDate(organization.renewalDate)}</strong>
      </div>
      <div class="detail-box">
        <span>Soporte</span>
        <strong>${organization.supportLevel}</strong>
      </div>
      <div class="detail-box">
        <span>Administrador</span>
        <strong>${admin?.name || "Sin administrador"}</strong>
      </div>
      <div class="detail-box">
        <span>Por cobrar cartera</span>
        <strong>${formatCurrency(pending)}</strong>
      </div>
    </div>
    <div class="detail-box" style="margin-top: 12px;">
      <span>Propiedades</span>
      <p>${properties}</p>
    </div>
    <div class="detail-box" style="margin-top: 12px;">
      <span>Notas</span>
      <p>${organization.notes || "Sin notas registradas."}</p>
    </div>
  `;
  openModal();
}

function renderUnitsTable() {
  const units = visibleUnits();
  const periodKeys = recentMonthKeys();
  const isTenant = view.roleId === "tenant";
  els.contentArea.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">${isTenant ? "Mis rentas" : "Cobranza por unidad"}</p>
        <h3>${isTenant ? "Unidades y pagos por periodo" : "Resumen de atrasos y cobranza mensual"}</h3>
        <p class="muted">${isTenant ? "Consulta cada concepto por mes, paga pendientes y abre tus contratos asociados." : "Primero se muestran los atrasos por concepto; despues, el mes actual y seis meses hacia atras."}</p>
      </div>
      <div class="section-actions">
        ${isTenant ? `
          <button class="action-button" type="button" data-action="tenant-pay-pending">
            <span data-icon="creditCard" aria-hidden="true"></span>
            Pagar pendientes
          </button>
        ` : `
          <button class="action-button" type="button" data-action="download-report">
            <span data-icon="download" aria-hidden="true"></span>
            Reporte
          </button>
        `}
        <button class="secondary-button" type="button" data-action="reset-demo">
          <span data-icon="filter" aria-hidden="true"></span>
          Restaurar datos demo
        </button>
      </div>
    </div>
    ${units.length ? `
      ${arrearsSummaryMarkup(units)}
      <div class="month-stack">
        ${periodKeys.map((monthKey, index) => monthPanelMarkup(units, monthKey, index)).join("")}
      </div>
    ` : emptyState("No hay unidades con los filtros actuales.")}
  `;

  bindUnitActions();
  bindReportActions();
  els.contentArea.querySelector("[data-action='tenant-pay-pending']")?.addEventListener("click", () => {
    view.activeTab = "invoices";
    view.statusFilter = "pending";
    if (els.statusFilter) els.statusFilter.value = "pending";
    render();
  });
  const resetButton = els.contentArea.querySelector("[data-action='reset-demo']");
  resetButton?.addEventListener("click", resetDemoData);
}

function arrearsSummaryMarkup(units) {
  return `
    <section class="table-panel summary-panel">
      <div class="month-panel-header">
        <div>
          <p class="eyebrow">Resumen inicial</p>
          <h3>Atrasos por arrendatario</h3>
          <p class="muted">Cuando no hay meses vencidos pendientes, la unidad queda marcada como Al corriente.</p>
        </div>
      </div>
      <div class="table-scroll">
        <table class="summary-table">
          <thead>
            <tr>
              <th>Propiedad</th>
              <th>Unidad</th>
              <th>Nombre del arrendatario</th>
              <th>Estado</th>
              ${paymentConcepts.map((concept) => `<th>${concept.label}</th>`).join("")}
            </tr>
          </thead>
          <tbody>
            ${units.map((unit) => arrearsSummaryRowMarkup(unit)).join("")}
          </tbody>
        </table>
      </div>
    </section>
  `;
}

function arrearsSummaryRowMarkup(unit) {
  const property = getProperty(unit.propertyId);
  const arrears = arrearsByConcept(unit);
  const isCurrent = paymentConcepts.every((concept) => arrears[concept.key].length === 0);

  return `
    <tr>
      <td class="primary-cell">
        <strong>${property?.name || "Sin propiedad"}</strong>
        <small>${property?.type || ""}</small>
      </td>
      <td><strong>${unit.unit}</strong></td>
      <td>${unit.tenant}</td>
      <td>
        <span class="status-pill ${isCurrent ? "status-paid" : "status-danger"}">
          ${renderIcon(isCurrent ? "checkCircle" : "alertCircle")}
          ${isCurrent ? "Al corriente" : "Con atraso"}
        </span>
      </td>
      ${paymentConcepts.map((concept) => `
        <td>${arrears[concept.key].length ? monthChipList(arrears[concept.key]) : '<span class="status-pill status-paid">Al corriente</span>'}</td>
      `).join("")}
    </tr>
  `;
}

function arrearsByConcept(unit) {
  const currentKey = currentMonthKey();
  const monthKeys = allLedgerMonthKeys([unit]).filter((monthKey) => monthKey < currentKey);

  return paymentConcepts.reduce((result, concept) => {
    result[concept.key] = monthKeys.filter((monthKey) =>
      (unit[concept.key] || 0) > 0 && getPaymentStatus(unit, monthKey, concept.key) === "pending"
    );
    return result;
  }, {});
}

function monthChipList(monthKeys) {
  return `<div class="arrears-list">${monthKeys.map((monthKey) => `<span class="access-chip">${formatMonthShort(monthKey)}</span>`).join("")}</div>`;
}

function monthPanelMarkup(units, monthKey, monthsBack = 0) {
  const isCurrentMonth = monthsBack === 0;
  const tag = isCurrentMonth ? "Actual" : `${monthsBack} ${monthsBack === 1 ? "mes" : "meses"} atras`;
  return `
    <section class="table-panel month-panel">
      <div class="month-panel-header">
        <div>
          <p class="eyebrow">${isCurrentMonth ? "Mes en curso" : "Mes anterior"}</p>
          <h3>${formatMonthLabel(monthKey)}</h3>
        </div>
        <span class="month-tag">${tag}</span>
      </div>
      ${unitsTableMarkup(units, monthKey)}
    </section>
  `;
}

function unitsTableMarkup(units, monthKey) {
  return `
    <div class="table-scroll">
      <table>
        <thead>
          <tr>
            <th>Mes</th>
            <th>Propiedad</th>
            <th>Unidad</th>
            <th>Arrendatario</th>
            <th>Renta mensual</th>
            <th>Extraordinarios</th>
            <th>Servicios</th>
            <th>Mantenimiento</th>
            <th>Publicidad</th>
            <th>Inicio contrato</th>
            <th>Fin contrato</th>
            <th>Machote</th>
            <th>Firmado</th>
          </tr>
        </thead>
        <tbody>
          ${units.map((unit) => unitRowMarkup(unit, monthKey)).join("")}
        </tbody>
      </table>
    </div>
  `;
}

function unitRowMarkup(unit, monthKey) {
  const property = getProperty(unit.propertyId);
  return `
    <tr>
      <td class="nowrap"><strong>${formatMonthShort(monthKey)}</strong></td>
      <td class="primary-cell">
        <strong>${property?.name || "Sin propiedad"}</strong>
        <small>${property?.type || ""} - ${property?.location || ""}</small>
      </td>
      <td><strong>${unit.unit}</strong></td>
      <td class="stacked">
        <strong>${unit.tenant}</strong>
        <small>${tenantEmail(unit)}</small>
      </td>
      ${paymentConcepts.map((concept) => paymentCellMarkup(unit, concept, monthKey)).join("")}
      <td>${formatDate(unit.contractStart)}</td>
      <td>
        <span class="status-pill ${contractStatus(unit).className}">${contractStatus(unit).label}</span>
        <div>${formatDate(unit.contractEnd)}</div>
      </td>
      <td>
        <button class="icon-button" type="button" title="Ver contrato machote" aria-label="Ver contrato machote" data-contract="template" data-unit-id="${unit.id}">
          <span data-icon="fileText" aria-hidden="true"></span>
        </button>
      </td>
      <td>
        <button class="icon-button" type="button" title="Ver contrato firmado" aria-label="Ver contrato firmado" data-contract="signed" data-unit-id="${unit.id}">
          <span data-icon="eye" aria-hidden="true"></span>
        </button>
      </td>
    </tr>
  `;
}

function paymentCellMarkup(unit, concept, monthKey) {
  const status = getPaymentStatus(unit, monthKey, concept.key);
  const isTenantPayment = view.roleId === "tenant" && unit.tenantUserId === view.userId;
  const isEditable = canManagePayments() || (isTenantPayment && status === "pending");
  const label = status === "paid" ? "Pagado" : isTenantPayment ? "Pagar" : "Por pagar";
  const className = status === "paid" ? "status-paid" : "status-pending";
  const icon = status === "paid" ? "checkCircle" : "alertCircle";
  const amount = unit[concept.key] || 0;

  return `
    <td class="money-cell">
      <strong>${formatCurrency(amount)}</strong>
      <button class="status-pill ${className}" type="button" ${isEditable ? "" : "disabled"} data-payment-toggle="${concept.key}" data-payment-month="${monthKey}" data-unit-id="${unit.id}" title="${isEditable ? (isTenantPayment ? "Registrar pago" : "Cambiar estado") : "Solo lectura"}">
        ${renderIcon(icon)}
        ${label}
      </button>
    </td>
  `;
}

function bindUnitActions() {
  els.contentArea.querySelectorAll("[data-payment-toggle]").forEach((button) => {
    button.addEventListener("click", () => {
      const unit = state.units.find((item) => item.id === button.dataset.unitId);
      const concept = button.dataset.paymentToggle;
      const monthKey = button.dataset.paymentMonth || currentMonthKey();
      if (!unit) return;
      const isTenantPayment = view.roleId === "tenant" && unit.tenantUserId === view.userId;
      const nextStatus = isTenantPayment ? "paid" : getPaymentStatus(unit, monthKey, concept) === "paid" ? "pending" : "paid";
      setPaymentStatus(unit, monthKey, concept, nextStatus);
      saveState();
      render();
      toast(isTenantPayment ? "Pago registrado. La factura ya esta disponible." : "Estado de pago actualizado");
    });
  });

  els.contentArea.querySelectorAll("[data-contract]").forEach((button) => {
    button.addEventListener("click", () => openContractModal(button.dataset.unitId, button.dataset.contract));
  });
}

function renderPlazaGeneralDashboard() {
  const properties = visibleProperties();

  if (view.roleId !== "superadmin") {
    els.contentArea.innerHTML = emptyState("Este panel solo esta disponible para Superadministrador.");
    return;
  }

  if (!properties.length) {
    els.contentArea.innerHTML = `
      <section class="plaza-dashboard-page">
        <div class="section-header plaza-dashboard-heading">
          <div>
            <h3>Selecciona una plaza</h3>
            <p class="muted">Crea la primera plaza para comenzar a administrar unidades, arrendatarios y cobranza.</p>
          </div>
        </div>
        <button class="plaza-dashboard-add-card is-empty" type="button" data-plaza-dashboard-new>
          <span class="plaza-dashboard-add-icon" aria-hidden="true">+</span>
          <strong>Nueva plaza</strong>
        </button>
      </section>
    `;
    els.contentArea.querySelector("[data-plaza-dashboard-new]")?.addEventListener("click", openPropertyFormModal);
    return;
  }

  const allSelected = view.propertyFilter === "all";
  let activePropertyIndex = properties.findIndex((property) => property.id === view.propertyFilter);
  if (!allSelected && activePropertyIndex < 0) {
    activePropertyIndex = properties.findIndex((property) => property.id === view.administrationPropertyId);
  }
  if (!allSelected && activePropertyIndex < 0) activePropertyIndex = 0;

  const property = allSelected ? null : properties[activePropertyIndex];
  const units = property ? propertyUnits(property.id) : [];
  const carouselItemCount = properties.length + 1;
  const activeCarouselIndex = allSelected ? 0 : activePropertyIndex + 1;

  if (property) {
    view.propertyFilter = property.id;
    rememberSelectedProperty(property.id);
  }
  view.propertyReturnTab = "superadmin_dashboard";

  els.contentArea.innerHTML = `
    <section class="plaza-dashboard-page">
      <div class="section-header plaza-dashboard-heading">
        <div>
          <h3>Selecciona una plaza</h3>
        </div>
      </div>

      <section class="plaza-dashboard-carousel is-compact" aria-label="Plazas administradas">
        <button class="icon-button plaza-dashboard-arrow" type="button" data-plaza-dashboard-carousel="previous" title="Seleccion anterior" aria-label="Seleccion anterior" ${carouselItemCount < 2 ? "disabled" : ""}>
          <span data-icon="chevronLeft" aria-hidden="true"></span>
        </button>

        <div class="plaza-dashboard-viewport">
          <div class="plaza-dashboard-cards">
            ${plazaDashboardAllCardMarkup(properties, allSelected)}
            ${properties.map((item) => plazaDashboardCardMarkup(item, !allSelected && item.id === property?.id)).join("")}
            <button class="plaza-dashboard-add-card" type="button" data-plaza-dashboard-new>
              <span class="plaza-dashboard-add-icon" aria-hidden="true">+</span>
              <strong>Nueva plaza</strong>
            </button>
          </div>
        </div>

        <button class="icon-button plaza-dashboard-arrow" type="button" data-plaza-dashboard-carousel="next" title="Seleccion siguiente" aria-label="Seleccion siguiente" ${carouselItemCount < 2 ? "disabled" : ""}>
          <span data-icon="chevronRight" aria-hidden="true"></span>
        </button>
      </section>

      <div class="plaza-dashboard-carousel-status" aria-label="Posicion del carrusel">
        <div class="plaza-dashboard-dots" role="group" aria-label="Seleccion rapida de plaza">
          <button
            class="plaza-dashboard-dot ${allSelected ? "is-active" : ""}"
            type="button"
            data-plaza-dashboard-all
            title="Seleccionar todas las plazas"
            aria-label="Seleccionar todas las plazas"
            aria-current="${allSelected ? "true" : "false"}"
          ></button>
          ${properties.map((item) => `
            <button
              class="plaza-dashboard-dot ${!allSelected && item.id === property?.id ? "is-active" : ""}"
              type="button"
              data-plaza-dashboard-property-id="${escapeAttribute(item.id)}"
              title="Seleccionar ${escapeAttribute(item.name)}"
              aria-label="Seleccionar ${escapeAttribute(item.name)}"
              aria-current="${!allSelected && item.id === property?.id ? "true" : "false"}"
            ></button>
          `).join("")}
        </div>
        <strong>${activeCarouselIndex + 1} de ${carouselItemCount}</strong>
      </div>

      ${allSelected ? `
        <section class="property-detail-section plaza-dashboard-unit-status" aria-labelledby="plazaDashboardPortfolioTitle">
          <div class="section-header">
            <div>
              <p class="eyebrow">Vista general</p>
              <h3 id="plazaDashboardPortfolioTitle">Todas las plazas</h3>
              <p class="muted">Informacion general consolidada, con un renglon por plaza.</p>
            </div>
          </div>
          ${plazaDashboardAllPropertiesTableMarkup(properties)}
        </section>
      ` : `
        <section class="property-detail-section plaza-dashboard-unit-status" aria-labelledby="plazaDashboardUnitStatusTitle">
          <div class="section-header">
            <div>
              <p class="eyebrow">Estado por unidad</p>
              <h3 id="plazaDashboardUnitStatusTitle">${escapeAttribute(property.name)}</h3>
            </div>
          </div>
          ${plazaDashboardStatusTableMarkup(property, units)}
        </section>
      `}
    </section>
  `;

  bindPlazaDashboardActions(properties, activeCarouselIndex);
}

function plazaDashboardAllCardMarkup(properties, isActive) {
  const units = properties.flatMap((property) => propertyUnits(property.id));
  const totalUnits = properties.reduce((sum, property) => {
    const propertyUnitCount = propertyUnits(property.id).length;
    return sum + (propertyUnitCount || Number(property.plannedUnits || 0));
  }, 0);
  const occupiedUnits = properties.reduce((sum, property) => {
    const propertyUnitList = propertyUnits(property.id);
    if (propertyUnitList.length) {
      return sum + propertyUnitList.filter((unit) => !isUnitAvailable(unit)).length;
    }
    return sum + Number(property.plannedOccupiedUnits || 0);
  }, 0);

  return `
    <button
      class="plaza-dashboard-card plaza-dashboard-all-card ${isActive ? "is-selected" : ""}"
      type="button"
      data-plaza-dashboard-all
      aria-label="Mostrar todas las plazas"
      aria-current="${isActive ? "true" : "false"}"
    >
      <span class="plaza-dashboard-card-header">
        <span class="property-icon is-all" aria-hidden="true">${renderIcon("layoutDashboard")}</span>
        <span class="role-badge">Vista general</span>
      </span>
      <span class="plaza-dashboard-card-title">
        <strong>Todas</strong>
      </span>
      <span class="plaza-dashboard-location">Resumen de todas las plazas</span>
      <span class="plaza-dashboard-card-metrics">
        <span><small>Plazas</small><strong>${properties.length}</strong></span>
        <span><small>Unidades</small><strong>${totalUnits || units.length}</strong></span>
        <span><small>Ocupacion</small><strong class="is-positive">${occupiedUnits}/${totalUnits || units.length}</strong></span>
      </span>
    </button>
  `;
}

function plazaDashboardAllPropertiesTableMarkup(properties) {
  return `
    <div class="table-panel plaza-dashboard-status-panel">
      <div class="table-scroll">
        <table class="plaza-dashboard-portfolio-table" aria-label="Informacion general de todas las plazas">
          <thead>
            <tr>
              <th>Plaza</th>
              <th>Clave</th>
              <th>Tipo</th>
              <th>Ubicacion</th>
              <th>Responsable</th>
              <th>Unidades</th>
              <th>Ocupacion</th>
              <th>Ingreso mensual</th>
              <th>Marketplace</th>
              <th>Estatus</th>
              <th>Editar</th>
            </tr>
          </thead>
          <tbody>
            ${properties.map(plazaDashboardPropertyRowMarkup).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function plazaDashboardPropertyRowMarkup(property) {
  const units = propertyUnits(property.id);
  const unitCount = units.length || Number(property.plannedUnits || 0);
  const occupiedUnits = units.length
    ? units.filter((unit) => !isUnitAvailable(unit)).length
    : Number(property.plannedOccupiedUnits || 0);
  const monthlyIncome = units.length
    ? units.reduce((sum, unit) => sum + Number(unit.monthlyRent || 0), 0)
    : Number(property.targetMonthlyIncome || 0);
  const manager = getUser(property.managerUserId);
  const marketplaceUnits = units.filter((unit) => isUnitMarketplaceEnabled(property, unit)).length;
  const status = property.status || "Operando";
  const statusClass = status === "Operando"
    ? "status-paid"
    : status === "Pausada"
      ? "status-danger"
      : status === "En apertura"
        ? "status-pending"
        : "status-neutral";

  return `
    <tr>
      <td><strong>${escapeAttribute(property.name)}</strong></td>
      <td>${escapeAttribute(property.internalKey || "Sin clave")}</td>
      <td>${escapeAttribute(property.type)}</td>
      <td>${escapeAttribute(property.location || "Sin ubicacion")}</td>
      <td>${escapeAttribute(manager?.name || "Sin responsable")}</td>
      <td><strong>${unitCount}</strong></td>
      <td><strong>${occupiedUnits}/${unitCount}</strong></td>
      <td><strong>${formatCurrency(monthlyIncome)}</strong></td>
      <td>
        <span class="status-pill ${marketplaceUnits ? "status-paid" : "status-neutral"}">
          ${marketplaceUnits ? `${marketplaceUnits} de ${unitCount}` : "No"}
        </span>
      </td>
      <td><span class="status-pill ${statusClass}">${escapeAttribute(status)}</span></td>
      <td>
        <button class="secondary-button plaza-dashboard-edit-button" type="button" data-plaza-dashboard-edit="${escapeAttribute(property.id)}">
          <span data-icon="settings" aria-hidden="true"></span>
          Editar
        </button>
      </td>
    </tr>
  `;
}

function plazaDashboardStatusTableMarkup(property, units) {
  if (!units.length) return emptyState("No hay unidades registradas en esta plaza.");

  return `
    <div class="table-panel plaza-dashboard-status-panel">
      <div class="table-scroll">
        <table class="plaza-dashboard-status-table" aria-label="Estado de unidades de ${escapeAttribute(property.name)}">
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Arrendatario</th>
              <th>Pago de renta</th>
              <th>Pago de mantenimiento</th>
              <th>Contrato</th>
              <th>Activo en Marketplace</th>
            </tr>
          </thead>
          <tbody>
            ${units.map((unit) => plazaDashboardStatusRowMarkup(property, unit)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function plazaDashboardStatusRowMarkup(property, unit) {
  const isAvailable = isUnitAvailable(unit);
  const contract = plazaDashboardContractStatus(unit);
  const marketplaceEnabled = isUnitMarketplaceEnabled(property, unit);
  const paymentNotApplicable = '<span class="payment-status-chip status-neutral">No aplica</span>';

  return `
    <tr>
      <td><strong>${escapeAttribute(unit.unit)}</strong></td>
      <td>${escapeAttribute(isAvailable ? "Disponible" : unit.tenant)}</td>
      <td>${isAvailable ? paymentNotApplicable : paymentStatusChipMarkup(unit, "rent")}</td>
      <td>${isAvailable ? paymentNotApplicable : paymentStatusChipMarkup(unit, "maintenance")}</td>
      <td>
        <span class="status-pill ${contract.className}" title="${escapeAttribute(contract.description)}">
          ${contract.label}
        </span>
      </td>
      <td>
        <span class="status-pill ${marketplaceEnabled ? "status-paid" : "status-neutral"}">
          ${marketplaceEnabled ? "Si" : "No"}
        </span>
      </td>
    </tr>
  `;
}

function plazaDashboardContractStatus(unit) {
  if (isUnitAvailable(unit)) {
    return { label: "Sin contrato", className: "status-neutral", description: "Unidad disponible" };
  }

  const daysRemaining = tenantContractDaysRemaining(unit);
  if (!Number.isFinite(daysRemaining)) {
    return { label: "Sin contrato", className: "status-neutral", description: "Sin vigencia registrada" };
  }
  if (daysRemaining < 0) {
    return { label: "Vencido", className: "status-danger", description: "Contrato de arrendamiento vencido" };
  }

  return {
    label: "Vigente",
    className: daysRemaining < 60 ? "status-pending" : "status-paid",
    description: daysRemaining === 0 ? "Vence hoy" : `Vence en ${daysRemaining} dias`
  };
}

function plazaDashboardCardMarkup(property, isActive, dataAttribute = "data-plaza-dashboard-property-id") {
  const units = propertyUnits(property.id);
  const occupied = units.filter((unit) => unit.tenant !== "Disponible").length;
  const pending = units.reduce((sum, unit) => sum + unitPendingTotal(unit, recentMonthKeys()), 0);
  const enabledMarketplaceUnits = units.filter((unit) => isUnitMarketplaceEnabled(property, unit)).length;
  const typeClass = property.type === "Bodega" ? "is-industrial" : property.type === "Vivienda" ? "is-housing" : "";
  const titleBadges = [
    isActive ? `<span class="role-badge">${escapeAttribute(property.type)}</span>` : "",
    property.marketplaceEnabled
      ? `<span class="role-badge is-marketplace">Marketplace ${enabledMarketplaceUnits} de ${units.length}</span>`
      : ""
  ].join("");

  return `
    <button
      class="plaza-dashboard-card ${isActive ? "is-selected" : ""}"
      type="button"
      ${dataAttribute}="${escapeAttribute(property.id)}"
      aria-label="Seleccionar ${escapeAttribute(property.name)}"
      aria-current="${isActive ? "true" : "false"}"
    >
      <span class="plaza-dashboard-card-header">
        <span class="property-icon ${typeClass}" aria-hidden="true">${renderIcon(property.type === "Vivienda" ? "home" : "building")}</span>
        ${isActive ? "" : `<span class="role-badge">${escapeAttribute(property.type)}</span>`}
      </span>
      <span class="plaza-dashboard-card-title">
        <strong>${escapeAttribute(property.name)}</strong>
        ${titleBadges ? `<span class="plaza-dashboard-card-badges">${titleBadges}</span>` : ""}
      </span>
      <span class="plaza-dashboard-location">${escapeAttribute(property.location)}</span>
      <span class="plaza-dashboard-card-metrics">
        <span><small>Unidades</small><strong>${units.length}</strong></span>
        <span><small>Ocupacion</small><strong class="is-positive">${occupied}/${units.length}</strong></span>
        <span><small>Por cobrar</small><strong>${formatCurrency(pending)}</strong></span>
      </span>
    </button>
  `;
}

function resolveCatalogPropertySelection(properties, preferredPropertyId = "") {
  const candidateIds = [preferredPropertyId, view.administrationPropertyId, view.propertyFilter];
  let activeIndex = candidateIds.reduce((match, propertyId) => {
    if (match >= 0 || !propertyId || propertyId === "all") return match;
    return properties.findIndex((property) => property.id === propertyId);
  }, -1);

  if (activeIndex < 0) activeIndex = 0;
  const property = properties[activeIndex] || null;
  if (property) rememberSelectedProperty(property.id);

  return { activeIndex, property };
}

function plazaCatalogSelectorMarkup(properties, activeIndex, ariaLabel) {
  const activeProperty = properties[activeIndex];

  return `
    <section class="plaza-catalog-selector" aria-label="${escapeAttribute(ariaLabel)}">
      <div class="section-header plaza-dashboard-heading">
        <div>
          <h3>Selecciona una plaza</h3>
        </div>
      </div>

      <section class="plaza-dashboard-carousel is-compact is-menu-compact" aria-label="Carrusel de plazas">
        <button class="icon-button plaza-dashboard-arrow" type="button" data-catalog-plaza-carousel="previous" title="Plaza anterior" aria-label="Plaza anterior" ${properties.length < 2 ? "disabled" : ""}>
          <span data-icon="chevronLeft" aria-hidden="true"></span>
        </button>

        <div class="plaza-dashboard-viewport">
          <div class="plaza-dashboard-cards">
            ${properties.map((property) => plazaDashboardCardMarkup(property, property.id === activeProperty?.id, "data-catalog-plaza-property-id")).join("")}
          </div>
        </div>

        <button class="icon-button plaza-dashboard-arrow" type="button" data-catalog-plaza-carousel="next" title="Plaza siguiente" aria-label="Plaza siguiente" ${properties.length < 2 ? "disabled" : ""}>
          <span data-icon="chevronRight" aria-hidden="true"></span>
        </button>
      </section>

      <div class="plaza-dashboard-carousel-status" aria-label="Posicion del carrusel">
        <div class="plaza-dashboard-dots" role="group" aria-label="Seleccion rapida de plaza">
          ${properties.map((property) => `
            <button
              class="plaza-dashboard-dot ${property.id === activeProperty?.id ? "is-active" : ""}"
              type="button"
              data-catalog-plaza-property-id="${escapeAttribute(property.id)}"
              title="Seleccionar ${escapeAttribute(property.name)}"
              aria-label="Seleccionar ${escapeAttribute(property.name)}"
              aria-current="${property.id === activeProperty?.id ? "true" : "false"}"
            ></button>
          `).join("")}
        </div>
        <strong>${activeIndex + 1} de ${properties.length}</strong>
      </div>
    </section>
  `;
}

function bindCatalogPlazaSelector(properties, activeIndex, onSelect) {
  const selectProperty = (propertyId) => {
    if (!properties.some((property) => property.id === propertyId)) return;
    rememberSelectedProperty(propertyId);
    onSelect(propertyId);
    requestAnimationFrame(() => {
      centerPlazaCarouselCard(
        els.contentArea.querySelector(`.plaza-dashboard-card[data-catalog-plaza-property-id="${propertyId}"]`)
      );
    });
  };

  els.contentArea.querySelectorAll("[data-catalog-plaza-carousel]").forEach((button) => {
    button.addEventListener("click", () => {
      const direction = button.dataset.catalogPlazaCarousel === "previous" ? -1 : 1;
      const nextIndex = (activeIndex + direction + properties.length) % properties.length;
      selectProperty(properties[nextIndex].id);
    });
  });

  els.contentArea.querySelectorAll("[data-catalog-plaza-property-id]").forEach((button) => {
    button.addEventListener("click", () => selectProperty(button.dataset.catalogPlazaPropertyId));
  });
}

function centerPlazaCarouselCard(card) {
  const viewport = card?.closest(".plaza-dashboard-viewport");
  if (!viewport) return;

  const left = card.offsetLeft - ((viewport.clientWidth - card.offsetWidth) / 2);
  viewport.scrollTo({ left: Math.max(0, left), behavior: "smooth" });
}

function bindPlazaDashboardActions(properties, activeIndex) {
  const carouselSelections = ["all", ...properties.map((property) => property.id)];
  const selectProperty = (nextPropertyId) => {
    if (nextPropertyId !== "all" && !properties.some((property) => property.id === nextPropertyId)) return;
    if (nextPropertyId !== "all") rememberSelectedProperty(nextPropertyId);
    view.propertyFilter = nextPropertyId;
    view.propertyAdministrationView = "units";
    render();
    requestAnimationFrame(() => {
      centerPlazaCarouselCard(
        nextPropertyId === "all"
          ? els.contentArea.querySelector(".plaza-dashboard-card[data-plaza-dashboard-all]")
          : els.contentArea.querySelector(`.plaza-dashboard-card[data-plaza-dashboard-property-id="${nextPropertyId}"]`)
      );
    });
  };

  els.contentArea.querySelectorAll("[data-plaza-dashboard-carousel]").forEach((button) => {
    button.addEventListener("click", () => {
      const direction = button.dataset.plazaDashboardCarousel === "previous" ? -1 : 1;
      const nextIndex = (activeIndex + direction + carouselSelections.length) % carouselSelections.length;
      selectProperty(carouselSelections[nextIndex]);
    });
  });

  els.contentArea.querySelectorAll("[data-plaza-dashboard-all]").forEach((button) => {
    button.addEventListener("click", () => selectProperty("all"));
  });

  els.contentArea.querySelectorAll("[data-plaza-dashboard-property-id]").forEach((button) => {
    button.addEventListener("click", () => selectProperty(button.dataset.plazaDashboardPropertyId));
  });

  els.contentArea.querySelectorAll("[data-plaza-dashboard-edit]").forEach((button) => {
    button.addEventListener("click", () => openPlazaCreationFlow(button.dataset.plazaDashboardEdit));
  });

  els.contentArea.querySelector("[data-plaza-dashboard-new]")?.addEventListener("click", openPropertyFormModal);
}

function renderPlazaAdministration() {
  const properties = visibleProperties();

  if (!properties.length) {
    els.contentArea.innerHTML = `
      <section class="plaza-dashboard-page plaza-administration-page">
        <div class="section-header plaza-dashboard-heading">
          <div>
            <h3>Selecciona una plaza</h3>
            <p class="muted">No hay plazas registradas para administrar.</p>
          </div>
        </div>
      </section>
    `;
    return;
  }

  let activeIndex = properties.findIndex((property) => property.id === view.administrationPropertyId);
  if (activeIndex < 0) activeIndex = 0;

  const property = properties[activeIndex];
  rememberSelectedProperty(property.id);
  view.propertyDetailId = property.id;
  view.propertyFilter = property.id;
  view.propertyReturnTab = "administration";

  els.contentArea.innerHTML = `
    <section class="plaza-dashboard-page plaza-administration-page">
      <div class="section-header plaza-dashboard-heading">
        <div>
          <h3>Selecciona una plaza</h3>
        </div>
      </div>

      <section class="plaza-dashboard-carousel is-compact is-menu-compact" aria-label="Carrusel de plazas existentes">
        <button class="icon-button plaza-dashboard-arrow" type="button" data-administration-carousel="previous" title="Plaza anterior" aria-label="Plaza anterior" ${properties.length < 2 ? "disabled" : ""}>
          <span data-icon="chevronLeft" aria-hidden="true"></span>
        </button>

        <div class="plaza-dashboard-viewport">
          <div class="plaza-dashboard-cards plaza-administration-selector-cards">
            ${properties.map((item) => plazaDashboardCardMarkup(item, item.id === property.id, "data-administration-property-id")).join("")}
          </div>
        </div>

        <button class="icon-button plaza-dashboard-arrow" type="button" data-administration-carousel="next" title="Plaza siguiente" aria-label="Plaza siguiente" ${properties.length < 2 ? "disabled" : ""}>
          <span data-icon="chevronRight" aria-hidden="true"></span>
        </button>
      </section>

      <div class="plaza-dashboard-carousel-status" aria-label="Posicion del carrusel">
        <div class="plaza-dashboard-dots" role="group" aria-label="Seleccion rapida de plaza">
          ${properties.map((item) => `
            <button
              class="plaza-dashboard-dot ${item.id === property.id ? "is-active" : ""}"
              type="button"
              data-administration-property-id="${escapeAttribute(item.id)}"
              title="Seleccionar ${escapeAttribute(item.name)}"
              aria-label="Seleccionar ${escapeAttribute(item.name)}"
              aria-current="${item.id === property.id ? "true" : "false"}"
            ></button>
          `).join("")}
        </div>
        <strong>${activeIndex + 1} de ${properties.length}</strong>
      </div>

      <div class="plaza-administration-flow">
        ${renderPropertyDetailSection({ property, markupOnly: true })}
      </div>
    </section>
  `;

  bindPlazaAdministrationActions(properties, activeIndex);
  bindPropertyDetailActions(property.id);
}

function bindPlazaAdministrationActions(properties, activeIndex) {
  const selectProperty = (nextPropertyId) => {
    if (!properties.some((property) => property.id === nextPropertyId)) return;
    rememberSelectedProperty(nextPropertyId);
    view.propertyFilter = nextPropertyId;
    view.propertyAdministrationView = "units";
    render();
    requestAnimationFrame(() => {
      centerPlazaCarouselCard(
        els.contentArea.querySelector(`.plaza-dashboard-card[data-administration-property-id="${nextPropertyId}"]`)
      );
    });
  };

  els.contentArea.querySelectorAll("[data-administration-carousel]").forEach((button) => {
    button.addEventListener("click", () => {
      const direction = button.dataset.administrationCarousel === "previous" ? -1 : 1;
      const nextIndex = (activeIndex + direction + properties.length) % properties.length;
      selectProperty(properties[nextIndex].id);
    });
  });

  els.contentArea.querySelectorAll("[data-administration-property-id]").forEach((button) => {
    button.addEventListener("click", () => selectProperty(button.dataset.administrationPropertyId));
  });
}

function renderPlazaContracts() {
  const properties = visibleProperties();

  if (!properties.length) {
    els.contentArea.innerHTML = `
      <section class="plaza-dashboard-page plaza-contracts-page">
        <div class="section-header plaza-dashboard-heading">
          <div>
            <h3>Selecciona una plaza</h3>
            <p class="muted">No hay plazas registradas para administrar contratos.</p>
          </div>
        </div>
      </section>
    `;
    return;
  }

  const { activeIndex, property } = resolveCatalogPropertySelection(properties);
  const units = propertyUnits(property.id);
  view.propertyDetailId = property.id;
  view.propertyFilter = property.id;
  view.legalReturnTab = "plaza_contracts";

  els.contentArea.innerHTML = `
    <section class="plaza-dashboard-page plaza-contracts-page">
      ${plazaCatalogSelectorMarkup(properties, activeIndex, "Selector de plazas para contratos")}

      <section class="plaza-catalog-content">
        <section id="propertyLegalPanel" class="property-detail-section property-legal-section">
          <div class="section-header">
            <div>
              <p class="eyebrow">Contratos</p>
              <h3>Panel legal por unidad</h3>
              <p class="muted">Consulta machotes, contratos firmados y genera el contrato del proximo periodo para la plaza seleccionada.</p>
            </div>
          </div>
          ${units.length ? propertyLegalPanelTableMarkup(units) : emptyState("Aun no hay unidades registradas para esta plaza.")}
        </section>
      </section>
    </section>
  `;

  bindCatalogPlazaSelector(properties, activeIndex, (propertyId) => {
    view.propertyDetailId = propertyId;
    view.propertyFilter = propertyId;
    view.legalReturnTab = "plaza_contracts";
    render();
  });
  bindPropertyLegalPanelActions(property.id);
}

function renderPlazaMarketplace() {
  const properties = visibleProperties();

  if (!properties.length) {
    els.contentArea.innerHTML = `
      <section class="plaza-dashboard-page plaza-marketplace-page">
        <div class="section-header plaza-dashboard-heading">
          <div>
            <h3>Selecciona una plaza</h3>
            <p class="muted">No hay plazas registradas para mostrar en Marketplace.</p>
          </div>
        </div>
      </section>
    `;
    return;
  }

  const { activeIndex, property } = resolveCatalogPropertySelection(properties);
  const units = propertyUnits(property.id);
  view.propertyDetailId = property.id;
  view.propertyFilter = property.id;

  els.contentArea.innerHTML = `
    <section class="plaza-dashboard-page plaza-marketplace-page">
      ${plazaCatalogSelectorMarkup(properties, activeIndex, "Selector de plazas para marketplace")}

      <section class="plaza-catalog-content">
        <section class="property-detail-section marketplace-inventory-section">
          <div class="section-header">
            <div class="marketplace-section-heading">
              <div>
                <p class="eyebrow">Inventario comercial</p>
                <h3>Espacios de la plaza</h3>
              </div>
              <label class="marketplace-toggle-control">
                <span class="marketplace-toggle-copy">
                  <strong>Marketplace</strong>
                  <small>${property.marketplaceEnabled ? "Habilitado para esta plaza" : "Deshabilitado para esta plaza"}</small>
                </span>
                <input
                  class="marketplace-toggle-input"
                  type="checkbox"
                  data-marketplace-toggle="${escapeAttribute(property.id)}"
                  ${property.marketplaceEnabled ? "checked" : ""}
                  aria-label="${property.marketplaceEnabled ? "Deshabilitar" : "Habilitar"} Marketplace para ${escapeAttribute(property.name)}"
                >
                <span class="marketplace-toggle-track" aria-hidden="true"></span>
              </label>
            </div>
          </div>
          ${units.length ? marketplaceUnitsTableMarkup(units, property) : emptyState("Aun no hay unidades registradas para esta plaza.")}
        </section>
      </section>
    </section>
  `;

  bindCatalogPlazaSelector(properties, activeIndex, (propertyId) => {
    view.propertyDetailId = propertyId;
    view.propertyFilter = propertyId;
    render();
  });

  els.contentArea.querySelector("[data-marketplace-toggle]")?.addEventListener("change", (event) => {
    const selectedProperty = getProperty(event.currentTarget.dataset.marketplaceToggle);
    if (!selectedProperty) return;

    selectedProperty.marketplaceEnabled = event.currentTarget.checked;
    saveState();
    render();
    toast(`Marketplace ${selectedProperty.marketplaceEnabled ? "habilitado" : "deshabilitado"} en ${selectedProperty.name}`);
  });

  els.contentArea.querySelectorAll("[data-unit-marketplace-toggle]").forEach((input) => {
    input.addEventListener("change", (event) => {
      const unit = state.units.find((item) => item.id === event.currentTarget.dataset.unitMarketplaceToggle);
      const selectedProperty = getProperty(unit?.propertyId);
      if (!unit || !selectedProperty?.marketplaceEnabled) return;

      unit.marketplaceEnabled = event.currentTarget.checked;
      saveState();
      render();
      toast(`Marketplace ${unit.marketplaceEnabled ? "habilitado" : "deshabilitado"} para ${unit.unit}`);
    });
  });
}

function marketplaceUnitsTableMarkup(units, property) {
  const orderedUnits = [...units].sort((first, second) => {
    const availabilityDifference = Number(isUnitAvailable(second)) - Number(isUnitAvailable(first));
    if (availabilityDifference !== 0) return availabilityDifference;
    return unitSortNumber(first) - unitSortNumber(second);
  });

  return `
    <div class="table-panel">
      <div class="table-scroll">
        <table class="marketplace-units-table">
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Estatus comercial</th>
              <th>Arrendatario</th>
              <th>Renta mensual</th>
              <th>Mantenimiento</th>
              <th>Metros cuadrados</th>
              <th>Medidas</th>
              <th>Marketplace</th>
            </tr>
          </thead>
          <tbody>
            ${orderedUnits.map((unit) => `
              <tr>
                <td><strong>${escapeAttribute(unit.unit)}</strong></td>
                <td>${isUnitAvailable(unit) ? '<span class="availability-chip">Disponible</span>' : '<span class="role-badge">Ocupada</span>'}</td>
                <td>${isUnitAvailable(unit) ? '<span class="muted-cell">Sin arrendatario</span>' : escapeAttribute(unit.tenant)}</td>
                <td><strong>${formatCurrency(unit.monthlyRent)}</strong></td>
                <td>${formatCurrency(unit.maintenance)}</td>
                <td>${unitSquareMetersLabel(unit)}</td>
                <td>${unitMeasurementsLabel(unit)}</td>
                <td>${marketplaceUnitToggleMarkup(property, unit)}</td>
              </tr>
            `).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function marketplaceUnitToggleMarkup(property, unit) {
  const marketplaceEnabled = isUnitMarketplaceEnabled(property, unit);
  const unitPreferenceEnabled = unit.marketplaceEnabled !== false;
  const statusLabel = property.marketplaceEnabled
    ? unitPreferenceEnabled ? "Habilitado" : "Deshabilitado"
    : "Plaza deshabilitada";

  return `
    <label class="marketplace-unit-toggle-control ${property.marketplaceEnabled ? "" : "is-disabled"}">
      <input
        class="marketplace-toggle-input"
        type="checkbox"
        data-unit-marketplace-toggle="${escapeAttribute(unit.id)}"
        ${marketplaceEnabled ? "checked" : ""}
        ${property.marketplaceEnabled ? "" : "disabled"}
        aria-label="${unitPreferenceEnabled ? "Deshabilitar" : "Habilitar"} Marketplace para ${escapeAttribute(unit.unit)}"
      >
      <span class="marketplace-toggle-track" aria-hidden="true"></span>
      <span class="marketplace-unit-toggle-label">${statusLabel}</span>
    </label>
  `;
}

function isUnitMarketplaceEnabled(property, unit) {
  return Boolean(property?.marketplaceEnabled && unit?.marketplaceEnabled !== false);
}

function renderPropertiesCatalog() {
  const properties = visibleProperties();
  const { activeIndex, property: selectedProperty } = resolveCatalogPropertySelection(properties);

  els.contentArea.innerHTML = `
    <section class="plaza-dashboard-page plaza-catalog-page">
      ${properties.length ? plazaCatalogSelectorMarkup(properties, activeIndex, "Selector de plazas del catalogo de unidades") : ""}

      <section class="plaza-catalog-content">
        ${selectedProperty
          ? propertyUnitCatalogSectionMarkup(selectedProperty)
          : emptyState("No hay plazas registradas para administrar unidades.")}
      </section>
    </section>
  `;

  if (properties.length) {
    bindCatalogPlazaSelector(properties, activeIndex, (propertyId) => {
      view.propertyDetailId = propertyId;
      view.propertyFilter = propertyId;
      render();
    });
  }

  if (selectedProperty) bindPropertyUnitCatalogActions(selectedProperty.id);
}

function openPropertiesCatalogForProperty(propertyId) {
  if (!getProperty(propertyId)) return;
  view.propertyDetailId = propertyId;
  view.propertyFilter = propertyId;
  view.activeTab = "properties";
  window.location.hash = "propertyUnitCatalog";
  render();
  requestAnimationFrame(() => document.querySelector("#propertyUnitCatalog")?.scrollIntoView({ behavior: "smooth", block: "start" }));
}

function renderPropertyDetailSection(options = {}) {
  const property = options.property || getProperty(view.propertyDetailId) || visibleProperties()[0];
  if (!property) {
    const markup = emptyState("No hay propiedad seleccionada.");
    if (options.markupOnly) return markup;
    els.contentArea.innerHTML = markup;
    return markup;
  }

  view.propertyDetailId = property.id;
  const activeAdministrationView = ["units", "payments", "advances", "balances"].includes(view.propertyAdministrationView)
    ? view.propertyAdministrationView
    : "units";

  const markup = `
    <section class="property-detail-page">
      <section id="propertyUnits" class="property-detail-section property-administration-panel" data-property-administration-panel data-active-view="${activeAdministrationView}">
        <nav class="property-administration-toolbar" aria-label="Vistas de administracion y cobranza">
          <button
            class="${activeAdministrationView === "units" ? "action-button" : "secondary-button"}"
            type="button"
            data-property-administration-home="${property.id}"
            data-property-administration-view="units"
            ${activeAdministrationView === "units" ? 'aria-current="page"' : ""}
          >
            <span data-icon="home" aria-hidden="true"></span>
            Inicio
          </button>
          <button
            class="${activeAdministrationView === "payments" ? "action-button" : "secondary-button"}"
            type="button"
            data-property-administration-view="payments"
            ${activeAdministrationView === "payments" ? 'aria-current="page"' : ""}
          >
            <span data-icon="creditCard" aria-hidden="true"></span>
            Registrar Pago
          </button>
          <button
            class="${activeAdministrationView === "advances" ? "action-button" : "secondary-button"}"
            type="button"
            data-property-administration-view="advances"
            ${activeAdministrationView === "advances" ? 'aria-current="page"' : ""}
          >
            <span data-icon="receipt" aria-hidden="true"></span>
            Registro de Anticipos
          </button>
          <button
            class="${activeAdministrationView === "balances" ? "action-button" : "secondary-button"}"
            type="button"
            data-property-administration-view="balances"
            ${activeAdministrationView === "balances" ? 'aria-current="page"' : ""}
          >
            <span data-icon="receipt" aria-hidden="true"></span>
            Ver Adeudos
          </button>
        </nav>

        <div class="property-administration-content" data-property-administration-content data-active-view="${activeAdministrationView}" aria-live="polite">
          ${propertyAdministrationViewMarkup(property, activeAdministrationView)}
        </div>
      </section>
    </section>
  `;

  if (options.markupOnly) return markup;

  els.contentArea.innerHTML = markup;
  bindPropertyDetailActions(property.id);

  return markup;
}

function propertyAdministrationViewMarkup(property, activeView = "units") {
  const units = propertyUnits(property.id);

  if (activeView === "payments") {
    ensureMonthlyPaymentRollover(units);
    syncAdvancePaymentsForUnits(units);
    const monthKeys = propertyBalanceDisplayMonthKeys();

    return `
      <section class="property-administration-view payment-method-window" data-administration-view-panel="payments">
        <div class="section-header">
          <div>
            <p class="eyebrow">Captura manual</p>
            <h3>Parciales de renta y mantenimiento</h3>
            <p class="muted">Captura Renta 1, Renta 2, Mantenimiento 1 y Mantenimiento 2 por mes. Las sumas deben cuadrar con Renta Total y Mantenimiento Total.</p>
          </div>
        </div>
        ${units.length ? `
          <div class="payment-method-stack" data-payment-method-monthly-panel>
            ${monthKeys.map((monthKey) => paymentMethodMonthPanelMarkup(units, monthKey, monthsBackFromCurrentMonth(monthKey))).join("")}
          </div>
          <div class="form-actions" data-payment-method-monthly-actions>
            <button class="action-button" type="button" data-save-payment-method="${property.id}">
              <span data-icon="checkCircle" aria-hidden="true"></span>
              Guardar metodo de pago
            </button>
          </div>
        ` : emptyState("Aun no hay unidades registradas para esta propiedad.")}
      </section>
    `;
  }

  if (activeView === "advances") {
    syncAdvancePaymentsForUnits(units);

    return `
      <section class="property-administration-view payment-method-window" data-administration-view-panel="advances">
        <div class="section-header">
          <div>
            <p class="eyebrow">Captura manual</p>
            <h3>Registro de Anticipos</h3>
            <p class="muted">Consulta y captura pagos anticipados por unidad, indicando los meses que cubre cada anticipo.</p>
          </div>
        </div>
        ${units.length ? paymentMethodAdvancePanelMarkup(units) : emptyState("Aun no hay unidades registradas para esta propiedad.")}
      </section>
    `;
  }

  if (activeView === "balances") {
    ensureMonthlyPaymentRollover(units);
    syncAdvancePaymentsForUnits(units);
    const monthKeys = propertyBalanceDisplayMonthKeys();

    return `
      <section class="property-administration-view property-balance-section" data-administration-view-panel="balances">
        <div class="section-header">
          <div>
            <p class="eyebrow">Balance</p>
            <h3>Balance de pago mensual</h3>
            <p class="muted">Mes corriente y doce meses atras por unidad y concepto de pago.</p>
          </div>
        </div>
        ${units.length ? propertyBalanceMonthStackMarkup(units, monthKeys) : emptyState("Aun no hay unidades registradas para esta propiedad.")}
      </section>
    `;
  }

  return `
    <section class="property-administration-view" data-administration-view-panel="units">
      <div class="section-header">
        <div>
          <p class="eyebrow">Unidades</p>
          <h3>Listado de unidades</h3>
          <p class="muted">Cada unidad puede abrirse para revisar su informacion especifica.</p>
        </div>
      </div>
      ${units.length ? propertyUnitsTableMarkup(units) : emptyState("Aun no hay unidades registradas para esta propiedad.")}
    </section>
  `;
}

function switchPropertyAdministrationView(propertyId, nextView) {
  const property = getProperty(propertyId);
  const panel = els.contentArea.querySelector("[data-property-administration-panel]");
  const content = panel?.querySelector("[data-property-administration-content]");
  if (!property || !panel || !content || !["units", "payments", "advances", "balances"].includes(nextView)) return;

  const toolbar = panel.querySelector(".property-administration-toolbar");
  const toolbarFrameTop = toolbar?.getBoundingClientRect().top ?? 0;
  let toolbarParentTop = null;
  try {
    if (window.parent !== window && window.frameElement) {
      toolbarParentTop = window.frameElement.getBoundingClientRect().top + toolbarFrameTop;
    }
  } catch {
    toolbarParentTop = null;
  }

  panel.classList.add("is-switching-view");
  view.propertyAdministrationView = nextView;
  panel.dataset.activeView = nextView;
  content.dataset.activeView = nextView;
  content.innerHTML = propertyAdministrationViewMarkup(property, nextView);
  content.scrollTop = 0;
  content.scrollLeft = 0;

  panel.querySelectorAll("[data-property-administration-view]").forEach((button) => {
    const isActive = button.dataset.propertyAdministrationView === nextView;
    button.classList.toggle("action-button", isActive);
    button.classList.toggle("secondary-button", !isActive);
    if (isActive) button.setAttribute("aria-current", "page");
    else button.removeAttribute("aria-current");
  });

  injectIcons(content);
  bindPropertyAdministrationContentActions(property.id, content);

  const restoreViewAnchor = () => {
    if (!toolbar) return;
    const frameDelta = toolbar.getBoundingClientRect().top - toolbarFrameTop;
    if (Math.abs(frameDelta) > 0.5) window.scrollBy(0, frameDelta);

    try {
      if (toolbarParentTop !== null && window.frameElement) {
        const currentParentTop = window.frameElement.getBoundingClientRect().top + toolbar.getBoundingClientRect().top;
        const parentDelta = currentParentTop - toolbarParentTop;
        if (Math.abs(parentDelta) > 0.5) window.parent.scrollBy(0, parentDelta);
      }
    } catch {
      // The embedded panel still keeps its own scroll position when parent access is unavailable.
    }
  };
  restoreViewAnchor();
  window.requestAnimationFrame(() => window.requestAnimationFrame(restoreViewAnchor));
  window.setTimeout(restoreViewAnchor, 80);
  window.setTimeout(() => {
    restoreViewAnchor();
    panel.classList.remove("is-switching-view");
  }, 220);
}

function openPropertyBalanceSection(propertyId) {
  if (!getProperty(propertyId)) return;
  view.propertyDetailId = propertyId;
  view.activeTab = "property_balance";
  view.propertyFilter = propertyId;
  window.location.hash = "propertyBalance";
  render();
}

function openPropertyPaymentMethodSection(propertyId) {
  if (!getProperty(propertyId)) return;
  view.propertyDetailId = propertyId;
  view.activeTab = "property_payment_method";
  view.propertyFilter = propertyId;
  window.location.hash = "propertyPaymentMethod";
  render();
}

function openPropertyAdvancePaymentsSection(propertyId) {
  if (!getProperty(propertyId)) return;
  view.propertyDetailId = propertyId;
  view.activeTab = "property_advance_payments";
  view.propertyFilter = propertyId;
  window.location.hash = "propertyAdvancePayments";
  render();
}

function openPropertyUnitStatusSection(unitId, propertyId = null) {
  const unit = state.units.find((item) => item.id === unitId);
  const targetPropertyId = propertyId || unit?.propertyId;
  if (!unit || !getProperty(targetPropertyId)) return;
  view.propertyDetailId = targetPropertyId;
  view.unitStatusId = unit.id;
  view.activeTab = "property_unit_status";
  view.propertyFilter = targetPropertyId;
  window.location.hash = "propertyUnitStatus";
  render();
}

function openPropertyLegalPanelSection(propertyId) {
  if (!getProperty(propertyId)) return;
  view.propertyDetailId = propertyId;
  view.legalReturnTab = "property_legal_panel";
  view.activeTab = "property_legal_panel";
  view.propertyFilter = propertyId;
  window.location.hash = "propertyLegalPanel";
  render();
}

function openPropertyOperatingCostsSection(propertyId) {
  if (!getProperty(propertyId)) return;
  view.propertyDetailId = propertyId;
  view.activeTab = "property_operating_costs";
  view.propertyFilter = propertyId;
  window.location.hash = "propertyOperatingCosts";
  render();
}

function renderPropertyBalanceSection() {
  const property = getProperty(view.propertyDetailId) || visibleProperties()[0];
  if (!property) {
    els.contentArea.innerHTML = emptyState("No hay propiedad seleccionada.");
    return;
  }

  view.propertyDetailId = property.id;
  view.legalReturnTab = "property_legal_panel";
  const units = propertyUnits(property.id);
  ensureMonthlyPaymentRollover(units);
  syncAdvancePaymentsForUnits(units);
  const monthKeys = propertyBalanceDisplayMonthKeys();

  els.contentArea.innerHTML = `
    <section id="propertyBalance" class="property-detail-page">
      <div class="property-detail-page-header">
        <div class="property-header-main">
          <div>
            <p class="eyebrow">Balance de propiedad</p>
            <h3>${property.name}</h3>
            <p class="muted">${property.type} - ${property.location}</p>
          </div>
        </div>
      </div>

      <section class="property-detail-section property-balance-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Balance</p>
            <h3>Balance de pago mensual</h3>
            <p class="muted">Mes corriente y doce meses atras por unidad y concepto de pago.</p>
          </div>
          <div class="section-actions">
            <button class="secondary-button" type="button" data-back-property-start="${property.id}">
              <span data-icon="eye" aria-hidden="true"></span>
              Regresar a administracion
            </button>
          </div>
        </div>
        ${units.length ? propertyBalanceMonthStackMarkup(units, monthKeys) : emptyState("Aun no hay unidades registradas para esta propiedad.")}
      </section>
    </section>
  `;

  bindPropertyBalanceActions(property.id);
}

function renderPropertyPaymentMethodSection() {
  const property = getProperty(view.propertyDetailId) || visibleProperties()[0];
  if (!property) {
    els.contentArea.innerHTML = emptyState("No hay propiedad seleccionada.");
    return;
  }

  view.propertyDetailId = property.id;
  const units = propertyUnits(property.id);
  ensureMonthlyPaymentRollover(units);
  syncAdvancePaymentsForUnits(units);
  const monthKeys = propertyBalanceDisplayMonthKeys();

  els.contentArea.innerHTML = `
    <section id="propertyPaymentMethod" class="property-detail-page">
      <div class="property-detail-page-header">
        <div class="property-header-main">
          <div>
            <p class="eyebrow">Metodo de pago</p>
            <h3>${property.name}</h3>
            <p class="muted">${property.type} - ${property.location}</p>
          </div>
        </div>
      </div>

      <section class="property-detail-section payment-method-window">
        <div class="section-header">
          <div>
            <p class="eyebrow">Captura manual</p>
            <h3>Parciales de renta y mantenimiento</h3>
            <p class="muted">Captura Renta 1, Renta 2, Mantenimiento 1 y Mantenimiento 2 por mes. Las sumas deben cuadrar con Renta Total y Mantenimiento Total.</p>
          </div>
          <div class="section-actions">
            <button class="secondary-button" type="button" data-back-property-start="${property.id}">
              <span data-icon="eye" aria-hidden="true"></span>
              Regresar a administracion
            </button>
          </div>
        </div>
        ${units.length ? `
          <div class="payment-method-stack" data-payment-method-monthly-panel>
            ${monthKeys.map((monthKey) => paymentMethodMonthPanelMarkup(units, monthKey, monthsBackFromCurrentMonth(monthKey))).join("")}
          </div>
          <div class="form-actions" data-payment-method-monthly-actions>
            <button class="action-button" type="button" data-save-payment-method="${property.id}">
              <span data-icon="checkCircle" aria-hidden="true"></span>
              Guardar metodo de pago
            </button>
          </div>
        ` : emptyState("Aun no hay unidades registradas para esta propiedad.")}
      </section>
    </section>
  `;

  bindPaymentMethodActions(els.contentArea);
}

function renderPropertyAdvancePaymentsSection() {
  const property = getProperty(view.propertyDetailId) || visibleProperties()[0];
  if (!property) {
    els.contentArea.innerHTML = emptyState("No hay propiedad seleccionada.");
    return;
  }

  view.propertyDetailId = property.id;
  const units = propertyUnits(property.id);

  els.contentArea.innerHTML = `
    <section id="propertyAdvancePayments" class="property-detail-page">
      <div class="property-detail-page-header">
        <div class="property-header-main">
          <div>
            <p class="eyebrow">Metodo de pago</p>
            <h3>${property.name}</h3>
            <p class="muted">${property.type} - ${property.location}</p>
          </div>
        </div>
      </div>

      <section class="property-detail-section payment-method-window">
        <div class="section-header">
          <div>
            <p class="eyebrow">Captura manual</p>
            <h3>Registro de Anticipos</h3>
            <p class="muted">Consulta y captura pagos anticipados por unidad, indicando los meses que cubre cada anticipo.</p>
          </div>
          <div class="section-actions">
            <button class="secondary-button" type="button" data-back-payment-method="${property.id}">
              <span data-icon="eye" aria-hidden="true"></span>
              Regresar a registro mensual
            </button>
          </div>
        </div>
        ${units.length ? paymentMethodAdvancePanelMarkup(units) : emptyState("Aun no hay unidades registradas para esta propiedad.")}
      </section>
    </section>
  `;

  bindPaymentMethodActions(els.contentArea);
}

function propertyUnitCatalogSectionMarkup(property) {
  const units = propertyUnits(property.id);
  return `
    <section id="propertyUnitCatalog" class="property-detail-section property-unit-catalog-section">
      <div class="section-header">
        <div>
          <p class="eyebrow">Unidades</p>
          <h3>Catalogo de unidades de ${property.name}</h3>
          <p class="muted">${property.type} - ${property.location}. Consulta la informacion base y administra altas o bajas.</p>
        </div>
        <div class="section-actions">
          <button class="action-button" type="button" data-add-unit="${property.id}">
            <span data-icon="building" aria-hidden="true"></span>
            Nueva Unidad
          </button>
        </div>
      </div>
      ${units.length ? propertyUnitCatalogTableMarkup(units) : emptyState("Aun no hay unidades registradas para esta propiedad.")}
    </section>
  `;
}

function renderPropertyUnitStatusSection() {
  const unit = state.units.find((item) => item.id === view.unitStatusId);
  const property = getProperty(unit?.propertyId || view.propertyDetailId) || visibleProperties()[0];
  if (!unit || !property) {
    els.contentArea.innerHTML = emptyState("No hay unidad seleccionada.");
    return;
  }

  view.propertyDetailId = property.id;
  view.unitStatusId = unit.id;
  const monthKeys = unitPaymentStatusMonthKeys();

  els.contentArea.innerHTML = `
    <section id="propertyUnitStatus" class="property-detail-page">
      <div class="property-detail-page-header">
        <div class="property-header-main">
          <div>
            <p class="eyebrow">Unidad</p>
            <h3>${unit.unit}</h3>
            <p class="muted">${property.name} - ${unit.tenant}</p>
          </div>
        </div>
        <div class="section-actions">
          <button class="secondary-button" type="button" data-back-property-units="${property.id}">
            <span data-icon="eye" aria-hidden="true"></span>
            Regresar a unidades
          </button>
        </div>
      </div>

      <section class="property-detail-section property-unit-status-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Pagos</p>
            <h3>Estatus de pagos de la unidad</h3>
            <p class="muted">Mes actual y doce meses atras con historico de pagos y adeudos.</p>
          </div>
          <div class="unit-status-summary">
            <div>
              <span>Unidad</span>
              <strong>${unit.unit}</strong>
            </div>
            <div>
              <span>Arrendatario</span>
              <strong>${tenantCellMarkup(unit)}</strong>
            </div>
          </div>
        </div>
        ${propertyUnitStatusTableMarkup(unit, monthKeys)}
      </section>
    </section>
  `;

  bindPropertyUnitStatusActions(property.id);
}

function unitPaymentStatusMonthKeys() {
  return [currentMonthKey(), ...propertyBalanceMonthKeys()];
}

function propertyUnitStatusTableMarkup(unit, monthKeys) {
  return `
    <div class="table-panel property-unit-status-panel">
      <div class="table-scroll">
        <table class="property-unit-status-table">
          <thead>
            <tr>
              <th>Mes</th>
              <th>Renta Total</th>
              <th>Mantenimiento Total</th>
              ${propertyPaymentColumns.map((concept) => `
                <th>${concept.label}</th>
                <th>Estatus</th>
              `).join("")}
            </tr>
          </thead>
          <tbody>
            ${monthKeys.map((monthKey) => propertyUnitStatusRowMarkup(unit, monthKey)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function propertyUnitStatusRowMarkup(unit, monthKey) {
  return `
    <tr>
      <td class="unit-status-month-cell">
        <strong>${formatMonthLabel(monthKey)}</strong>
        ${monthKey === currentMonthKey() ? `<small>Mes actual</small>` : ""}
      </td>
      <td><strong>${formatCurrency(paymentTotalAmount(unit, "rentTotal"))}</strong></td>
      <td><strong>${formatCurrency(paymentTotalAmount(unit, "maintenanceTotal"))}</strong></td>
      ${propertyPaymentColumns.map((concept) => `
        <td>${formatCurrency(conceptAmountForMonth(unit, concept.key, monthKey))}</td>
        <td>${paymentStatusChipMarkup(unit, concept.key, monthKey)}</td>
      `).join("")}
    </tr>
  `;
}

function bindPropertyUnitStatusActions(propertyId) {
  els.contentArea.querySelector("[data-back-property-units]")?.addEventListener("click", () => returnToPropertyUnits(propertyId));
}

function renderPropertyOperatingCostsSection() {
  const property = getProperty(view.propertyDetailId) || visibleProperties()[0];
  if (!property) {
    els.contentArea.innerHTML = emptyState("No hay propiedad seleccionada.");
    return;
  }

  view.propertyDetailId = property.id;

  els.contentArea.innerHTML = `
    <section id="propertyOperatingCosts" class="property-detail-page">
      <div class="property-detail-page-header">
        <div class="property-header-main">
          <div>
            <p class="eyebrow">Costos Operativos</p>
            <h3>${property.name}</h3>
            <p class="muted">${property.type} - ${property.location}</p>
          </div>
        </div>
        <div class="section-actions">
          <button class="secondary-button" type="button" data-back-property-start="${property.id}">
            <span data-icon="home" aria-hidden="true"></span>
            Regresar al inicio de propiedad
          </button>
        </div>
      </div>

      <section class="property-detail-section property-operating-costs-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Costos</p>
            <h3>Costos operativos anuales</h3>
            <p class="muted">Vista mensual de enero a diciembre por concepto operativo.</p>
          </div>
        </div>
        ${propertyOperatingCostsTableMarkup()}
      </section>
    </section>
  `;

  bindPropertyOperatingCostsActions(property.id);
}

function propertyOperatingCostsTableMarkup() {
  return `
    <div class="table-panel property-operating-costs-panel">
      <div class="table-scroll">
        <table class="property-operating-costs-table">
          <thead>
            <tr>
              <th>Concepto</th>
              ${operatingCostMonths.map((month) => `<th>${month}</th>`).join("")}
            </tr>
          </thead>
          <tbody>
            ${operatingCostRows.map((row) => `
              <tr>
                <td class="operating-cost-concept-cell"><strong>${row.label}</strong></td>
                ${row.amounts.map((amount) => `<td>${formatCurrency(amount)}</td>`).join("")}
              </tr>
            `).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function bindPropertyOperatingCostsActions(propertyId) {
  els.contentArea.querySelector("[data-back-property-start]")?.addEventListener("click", () => returnToPropertyStart(propertyId));
}

function renderPropertyLegalPanelSection() {
  const property = getProperty(view.propertyDetailId) || visibleProperties()[0];
  if (!property) {
    els.contentArea.innerHTML = emptyState("No hay propiedad seleccionada.");
    return;
  }

  view.propertyDetailId = property.id;
  const units = propertyUnits(property.id);

  els.contentArea.innerHTML = `
    <section id="propertyLegalPanel" class="property-detail-page">
      <div class="property-detail-page-header">
        <div class="property-header-main">
          <div>
            <p class="eyebrow">Panel legal</p>
            <h3>${property.name}</h3>
            <p class="muted">${property.type} - ${property.location}</p>
          </div>
        </div>
        <div class="section-actions">
          <button class="secondary-button" type="button" data-back-property-units="${property.id}">
            <span data-icon="eye" aria-hidden="true"></span>
            Regresar a unidades
          </button>
        </div>
      </div>

      <section class="property-detail-section property-legal-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Contratos</p>
            <h3>Panel legal por unidad</h3>
            <p class="muted">Consulta machotes, contratos firmados y genera el contrato del proximo periodo.</p>
          </div>
        </div>
        ${units.length ? propertyLegalPanelTableMarkup(units) : emptyState("Aun no hay unidades registradas para esta propiedad.")}
      </section>
    </section>
  `;

  bindPropertyLegalPanelActions(property.id);
}

function propertyBalanceMonthStackMarkup(units, monthKeys) {
  const orderedUnits = sortedUnitsByNumber(units);
  return `
    <div class="property-balance-stack">
      ${monthKeys.map((monthKey) => propertyBalanceMonthPanelMarkup(orderedUnits, monthKey, monthsBackFromCurrentMonth(monthKey))).join("")}
    </div>
  `;
}

function propertyBalanceMonthPanelMarkup(units, monthKey, monthsBack) {
  const pending = units.reduce((sum, unit) => sum + unitPendingTotal(unit, [monthKey]), 0);
  const paid = units.reduce((sum, unit) => sum + unitPaidTotal(unit, [monthKey]), 0);
  const periodLabel = monthsBack === 0 ? "Mes corriente" : `${monthsBack} ${monthsBack === 1 ? "mes atras" : "meses atras"}`;

  return `
    <section class="table-panel property-balance-month-panel payment-method-month-panel">
      <div class="month-panel-header">
        <div>
          <p class="eyebrow">${periodLabel}</p>
          <h3>${formatMonthLabel(monthKey)}</h3>
          <p class="muted">${formatCurrency(paid)} cobrado - ${formatCurrency(pending)} con adeudo</p>
        </div>
        <span class="month-tag">${formatMonthShort(monthKey)}</span>
      </div>
      <div class="table-scroll">
        <table class="property-balance-table payment-method-table">
          ${propertyBalanceColGroupMarkup()}
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Arrendatario</th>
              ${propertyBalanceHeadersMarkup({ monthKey, units })}
            </tr>
          </thead>
          <tbody>
            ${units.map((unit) => propertyBalanceRowMarkup(unit, monthKey)).join("")}
          </tbody>
          <tfoot>
            ${propertyBalanceTotalsRowMarkup(units, monthKey)}
          </tfoot>
        </table>
      </div>
    </section>
  `;
}

function propertyBalanceColGroupMarkup() {
  const balanceColumns = propertyBalanceColumnLayout().map((column) => {
    if (column.type === "total") return `<col class="balance-col-total balance-col-${column.balanceGroup}">`;
    if (column.type === "sum") return `<col class="balance-col-total balance-col-${column.balanceGroup}">`;

    return `
      <col class="balance-col-amount balance-col-${column.balanceGroup}">
      <col class="balance-col-status balance-col-${column.balanceGroup}">
    `;
  }).join("");

  return `
    <colgroup>
      <col class="balance-col-unit">
      <col class="balance-col-tenant">
      ${balanceColumns}
    </colgroup>
  `;
}

function propertyBalanceRowMarkup(unit, monthKey) {
  return `
    <tr class="${isUnitAvailable(unit) ? "payment-method-row-disabled" : ""}">
      <td><strong>${unit.unit}</strong></td>
      <td>${tenantCellMarkup(unit)}</td>
      ${propertyBalanceCellsMarkup(unit, { monthKey })}
    </tr>
  `;
}

function propertyBalanceColumnLayout() {
  const totals = Object.fromEntries(paymentTotalColumns.map((column) => [column.key, { ...column, type: "total" }]));
  const concepts = Object.fromEntries(propertyPaymentColumns.map((column) => [column.key, { ...column, type: "concept" }]));

  return [
    totals.rentTotal,
    concepts.rent,
    concepts.extraordinary,
    totals.maintenanceTotal,
    concepts.maintenance,
    concepts.services
  ].filter(Boolean).map((column) => ({
    ...column,
    balanceGroup: balanceColumnGroup(column)
  }));
}

function balanceColumnGroup(column) {
  if (["rentTotal", "rent", "extraordinary"].includes(column.key)) return "rent";
  if (["maintenanceTotal", "maintenance", "services"].includes(column.key)) return "maintenance";
  return "base";
}

function propertyBalanceGroupHeadersMarkup() {
  return propertyBalanceColumnLayout().map((column) => {
    if (column.type === "total") {
      return `<th class="balance-total-group-header is-${column.balanceGroup}" colspan="1">Totales</th>`;
    }

    return `<th class="balance-group-header is-${column.balanceGroup}" colspan="2">${compactBalanceHeaderLabel(column)}</th>`;
  }).join("");
}

function propertyBalanceHeadersMarkup(options = {}) {
  return propertyBalanceColumnLayout().map((column) => {
    const groupClass = propertyBalanceColumnClass(column);
    if (column.type === "total") {
      return `<th class="payment-total-column ${groupClass}">${compactBalanceHeaderLabel(column)}</th>`;
    }
    if (column.type === "sum") {
      return `<th class="payment-total-column ${groupClass}">${compactBalanceHeaderLabel(column)}</th>`;
    }

    const amountClass = propertyBalanceColumnClass(column, "amount");
    const statusClass = propertyBalanceColumnClass(column, "status");
    return `
      <th class="payment-amount-column ${amountClass}">${compactBalanceHeaderLabel(column)}</th>
      <th class="payment-status-column ${statusClass}">Estatus</th>
    `;
  }).join("");
}

function propertyPaymentHeadersMarkup(options = {}) {
  const includeRecords = options.includeRecords !== false;
  const includeValidation = options.includeValidation ?? includeRecords;
  const includeTotals = options.includeTotals !== false;
  const amountLabel = options.amountLabel || null;
  const monthKey = options.monthKey || currentMonthKey();
  const units = options.units || [];
  return `
    ${includeTotals ? propertyPaymentTotalHeadersMarkup() : ""}
    ${propertyPaymentColumns.map((concept) => `
    <th class="payment-amount-column">${amountLabel || compactPaymentHeaderLabel(concept.label)}</th>
    <th class="payment-status-column">Estatus</th>
    ${includeRecords ? `
      <th class="payment-date-column">Fecha de pago</th>
      <th class="payment-receipt-column">Recibo</th>
    ` : ""}
    ${includeValidation ? propertyValidationHeaderMarkup(concept, monthKey, units) : ""}
    `).join("")}
  `;
}

function propertyPaymentTotalHeadersMarkup() {
  return paymentTotalColumns.map((column) => `<th class="payment-total-column">${compactPaymentHeaderLabel(column.label)}</th>`).join("");
}

function propertyPaymentGroupHeadersMarkup() {
  return propertyPaymentColumns.map((concept) => `
    <th class="balance-group-header" colspan="5">${compactPaymentHeaderLabel(concept.label)}</th>
  `).join("");
}

function compactPaymentHeaderLabel(label) {
  return String(label || "").replace(/\s+(1|2|Total)$/i, "<br>$1");
}

function compactBalanceHeaderLabel(column) {
  if (column.key === "rentTotal") return "Renta Total";
  if (column.key === "maintenanceTotal") return "Mantenimiento Total";
  if (column.key === "rent") return "Renta 1<br>Transferencia/Deposito";
  if (column.key === "maintenance") return "Mantenimiento 1<br>Transferencia/Deposito";
  return column.label;
}

function propertyBalanceCellsMarkup(unit, options = {}) {
  const monthKey = options.monthKey || currentMonthKey();

  return propertyBalanceColumnLayout().map((column) => {
    const groupClass = propertyBalanceColumnClass(column);
    if (column.type === "total") {
      return `<td class="payment-total-column payment-method-readonly-total ${groupClass}"><strong>${formatCurrency(paymentTotalAmount(unit, column.key))}</strong></td>`;
    }
    const amountClass = propertyBalanceColumnClass(column, "amount");
    const statusClass = propertyBalanceColumnClass(column, "status");
    return `
      <td class="payment-amount-column ${amountClass}">${formatCurrency(conceptAmountForMonth(unit, column.key, monthKey))}</td>
      <td class="payment-status-column ${statusClass}">${paymentStatusChipMarkup(unit, column.key, monthKey)}</td>
    `;
  }).join("");
}

function propertyBalanceColumnClass(column, cellType = "single") {
  const classes = [`balance-${column.balanceGroup}-column`];
  if (column.balanceGroup === "rent") classes.push("payment-method-rent-section");
  if (column.balanceGroup === "maintenance") classes.push("payment-method-maintenance-section");
  if (["rentTotal", "maintenanceTotal"].includes(column.key)) classes.push("payment-method-section-start");
  if (["extraordinary", "services"].includes(column.key) && cellType === "status") classes.push("payment-method-section-end");
  return classes.join(" ");
}

function propertyBalanceTotalsRowMarkup(units, monthKey) {
  const totals = propertyBalanceTotals(units, monthKey);

  return `
    <tr class="payment-method-total-row">
      <td colspan="2">
        <span>Total</span>
      </td>
      ${propertyBalanceColumnLayout().map((column) => {
        const groupClass = propertyBalanceColumnClass(column);
        if (column.type === "concept") {
          const amountClass = propertyBalanceColumnClass(column, "amount");
          const statusClass = propertyBalanceColumnClass(column, "status");
          return `
            <td class="payment-amount-column ${amountClass}"><strong>${formatCurrency(totals[column.key])}</strong></td>
            <td class="payment-status-column ${statusClass}"></td>
          `;
        }
        if (column.type === "total") {
          return `<td class="payment-total-column payment-method-readonly-total ${groupClass}"><strong>${formatCurrency(totals[column.key])}</strong></td>`;
        }
        return `<td class="payment-total-column ${groupClass}"><strong>${formatCurrency(totals[column.key])}</strong></td>`;
      }).join("")}
    </tr>
  `;
}

function propertyBalanceTotals(units, monthKey) {
  return units.reduce((totals, unit) => {
    totals.rentTotal += paymentTotalAmount(unit, "rentTotal");
    totals.rent += conceptAmountForMonth(unit, "rent", monthKey);
    totals.extraordinary += conceptAmountForMonth(unit, "extraordinary", monthKey);
    totals.maintenanceTotal += paymentTotalAmount(unit, "maintenanceTotal");
    totals.maintenance += conceptAmountForMonth(unit, "maintenance", monthKey);
    totals.services += conceptAmountForMonth(unit, "services", monthKey);
    return totals;
  }, {
    rentTotal: 0,
    rent: 0,
    extraordinary: 0,
    maintenanceTotal: 0,
    maintenance: 0,
    services: 0
  });
}

function propertyPaymentCellsMarkup(unit, options = {}) {
  const monthKey = options.monthKey || currentMonthKey();
  const includeRecords = options.includeRecords !== false;
  const includeValidation = options.includeValidation ?? includeRecords;
  const includeTotals = options.includeTotals !== false;
  return `
    ${includeTotals ? propertyPaymentTotalCellsMarkup(unit) : ""}
    ${propertyPaymentColumns.map((concept) => `
    <td class="payment-amount-column">${formatCurrency(conceptAmountForMonth(unit, concept.key, monthKey))}</td>
    <td class="payment-status-column">${paymentStatusChipMarkup(unit, concept.key, monthKey, { fromValidation: includeValidation })}</td>
    ${includeRecords ? `
      <td class="payment-date-column">${paymentDateCellMarkup(unit, concept.key, monthKey)}</td>
      <td class="payment-receipt-column">${paymentReceiptCellMarkup(unit, concept.key, monthKey)}</td>
    ` : ""}
    ${includeValidation ? paymentValidationCellMarkup(unit, concept.key, monthKey) : ""}
    `).join("")}
  `;
}

function propertyPaymentTotalCellsMarkup(unit) {
  return paymentTotalColumns.map((column) => `
    <td class="payment-total-column"><strong>${formatCurrency(paymentTotalAmount(unit, column.key))}</strong></td>
  `).join("");
}

function propertyValidationHeaderMarkup(concept, monthKey, units) {
  const isAllValidated = units.length > 0 && units.every((unit) => paymentRecord(unit, monthKey, concept.key).validated);
  return `
    <th class="payment-validation-column">
      <label class="validation-header-control">
        <span>Validar</span>
        <input
          class="validation-checkbox"
          type="checkbox"
          data-validation-all="${concept.key}"
          data-payment-month="${monthKey}"
          ${isAllValidated ? "checked" : ""}
          aria-label="Seleccionar todos ${concept.label} ${formatMonthShort(monthKey)}"
        >
      </label>
    </th>
  `;
}

function paymentRecord(unit, monthKey, conceptKey) {
  return unit.paymentRecords?.[monthKey]?.[conceptKey] || {};
}

function ensurePaymentRecord(unit, monthKey, conceptKey) {
  if (!unit.paymentRecords) unit.paymentRecords = {};
  if (!unit.paymentRecords[monthKey]) unit.paymentRecords[monthKey] = {};
  if (!unit.paymentRecords[monthKey][conceptKey]) unit.paymentRecords[monthKey][conceptKey] = {};
  return unit.paymentRecords[monthKey][conceptKey];
}

function paymentDateCellMarkup(unit, conceptKey, monthKey) {
  const record = paymentRecord(unit, monthKey, conceptKey);
  return `
    <input
      class="payment-date-input"
      type="date"
      value="${escapeAttribute(record.paymentDate || "")}"
      data-payment-date="${conceptKey}"
      data-payment-month="${monthKey}"
      data-unit-id="${unit.id}"
      aria-label="Fecha de pago ${conceptKey} ${unit.unit}"
    >
  `;
}

function paymentReceiptCellMarkup(unit, conceptKey, monthKey) {
  const record = paymentRecord(unit, monthKey, conceptKey);
  const hasReceipt = Boolean(record.receiptName);
  return `
    <label class="secondary-button receipt-upload-button ${hasReceipt ? "has-file" : ""}" title="${escapeAttribute(record.receiptName || "Subir recibo PDF")}">
      <span data-icon="${hasReceipt ? "checkCircle" : "fileText"}" aria-hidden="true"></span>
      <span class="receipt-upload-text">${hasReceipt ? "PDF" : "Subir PDF"}</span>
      <input
        type="file"
        accept="application/pdf,.pdf"
        data-upload-receipt="${conceptKey}"
        data-payment-month="${monthKey}"
        data-unit-id="${unit.id}"
      >
    </label>
  `;
}

function paymentValidationCellMarkup(unit, conceptKey, monthKey) {
  const record = paymentRecord(unit, monthKey, conceptKey);
  const isValidated = Boolean(record.validated);
  return `
    <td class="payment-validation-column">
      <input
        class="validation-checkbox ${isValidated ? "is-validated" : ""}"
        type="checkbox"
        data-validation-check="${conceptKey}"
        data-concept="${conceptKey}"
        data-payment-month="${monthKey}"
        data-unit-id="${unit.id}"
        ${isValidated ? "checked disabled" : ""}
        aria-label="Validar ${conceptKey} ${unit.unit} ${formatMonthShort(monthKey)}"
      >
    </td>
  `;
}

function templateAttachmentMarkup(unit = {}) {
  const fileName = unit.templateAttachmentName || "";
  return `
    <div class="template-upload-group">
      <input id="unitTemplate" name="templateContract" placeholder="Machote de contrato propuesto" value="${escapeAttribute(unit.templateContract || "")}">
      <label class="secondary-button template-upload-button ${fileName ? "has-file" : ""}" title="${escapeAttribute(fileName || "Subir archivo de machote")}">
        <span data-icon="${fileName ? "checkCircle" : "fileText"}" aria-hidden="true"></span>
        <span class="template-upload-text">${fileName ? "Adjunto" : "Subir adjunto"}</span>
        <input
          type="file"
          name="templateAttachment"
          accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
          data-template-attachment
        >
      </label>
    </div>
    <small class="template-file-note" data-template-file-note>${fileName ? `Archivo adjunto: ${escapeAttribute(fileName)}` : "Adjunta el machote propuesto para revisarlo antes de asignar arrendatario."}</small>
  `;
}

function propertyUnitsTableMarkup(units) {
  const orderedUnits = sortedUnitsByNumber(units);
  return `
    <div class="table-panel property-units-panel">
      <div class="table-scroll">
        <table class="property-units-table">
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Arrendatario</th>
              ${propertyPaymentHeadersMarkup({ includeRecords: false })}
              <th class="contract-date-column">Contrato</th>
              ${propertyUnitContractValidationHeaderMarkup(orderedUnits)}
              <th class="contract-status-column">Estado</th>
              <th class="actions-column">Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${orderedUnits.map((unit) => `
              <tr>
                <td><strong>${unit.unit}</strong></td>
                <td class="unit-tenant-name-cell">${unitTenantNameLabel(unit)}</td>
                ${propertyPaymentCellsMarkup(unit, { includeRecords: false })}
                <td class="contract-date-column">${formatDate(unit.contractStart)} - ${formatDate(unit.contractEnd)}</td>
                ${propertyUnitContractValidationCellMarkup(unit)}
                <td class="contract-status-column"><span class="status-pill ${contractStatus(unit).className}">${contractStatus(unit).label}</span></td>
                <td class="actions-column">
                  <div class="unit-row-actions">
                    <button class="secondary-button" type="button" data-view-unit="${unit.id}">
                      <span data-icon="eye" aria-hidden="true"></span>
                      Ver unidad
                    </button>
                  </div>
                </td>
              </tr>
            `).join("")}
          </tbody>
        </table>
      </div>
      ${propertyUnitContractValidationActionsMarkup()}
    </div>
  `;
}

function propertyUnitContractValidationHeaderMarkup(units) {
  const eligibleUnits = units.filter(canValidateUnitContractTerm);
  const isAllValidated = eligibleUnits.length > 0 && eligibleUnits.every(isContractTermValidated);
  return `
    <th class="contract-validation-column">
      <label class="validation-header-control">
        <span>Validar</span>
        <input
          class="validation-checkbox"
          type="checkbox"
          data-unit-contract-validation-all
          ${isAllValidated ? "checked" : ""}
          aria-label="Seleccionar todas las validaciones de contrato"
        >
      </label>
    </th>
  `;
}

function propertyUnitContractValidationCellMarkup(unit) {
  const isEligible = canValidateUnitContractTerm(unit);
  const isValidated = isEligible && isContractTermValidated(unit);
  return `
    <td class="contract-validation-column">
      <input
        class="validation-checkbox ${isValidated ? "is-validated" : ""}"
        type="checkbox"
        data-unit-contract-validation-check
        data-unit-id="${unit.id}"
        ${isValidated ? "checked disabled" : ""}
        ${!isEligible ? "disabled" : ""}
        aria-label="Validar contrato ${unit.unit}"
      >
    </td>
  `;
}

function propertyUnitContractValidationActionsMarkup() {
  return `
    <div class="payment-method-validation-actions unit-contract-validation-actions">
      <button class="secondary-button" type="button" data-edit-unit-contract-validation>
        <span data-icon="settings" aria-hidden="true"></span>
        Editar Validacion
      </button>
      <button class="action-button" type="button" data-confirm-unit-contract-validation>
        <span data-icon="checkCircle" aria-hidden="true"></span>
        Validar
      </button>
    </div>
  `;
}

function canValidateUnitContractTerm(unit) {
  return Boolean(unit && !isUnitAvailable(unit) && unit.contractStart && unit.contractEnd && !isContractExpired(unit));
}

function clearContractTermValidation(unit) {
  if (!unit || (unit.contractTermValidated === false && !unit.contractTermValidatedAt)) return false;
  unit.contractTermValidated = false;
  delete unit.contractTermValidatedAt;
  return true;
}

function syncExpiredContractValidations(units = state.units) {
  let changed = false;
  (units || []).forEach((unit) => {
    if (isContractExpired(unit)) {
      changed = clearContractTermValidation(unit) || changed;
    }
  });
  return changed;
}

function bindPropertyUnitContractValidationActions(root = els.contentArea) {
  root.querySelectorAll("[data-unit-contract-validation-all]").forEach((checkbox) => {
    checkbox.addEventListener("change", () => togglePropertyUnitContractValidation(checkbox));
  });

  root.querySelectorAll("[data-unit-contract-validation-check]").forEach((checkbox) => {
    checkbox.addEventListener("change", () => updatePropertyUnitContractValidationHeader(checkbox.closest(".property-units-table")));
  });

  root.querySelectorAll("[data-confirm-unit-contract-validation]").forEach((button) => {
    button.addEventListener("click", () => confirmPropertyUnitContractValidation(button));
  });

  root.querySelectorAll("[data-edit-unit-contract-validation]").forEach((button) => {
    button.addEventListener("click", () => editPropertyUnitContractValidation(button));
  });

  root.querySelectorAll(".property-units-table").forEach(updatePropertyUnitContractValidationHeader);
}

function togglePropertyUnitContractValidation(headerCheckbox) {
  const table = headerCheckbox.closest(".property-units-table");
  if (!table) return;

  table.querySelectorAll("[data-unit-contract-validation-check]").forEach((checkbox) => {
    if (checkbox.disabled && checkbox.classList.contains("is-validated")) return;
    if (checkbox.disabled) return;
    checkbox.checked = headerCheckbox.checked;
  });
  updatePropertyUnitContractValidationHeader(table);
}

function updatePropertyUnitContractValidationHeader(table) {
  if (!table) return;
  const headerCheck = table.querySelector("[data-unit-contract-validation-all]");
  const rowChecks = Array.from(table.querySelectorAll("[data-unit-contract-validation-check]"))
    .filter((checkbox) => !checkbox.disabled || checkbox.classList.contains("is-validated"));
  if (!headerCheck || !rowChecks.length) return;

  const checkedCount = rowChecks.filter((checkbox) => checkbox.checked).length;
  headerCheck.checked = checkedCount === rowChecks.length;
  headerCheck.indeterminate = checkedCount > 0 && checkedCount < rowChecks.length;
}

function confirmPropertyUnitContractValidation(button) {
  const panel = button.closest(".property-units-panel");
  if (!panel) return;

  panel.querySelectorAll("[data-unit-contract-validation-check]").forEach((checkbox) => {
    const unit = state.units.find((item) => item.id === checkbox.dataset.unitId);
    if (!unit || !canValidateUnitContractTerm(unit)) return;

    unit.contractTermValidated = checkbox.checked;
    if (checkbox.checked) {
      unit.contractTermValidatedAt = new Date().toISOString();
      checkbox.disabled = true;
      checkbox.classList.add("is-validated");
    } else {
      delete unit.contractTermValidatedAt;
    }
  });

  saveState();
  render();
  toast("Validacion confirmada");
}

function editPropertyUnitContractValidation(button) {
  const panel = button.closest(".property-units-panel");
  if (!panel) return;

  panel.querySelectorAll("[data-unit-contract-validation-check]").forEach((checkbox) => {
    const unit = state.units.find((item) => item.id === checkbox.dataset.unitId);
    if (!canValidateUnitContractTerm(unit)) return;
    checkbox.disabled = false;
    checkbox.classList.remove("is-validated");
  });
  panel.querySelectorAll(".property-units-table").forEach(updatePropertyUnitContractValidationHeader);
  toast("Validaciones listas para editar");
}

function propertyUnitCatalogTableMarkup(units) {
  const orderedUnits = sortedUnitsByNumber(units);
  return `
    <div class="table-panel property-unit-catalog-panel">
      <div class="table-scroll">
        <table class="property-unit-catalog-table">
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Nombre del arrendatario</th>
              <th>Editar arrendatario</th>
              <th>Nivel</th>
              <th>Renta Total</th>
              <th>Mantenimiento Total</th>
              <th>Metros cuadrados</th>
              <th>Medidas</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${orderedUnits.map((unit) => `
              <tr>
                <td><strong>${unit.unit}</strong></td>
                <td class="unit-tenant-name-cell">${unitTenantNameLabel(unit)}</td>
                <td class="unit-tenant-edit-cell">
                  <button class="secondary-button" type="button" data-edit-unit-tenant="${unit.id}">
                    <span data-icon="settings" aria-hidden="true"></span>
                    Editar
                  </button>
                </td>
                <td>${unitLevelLabel(unit)}</td>
                <td><strong>${formatCurrency(paymentTotalAmount(unit, "rentTotal"))}</strong></td>
                <td><strong>${formatCurrency(paymentTotalAmount(unit, "maintenanceTotal"))}</strong></td>
                <td>${unitSquareMetersLabel(unit)}</td>
                <td>${unitMeasurementsLabel(unit)}</td>
                <td>
                  <div class="unit-catalog-actions">
                    <button class="secondary-button" type="button" data-edit-unit-catalog="${unit.id}">
                      <span data-icon="settings" aria-hidden="true"></span>
                      Editar
                    </button>
                    <button class="danger-button unit-delete-button" type="button" data-delete-unit-catalog="${unit.id}">
                      <span data-icon="x" aria-hidden="true"></span>
                      Eliminar Unidad
                    </button>
                  </div>
                </td>
              </tr>
            `).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function unitSquareMetersLabel(unit) {
  const squareMeters = Number(unit.squareMeters || 0);
  return squareMeters > 0 ? `${squareMeters} m2` : `<span class="muted-cell">Pendiente</span>`;
}

function unitTenantNameLabel(unit) {
  return isUnitAvailable(unit)
    ? `<span class="availability-chip">Disponible</span>`
    : `<strong>${escapeAttribute(unit.tenant || "Sin arrendatario")}</strong>`;
}

function unitLevelLabel(unit) {
  const level = String(unit.unitLevel || "").trim();
  return level ? escapeAttribute(level) : `<span class="muted-cell">Pendiente</span>`;
}

function unitMeasurementsLabel(unit) {
  const measurements = String(unit.measurements || "").trim();
  return measurements ? escapeAttribute(measurements) : `<span class="muted-cell">Pendiente</span>`;
}

function isUnitAvailable(unit) {
  return normalizeText(unit?.tenant) === "disponible";
}

function tenantCellMarkup(unit) {
  return isUnitAvailable(unit)
    ? `<span class="availability-chip">Disponible</span>`
    : unit.tenant;
}

function unitContractActionsMarkup(unit) {
  const hasTemplateAttachment = Boolean(unit.templateAttachmentName);
  return `
    <div class="contract-actions">
      <button class="secondary-button" type="button" data-contract="signed" data-unit-id="${unit.id}">
        <span data-icon="fileText" aria-hidden="true"></span>
        Ver PDF contrato
      </button>
      <button class="secondary-button" type="button" data-contract="template" data-unit-id="${unit.id}">
        <span data-icon="${hasTemplateAttachment ? "checkCircle" : "fileText"}" aria-hidden="true"></span>
        ${hasTemplateAttachment ? "Ver machote adjunto" : "Ver machote contrato"}
      </button>
    </div>
  `;
}

function legalPanelButtonMarkup(propertyId) {
  return `
    <button class="secondary-button legal-panel-button" type="button" data-legal-panel="${propertyId}">
      <span data-icon="scale" aria-hidden="true"></span>
      Ver Panel Legal
    </button>
  `;
}

function propertyLegalPanelTableMarkup(units) {
  const orderedUnits = sortedUnitsByNumber(units);
  return `
    <div class="table-panel property-legal-panel">
      <div class="table-scroll">
        <table class="property-legal-table">
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Arrendatario</th>
              <th>Renta Total</th>
              <th class="legal-compact-heading">
                <span class="legal-heading-lines">Mantenimiento <span>Total</span></span>
              </th>
              <th>Vigencia de contrato</th>
              <th>Estatus</th>
              <th>Editar vigencia</th>
              <th class="legal-compact-heading">
                <span class="legal-heading-lines">Machote de Contrato <span>en PDF</span></span>
              </th>
              <th>Contrato en PDF</th>
              <th class="legal-compact-heading">
                <span class="legal-heading-lines">Contrato de Nuevo <span>Periodo</span></span>
              </th>
            </tr>
          </thead>
          <tbody>
            ${orderedUnits.map((unit) => propertyLegalPanelRowMarkup(unit)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function propertyLegalPanelRowMarkup(unit) {
  const hasContract = !isUnitAvailable(unit);
  const status = hasContract ? contractStatus(unit) : { label: "Sin Contrato", className: "status-neutral" };
  const nextContract = unit.nextPeriodContract || "";

  return `
    <tr>
      <td><strong>${unit.unit}</strong></td>
      <td>${tenantCellMarkup(unit)}</td>
      <td><strong>${formatCurrency(paymentTotalAmount(unit, "rentTotal"))}</strong></td>
      <td><strong>${formatCurrency(paymentTotalAmount(unit, "maintenanceTotal"))}</strong></td>
      <td>
        <div class="legal-contract-term-inline">
          <strong>${hasContract ? `${formatDate(unit.contractStart)} - ${formatDate(unit.contractEnd)}` : "Sin Contrato"}</strong>
          ${hasContract ? `<div class="contract-validation-inline">${contractTermValidationMarkup(unit)}</div>` : ""}
        </div>
      </td>
      <td>
        <span class="status-pill ${status.className}">${status.label}</span>
      </td>
      <td>
        <button class="secondary-button legal-term-edit-button" type="button" data-edit-contract-term="${unit.id}">
          <span data-icon="settings" aria-hidden="true"></span>
          Editar fechas
        </button>
      </td>
      <td>${legalTemplateFileActionsMarkup(unit)}</td>
      <td>${legalSignedContractActionsMarkup(unit)}</td>
      <td>
        ${legalNextPeriodActionsMarkup(unit)}
      </td>
    </tr>
  `;
}

function legalNextPeriodActionsMarkup(unit) {
  const nextContract = unit.nextPeriodContract || "";
  return `
    <div class="legal-next-period-actions">
      <button class="action-button legal-generate-button" type="button" data-generate-next-contract="${unit.id}">
        <span data-icon="fileText" aria-hidden="true"></span>
        Generar
      </button>
      <button class="secondary-button legal-file-button" type="button" data-contract="nextPeriod" data-unit-id="${unit.id}" ${nextContract ? "" : "disabled"} title="${escapeAttribute(nextContract || "Pendiente")}">
        <span data-icon="eye" aria-hidden="true"></span>
        <span>Ver Contrato</span>
      </button>
    </div>
  `;
}

function legalContractFileButtonMarkup(unit, type) {
  const isTemplate = type === "template";
  const fileName = isTemplate
    ? unit.templateAttachmentName || `${unit.templateContract || "Machote de contrato"}.pdf`
    : unit.signedContract || "Contrato pendiente.pdf";

  return `
    <button class="secondary-button legal-file-button" type="button" data-contract="${type}" data-unit-id="${unit.id}" title="${escapeAttribute(fileName)}">
      <span data-icon="fileText" aria-hidden="true"></span>
      <span>${escapeAttribute(fileName)}</span>
    </button>
  `;
}

function legalTemplateFileActionsMarkup(unit) {
  const fileName = unit.templateAttachmentName || "";
  const buttonText = fileName || "Sin Archivo";
  return `
    <div class="legal-template-actions">
      <button class="secondary-button legal-file-button ${fileName ? "" : "is-empty-file"}" type="button" data-template-file-menu="${unit.id}" title="${escapeAttribute(buttonText)}">
        <span data-icon="fileText" aria-hidden="true"></span>
        <span>${escapeAttribute(buttonText)}</span>
      </button>
    </div>
  `;
}

function legalSignedContractActionsMarkup(unit) {
  if (!isContractUnsigned(unit)) {
    return `
      <div class="legal-contract-actions">
        ${legalContractFileButtonMarkup(unit, "signed")}
      </div>
    `;
  }

  return `
    <div class="legal-contract-actions">
      <button class="secondary-button legal-file-button legal-create-contract-button" type="button" data-generate-current-contract="${unit.id}">
        <span data-icon="fileText" aria-hidden="true"></span>
        <span>Generar contrato</span>
      </button>
    </div>
  `;
}

function legalContractFileChipMarkup(fileName) {
  return `
    <span class="legal-file-chip" title="${escapeAttribute(fileName)}">
      <span data-icon="fileText" aria-hidden="true"></span>
      <span>${escapeAttribute(fileName)}</span>
    </span>
  `;
}

function legalNextPeriodContractButtonMarkup(unit) {
  const fileName = unit.nextPeriodContract || "";
  return `
    <button class="secondary-button legal-file-button" type="button" data-contract="nextPeriod" data-unit-id="${unit.id}" title="${escapeAttribute(fileName)}">
      <span data-icon="fileText" aria-hidden="true"></span>
      <span>${escapeAttribute(fileName)}</span>
    </button>
  `;
}

function paymentStatusChipMarkup(unit, conceptKey, monthKey = null, options = {}) {
  if (options.fromValidation && monthKey) {
    const isValidated = Boolean(paymentRecord(unit, monthKey, conceptKey).validated);
    return `<span class="payment-status-chip ${isValidated ? "is-current" : "is-due"}">${isValidated ? "Al Corriente" : "Con Adeudo"}</span>`;
  }

  const monthKeys = allLedgerMonthKeys([unit]);
  const monthsToCheck = monthKey ? [monthKey] : monthKeys.length ? monthKeys : [currentMonthKey()];
  const paymentGroup = paymentGroupForConcept(conceptKey);
  const hasDebt = monthsToCheck.some((monthKey) => {
    if (paymentGroup) return getPaymentStatus(unit, monthKey, conceptKey) === "pending";
    return conceptAmountForMonth(unit, conceptKey, monthKey) > 0 && getPaymentStatus(unit, monthKey, conceptKey) === "pending";
  });

  return `<span class="payment-status-chip ${hasDebt ? "is-due" : "is-current"}">${hasDebt ? "Con Adeudo" : "Al Corriente"}</span>`;
}

function bindPropertyDetailActions(propertyId) {
  els.contentArea.querySelectorAll("[data-property-administration-view]").forEach((button) => {
    button.addEventListener("click", () => switchPropertyAdministrationView(propertyId, button.dataset.propertyAdministrationView));
  });
  els.contentArea.querySelector("[data-view-property-tenants]")?.addEventListener("click", () => openPropertyTenantsModal(propertyId));
  els.contentArea.querySelector("[data-property-team]")?.addEventListener("click", () => openPropertyTeamModal(propertyId));
  els.contentArea.querySelectorAll("[data-legal-panel]").forEach((button) => {
    button.addEventListener("click", () => openPropertyLegalPanelSection(button.dataset.legalPanel || propertyId));
  });
  const administrationContent = els.contentArea.querySelector("[data-property-administration-content]") || els.contentArea;
  bindPropertyAdministrationContentActions(propertyId, administrationContent);
}

function bindPropertyAdministrationContentActions(propertyId, root) {
  root.querySelectorAll(".table-scroll").forEach((scrollContainer) => {
    scrollContainer.scrollLeft = 0;
  });
  bindPaymentRecordActions(root);
  bindPropertyUnitContractValidationActions(root);
  bindPaymentMethodActions(root);
  bindBalanceValidationActions(root);
  root.querySelectorAll("[data-view-unit]").forEach((button) => {
    button.addEventListener("click", () => openPropertyUnitStatusSection(button.dataset.viewUnit, propertyId));
  });
  root.querySelectorAll("[data-delete-unit]").forEach((button) => {
    button.addEventListener("click", () => openDeleteUnitModal(button.dataset.deleteUnit, propertyId));
  });
  root.querySelectorAll("[data-contract]").forEach((button) => {
    button.addEventListener("click", () => openContractModal(button.dataset.unitId, button.dataset.contract));
  });
}

function bindPropertyUnitCatalogActions(propertyId) {
  els.contentArea.querySelector("[data-add-unit]")?.addEventListener("click", () => openUnitFormModal(propertyId));
  els.contentArea.querySelectorAll("[data-edit-unit-catalog]").forEach((button) => {
    button.addEventListener("click", () => openUnitFormModal(propertyId, button.dataset.editUnitCatalog));
  });
  els.contentArea.querySelectorAll("[data-edit-unit-tenant]").forEach((button) => {
    button.addEventListener("click", () => openUnitTenantModal(propertyId, button.dataset.editUnitTenant));
  });
  els.contentArea.querySelectorAll("[data-delete-unit-catalog]").forEach((button) => {
    button.addEventListener("click", () => openDeleteUnitModal(button.dataset.deleteUnitCatalog, propertyId, "properties_catalog"));
  });
}

function tenantTypeOptions(selectedType = "Persona moral") {
  return ["Persona moral", "Persona fisica", "Sin clasificar"]
    .map((type) => `<option value="${type}" ${type === selectedType ? "selected" : ""}>${type}</option>`)
    .join("");
}

function tenantCatalogOptions(selectedProfileId = "", selectedName = "", query = "") {
  const normalizedQuery = normalizeText(query);
  const tenants = tenantRows()
    .filter((tenant) => normalizeText(tenant.name) !== "disponible")
    .filter((tenant) => !normalizedQuery || normalizeText(tenant.name).includes(normalizedQuery))
    .sort((first, second) => first.name.localeCompare(second.name, "es"));
  const selectedText = normalizeText(selectedName);
  const hasSelectedTenant = tenants.some((tenant) =>
    tenant.id === selectedProfileId || normalizeText(tenant.name) === selectedText
  );

  const options = [
    `<option value="">Seleccionar arrendatario</option>`,
    ...tenants.map((tenant) => {
      const isSelected = tenant.id === selectedProfileId || normalizeText(tenant.name) === selectedText;
      return `<option value="${escapeAttribute(tenant.id)}" ${isSelected ? "selected" : ""}>${escapeAttribute(tenant.name)}</option>`;
    })
  ];

  if (selectedName && !hasSelectedTenant) {
    options.push(`<option value="" selected>${escapeAttribute(selectedName)}</option>`);
  }

  return options.join("");
}

function openUnitTenantModal(propertyId, unitId) {
  const property = getProperty(propertyId);
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!property || !unit) return;

  const profile = isUnitAvailable(unit) ? null : tenantProfileForUnit(unit);
  const tenantName = isUnitAvailable(unit) ? "" : unit.tenant || profile?.name || "";
  els.modalEyebrow.textContent = "Arrendatario de unidad";
  els.modalTitle.textContent = `${unit.unit} - ${property.name}`;
  els.modalBody.innerHTML = `
    <form id="unitTenantForm">
      <div class="form-grid">
        <div class="field">
          <label for="unitTenantProfile">Nombre del arrendatario</label>
          <select id="unitTenantProfile" name="tenantProfileId" required>
            ${tenantCatalogOptions(profile?.id || "", tenantName)}
          </select>
          <input id="unitTenantSearch" class="tenant-search-input" type="search" placeholder="Buscar por nombre" autocomplete="off">
        </div>
        <div class="field">
          <label for="unitTenantType">Tipo</label>
          <select id="unitTenantType" name="type">
            ${tenantTypeOptions(profile?.type || "Persona moral")}
          </select>
        </div>
        <div class="field">
          <label for="unitTenantRfc">RFC</label>
          <input id="unitTenantRfc" name="rfc" placeholder="RFC" value="${escapeAttribute(profile?.rfc || "")}">
        </div>
        <div class="field">
          <label for="unitTenantContact">Contacto</label>
          <input id="unitTenantContact" name="contact" placeholder="Contacto principal" value="${escapeAttribute(profile?.contact || tenantName)}">
        </div>
        <div class="field">
          <label for="unitTenantPhone">Telefono</label>
          <input id="unitTenantPhone" name="phone" placeholder="Telefono" value="${escapeAttribute(profile?.phone || "")}">
        </div>
        <div class="field">
          <label for="unitTenantEmail">Correo</label>
          <input id="unitTenantEmail" name="email" type="email" placeholder="correo@empresa.com" value="${escapeAttribute(profile?.email || "")}">
        </div>
      </div>
      <div class="field">
        <label for="unitTenantNotes">Notas</label>
        <textarea id="unitTenantNotes" name="notes" rows="3" placeholder="Informacion importante del arrendatario">${escapeAttribute(profile?.notes || "")}</textarea>
      </div>
      <div class="form-actions form-actions-split">
        <button class="secondary-button" type="button" data-modal-cancel>
          <span data-icon="x" aria-hidden="true"></span>
          Cancelar
        </button>
        <button class="secondary-button" type="button" data-mark-unit-available>
          <span data-icon="checkCircle" aria-hidden="true"></span>
          Marcar como disponible
        </button>
        <button class="action-button" type="submit">
          <span data-icon="users" aria-hidden="true"></span>
          Guardar arrendatario
        </button>
      </div>
    </form>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-modal-cancel]")?.addEventListener("click", closeModal);
  els.modalBody.querySelector("[data-mark-unit-available]")?.addEventListener("click", () => openMarkUnitAvailableConfirmation(propertyId, unitId));
  bindUnitTenantProfileSelect();
  els.modalBody.querySelector("#unitTenantForm")?.addEventListener("submit", (event) => saveUnitTenantFromForm(event, propertyId, unitId));
  openModal();
}

function bindUnitTenantProfileSelect() {
  const select = els.modalBody.querySelector("#unitTenantProfile");
  const searchInput = els.modalBody.querySelector("#unitTenantSearch");
  if (!select) return;

  select.dataset.selectedProfileId = select.value;
  select.addEventListener("change", () => {
    select.dataset.selectedProfileId = select.value;
    populateUnitTenantFields(select.value);
  });
  searchInput?.addEventListener("input", () => {
    const selectedProfileId = select.dataset.selectedProfileId || select.value;
    select.innerHTML = tenantCatalogOptions(selectedProfileId, "", searchInput.value);
    const hasSelectedOption = Array.from(select.options).some((option) => option.value === selectedProfileId);
    select.value = hasSelectedOption ? selectedProfileId : "";
  });
  if (select.value) populateUnitTenantFields(select.value);
}

function populateUnitTenantFields(profileId) {
  const tenant = tenantRows().find((item) => item.id === profileId);
  if (!tenant) return;

  const values = {
    unitTenantType: tenant.type || "Sin clasificar",
    unitTenantRfc: tenant.rfc || "",
    unitTenantContact: tenant.contact || tenant.name || "",
    unitTenantPhone: tenant.phone || "",
    unitTenantEmail: tenant.email || "",
    unitTenantNotes: tenant.notes || ""
  };

  Object.entries(values).forEach(([id, value]) => {
    const control = els.modalBody.querySelector(`#${id}`);
    if (!control) return;
    control.value = value;
  });
}

function saveUnitTenantFromForm(event, propertyId, unitId) {
  event.preventDefault();
  const property = getProperty(propertyId);
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  const data = new FormData(event.currentTarget);
  const profile = upsertTenantProfileFromUnit(data, propertyId);
  if (!property || !unit || !profile) {
    toast("Selecciona un arrendatario del catalogo.");
    return;
  }

  unit.tenant = profile.name;
  unit.tenantUserId = profile.userId || null;
  unit.tenantProfileId = profile.id;
  unit.tenantAssignmentManual = true;
  unit.contractStart = unit.contractStart || defaultUnitContractStart();
  unit.contractEnd = unit.contractEnd || defaultUnitContractEnd();
  unit.contractTermValidated = false;
  unit.signedContract = unit.signedContract || "Pendiente de firma";

  saveState();
  returnToPropertiesCatalog(propertyId);
  toast("Arrendatario actualizado");
}

function upsertTenantProfileFromUnit(data, propertyId) {
  const profileId = String(data.get("tenantProfileId") || "").trim();
  const profile = state.tenantProfiles.find((item) => item.id === profileId);
  if (!profile) return null;

  const email = String(data.get("email") || "").trim().toLowerCase();
  const linkedUser = email
    ? state.users.find((user) => user.role === "tenant" && normalizeText(user.email) === normalizeText(email))
    : null;

  const propertyIds = new Set([...(profile.propertyIds || []), propertyId]);
  profile.userId = profile.userId || linkedUser?.id || null;
  profile.type = String(data.get("type") || "Sin clasificar");
  profile.rfc = String(data.get("rfc") || "").trim().toUpperCase();
  profile.phone = String(data.get("phone") || "").trim();
  profile.email = email || profile.email || linkedUser?.email || "";
  profile.contact = String(data.get("contact") || "").trim() || profile.name;
  profile.status = profile.userId ? "Activo" : profile.status || "Pendiente de portal";
  profile.propertyIds = Array.from(propertyIds);
  profile.notes = String(data.get("notes") || "").trim();
  return profile;
}

function openMarkUnitAvailableConfirmation(propertyId, unitId) {
  const property = getProperty(propertyId);
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!property || !unit) return;

  if (!canMarkUnitAsAvailable(unit)) {
    openMarkUnitAvailableBlockedModal(propertyId, unitId);
    return;
  }

  els.modalEyebrow.textContent = "Confirmar disponibilidad";
  els.modalTitle.textContent = unit.unit;
  els.modalBody.innerHTML = `
    <section class="delete-confirmation">
      <p class="delete-question">Estas seguro que quieres marcarlo como disponible?</p>
      <div class="modal-grid">
        <div class="detail-box">
          <span>Propiedad</span>
          <strong>${property.name}</strong>
        </div>
        <div class="detail-box">
          <span>Unidad</span>
          <strong>${unit.unit}</strong>
        </div>
        <div class="detail-box">
          <span>Arrendatario actual</span>
          <strong>${isUnitAvailable(unit) ? "Disponible" : unit.tenant}</strong>
        </div>
        <div class="detail-box">
          <span>Accion</span>
          <strong>Marcar como disponible</strong>
        </div>
        <div class="detail-box">
          <span>Vigencia</span>
          <strong>${formatDate(unit.contractStart)} - ${formatDate(unit.contractEnd)}</strong>
        </div>
        <div class="detail-box">
          <span>Validacion de vigencia</span>
          <strong>${contractTermValidationLabel(unit)}</strong>
        </div>
      </div>
      <div class="form-actions delete-confirmation-actions">
        <button class="secondary-button" type="button" data-cancel-mark-available>
          <span data-icon="x" aria-hidden="true"></span>
          No
        </button>
        <button class="action-button" type="button" data-confirm-mark-available>
          <span data-icon="checkCircle" aria-hidden="true"></span>
          Si
        </button>
      </div>
    </section>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-cancel-mark-available]")?.addEventListener("click", () => openUnitTenantModal(propertyId, unitId));
  els.modalBody.querySelector("[data-confirm-mark-available]")?.addEventListener("click", () => markUnitAsAvailable(propertyId, unitId));
  openModal();
}

function openMarkUnitAvailableBlockedModal(propertyId, unitId) {
  const property = getProperty(propertyId);
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!property || !unit) return;

  els.modalEyebrow.textContent = "Disponibilidad bloqueada";
  els.modalTitle.textContent = unit.unit;
  els.modalBody.innerHTML = `
    <section class="delete-confirmation">
      <p class="delete-question">No se puede marcar como disponible porque la fecha de inicio y fecha de fin de la vigencia del contrato estan validadas.</p>
      <div class="modal-grid">
        <div class="detail-box">
          <span>Propiedad</span>
          <strong>${property.name}</strong>
        </div>
        <div class="detail-box">
          <span>Arrendatario actual</span>
          <strong>${isUnitAvailable(unit) ? "Disponible" : unit.tenant}</strong>
        </div>
        <div class="detail-box">
          <span>Contrato</span>
          <strong>${contractStatus(unit).label}</strong>
        </div>
        <div class="detail-box">
          <span>Validacion de vigencia</span>
          <strong>${contractTermValidationLabel(unit)}</strong>
        </div>
      </div>
      <div class="form-actions">
        <button class="secondary-button" type="button" data-back-unit-tenant-form>
          <span data-icon="x" aria-hidden="true"></span>
          Cerrar
        </button>
      </div>
    </section>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-back-unit-tenant-form]")?.addEventListener("click", () => openUnitTenantModal(propertyId, unitId));
  openModal();
}

function canMarkUnitAsAvailable(unit) {
  if (!unit || isUnitAvailable(unit) || !unit.contractEnd) return true;
  if (isContractExpired(unit)) {
    clearContractTermValidation(unit);
    return true;
  }
  return !isContractTermValidated(unit);
}

function markUnitAsAvailable(propertyId, unitId) {
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!unit) return;
  if (!canMarkUnitAsAvailable(unit)) {
    openMarkUnitAvailableBlockedModal(propertyId, unitId);
    return;
  }

  unit.tenant = "Disponible";
  unit.tenantUserId = null;
  unit.tenantProfileId = null;
  unit.tenantAssignmentManual = true;
  unit.contractTermValidated = false;
  unit.contractStart = "";
  unit.contractEnd = "";
  unit.signedContract = "Pendiente de firma";
  unit.nextPeriodContract = "";
  unit.nextPeriodContractStart = "";
  unit.nextPeriodContractEnd = "";
  markUnitPaymentsAsPaid(unit);

  saveState();
  returnToPropertiesCatalog(propertyId);
  toast("Unidad marcada como disponible");
}

function markUnitPaymentsAsPaid(unit) {
  unit.paymentStatus = paymentConcepts.reduce((result, concept) => {
    result[concept.key] = "paid";
    return result;
  }, {});
  if (!unit.paymentLedger) unit.paymentLedger = {};
  const monthKeys = new Set([currentMonthKey(), ...generatedLedgerMonthKeys(), ...Object.keys(unit.paymentLedger || {})]);
  monthKeys.forEach((monthKey) => {
    unit.paymentLedger[monthKey] = paymentConcepts.reduce((result, concept) => {
      result[concept.key] = "paid";
      return result;
    }, {});
  });
}

function activeTenantUnitsForProperty(propertyId) {
  return sortedUnitsByNumber(propertyUnits(propertyId).filter((unit) =>
    unit.tenant !== "Disponible" && contractStatus(unit).kind !== "expired"
  ));
}

function tenantProfileForUnit(unit) {
  return tenantRows().find((tenant) =>
    tenant.userId === unit.tenantUserId ||
    tenant.id === unit.tenantProfileId ||
    normalizeText(tenant.name) === normalizeText(unit.tenant)
  );
}

function propertyTenantDebtItems(unit) {
  const startMonth = unit.contractStart ? monthKeyFromDate(new Date(`${unit.contractStart}T00:00:00`)) : "";
  const endMonth = unit.contractEnd ? monthKeyFromDate(new Date(`${unit.contractEnd}T00:00:00`)) : "";

  return propertyBalanceMonthKeys().flatMap((monthKey, index) => {
    if (startMonth && monthKey < startMonth) return [];
    if (endMonth && monthKey > endMonth) return [];

    return propertyPaymentColumns
      .filter((concept) => conceptAmountForMonth(unit, concept.key, monthKey) > 0 && !paymentRecord(unit, monthKey, concept.key).validated)
      .map((concept) => ({
        monthKey,
        period: `${index + 1} ${index === 0 ? "mes" : "meses"} atras`,
        concept,
        amount: conceptAmountForMonth(unit, concept.key, monthKey)
      }));
  });
}

function openPropertyTenantsModal(propertyId) {
  const property = getProperty(propertyId);
  if (!property) return;

  const units = activeTenantUnitsForProperty(property.id);
  els.modal?.classList.add("modal-wide");
  els.modalEyebrow.textContent = "Catalogo de Arrendatarios";
  els.modalTitle.textContent = property.name;
  els.modalBody.innerHTML = `
    <section class="property-tenants-window">
      <div class="section-header">
        <div>
          <p class="eyebrow">Contratos vigentes</p>
          <h3>Arrendatarios por unidad</h3>
          <p class="muted">${property.type} - ${property.location}. Se muestran solo unidades con arrendatario y contrato vigente.</p>
        </div>
      </div>
      ${units.length ? propertyTenantCatalogTableMarkup(units) : emptyState("No hay arrendatarios con contrato vigente en esta propiedad.")}
      <div id="tenantDebtDetail" class="tenant-debt-detail" hidden></div>
    </section>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelectorAll("[data-tenant-debt]").forEach((button) => {
    button.addEventListener("click", () => renderTenantDebtDetail(button.dataset.tenantDebt));
  });
  els.modalBody.querySelectorAll("[data-contract]").forEach((button) => {
    button.addEventListener("click", () => openContractModal(button.dataset.unitId, button.dataset.contract));
  });
  openModal();
}

function propertyTenantCatalogTableMarkup(units) {
  return `
    <div class="table-panel">
      <div class="table-scroll">
        <table class="property-tenants-table">
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Arrendatario</th>
              <th>Estatus</th>
              <th>Contrato</th>
              <th>Vigencia</th>
              <th>Pagos</th>
            </tr>
          </thead>
          <tbody>
            ${units.map((unit) => propertyTenantCatalogRowMarkup(unit)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function propertyTenantCatalogRowMarkup(unit) {
  const tenant = tenantProfileForUnit(unit);
  const status = contractStatus(unit);
  const debtItems = propertyTenantDebtItems(unit);
  const hasDebt = debtItems.length > 0;
  const debtTotal = debtItems.reduce((sum, item) => sum + item.amount, 0);

  return `
    <tr>
      <td><strong>${unit.unit}</strong></td>
      <td class="primary-cell">
        <strong>${unit.tenant}</strong>
        <small>${tenant?.email || tenantEmail(unit)}</small>
      </td>
      <td>
        <span class="status-pill ${tenant?.status === "Inactivo" ? "status-pending" : "status-paid"}">
          ${tenant?.status || "Activo"}
        </span>
      </td>
      <td>
        <strong>${unit.signedContract || "Sin contrato firmado"}</strong>
        <div class="contract-actions tenant-contract-actions">
          <button class="secondary-button" type="button" data-contract="signed" data-unit-id="${unit.id}">
            <span data-icon="eye" aria-hidden="true"></span>
            Ver contrato
          </button>
        </div>
      </td>
      <td>
        <strong>${formatDate(unit.contractStart)} - ${formatDate(unit.contractEnd)}</strong>
        <p><span class="status-pill ${status.className}">${status.label}</span></p>
      </td>
      <td>
        ${hasDebt ? `
          <button class="status-pill status-danger tenant-debt-button" type="button" data-tenant-debt="${unit.id}" title="Ver detalle de adeudos">
            Con Adeudo
          </button>
          <small>${formatCurrency(debtTotal)}</small>
        ` : `
          <span class="status-pill status-paid">Al Corriente</span>
        `}
      </td>
    </tr>
  `;
}

function renderTenantDebtDetail(unitId) {
  const unit = state.units.find((item) => item.id === unitId);
  if (!unit) return;

  const debtItems = propertyTenantDebtItems(unit);
  const total = debtItems.reduce((sum, item) => sum + item.amount, 0);
  const container = els.modalBody.querySelector("#tenantDebtDetail");
  if (!container) return;

  container.hidden = false;
  container.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">Detalle de adeudo</p>
        <h3>${unit.unit} - ${unit.tenant}</h3>
        <p class="muted">${debtItems.length ? `${debtItems.length} conceptos pendientes por ${formatCurrency(total)}.` : "Sin adeudos registrados."}</p>
      </div>
    </div>
    ${debtItems.length ? `
      <div class="table-panel">
        <div class="table-scroll">
          <table class="tenant-debt-table">
            <thead>
              <tr>
                <th>Periodo</th>
                <th>Mes</th>
                <th>Categoria</th>
                <th>Importe</th>
                <th>Estatus</th>
              </tr>
            </thead>
            <tbody>
              ${debtItems.map((item) => `
                <tr>
                  <td>${item.period}</td>
                  <td>${formatMonthLabel(item.monthKey)}</td>
                  <td>${item.concept.label}</td>
                  <td><strong>${formatCurrency(item.amount)}</strong></td>
                  <td><span class="status-pill status-danger">Con Adeudo</span></td>
                </tr>
              `).join("")}
            </tbody>
          </table>
        </div>
      </div>
    ` : emptyState("Este arrendatario aparece al corriente.")}
  `;
  container.scrollIntoView({ behavior: "smooth", block: "start" });
}

function openPaymentMethodModal(propertyId) {
  const property = getProperty(propertyId);
  if (!property) return;

  const units = propertyUnits(property.id);
  const monthKeys = propertyBalanceDisplayMonthKeys();
  els.modal?.classList.add("modal-wide");
  els.modalEyebrow.textContent = "Metodo de pago";
  els.modalTitle.textContent = property.name;
  els.modalBody.innerHTML = `
    <section class="payment-method-window">
      <div class="section-header">
        <div>
          <p class="eyebrow">Captura manual</p>
          <h3>Parciales de renta y mantenimiento</h3>
          <p class="muted">Captura Renta 1, Renta 2, Mantenimiento 1 y Mantenimiento 2 por mes. Las sumas deben cuadrar con Renta Total y Mantenimiento Total.</p>
        </div>
      </div>
      ${units.length ? `
        <div class="payment-method-stack">
          ${monthKeys.map((monthKey) => paymentMethodMonthPanelMarkup(units, monthKey, monthsBackFromCurrentMonth(monthKey))).join("")}
        </div>
        <div class="form-actions">
          <button class="secondary-button" type="button" data-modal-cancel>
            <span data-icon="x" aria-hidden="true"></span>
            Cerrar
          </button>
          <button class="action-button" type="button" data-save-payment-method="${property.id}">
            <span data-icon="checkCircle" aria-hidden="true"></span>
            Guardar metodo de pago
          </button>
        </div>
      ` : emptyState("Aun no hay unidades registradas para esta propiedad.")}
    </section>
  `;

  injectIcons(els.modalBody);
  bindPaymentMethodActions(els.modalBody);
  openModal();
}

function paymentMethodMonthPanelMarkup(units, monthKey, monthsBack) {
  const periodLabel = monthsBack === 0 ? "Mes corriente" : `${monthsBack} ${monthsBack === 1 ? "mes atras" : "meses atras"}`;
  const isExpanded = monthsBack === 0;
  const contentId = `paymentMethodMonthContent-${monthKey}`;
  return `
    <section class="table-panel payment-method-month-panel payment-method-month-accordion ${isExpanded ? "is-expanded" : "is-collapsed"}" data-payment-month-panel="${monthKey}">
      <button
        class="month-panel-header payment-method-month-toggle"
        type="button"
        data-payment-month-toggle="${monthKey}"
        aria-expanded="${isExpanded ? "true" : "false"}"
        aria-controls="${contentId}"
        aria-label="${isExpanded ? "Ocultar" : "Mostrar"} ${formatMonthLabel(monthKey)}"
      >
        <span class="payment-method-month-heading">
          <span class="eyebrow">${periodLabel}</span>
          <span class="payment-method-month-title">${formatMonthLabel(monthKey)}</span>
        </span>
        <span class="payment-method-month-toggle-meta">
          <span class="month-tag">${formatMonthShort(monthKey)}</span>
          <span class="payment-method-month-toggle-icon" data-payment-month-toggle-icon aria-hidden="true">${isExpanded ? "-" : "+"}</span>
        </span>
      </button>
      <div id="${contentId}" class="payment-method-month-content" data-payment-month-content ${isExpanded ? "" : "hidden"}>
        <div class="table-scroll">
          <table class="payment-method-table">
            <thead>
              <tr>
                <th>Unidad</th>
                <th>Arrendatario</th>
                <th class="payment-method-compact-total payment-method-rent-section payment-method-section-start">Renta<br>Total</th>
                <th class="payment-method-compact-amount payment-method-rent-section">Renta 1<br>Transferencia/<br>Deposito</th>
                ${paymentMethodConceptValidationHeaderMarkup(units, monthKey, "rent")}
                <th class="payment-method-compact-amount payment-method-rent-section">Renta<br>2</th>
                ${paymentMethodConceptValidationHeaderMarkup(units, monthKey, "extraordinary")}
                <th class="payment-method-compact-sum payment-method-rent-section payment-method-section-end">Suma<br>Renta</th>
                <th class="payment-method-compact-total payment-method-maintenance-section payment-method-section-start">Mantenimiento<br>Total</th>
                <th class="payment-method-compact-amount payment-method-maintenance-section">Mantenimiento 1<br>Transferencia/<br>Deposito</th>
                ${paymentMethodConceptValidationHeaderMarkup(units, monthKey, "maintenance")}
                <th class="payment-method-compact-amount payment-method-maintenance-section">Mantenimiento<br>2</th>
                ${paymentMethodConceptValidationHeaderMarkup(units, monthKey, "services")}
                <th class="payment-method-compact-sum payment-method-maintenance-section payment-method-section-end">Suma<br>Mantenimiento</th>
              </tr>
            </thead>
            <tbody>
              ${units.map((unit) => paymentMethodRowMarkup(unit, monthKey)).join("")}
            </tbody>
            <tfoot>
              ${paymentMethodTotalsRowMarkup(units, monthKey)}
            </tfoot>
          </table>
        </div>
        <div class="payment-method-validation-actions">
          <button class="secondary-button" type="button" data-edit-payment-method-validation="${monthKey}">
            <span data-icon="settings" aria-hidden="true"></span>
            Editar Validacion
          </button>
          <button class="action-button" type="button" data-confirm-payment-method-rent-validation="${monthKey}">
            <span data-icon="checkCircle" aria-hidden="true"></span>
            Validar
          </button>
        </div>
      </div>
    </section>
  `;
}

function paymentMethodAdvancePanelMarkup(units) {
  const monthKey = currentMonthKey();

  return `
    <section class="table-panel payment-method-month-panel payment-method-advance-panel">
      <div class="month-panel-header">
        <div>
          <p class="eyebrow">Registro de anticipos</p>
          <h3>Anticipos</h3>
          <p class="muted">Captura pagos anticipados e indica el periodo de meses que cubren.</p>
        </div>
        <span class="month-tag">${formatMonthShort(monthKey)}</span>
      </div>
      <div class="table-scroll">
        <table class="payment-method-table advance-payment-table">
          <thead>
            <tr>
              <th>Unidad</th>
              <th>Arrendatario</th>
              <th class="advance-period-column">Meses anticipados</th>
              <th class="payment-method-compact-total payment-method-rent-section payment-method-section-start">Renta<br>Total</th>
              <th class="payment-method-compact-amount payment-method-rent-section">Renta 1<br>Transferencia/<br>Deposito</th>
              ${advancePaymentConceptValidationHeaderMarkup(units, monthKey, "rent")}
              <th class="payment-method-compact-amount payment-method-rent-section">Renta<br>2</th>
              ${advancePaymentConceptValidationHeaderMarkup(units, monthKey, "extraordinary")}
              <th class="payment-method-compact-sum payment-method-rent-section payment-method-section-end">Suma<br>Renta</th>
              <th class="payment-method-compact-total payment-method-maintenance-section payment-method-section-start">Mantenimiento<br>Total</th>
              <th class="payment-method-compact-amount payment-method-maintenance-section">Mantenimiento 1<br>Transferencia/<br>Deposito</th>
              ${advancePaymentConceptValidationHeaderMarkup(units, monthKey, "maintenance")}
              <th class="payment-method-compact-amount payment-method-maintenance-section">Mantenimiento<br>2</th>
              ${advancePaymentConceptValidationHeaderMarkup(units, monthKey, "services")}
              <th class="payment-method-compact-sum payment-method-maintenance-section payment-method-section-end">Suma<br>Mantenimiento</th>
            </tr>
          </thead>
          <tbody>
            ${units.map((unit) => paymentMethodAdvanceRowMarkup(unit, monthKey)).join("")}
          </tbody>
          <tfoot>
            ${paymentMethodAdvanceTotalsRowMarkup(units, monthKey)}
          </tfoot>
        </table>
      </div>
      <div class="payment-method-validation-actions">
        <button class="secondary-button" type="button" data-edit-advance-payment-validation="${monthKey}">
          <span data-icon="settings" aria-hidden="true"></span>
          Editar Validacion
        </button>
        <button class="action-button" type="button" data-confirm-advance-payment-validation="${monthKey}">
          <span data-icon="checkCircle" aria-hidden="true"></span>
          Validar
        </button>
      </div>
    </section>
  `;
}

function paymentMethodAdvanceRowMarkup(unit, monthKey) {
  const rentTotal = paymentTotalAmount(unit, "rentTotal");
  const maintenanceTotal = paymentTotalAmount(unit, "maintenanceTotal");
  const rent1 = advancePaymentAmountFor(unit, monthKey, "rent", conceptAmountForMonth(unit, "rent", monthKey));
  const rent2 = advancePaymentAmountFor(unit, monthKey, "extraordinary", conceptAmountForMonth(unit, "extraordinary", monthKey));
  const maintenance1 = advancePaymentAmountFor(unit, monthKey, "maintenance", conceptAmountForMonth(unit, "maintenance", monthKey));
  const maintenance2 = advancePaymentAmountFor(unit, monthKey, "services", conceptAmountForMonth(unit, "services", monthKey));
  const isAvailable = isUnitAvailable(unit) || !isUnitBillableForMonth(unit, monthKey);
  const periodSelection = advancePaymentPeriodSelection(unit, monthKey);

  return `
    <tr class="${isAvailable ? "payment-method-row-disabled" : ""}" data-advance-payment-row data-rent-total="${rentTotal}" data-maintenance-total="${maintenanceTotal}" data-unit-available="${isAvailable ? "true" : "false"}">
      <td><strong>${unit.unit}</strong></td>
      <td>${tenantCellMarkup(unit)}</td>
      <td class="advance-period-cell">
        ${advanceMonthSelectMarkup(unit, "desde", periodSelection.fromMonthKey, isAvailable)}
        ${advanceMonthSelectMarkup(unit, "hasta", periodSelection.toMonthKey, isAvailable)}
      </td>
      <td class="payment-method-readonly-total payment-method-rent-section payment-method-section-start">
        <strong>${formatCurrency(rentTotal)}</strong>
        <small>Catalogo de unidades</small>
      </td>
      ${paymentMethodAdvanceAmountCellMarkup(unit, monthKey, "rent", rent1, isAvailable)}
      ${advancePaymentConceptValidationCellMarkup(unit, monthKey, "rent")}
      ${paymentMethodAdvanceAmountCellMarkup(unit, monthKey, "extraordinary", rent2, isAvailable)}
      ${advancePaymentConceptValidationCellMarkup(unit, monthKey, "extraordinary")}
      <td class="payment-method-rent-section payment-method-section-end" data-advance-payment-sum="rent">${paymentMethodSumMarkup(rent1 + rent2, rentTotal)}</td>
      <td class="payment-method-readonly-total payment-method-maintenance-section payment-method-section-start">
        <strong>${formatCurrency(maintenanceTotal)}</strong>
        <small>Catalogo de unidades</small>
      </td>
      ${paymentMethodAdvanceAmountCellMarkup(unit, monthKey, "maintenance", maintenance1, isAvailable)}
      ${advancePaymentConceptValidationCellMarkup(unit, monthKey, "maintenance")}
      ${paymentMethodAdvanceAmountCellMarkup(unit, monthKey, "services", maintenance2, isAvailable)}
      ${advancePaymentConceptValidationCellMarkup(unit, monthKey, "services")}
      <td class="payment-method-maintenance-section payment-method-section-end" data-advance-payment-sum="maintenance">${paymentMethodSumMarkup(maintenance1 + maintenance2, maintenanceTotal)}</td>
    </tr>
  `;
}

function advancePaymentMonthOptions() {
  const baseDate = monthKeyToDate(currentMonthKey());
  return Array.from({ length: 18 }, (_, index) => {
    const optionDate = new Date(baseDate);
    optionDate.setMonth(optionDate.getMonth() + index);
    const monthKey = monthKeyFromDate(optionDate);
    return {
      monthKey,
      label: formatMonthLabel(monthKey)
    };
  });
}

function advancePaymentDefaultEndMonthKey() {
  return currentMonthKey();
}

function normalizeAdvancePaymentMonthKey(value, fallback = currentMonthKey()) {
  if (/^\d{4}-\d{2}$/.test(String(value || ""))) return String(value);
  const numericMonth = Number(value);
  if (Number.isInteger(numericMonth) && numericMonth >= 0 && numericMonth <= 11) {
    const currentYear = monthKeyToDate(currentMonthKey()).getFullYear();
    return `${currentYear}-${String(numericMonth + 1).padStart(2, "0")}`;
  }
  return fallback;
}

function advancePaymentPeriodSelection(unit, monthKey) {
  const recordWithPeriod = paymentMethodValidationConcepts
    .map((conceptKey) => advancePaymentValidationRecord(unit, monthKey, conceptKey))
    .find((record) => record.fromMonthKey || record.toMonthKey || (Number.isInteger(record.fromMonthIndex) && Number.isInteger(record.toMonthIndex)));

  return {
    fromMonthKey: normalizeAdvancePaymentMonthKey(recordWithPeriod?.fromMonthKey ?? recordWithPeriod?.fromMonthIndex, currentMonthKey()),
    toMonthKey: normalizeAdvancePaymentMonthKey(recordWithPeriod?.toMonthKey ?? recordWithPeriod?.toMonthIndex, advancePaymentDefaultEndMonthKey())
  };
}

function advanceMonthSelectMarkup(unit, field, selectedMonthKey, isDisabled = false) {
  const label = field === "desde" ? "Desde" : "Hasta";
  const options = advancePaymentMonthOptions();
  const normalizedSelection = options.some((option) => option.monthKey === selectedMonthKey)
    ? selectedMonthKey
    : field === "desde" ? currentMonthKey() : advancePaymentDefaultEndMonthKey();
  return `
    <label class="advance-month-field">
      <span>${label}</span>
      <select data-advance-month="${field}" data-unit-id="${unit.id}" ${isDisabled ? "disabled" : ""}>
        ${options.map((option) => `<option value="${option.monthKey}" ${option.monthKey === normalizedSelection ? "selected" : ""}>${option.label}</option>`).join("")}
      </select>
    </label>
  `;
}

function paymentMethodAdvanceAmountCellMarkup(unit, monthKey, conceptKey, value, isDisabled = false) {
  const isValidated = isAdvancePaymentConceptValidated(unit, monthKey, conceptKey);
  const isLocked = isDisabled || isValidated;
  return `
    <td class="payment-method-amount-cell ${paymentMethodSectionClass(conceptKey)} ${isLocked ? "is-frozen" : ""}">
      <input
        class="payment-method-input ${isLocked ? "is-validated" : ""}"
        type="number"
        min="0"
        step="1"
        value="${Number(value || 0)}"
        data-advance-payment-amount="${conceptKey}"
        data-payment-month="${monthKey}"
        data-unit-id="${unit.id}"
        ${isLocked ? "disabled" : ""}
        aria-label="Anticipo ${paymentMethodConceptLabel(conceptKey)} ${unit.unit}"
      >
    </td>
  `;
}

function advancePaymentConceptValidationHeaderMarkup(units, monthKey, conceptKey) {
  const editableUnits = units.filter((unit) => !isUnitAvailable(unit));
  const isAllValidated = editableUnits.length > 0 && editableUnits.every((unit) => isAdvancePaymentConceptValidated(unit, monthKey, conceptKey));
  return `
    <th class="payment-method-validation-column ${paymentMethodSectionClass(conceptKey)}">
      <label class="validation-header-control">
        <span>Validar</span>
        <input
          class="validation-checkbox"
          type="checkbox"
          data-advance-payment-validation-all
          data-payment-month="${monthKey}"
          data-payment-concept="${conceptKey}"
          ${isAllValidated ? "checked" : ""}
          aria-label="Seleccionar validaciones de anticipo ${paymentMethodConceptLabel(conceptKey)} ${formatMonthShort(monthKey)}"
        >
      </label>
    </th>
  `;
}

function advancePaymentConceptValidationCellMarkup(unit, monthKey, conceptKey) {
  const isValidated = isAdvancePaymentConceptValidated(unit, monthKey, conceptKey);
  const isAvailable = isUnitAvailable(unit);
  return `
    <td class="payment-method-validation-column ${paymentMethodSectionClass(conceptKey)}">
      <input
        class="validation-checkbox ${isValidated && !isAvailable ? "is-validated" : ""}"
        type="checkbox"
        data-advance-payment-validation-check
        data-payment-month="${monthKey}"
        data-payment-concept="${conceptKey}"
        data-unit-id="${unit.id}"
        ${isValidated && !isAvailable ? "checked" : ""}
        ${isValidated || isAvailable ? "disabled" : ""}
        aria-label="Validar anticipo ${paymentMethodConceptLabel(conceptKey)} ${unit.unit} ${formatMonthShort(monthKey)}"
      >
    </td>
  `;
}

function advancePaymentValidationRecord(unit, monthKey, conceptKey) {
  const monthRecords = unit.advancePaymentValidation?.[monthKey] || {};
  return monthRecords[conceptKey] || {};
}

function ensureAdvancePaymentValidationRecord(unit, monthKey, conceptKey) {
  if (!unit.advancePaymentValidation) unit.advancePaymentValidation = {};
  if (!unit.advancePaymentValidation[monthKey]) unit.advancePaymentValidation[monthKey] = {};
  if (!unit.advancePaymentValidation[monthKey][conceptKey]) unit.advancePaymentValidation[monthKey][conceptKey] = {};
  return unit.advancePaymentValidation[monthKey][conceptKey];
}

function isAdvancePaymentConceptValidated(unit, monthKey, conceptKey) {
  return Boolean(advancePaymentValidationRecord(unit, monthKey, conceptKey).validated);
}

function advancePaymentAmountFor(unit, monthKey, conceptKey, fallback = 0) {
  const record = advancePaymentValidationRecord(unit, monthKey, conceptKey);
  return typeof record.amount === "number" ? record.amount : fallback;
}

function advancePaymentPeriodFromRow(row) {
  const selectedFrom = normalizeAdvancePaymentMonthKey(row?.querySelector('[data-advance-month="desde"]')?.value, currentMonthKey());
  const selectedTo = normalizeAdvancePaymentMonthKey(row?.querySelector('[data-advance-month="hasta"]')?.value, advancePaymentDefaultEndMonthKey());
  const fromMonthKey = selectedFrom <= selectedTo ? selectedFrom : selectedTo;
  const toMonthKey = selectedFrom <= selectedTo ? selectedTo : selectedFrom;
  const monthKeys = advancePaymentMonthOptions()
    .map((option) => option.monthKey)
    .filter((monthKey) => monthKey >= fromMonthKey && monthKey <= toMonthKey);

  return {
    fromIndex: monthKeyToDate(fromMonthKey).getMonth(),
    toIndex: monthKeyToDate(toMonthKey).getMonth(),
    fromMonthKey: monthKeys[0] || fromMonthKey,
    toMonthKey: monthKeys[monthKeys.length - 1] || toMonthKey,
    monthKeys
  };
}

function advancePaymentSourceKey(advanceMonthKey, conceptKey) {
  return `advance:${advanceMonthKey}:${conceptKey}`;
}

function registerAdvancePaymentInMonthlyRecords(unit, advanceMonthKey, conceptKey, record) {
  const sourceKey = advancePaymentSourceKey(advanceMonthKey, conceptKey);
  const coveredMonths = Array.isArray(record.coveredMonths) ? record.coveredMonths : [];

  coveredMonths.forEach((coveredMonthKey) => {
    const monthlyRecord = ensurePaymentRecord(unit, coveredMonthKey, conceptKey);
    monthlyRecord.amount = Number(record.amount || 0);
    monthlyRecord.validated = true;
    monthlyRecord.validatedAt = record.validatedAt || new Date().toISOString();
    monthlyRecord.advancePaymentSource = true;
    monthlyRecord.advancePaymentSourceKey = sourceKey;
    monthlyRecord.advancePaymentPeriod = {
      from: record.fromMonthKey,
      to: record.toMonthKey
    };
    setPaymentStatus(unit, coveredMonthKey, conceptKey, "paid");
  });
}

function removeAdvancePaymentFromMonthlyRecords(unit, advanceMonthKey, conceptKey, monthKeys = []) {
  const sourceKey = advancePaymentSourceKey(advanceMonthKey, conceptKey);

  monthKeys.forEach((coveredMonthKey) => {
    const monthlyRecord = unit.paymentRecords?.[coveredMonthKey]?.[conceptKey];
    if (!monthlyRecord || monthlyRecord.advancePaymentSourceKey !== sourceKey) return;

    delete monthlyRecord.amount;
    delete monthlyRecord.validated;
    delete monthlyRecord.validatedAt;
    delete monthlyRecord.advancePaymentSource;
    delete monthlyRecord.advancePaymentSourceKey;
    delete monthlyRecord.advancePaymentPeriod;

    setPaymentStatus(unit, coveredMonthKey, conceptKey, conceptAmountForMonth(unit, conceptKey, coveredMonthKey) > 0 ? "pending" : "paid");
  });
}

function syncAdvancePaymentsForUnits(units) {
  units.forEach((unit) => {
    const validationMonths = Object.keys(unit.advancePaymentValidation || {});
    validationMonths.forEach((advanceMonthKey) => {
      paymentMethodValidationConcepts.forEach((conceptKey) => {
        const record = advancePaymentValidationRecord(unit, advanceMonthKey, conceptKey);
        if (!record.validated) return;
        registerAdvancePaymentInMonthlyRecords(unit, advanceMonthKey, conceptKey, record);
      });
    });
  });
}

function paymentMethodAdvanceTotalsRowMarkup(units, monthKey) {
  const totals = paymentMethodAdvanceTotals(units, monthKey);

  return `
    <tr class="payment-method-total-row" data-advance-payment-totals-row>
      <td colspan="3">
        <span>Total</span>
      </td>
      <td class="payment-method-rent-section payment-method-section-start" data-advance-payment-total="rentTotal"><strong>${formatCurrency(totals.rentTotal)}</strong></td>
      <td class="payment-method-rent-section" data-advance-payment-total="rent1"><strong>${formatCurrency(totals.rent1)}</strong></td>
      <td class="payment-method-validation-column payment-method-rent-section"></td>
      <td class="payment-method-rent-section" data-advance-payment-total="rent2"><strong>${formatCurrency(totals.rent2)}</strong></td>
      <td class="payment-method-validation-column payment-method-rent-section"></td>
      <td class="payment-method-rent-section payment-method-section-end" data-advance-payment-total="rentSum"><strong>${formatCurrency(totals.rentSum)}</strong></td>
      <td class="payment-method-maintenance-section payment-method-section-start" data-advance-payment-total="maintenanceTotal"><strong>${formatCurrency(totals.maintenanceTotal)}</strong></td>
      <td class="payment-method-maintenance-section" data-advance-payment-total="maintenance1"><strong>${formatCurrency(totals.maintenance1)}</strong></td>
      <td class="payment-method-validation-column payment-method-maintenance-section"></td>
      <td class="payment-method-maintenance-section" data-advance-payment-total="maintenance2"><strong>${formatCurrency(totals.maintenance2)}</strong></td>
      <td class="payment-method-validation-column payment-method-maintenance-section"></td>
      <td class="payment-method-maintenance-section payment-method-section-end" data-advance-payment-total="maintenanceSum"><strong>${formatCurrency(totals.maintenanceSum)}</strong></td>
    </tr>
  `;
}

function paymentMethodAdvanceTotals(units, monthKey) {
  return units.reduce((totals, unit) => {
    if (isUnitAvailable(unit)) return totals;

    const rentTotal = paymentTotalAmount(unit, "rentTotal");
    const rent1 = advancePaymentAmountFor(unit, monthKey, "rent", conceptAmountForMonth(unit, "rent", monthKey));
    const rent2 = advancePaymentAmountFor(unit, monthKey, "extraordinary", conceptAmountForMonth(unit, "extraordinary", monthKey));
    const maintenanceTotal = paymentTotalAmount(unit, "maintenanceTotal");
    const maintenance1 = advancePaymentAmountFor(unit, monthKey, "maintenance", conceptAmountForMonth(unit, "maintenance", monthKey));
    const maintenance2 = advancePaymentAmountFor(unit, monthKey, "services", conceptAmountForMonth(unit, "services", monthKey));

    totals.rentTotal += rentTotal;
    totals.rent1 += rent1;
    totals.rent2 += rent2;
    totals.rentSum += rent1 + rent2;
    totals.maintenanceTotal += maintenanceTotal;
    totals.maintenance1 += maintenance1;
    totals.maintenance2 += maintenance2;
    totals.maintenanceSum += maintenance1 + maintenance2;
    return totals;
  }, {
    rentTotal: 0,
    rent1: 0,
    rent2: 0,
    rentSum: 0,
    maintenanceTotal: 0,
    maintenance1: 0,
    maintenance2: 0,
    maintenanceSum: 0
  });
}

function paymentMethodRowMarkup(unit, monthKey) {
  const rentTotal = paymentTotalAmount(unit, "rentTotal");
  const maintenanceTotal = paymentTotalAmount(unit, "maintenanceTotal");
  const rent1 = conceptAmountForMonth(unit, "rent", monthKey);
  const rent2 = conceptAmountForMonth(unit, "extraordinary", monthKey);
  const maintenance1 = conceptAmountForMonth(unit, "maintenance", monthKey);
  const maintenance2 = conceptAmountForMonth(unit, "services", monthKey);
  const isMarkedAvailable = isUnitAvailable(unit);
  const isAvailable = isMarkedAvailable || !isUnitBillableForMonth(unit, monthKey);

  return `
    <tr class="${isAvailable ? "payment-method-row-disabled" : ""}" data-payment-method-row data-unit-id="${unit.id}" data-payment-month="${monthKey}" data-rent-total="${rentTotal}" data-maintenance-total="${maintenanceTotal}" data-unit-available="${isAvailable ? "true" : "false"}">
      <td><strong>${unit.unit}</strong></td>
      <td>${tenantCellMarkup(unit)}</td>
      <td class="payment-method-readonly-total payment-method-rent-section payment-method-section-start">
        <strong>${formatCurrency(rentTotal)}</strong>
        <small>Catalogo de unidades</small>
      </td>
      ${paymentMethodAmountCellMarkup(unit, monthKey, "rent", rent1)}
      ${paymentMethodConceptValidationCellMarkup(unit, monthKey, "rent")}
      ${paymentMethodAmountCellMarkup(unit, monthKey, "extraordinary", rent2)}
      ${paymentMethodConceptValidationCellMarkup(unit, monthKey, "extraordinary")}
      ${isMarkedAvailable
        ? `<td class="payment-method-rent-section payment-method-section-end payment-method-empty-cell" data-payment-method-sum="rent"></td>`
        : `<td class="payment-method-rent-section payment-method-section-end" data-payment-method-sum="rent">${paymentMethodSumMarkup(rent1 + rent2, rentTotal)}</td>`}
      <td class="payment-method-readonly-total payment-method-maintenance-section payment-method-section-start">
        <strong>${formatCurrency(maintenanceTotal)}</strong>
        <small>Catalogo de unidades</small>
      </td>
      ${paymentMethodAmountCellMarkup(unit, monthKey, "maintenance", maintenance1)}
      ${paymentMethodConceptValidationCellMarkup(unit, monthKey, "maintenance")}
      ${paymentMethodAmountCellMarkup(unit, monthKey, "services", maintenance2)}
      ${paymentMethodConceptValidationCellMarkup(unit, monthKey, "services")}
      ${isMarkedAvailable
        ? `<td class="payment-method-maintenance-section payment-method-section-end payment-method-empty-cell" data-payment-method-sum="maintenance"></td>`
        : `<td class="payment-method-maintenance-section payment-method-section-end" data-payment-method-sum="maintenance">${paymentMethodSumMarkup(maintenance1 + maintenance2, maintenanceTotal)}</td>`}
    </tr>
  `;
}

function paymentMethodAmountCellMarkup(unit, monthKey, conceptKey, value) {
  if (isUnitAvailable(unit)) {
    return `<td class="payment-method-amount-cell payment-method-empty-cell ${paymentMethodSectionClass(conceptKey)}"></td>`;
  }

  const isValidated = isPaymentMethodConceptValidated(unit, monthKey, conceptKey);
  const isAvailable = isUnitAvailable(unit);
  const isLocked = isValidated || isAvailable;
  return `
    <td class="payment-method-amount-cell ${paymentMethodSectionClass(conceptKey)} ${isLocked ? "is-frozen" : ""}">
      ${paymentMethodAmountInputMarkup(unit, monthKey, conceptKey, value, isLocked)}
    </td>
  `;
}

function paymentMethodAmountInputMarkup(unit, monthKey, conceptKey, value, isLocked = false) {
  return `
    <input
      class="payment-method-input ${isLocked ? "is-validated" : ""}"
      type="number"
      min="0"
      step="1"
      value="${Number(value || 0)}"
      data-payment-method-amount="${conceptKey}"
      data-payment-month="${monthKey}"
      data-unit-id="${unit.id}"
      ${isLocked ? "disabled" : ""}
      aria-label="${conceptKey} ${unit.unit} ${formatMonthShort(monthKey)}"
    >
  `;
}

function paymentMethodConceptValidationHeaderMarkup(units, monthKey, conceptKey) {
  const editableUnits = units.filter((unit) => !isUnitAvailable(unit));
  const isAllValidated = editableUnits.length > 0 && editableUnits.every((unit) => isPaymentMethodConceptValidated(unit, monthKey, conceptKey));
  return `
    <th class="payment-method-validation-column ${paymentMethodSectionClass(conceptKey)}">
      <label class="validation-header-control">
        <span>Validar</span>
        <input
          class="validation-checkbox"
          type="checkbox"
          data-payment-method-validation-all
          data-payment-month="${monthKey}"
          data-payment-concept="${conceptKey}"
          ${isAllValidated ? "checked" : ""}
          aria-label="Seleccionar validaciones de ${paymentMethodConceptLabel(conceptKey)} ${formatMonthShort(monthKey)}"
        >
      </label>
    </th>
  `;
}

function isPaymentMethodConceptValidated(unit, monthKey, conceptKey) {
  return Boolean(paymentRecord(unit, monthKey, conceptKey).validated);
}

function paymentMethodConceptValidationCellMarkup(unit, monthKey, conceptKey) {
  const isValidated = isPaymentMethodConceptValidated(unit, monthKey, conceptKey);
  const isAvailable = isUnitAvailable(unit);
  if (isAvailable) {
    return `<td class="payment-method-validation-column payment-method-empty-cell ${paymentMethodSectionClass(conceptKey)}"></td>`;
  }

  return `
    <td class="payment-method-validation-column ${paymentMethodSectionClass(conceptKey)}">
      <input
        class="validation-checkbox ${isValidated && !isAvailable ? "is-validated" : ""}"
        type="checkbox"
        data-payment-method-validation-check
        data-payment-month="${monthKey}"
        data-payment-concept="${conceptKey}"
        data-unit-id="${unit.id}"
        ${isValidated && !isAvailable ? "checked" : ""}
        ${isValidated || isAvailable ? "disabled" : ""}
        aria-label="Validar ${paymentMethodConceptLabel(conceptKey)} ${unit.unit} ${formatMonthShort(monthKey)}"
      >
    </td>
  `;
}

function paymentMethodConceptLabel(conceptKey) {
  const labels = {
    rent: "Renta 1",
    extraordinary: "Renta 2",
    maintenance: "Mantenimiento 1",
    services: "Mantenimiento 2"
  };
  return labels[conceptKey] || conceptKey;
}

function paymentMethodSectionClass(conceptKey) {
  return ["rent", "extraordinary"].includes(conceptKey) ? "payment-method-rent-section" : "payment-method-maintenance-section";
}

function paymentMethodTotals(units, monthKey) {
  return units.reduce((totals, unit) => {
    if (isUnitAvailable(unit)) return totals;

    const rentTotal = paymentTotalAmount(unit, "rentTotal");
    const rent1 = conceptAmountForMonth(unit, "rent", monthKey);
    const rent2 = conceptAmountForMonth(unit, "extraordinary", monthKey);
    const maintenanceTotal = paymentTotalAmount(unit, "maintenanceTotal");
    const maintenance1 = conceptAmountForMonth(unit, "maintenance", monthKey);
    const maintenance2 = conceptAmountForMonth(unit, "services", monthKey);

    totals.rentTotal += rentTotal;
    totals.rent1 += rent1;
    totals.rent2 += rent2;
    totals.rentSum += rent1 + rent2;
    totals.maintenanceTotal += maintenanceTotal;
    totals.maintenance1 += maintenance1;
    totals.maintenance2 += maintenance2;
    totals.maintenanceSum += maintenance1 + maintenance2;
    return totals;
  }, {
    rentTotal: 0,
    rent1: 0,
    rent2: 0,
    rentSum: 0,
    maintenanceTotal: 0,
    maintenance1: 0,
    maintenance2: 0,
    maintenanceSum: 0
  });
}

function paymentMethodTotalsRowMarkup(units, monthKey) {
  const totals = paymentMethodTotals(units, monthKey);

  return `
    <tr class="payment-method-total-row" data-payment-method-totals-row>
      <td colspan="2">
        <span>Total</span>
      </td>
      <td class="payment-method-rent-section payment-method-section-start" data-payment-method-total="rentTotal"><strong>${formatCurrency(totals.rentTotal)}</strong></td>
      <td class="payment-method-rent-section" data-payment-method-total="rent1"><strong>${formatCurrency(totals.rent1)}</strong></td>
      <td class="payment-method-validation-column payment-method-rent-section"></td>
      <td class="payment-method-rent-section" data-payment-method-total="rent2"><strong>${formatCurrency(totals.rent2)}</strong></td>
      <td class="payment-method-validation-column payment-method-rent-section"></td>
      <td class="payment-method-rent-section payment-method-section-end" data-payment-method-total="rentSum"><strong>${formatCurrency(totals.rentSum)}</strong></td>
      <td class="payment-method-maintenance-section payment-method-section-start" data-payment-method-total="maintenanceTotal"><strong>${formatCurrency(totals.maintenanceTotal)}</strong></td>
      <td class="payment-method-maintenance-section" data-payment-method-total="maintenance1"><strong>${formatCurrency(totals.maintenance1)}</strong></td>
      <td class="payment-method-validation-column payment-method-maintenance-section"></td>
      <td class="payment-method-maintenance-section" data-payment-method-total="maintenance2"><strong>${formatCurrency(totals.maintenance2)}</strong></td>
      <td class="payment-method-validation-column payment-method-maintenance-section"></td>
      <td class="payment-method-maintenance-section payment-method-section-end" data-payment-method-total="maintenanceSum"><strong>${formatCurrency(totals.maintenanceSum)}</strong></td>
    </tr>
  `;
}

function paymentMethodSumMarkup(sum, total) {
  const difference = sum - total;
  const isBalanced = Math.abs(difference) < 0.01;
  const statusClass = isBalanced ? "is-balanced" : difference < 0 ? "is-under" : "is-over";
  const differenceSign = difference > 0 ? "+" : "-";
  return `
    <div class="payment-method-sum ${statusClass}">
      <strong>${formatCurrency(sum)}</strong>
      <small>${isBalanced ? "Cuadra" : `Diferencia ${differenceSign}${formatCurrency(Math.abs(difference))}`}</small>
    </div>
  `;
}

function bindPaymentMethodActions(root = els.contentArea) {
  root.querySelector("[data-modal-cancel]")?.addEventListener("click", closeModal);
  root.querySelectorAll("[data-payment-month-toggle]").forEach((button) => {
    button.addEventListener("click", () => togglePaymentMethodMonthPanel(button));
  });
  root.querySelectorAll("[data-back-property-start]").forEach((button) => {
    button.addEventListener("click", () => returnToPropertyStart(button.dataset.backPropertyStart));
  });
  root.querySelectorAll("[data-back-property-units]").forEach((button) => {
    button.addEventListener("click", () => {
      returnToPropertyUnits(button.dataset.backPropertyUnits);
    });
  });
  root.querySelectorAll("[data-open-advance-payments]").forEach((button) => {
    button.addEventListener("click", () => openPropertyAdvancePaymentsSection(button.dataset.openAdvancePayments));
  });
  root.querySelectorAll("[data-back-payment-method]").forEach((button) => {
    button.addEventListener("click", () => openPropertyPaymentMethodSection(button.dataset.backPaymentMethod));
  });
  root.querySelectorAll("[data-toggle-advance-payments]").forEach((button) => {
    button.addEventListener("click", () => toggleAdvancePaymentsPanel(button, root));
  });
  root.querySelectorAll("[data-back-monthly-payments]").forEach((button) => {
    button.addEventListener("click", () => returnToMonthlyPaymentPanel(button, root));
  });
  root.querySelectorAll("[data-payment-method-amount]").forEach((input) => {
    input.addEventListener("input", () => {
      const row = input.closest("[data-payment-method-row]");
      updatePaymentMethodRow(row);
      updatePaymentMethodTableTotals(row?.closest(".payment-method-table"));
    });
  });
  root.querySelectorAll("[data-advance-payment-amount]").forEach((input) => {
    input.addEventListener("input", () => {
      const row = input.closest("[data-advance-payment-row]");
      updateAdvancePaymentRow(row);
      updateAdvancePaymentTableTotals(row?.closest(".advance-payment-table"));
    });
  });
  bindPaymentMethodValidationActions(root);
  bindAdvancePaymentValidationActions(root);
  root.querySelector("[data-save-payment-method]")?.addEventListener("click", () => savePaymentMethodAmounts(root));
}

function togglePaymentMethodMonthPanel(button) {
  const panel = button.closest("[data-payment-month-panel]");
  const content = panel?.querySelector("[data-payment-month-content]");
  if (!panel || !content) return;

  const shouldExpand = button.getAttribute("aria-expanded") !== "true";
  const monthLabel = button.querySelector(".payment-method-month-title")?.textContent.trim() || "el mes";
  const icon = button.querySelector("[data-payment-month-toggle-icon]");

  button.setAttribute("aria-expanded", shouldExpand ? "true" : "false");
  button.setAttribute("aria-label", `${shouldExpand ? "Ocultar" : "Mostrar"} ${monthLabel}`);
  content.hidden = !shouldExpand;
  panel.classList.toggle("is-expanded", shouldExpand);
  panel.classList.toggle("is-collapsed", !shouldExpand);
  if (icon) icon.textContent = shouldExpand ? "-" : "+";
}

function toggleAdvancePaymentsPanel(button, root = els.contentArea) {
  const panelId = button.dataset.toggleAdvancePayments;
  const panel = panelId ? root.querySelector(`#${panelId}`) : null;
  if (!panel) return;

  const shouldShow = panel.hidden;
  setAdvancePaymentPanelVisibility(root, shouldShow);

  if (shouldShow) {
    panel.scrollIntoView({ behavior: "smooth", block: "start" });
  }
}

function returnToMonthlyPaymentPanel(button, root = els.contentArea) {
  setAdvancePaymentPanelVisibility(root, false);
  const monthlyPanel = root.querySelector("[data-payment-method-monthly-panel]");
  (monthlyPanel || button.closest(".payment-method-window"))?.scrollIntoView({ behavior: "smooth", block: "start" });
}

function setAdvancePaymentPanelVisibility(root, showAdvancePanel) {
  const advancePanel = root.querySelector("#advancePaymentPanel");
  const monthlyPanel = root.querySelector("[data-payment-method-monthly-panel]");
  const monthlyActions = root.querySelector("[data-payment-method-monthly-actions]");
  const trigger = root.querySelector("[data-toggle-advance-payments]");

  if (advancePanel) advancePanel.hidden = !showAdvancePanel;
  if (monthlyPanel) monthlyPanel.hidden = showAdvancePanel;
  if (monthlyActions) monthlyActions.hidden = showAdvancePanel;
  if (trigger) trigger.setAttribute("aria-expanded", showAdvancePanel ? "true" : "false");
}

function bindPaymentMethodValidationActions(root = els.contentArea) {
  root.querySelectorAll("[data-payment-method-validation-all]").forEach((checkbox) => {
    checkbox.addEventListener("change", () => togglePaymentMethodValidation(checkbox));
  });

  root.querySelectorAll("[data-payment-method-validation-check]").forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      updatePaymentMethodValidationHeader(
        checkbox.closest(".payment-method-table"),
        checkbox.dataset.paymentMonth,
        checkbox.dataset.paymentConcept
      );
    });
  });

  root.querySelectorAll("[data-confirm-payment-method-rent-validation]").forEach((button) => {
    button.addEventListener("click", () => confirmPaymentMethodRentValidation(button));
  });

  root.querySelectorAll("[data-edit-payment-method-validation]").forEach((button) => {
    button.addEventListener("click", () => editPaymentMethodValidation(button));
  });

  root.querySelectorAll(".payment-method-table").forEach((table) => {
    table.querySelectorAll("[data-payment-method-validation-all]").forEach((checkbox) => {
      updatePaymentMethodValidationHeader(table, checkbox.dataset.paymentMonth, checkbox.dataset.paymentConcept);
    });
  });
}

function bindAdvancePaymentValidationActions(root = els.contentArea) {
  root.querySelectorAll("[data-advance-payment-validation-all]").forEach((checkbox) => {
    checkbox.addEventListener("change", () => toggleAdvancePaymentValidation(checkbox));
  });

  root.querySelectorAll("[data-advance-payment-validation-check]").forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      updateAdvancePaymentValidationHeader(
        checkbox.closest(".advance-payment-table"),
        checkbox.dataset.paymentMonth,
        checkbox.dataset.paymentConcept
      );
    });
  });

  root.querySelectorAll("[data-confirm-advance-payment-validation]").forEach((button) => {
    button.addEventListener("click", () => confirmAdvancePaymentValidation(button));
  });

  root.querySelectorAll("[data-edit-advance-payment-validation]").forEach((button) => {
    button.addEventListener("click", () => editAdvancePaymentValidation(button));
  });

  root.querySelectorAll(".advance-payment-table").forEach((table) => {
    table.querySelectorAll("[data-advance-payment-validation-all]").forEach((checkbox) => {
      updateAdvancePaymentValidationHeader(table, checkbox.dataset.paymentMonth, checkbox.dataset.paymentConcept);
    });
  });
}

function toggleAdvancePaymentValidation(headerCheckbox) {
  const table = headerCheckbox.closest(".advance-payment-table");
  if (!table) return;

  table.querySelectorAll(advancePaymentValidationCheckSelector(headerCheckbox.dataset.paymentMonth, headerCheckbox.dataset.paymentConcept)).forEach((checkbox) => {
    if (checkbox.disabled) return;
    checkbox.checked = headerCheckbox.checked;
  });
  updateAdvancePaymentValidationHeader(table, headerCheckbox.dataset.paymentMonth, headerCheckbox.dataset.paymentConcept);
}

function updateAdvancePaymentValidationHeader(table, monthKey, conceptKey) {
  if (!table || !monthKey || !conceptKey) return;
  const rowChecks = [...table.querySelectorAll(advancePaymentValidationCheckSelector(monthKey, conceptKey))]
    .filter((checkbox) => checkbox.closest("[data-advance-payment-row]")?.dataset.unitAvailable !== "true");
  const headerCheck = table.querySelector(`[data-advance-payment-validation-all][data-payment-month="${monthKey}"][data-payment-concept="${conceptKey}"]`);
  if (!headerCheck || !rowChecks.length) return;

  const checkedCount = rowChecks.filter((checkbox) => checkbox.checked).length;
  headerCheck.checked = checkedCount === rowChecks.length;
  headerCheck.indeterminate = checkedCount > 0 && checkedCount < rowChecks.length;
}

function advancePaymentValidationCheckSelector(monthKey, conceptKey) {
  return `[data-advance-payment-validation-check][data-payment-month="${monthKey}"][data-payment-concept="${conceptKey}"]`;
}

function syncAdvancePaymentValidationFreeze(checkbox) {
  const row = checkbox.closest("[data-advance-payment-row]");
  const input = row?.querySelector(`[data-advance-payment-amount="${checkbox.dataset.paymentConcept}"][data-payment-month="${checkbox.dataset.paymentMonth}"]`);
  const amountCell = input?.closest(".payment-method-amount-cell");
  const shouldFreeze = checkbox.checked;

  if (input) {
    input.disabled = shouldFreeze;
    input.classList.toggle("is-validated", shouldFreeze);
  }
  amountCell?.classList.toggle("is-frozen", shouldFreeze);
  if (shouldFreeze) {
    checkbox.disabled = true;
    checkbox.classList.add("is-validated");
  }
}

function confirmAdvancePaymentValidation(button) {
  const panel = button.closest(".payment-method-advance-panel");
  const monthKey = button.dataset.confirmAdvancePaymentValidation;
  if (!panel || !monthKey) return;

  applyAdvancePaymentValidation(panel, monthKey);

  saveState();
  render();
  toast("Validacion de anticipos confirmada");
}

function editAdvancePaymentValidation(button) {
  const panel = button.closest(".payment-method-advance-panel");
  const table = panel?.querySelector(".advance-payment-table");
  const monthKey = button.dataset.editAdvancePaymentValidation;
  if (!panel || !table || !monthKey) return;

  panel.querySelectorAll(`[data-advance-payment-validation-check][data-payment-month="${monthKey}"]`).forEach((checkbox) => {
    if (checkbox.closest("[data-advance-payment-row]")?.dataset.unitAvailable === "true") return;
    checkbox.disabled = false;
    checkbox.classList.remove("is-validated");
  });
  panel.querySelectorAll(`[data-advance-payment-amount][data-payment-month="${monthKey}"]`).forEach((input) => {
    if (input.closest("[data-advance-payment-row]")?.dataset.unitAvailable === "true") return;
    input.disabled = false;
    input.classList.remove("is-validated");
    input.closest(".payment-method-amount-cell")?.classList.remove("is-frozen");
  });

  paymentMethodValidationConcepts.forEach((conceptKey) => updateAdvancePaymentValidationHeader(table, monthKey, conceptKey));
  toast("Validaciones de anticipos listas para editar");
}

function applyAdvancePaymentValidation(panel, monthKey) {
  panel.querySelectorAll(`[data-advance-payment-validation-check][data-payment-month="${monthKey}"]`).forEach((checkbox) => {
    if (checkbox.closest("[data-advance-payment-row]")?.dataset.unitAvailable === "true") return;
    const unit = state.units.find((item) => item.id === checkbox.dataset.unitId);
    const conceptKey = checkbox.dataset.paymentConcept;
    if (!unit) return;
    if (!paymentMethodValidationConcepts.includes(conceptKey)) return;

    const row = checkbox.closest("[data-advance-payment-row]");
    const input = row?.querySelector(`[data-advance-payment-amount="${conceptKey}"][data-payment-month="${monthKey}"]`);
    const record = ensureAdvancePaymentValidationRecord(unit, monthKey, conceptKey);
    const previousCoveredMonths = Array.isArray(record.coveredMonths) ? [...record.coveredMonths] : [];
    const period = advancePaymentPeriodFromRow(row);
    const removedMonths = previousCoveredMonths.filter((coveredMonth) => !period.monthKeys.includes(coveredMonth));

    if (removedMonths.length) {
      removeAdvancePaymentFromMonthlyRecords(unit, monthKey, conceptKey, removedMonths);
    }

    record.amount = Number(input?.value || 0);
    record.fromMonthIndex = period.fromIndex;
    record.toMonthIndex = period.toIndex;
    record.fromMonthKey = period.fromMonthKey;
    record.toMonthKey = period.toMonthKey;
    record.coveredMonths = period.monthKeys;
    record.validated = checkbox.checked;
    if (checkbox.checked) {
      record.validatedAt = new Date().toISOString();
      registerAdvancePaymentInMonthlyRecords(unit, monthKey, conceptKey, record);
      syncAdvancePaymentValidationFreeze(checkbox);
    } else {
      delete record.validatedAt;
      removeAdvancePaymentFromMonthlyRecords(unit, monthKey, conceptKey, previousCoveredMonths);
    }
  });
}

function togglePaymentMethodValidation(headerCheckbox) {
  const table = headerCheckbox.closest(".payment-method-table");
  if (!table) return;

  table.querySelectorAll(paymentMethodValidationCheckSelector(headerCheckbox.dataset.paymentMonth, headerCheckbox.dataset.paymentConcept)).forEach((checkbox) => {
    if (checkbox.disabled) return;
    checkbox.checked = headerCheckbox.checked;
  });
  updatePaymentMethodValidationHeader(table, headerCheckbox.dataset.paymentMonth, headerCheckbox.dataset.paymentConcept);
}

function updatePaymentMethodValidationHeader(table, monthKey, conceptKey) {
  if (!table || !monthKey || !conceptKey) return;
  const rowChecks = [...table.querySelectorAll(paymentMethodValidationCheckSelector(monthKey, conceptKey))]
    .filter((checkbox) => checkbox.closest("[data-payment-method-row]")?.dataset.unitAvailable !== "true");
  const headerCheck = table.querySelector(`[data-payment-method-validation-all][data-payment-month="${monthKey}"][data-payment-concept="${conceptKey}"]`);
  if (!headerCheck || !rowChecks.length) return;

  const checkedCount = rowChecks.filter((checkbox) => checkbox.checked).length;
  headerCheck.checked = checkedCount === rowChecks.length;
  headerCheck.indeterminate = checkedCount > 0 && checkedCount < rowChecks.length;
}

function paymentMethodValidationCheckSelector(monthKey, conceptKey) {
  return `[data-payment-method-validation-check][data-payment-month="${monthKey}"][data-payment-concept="${conceptKey}"]`;
}

function syncPaymentMethodValidationFreeze(checkbox) {
  const row = checkbox.closest("[data-payment-method-row]");
  const input = row?.querySelector(`[data-payment-method-amount="${checkbox.dataset.paymentConcept}"][data-payment-month="${checkbox.dataset.paymentMonth}"]`);
  const amountCell = input?.closest(".payment-method-amount-cell");
  const shouldFreeze = checkbox.checked;

  if (input) {
    input.disabled = shouldFreeze;
    input.classList.toggle("is-validated", shouldFreeze);
  }
  amountCell?.classList.toggle("is-frozen", shouldFreeze);
  if (shouldFreeze) {
    checkbox.disabled = true;
    checkbox.classList.add("is-validated");
  }
}

function confirmPaymentMethodRentValidation(button) {
  const panel = button.closest(".payment-method-month-panel");
  const monthKey = button.dataset.confirmPaymentMethodRentValidation;
  if (!panel || !monthKey) return;

  applyPaymentMethodValidation(panel, monthKey);

  saveState();
  render();
  toast("Validacion confirmada");
}

function editPaymentMethodValidation(button) {
  const panel = button.closest(".payment-method-month-panel");
  const table = panel?.querySelector(".payment-method-table");
  const monthKey = button.dataset.editPaymentMethodValidation;
  if (!panel || !table || !monthKey) return;

  panel.querySelectorAll(`[data-payment-method-validation-check][data-payment-month="${monthKey}"]`).forEach((checkbox) => {
    if (checkbox.closest("[data-payment-method-row]")?.dataset.unitAvailable === "true") return;
    checkbox.disabled = false;
    checkbox.classList.remove("is-validated");
  });
  panel.querySelectorAll(`[data-payment-method-amount][data-payment-month="${monthKey}"]`).forEach((input) => {
    if (input.closest("[data-payment-method-row]")?.dataset.unitAvailable === "true") return;
    input.disabled = false;
    input.classList.remove("is-validated");
    input.closest(".payment-method-amount-cell")?.classList.remove("is-frozen");
  });

  paymentMethodValidationConcepts.forEach((conceptKey) => updatePaymentMethodValidationHeader(table, monthKey, conceptKey));
  toast("Validaciones listas para editar");
}

function applyPaymentMethodValidation(panel, monthKey) {
  persistPaymentMethodPanelAmounts(panel, monthKey);
  const touchedUnits = new Set();
  panel.querySelectorAll(`[data-payment-method-validation-check][data-payment-month="${monthKey}"]`).forEach((checkbox) => {
    if (checkbox.closest("[data-payment-method-row]")?.dataset.unitAvailable === "true") return;
    const unit = state.units.find((item) => item.id === checkbox.dataset.unitId);
    const conceptKey = checkbox.dataset.paymentConcept;
    if (!unit) return;
    if (!paymentMethodValidationConcepts.includes(conceptKey)) return;
    touchedUnits.add(unit);

    const record = ensurePaymentRecord(unit, monthKey, conceptKey);
    record.validated = checkbox.checked;
    if (checkbox.checked) {
      record.validatedAt = new Date().toISOString();
      syncPaymentMethodValidationFreeze(checkbox);
    } else {
      delete record.validatedAt;
    }
    setPaymentStatus(unit, monthKey, conceptKey, checkbox.checked ? "paid" : "pending");
  });

  touchedUnits.forEach((unit) => syncManualPaymentGroupStatuses(unit, monthKey));
}

function persistPaymentMethodPanelAmounts(panel, monthKey) {
  panel.querySelectorAll(`[data-payment-method-amount][data-payment-month="${monthKey}"]`).forEach((input) => {
    if (input.closest("[data-payment-method-row]")?.dataset.unitAvailable === "true") return;
    const unit = state.units.find((item) => item.id === input.dataset.unitId);
    const conceptKey = input.dataset.paymentMethodAmount;
    if (!unit || !paymentMethodValidationConcepts.includes(conceptKey)) return;

    const record = ensurePaymentRecord(unit, monthKey, conceptKey);
    record.amount = Number(input.value || 0);
  });
}

function updatePaymentMethodRow(row) {
  if (!row) return;
  const valueFor = (conceptKey) => Number(row.querySelector(`[data-payment-method-amount="${conceptKey}"]`)?.value || 0);
  const rentTotal = Number(row.dataset.rentTotal || 0);
  const maintenanceTotal = Number(row.dataset.maintenanceTotal || 0);

  const rentSumNode = row.querySelector('[data-payment-method-sum="rent"]');
  const maintenanceSumNode = row.querySelector('[data-payment-method-sum="maintenance"]');
  if (rentSumNode) rentSumNode.innerHTML = paymentMethodSumMarkup(valueFor("rent") + valueFor("extraordinary"), rentTotal);
  if (maintenanceSumNode) maintenanceSumNode.innerHTML = paymentMethodSumMarkup(valueFor("maintenance") + valueFor("services"), maintenanceTotal);
}

function updatePaymentMethodTableTotals(table) {
  if (!table) return;

  const totals = {
    rentTotal: 0,
    rent1: 0,
    rent2: 0,
    rentSum: 0,
    maintenanceTotal: 0,
    maintenance1: 0,
    maintenance2: 0,
    maintenanceSum: 0
  };

  table.querySelectorAll("[data-payment-method-row]").forEach((row) => {
    if (row.dataset.unitAvailable === "true") return;
    const valueFor = (conceptKey) => Number(row.querySelector(`[data-payment-method-amount="${conceptKey}"]`)?.value || 0);
    const rent1 = valueFor("rent");
    const rent2 = valueFor("extraordinary");
    const maintenance1 = valueFor("maintenance");
    const maintenance2 = valueFor("services");

    totals.rentTotal += Number(row.dataset.rentTotal || 0);
    totals.rent1 += rent1;
    totals.rent2 += rent2;
    totals.rentSum += rent1 + rent2;
    totals.maintenanceTotal += Number(row.dataset.maintenanceTotal || 0);
    totals.maintenance1 += maintenance1;
    totals.maintenance2 += maintenance2;
    totals.maintenanceSum += maintenance1 + maintenance2;
  });

  Object.entries(totals).forEach(([key, value]) => {
    const cell = table.querySelector(`[data-payment-method-total="${key}"]`);
    if (cell) cell.innerHTML = `<strong>${formatCurrency(value)}</strong>`;
  });
}

function updateAdvancePaymentRow(row) {
  if (!row) return;
  const valueFor = (conceptKey) => Number(row.querySelector(`[data-advance-payment-amount="${conceptKey}"]`)?.value || 0);
  const rentTotal = Number(row.dataset.rentTotal || 0);
  const maintenanceTotal = Number(row.dataset.maintenanceTotal || 0);

  const rentSumNode = row.querySelector('[data-advance-payment-sum="rent"]');
  const maintenanceSumNode = row.querySelector('[data-advance-payment-sum="maintenance"]');
  if (rentSumNode) rentSumNode.innerHTML = paymentMethodSumMarkup(valueFor("rent") + valueFor("extraordinary"), rentTotal);
  if (maintenanceSumNode) maintenanceSumNode.innerHTML = paymentMethodSumMarkup(valueFor("maintenance") + valueFor("services"), maintenanceTotal);
}

function updateAdvancePaymentTableTotals(table) {
  if (!table) return;

  const totals = {
    rentTotal: 0,
    rent1: 0,
    rent2: 0,
    rentSum: 0,
    maintenanceTotal: 0,
    maintenance1: 0,
    maintenance2: 0,
    maintenanceSum: 0
  };

  table.querySelectorAll("[data-advance-payment-row]").forEach((row) => {
    if (row.dataset.unitAvailable === "true") return;
    const valueFor = (conceptKey) => Number(row.querySelector(`[data-advance-payment-amount="${conceptKey}"]`)?.value || 0);
    const rent1 = valueFor("rent");
    const rent2 = valueFor("extraordinary");
    const maintenance1 = valueFor("maintenance");
    const maintenance2 = valueFor("services");

    totals.rentTotal += Number(row.dataset.rentTotal || 0);
    totals.rent1 += rent1;
    totals.rent2 += rent2;
    totals.rentSum += rent1 + rent2;
    totals.maintenanceTotal += Number(row.dataset.maintenanceTotal || 0);
    totals.maintenance1 += maintenance1;
    totals.maintenance2 += maintenance2;
    totals.maintenanceSum += maintenance1 + maintenance2;
  });

  Object.entries(totals).forEach(([key, value]) => {
    const cell = table.querySelector(`[data-advance-payment-total="${key}"]`);
    if (cell) cell.innerHTML = `<strong>${formatCurrency(value)}</strong>`;
  });
}

function savePaymentMethodAmounts(root = els.contentArea) {
  const touchedUnitMonths = new Map();
  root.querySelectorAll("[data-payment-method-amount]").forEach((input) => {
    if (input.closest("[data-payment-method-row]")?.dataset.unitAvailable === "true") return;
    const unit = state.units.find((item) => item.id === input.dataset.unitId);
    if (!unit) return;

    const record = ensurePaymentRecord(unit, input.dataset.paymentMonth, input.dataset.paymentMethodAmount);
    record.amount = Number(input.value || 0);
    if (!touchedUnitMonths.has(unit.id)) {
      touchedUnitMonths.set(unit.id, { unit, monthKeys: new Set() });
    }
    touchedUnitMonths.get(unit.id).monthKeys.add(input.dataset.paymentMonth);
  });

  touchedUnitMonths.forEach(({ unit, monthKeys }) => {
    monthKeys.forEach((monthKey) => syncManualPaymentGroupStatuses(unit, monthKey));
  });

  saveState();
  toast("Metodo de pago actualizado");
}

function bindPropertyBalanceActions(propertyId) {
  els.contentArea.querySelector("[data-back-property-start]")?.addEventListener("click", () => returnToPropertyStart(propertyId));
  els.contentArea.querySelector("[data-back-property-units]")?.addEventListener("click", () => {
    returnToPropertyUnits(propertyId);
  });
}

function bindBalanceValidationActions(root) {
  root.querySelectorAll("[data-validation-all]").forEach((checkbox) => {
    checkbox.addEventListener("change", () => toggleBalanceValidationColumn(checkbox));
  });

  root.querySelectorAll("[data-validation-check]").forEach((checkbox) => {
    checkbox.addEventListener("change", () => updateValidationHeaderState(checkbox.closest(".property-balance-table"), checkbox.dataset.paymentMonth, checkbox.dataset.concept));
  });

  root.querySelectorAll("[data-confirm-validation]").forEach((button) => {
    button.addEventListener("click", () => confirmBalanceValidation(button));
  });

  root.querySelectorAll(".property-balance-table").forEach((table) => {
    propertyPaymentColumns.forEach((concept) => {
      const monthKey = table.querySelector(`[data-validation-all="${concept.key}"]`)?.dataset.paymentMonth;
      if (monthKey) updateValidationHeaderState(table, monthKey, concept.key);
    });
  });
}

function toggleBalanceValidationColumn(headerCheckbox) {
  const table = headerCheckbox.closest(".property-balance-table");
  if (!table) return;
  table.querySelectorAll(`[data-validation-check="${headerCheckbox.dataset.validationAll}"][data-payment-month="${headerCheckbox.dataset.paymentMonth}"]`).forEach((checkbox) => {
    if (checkbox.disabled) return;
    checkbox.checked = headerCheckbox.checked;
  });
  updateValidationHeaderState(table, headerCheckbox.dataset.paymentMonth, headerCheckbox.dataset.validationAll);
}

function updateValidationHeaderState(table, monthKey, conceptKey) {
  if (!table || !monthKey || !conceptKey) return;
  const rowChecks = [...table.querySelectorAll(`[data-validation-check="${conceptKey}"][data-payment-month="${monthKey}"]`)];
  const headerCheck = table.querySelector(`[data-validation-all="${conceptKey}"][data-payment-month="${monthKey}"]`);
  if (!headerCheck || !rowChecks.length) {
    updateBalanceSelectedTotals(table, monthKey);
    return;
  }

  const checkedCount = rowChecks.filter((checkbox) => checkbox.checked).length;
  headerCheck.checked = checkedCount === rowChecks.length;
  headerCheck.indeterminate = checkedCount > 0 && checkedCount < rowChecks.length;
  updateBalanceSelectedTotals(table, monthKey);
}

function updateBalanceSelectedTotals(table, monthKey) {
  if (!table || !monthKey) return;
  let grandTotal = 0;

  propertyPaymentColumns.forEach((concept) => {
    const total = [...table.querySelectorAll(`[data-validation-check="${concept.key}"][data-payment-month="${monthKey}"]`)]
      .reduce((sum, checkbox) => {
        if (!checkbox.checked) return sum;
        const unit = state.units.find((item) => item.id === checkbox.dataset.unitId);
        return unit ? sum + conceptAmountForMonth(unit, concept.key, monthKey) : sum;
      }, 0);
    grandTotal += total;

    const totalNode = table.querySelector(`[data-selected-total="${concept.key}"][data-payment-month="${monthKey}"]`);
    if (totalNode) totalNode.textContent = formatCurrency(total);
  });

  const grandTotalNode = table.querySelector(`[data-selected-grand-total="${monthKey}"]`);
  if (grandTotalNode) grandTotalNode.textContent = formatCurrency(grandTotal);
}

function confirmBalanceValidation(button) {
  const panel = button.closest(".property-balance-month-panel");
  const monthKey = button.dataset.confirmValidation;
  if (!panel || !monthKey) return;

  panel.querySelectorAll("[data-validation-check]").forEach((checkbox) => {
    const unit = state.units.find((item) => item.id === checkbox.dataset.unitId);
    const conceptKey = checkbox.dataset.concept;
    if (!unit || !conceptKey) return;
    const record = ensurePaymentRecord(unit, monthKey, conceptKey);
    record.validated = checkbox.checked;
    if (checkbox.checked) {
      record.validatedAt = new Date().toISOString();
    } else {
      delete record.validatedAt;
    }
    setPaymentStatus(unit, monthKey, conceptKey, checkbox.checked ? "paid" : "pending");
  });

  saveState();
  render();
  toast("Validacion confirmada");
}

function renderLegalWorkspace(propertyId) {
  view.propertyDetailId = propertyId;
  view.propertyFilter = propertyId;
  view.activeTab = view.legalReturnTab === "plaza_contracts" ? "plaza_contracts" : "property_legal_panel";
  render();
}

function bindPropertyLegalPanelActions(propertyId) {
  els.contentArea.querySelector("[data-back-property-units]")?.addEventListener("click", () => {
    returnToPropertyUnits(propertyId);
  });

  els.contentArea.querySelectorAll("[data-contract]").forEach((button) => {
    button.addEventListener("click", () => openContractModal(button.dataset.unitId, button.dataset.contract));
  });

  els.contentArea.querySelectorAll("[data-upload-legal-template]").forEach((input) => {
    input.addEventListener("change", () => saveLegalTemplateAttachment(input, propertyId));
  });

  els.contentArea.querySelectorAll("[data-template-file-menu]").forEach((button) => {
    button.addEventListener("click", () => openLegalTemplateFileMenu(button.dataset.templateFileMenu, propertyId));
  });

  els.contentArea.querySelectorAll("[data-generate-current-contract]").forEach((button) => {
    button.addEventListener("click", () => generateCurrentContract(button.dataset.generateCurrentContract, propertyId));
  });

  els.contentArea.querySelectorAll("[data-edit-contract-term]").forEach((button) => {
    button.addEventListener("click", () => openContractTermModal(button.dataset.editContractTerm, propertyId));
  });

  els.contentArea.querySelectorAll("[data-generate-next-contract]").forEach((button) => {
    button.addEventListener("click", () => openNextPeriodContractModal(button.dataset.generateNextContract, propertyId));
  });
}

function saveLegalTemplateAttachment(input, propertyId) {
  const unit = state.units.find((item) => item.id === input.dataset.uploadLegalTemplate && item.propertyId === propertyId);
  const file = input.files?.[0];
  if (!unit || !file) return;

  const isPdf = file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");
  if (!isPdf) {
    input.value = "";
    toast("Selecciona un archivo PDF para el machote");
    return;
  }

  unit.templateAttachmentName = file.name;
  unit.templateAttachmentType = file.type || "application/pdf";
  unit.templateAttachmentUploadedAt = new Date().toISOString();
  unit.templateContract = file.name.replace(/\.[^/.]+$/, "") || unit.templateContract;

  saveState();
  renderLegalWorkspace(propertyId);
  toast("Machote adjunto actualizado");
}

function openLegalTemplateFileMenu(unitId, propertyId) {
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!unit) return;

  const fileName = unit.templateAttachmentName || "";
  els.modalEyebrow.textContent = "Machote de contrato";
  els.modalTitle.textContent = unit.unit;
  els.modalBody.innerHTML = `
    <section class="legal-file-menu">
      <div class="detail-box">
        <span>Archivo adjunto</span>
        <strong>${fileName ? escapeAttribute(fileName) : "Sin Archivo"}</strong>
      </div>
      <div class="form-actions">
        <button class="secondary-button" type="button" data-view-template-file ${fileName ? "" : "disabled"}>
          <span data-icon="eye" aria-hidden="true"></span>
          Ver
        </button>
        <button class="danger-button" type="button" data-delete-template-file ${fileName ? "" : "disabled"}>
          <span data-icon="x" aria-hidden="true"></span>
          Eliminar
        </button>
      </div>
    </section>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-view-template-file]")?.addEventListener("click", () => openContractModal(unitId, "template"));
  els.modalBody.querySelector("[data-delete-template-file]")?.addEventListener("click", () => deleteLegalTemplateAttachment(unitId, propertyId));
  openModal();
}

function deleteLegalTemplateAttachment(unitId, propertyId) {
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!unit) return;

  unit.templateAttachmentName = "";
  unit.templateAttachmentType = "";
  unit.templateAttachmentUploadedAt = "";

  saveState();
  closeModal();
  renderLegalWorkspace(propertyId);
  toast("Machote adjunto eliminado");
}

function generateCurrentContract(unitId, propertyId) {
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!unit) return;

  if (isUnitAvailable(unit)) {
    toast("Asigna un arrendatario antes de generar contrato");
    return;
  }

  unit.signedContract = currentContractFileName(unit);
  unit.signedContractGeneratedAt = new Date().toISOString();
  saveState();
  renderLegalWorkspace(propertyId);
  toast("Contrato generado");
}

function currentContractFileName(unit) {
  const unitLabel = String(unit.unit || "Unidad").replace(/\s+/g, "-");
  const tenantLabel = String(unit.tenant || "Arrendatario").replace(/\s+/g, "-");
  const contractYear = unit.contractStart ? new Date(`${unit.contractStart}T00:00:00`).getFullYear() : new Date().getFullYear();
  return `Contrato ${tenantLabel} ${unitLabel} ${contractYear}.pdf`;
}

function openContractTermModal(unitId, propertyId) {
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!unit) return;

  if (isContractTermValidated(unit)) {
    openContractTermValidatedBlockedModal(unit);
    return;
  }

  els.modalEyebrow.textContent = "Editar vigencia de contrato";
  els.modalTitle.textContent = unit.unit;
  els.modalBody.innerHTML = `
    <form id="contractTermForm">
      <div class="form-grid">
        <div class="field">
          <label for="contractStart">Fecha de inicio del contrato</label>
          <input id="contractStart" name="contractStart" type="date" value="${escapeAttribute(unit.contractStart || defaultUnitContractStart())}" required>
        </div>
        <div class="field">
          <label for="contractEnd">Fecha de fin del contrato</label>
          <input id="contractEnd" name="contractEnd" type="date" value="${escapeAttribute(unit.contractEnd || defaultUnitContractEnd())}" required>
        </div>
      </div>
      <div class="form-actions">
        <button class="secondary-button" type="button" data-modal-cancel>
          <span data-icon="x" aria-hidden="true"></span>
          Cancelar
        </button>
        <button class="action-button" type="submit">
          <span data-icon="checkCircle" aria-hidden="true"></span>
          Guardar
        </button>
      </div>
    </form>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-modal-cancel]")?.addEventListener("click", closeModal);
  els.modalBody.querySelector("#contractTermForm")?.addEventListener("submit", (event) => saveContractTermFromForm(event, unitId, propertyId));
  openModal();
}

function openContractTermValidatedBlockedModal(unit) {
  els.modalEyebrow.textContent = "Editar vigencia de contrato";
  els.modalTitle.textContent = unit.unit;
  els.modalBody.innerHTML = `
    <section class="delete-confirmation">
      <p class="delete-question">La vigencia del contrato no se puede modificar por que aparece como validadado.</p>
    </section>
  `;

  openModal();
}

function saveContractTermFromForm(event, unitId, propertyId) {
  event.preventDefault();
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!unit) return;

  const data = new FormData(event.currentTarget);
  const contractStart = String(data.get("contractStart") || "");
  const contractEnd = String(data.get("contractEnd") || "");

  if (!contractStart || !contractEnd || contractEnd < contractStart) {
    toast("Revisa la vigencia del contrato.");
    return;
  }

  unit.contractStart = contractStart;
  unit.contractEnd = contractEnd;
  unit.contractTermManual = true;
  unit.contractTermValidated = false;
  delete unit.contractTermValidatedAt;
  saveState();
  closeModal();
  renderLegalWorkspace(propertyId);
  toast("Vigencia actualizada");
}

function openNextPeriodContractModal(unitId, propertyId) {
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  const property = getProperty(propertyId);
  if (!unit) return;

  if (isUnitAvailable(unit)) {
    toast("Asigna un arrendatario antes de generar contrato");
    return;
  }

  const period = nextContractPeriodDates(unit);
  const templateName = nextPeriodContractSourceName(unit);

  els.modalEyebrow.textContent = "Generar contrato de proximo periodo";
  els.modalTitle.textContent = `${unit.unit} - ${unit.tenant}`;
  els.modalBody.innerHTML = `
    <form id="nextPeriodContractForm">
      <div class="modal-grid next-contract-summary">
        <div class="detail-box">
          <span>Machote IA</span>
          <strong>${escapeAttribute(templateName)}</strong>
        </div>
        <div class="detail-box">
          <span>Arrendatario</span>
          <strong>${escapeAttribute(unit.tenant)}</strong>
        </div>
        <div class="detail-box">
          <span>Propiedad</span>
          <strong>${escapeAttribute(property?.name || "Sin propiedad")}</strong>
        </div>
        <div class="detail-box">
          <span>Unidad</span>
          <strong>${escapeAttribute(unit.unit)}</strong>
        </div>
        <div class="detail-box">
          <span>Nuevo periodo</span>
          <strong>${formatDate(period.start)} - ${formatDate(period.end)}</strong>
        </div>
        <div class="detail-box">
          <span>Importes base</span>
          <p>Renta ${formatCurrency(paymentTotalAmount(unit, "rentTotal"))} / Mantenimiento ${formatCurrency(paymentTotalAmount(unit, "maintenanceTotal"))}</p>
        </div>
      </div>
      <div class="form-grid">
        <div class="field">
          <label for="nextContractIncrease">Incremento anual de renta por inflacion (%)</label>
          <input id="nextContractIncrease" name="increasePercent" type="number" min="0" max="100" step="0.01" value="4.50" required>
        </div>
        <div class="field span-2">
          <label for="nextContractChange">Cambio importante para el nuevo contrato</label>
          <textarea id="nextContractChange" name="importantChange" rows="4" required placeholder="Ej. Actualizar deposito, plazo, clausula especial o condiciones de pago."></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button class="secondary-button" type="button" data-modal-cancel>
          <span data-icon="x" aria-hidden="true"></span>
          Cerrar
        </button>
        <button class="action-button" type="submit">
          <span data-icon="checkCircle" aria-hidden="true"></span>
          Confirmar
        </button>
      </div>
    </form>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-modal-cancel]")?.addEventListener("click", closeModal);
  els.modalBody.querySelector("#nextPeriodContractForm")?.addEventListener("submit", (event) => generateNextPeriodContract(event, unitId, propertyId));
  openModal();
}

function generateNextPeriodContract(event, unitId, propertyId) {
  event.preventDefault();
  const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId);
  if (!unit) return;

  const data = new FormData(event.currentTarget);
  const increasePercent = Number(data.get("increasePercent"));
  const importantChange = String(data.get("importantChange") || "").trim();

  if (!Number.isFinite(increasePercent) || increasePercent < 0) {
    toast("Define un porcentaje de incremento valido");
    return;
  }

  if (!importantChange) {
    toast("Agrega el cambio importante del nuevo contrato");
    return;
  }

  const period = nextContractPeriodDates(unit);
  const adjustedAmounts = nextContractAdjustedAmounts(unit, increasePercent);

  unit.nextPeriodContract = nextPeriodContractFileName(unit, period);
  unit.nextPeriodContractGeneratedAt = new Date().toISOString();
  unit.nextPeriodContractStart = period.start;
  unit.nextPeriodContractEnd = period.end;
  unit.nextPeriodIncreasePercent = increasePercent;
  unit.nextPeriodImportantChange = importantChange;
  unit.nextPeriodRentTotal = adjustedAmounts.rentTotal;
  unit.nextPeriodMaintenanceTotal = adjustedAmounts.maintenanceTotal;
  unit.nextPeriodOtherAmounts = adjustedAmounts.otherAmounts;
  unit.nextPeriodAiSource = nextPeriodContractSourceName(unit);
  unit.nextPeriodAiSummary = nextContractAiSummary(unit, adjustedAmounts, importantChange);
  unit.nextPeriodContractDraft = nextPeriodContractDraftText(unit, getProperty(propertyId), period, adjustedAmounts, importantChange);
  unit.nextPeriodContractPdfDataUrl = buildContractPdfDataUrl(unit.nextPeriodContract, unit.nextPeriodContractDraft);
  unit.nextPeriodContractPdfAttachedAt = new Date().toISOString();

  saveState();
  closeModal();
  renderLegalWorkspace(propertyId);
  toast("Contrato de nuevo periodo generado");
}

function nextContractPeriodDates(unit) {
  const baseDate = parseIsoDate(unit.contractEnd) || new Date();
  const startDate = new Date(baseDate);
  startDate.setDate(startDate.getDate() + 1);
  const endDate = new Date(startDate);
  endDate.setFullYear(endDate.getFullYear() + 1);
  endDate.setDate(endDate.getDate() - 1);

  return {
    start: dateToIso(startDate),
    end: dateToIso(endDate)
  };
}

function nextContractAdjustedAmounts(unit, increasePercent) {
  const factor = 1 + increasePercent / 100;
  const adjust = (value) => Math.round((Number(value) || 0) * factor);

  return {
    rentTotal: adjust(paymentTotalAmount(unit, "rentTotal")),
    maintenanceTotal: adjust(paymentTotalAmount(unit, "maintenanceTotal")),
    otherAmounts: {
      rentPart1: adjust(conceptAmount(unit, "rent")),
      rentPart2: adjust(conceptAmount(unit, "extraordinary")),
      maintenancePart1: adjust(conceptAmount(unit, "maintenance")),
      maintenancePart2: adjust(conceptAmount(unit, "services")),
      advertising: adjust(conceptAmount(unit, "advertising"))
    }
  };
}

function nextContractAiSummary(unit, adjustedAmounts, importantChange) {
  return [
    `Machote base: ${unit.nextPeriodAiSource}`,
    `Arrendatario: ${unit.tenant}`,
    `Unidad: ${unit.unit}`,
    `Renta mensual actualizada: ${formatCurrency(adjustedAmounts.rentTotal)}`,
    `Mantenimiento mensual actualizado: ${formatCurrency(adjustedAmounts.maintenanceTotal)}`,
    `Cambio importante: ${importantChange}`
  ].join(" | ");
}

function nextPeriodContractSourceName(unit) {
  const attachmentName = String(unit?.templateAttachmentName || "").trim();
  if (attachmentName) return attachmentName;

  const templateName = String(unit?.templateContract || "").trim();
  if (!templateName) return "Machote de contrato.pdf";
  return /\.pdf$/i.test(templateName) ? templateName : `${templateName}.pdf`;
}

function nextPeriodContractAmountsFromUnit(unit) {
  return {
    rentTotal: Number(unit.nextPeriodRentTotal ?? paymentTotalAmount(unit, "rentTotal")),
    maintenanceTotal: Number(unit.nextPeriodMaintenanceTotal ?? paymentTotalAmount(unit, "maintenanceTotal")),
    otherAmounts: {
      rentPart1: Number(unit.nextPeriodOtherAmounts?.rentPart1 ?? conceptAmount(unit, "rent")),
      rentPart2: Number(unit.nextPeriodOtherAmounts?.rentPart2 ?? conceptAmount(unit, "extraordinary")),
      maintenancePart1: Number(unit.nextPeriodOtherAmounts?.maintenancePart1 ?? conceptAmount(unit, "maintenance")),
      maintenancePart2: Number(unit.nextPeriodOtherAmounts?.maintenancePart2 ?? conceptAmount(unit, "services")),
      advertising: Number(unit.nextPeriodOtherAmounts?.advertising ?? conceptAmount(unit, "advertising"))
    }
  };
}

function nextPeriodContractDraftText(unit, property, period, adjustedAmounts, importantChange) {
  const sourceName = unit.nextPeriodAiSource || nextPeriodContractSourceName(unit);
  const amounts = adjustedAmounts || nextPeriodContractAmountsFromUnit(unit);
  const otherAmounts = amounts.otherAmounts || {};
  const changeText = String(importantChange || unit.nextPeriodImportantChange || "").trim() || "Sin cambios adicionales registrados.";
  const increasePercent = Number(unit.nextPeriodIncreasePercent || 0);
  const propertyName = property?.name || "Sin propiedad";
  const propertyLocation = property?.location || "Sin ubicacion";
  const tenantName = unit.tenant || "Arrendatario pendiente";
  const startDate = period?.start || unit.nextPeriodContractStart || unit.contractStart;
  const endDate = period?.end || unit.nextPeriodContractEnd || unit.contractEnd;

  return [
    "CONTRATO DE ARRENDAMIENTO - NUEVO PERIODO",
    "",
    "Origen del documento",
    `Machote PDF usado como base: ${sourceName}`,
    "Lectura IA basica: se tomaron las clausulas del machote y se sustituyeron los campos variables con la informacion de la unidad, arrendatario, vigencia e importes.",
    "",
    "Datos principales",
    `Propiedad: ${propertyName}`,
    `Ubicacion: ${propertyLocation}`,
    `Unidad: ${unit.unit}`,
    `Arrendatario: ${tenantName}`,
    `Vigencia del nuevo periodo: ${formatDate(startDate)} al ${formatDate(endDate)}`,
    "",
    "Condiciones economicas",
    `Incremento anual aplicado: ${increasePercent.toFixed(2)}%`,
    `Renta mensual total: ${formatCurrency(amounts.rentTotal)}`,
    `Renta 1 transferencia/deposito: ${formatCurrency(otherAmounts.rentPart1 || 0)}`,
    `Renta 2: ${formatCurrency(otherAmounts.rentPart2 || 0)}`,
    `Mantenimiento mensual total: ${formatCurrency(amounts.maintenanceTotal)}`,
    `Mantenimiento 1 transferencia/deposito: ${formatCurrency(otherAmounts.maintenancePart1 || 0)}`,
    `Mantenimiento 2: ${formatCurrency(otherAmounts.maintenancePart2 || 0)}`,
    `Publicidad u otros montos: ${formatCurrency(otherAmounts.advertising || 0)}`,
    "",
    "Cambios importantes para este periodo",
    changeText,
    "",
    "Texto de contrato generado",
    `Con base en el machote ${sourceName}, las partes acuerdan renovar el contrato de arrendamiento de la unidad ${unit.unit} ubicada en ${propertyName}, ${propertyLocation}, a favor de ${tenantName}, por el periodo comprendido del ${formatDate(startDate)} al ${formatDate(endDate)}.`,
    `El arrendatario pagara una renta mensual total de ${formatCurrency(amounts.rentTotal)} y un mantenimiento mensual total de ${formatCurrency(amounts.maintenanceTotal)}, conforme a los conceptos y metodos de pago registrados en Rentas 360.`,
    "Las demas clausulas del machote se mantienen vigentes salvo los cambios importantes capturados para este nuevo periodo."
  ].join("\n");
}

function ensureNextPeriodContractPdf(unit, property, draftText) {
  if (!unit?.nextPeriodContract) return "";
  if (!unit.nextPeriodContractPdfDataUrl) {
    unit.nextPeriodContractPdfDataUrl = buildContractPdfDataUrl(unit.nextPeriodContract, draftText);
    unit.nextPeriodContractPdfAttachedAt = new Date().toISOString();
    unit.nextPeriodAiSource = unit.nextPeriodAiSource || nextPeriodContractSourceName(unit);
    unit.nextPeriodContractDraft = draftText;
    saveState();
  }
  return unit.nextPeriodContractPdfDataUrl;
}

function buildContractPdfDataUrl(fileName, draftText) {
  if (typeof btoa !== "function") return "";

  const title = normalizePdfText(fileName || "Contrato nuevo periodo.pdf");
  const lines = [
    title,
    "Generado por Rentas 360",
    "",
    ...normalizePdfText(draftText || "Contrato generado desde machote.").split("\n")
  ].flatMap((line) => wrapPdfTextLine(line, 88)).slice(0, 62);

  const content = [
    "BT",
    "/F1 9 Tf",
    "40 790 Td",
    "12 TL",
    ...lines.map((line, index) => `${index ? "T* " : ""}(${escapePdfText(line)}) Tj`),
    "ET"
  ].join("\n");

  const objects = [
    "<< /Type /Catalog /Pages 2 0 R >>",
    "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
    "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>",
    `<< /Length ${content.length} >>\nstream\n${content}\nendstream`,
    "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>"
  ];

  let pdf = "%PDF-1.4\n";
  const offsets = [0];
  objects.forEach((object, index) => {
    offsets.push(pdf.length);
    pdf += `${index + 1} 0 obj\n${object}\nendobj\n`;
  });
  const xrefOffset = pdf.length;
  pdf += `xref\n0 ${objects.length + 1}\n`;
  pdf += "0000000000 65535 f \n";
  offsets.slice(1).forEach((offset) => {
    pdf += `${String(offset).padStart(10, "0")} 00000 n \n`;
  });
  pdf += `trailer\n<< /Size ${objects.length + 1} /Root 1 0 R >>\nstartxref\n${xrefOffset}\n%%EOF`;

  return `data:application/pdf;base64,${btoa(pdf)}`;
}

function normalizePdfText(value) {
  return String(value || "")
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^\x09\x0A\x0D\x20-\x7E]/g, "");
}

function wrapPdfTextLine(line, maxLength) {
  const cleanLine = String(line || "").trim();
  if (!cleanLine) return [""];

  const lines = [];
  let current = "";
  cleanLine.split(/\s+/).forEach((word) => {
    if (!current) {
      current = word;
      return;
    }
    if (`${current} ${word}`.length > maxLength) {
      lines.push(current);
      current = word;
      return;
    }
    current = `${current} ${word}`;
  });
  if (current) lines.push(current);
  return lines;
}

function escapePdfText(value) {
  return String(value || "").replace(/\\/g, "\\\\").replace(/\(/g, "\\(").replace(/\)/g, "\\)");
}

function openGeneratedContractPdf(unitId) {
  const unit = state.units.find((item) => item.id === unitId);
  if (!unit?.nextPeriodContractPdfDataUrl) {
    toast("Primero genera el contrato de nuevo periodo");
    return;
  }

  const pdfBlob = dataUrlToBlob(unit.nextPeriodContractPdfDataUrl);
  const pdfUrl = pdfBlob ? URL.createObjectURL(pdfBlob) : unit.nextPeriodContractPdfDataUrl;
  const pdfWindow = window.open(pdfUrl, "_blank", "noopener");
  if (!pdfWindow) {
    toast("El PDF esta adjunto. Usa Descargar PDF para abrirlo.");
    if (pdfBlob) URL.revokeObjectURL(pdfUrl);
    return;
  }
  if (pdfBlob) {
    setTimeout(() => URL.revokeObjectURL(pdfUrl), 60000);
  }
}

function dataUrlToBlob(dataUrl) {
  if (typeof atob !== "function") return null;
  const [metadata, base64Data] = String(dataUrl || "").split(",");
  if (!metadata || !base64Data || !metadata.includes("application/pdf")) return null;

  const binary = atob(base64Data);
  const bytes = new Uint8Array(binary.length);
  for (let index = 0; index < binary.length; index += 1) {
    bytes[index] = binary.charCodeAt(index);
  }
  return new Blob([bytes], { type: "application/pdf" });
}

function nextPeriodContractFileName(unit, period) {
  const year = parseIsoDate(period?.start)?.getFullYear() || new Date().getFullYear();
  const unitLabel = String(unit.unit || "Unidad").replace(/\s+/g, "-");
  const tenantLabel = String(unit.tenant || "Arrendatario").replace(/\s+/g, "-");
  return `Contrato nuevo periodo ${tenantLabel} ${unitLabel} ${year}.pdf`;
}

function parseIsoDate(value) {
  const [year, month, day] = String(value || "").split("-").map(Number);
  if (!year || !month || !day) return null;
  return new Date(year, month - 1, day);
}

function dateToIso(date) {
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`;
}

function openPropertyTenantDirectorySection(propertyId) {
  if (!getProperty(propertyId)) return;
  view.propertyDetailId = propertyId;
  view.propertyFilter = propertyId;
  view.activeTab = "property_tenants";
  window.location.hash = "propertyTenants";
  render();
}

function renderPropertyTenantDirectorySection() {
  const property = getProperty(view.propertyDetailId) || visibleProperties()[0];
  if (!property) {
    els.contentArea.innerHTML = emptyState("No hay propiedad seleccionada.");
    return;
  }

  view.propertyDetailId = property.id;
  view.propertyFilter = property.id;
  const tenants = propertyTenantDirectoryRows(property.id);

  els.contentArea.innerHTML = `
    <section class="property-detail-page">
      <div class="property-detail-page-header">
        <div class="property-header-main">
          <div>
            <p class="eyebrow">Arrendatarios</p>
            <h3>Catalogo de Arrendatarios</h3>
            <p class="muted">${property.name} - ${property.type}, ${property.location}</p>
          </div>
        </div>
        <div class="section-actions">
          <button class="secondary-button" type="button" data-back-property-units="${property.id}">
            <span data-icon="home" aria-hidden="true"></span>
            Regresar
          </button>
        </div>
      </div>

      <section class="property-detail-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Listado</p>
            <h3>Arrendatarios registrados</h3>
            <p class="muted">Informacion fiscal, contacto, datos bancarios, unidades asignadas y notas operativas.</p>
          </div>
        </div>
        ${tenants.length ? propertyTenantDirectoryTableMarkup(tenants, property.id) : emptyState("No hay arrendatarios registrados para esta propiedad.")}
      </section>
    </section>
  `;

  els.contentArea.querySelector("[data-back-property-units]")?.addEventListener("click", () => {
    returnToPropertyStart(property.id);
  });

  els.contentArea.querySelectorAll("[data-tenant-detail]").forEach((button) => {
    button.addEventListener("click", () => openTenantDetail(button.dataset.tenantDetail));
  });
}

function propertyTenantDirectoryRows(propertyId) {
  return tenantRows()
    .filter((tenant) => tenant.propertyIds.includes(propertyId))
    .filter((tenant) => tenant.assignedUnits.some((unit) => unit.propertyId === propertyId && !isUnitAvailable(unit)))
    .sort((a, b) => a.name.localeCompare(b.name, "es"));
}

function propertyTenantDirectoryTableMarkup(tenants, propertyId) {
  return `
    <div class="table-panel">
      <div class="table-scroll">
        <table class="tenant-directory-table">
          <thead>
            <tr>
              <th>Arrendatario</th>
              <th>Datos fiscales</th>
              <th>Contacto</th>
              <th>Datos bancarios</th>
              <th>Unidad y contrato</th>
              <th>Estatus</th>
              <th>Informacion importante</th>
            </tr>
          </thead>
          <tbody>
            ${tenants.map((tenant) => propertyTenantDirectoryRowMarkup(tenant, propertyId)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function propertyTenantDirectoryRowMarkup(tenant, propertyId) {
  const units = tenant.assignedUnits.filter((unit) => unit.propertyId === propertyId && !isUnitAvailable(unit));
  const unitLabels = units.map((unit) => unit.unit).join(", ") || "Sin unidad asignada";
  const contractLabels = units.map((unit) => `${formatDate(unit.contractStart)} - ${formatDate(unit.contractEnd)}`).join(" | ") || "Sin contrato";

  return `
    <tr>
      <td class="primary-cell">
        <strong>${tenant.name}</strong>
        <small>${tenant.type || "Sin clasificar"}</small>
        <small>${tenant.hasPortalAccess ? "Con usuario de plataforma" : "Sin usuario de plataforma"}</small>
      </td>
      <td>
        <strong>${tenant.rfc || "Sin RFC"}</strong>
        <small>${tenant.legalRepresentative || tenant.contact || "Sin representante"}</small>
        <small>${tenant.fiscalAddress || "Sin domicilio fiscal"}</small>
      </td>
      <td>
        <strong>${tenant.contact || tenant.name}</strong>
        <small>${tenant.email || "Sin correo"}</small>
        <small>${tenant.phone || "Sin telefono"}</small>
      </td>
      <td>
        <strong>${tenant.bankName || "Sin banco"}</strong>
        <small>Cuenta: ${tenant.bankAccount || "Sin cuenta"}</small>
        <small>CLABE: ${tenant.bankClabe || "Sin CLABE"}</small>
        <small>Referencia: ${tenant.paymentReference || "Sin referencia"}</small>
      </td>
      <td>
        <strong>${unitLabels}</strong>
        <small>${contractLabels}</small>
      </td>
      <td>
        <span class="status-pill ${tenant.status === "Activo" ? "status-paid" : "status-pending"}">${tenant.status}</span>
      </td>
      <td>
        <p>${tenant.notes || "Sin notas registradas."}</p>
        <button class="secondary-button tenant-directory-detail-button" type="button" data-tenant-detail="${tenant.id}">
          <span data-icon="eye" aria-hidden="true"></span>
          Detalle
        </button>
      </td>
    </tr>
  `;
}

function openDeleteUnitModal(unitId, returnPropertyId = null, returnTarget = "units") {
  const unit = state.units.find((item) => item.id === unitId);
  const property = getProperty(unit?.propertyId || returnPropertyId);
  if (!unit || !property) return;

  const { unitNumber } = unitIdentityParts(unit);
  const unitLabel = unitNumber || unit.unit;

  els.modalEyebrow.textContent = "Eliminar unidad";
  els.modalTitle.textContent = `Unidad ${unitLabel}`;
  els.modalBody.innerHTML = `
    <section class="delete-confirmation">
      <p class="delete-question">Estas seguro que quieres eliminar unidad ${unitLabel}?</p>
      <div class="modal-grid">
        <div class="detail-box">
          <span>Propiedad</span>
          <strong>${property.name}</strong>
        </div>
        <div class="detail-box">
          <span>Unidad</span>
          <strong>${unit.unit}</strong>
        </div>
        <div class="detail-box">
          <span>Arrendatario</span>
          <strong>${unit.tenant}</strong>
        </div>
        <div class="detail-box">
          <span>Nivel</span>
          <strong>${unit.unitLevel || "Sin nivel asignado"}</strong>
        </div>
        <div class="detail-box">
          <span>Renta mensual</span>
          <strong>${formatCurrency(unit.monthlyRent)}</strong>
        </div>
        <div class="detail-box">
          <span>Contrato</span>
          <strong>${formatDate(unit.contractStart)} - ${formatDate(unit.contractEnd)}</strong>
        </div>
      </div>
      <div class="form-actions delete-confirmation-actions">
        <button class="secondary-button" type="button" data-cancel-delete-unit="${unit.id}">
          <span data-icon="x" aria-hidden="true"></span>
          No
        </button>
        <button class="danger-button" type="button" data-confirm-delete-unit="${unit.id}">
          <span data-icon="x" aria-hidden="true"></span>
          Si, eliminar unidad
        </button>
      </div>
    </section>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-confirm-delete-unit]")?.addEventListener("click", () => deleteUnitAndReturn(unit.id, property.id, returnTarget));
  els.modalBody.querySelector("[data-cancel-delete-unit]")?.addEventListener("click", () => returnAfterDeleteUnit(property.id, returnTarget));
  openModal();
}

function deleteUnitAndReturn(unitId, propertyId, returnTarget = "units") {
  state.units = state.units.filter((unit) => unit.id !== unitId);
  saveState();
  closeModal();
  returnAfterDeleteUnit(propertyId, returnTarget);
  toast("Unidad eliminada");
}

function returnAfterDeleteUnit(propertyId, returnTarget = "units") {
  if (returnTarget === "properties_catalog") {
    returnToPropertiesCatalog(propertyId);
    return;
  }
  returnToPropertyUnits(propertyId);
}

function returnToPropertyUnits(propertyId) {
  closeModal();
  view.propertyDetailId = propertyId;
  view.administrationPropertyId = propertyId;
  view.propertyAdministrationView = "units";
  view.propertyFilter = propertyId;
  view.activeTab = ["administration", "superadmin_dashboard"].includes(view.propertyReturnTab)
    ? view.propertyReturnTab
    : "property_detail";
  render();
  window.location.hash = "propertyUnits";
  requestAnimationFrame(() => document.querySelector("#propertyUnits")?.scrollIntoView({ behavior: "smooth", block: "start" }));
}

function returnToPropertiesCatalog(propertyId) {
  closeModal();
  openPropertiesCatalogForProperty(propertyId);
}

function returnToPropertyStart(propertyId) {
  closeModal();
  view.propertyDetailId = propertyId;
  view.administrationPropertyId = propertyId;
  view.propertyAdministrationView = "units";
  view.propertyFilter = propertyId;
  view.activeTab = ["administration", "superadmin_dashboard"].includes(view.propertyReturnTab)
    ? view.propertyReturnTab
    : "property_detail";
  window.location.hash = "";
  render();
}

function bindPaymentRecordActions(root) {
  root.querySelectorAll("[data-payment-date]").forEach((input) => {
    input.addEventListener("change", () => savePaymentDate(input));
  });

  root.querySelectorAll("[data-upload-receipt]").forEach((input) => {
    input.addEventListener("change", () => savePaymentReceipt(input));
  });
}

function savePaymentDate(input) {
  const unit = state.units.find((item) => item.id === input.dataset.unitId);
  const conceptKey = input.dataset.paymentDate;
  const monthKey = input.dataset.paymentMonth || currentMonthKey();
  if (!unit || !conceptKey || !monthKey) return;

  const record = ensurePaymentRecord(unit, monthKey, conceptKey);
  record.paymentDate = input.value;
  saveState();
  toast("Fecha de pago actualizada");
}

function savePaymentReceipt(input) {
  const unit = state.units.find((item) => item.id === input.dataset.unitId);
  const conceptKey = input.dataset.uploadReceipt;
  const monthKey = input.dataset.paymentMonth || currentMonthKey();
  const file = input.files?.[0];
  if (!unit || !conceptKey || !monthKey || !file) return;

  const isPdf = file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");
  if (!isPdf) {
    input.value = "";
    toast("Selecciona un archivo PDF.");
    return;
  }

  const record = ensurePaymentRecord(unit, monthKey, conceptKey);
  record.receiptName = file.name;
  record.receiptUploadedAt = new Date().toISOString();
  saveState();
  const label = input.closest(".receipt-upload-button");
  if (label) {
    label.classList.add("has-file");
    label.title = file.name;
    label.querySelector(".receipt-upload-text").textContent = "PDF";
    const icon = label.querySelector("[data-icon]");
    if (icon) icon.dataset.icon = "checkCircle";
    injectIcons(label);
  }
  toast("Recibo PDF cargado");
}

function openPropertyUnitsModal(propertyId) {
  const property = getProperty(propertyId);
  if (!property) return;

  const units = propertyUnits(propertyId);
  const manager = getUser(property.managerUserId);
  const accounting = getUser(property.localAccountingUserId);
  const occupied = units.filter((unit) => unit.tenant !== "Disponible").length;
  const monthlyTotal = units.reduce((sum, unit) => sum + unitTotal(unit), 0);
  const pending = units.reduce((sum, unit) => sum + unitPendingTotal(unit, recentMonthKeys()), 0);
  const accessUsers = propertyAccessUsers(propertyId);

  els.modal?.classList.add("modal-wide");
  els.modalEyebrow.textContent = "Detalle de propiedad";
  els.modalTitle.textContent = property.name;
  els.modalBody.innerHTML = `
    <section class="property-detail-window">
      <div class="property-detail-header">
        <div>
          <p class="eyebrow">Informacion general</p>
          <h3>${property.name}</h3>
          <p class="muted">${property.type} - ${property.location}</p>
        </div>
        <div class="section-actions">
          <button class="secondary-button" type="button" data-edit-property="${property.id}">
            <span data-icon="settings" aria-hidden="true"></span>
            Editar propiedad
          </button>
          <button class="action-button" type="button" data-add-unit="${property.id}">
            <span data-icon="building" aria-hidden="true"></span>
            Nueva unidad
          </button>
        </div>
      </div>

      <div class="property-detail-grid">
        <div class="detail-box">
          <span>Tipo</span>
          <strong>${property.type}</strong>
        </div>
        <div class="detail-box">
          <span>Ubicacion</span>
          <strong>${property.location}</strong>
        </div>
        <div class="detail-box">
          <span>Gerente de propiedad</span>
          <strong>${manager?.name || "Sin asignar"}</strong>
          <p>${manager?.email || "Crea un usuario desde Usuarios."}</p>
        </div>
        <div class="detail-box">
          <span>Contabilidad local</span>
          <strong>${accounting?.name || "Sin asignar"}</strong>
          <p>${accounting?.email || "Crea un usuario desde Usuarios."}</p>
        </div>
        <div class="detail-box">
          <span>Unidades</span>
          <strong>${units.length}</strong>
          <p>${occupied} ocupadas</p>
        </div>
        <div class="detail-box">
          <span>Ingreso mensual potencial</span>
          <strong>${formatCurrency(monthlyTotal)}</strong>
          <p>${formatCurrency(pending)} por cobrar</p>
          <p>Considerando todos los espacios rentables existentes</p>
        </div>
      </div>

      <section class="property-access-panel">
        <div class="section-header">
          <div>
            <p class="eyebrow">Accesos</p>
            <h3>Usuarios vinculados a esta propiedad</h3>
          </div>
          <button class="secondary-button" type="button" data-property-team="${property.id}">
            <span data-icon="users" aria-hidden="true"></span>
            Administrar usuarios
          </button>
        </div>
        ${accessUsers.length ? `
          <div class="access-list property-access-list">
            ${accessUsers.map((user) => `<span class="access-chip">${user.name} - ${roleNames[user.role]}</span>`).join("")}
          </div>
        ` : emptyState("Aun no hay usuarios asignados a esta propiedad.")}
      </section>

      <section class="property-units-section">
        <div class="section-header">
          <div>
            <p class="eyebrow">Listado de unidades</p>
            <h3>Unidades que contiene la propiedad</h3>
            <p class="muted">Consulta el resumen de cada unidad y abre su informacion especifica.</p>
          </div>
        </div>
      </section>
    </section>
    ${units.length ? `
      <div class="table-panel property-units-panel">
        <div class="table-scroll">
          <table class="property-units-table">
            <thead>
              <tr>
                <th>Unidad</th>
                <th>Arrendatario</th>
                ${propertyPaymentHeadersMarkup({ includeRecords: false })}
                <th class="contract-date-column">Contrato</th>
                ${propertyUnitContractValidationHeaderMarkup(units)}
                <th class="contract-status-column">Estado</th>
                <th class="actions-column">Acciones</th>
              </tr>
            </thead>
            <tbody>
              ${units.map((unit) => `
                <tr>
                  <td><strong>${unit.unit}</strong></td>
                  <td>${tenantCellMarkup(unit)}</td>
                  ${propertyPaymentCellsMarkup(unit, { includeRecords: false })}
                  <td class="contract-date-column">${formatDate(unit.contractStart)} - ${formatDate(unit.contractEnd)}</td>
                  ${propertyUnitContractValidationCellMarkup(unit)}
                  <td class="contract-status-column"><span class="status-pill ${contractStatus(unit).className}">${contractStatus(unit).label}</span></td>
                  <td class="actions-column">
                    <button class="secondary-button" type="button" data-view-unit="${unit.id}">
                      <span data-icon="eye" aria-hidden="true"></span>
                      Ver unidad
                    </button>
                  </td>
                </tr>
              `).join("")}
            </tbody>
          </table>
        </div>
        ${propertyUnitContractValidationActionsMarkup()}
      </div>
    ` : emptyState("Aun no hay unidades registradas para esta propiedad.")}
  `;

  injectIcons(els.modalBody);
  bindPaymentRecordActions(els.modalBody);
  bindPropertyUnitContractValidationActions(els.modalBody);
  els.modalBody.querySelector("[data-add-unit]")?.addEventListener("click", () => {
    closeModal();
    openUnitFormModal(propertyId);
  });
  els.modalBody.querySelector("[data-edit-property]")?.addEventListener("click", () => {
    closeModal();
    openPropertyFormModal(propertyId);
  });
  els.modalBody.querySelector("[data-property-team]")?.addEventListener("click", () => {
    closeModal();
    openPropertyTeamModal(propertyId);
  });
  els.modalBody.querySelectorAll("[data-view-unit]").forEach((button) => {
    button.addEventListener("click", () => {
      closeModal();
      openPropertyUnitStatusSection(button.dataset.viewUnit, propertyId);
    });
  });
  els.modalBody.querySelectorAll("[data-legal-panel]").forEach((button) => {
    button.addEventListener("click", () => {
      closeModal();
      openPropertyLegalPanelSection(button.dataset.legalPanel || propertyId);
    });
  });
  els.modalBody.querySelectorAll("[data-contract]").forEach((button) => {
    button.addEventListener("click", () => openContractModal(button.dataset.unitId, button.dataset.contract));
  });
  openModal();
}

function openUnitDetailModal(unitId, returnPropertyId = null) {
  const unit = state.units.find((item) => item.id === unitId);
  const property = getProperty(unit?.propertyId);
  if (!unit || !property) return;

  const status = contractStatus(unit);
  const currentPending = unitPendingTotal(unit, [currentMonthKey()]);
  const recentPending = unitPendingTotal(unit, recentMonthKeys());
  const tenant = tenantRows().find((row) =>
    row.userId === unit.tenantUserId || normalizeText(row.name) === normalizeText(unit.tenant)
  );

  els.modal?.classList.add("modal-wide");
  els.modalEyebrow.textContent = "Detalle de unidad";
  els.modalTitle.textContent = `${unit.unit} - ${property.name}`;
  els.modalBody.innerHTML = `
    <section class="unit-detail-window">
      <div class="property-detail-header">
        <div>
          <p class="eyebrow">Informacion especifica</p>
          <h3>${unit.unit}</h3>
          <p class="muted">${property.name} - ${unit.tenant}</p>
        </div>
        <div class="section-actions">
          ${returnPropertyId ? `
            <button class="secondary-button" type="button" data-back-property="${returnPropertyId}">
              <span data-icon="home" aria-hidden="true"></span>
              Propiedad
            </button>
          ` : ""}
          <button class="secondary-button" type="button" data-add-unit="${property.id}">
            <span data-icon="building" aria-hidden="true"></span>
            Nueva unidad
          </button>
        </div>
      </div>

      <div class="property-detail-grid">
        <div class="detail-box">
          <span>Propiedad</span>
          <strong>${property.name}</strong>
          <p>${property.type} - ${property.location}</p>
        </div>
        <div class="detail-box">
          <span>Arrendatario</span>
          <strong>${unit.tenant}</strong>
          <p>${tenant?.email || tenantEmail(unit)}</p>
        </div>
        <div class="detail-box">
          <span>Nivel</span>
          <strong>${unit.unitLevel || "Sin nivel asignado"}</strong>
        </div>
        <div class="detail-box">
          <span>Renta mensual</span>
          <strong>${formatCurrency(unit.monthlyRent)}</strong>
        </div>
        <div class="detail-box">
          <span>Total mensual</span>
          <strong>${formatCurrency(unitTotal(unit))}</strong>
        </div>
        <div class="detail-box">
          <span>Pendiente mes actual</span>
          <strong>${formatCurrency(currentPending)}</strong>
        </div>
        <div class="detail-box">
          <span>Pendiente ventana operativa</span>
          <strong>${formatCurrency(recentPending)}</strong>
        </div>
        <div class="detail-box">
          <span>Inicio contrato</span>
          <strong>${formatDate(unit.contractStart)}</strong>
        </div>
        <div class="detail-box">
          <span>Fin contrato</span>
          <strong>${formatDate(unit.contractEnd)}</strong>
          <p><span class="status-pill ${status.className}">${status.label}</span></p>
        </div>
      </div>

      <section class="property-access-panel">
        <div class="section-header">
          <div>
            <p class="eyebrow">Importes por concepto</p>
            <h3>Cobranza de la unidad</h3>
          </div>
        </div>
        <div class="unit-concepts-grid">
          ${paymentConcepts.map((concept) => `
            <article>
              <span>${concept.label}</span>
              <strong>${formatCurrency(conceptAmountForMonth(unit, concept.key, currentMonthKey()))}</strong>
              <small>${getPaymentStatus(unit, currentMonthKey(), concept.key) === "paid" ? "Pagado" : "Por pagar"} en ${formatMonthShort(currentMonthKey())}</small>
            </article>
          `).join("")}
        </div>
      </section>

      <section class="property-access-panel">
        <div class="modal-grid">
          <div class="detail-box">
            <span>Machote</span>
            <strong>${unit.templateContract}</strong>
            <p>${unit.templateAttachmentName ? `Adjunto: ${escapeAttribute(unit.templateAttachmentName)}` : "Sin archivo adjunto"}</p>
          </div>
          <div class="detail-box">
            <span>Contrato firmado</span>
            <strong>${unit.signedContract}</strong>
          </div>
        </div>
      </section>
    </section>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-back-property]")?.addEventListener("click", () => openPropertyUnitsModal(returnPropertyId));
  els.modalBody.querySelector("[data-add-unit]")?.addEventListener("click", () => {
    closeModal();
    openUnitFormModal(property.id);
  });
  openModal();
}

function plazaCreationDefaultValues() {
  const manager = state.users.find((user) => user.role === "project_manager");
  const localAccounting = state.users.find((user) => user.role === "local_accounting");
  const organization = state.organizations.find((item) => item.status === "Activa") || state.organizations[0];

  return {
    name: "",
    internalKey: `PLA-${String(state.properties.length + 1).padStart(3, "0")}`,
    type: "Plaza comercial",
    status: "En proyecto",
    businessLine: "Comercial",
    responsibleId: manager?.id || "",
    organizationId: organization?.id || "",
    rfc: "",
    legalName: "",
    landArea: "",
    leasableArea: "",
    builtArea: "",
    levels: "1",
    totalUnits: "",
    occupiedUnits: "0",
    parkingSpaces: "",
    elevators: "",
    warehouses: "",
    commonAreas: true,
    security24: false,
    cctv: false,
    controlledAccess: false,
    physicalComments: "",
    street: "",
    exteriorNumber: "",
    interiorNumber: "",
    neighborhood: "",
    municipality: "",
    city: "",
    state: "Nuevo Leon",
    postalCode: "",
    country: "Mexico",
    latitude: "",
    longitude: "",
    locationReference: "",
    operationSchedule: "Lun - Dom 08:00 - 22:00",
    maintenanceFee: "",
    collectionFrequency: "Mensual",
    waterService: "Municipal",
    electricService: "CFE",
    emergencyPlant: false,
    internet: true,
    trashCollection: true,
    cleaning: true,
    preventiveMaintenance: true,
    correctiveMaintenance: true,
    operationNotes: "",
    targetMonthlyIncome: "",
    baseRent: "",
    depositMonths: "1",
    graceDays: "5",
    annualIncrease: "",
    marketplaceEnabled: false,
    commercializationStatus: "Preparacion",
    commercialNotes: "",
    ownershipRegime: "Propiedad privada",
    deedNumber: "",
    notary: "",
    publicRegistry: "",
    landUsePermit: "",
    insurancePolicy: "",
    legalNotes: "",
    localAccountingId: localAccounting?.id || "",
    administrationEmail: "",
    legalContactName: "",
    legalEmail: "",
    emergencyPhone: "",
    billingEmail: "",
    openingDate: "",
    priority: "Media",
    observations: "",
    internalNotes: ""
  };
}

function loadPlazaCreationDraft() {
  try {
    const savedDraft = JSON.parse(localStorage.getItem(PLAZA_DRAFT_KEY) || "null");
    if (!savedDraft || typeof savedDraft !== "object") return null;
    return {
      values: savedDraft.values && typeof savedDraft.values === "object" ? savedDraft.values : {},
      documents: Array.isArray(savedDraft.documents) ? savedDraft.documents : [],
      savedAt: savedDraft.savedAt || ""
    };
  } catch {
    return null;
  }
}

function plazaCreationOptions(options, selectedValue = "") {
  return options.map((option) => {
    const value = typeof option === "string" ? option : option.value;
    const label = typeof option === "string" ? option : option.label;
    return `<option value="${escapeAttribute(value)}" ${String(value) === String(selectedValue) ? "selected" : ""}>${escapeAttribute(label)}</option>`;
  }).join("");
}

function plazaCreationToggleMarkup(name, label, description, checked = false) {
  const isChecked = checked === true || checked === "true" || checked === "on";
  return `
    <label class="plaza-flow-toggle">
      <input type="checkbox" name="${escapeAttribute(name)}" ${isChecked ? "checked" : ""}>
      <span class="plaza-flow-toggle-track" aria-hidden="true"></span>
      <span class="plaza-flow-toggle-copy">
        <strong>${escapeAttribute(label)}</strong>
        <small>${escapeAttribute(description)}</small>
      </span>
    </label>
  `;
}

function plazaCreationFlowMarkup(values, { isEditing = false } = {}) {
  const managerOptions = [
    { value: "", label: "Seleccionar responsable" },
    ...state.users
      .filter((user) => user.role === "project_manager")
      .map((user) => ({ value: user.id, label: user.name }))
  ];
  const accountingOptions = [
    { value: "", label: "Seleccionar contabilidad local" },
    ...state.users
      .filter((user) => user.role === "local_accounting")
      .map((user) => ({ value: user.id, label: user.name }))
  ];
  const organizationOptions = [
    { value: "", label: "Seleccionar empresa" },
    ...state.organizations.map((organization) => ({ value: organization.id, label: organization.name }))
  ];

  return `
    <form id="plazaCreationForm" class="plaza-creation-flow" novalidate>
      <nav class="plaza-flow-tabs" role="tablist" aria-label="Secciones del alta de plaza">
        ${plazaCreationSections.map((section, index) => `
          <button
            class="plaza-flow-tab ${index === 0 ? "is-active" : ""}"
            type="button"
            role="tab"
            aria-selected="${index === 0 ? "true" : "false"}"
            aria-controls="plazaFlowSection-${section.id}"
            data-plaza-flow-tab="${section.id}"
          >
            <span data-icon="${section.icon}" aria-hidden="true"></span>
            ${section.label}
          </button>
        `).join("")}
      </nav>

      <div class="plaza-creation-layout">
        <div class="plaza-creation-form-scroll" data-plaza-form-scroll>
          <section id="plazaFlowSection-general" class="plaza-flow-section" data-plaza-flow-section="general" tabindex="-1">
            <div class="plaza-flow-section-heading">
              <p class="eyebrow">Datos generales</p>
              <h4>Identificacion de la plaza</h4>
            </div>
            <div class="plaza-form-grid">
              <div class="field span-2">
                <label for="plazaName">Nombre de la plaza <span aria-hidden="true">*</span></label>
                <input id="plazaName" name="name" required placeholder="Nombre comercial o interno" value="${escapeAttribute(values.name)}">
              </div>
              <div class="field">
                <label for="plazaInternalKey">Clave interna <span aria-hidden="true">*</span></label>
                <input id="plazaInternalKey" name="internalKey" required value="${escapeAttribute(values.internalKey)}">
              </div>
              <div class="field">
                <label for="plazaType">Tipo de propiedad <span aria-hidden="true">*</span></label>
                <select id="plazaType" name="type" required>${propertyTypeOptions(values.type)}</select>
              </div>
              <div class="field">
                <label for="plazaStatus">Estatus <span aria-hidden="true">*</span></label>
                <select id="plazaStatus" name="status" required>
                  ${plazaCreationOptions(["En proyecto", "En apertura", "Operando", "Pausada"], values.status)}
                </select>
              </div>
              <div class="field">
                <label for="plazaBusinessLine">Giro principal <span aria-hidden="true">*</span></label>
                <select id="plazaBusinessLine" name="businessLine" required>
                  ${plazaCreationOptions(["Comercial", "Industrial", "Residencial", "Oficinas", "Mixto"], values.businessLine)}
                </select>
              </div>
              <div class="field">
                <label for="plazaResponsible">Responsable <span aria-hidden="true">*</span></label>
                <select id="plazaResponsible" name="responsibleId" required>${plazaCreationOptions(managerOptions, values.responsibleId)}</select>
              </div>
              <div class="field">
                <label for="plazaOrganization">Empresa propietaria <span aria-hidden="true">*</span></label>
                <select id="plazaOrganization" name="organizationId" required>${plazaCreationOptions(organizationOptions, values.organizationId)}</select>
              </div>
              <div class="field">
                <label for="plazaRfc">RFC <span aria-hidden="true">*</span></label>
                <input id="plazaRfc" name="rfc" required maxlength="13" placeholder="RFC de la empresa" value="${escapeAttribute(values.rfc)}">
              </div>
              <div class="field span-3">
                <label for="plazaLegalName">Razon social <span aria-hidden="true">*</span></label>
                <input id="plazaLegalName" name="legalName" required placeholder="Razon social de la empresa propietaria" value="${escapeAttribute(values.legalName)}">
              </div>
            </div>

            <div class="plaza-flow-subsection-heading">
              <p class="eyebrow">Caracteristicas fisicas</p>
            </div>
            <div class="plaza-form-grid plaza-form-grid-five">
              <div class="field">
                <label for="plazaLandArea">Superficie de terreno (m2)</label>
                <input id="plazaLandArea" name="landArea" type="number" min="0" step="0.01" value="${escapeAttribute(values.landArea)}">
              </div>
              <div class="field">
                <label for="plazaLeasableArea">Superficie rentable (m2)</label>
                <input id="plazaLeasableArea" name="leasableArea" type="number" min="0" step="0.01" value="${escapeAttribute(values.leasableArea)}">
              </div>
              <div class="field">
                <label for="plazaBuiltArea">Superficie construida (m2)</label>
                <input id="plazaBuiltArea" name="builtArea" type="number" min="0" step="0.01" value="${escapeAttribute(values.builtArea)}">
              </div>
              <div class="field">
                <label for="plazaLevels">Niveles</label>
                <input id="plazaLevels" name="levels" type="number" min="1" step="1" value="${escapeAttribute(values.levels)}">
              </div>
              <div class="field">
                <label for="plazaTotalUnits">Total de unidades / locales</label>
                <input id="plazaTotalUnits" name="totalUnits" type="number" min="0" step="1" value="${escapeAttribute(values.totalUnits)}">
              </div>
              <div class="field">
                <label for="plazaOccupiedUnits">Locales ocupados</label>
                <input id="plazaOccupiedUnits" name="occupiedUnits" type="number" min="0" step="1" value="${escapeAttribute(values.occupiedUnits)}">
              </div>
              <div class="field">
                <label for="plazaAvailableUnits">Locales disponibles</label>
                <input id="plazaAvailableUnits" name="availableUnits" type="number" readonly value="0">
              </div>
              <div class="field">
                <label for="plazaParkingSpaces">Cajones de estacionamiento</label>
                <input id="plazaParkingSpaces" name="parkingSpaces" type="number" min="0" step="1" value="${escapeAttribute(values.parkingSpaces)}">
              </div>
              <div class="field">
                <label for="plazaElevators">Elevadores</label>
                <input id="plazaElevators" name="elevators" type="number" min="0" step="1" value="${escapeAttribute(values.elevators)}">
              </div>
              <div class="field">
                <label for="plazaWarehouses">Bodegas</label>
                <input id="plazaWarehouses" name="warehouses" type="number" min="0" step="1" value="${escapeAttribute(values.warehouses)}">
              </div>
            </div>
            <div class="plaza-flow-toggle-grid">
              ${plazaCreationToggleMarkup("commonAreas", "Area comun", "Espacios compartidos", values.commonAreas)}
              ${plazaCreationToggleMarkup("security24", "Seguridad 24/7", "Cobertura permanente", values.security24)}
              ${plazaCreationToggleMarkup("cctv", "CCTV", "Videovigilancia", values.cctv)}
              ${plazaCreationToggleMarkup("controlledAccess", "Acceso controlado", "Control de entradas", values.controlledAccess)}
            </div>
            <div class="field plaza-flow-notes-field">
              <label for="plazaPhysicalComments">Comentarios</label>
              <textarea id="plazaPhysicalComments" name="physicalComments" rows="2" placeholder="Observaciones sobre las caracteristicas fisicas">${escapeAttribute(values.physicalComments)}</textarea>
            </div>
          </section>

          <section id="plazaFlowSection-location" class="plaza-flow-section" data-plaza-flow-section="location" tabindex="-1">
            <div class="plaza-flow-section-heading">
              <p class="eyebrow">Ubicacion</p>
              <h4>Domicilio y localizacion</h4>
            </div>
            <div class="plaza-form-grid">
              <div class="field span-2">
                <label for="plazaStreet">Calle y avenida <span aria-hidden="true">*</span></label>
                <input id="plazaStreet" name="street" required placeholder="Calle principal" value="${escapeAttribute(values.street)}">
              </div>
              <div class="field">
                <label for="plazaExteriorNumber">Numero exterior</label>
                <input id="plazaExteriorNumber" name="exteriorNumber" value="${escapeAttribute(values.exteriorNumber)}">
              </div>
              <div class="field">
                <label for="plazaInteriorNumber">Numero interior</label>
                <input id="plazaInteriorNumber" name="interiorNumber" value="${escapeAttribute(values.interiorNumber)}">
              </div>
              <div class="field span-2">
                <label for="plazaNeighborhood">Colonia</label>
                <input id="plazaNeighborhood" name="neighborhood" value="${escapeAttribute(values.neighborhood)}">
              </div>
              <div class="field">
                <label for="plazaMunicipality">Municipio / alcaldia <span aria-hidden="true">*</span></label>
                <input id="plazaMunicipality" name="municipality" required value="${escapeAttribute(values.municipality)}">
              </div>
              <div class="field">
                <label for="plazaCity">Ciudad <span aria-hidden="true">*</span></label>
                <input id="plazaCity" name="city" required value="${escapeAttribute(values.city)}">
              </div>
              <div class="field">
                <label for="plazaState">Estado <span aria-hidden="true">*</span></label>
                <input id="plazaState" name="state" required value="${escapeAttribute(values.state)}">
              </div>
              <div class="field">
                <label for="plazaPostalCode">Codigo postal</label>
                <input id="plazaPostalCode" name="postalCode" inputmode="numeric" maxlength="5" value="${escapeAttribute(values.postalCode)}">
              </div>
              <div class="field">
                <label for="plazaCountry">Pais</label>
                <input id="plazaCountry" name="country" value="${escapeAttribute(values.country)}">
              </div>
              <div class="field">
                <label for="plazaLatitude">Latitud</label>
                <input id="plazaLatitude" name="latitude" inputmode="decimal" value="${escapeAttribute(values.latitude)}">
              </div>
              <div class="field">
                <label for="plazaLongitude">Longitud</label>
                <input id="plazaLongitude" name="longitude" inputmode="decimal" value="${escapeAttribute(values.longitude)}">
              </div>
              <div class="field span-3">
                <label for="plazaLocationReference">Referencias de ubicacion</label>
                <input id="plazaLocationReference" name="locationReference" placeholder="Entre calles, accesos o puntos de referencia" value="${escapeAttribute(values.locationReference)}">
              </div>
            </div>
          </section>

          <section id="plazaFlowSection-operation" class="plaza-flow-section" data-plaza-flow-section="operation" tabindex="-1">
            <div class="plaza-flow-section-heading">
              <p class="eyebrow">Operacion</p>
              <h4>Horarios y servicios</h4>
            </div>
            <div class="plaza-form-grid plaza-form-grid-five">
              <div class="field">
                <label for="plazaOperationSchedule">Horario de operacion</label>
                <input id="plazaOperationSchedule" name="operationSchedule" value="${escapeAttribute(values.operationSchedule)}">
              </div>
              <div class="field">
                <label for="plazaMaintenanceFee">Cuota de mantenimiento (m2)</label>
                <input id="plazaMaintenanceFee" name="maintenanceFee" type="number" min="0" step="0.01" value="${escapeAttribute(values.maintenanceFee)}">
              </div>
              <div class="field">
                <label for="plazaCollectionFrequency">Frecuencia de cobro</label>
                <select id="plazaCollectionFrequency" name="collectionFrequency">${plazaCreationOptions(["Mensual", "Bimestral", "Trimestral"], values.collectionFrequency)}</select>
              </div>
              <div class="field">
                <label for="plazaWaterService">Servicio de agua</label>
                <select id="plazaWaterService" name="waterService">${plazaCreationOptions(["Municipal", "Pozo", "Pipa", "Mixto"], values.waterService)}</select>
              </div>
              <div class="field">
                <label for="plazaElectricService">Energia electrica</label>
                <select id="plazaElectricService" name="electricService">${plazaCreationOptions(["CFE", "Paneles solares", "Mixto"], values.electricService)}</select>
              </div>
            </div>
            <div class="plaza-flow-toggle-grid plaza-flow-toggle-grid-services">
              ${plazaCreationToggleMarkup("emergencyPlant", "Planta de emergencia", "Respaldo electrico", values.emergencyPlant)}
              ${plazaCreationToggleMarkup("internet", "Internet", "Servicio disponible", values.internet)}
              ${plazaCreationToggleMarkup("trashCollection", "Recoleccion de basura", "Servicio activo", values.trashCollection)}
              ${plazaCreationToggleMarkup("cleaning", "Limpieza", "Servicio activo", values.cleaning)}
              ${plazaCreationToggleMarkup("preventiveMaintenance", "Mantenimiento preventivo", "Programa vigente", values.preventiveMaintenance)}
              ${plazaCreationToggleMarkup("correctiveMaintenance", "Mantenimiento correctivo", "Atencion bajo demanda", values.correctiveMaintenance)}
            </div>
            <div class="field plaza-flow-notes-field">
              <label for="plazaOperationNotes">Notas de operacion</label>
              <textarea id="plazaOperationNotes" name="operationNotes" rows="3" placeholder="Informacion adicional de servicios operativos">${escapeAttribute(values.operationNotes)}</textarea>
            </div>
          </section>

          <section id="plazaFlowSection-commercial" class="plaza-flow-section" data-plaza-flow-section="commercial" tabindex="-1">
            <div class="plaza-flow-section-heading">
              <p class="eyebrow">Comercial</p>
              <h4>Objetivos y condiciones de renta</h4>
            </div>
            <div class="plaza-form-grid">
              <div class="field">
                <label for="plazaTargetIncome">Ingreso mensual objetivo</label>
                <input id="plazaTargetIncome" name="targetMonthlyIncome" type="number" min="0" step="1" value="${escapeAttribute(values.targetMonthlyIncome)}">
              </div>
              <div class="field">
                <label for="plazaBaseRent">Renta base de referencia</label>
                <input id="plazaBaseRent" name="baseRent" type="number" min="0" step="1" value="${escapeAttribute(values.baseRent)}">
              </div>
              <div class="field">
                <label for="plazaDepositMonths">Meses de deposito</label>
                <input id="plazaDepositMonths" name="depositMonths" type="number" min="0" max="12" step="1" value="${escapeAttribute(values.depositMonths)}">
              </div>
              <div class="field">
                <label for="plazaGraceDays">Dias de gracia para pago</label>
                <input id="plazaGraceDays" name="graceDays" type="number" min="0" max="31" step="1" value="${escapeAttribute(values.graceDays)}">
              </div>
              <div class="field">
                <label for="plazaAnnualIncrease">Incremento anual (%)</label>
                <input id="plazaAnnualIncrease" name="annualIncrease" type="number" min="0" step="0.01" value="${escapeAttribute(values.annualIncrease)}">
              </div>
              <div class="field">
                <label for="plazaCommercializationStatus">Estatus de comercializacion</label>
                <select id="plazaCommercializationStatus" name="commercializationStatus">${plazaCreationOptions(["Preparacion", "Comercializando", "Ocupacion total", "Pausada"], values.commercializationStatus)}</select>
              </div>
              <div class="span-2 plaza-flow-single-toggle">
                ${plazaCreationToggleMarkup("marketplaceEnabled", "Marketplace", "Habilitar publicacion de unidades disponibles", values.marketplaceEnabled)}
              </div>
              <div class="field span-2">
                <label for="plazaCommercialNotes">Notas comerciales</label>
                <textarea id="plazaCommercialNotes" name="commercialNotes" rows="3">${escapeAttribute(values.commercialNotes)}</textarea>
              </div>
            </div>
          </section>

          <section id="plazaFlowSection-legal" class="plaza-flow-section" data-plaza-flow-section="legal" tabindex="-1">
            <div class="plaza-flow-section-heading">
              <p class="eyebrow">Legal</p>
              <h4>Documentacion y regimen</h4>
            </div>
            <div class="plaza-form-grid">
              <div class="field">
                <label for="plazaOwnershipRegime">Regimen de propiedad</label>
                <select id="plazaOwnershipRegime" name="ownershipRegime">${plazaCreationOptions(["Propiedad privada", "Condominio", "Arrendamiento maestro", "Fideicomiso"], values.ownershipRegime)}</select>
              </div>
              <div class="field">
                <label for="plazaDeedNumber">Numero de escritura</label>
                <input id="plazaDeedNumber" name="deedNumber" value="${escapeAttribute(values.deedNumber)}">
              </div>
              <div class="field">
                <label for="plazaNotary">Notaria</label>
                <input id="plazaNotary" name="notary" value="${escapeAttribute(values.notary)}">
              </div>
              <div class="field">
                <label for="plazaPublicRegistry">Registro publico</label>
                <input id="plazaPublicRegistry" name="publicRegistry" value="${escapeAttribute(values.publicRegistry)}">
              </div>
              <div class="field span-2">
                <label for="plazaLandUsePermit">Licencia de uso de suelo</label>
                <input id="plazaLandUsePermit" name="landUsePermit" value="${escapeAttribute(values.landUsePermit)}">
              </div>
              <div class="field span-2">
                <label for="plazaInsurancePolicy">Poliza de seguro</label>
                <input id="plazaInsurancePolicy" name="insurancePolicy" value="${escapeAttribute(values.insurancePolicy)}">
              </div>
              <div class="field span-4">
                <label for="plazaLegalNotes">Notas legales</label>
                <textarea id="plazaLegalNotes" name="legalNotes" rows="3">${escapeAttribute(values.legalNotes)}</textarea>
              </div>
            </div>
          </section>

          <section id="plazaFlowSection-contacts" class="plaza-flow-section" data-plaza-flow-section="contacts" tabindex="-1">
            <div class="plaza-flow-section-heading">
              <p class="eyebrow">Contactos</p>
              <h4>Responsables y canales de atencion</h4>
            </div>
            <div class="plaza-form-grid">
              <div class="field">
                <label for="plazaLocalAccounting">Contabilidad local</label>
                <select id="plazaLocalAccounting" name="localAccountingId">${plazaCreationOptions(accountingOptions, values.localAccountingId)}</select>
              </div>
              <div class="field">
                <label for="plazaAdministrationEmail">Correo de administracion</label>
                <input id="plazaAdministrationEmail" name="administrationEmail" type="email" value="${escapeAttribute(values.administrationEmail)}">
              </div>
              <div class="field">
                <label for="plazaLegalContact">Contacto legal</label>
                <input id="plazaLegalContact" name="legalContactName" value="${escapeAttribute(values.legalContactName)}">
              </div>
              <div class="field">
                <label for="plazaLegalEmail">Correo legal</label>
                <input id="plazaLegalEmail" name="legalEmail" type="email" value="${escapeAttribute(values.legalEmail)}">
              </div>
              <div class="field span-2">
                <label for="plazaEmergencyPhone">Telefono de emergencia</label>
                <input id="plazaEmergencyPhone" name="emergencyPhone" type="tel" value="${escapeAttribute(values.emergencyPhone)}">
              </div>
              <div class="field span-2">
                <label for="plazaBillingEmail">Correo de facturacion</label>
                <input id="plazaBillingEmail" name="billingEmail" type="email" value="${escapeAttribute(values.billingEmail)}">
              </div>
            </div>
          </section>

          <section id="plazaFlowSection-observations" class="plaza-flow-section" data-plaza-flow-section="observations" tabindex="-1">
            <div class="plaza-flow-section-heading">
              <p class="eyebrow">Observaciones</p>
              <h4>Seguimiento de apertura</h4>
            </div>
            <div class="plaza-form-grid">
              <div class="field">
                <label for="plazaOpeningDate">Fecha estimada de apertura</label>
                <input id="plazaOpeningDate" name="openingDate" type="date" value="${escapeAttribute(values.openingDate)}">
              </div>
              <div class="field">
                <label for="plazaPriority">Prioridad</label>
                <select id="plazaPriority" name="priority">${plazaCreationOptions(["Baja", "Media", "Alta", "Urgente"], values.priority)}</select>
              </div>
              <div class="field span-4">
                <label for="plazaObservations">Observaciones generales</label>
                <textarea id="plazaObservations" name="observations" rows="5" placeholder="Informacion relevante para el alta y puesta en operacion">${escapeAttribute(values.observations)}</textarea>
              </div>
              <div class="field span-4">
                <label for="plazaInternalNotes">Notas internas</label>
                <textarea id="plazaInternalNotes" name="internalNotes" rows="4" placeholder="Notas visibles solo para el equipo administrativo">${escapeAttribute(values.internalNotes)}</textarea>
              </div>
            </div>
          </section>
        </div>

        <aside class="plaza-creation-summary" aria-label="Resumen de la plaza">
          <div class="plaza-summary-heading">
            <p class="eyebrow">Resumen de la plaza</p>
            <strong data-plaza-summary-name>Nueva plaza</strong>
          </div>
          <div class="plaza-summary-cards">
            <article class="plaza-summary-card is-blue">
              <span class="plaza-summary-icon" data-icon="building" aria-hidden="true"></span>
              <div><small>Unidades / locales</small><strong data-plaza-summary-units>0</strong><span>Total planeado</span></div>
            </article>
            <article class="plaza-summary-card is-green">
              <span class="plaza-summary-icon" data-icon="activity" aria-hidden="true"></span>
              <div><small>Ocupacion estimada</small><strong data-plaza-summary-occupancy>0%</strong><span data-plaza-summary-occupancy-detail>0 de 0 ocupadas</span></div>
            </article>
            <article class="plaza-summary-card is-gold">
              <span class="plaza-summary-icon" data-icon="creditCard" aria-hidden="true"></span>
              <div><small>Ingreso mensual objetivo</small><strong data-plaza-summary-income>$0</strong><span>MXN</span></div>
            </article>
            <article class="plaza-summary-card is-rose" data-plaza-summary-status-card>
              <span class="plaza-summary-icon" data-icon="fileText" aria-hidden="true"></span>
              <div><small>Estatus</small><strong data-plaza-summary-status>En proyecto</strong><span data-plaza-summary-status-detail>Pendiente de apertura</span></div>
            </article>
          </div>

          <section class="plaza-documents-panel">
            <p class="eyebrow">Documentos cargados</p>
            <label class="plaza-document-dropzone" data-plaza-documents>
              <input type="file" accept=".pdf,.jpg,.jpeg,.png" multiple data-plaza-document-input>
              <span data-icon="upload" aria-hidden="true"></span>
              <strong>Arrastra y suelta archivos aqui</strong>
              <small>o haz clic para seleccionar</small>
              <span>PDF, JPG, PNG - Max. 10 MB</span>
            </label>
            <div class="plaza-document-list" data-plaza-document-list></div>
          </section>
        </aside>
      </div>

      <footer class="plaza-creation-footer">
        <button class="secondary-button" type="button" data-modal-cancel>
          <span data-icon="x" aria-hidden="true"></span>
          Cancelar
        </button>
        ${isEditing ? "" : `
          <button class="secondary-button" type="button" data-plaza-save-draft>
            <span data-icon="fileText" aria-hidden="true"></span>
            Guardar borrador
          </button>
        `}
        <button class="action-button" type="submit">
          <span data-icon="checkCircle" aria-hidden="true"></span>
          ${isEditing ? "Guardar cambios" : "Guardar plaza"}
        </button>
      </footer>
    </form>
  `;
}

function plazaCreationValuesForProperty(property) {
  const defaults = plazaCreationDefaultValues();
  const profile = property.profile && typeof property.profile === "object" ? property.profile : {};
  const units = propertyUnits(property.id);
  const locationParts = String(property.location || "").split(",").map((part) => part.trim()).filter(Boolean);
  const organization = state.organizations.find((item) =>
    item.id === property.organizationId || (item.propertyIds || []).includes(property.id)
  );
  const totalUnits = property.plannedUnits ?? units.length;
  const occupiedUnits = property.plannedOccupiedUnits
    ?? units.filter((unit) => !isUnitAvailable(unit)).length;
  const monthlyIncome = property.targetMonthlyIncome
    ?? units.reduce((sum, unit) => sum + Number(unit.monthlyRent || 0), 0);

  return {
    ...defaults,
    ...profile,
    name: property.name || profile.name || "",
    internalKey: property.internalKey || profile.internalKey || `PLA-${String(state.properties.indexOf(property) + 1).padStart(3, "0")}`,
    type: property.type || profile.type || defaults.type,
    status: property.status || profile.status || "Operando",
    businessLine: property.businessLine || profile.businessLine || defaults.businessLine,
    responsibleId: property.managerUserId || profile.responsibleId || defaults.responsibleId,
    organizationId: property.organizationId || profile.organizationId || organization?.id || defaults.organizationId,
    rfc: property.rfc || profile.rfc || "",
    legalName: property.legalName || profile.legalName || organization?.name || "",
    totalUnits: String(Math.max(0, Number(totalUnits || 0))),
    occupiedUnits: String(Math.max(0, Number(occupiedUnits || 0))),
    city: profile.city || locationParts[0] || "",
    municipality: profile.municipality || locationParts[0] || "",
    state: profile.state || locationParts.slice(1).join(", ") || defaults.state,
    targetMonthlyIncome: String(Math.max(0, Number(monthlyIncome || 0))),
    marketplaceEnabled: typeof property.marketplaceEnabled === "boolean"
      ? property.marketplaceEnabled
      : Boolean(profile.marketplaceEnabled),
    localAccountingId: property.localAccountingUserId || profile.localAccountingId || defaults.localAccountingId
  };
}

function revealEmbeddedPlazaPanelTop() {
  try {
    if (window.parent === window || !window.frameElement) return;
    const frameTop = window.frameElement.getBoundingClientRect().top + window.parent.scrollY;
    window.parent.scrollTo({ top: Math.max(0, frameTop - 72), behavior: "auto" });
  } catch {
    // The form still opens if the embedding page cannot be accessed.
  }
}

function openPlazaCreationFlow(propertyId = null) {
  const property = propertyId ? getProperty(propertyId) : null;
  const isEditing = Boolean(property);
  const draft = isEditing ? null : loadPlazaCreationDraft();
  const values = isEditing
    ? plazaCreationValuesForProperty(property)
    : {
        ...plazaCreationDefaultValues(),
        ...(draft?.values || {})
      };
  const documents = (isEditing ? property.documents || [] : draft?.documents || []).map((document) => ({
    name: String(document.name || "Documento"),
    size: Number(document.size || 0),
    type: String(document.type || "")
  }));

  els.modal?.classList.remove("modal-wide", "modal-compact");
  els.modal?.classList.add("modal-plaza-flow");
  els.modalEyebrow.textContent = "Plantilla de captura de datos";
  els.modalTitle.textContent = isEditing ? `Editar plaza: ${property.name}` : "Alta de plaza";
  els.modalBody.innerHTML = plazaCreationFlowMarkup(values, { isEditing });

  injectIcons(els.modalBody);
  bindPlazaCreationFlow(documents, property?.id || null);
  revealEmbeddedPlazaPanelTop();
  openModal();
}

function plazaCreationValuesFromForm(form) {
  const values = {};
  new FormData(form).forEach((value, key) => {
    if (typeof value === "string") values[key] = value.trim();
  });
  plazaCreationBooleanFields.forEach((fieldName) => {
    values[fieldName] = Boolean(form.elements.namedItem(fieldName)?.checked);
  });
  return values;
}

function bindPlazaCreationFlow(documents, propertyId = null) {
  const form = els.modalBody.querySelector("#plazaCreationForm");
  const scroller = form?.querySelector("[data-plaza-form-scroll]");
  const dropzone = form?.querySelector("[data-plaza-documents]");
  const fileInput = form?.querySelector("[data-plaza-document-input]");
  const documentList = form?.querySelector("[data-plaza-document-list]");
  if (!form || !scroller || !documentList) return;

  const activateSection = (sectionId, shouldScroll = false) => {
    const section = form.querySelector(`[data-plaza-flow-section="${sectionId}"]`);
    if (!section) return;

    form.querySelectorAll("[data-plaza-flow-tab]").forEach((button) => {
      const isActive = button.dataset.plazaFlowTab === sectionId;
      button.classList.toggle("is-active", isActive);
      button.setAttribute("aria-selected", String(isActive));
    });

    if (shouldScroll) {
      scroller.scrollTo({ top: Math.max(0, section.offsetTop - 12), behavior: "smooth" });
      window.setTimeout(() => section.focus({ preventScroll: true }), 260);
    }
  };

  form.querySelectorAll("[data-plaza-flow-tab]").forEach((button) => {
    button.addEventListener("click", () => activateSection(button.dataset.plazaFlowTab, true));
  });

  let scrollFrame = null;
  scroller.addEventListener("scroll", () => {
    if (scrollFrame) window.cancelAnimationFrame(scrollFrame);
    scrollFrame = window.requestAnimationFrame(() => {
      const targetPosition = scroller.scrollTop + 36;
      const activeSection = [...form.querySelectorAll("[data-plaza-flow-section]")]
        .filter((section) => section.offsetTop <= targetPosition)
        .at(-1);
      if (activeSection) activateSection(activeSection.dataset.plazaFlowSection);
    });
  });

  const updateSummary = () => updatePlazaCreationSummary(form);
  form.addEventListener("input", updateSummary);
  form.addEventListener("change", updateSummary);

  const addDocuments = (files) => {
    let rejectedFiles = 0;
    [...files].forEach((file) => {
      const supportedType = /\.(pdf|jpe?g|png)$/i.test(file.name);
      if (!supportedType || file.size > 10 * 1024 * 1024) {
        rejectedFiles += 1;
        return;
      }
      if (documents.some((item) => item.name === file.name && item.size === file.size)) return;
      documents.push({ name: file.name, size: file.size, type: file.type });
    });
    renderPlazaCreationDocuments(documentList, documents);
    if (fileInput) fileInput.value = "";
    if (rejectedFiles) toast("Solo se admiten archivos PDF, JPG o PNG de hasta 10 MB.");
  };

  fileInput?.addEventListener("change", () => addDocuments(fileInput.files || []));
  ["dragenter", "dragover"].forEach((eventName) => {
    dropzone?.addEventListener(eventName, (event) => {
      event.preventDefault();
      dropzone.classList.add("is-dragging");
    });
  });
  ["dragleave", "drop"].forEach((eventName) => {
    dropzone?.addEventListener(eventName, (event) => {
      event.preventDefault();
      dropzone.classList.remove("is-dragging");
    });
  });
  dropzone?.addEventListener("drop", (event) => addDocuments(event.dataTransfer?.files || []));

  documentList.addEventListener("click", (event) => {
    const removeButton = event.target.closest("[data-plaza-document-remove]");
    if (!removeButton) return;
    documents.splice(Number(removeButton.dataset.plazaDocumentRemove), 1);
    renderPlazaCreationDocuments(documentList, documents);
  });

  form.querySelector("[data-modal-cancel]")?.addEventListener("click", closeModal);
  form.querySelector("[data-plaza-save-draft]")?.addEventListener("click", () => {
    const draft = {
      values: plazaCreationValuesFromForm(form),
      documents: documents.map((document) => ({ ...document })),
      savedAt: new Date().toISOString()
    };
    try {
      localStorage.setItem(PLAZA_DRAFT_KEY, JSON.stringify(draft));
      toast("Borrador de plaza guardado");
    } catch {
      toast("No fue posible guardar el borrador.");
    }
  });
  form.addEventListener("submit", (event) => {
    event.preventDefault();
    savePlazaCreationForm(form, documents, activateSection, propertyId);
  });

  renderPlazaCreationDocuments(documentList, documents);
  updateSummary();
}

function updatePlazaCreationSummary(form) {
  const values = plazaCreationValuesFromForm(form);
  const totalUnits = Math.max(0, Number(values.totalUnits || 0));
  const occupiedUnits = Math.min(totalUnits, Math.max(0, Number(values.occupiedUnits || 0)));
  const availableUnits = Math.max(0, totalUnits - occupiedUnits);
  const occupancy = totalUnits ? (occupiedUnits / totalUnits) * 100 : 0;
  const statusDetails = {
    "En proyecto": "Pendiente de apertura",
    "En apertura": "Preparando operacion",
    Operando: "Operacion activa",
    Pausada: "Operacion suspendida"
  };

  const availableInput = form.elements.namedItem("availableUnits");
  if (availableInput) availableInput.value = String(availableUnits);

  const setText = (selector, value) => {
    const element = form.querySelector(selector);
    if (element) element.textContent = value;
  };

  setText("[data-plaza-summary-name]", values.name || "Nueva plaza");
  setText("[data-plaza-summary-units]", String(totalUnits));
  setText("[data-plaza-summary-occupancy]", `${occupancy.toFixed(1)}%`);
  setText("[data-plaza-summary-occupancy-detail]", `${occupiedUnits} de ${totalUnits} ocupadas`);
  setText("[data-plaza-summary-income]", formatCurrency(Number(values.targetMonthlyIncome || 0)));
  setText("[data-plaza-summary-status]", values.status || "En proyecto");
  setText("[data-plaza-summary-status-detail]", statusDetails[values.status] || "Estatus por definir");

  const statusCard = form.querySelector("[data-plaza-summary-status-card]");
  statusCard?.classList.toggle("is-operating", values.status === "Operando");
  statusCard?.classList.toggle("is-paused", values.status === "Pausada");
}

function renderPlazaCreationDocuments(container, documents) {
  if (!documents.length) {
    container.innerHTML = '<p class="plaza-document-empty">Aun no hay documentos cargados.</p>';
    return;
  }

  container.innerHTML = documents.map((document, index) => `
    <article class="plaza-document-item">
      <span class="plaza-document-file-icon" data-icon="fileText" aria-hidden="true"></span>
      <span class="plaza-document-item-copy">
        <strong title="${escapeAttribute(document.name)}">${escapeAttribute(document.name)}</strong>
        <small>${formatPlazaDocumentSize(document.size)}</small>
      </span>
      <button class="icon-button" type="button" data-plaza-document-remove="${index}" title="Eliminar documento" aria-label="Eliminar ${escapeAttribute(document.name)}">
        <span data-icon="x" aria-hidden="true"></span>
      </button>
    </article>
  `).join("");
  injectIcons(container);
}

function formatPlazaDocumentSize(size) {
  const bytes = Math.max(0, Number(size || 0));
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function savePlazaCreationForm(form, documents, activateSection, propertyId = null) {
  if (!form.reportValidity()) {
    const invalidField = form.querySelector(":invalid");
    const invalidSection = invalidField?.closest("[data-plaza-flow-section]");
    if (invalidSection) activateSection(invalidSection.dataset.plazaFlowSection, true);
    invalidField?.focus({ preventScroll: true });
    toast("Completa los campos obligatorios.");
    return;
  }

  const values = plazaCreationValuesFromForm(form);
  const totalUnits = Math.max(0, Number(values.totalUnits || 0));
  const occupiedUnits = Math.max(0, Number(values.occupiedUnits || 0));
  if (occupiedUnits > totalUnits) {
    activateSection("general", true);
    form.elements.namedItem("occupiedUnits")?.focus({ preventScroll: true });
    toast("Los locales ocupados no pueden superar el total de unidades.");
    return;
  }

  const existingProperty = propertyId ? getProperty(propertyId) : null;
  const savedPropertyId = existingProperty?.id
    || uniqueId("prop", values.name, state.properties.map((property) => property.id));
  const location = [values.city, values.state].filter(Boolean).join(", ");
  const propertyData = {
    id: savedPropertyId,
    name: values.name,
    type: values.type,
    location,
    managerUserId: values.responsibleId,
    localAccountingUserId: values.localAccountingId,
    marketplaceEnabled: values.marketplaceEnabled,
    internalKey: values.internalKey,
    status: values.status,
    businessLine: values.businessLine,
    organizationId: values.organizationId,
    rfc: values.rfc,
    legalName: values.legalName,
    plannedUnits: totalUnits,
    plannedOccupiedUnits: occupiedUnits,
    targetMonthlyIncome: Number(values.targetMonthlyIncome || 0),
    profile: { ...values },
    documents: documents.map((document) => ({ ...document })),
    createdAt: existingProperty?.createdAt || new Date().toISOString(),
    updatedAt: new Date().toISOString()
  };

  if (existingProperty) {
    Object.assign(existingProperty, propertyData);
  } else {
    state.properties.push(propertyData);
  }

  [
    { role: "project_manager", userId: values.responsibleId },
    { role: "local_accounting", userId: values.localAccountingId }
  ].forEach(({ role, userId }) => {
    state.users.filter((user) => user.role === role).forEach((user) => {
      user.propertyIds = Array.isArray(user.propertyIds) ? user.propertyIds : [];
      if (user.id === userId) {
        if (!user.propertyIds.includes(savedPropertyId)) user.propertyIds.push(savedPropertyId);
      } else {
        user.propertyIds = user.propertyIds.filter((id) => id !== savedPropertyId);
      }
    });
  });

  state.organizations.forEach((organization) => {
    organization.propertyIds = Array.isArray(organization.propertyIds) ? organization.propertyIds : [];
    if (organization.id === values.organizationId) {
      if (!organization.propertyIds.includes(savedPropertyId)) organization.propertyIds.push(savedPropertyId);
    } else {
      organization.propertyIds = organization.propertyIds.filter((id) => id !== savedPropertyId);
    }
  });

  if (!existingProperty) {
    try {
      localStorage.removeItem(PLAZA_DRAFT_KEY);
    } catch {
      // The plaza can still be created when local draft storage is unavailable.
    }
  }

  const dashboardSelection = view.propertyFilter;
  saveState();
  rememberSelectedProperty(savedPropertyId);
  closeModal();
  view.propertyFilter = existingProperty && dashboardSelection === "all" ? "all" : savedPropertyId;
  view.administrationPropertyId = savedPropertyId;
  view.activeTab = "superadmin_dashboard";
  render();
  toast(existingProperty ? "Informacion de la plaza actualizada" : "Plaza creada correctamente");
}

function openPropertyFormModal(propertyId = null) {
  const property = propertyId ? getProperty(propertyId) : null;
  if (!property) {
    openPlazaCreationFlow();
    return;
  }
  const isEditing = Boolean(property);

  els.modalEyebrow.textContent = isEditing ? "Editar propiedad" : "Alta de propiedad";
  els.modalTitle.textContent = isEditing ? property.name : "Nueva propiedad";
  els.modalBody.innerHTML = `
    <form id="propertyForm">
      <div class="form-grid">
        <div class="field span-2">
          <label for="propertyName">Nombre de la propiedad</label>
          <input id="propertyName" name="name" required placeholder="Nombre comercial o interno" value="${escapeAttribute(property?.name || "")}">
        </div>
        <div class="field">
          <label for="propertyType">Tipo</label>
          <select id="propertyType" name="type">
            ${propertyTypeOptions(property?.type)}
          </select>
        </div>
        <div class="field">
          <label for="propertyLocation">Ubicacion</label>
          <input id="propertyLocation" name="location" required placeholder="Ciudad, estado" value="${escapeAttribute(property?.location || "")}">
        </div>
      </div>
      <div class="form-actions">
        <button class="secondary-button" type="button" data-modal-cancel>
          <span data-icon="x" aria-hidden="true"></span>
          Cancelar
        </button>
        <button class="action-button" type="submit">
          <span data-icon="${isEditing ? "settings" : "home"}" aria-hidden="true"></span>
          ${isEditing ? "Actualizar propiedad" : "Guardar propiedad"}
        </button>
      </div>
    </form>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-modal-cancel]")?.addEventListener("click", closeModal);
  els.modalBody.querySelector("#propertyForm")?.addEventListener("submit", (event) => savePropertyFromForm(event, propertyId));
  openModal();
}

function propertyTypeOptions(selectedType = "Plaza comercial") {
  return ["Plaza comercial", "Bodega", "Vivienda", "Oficinas", "Mixta"]
    .map((type) => `<option value="${type}" ${type === selectedType ? "selected" : ""}>${type}</option>`)
    .join("");
}

function unitLevelOptions(selectedLevel = "") {
  return ["", "Planta baja", "Nivel 1", "Nivel 2", "Nivel 3", "Nivel 4", "Planta alta", "Azotea"]
    .map((level) => `<option value="${escapeAttribute(level)}" ${level === selectedLevel ? "selected" : ""}>${level || "Seleccionar nivel"}</option>`)
    .join("");
}

function defaultUnitContractStart() {
  return `${monthKeyFromDate(new Date())}-01`;
}

function defaultUnitContractEnd() {
  const today = new Date();
  return `${today.getFullYear() + 1}-${String(today.getMonth() + 1).padStart(2, "0")}-01`;
}

function escapeAttribute(value) {
  return String(value || "")
    .replace(/&/g, "&amp;")
    .replace(/"/g, "&quot;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;");
}

function savePropertyFromForm(event, propertyId = null) {
  event.preventDefault();
  const shouldReturnToDetail = view.activeTab === "property_detail" && Boolean(propertyId);
  const shouldReturnToDashboard = view.activeTab === "superadmin_dashboard";
  const shouldReturnToAdministration = view.activeTab === "administration";
  const form = event.currentTarget;
  const data = new FormData(form);
  const name = String(data.get("name") || "").trim();
  const type = String(data.get("type") || "Plaza comercial");
  const location = String(data.get("location") || "").trim();

  if (!name || !location) {
    toast("Captura nombre y ubicacion.");
    return;
  }

  const existingProperty = propertyId ? getProperty(propertyId) : null;

  let savedPropertyId = propertyId;

  if (existingProperty) {
    existingProperty.name = name;
    existingProperty.type = type;
    existingProperty.location = location;
  } else {
    savedPropertyId = uniqueId("prop", name, state.properties.map((item) => item.id));
    state.properties.push({
      id: savedPropertyId,
      name,
      type,
      location,
      managerUserId: "",
      localAccountingUserId: "",
      marketplaceEnabled: false
    });
  }

  saveState();
  closeModal();
  view.propertyFilter = shouldReturnToDetail || shouldReturnToDashboard || shouldReturnToAdministration ? savedPropertyId : "all";
  view.activeTab = shouldReturnToDetail
    ? "property_detail"
    : shouldReturnToDashboard
      ? "superadmin_dashboard"
      : shouldReturnToAdministration
        ? "administration"
        : "properties";
  if (shouldReturnToDetail) view.propertyDetailId = propertyId;
  if (shouldReturnToDashboard || shouldReturnToAdministration) view.administrationPropertyId = savedPropertyId;
  render();
  toast(existingProperty ? "Propiedad actualizada" : "Propiedad creada");
}

function openUnitFormModal(propertyId, unitId = null) {
  const property = getProperty(propertyId);
  if (!property) return;

  const existingUnit = unitId ? state.units.find((item) => item.id === unitId && item.propertyId === propertyId) : null;
  if (unitId && !existingUnit) return;
  const isEditing = Boolean(existingUnit);
  const unitIdentity = unitIdentityParts(existingUnit);
  els.modalEyebrow.textContent = isEditing ? "Editar informacion de unidad" : "Alta de unidad";
  els.modalTitle.textContent = property.name;
  els.modalBody.innerHTML = `
    <form id="unitForm">
      <div class="form-grid">
        <div class="field">
          <label for="unitName">Nombre de unidad</label>
          <input id="unitName" name="unitName" required placeholder="Local, Bodega, Departamento" value="${escapeAttribute(unitIdentity.unitName)}">
        </div>
        <div class="field">
          <label for="unitNumber">Numero de unidad</label>
          <input id="unitNumber" name="unitNumber" required placeholder="01, A-1, 101" value="${escapeAttribute(unitIdentity.unitNumber)}">
        </div>
        <div class="field">
          <label for="unitTenant">Arrendatario</label>
          <input id="unitTenant" name="tenant" placeholder="Disponible o nombre del arrendatario" value="${escapeAttribute(existingUnit?.tenant || "Disponible")}">
        </div>
        <div class="field">
          <label for="unitLevel">Nivel</label>
          <select id="unitLevel" name="unitLevel">
            ${unitLevelOptions(existingUnit?.unitLevel || "")}
          </select>
        </div>
        <div class="field">
          <label for="unitSquareMeters">Metros cuadrados</label>
          <input id="unitSquareMeters" name="squareMeters" type="number" min="0" step="0.01" placeholder="0" value="${isEditing ? Number(existingUnit.squareMeters || 0) : ""}">
        </div>
        <div class="field">
          <label for="unitMeasurements">Medidas</label>
          <input id="unitMeasurements" name="measurements" placeholder="Ej. 5 m x 8 m" value="${escapeAttribute(existingUnit?.measurements || "")}">
        </div>
        <div class="field">
          <label for="unitRent">Renta Total</label>
          <input id="unitRent" name="monthlyRent" type="number" min="0" step="1" required placeholder="0" value="${isEditing ? Number(existingUnit.monthlyRent || 0) : ""}">
        </div>
        <div class="field">
          <label for="unitMaintenance">Mantenimiento Total</label>
          <input id="unitMaintenance" name="maintenance" type="number" min="0" step="1" value="${isEditing ? Number(existingUnit.maintenance || 0) : 0}">
        </div>
        <div class="field">
          <label for="unitAdvertising">Publicidad</label>
          <input id="unitAdvertising" name="advertising" type="number" min="0" step="1" value="${isEditing ? Number(existingUnit.advertising || 0) : 0}">
        </div>
        <div class="field">
          <label for="unitTemplate">Machote</label>
          ${templateAttachmentMarkup({
            templateContract: existingUnit?.templateContract || `Machote ${property.type.toLowerCase()}`,
            templateAttachmentName: existingUnit?.templateAttachmentName || ""
          })}
        </div>
      </div>
      <div class="form-actions">
        <button class="secondary-button" type="button" data-modal-cancel>
          <span data-icon="x" aria-hidden="true"></span>
          Cancelar
        </button>
        <button class="action-button" type="submit">
          <span data-icon="building" aria-hidden="true"></span>
          ${isEditing ? "Guardar cambios" : "Guardar unidad"}
        </button>
      </div>
    </form>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-template-attachment]")?.addEventListener("change", (event) => updateTemplateAttachmentPreview(event.currentTarget));
  els.modalBody.querySelector("[data-modal-cancel]")?.addEventListener("click", closeModal);
  els.modalBody.querySelector("#unitForm")?.addEventListener("submit", (event) => saveUnitFromForm(event, propertyId, unitId));
  openModal();
}

function isTemplateAttachmentFile(file) {
  if (!(file instanceof File)) return false;
  const allowedExtensions = [".pdf", ".doc", ".docx"];
  const allowedTypes = [
    "application/pdf",
    "application/msword",
    "application/vnd.openxmlformats-officedocument.wordprocessingml.document"
  ];
  const fileName = file.name.toLowerCase();
  return allowedTypes.includes(file.type) || allowedExtensions.some((extension) => fileName.endsWith(extension));
}

function updateTemplateAttachmentPreview(input) {
  const file = input.files?.[0];
  if (!file) return;

  if (!isTemplateAttachmentFile(file)) {
    input.value = "";
    toast("El machote adjunto debe ser PDF, DOC o DOCX.");
    return;
  }

  const button = input.closest(".template-upload-button");
  const note = els.modalBody.querySelector("[data-template-file-note]");
  if (button) {
    button.classList.add("has-file");
    button.title = file.name;
    button.querySelector(".template-upload-text").textContent = "Adjunto";
    const icon = button.querySelector("[data-icon]");
    if (icon) icon.dataset.icon = "checkCircle";
    injectIcons(button);
  }
  if (note) note.textContent = `Archivo adjunto: ${file.name}`;
}

function saveUnitFromForm(event, propertyId, unitId = null) {
  event.preventDefault();
  const shouldReturnToPropertiesCatalog = view.activeTab === "properties";
  const property = getProperty(propertyId);
  const existingUnit = unitId ? state.units.find((item) => item.id === unitId && item.propertyId === propertyId) : null;
  const isEditing = Boolean(existingUnit);
  const data = new FormData(event.currentTarget);
  const unitName = String(data.get("unitName") || "").trim();
  const unitNumber = String(data.get("unitNumber") || "").trim();
  const unitLabel = buildUnitLabel(unitName, unitNumber);
  const unitLevel = String(data.get("unitLevel") || "").trim();
  const tenantName = String(data.get("tenant") || "").trim() || "Disponible";
  const tenantChanged = isEditing && normalizeText(existingUnit?.tenant) !== normalizeText(tenantName);
  const matchedTenant = tenantName === "Disponible"
    ? null
    : tenantRows().find((tenant) => normalizeText(tenant.name) === normalizeText(tenantName));
  const monthlyRent = Number(data.get("monthlyRent") || 0);
  const maintenanceTotal = Number(data.get("maintenance") || 0);
  const templateAttachment = data.get("templateAttachment");
  const hasTemplateAttachment = templateAttachment instanceof File && templateAttachment.size > 0;

  if (!property || (unitId && !existingUnit) || !unitName || !unitNumber || monthlyRent < 0) {
    toast("Revisa nombre, numero y renta mensual.");
    return;
  }

  if (hasTemplateAttachment && !isTemplateAttachmentFile(templateAttachment)) {
    toast("El machote adjunto debe ser PDF, DOC o DOCX.");
    return;
  }

  const unit = normalizeUnitPayments({
    ...(existingUnit || {}),
    id: existingUnit?.id || uniqueId("unit", `${property.name}-${unitLabel}`, state.units.map((item) => item.id)),
    propertyId,
    unit: unitLabel,
    unitName,
    unitNumber,
    unitLevel,
    squareMeters: Number(data.get("squareMeters") || 0),
    measurements: String(data.get("measurements") || "").trim(),
    tenant: tenantName,
    tenantUserId: tenantChanged ? matchedTenant?.userId || null : existingUnit?.tenantUserId || matchedTenant?.userId || null,
    tenantProfileId: tenantChanged ? matchedTenant?.id || null : existingUnit?.tenantProfileId || matchedTenant?.id || null,
    tenantAssignmentManual: tenantChanged ? true : Boolean(existingUnit?.tenantAssignmentManual),
    monthlyRent,
    rentTotal: monthlyRent,
    maintenanceTotal,
    extraordinary: isEditing ? Number(existingUnit?.extraordinary || 0) : 0,
    services: isEditing ? Number(existingUnit?.services || 0) : 0,
    maintenance: maintenanceTotal,
    advertising: Number(data.get("advertising") || 0),
    contractStart: existingUnit?.contractStart || defaultUnitContractStart(),
    contractEnd: existingUnit?.contractEnd || defaultUnitContractEnd(),
    contractTermValidated: isEditing ? isContractTermValidated(existingUnit) : false,
    templateContract: String(data.get("templateContract") || "").trim() || `Machote ${property.type.toLowerCase()}`,
    templateAttachmentName: hasTemplateAttachment ? templateAttachment.name : existingUnit?.templateAttachmentName || "",
    templateAttachmentType: hasTemplateAttachment ? templateAttachment.type : existingUnit?.templateAttachmentType || "",
    templateAttachmentUploadedAt: hasTemplateAttachment ? new Date().toISOString() : existingUnit?.templateAttachmentUploadedAt || "",
    signedContract: existingUnit?.signedContract || "Pendiente de firma",
    paymentStatus: existingUnit?.paymentStatus || {}
  });

  if (isEditing) {
    state.units = state.units.map((item) => item.id === unit.id ? unit : item);
  } else {
    state.units.push(unit);
  }
  saveState();
  if (shouldReturnToPropertiesCatalog) {
    returnToPropertiesCatalog(propertyId);
  } else {
    closeModal();
    view.propertyDetailId = propertyId;
    view.propertyFilter = propertyId;
    view.activeTab = "property_detail";
    render();
  }
  toast(isEditing ? "Unidad actualizada" : "Unidad creada");
}

function openPropertyTeamModal(propertyId) {
  const property = getProperty(propertyId);
  if (!property) return;

  els.modal?.classList.add("modal-wide");
  els.modalEyebrow.textContent = "Usuarios de propiedad";
  els.modalTitle.textContent = property.name;
  els.modalBody.innerHTML = `
    <section class="property-access-panel property-team-editor">
      <div class="section-header">
        <div>
          <p class="eyebrow">Accesos activos</p>
          <h3>Responsables de esta propiedad</h3>
          <p class="muted">Edita los datos de gerente de propiedad y contabilidad local.</p>
        </div>
      </div>
      <div class="table-panel property-team-table-panel">
        <div class="table-scroll">
          <table class="property-team-editor-table">
            <thead>
              <tr>
                <th>Panel de acceso</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Usuario</th>
                <th>Contrasena</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              ${propertyTeamEditorRowsMarkup(property)}
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <form id="propertyUserForm" class="property-user-form">
      <div class="section-header">
        <div>
          <p class="eyebrow">Alta adicional</p>
          <h3>Crear usuario de propiedad</h3>
        </div>
      </div>
      <div class="form-grid">
        <div class="field">
          <label for="propertyUserRole">Panel de acceso</label>
          <select id="propertyUserRole" name="role">
            <option value="project_manager">Gerente de Propiedad</option>
            <option value="local_accounting">Contabilidad local</option>
          </select>
        </div>
        <div class="field">
          <label for="propertyUserName">Nombre</label>
          <input id="propertyUserName" name="name" required placeholder="Nombre completo">
        </div>
        <div class="field span-2">
          <label for="propertyUserEmail">Correo</label>
          <input id="propertyUserEmail" name="email" type="email" required placeholder="correo@empresa.com">
        </div>
      </div>
      <div class="form-actions">
        <button class="secondary-button" type="button" data-modal-cancel>
          <span data-icon="x" aria-hidden="true"></span>
          Cancelar
        </button>
        <button class="action-button" type="submit">
          <span data-icon="shield" aria-hidden="true"></span>
          Crear usuario
        </button>
      </div>
    </form>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-modal-cancel]")?.addEventListener("click", closeModal);
  els.modalBody.querySelectorAll("[data-edit-property-access]").forEach((button) => {
    button.addEventListener("click", () => enablePropertyAccessEditing(button.dataset.editPropertyAccess));
  });
  els.modalBody.querySelectorAll("[data-save-property-access]").forEach((button) => {
    button.addEventListener("click", () => savePropertyAccessUser(propertyId, button.dataset.savePropertyAccess));
  });
  els.modalBody.querySelectorAll("[data-delete-property-access]").forEach((button) => {
    button.addEventListener("click", () => openDeletePropertyAccessModal(propertyId, button.dataset.deletePropertyAccess));
  });
  els.modalBody.querySelector("#propertyUserForm")?.addEventListener("submit", (event) => createPropertyUserFromForm(event, propertyId));
  openModal();
}

function propertyTeamEditorRowsMarkup(property) {
  return [
    { role: "project_manager", propertyKey: "managerUserId", label: "Gerente de propiedad" },
    { role: "local_accounting", propertyKey: "localAccountingUserId", label: "Contabilidad local" }
  ].map((access) => propertyTeamEditorRowMarkup(property, access)).join("");
}

function propertyTeamEditorRowMarkup(property, access) {
  const user = getUser(property[access.propertyKey]);
  const userId = user?.id || "";
  const readonlyAttr = userId ? "readonly" : "";
  return `
    <tr data-property-access-row="${access.role}" data-user-id="${userId}">
      <td><strong>${access.label}</strong></td>
      <td>
        <input data-access-field="name" value="${escapeAttribute(user?.name || "")}" placeholder="Nombre completo" ${readonlyAttr}>
      </td>
      <td>
        <input data-access-field="email" type="email" value="${escapeAttribute(user?.email || "")}" placeholder="correo@empresa.com" ${readonlyAttr}>
      </td>
      <td>
        <input data-access-field="username" value="${escapeAttribute(user?.username || "")}" placeholder="usuario" ${readonlyAttr}>
      </td>
      <td>
        <input data-access-field="password" type="text" value="${escapeAttribute(user?.password || defaultPassword({ role: access.role }))}" placeholder="Contrasena" ${readonlyAttr}>
      </td>
      <td>
        <div class="property-team-actions">
          <button class="secondary-button" type="button" data-edit-property-access="${access.role}" ${userId ? "" : "disabled"}>
            <span data-icon="settings" aria-hidden="true"></span>
            Editar Usuario
          </button>
          <button class="action-button" type="button" data-save-property-access="${access.role}">
            <span data-icon="checkCircle" aria-hidden="true"></span>
            Guardar
          </button>
          <button class="danger-button" type="button" data-delete-property-access="${access.role}" ${userId ? "" : "disabled"}>
            <span data-icon="x" aria-hidden="true"></span>
            Eliminar Usuario
          </button>
        </div>
      </td>
    </tr>
  `;
}

function enablePropertyAccessEditing(role) {
  const row = els.modalBody.querySelector(`[data-property-access-row="${role}"]`);
  if (!row) return;

  row.classList.add("is-editing");
  row.querySelectorAll("[data-access-field]").forEach((input) => {
    input.removeAttribute("readonly");
  });
  const editButton = row.querySelector(`[data-edit-property-access="${role}"]`);
  if (editButton) editButton.disabled = true;
  row.querySelector('[data-access-field="name"]')?.focus();
  toast("Edita los datos y guarda los cambios.");
}

function propertyAccessUsers(propertyId) {
  return state.users.filter((user) =>
    ["project_manager", "local_accounting"].includes(user.role) &&
    ((user.propertyIds || []).includes(propertyId) || user.id === getProperty(propertyId)?.managerUserId || user.id === getProperty(propertyId)?.localAccountingUserId)
  );
}

function savePropertyAccessUser(propertyId, role) {
  const property = getProperty(propertyId);
  const row = els.modalBody.querySelector(`[data-property-access-row="${role}"]`);
  if (!property || !row || !["project_manager", "local_accounting"].includes(role)) return;

  const value = (field) => String(row.querySelector(`[data-access-field="${field}"]`)?.value || "").trim();
  const name = value("name");
  const email = value("email").toLowerCase();
  const username = value("username");
  const password = value("password");
  const userId = row.dataset.userId;
  const existingUser = getUser(userId);

  if (!name || !email || !username || !password) {
    toast("Completa nombre, correo, usuario y contrasena.");
    return;
  }

  if (state.users.some((user) => user.id !== userId && normalizeText(user.email) === normalizeText(email))) {
    toast("Ya existe un usuario con ese correo.");
    return;
  }

  if (state.users.some((user) => user.id !== userId && normalizeText(user.username) === normalizeText(username))) {
    toast("Ya existe un usuario con ese nombre de usuario.");
    return;
  }

  let savedUser = existingUser;
  if (savedUser) {
    savedUser.name = name;
    savedUser.email = email;
    savedUser.username = username;
    savedUser.password = password;
    savedUser.role = role;
    savedUser.propertyIds = Array.from(new Set([...(savedUser.propertyIds || []), propertyId]));
  } else {
    const prefix = role === "project_manager" ? "u-gerente" : "u-conta-local";
    const newUserId = uniqueId(prefix, name, state.users.map((user) => user.id));
    savedUser = {
      id: newUserId,
      name,
      email,
      username,
      password,
      role,
      propertyIds: [propertyId]
    };
    state.users.push(savedUser);
  }

  if (role === "project_manager") {
    property.managerUserId = savedUser.id;
  } else {
    property.localAccountingUserId = savedUser.id;
  }

  saveState();
  renderRoleOptions();
  openPropertyTeamModal(propertyId);
  render();
  toast("Usuario actualizado");
}

function openDeletePropertyAccessModal(propertyId, role) {
  const property = getProperty(propertyId);
  const access = propertyAccessConfig(role);
  const user = access ? getUser(property?.[access.propertyKey]) : null;
  if (!property || !access || !user) {
    toast("No hay usuario asignado para eliminar.");
    return;
  }

  els.modalEyebrow.textContent = "Eliminar usuario";
  els.modalTitle.textContent = access.label;
  els.modalBody.innerHTML = `
    <section class="delete-confirmation">
      <p class="delete-question">Estas seguro que quieres eliminar al usuario ${user.name}?</p>
      <div class="modal-grid">
        <div class="detail-box">
          <span>Propiedad</span>
          <strong>${property.name}</strong>
        </div>
        <div class="detail-box">
          <span>Panel de acceso</span>
          <strong>${access.label}</strong>
        </div>
        <div class="detail-box">
          <span>Usuario</span>
          <strong>${user.username}</strong>
        </div>
        <div class="detail-box">
          <span>Correo</span>
          <strong>${user.email}</strong>
        </div>
      </div>
      <div class="form-actions delete-confirmation-actions">
        <button class="secondary-button" type="button" data-cancel-delete-property-access>
          <span data-icon="x" aria-hidden="true"></span>
          No
        </button>
        <button class="danger-button" type="button" data-confirm-delete-property-access>
          <span data-icon="x" aria-hidden="true"></span>
          Si, eliminar usuario
        </button>
      </div>
    </section>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-cancel-delete-property-access]")?.addEventListener("click", () => openPropertyTeamModal(propertyId));
  els.modalBody.querySelector("[data-confirm-delete-property-access]")?.addEventListener("click", () => deletePropertyAccessUser(propertyId, role));
}

function deletePropertyAccessUser(propertyId, role) {
  const property = getProperty(propertyId);
  const access = propertyAccessConfig(role);
  const userId = access ? property?.[access.propertyKey] : "";
  const user = getUser(userId);
  if (!property || !access || !user) return;

  property[access.propertyKey] = "";
  user.propertyIds = (user.propertyIds || []).filter((item) => item !== propertyId);

  const stillAssigned = state.properties.some((item) =>
    item.id !== propertyId && (item.managerUserId === user.id || item.localAccountingUserId === user.id)
  ) || user.propertyIds.length > 0;

  if (!stillAssigned) {
    state.users = state.users.filter((item) => item.id !== user.id);
  }

  saveState();
  renderRoleOptions();
  openPropertyTeamModal(propertyId);
  render();
  toast("Usuario eliminado de la propiedad");
}

function propertyAccessConfig(role) {
  return {
    project_manager: { propertyKey: "managerUserId", label: "Gerente de propiedad" },
    local_accounting: { propertyKey: "localAccountingUserId", label: "Contabilidad local" }
  }[role] || null;
}

function createPropertyUserFromForm(event, propertyId) {
  event.preventDefault();
  const property = getProperty(propertyId);
  const data = new FormData(event.currentTarget);
  const role = String(data.get("role") || "project_manager");
  const name = String(data.get("name") || "").trim();
  const email = String(data.get("email") || "").trim().toLowerCase();

  if (!property || !name || !email || !["project_manager", "local_accounting"].includes(role)) {
    toast("Revisa nombre, correo y rol.");
    return;
  }

  if (state.users.some((user) => normalizeText(user.email) === normalizeText(email))) {
    toast("Ya existe un usuario con ese correo.");
    return;
  }

  const prefix = role === "project_manager" ? "u-gerente" : "u-conta-local";
  const userId = uniqueId(prefix, name, state.users.map((user) => user.id));
  const user = {
    id: userId,
    name,
    email,
    username: uniqueUsername(defaultUsername({ name, email, id: userId }), state.users),
    password: defaultPassword({ role }),
    role,
    propertyIds: [propertyId]
  };

  state.users.push(user);
  if (role === "project_manager") {
    property.managerUserId = userId;
  } else {
    property.localAccountingUserId = userId;
  }

  saveState();
  renderRoleOptions();
  closeModal();
  render();
  toast("Usuario creado y acceso asignado");
}

function renderTenantCatalog() {
  if (!canViewTenants()) {
    els.contentArea.innerHTML = emptyState("Este catalogo no esta disponible para el rol activo.");
    return;
  }

  const properties = visibleProperties();
  if (!properties.length) {
    els.contentArea.innerHTML = emptyState("No hay propiedades registradas.");
    return;
  }

  const { activeIndex, property } = resolveCatalogPropertySelection(properties, view.tenantCatalogPropertyId);
  view.tenantCatalogPropertyId = property.id;
  view.propertyFilter = property.id;

  els.contentArea.innerHTML = `
    <section class="plaza-dashboard-page plaza-catalog-page">
      ${plazaCatalogSelectorMarkup(properties, activeIndex, "Selector de plazas del catalogo de arrendatarios")}
      ${renderTenantCatalogForProperty(property, { markupOnly: true })}
    </section>
  `;

  bindCatalogPlazaSelector(properties, activeIndex, (propertyId) => {
    view.tenantCatalogPropertyId = propertyId;
    view.propertyFilter = propertyId;
    render();
  });
  bindTenantCatalogForPropertyActions(property);
}

function openTenantCatalogProperty(propertyId) {
  if (!getProperty(propertyId)) return;
  rememberSelectedProperty(propertyId);
  view.tenantCatalogPropertyId = propertyId;
  view.propertyFilter = propertyId;
  render();
}

function renderTenantCatalogForProperty(property, options = {}) {
  view.propertyFilter = property.id;
  const tenants = visibleTenantRows();

  const markup = `
    <section class="plaza-catalog-content">
      <div class="section-header">
        <div>
          <p class="eyebrow">Arrendatarios</p>
          <h3>${property.name}</h3>
          <p class="muted">${property.type} - ${property.location}. Consulta datos fiscales, contacto, unidades asignadas y acceso a plataforma.</p>
        </div>
        <div class="section-actions">
          ${canManageTenants() ? `
            <button class="action-button" type="button" data-action="new-tenant">
              <span data-icon="users" aria-hidden="true"></span>
              Alta arrendatario
            </button>
          ` : ""}
        </div>
      </div>
      ${tenants.length ? tenantCatalogTableMarkup(tenants) : emptyState("No hay arrendatarios registrados para esta propiedad.")}
    </section>
  `;

  if (options.markupOnly) return markup;
  els.contentArea.innerHTML = markup;
  bindTenantCatalogForPropertyActions(property);
  return markup;
}

function bindTenantCatalogForPropertyActions(property) {
  els.contentArea.querySelector("[data-action='new-tenant']")?.addEventListener("click", () => {
    view.propertyFilter = property.id;
    view.activeTab = "tenant_new";
    render();
  });

  els.contentArea.querySelectorAll("[data-tenant-detail]").forEach((button) => {
    button.addEventListener("click", () => openTenantDetail(button.dataset.tenantDetail));
  });

  els.contentArea.querySelectorAll("[data-tenant-contact]").forEach((button) => {
    button.addEventListener("click", () => openTenantContact(button.dataset.tenantContact));
  });
}

function tenantCatalogTableMarkup(tenants) {
  return `
    <div class="table-panel">
      <div class="table-scroll">
        <table class="tenant-table">
          <thead>
            <tr>
              <th>Arrendatario</th>
              <th>Tipo</th>
              <th>RFC</th>
              <th>Unidades</th>
              <th>Contrato</th>
              <th>Datos de contacto</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${tenants.map((tenant) => tenantCatalogRowMarkup(tenant)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function tenantCatalogRowMarkup(tenant) {
  const leaseStatus = tenantLeaseContractStatus(tenant);

  return `
    <tr>
      <td class="primary-cell">
        <strong>${escapeAttribute(tenant.name)}</strong>
        <small>${tenant.hasPortalAccess ? "Con usuario de plataforma" : "Sin usuario de plataforma"}</small>
      </td>
      <td>${escapeAttribute(tenant.type)}</td>
      <td class="nowrap">${escapeAttribute(tenant.rfc || "Sin RFC")}</td>
      <td>${escapeAttribute(tenant.unitsLabel)}</td>
      <td class="tenant-contract-column">
        <span class="status-pill ${leaseStatus.className}" title="${escapeAttribute(leaseStatus.description)}">
          ${leaseStatus.label}
        </span>
      </td>
      <td class="tenant-contact-column">
        <button class="secondary-button" type="button" data-tenant-contact="${escapeAttribute(tenant.id)}">
          <span data-icon="eye" aria-hidden="true"></span>
          Ver
        </button>
      </td>
      <td>
        <button class="secondary-button" type="button" data-tenant-detail="${escapeAttribute(tenant.id)}">
          <span data-icon="eye" aria-hidden="true"></span>
          Detalle
        </button>
      </td>
    </tr>
  `;
}

function tenantLeaseContractStatus(tenant) {
  const contracts = (tenant.assignedUnits || [])
    .map((unit) => ({ unit, daysRemaining: tenantContractDaysRemaining(unit) }))
    .filter((contract) => Number.isFinite(contract.daysRemaining));

  if (!contracts.length) {
    return { label: "Sin contrato", className: "status-neutral", description: "Sin contrato de arrendamiento" };
  }

  const currentContracts = contracts
    .filter((contract) => contract.daysRemaining >= 0)
    .sort((first, second) => first.daysRemaining - second.daysRemaining);

  if (!currentContracts.length) {
    return { label: "Vencido", className: "status-danger", description: "Contrato de arrendamiento vencido" };
  }

  const daysRemaining = currentContracts[0].daysRemaining;
  return {
    label: "Vigente",
    className: daysRemaining < 60 ? "status-pending" : "status-paid",
    description: daysRemaining === 0 ? "Vence hoy" : `Vence en ${daysRemaining} dias`
  };
}

function tenantContractDaysRemaining(unit) {
  if (!unit?.contractEnd) return Number.NaN;
  const [year, month, day] = String(unit.contractEnd).split("-").map(Number);
  const endDate = new Date(year, month - 1, day);
  if (Number.isNaN(endDate.getTime())) return Number.NaN;

  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  return Math.round((endDate - today) / (1000 * 60 * 60 * 24));
}

function renderTenantNew() {
  if (!canManageTenants()) {
    els.contentArea.innerHTML = emptyState("Solo administracion o gerente de propiedad pueden dar de alta arrendatarios.");
    return;
  }

  const properties = visibleProperties();
  if (!properties.length) {
    els.contentArea.innerHTML = emptyState("No hay propiedades asignadas para registrar arrendatarios.");
    return;
  }

  const selectedPropertyId = properties.some((property) => property.id === view.propertyFilter)
    ? view.propertyFilter
    : properties[0].id;

  els.contentArea.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">Alta</p>
        <h3>Nuevo arrendatario</h3>
        <p class="muted">Administracion y gerentes de propiedad pueden crear el arrendatario, su usuario de plataforma y, si aplica, asignarlo a una unidad disponible.</p>
      </div>
    </div>
    <section class="form-panel">
      <form id="tenantForm">
        <div class="form-grid">
          <div class="field span-2">
            <label for="tenantName">Nombre o razon social</label>
            <input id="tenantName" name="name" required placeholder="Nombre del arrendatario">
          </div>
          <div class="field">
            <label for="tenantType">Tipo de persona</label>
            <select id="tenantType" name="type">
              <option value="Persona moral">Persona moral</option>
              <option value="Persona fisica">Persona fisica</option>
              <option value="Sin clasificar">Sin clasificar</option>
            </select>
          </div>
          <div class="field">
            <label for="tenantRfc">RFC</label>
            <input id="tenantRfc" name="rfc" placeholder="RFC">
          </div>
          <div class="field">
            <label for="tenantEmail">Correo de plataforma</label>
            <input id="tenantEmail" name="email" type="email" required placeholder="correo@empresa.com">
          </div>
          <div class="field">
            <label for="tenantPhone">Telefono</label>
            <input id="tenantPhone" name="phone" placeholder="Telefono">
          </div>
          <div class="field">
            <label for="tenantContact">Contacto principal</label>
            <input id="tenantContact" name="contact" placeholder="Nombre del contacto">
          </div>
          <div class="field">
            <label for="tenantProperty">Propiedad</label>
            <select id="tenantProperty" name="propertyId" required>
              ${properties.map((property) => `<option value="${property.id}" ${property.id === selectedPropertyId ? "selected" : ""}>${property.name}</option>`).join("")}
            </select>
          </div>
          <div class="field">
            <label for="tenantUnit">Unidad disponible</label>
            <select id="tenantUnit" name="unitId">
              ${tenantAvailableUnitOptions(selectedPropertyId)}
            </select>
          </div>
          <div class="field span-2">
            <label for="tenantNotes">Notas</label>
            <textarea id="tenantNotes" name="notes" rows="4" placeholder="Notas administrativas, documentos pendientes o condiciones especiales"></textarea>
          </div>
        </div>
        <div class="form-actions">
          <button class="secondary-button" type="button" data-action="cancel-tenant">
            <span data-icon="x" aria-hidden="true"></span>
            Cancelar
          </button>
          <button class="action-button" type="submit">
            <span data-icon="shield" aria-hidden="true"></span>
            Guardar arrendatario
          </button>
        </div>
      </form>
    </section>
  `;

  const propertySelect = els.contentArea.querySelector("#tenantProperty");
  const unitSelect = els.contentArea.querySelector("#tenantUnit");
  propertySelect?.addEventListener("change", () => {
    unitSelect.innerHTML = tenantAvailableUnitOptions(propertySelect.value);
  });

  els.contentArea.querySelector("[data-action='cancel-tenant']")?.addEventListener("click", () => {
    view.activeTab = "tenants";
    render();
  });

  els.contentArea.querySelector("#tenantForm")?.addEventListener("submit", createTenantFromForm);
}

function tenantAvailableUnitOptions(propertyId) {
  const units = state.units.filter((unit) => unit.propertyId === propertyId && unit.tenant === "Disponible");
  return `
    <option value="">Sin asignar unidad</option>
    ${units.map((unit) => `<option value="${unit.id}">${unit.unit}</option>`).join("")}
  `;
}

function createTenantFromForm(event) {
  event.preventDefault();
  const form = event.currentTarget;
  const data = new FormData(form);
  const name = String(data.get("name") || "").trim();
  const email = String(data.get("email") || "").trim().toLowerCase();
  const propertyId = String(data.get("propertyId") || "");
  const unitId = String(data.get("unitId") || "");
  const allowedPropertyIds = new Set(visibleProperties().map((property) => property.id));

  if (!name || !email || !allowedPropertyIds.has(propertyId)) {
    toast("Revisa nombre, correo y propiedad.");
    return;
  }

  if (state.users.some((user) => normalizeText(user.email) === normalizeText(email))) {
    toast("Ya existe un usuario con ese correo.");
    return;
  }

  const userId = uniqueId("tenant", name, state.users.map((user) => user.id));
  const profileId = uniqueId("tp", name, state.tenantProfiles.map((profile) => profile.id));
  const newUser = {
    id: userId,
    name,
    email,
    username: uniqueUsername(defaultUsername({ name, email, id: userId }), state.users),
    password: defaultPassword({ role: "tenant" }),
    role: "tenant",
    propertyIds: []
  };
  const newProfile = {
    id: profileId,
    userId,
    name,
    type: String(data.get("type") || "Sin clasificar"),
    rfc: String(data.get("rfc") || "").trim().toUpperCase(),
    phone: String(data.get("phone") || "").trim(),
    contact: String(data.get("contact") || "").trim() || name,
    status: "Activo",
    propertyIds: [propertyId],
    notes: String(data.get("notes") || "").trim()
  };

  state.users.push(newUser);
  state.tenantProfiles.push(newProfile);

  if (unitId) {
    const unit = state.units.find((item) => item.id === unitId && item.propertyId === propertyId && item.tenant === "Disponible");
    if (unit) {
      unit.tenant = name;
      unit.tenantUserId = userId;
      unit.tenantProfileId = profileId;
    }
  }

  saveState();
  view.activeTab = "tenants";
  view.search = name;
  if (els.searchInput) els.searchInput.value = name;
  renderRoleOptions();
  render();
  toast("Arrendatario dado de alta");
}

function openTenantDetail(tenantId) {
  const tenant = tenantRows().find((item) => item.id === tenantId);
  if (!tenant) return;

  els.modalEyebrow.textContent = "Arrendatario";
  els.modalTitle.textContent = tenant.name;
  els.modalBody.innerHTML = `
    <div class="modal-grid">
      <div class="detail-box">
        <span>Tipo</span>
        <strong>${tenant.type}</strong>
      </div>
      <div class="detail-box">
        <span>RFC</span>
        <strong>${tenant.rfc || "Sin RFC"}</strong>
      </div>
      <div class="detail-box">
        <span>Contacto</span>
        <strong>${tenant.contact || tenant.name}</strong>
      </div>
      <div class="detail-box">
        <span>Representante legal</span>
        <strong>${tenant.legalRepresentative || tenant.contact || "Sin representante"}</strong>
      </div>
      <div class="detail-box">
        <span>Domicilio fiscal</span>
        <p>${tenant.fiscalAddress || "Sin domicilio fiscal"}</p>
      </div>
      <div class="detail-box">
        <span>Telefono</span>
        <strong>${tenant.phone || "Sin telefono"}</strong>
      </div>
      <div class="detail-box">
        <span>Correo</span>
        <strong>${tenant.email || "Sin correo"}</strong>
      </div>
      <div class="detail-box">
        <span>Estatus</span>
        <strong>${tenant.status}</strong>
      </div>
      <div class="detail-box">
        <span>Banco</span>
        <strong>${tenant.bankName || "Sin banco"}</strong>
      </div>
      <div class="detail-box">
        <span>Cuenta</span>
        <strong>${tenant.bankAccount || "Sin cuenta"}</strong>
      </div>
      <div class="detail-box">
        <span>CLABE</span>
        <strong>${tenant.bankClabe || "Sin CLABE"}</strong>
      </div>
      <div class="detail-box">
        <span>Referencia de pago</span>
        <strong>${tenant.paymentReference || "Sin referencia"}</strong>
      </div>
      <div class="detail-box">
        <span>Propiedades</span>
        <p>${tenant.propertiesLabel}</p>
      </div>
      <div class="detail-box">
        <span>Unidades</span>
        <p>${tenant.unitsLabel}</p>
      </div>
    </div>
    <div class="detail-box" style="margin-top: 12px;">
      <span>Notas</span>
      <p>${tenant.notes || "Sin notas registradas."}</p>
    </div>
  `;
  openModal();
}

function openTenantContact(tenantId) {
  const tenant = tenantRows().find((item) => item.id === tenantId);
  if (!tenant) return;

  els.modal?.classList.add("modal-compact");
  els.modalEyebrow.textContent = "Datos de contacto";
  els.modalTitle.textContent = tenant.name;
  els.modalBody.innerHTML = `
    <dl class="tenant-contact-details">
      <div>
        <dt>Contacto</dt>
        <dd>${escapeAttribute(tenant.contact || tenant.name)}</dd>
      </div>
      <div>
        <dt>Correo</dt>
        <dd>${escapeAttribute(tenant.email || "Sin correo")}</dd>
      </div>
      <div>
        <dt>Telefono</dt>
        <dd>${escapeAttribute(tenant.phone || "Sin telefono")}</dd>
      </div>
    </dl>
  `;
  openModal();
  els.modalClose?.focus();
}

function renderInvoices() {
  const units = visibleUnits();
  const periodKeys = recentMonthKeys();
  const invoices = units.flatMap((unit) =>
    periodKeys.flatMap((monthKey) =>
      paymentConcepts
        .filter((concept) => (unit[concept.key] || 0) > 0)
        .map((concept) => ({ unit, concept, monthKey }))
    )
  );

  els.contentArea.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">Facturacion</p>
        <h3>${view.roleId === "tenant" ? "Mis pagos y facturas" : "Revision de ingresos y facturas"}</h3>
        <p class="muted">${view.roleId === "tenant" ? "Paga los importes pendientes y descarga la factura automaticamente." : "Consulta el estado de cada factura por propiedad, unidad, mes y concepto."}</p>
      </div>
    </div>
    ${invoices.length ? invoicesTableMarkup(invoices) : emptyState("No hay facturas visibles con los filtros actuales.")}
  `;

  els.contentArea.querySelectorAll("[data-pay-invoice]").forEach((button) => {
    button.addEventListener("click", () => payInvoice(button.dataset.unitId, button.dataset.concept, button.dataset.paymentMonth));
  });

  els.contentArea.querySelectorAll("[data-download-invoice]").forEach((button) => {
    button.addEventListener("click", () => downloadInvoice(button.dataset.unitId, button.dataset.concept, button.dataset.paymentMonth));
  });
}

function invoicesTableMarkup(items) {
  return `
    <div class="table-panel">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Mes</th>
              <th>Propiedad</th>
              <th>Unidad</th>
              <th>Arrendatario</th>
              <th>Concepto</th>
              <th>Importe</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            ${items.map(({ unit, concept, monthKey }) => invoiceRowMarkup(unit, concept, monthKey)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function invoiceRowMarkup(unit, concept, monthKey) {
  const property = getProperty(unit.propertyId);
  const status = getPaymentStatus(unit, monthKey, concept.key);
  const isPaid = status === "paid";
  const canPay = view.roleId === "tenant" || canManagePayments();

  return `
    <tr>
      <td class="nowrap"><strong>${formatMonthShort(monthKey)}</strong></td>
      <td class="primary-cell">
        <strong>${property?.name || "Sin propiedad"}</strong>
        <small>${property?.type || ""}</small>
      </td>
      <td><strong>${unit.unit}</strong></td>
      <td>${unit.tenant}</td>
      <td>${concept.label}</td>
      <td><strong>${formatCurrency(unit[concept.key])}</strong></td>
      <td>
        <span class="status-pill ${isPaid ? "status-paid" : "status-pending"}">
          ${renderIcon(isPaid ? "checkCircle" : "alertCircle")}
          ${isPaid ? "Pagado" : "Por pagar"}
        </span>
      </td>
      <td>
        <div class="section-actions">
          ${!isPaid && canPay ? `
            <button class="action-button" type="button" data-pay-invoice="${concept.key}" data-concept="${concept.key}" data-payment-month="${monthKey}" data-unit-id="${unit.id}">
              <span data-icon="creditCard" aria-hidden="true"></span>
              Pagar
            </button>
          ` : ""}
          <button class="secondary-button" type="button" ${isPaid ? "" : "disabled"} data-download-invoice="${concept.key}" data-concept="${concept.key}" data-payment-month="${monthKey}" data-unit-id="${unit.id}">
            <span data-icon="download" aria-hidden="true"></span>
            Factura
          </button>
        </div>
      </td>
    </tr>
  `;
}

function payInvoice(unitId, conceptKey, monthKey = currentMonthKey()) {
  const unit = state.units.find((item) => item.id === unitId);
  if (!unit) return;
  const record = ensurePaymentRecord(unit, monthKey, conceptKey);
  record.amount = conceptAmountForMonth(unit, conceptKey, monthKey);
  record.validated = true;
  record.validatedAt = new Date().toISOString();
  setPaymentStatus(unit, monthKey, conceptKey, "paid");
  saveState();
  render();
  toast("Pago registrado. La factura ya esta disponible.");
}

function downloadInvoice(unitId, conceptKey, monthKey = currentMonthKey()) {
  const unit = state.units.find((item) => item.id === unitId);
  const concept = paymentConcepts.find((item) => item.key === conceptKey);
  const property = getProperty(unit?.propertyId);
  if (!unit || !concept) return;

  if (getPaymentStatus(unit, monthKey, conceptKey) !== "paid") {
    toast("La factura se habilita cuando el concepto esta pagado.");
    return;
  }

  const invoice = [
    "Factura Rentas 360",
    `Periodo: ${formatMonthLabel(monthKey)}`,
    `Propiedad: ${property?.name || ""}`,
    `Unidad: ${unit.unit}`,
    `Arrendatario: ${unit.tenant}`,
    `Concepto: ${concept.label}`,
    `Importe: ${formatCurrency(unit[concept.key])}`,
    "Estado: Pagado"
  ].join("\n");

  const blob = new Blob([invoice], { type: "text/plain;charset=utf-8" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = `factura-${unit.unit.replace(/\s+/g, "-").toLowerCase()}-${monthKey}-${concept.key}.txt`;
  document.body.appendChild(link);
  link.click();
  link.remove();
  URL.revokeObjectURL(url);
  toast("Factura descargada");
}

function renderHistory() {
  const units = visibleUnits();
  const monthKeys = historyMonthKeys(units);

  els.contentArea.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">Historial</p>
        <h3>Meses anteriores a la ventana operativa</h3>
        <p class="muted">Aqui se concentran los meses previos al mes actual y seis meses atras.</p>
      </div>
      <div class="section-actions">
        <button class="action-button" type="button" data-action="download-report">
          <span data-icon="download" aria-hidden="true"></span>
          Reporte
        </button>
      </div>
    </div>
    ${units.length && monthKeys.length ? historyTableMarkup(units, monthKeys) : emptyState("No hay meses historicos con los filtros actuales.")}
  `;

  bindUnitActions();
  bindReportActions();
}

function historyTableMarkup(units, monthKeys) {
  return `
    <div class="table-panel">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Mes</th>
              <th>Propiedad</th>
              <th>Unidad</th>
              <th>Arrendatario</th>
              <th>Renta mensual</th>
              <th>Extraordinarios</th>
              <th>Servicios</th>
              <th>Mantenimiento</th>
              <th>Publicidad</th>
              <th>Inicio contrato</th>
              <th>Fin contrato</th>
              <th>Machote</th>
              <th>Firmado</th>
            </tr>
          </thead>
          <tbody>
            ${monthKeys.flatMap((monthKey) => units.map((unit) => unitRowMarkup(unit, monthKey))).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function bindReportActions() {
  els.contentArea.querySelectorAll("[data-action='download-report']").forEach((button) => {
    button.addEventListener("click", downloadHistoricalReport);
  });
}

function downloadHistoricalReport() {
  const reportUnits = visibleUnits({ ignoreStatusFilter: true });
  const monthKeys = allLedgerMonthKeys(reportUnits).reverse();
  const scopeLabel = reportScopeLabel();
  const rows = [
    [
      "Propiedad",
      "Tipo de propiedad",
      "Unidad",
      "Arrendatario",
      "Mes",
      "Concepto",
      "Monto",
      "Estatus",
      "Inicio de contrato",
      "Fin de contrato",
      "Contrato machote",
      "Contrato firmado"
    ]
  ];

  reportUnits.forEach((unit) => {
    const property = getProperty(unit.propertyId);
    monthKeys.forEach((monthKey) => {
      paymentConcepts.forEach((concept) => {
        rows.push([
          property?.name || "",
          property?.type || "",
          unit.unit,
          unit.tenant,
          formatMonthLabel(monthKey),
          concept.label,
          unit[concept.key] || 0,
          getPaymentStatus(unit, monthKey, concept.key) === "paid" ? "Pagado" : "Por pagar",
          unit.contractStart,
          unit.contractEnd,
          unit.templateContract,
          unit.signedContract
        ]);
      });
    });
  });

  const csv = rows.map((row) => row.map(csvValue).join(",")).join("\n");
  const blob = new Blob([`\ufeff${csv}`], { type: "text/csv;charset=utf-8" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = `reporte-historico-${slugify(scopeLabel)}.csv`;
  document.body.appendChild(link);
  link.click();
  link.remove();
  URL.revokeObjectURL(url);
  toast("Reporte historico descargado");
}

function reportScopeLabel() {
  if (view.propertyFilter !== "all") {
    return getProperty(view.propertyFilter)?.name || "propiedad";
  }
  return canSeeEveryProperty() ? "todas-las-propiedades" : "propiedades-visibles";
}

function csvValue(value) {
  const text = String(value ?? "");
  return `"${text.replace(/"/g, '""')}"`;
}

function slugify(value) {
  return String(value)
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/^-|-$/g, "") || "reporte";
}

function uniqueId(prefix, source, existingIds = []) {
  const base = `${prefix}-${slugify(source) || "nuevo"}`;
  const existing = new Set(existingIds);
  let id = base;
  let index = 2;

  while (existing.has(id)) {
    id = `${base}-${index}`;
    index += 1;
  }

  return id;
}

function uniqueUsername(baseUsername, users = state.users) {
  const existing = new Set(users.map((user) => normalizeText(user.username)));
  let username = baseUsername;
  let index = 2;

  while (existing.has(normalizeText(username))) {
    username = `${baseUsername}.${index}`;
    index += 1;
  }

  return username;
}

function renderContracts() {
  const units = visibleUnits();
  const isTenant = view.roleId === "tenant";

  els.contentArea.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">${isTenant ? "Mis documentos" : "Legal"}</p>
        <h3>${isTenant ? "Mis contratos de arrendamiento" : "Contratos de arrendamiento"}</h3>
        <p class="muted">${isTenant ? "Consulta el contrato vigente y el documento firmado de cada unidad rentada." : "Seguimiento de machotes, contratos firmados y fechas criticas."}</p>
      </div>
    </div>
    ${units.length ? contractsTableMarkup(units) : emptyState("No hay contratos visibles con los filtros actuales.")}
  `;

  els.contentArea.querySelectorAll("[data-contract]").forEach((button) => {
    button.addEventListener("click", () => openContractModal(button.dataset.unitId, button.dataset.contract));
  });
}

function contractsTableMarkup(units) {
  return `
    <div class="table-panel">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Propiedad</th>
              <th>Unidad</th>
              <th>Arrendatario</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Estado legal</th>
              <th>Machote</th>
              <th>Firmado</th>
            </tr>
          </thead>
          <tbody>
            ${units.map((unit) => contractRowMarkup(unit)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function contractRowMarkup(unit) {
  const property = getProperty(unit.propertyId);
  const status = contractStatus(unit);

  return `
    <tr>
      <td class="primary-cell">
        <strong>${property?.name || "Sin propiedad"}</strong>
        <small>${property?.location || ""}</small>
      </td>
      <td><strong>${unit.unit}</strong></td>
      <td>${unit.tenant}</td>
      <td>${formatDate(unit.contractStart)}</td>
      <td>${formatDate(unit.contractEnd)}</td>
      <td><span class="status-pill ${status.className}">${status.label}</span></td>
      <td>
        <button class="secondary-button" type="button" data-contract="template" data-unit-id="${unit.id}">
          <span data-icon="${unit.templateAttachmentName ? "checkCircle" : "fileText"}" aria-hidden="true"></span>
          ${unit.templateAttachmentName ? "Adjunto" : "Machote"}
        </button>
      </td>
      <td>
        <button class="secondary-button" type="button" data-contract="signed" data-unit-id="${unit.id}">
          <span data-icon="eye" aria-hidden="true"></span>
          Firmado
        </button>
      </td>
    </tr>
  `;
}

function contractStatus(unit) {
  if (!unit?.contractEnd) {
    return { label: "Sin Contrato", kind: "none", className: "status-neutral" };
  }
  const now = new Date();
  const [year, month, day] = unit.contractEnd.split("-").map(Number);
  const endDate = new Date(year, month - 1, day);
  if (Number.isNaN(endDate.getTime())) {
    return { label: "Sin Contrato", kind: "none", className: "status-neutral" };
  }
  const diffDays = Math.ceil((endDate - now) / (1000 * 60 * 60 * 24));

  if (diffDays < 0) {
    return { label: "Vencido", kind: "expired", className: "status-danger" };
  }
  if (diffDays <= 30) {
    return { label: `Vence en ${diffDays} dias`, kind: "critical", className: "status-danger" };
  }
  if (diffDays <= 90) {
    return { label: `Vence en ${diffDays} dias`, kind: "warning", className: "status-pending" };
  }
  return { label: "Activo", kind: "active", className: "status-paid" };
}

function isContractExpired(unit) {
  return contractStatus(unit).kind === "expired";
}

function isContractTermValidated(unit) {
  if (!unit || isUnitAvailable(unit) || !unit.contractStart || !unit.contractEnd) return false;
  if (isContractExpired(unit)) return false;
  if (typeof unit.contractTermValidated === "boolean") return unit.contractTermValidated;
  return true;
}

function contractTermValidationLabel(unit) {
  return isContractTermValidated(unit) ? "Validado" : "No validado";
}

function contractTermValidationMarkup(unit) {
  const validated = isContractTermValidated(unit);
  return `<span class="status-pill ${validated ? "status-paid" : "status-neutral"}">${validated ? "Validado" : "No validado"}</span>`;
}

function openContractModal(unitId, type) {
  const unit = state.units.find((item) => item.id === unitId);
  const property = getProperty(unit?.propertyId);
  if (!unit) return;

  const isTemplate = type === "template";
  const isNextPeriod = type === "nextPeriod";
  const templateAttachmentName = unit.templateAttachmentName || "";
  const documentName = isTemplate
    ? unit.templateContract
    : isNextPeriod
      ? unit.nextPeriodContract
      : unit.signedContract;
  const startDate = isNextPeriod ? unit.nextPeriodContractStart || unit.contractStart : unit.contractStart;
  const endDate = isNextPeriod ? unit.nextPeriodContractEnd || unit.contractEnd : unit.contractEnd;
  const nextPeriodAmounts = isNextPeriod ? nextPeriodContractAmountsFromUnit(unit) : null;
  const nextPeriodSource = isNextPeriod ? unit.nextPeriodAiSource || nextPeriodContractSourceName(unit) : "";
  const nextPeriodDraft = isNextPeriod
    ? unit.nextPeriodContractDraft || nextPeriodContractDraftText(unit, property, { start: startDate, end: endDate }, nextPeriodAmounts, unit.nextPeriodImportantChange)
    : "";
  const nextPeriodPdfDataUrl = isNextPeriod ? ensureNextPeriodContractPdf(unit, property, nextPeriodDraft) : "";
  els.modalEyebrow.textContent = isTemplate ? "Contrato machote" : isNextPeriod ? "Contrato de nuevo periodo" : "Contrato firmado";
  els.modalTitle.textContent = `${unit.unit} - ${unit.tenant}`;
  els.modalBody.innerHTML = `
    <div class="modal-grid">
      <div class="detail-box">
        <span>Propiedad</span>
        <strong>${property?.name || "Sin propiedad"}</strong>
      </div>
      <div class="detail-box">
        <span>${isTemplate ? "Machote" : "Documento"}</span>
        <strong>${escapeAttribute(documentName || "Sin archivo")}</strong>
      </div>
      ${isNextPeriod ? `
        <div class="detail-box">
          <span>PDF adjunto</span>
          <strong>${nextPeriodPdfDataUrl ? "Contrato PDF generado y adjunto" : "PDF pendiente"}</strong>
          <p>${nextPeriodPdfDataUrl ? "El boton Ver Contrato abre este archivo generado desde el machote." : "Vuelve a generar el contrato para crear el PDF."}</p>
        </div>
      ` : ""}
      ${isTemplate ? `
        <div class="detail-box">
          <span>Archivo adjunto</span>
          <strong>${templateAttachmentName ? escapeAttribute(templateAttachmentName) : "Sin archivo adjunto"}</strong>
        </div>
      ` : ""}
      ${isNextPeriod ? `
        <div class="detail-box">
          <span>Machote base PDF</span>
          <strong>${escapeAttribute(nextPeriodSource)}</strong>
        </div>
      ` : ""}
      <div class="detail-box">
        <span>Inicio</span>
        <strong>${formatDate(startDate)}</strong>
      </div>
      <div class="detail-box">
        <span>Fin</span>
        <strong>${formatDate(endDate)}</strong>
      </div>
      <div class="detail-box">
        <span>Renta mensual</span>
        <strong>${formatCurrency(isNextPeriod ? nextPeriodAmounts.rentTotal : unit.monthlyRent)}</strong>
      </div>
      ${isNextPeriod ? `
        <div class="detail-box">
          <span>Mantenimiento mensual</span>
          <strong>${formatCurrency(nextPeriodAmounts.maintenanceTotal)}</strong>
        </div>
      ` : ""}
      ${isNextPeriod ? `
        <div class="detail-box">
          <span>IA basica</span>
          <p>${escapeAttribute(unit.nextPeriodAiSummary || "Documento generado con datos del machote, arrendatario, unidad, vigencia e importes.")}</p>
        </div>
      ` : ""}
      ${isNextPeriod ? `
        <div class="detail-box">
          <span>Cambio capturado</span>
          <p>${escapeAttribute(unit.nextPeriodImportantChange || "Sin cambios adicionales registrados.")}</p>
        </div>
      ` : ""}
      <div class="detail-box">
        <span>Estado legal</span>
        <strong>${contractStatus(unit).label}</strong>
      </div>
    </div>
    <div class="detail-box" style="margin-top: 12px;">
      <span>Vista previa</span>
      <p>${contractPreviewText({ isTemplate, isNextPeriod, templateAttachmentName, documentName })}</p>
    </div>
    ${isNextPeriod ? `
      <div class="detail-box generated-contract-preview">
        <span>Contrato generado desde machote</span>
        <pre>${escapeAttribute(nextPeriodDraft)}</pre>
      </div>
      <div class="form-actions generated-contract-actions">
        <button class="action-button" type="button" data-open-generated-contract-pdf="${unit.id}" ${nextPeriodPdfDataUrl ? "" : "disabled"}>
          <span data-icon="eye" aria-hidden="true"></span>
          Abrir PDF generado
        </button>
        <a class="secondary-button generated-contract-download ${nextPeriodPdfDataUrl ? "" : "is-disabled"}" href="${nextPeriodPdfDataUrl || "#"}" download="${escapeAttribute(documentName || "contrato-nuevo-periodo.pdf")}" ${nextPeriodPdfDataUrl ? "" : "aria-disabled=\"true\""}>
          <span data-icon="download" aria-hidden="true"></span>
          Descargar PDF
        </a>
      </div>
    ` : ""}
  `;
  injectIcons(els.modalBody);
  els.modalBody.querySelector("[data-open-generated-contract-pdf]")?.addEventListener("click", () => openGeneratedContractPdf(unit.id));
  openModal();
}

function contractPreviewText({ isTemplate, isNextPeriod, templateAttachmentName, documentName }) {
  if (isTemplate) {
    return templateAttachmentName
      ? `Machote propuesto cargado: ${escapeAttribute(templateAttachmentName)}. En una version conectada, este boton abriria el archivo adjunto para revision antes de asignar arrendatario.`
      : "Machote base con clausulas de renta, mantenimiento, servicios, extraordinarios, publicidad, garantias y penalizaciones. Aun no hay archivo adjunto cargado.";
  }

  if (isNextPeriod) {
    return `Contrato de nuevo periodo generado: ${escapeAttribute(documentName || "Sin archivo")}. En una version conectada, este boton abriria el PDF generado para revision y firma.`;
  }

  return "Documento firmado asociado a la unidad. En una version conectada, este boton abriria el PDF o el repositorio documental.";
}

function userPropertyAccessSectionMarkup(properties, property) {
  if (!property) {
    return `
      <section id="propertyUsers" class="property-detail-section user-property-access-section">
        ${emptyState("Aun no hay plazas registradas para administrar usuarios.")}
      </section>
    `;
  }

  const accessUsers = propertyAccessUsers(property.id);
  return `
    <section id="propertyUsers" class="property-detail-section user-property-access-section">
      <div class="section-header">
        <div>
          <p class="eyebrow">Usuarios por plaza</p>
          <h3>Usuarios vinculados</h3>
          <p class="muted">${property.name} - ${property.type}, ${property.location}</p>
        </div>
        <div class="user-property-access-controls">
          <div class="field user-property-access-select">
            <label for="userPropertyAccessSelect">Plaza</label>
            <select id="userPropertyAccessSelect" data-user-property-select>
              ${properties.map((item) => `
                <option value="${item.id}" ${item.id === property.id ? "selected" : ""}>${item.name}</option>
              `).join("")}
            </select>
          </div>
          <button class="secondary-button" type="button" data-property-team="${property.id}">
            <span data-icon="users" aria-hidden="true"></span>
            Administrar usuarios
          </button>
        </div>
      </div>
      ${accessUsers.length ? `
        <div class="access-list property-access-list">
          ${accessUsers.map((user) => `<span class="access-chip">${user.name} - ${roleNames[user.role] || user.role}</span>`).join("")}
        </div>
      ` : emptyState("Aun no hay usuarios asignados a esta plaza.")}
    </section>
  `;
}

function renderUserNew() {
  if (view.roleId !== "superadmin") {
    els.contentArea.innerHTML = emptyState("Este modulo solo esta disponible para Superadministrador.");
    return;
  }

  const properties = visibleProperties();
  const selectedProperty = properties.find((property) => property.id === view.propertyDetailId) || properties[0] || null;
  if (selectedProperty) {
    view.propertyDetailId = selectedProperty.id;
    view.propertyFilter = selectedProperty.id;
  }

  els.contentArea.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">Usuarios</p>
        <h3>Alta de Usuarios</h3>
        <p class="muted">Crea usuarios del sistema y asigna propiedades cuando el rol sea Gerente de Propiedad o Contabilidad local.</p>
      </div>
    </div>

    ${userPropertyAccessSectionMarkup(properties, selectedProperty)}

    <div class="user-module-layout">
      <section class="form-panel">
        <form id="platformUserForm">
          <div class="form-grid">
            <div class="field span-2">
              <label for="platformUserName">Nombre completo</label>
              <input id="platformUserName" name="name" required placeholder="Nombre del usuario">
            </div>
            <div class="field span-2">
              <label for="platformUserEmail">Correo</label>
              <input id="platformUserEmail" name="email" type="email" required placeholder="correo@empresa.com">
            </div>
            <div class="field">
              <label for="platformUserRole">Rol</label>
              <select id="platformUserRole" name="role">
                ${userCreationRoleOptions()}
              </select>
            </div>
            <div class="field">
              <label for="platformUsername">Usuario de acceso</label>
              <input id="platformUsername" name="username" placeholder="Se genera automaticamente">
            </div>
            <div id="platformPropertyAccess" class="field span-2" hidden>
              <label>Propiedades asignadas</label>
              <div class="checkbox-list user-property-list">
                ${state.properties.map((property) => `
                  <label class="checkbox-row">
                    <input type="checkbox" name="propertyIds" value="${property.id}">
                    <span>${property.name} - ${property.type}</span>
                  </label>
                `).join("")}
              </div>
            </div>
          </div>
          <div class="form-actions">
            <button class="secondary-button" type="reset">
              <span data-icon="x" aria-hidden="true"></span>
              Limpiar
            </button>
            <button class="action-button" type="submit">
              <span data-icon="shield" aria-hidden="true"></span>
              Crear usuario
            </button>
          </div>
        </form>
      </section>

      <section class="table-panel">
        <div class="month-panel-header">
          <div>
            <p class="eyebrow">Usuarios generados</p>
            <h3>Cuentas activas</h3>
            <p class="muted">Credenciales demo y propiedades visibles por usuario.</p>
          </div>
        </div>
        <div class="table-scroll">
          <table class="user-admin-table">
            <thead>
              <tr>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Acceso</th>
                <th>Contrasena</th>
                <th>Propiedades</th>
              </tr>
            </thead>
            <tbody>
              ${state.users.map((user) => userCreationRowMarkup(user)).join("")}
            </tbody>
          </table>
        </div>
      </section>
    </div>
  `;

  const roleSelect = els.contentArea.querySelector("#platformUserRole");
  const form = els.contentArea.querySelector("#platformUserForm");
  const syncAccess = () => syncUserPropertyAccessVisibility(roleSelect.value);
  const propertyAccessSelect = els.contentArea.querySelector("[data-user-property-select]");

  roleSelect?.addEventListener("change", syncAccess);
  form?.addEventListener("reset", () => window.setTimeout(syncAccess, 0));
  form?.addEventListener("submit", createPlatformUserFromForm);
  propertyAccessSelect?.addEventListener("change", () => {
    const propertyId = propertyAccessSelect.value;
    if (!getProperty(propertyId)) return;
    view.propertyDetailId = propertyId;
    view.propertyFilter = propertyId;
    render();
  });
  els.contentArea.querySelector("[data-property-team]")?.addEventListener("click", () => {
    if (selectedProperty) openPropertyTeamModal(selectedProperty.id);
  });
  syncAccess();
}

function userCreationRoleOptions() {
  const allowedRoles = ["admin", "project_manager", "local_accounting", "general_accounting", "legal", "superadmin"];
  return roles
    .filter((role) => allowedRoles.includes(role.id))
    .map((role) => `<option value="${role.id}">${role.name}</option>`)
    .join("");
}

function roleRequiresPropertyAccess(roleId) {
  return ["project_manager", "local_accounting"].includes(roleId);
}

function syncUserPropertyAccessVisibility(roleId) {
  const propertyBlock = els.contentArea.querySelector("#platformPropertyAccess");
  if (!propertyBlock) return;
  propertyBlock.hidden = !roleRequiresPropertyAccess(roleId);
}

function createPlatformUserFromForm(event) {
  event.preventDefault();
  const form = event.currentTarget;
  const data = new FormData(form);
  const name = String(data.get("name") || "").trim();
  const email = String(data.get("email") || "").trim().toLowerCase();
  const role = String(data.get("role") || "admin");
  const requestedUsername = String(data.get("username") || "").trim();
  const propertyIds = [...form.querySelectorAll("input[name='propertyIds']:checked")].map((input) => input.value);

  if (!name || !email) {
    toast("Captura nombre y correo.");
    return;
  }

  if (state.users.some((user) => normalizeText(user.email) === normalizeText(email))) {
    toast("Ya existe un usuario con ese correo.");
    return;
  }

  if (requestedUsername && state.users.some((user) => normalizeText(user.username) === normalizeText(requestedUsername))) {
    toast("Ya existe ese usuario de acceso.");
    return;
  }

  if (roleRequiresPropertyAccess(role) && propertyIds.length === 0) {
    toast("Selecciona al menos una propiedad.");
    return;
  }

  const userId = uniqueId(userIdPrefixForRole(role), name, state.users.map((user) => user.id));
  const usernameBase = requestedUsername || defaultUsername({ name, email, id: userId });
  const user = {
    id: userId,
    name,
    email,
    username: uniqueUsername(usernameBase, state.users),
    password: DEMO_PASSWORD,
    role,
    propertyIds: roleRequiresPropertyAccess(role) ? propertyIds : []
  };

  state.users.push(user);
  assignUserToPropertyRole(user);
  saveState();
  renderCredentialsTable();
  render();
  toast("Usuario creado");
}

function userIdPrefixForRole(role) {
  const prefixes = {
    superadmin: "u-super",
    admin: "u-admin",
    project_manager: "u-gerente",
    local_accounting: "u-conta-local",
    general_accounting: "u-conta-general",
    legal: "u-legal"
  };
  return prefixes[role] || "u-usuario";
}

function assignUserToPropertyRole(user) {
  if (user.role === "project_manager") {
    user.propertyIds.forEach((propertyId) => {
      const property = getProperty(propertyId);
      if (property) property.managerUserId = user.id;
    });
  }

  if (user.role === "local_accounting") {
    user.propertyIds.forEach((propertyId) => {
      const property = getProperty(propertyId);
      if (property) property.localAccountingUserId = user.id;
    });
  }
}

function userCreationRowMarkup(user) {
  return `
    <tr>
      <td class="primary-cell">
        <strong>${user.name}</strong>
        <small>${user.email}</small>
      </td>
      <td><span class="role-badge">${roleNames[user.role] || user.role}</span></td>
      <td><code>${user.username}</code></td>
      <td><code>${user.password}</code></td>
      <td>
        <div class="access-list">
          ${assignedPropertyLabels(user).map((label) => `<span class="access-chip">${label}</span>`).join("")}
        </div>
      </td>
    </tr>
  `;
}

function renderUsers() {
  if (!canManageAccess()) {
    els.contentArea.innerHTML = emptyState("Este panel solo esta disponible para administrador y superadministrador.");
    return;
  }

  els.contentArea.innerHTML = `
    <div class="section-header">
      <div>
        <p class="eyebrow">Administracion</p>
        <h3>Roles y accesos</h3>
        <p class="muted">El administrador designa el rol de cada persona y las propiedades asignadas.</p>
      </div>
    </div>
    <div class="table-panel">
      <div class="table-scroll">
        <table>
          <thead>
            <tr>
              <th>Usuario</th>
              <th>Rol</th>
              <th>Usuario acceso</th>
              <th>Contrasena</th>
              <th>Correo</th>
              <th>Propiedades asignadas</th>
              <th>Accesos</th>
            </tr>
          </thead>
          <tbody>
            ${state.users.map((user) => userRowMarkup(user)).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;

  els.contentArea.querySelectorAll("[data-role-change]").forEach((select) => {
    select.addEventListener("change", () => {
      const user = getUser(select.dataset.userId);
      if (!user) return;
      user.role = select.value;
      if (canSeeEveryProperty(user.role) || user.role === "tenant") {
        user.propertyIds = [];
      }
      saveState();
      renderRoleOptions();
      render();
      toast("Rol actualizado");
    });
  });

  els.contentArea.querySelectorAll("[data-access-edit]").forEach((button) => {
    button.addEventListener("click", () => openAccessModal(button.dataset.userId));
  });
}

function userRowMarkup(user) {
  const assigned = assignedPropertyLabels(user);
  const disabled = user.role === "tenant" || canSeeEveryProperty(user.role);
  const roleLocked = user.id === PANEL_USER_ID;

  return `
    <tr>
      <td class="primary-cell">
        <strong>${user.name}</strong>
        <small>${user.id}</small>
      </td>
      <td>
        <select data-role-change="${user.id}" data-user-id="${user.id}" ${roleLocked ? "disabled" : ""}>
          ${roles.map((role) => `<option value="${role.id}" ${role.id === user.role ? "selected" : ""}>${role.name}</option>`).join("")}
        </select>
      </td>
      <td><code>${user.username}</code></td>
      <td><code>${user.password}</code></td>
      <td>${user.email}</td>
      <td>
        <div class="access-list">
          ${assigned.map((label) => `<span class="access-chip">${label}</span>`).join("")}
        </div>
      </td>
      <td>
        <button class="secondary-button" type="button" ${disabled ? "disabled" : ""} data-access-edit="${user.id}">
          <span data-icon="shield" aria-hidden="true"></span>
          Editar accesos
        </button>
      </td>
    </tr>
  `;
}

function assignedPropertyLabels(user) {
  if (canSeeEveryProperty(user.role)) return ["Todas"];
  if (user.role === "tenant") {
    const unitProperties = state.units
      .filter((unit) => unit.tenantUserId === user.id)
      .map((unit) => getProperty(unit.propertyId)?.name)
      .filter(Boolean);
    return unitProperties.length ? [...new Set(unitProperties)] : ["Por unidad arrendada"];
  }

  const labels = user.propertyIds.map((propertyId) => getProperty(propertyId)?.name).filter(Boolean);
  return labels.length ? labels : ["Sin asignacion"];
}

function openAccessModal(userId) {
  const user = getUser(userId);
  if (!user) return;

  els.modalEyebrow.textContent = "Asignacion de propiedades";
  els.modalTitle.textContent = user.name;
  els.modalBody.innerHTML = `
    <div class="checkbox-list">
      ${state.properties.map((property) => `
        <label class="checkbox-row">
          <input type="checkbox" value="${property.id}" ${user.propertyIds.includes(property.id) ? "checked" : ""}>
          <span>${property.name} - ${property.type}</span>
        </label>
      `).join("")}
    </div>
    <div class="section-actions" style="margin-top: 16px;">
      <button class="action-button" type="button" id="saveAccessButton">
        <span data-icon="shield" aria-hidden="true"></span>
        Guardar accesos
      </button>
    </div>
  `;

  injectIcons(els.modalBody);
  els.modalBody.querySelector("#saveAccessButton").addEventListener("click", () => {
    user.propertyIds = [...els.modalBody.querySelectorAll("input:checked")].map((input) => input.value);
    saveState();
    closeModal();
    render();
    toast("Accesos actualizados");
  });

  openModal();
}

function tenantEmail(unit) {
  if (!unit.tenantUserId) return "Sin usuario de plataforma";
  return getUser(unit.tenantUserId)?.email || "Sin correo";
}

function emptyState(message) {
  return `
    <div class="empty-state">
      <p class="eyebrow">Sin resultados</p>
      <h3>${message}</h3>
    </div>
  `;
}

function openModal() {
  els.modalBackdrop.hidden = false;
}

function closeModal() {
  els.modalBackdrop.hidden = true;
  els.modal?.classList.remove("modal-wide", "modal-compact", "modal-plaza-flow");
}

function toast(message) {
  document.querySelector(".toast")?.remove();
  const node = document.createElement("div");
  node.className = "toast";
  node.textContent = message;
  document.body.appendChild(node);
  window.setTimeout(() => node.remove(), 2600);
}

function resetDemoData() {
  localStorage.removeItem(STORAGE_KEY);
  Object.assign(state, normalizeState(structuredClone(seedState)));
  renderRoleOptions();
  render();
  toast("Datos demo restaurados");
}

init();
