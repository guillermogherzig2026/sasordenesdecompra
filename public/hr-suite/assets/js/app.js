import { createInitialData, menuItems, roles, routeTitles, screenGroups } from "./data.js";

const STORAGE_KEY = "hr-suite-mexico-state-v1";
const app = document.querySelector("#app");
const toastRegion = document.querySelector("#toast-region");
const suiteConfig = window.HR_SUITE_CONFIG || {};
const isEmbedded = suiteConfig.embedded === true;

const icons = {
  "layout-dashboard": '<path d="M3 13h8V3H3v10Z"/><path d="M13 21h8V11h-8v10Z"/><path d="M13 3v6h8V3h-8Z"/><path d="M3 21h8v-6H3v6Z"/>',
  users: '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
  "file-signature": '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M16 13c-1.3 2.2-2.7 3.3-4 3.3-.9 0-1.6-.5-2-1.5-.4-1-1.1-1.5-2-1.5"/><path d="M8 18h8"/>',
  calculator: '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M8 6h8"/><path d="M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>',
  "calendar-alert": '<path d="M8 2v4M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="M12 14v3"/><path d="M12 20h.01"/>',
  scale: '<path d="m16 16 3-8 3 8c-.8 1.2-1.8 2-3 2s-2.2-.8-3-2Z"/><path d="m2 16 3-8 3 8c-.8 1.2-1.8 2-3 2s-2.2-.8-3-2Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 8h18"/>',
  wallet: '<path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3v4a1 1 0 0 1-1 1H5a2 2 0 0 1-2-2V5"/><path d="M18 12h.01"/>',
  receipt: '<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z"/><path d="M8 7h8M8 11h8M8 15h5"/>',
  "calendar-days": '<path d="M8 2v4M16 2v4"/><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/><path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01"/>',
  clock: '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
  chart: '<path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/><path d="M15 9h4v4"/>',
  settings: '<path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.87l.05.05a2 2 0 1 1-2.83 2.83l-.05-.05A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6l-.02.05a2 2 0 0 1-3.96 0L10 20a1.7 1.7 0 0 0-1-.6 1.7 1.7 0 0 0-1.87.34l-.05.05a2 2 0 1 1-2.83-2.83l.05-.05A1.7 1.7 0 0 0 4.6 15a1.7 1.7 0 0 0-.6-1l-.05-.02a2 2 0 0 1 0-3.96L4 10a1.7 1.7 0 0 0 .6-1 1.7 1.7 0 0 0-.34-1.87l-.05-.05a2 2 0 1 1 2.83-2.83l.05.05A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-.6l.02-.05a2 2 0 0 1 3.96 0L14 4a1.7 1.7 0 0 0 1 .6 1.7 1.7 0 0 0 1.87-.34l.05-.05a2 2 0 1 1 2.83 2.83l-.05.05A1.7 1.7 0 0 0 19.4 9c.42.22.77.55 1 1l.05.02a2 2 0 0 1 0 3.96L20.4 14c-.23.45-.58.78-1 1Z"/>',
  "badge-user": '<path d="M3 10a7 7 0 0 1 14 0v4a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4v-4Z"/><path d="M21 12v7a2 2 0 0 1-2 2h-2"/><circle cx="10" cy="9" r="2"/><path d="M6.5 15a4 4 0 0 1 7 0"/>',
  bell: '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
  logout: '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/>',
  menu: '<path d="M4 6h16M4 12h16M4 18h16"/>',
  plus: '<path d="M12 5v14M5 12h14"/>',
  minus: '<path d="M5 12h14"/>',
  check: '<path d="m20 6-11 11-5-5"/>',
  x: '<path d="M18 6 6 18M6 6l12 12"/>',
  edit: '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
  eye: '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
  download: '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/>',
  send: '<path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/>',
  upload: '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m17 8-5-5-5 5"/><path d="M12 3v12"/>',
  refresh: '<path d="M21 12a9 9 0 0 1-15.6 6"/><path d="M3 12A9 9 0 0 1 18.6 6"/><path d="M18 2v4h4"/><path d="M6 22v-4H2"/>',
  "arrow-left": '<path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>',
  "arrow-right": '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
  lock: '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
  percent: '<path d="M19 5 5 19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>',
  "circle-dollar": '<circle cx="12" cy="12" r="10"/><path d="M12 6v12"/><path d="M16 9a4 4 0 0 0-4-2H9.5a2.5 2.5 0 0 0 0 5H14a2.5 2.5 0 0 1 0 5h-2.5A4 4 0 0 1 8 15"/>',
  shield: '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/>',
  printer: '<path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/>',
  copy: '<rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/>',
  trash: '<path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/>',
  alert: '<path d="M10.3 3.6 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.6a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/>',
  filter: '<path d="M3 5h18"/><path d="M7 12h10"/><path d="M10 19h4"/>',
  file: '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h5"/>'
};

const contractSteps = [
  "Seleccionar persona",
  "Datos generales",
  "Condiciones laborales",
  "Condiciones económicas",
  "Cláusulas",
  "Vista previa",
  "Aprobaciones"
];

const payrollSteps = [
  "Crear periodo",
  "Seleccionar empresa",
  "Tipo de nómina",
  "Definir fechas",
  "Cargar empleados",
  "Importar incidencias",
  "Calcular percepciones",
  "Calcular deducciones",
  "Validar resultados",
  "Revisar excepciones",
  "Autorizar nómina",
  "Archivo de pago",
  "Registrar dispersión",
  "Generar recibos",
  "Cerrar periodo"
];

const employeeTabs = [
  "Resumen",
  "Información personal",
  "Información laboral",
  "Contratos",
  "Nómina",
  "Recibos",
  "Incidencias",
  "Vacaciones",
  "Documentos",
  "Historial",
  "Notas"
];

const portalTabs = ["Inicio", "Mis datos", "Mis contratos", "Mis recibos", "Mis vacaciones", "Mis incidencias", "Mis documentos", "Solicitudes"];
const tableState = {};
let state = loadState();

if (isEmbedded) {
  const requestedRoute = String(suiteConfig.route || "dashboard");
  state.ui.route = Object.prototype.hasOwnProperty.call(routeTitles, requestedRoute) ? requestedRoute : "dashboard";
  state.ui.role = "Superadministrador";
  state.ui.sidebarCollapsed = true;
}

function loadState() {
  const fallback = {
    data: createInitialData(),
    ui: defaultUi()
  };
  normalizeTemplates(fallback.data);
  normalizeContractDrafts(fallback.data);
  normalizeContractCandidateEmployees(fallback.data);
  normalizeNewHireContracts(fallback.data);
  normalizeConvertedCandidates(fallback.data);
  normalizeContractApprovals(fallback.data.contracts);
  normalizeDepartmentCatalog(fallback.data);
  normalizePayrollHistory(fallback.data);
  try {
    const saved = JSON.parse(localStorage.getItem(STORAGE_KEY));
    if (!saved?.data) return fallback;
    const ui = { ...defaultUi(), ...saved.ui, modal: null };
    if (ui.route === "employee-portal") ui.route = "dashboard";
    ui.contractStep = Math.min(Number(ui.contractStep) || 1, contractSteps.length);
    ui.contractPersonType = ui.contractPersonType === "employee" ? "employee" : "candidate";
    normalizeContractDraft(ui.contractDraft);
    const data = { ...fallback.data, ...saved.data, settings: { ...fallback.data.settings, ...(saved.data.settings || {}) } };
    normalizeContractDrafts(data);
    normalizeContractCandidateEmployees(data);
    normalizeNewHireContracts(data);
    normalizeConvertedCandidates(data);
    normalizeContractApprovals(data.contracts);
    normalizeDepartmentCatalog(data);
    normalizeTemplates(data);
    normalizePayrollHistory(data);
    return {
      data,
      ui
    };
  } catch {
    return fallback;
  }
}

function normalizeContractCandidateEmployees(data) {
  if (!Array.isArray(data.contractCandidateEmployees)) data.contractCandidateEmployees = [];
}

function normalizeConvertedCandidates(data) {
  if (!Array.isArray(data.candidates)) data.candidates = [];
  if (!Array.isArray(data.hiredCandidates)) data.hiredCandidates = [];

  const convertedCandidateIds = new Set(
    (data.employees || [])
      .map((employee) => Number(employee.sourceCandidateId))
      .filter(Number.isFinite)
  );

  data.candidates = data.candidates.filter((candidate) => {
    if (!convertedCandidateIds.has(Number(candidate.id))) return true;

    if (!data.hiredCandidates.some((item) => Number(item.id) === Number(candidate.id))) {
      data.hiredCandidates.unshift({
        ...candidate,
        status: "Contratado",
        convertedAt: candidate.convertedAt || candidate.lastUpdate || today()
      });
    }
    return false;
  });
}

function normalizeDepartmentCatalog(data) {
  const requiredDepartments = [
    "Recursos Humanos",
    "Finanzas",
    "Tecnología",
    "Operaciones",
    "Comercial",
    "Sucursales",
    "Ventas",
    "Compras",
    "Almacen",
    "Seguridad",
    "Administración",
    "Contabilidad",
    "Jurídico",
    "Logística",
    "Mantenimiento"
  ];
  data.departments = Array.from(new Set([...(data.departments || []), ...requiredDepartments]));
}

function normalizePayrollHistory(data) {
  if (!Array.isArray(data.payrollHistory)) data.payrollHistory = [];
  if (!Array.isArray(data.payrollPendingTables)) data.payrollPendingTables = [];
}

function normalizeContractDraft(draft) {
  if (!draft) return;
  draft.bonusesNotApplicable = draft.bonusesNotApplicable === true || draft.bonusesNotApplicable === "true" || draft.bonusesNotApplicable === 1 || draft.bonusesNotApplicable === "1";
  if (draft.bonusesNotApplicable) draft.bonuses = 0;
}

function normalizeContractDrafts(data) {
  if (!Array.isArray(data.contractDrafts)) data.contractDrafts = [];
  data.contractDrafts.forEach((process) => normalizeContractDraft(process.draft || process));
}

function normalizeNewHireContracts(data) {
  const toBool = (value) => value === true || value === "true" || value === 1 || value === "1";
  (data.employees || []).forEach((employee) => {
    employee.newHire = toBool(employee.newHire);
  });
  (data.contracts || []).forEach((contract) => {
    contract.isNewHire = toBool(contract.isNewHire);
  });
  (data.contractDrafts || []).forEach((process) => {
    if (process.draft) process.draft.newHire = toBool(process.draft.newHire);
  });
}

function normalizeContractApprovals(contracts = []) {
  contracts.forEach((contract) => {
    const approvals = Array.isArray(contract.approvals) ? contract.approvals : [];
    const hr = approvals.find((step) => step.step === "Elaborado por Recursos Humanos") || approvals[0] || {};
    const legalDirection = approvals.find((step) => step.step === "Aprobación Dirección Jurídica")
      || approvals.find((step) => step.step === "Aprobación de Dirección")
      || approvals.find((step) => step.step === "Revisión legal")
      || {};
    contract.approvals = [
      {
        step: "Elaborado por Recursos Humanos",
        status: hr.status || "Aprobado",
        user: hr.user || "Recursos Humanos",
        date: hr.date || today(),
        comment: hr.comment || "Creado"
      },
      {
        step: "Aprobación Dirección Jurídica",
        status: legalDirection.status === "Aprobado" ? "Aprobado" : "Pendiente",
        user: legalDirection.status === "Aprobado" ? legalDirection.user || "Dirección Jurídica" : "",
        date: legalDirection.status === "Aprobado" ? legalDirection.date || today() : "",
        comment: legalDirection.status === "Aprobado" ? legalDirection.comment || "Aprobación jurídica registrada" : ""
      }
    ];
  });
}

function uploadedContractTemplates() {
  return [
    {
      name: "Contrato por Tiempo Indeterminado",
      aliases: ["Contrato por tiempo indeterminado"],
      type: "Laboral",
      version: "v1.0",
      status: "Activo",
      updatedAt: "2026-07-29",
      sourceFileName: "Contrato por Tiempo Indeterminado.docx",
      sourceUrl: "./assets/docs/Contrato%20por%20Tiempo%20Indeterminado.docx",
      body: [
        "CONTRATO",
        "A.- DECLARA LA CONTRATANTE: SER UNA EMPRESA MEXICANA, LEGALMENTE CONSTITUIDA Y QUE SU REPRESENTANTE GOZA DE TODAS LAS FACULTADES PARA CELEBRAR EL PRESENTE CONTRATO.",
        "B.- DECLARA EL CONTRATADO: LLAMARSE ________________________________ CON ______ AÑOS DE EDAD, SEXO __________, ESTADO CIVIL __________, NACIONALIDAD __________________, CLAVE ÚNICA DE REGISTRO DE POBLACIÓN ______________________________, REGISTRO FEDERAL DE CONTRIBUYENTES ______________________________ Y CON DOMICILIO UBICADO EN ____________________________________________________________.",
        "C L A U S U L A S",
        "PRIMERA.- OBJETO DEL CONTRATO. EL CONTRATADO PRESTARA SUS SERVICIOS COMO EMPLEADO EN EL PUESTO DE ________________________________________, REALIZANDO TODA ACTIVIDAD NECESARIA PARA LLEVAR A BUEN TERMINO EL TRABAJO ENCOMENDADO.",
        "SEGUNDA.- EL PRESENTE CONTRATO SE CELEBRA POR TIEMPO INDETERMINADO.",
        "TERCERA.- LA DURACIÓN DE LA JORNADA SEMANARIA DE TRABAJO SERÁ DE CUARENTA Y OCHO HORAS. EL HORARIO DIARIO DE TRABAJO SERÁ: ________________________________________________.",
        "CUARTA.- LOS DÍAS DE DESCANSO OBLIGATORIOS Y VACACIONES SERÁN LOS ESTIPULADOS POR LA LEY FEDERAL DEL TRABAJO.",
        "SEXTA.- EL TRABAJADOR PERCIBIRÁ COMO SALARIO LA CANTIDAD DE $______________ MENSUALES, PAGADEROS DE FORMA QUINCENAL.",
        "DÉCIMA.- PARA LA INTERPRETACIÓN Y CUMPLIMIENTO DEL PRESENTE CONTRATO LAS PARTES SE SOMETEN A LA LEY FEDERAL DEL TRABAJO Y A LOS TRIBUNALES LABORALES COMPETENTES."
      ].join("\n"),
      clauses: [
        { name: "Declaraciones", active: true },
        { name: "Objeto del contrato", active: true },
        { name: "Tiempo indeterminado", active: true },
        { name: "Jornada y horario", active: true },
        { name: "Salario", active: true },
        { name: "Terminación", active: true }
      ]
    },
    {
      name: "Contrato por Tiempo Determinado",
      aliases: ["Contrato por tiempo determinado"],
      type: "Laboral",
      version: "v1.0",
      status: "Activo",
      updatedAt: "2026-07-29",
      sourceFileName: "Contrato por Tiempo Determinado.docx",
      sourceUrl: "./assets/docs/Contrato%20por%20Tiempo%20Determinado.docx",
      body: [
        "CONTRATO",
        "A.- DECLARA LA CONTRATANTE: SER UNA EMPRESA MEXICANA, LEGALMENTE CONSTITUIDA Y QUE SU REPRESENTANTE GOZA DE TODAS LAS FACULTADES PARA CELEBRAR EL PRESENTE CONTRATO.",
        "B.- DECLARA EL CONTRATADO: LLAMARSE ________________________________ CON ______ AÑOS DE EDAD, SEXO __________, ESTADO CIVIL __________, NACIONALIDAD __________________, CURP ______________________________, RFC ______________________________ Y DOMICILIO UBICADO EN ____________________________________________________________.",
        "C L A U S U L A S",
        "PRIMERA.- OBJETO DEL CONTRATO. EL CONTRATADO PRESTARA SUS SERVICIOS COMO EMPLEADO EN EL PUESTO DE ________________________________________.",
        "SEGUNDA.- EL PRESENTE CONTRATO SE CELEBRA POR ______ DÍAS, CONTADOS A PARTIR DEL ____ DE __________________ DE ______.",
        "TERCERA.- LA DURACIÓN DE LA JORNADA SEMANARIA DE TRABAJO SERÁ DE CUARENTA Y OCHO HORAS. EL HORARIO DIARIO DE TRABAJO SERÁ: ________________________________________________.",
        "CUARTA.- LOS DÍAS DE DESCANSO OBLIGATORIOS Y VACACIONES SERÁN LOS ESTIPULADOS POR LA LEY FEDERAL DEL TRABAJO.",
        "SEXTA.- EL TRABAJADOR PERCIBIRÁ COMO SALARIO LA CANTIDAD DE $______________ MENSUALES, PAGADEROS DE FORMA QUINCENAL.",
        "DÉCIMA.- LAS PARTES DECLARAN HABER LEÍDO EL CONTENIDO DEL PRESENTE CONTRATO, CONOCER SU ALCANCE Y MANIFESTAR SU CONFORMIDAD."
      ].join("\n"),
      clauses: [
        { name: "Declaraciones", active: true },
        { name: "Objeto del contrato", active: true },
        { name: "Vigencia determinada", active: true },
        { name: "Jornada y horario", active: true },
        { name: "Salario", active: true },
        { name: "Firma de conformidad", active: true }
      ]
    },
    {
      name: "Convenio de Confidencialidad",
      type: "Legal",
      version: "v1.0",
      status: "Activo",
      updatedAt: "2026-07-29",
      sourceFileName: "Convenio de Confidencialidad.docx",
      sourceUrl: "./assets/docs/Convenio%20de%20Confidencialidad.docx",
      body: [
        "CIUDAD DE _________________, A ____ DE ______________ DE ______.",
        "A QUIEN CORRESPONDA",
        "Por medio de la presente YO, ______________________________________, manifiesto que es de mi conocimiento que toda la información a la que tengo acceso durante mi trabajo en la empresa es propiedad exclusivamente de ella.",
        "Dicha información se considera confidencial y constituye un secreto industrial en términos de la Ley de la Propiedad Industrial, por lo que no debo divulgarla, comunicarla, transmitirla, utilizarla en beneficio propio o de terceros, duplicarla, grabarla, copiarla o reproducirla.",
        "Cualquier información que esté en mi poder me comprometo a destruirla, aceptando que la violación o incumplimiento de la presente podrá generar responsabilidad por daños y perjuicios.",
        "FIRMA ______________________________________",
        "NOMBRE"
      ].join("\n"),
      clauses: [
        { name: "Información confidencial", active: true },
        { name: "Secreto industrial", active: true },
        { name: "No divulgación", active: true },
        { name: "No reproducción", active: true },
        { name: "Responsabilidad", active: true }
      ]
    }
  ];
}

function defaultContractTemplates() {
  return [
    {
      name: "Contrato por tiempo indeterminado",
      type: "Laboral",
      version: "v3.2",
      status: "Activo",
      updatedAt: "2026-07-11",
      body: "Entre {{Empresa}} y {{Nombre del empleado}}, se celebra contrato individual de trabajo por tiempo indeterminado para desempeñar el puesto de {{Puesto}} en el departamento de {{Departamento}}, con sueldo mensual de {{Sueldo}}, jornada {{Jornada}} y horario {{Horario}}.",
      clauses: [
        { name: "Confidencialidad", active: true },
        { name: "Propiedad intelectual", active: true },
        { name: "Protección de datos", active: true },
        { name: "Uso de herramientas", active: true },
        { name: "Terminación", active: true }
      ]
    },
    {
      name: "Contrato por tiempo determinado",
      type: "Laboral",
      version: "v2.1",
      status: "Activo",
      updatedAt: "2026-07-05",
      body: "Las partes acuerdan una relación laboral por tiempo determinado que iniciará el {{Fecha de ingreso}} y terminará el {{Fecha de vencimiento}}, para prestar servicios como {{Puesto}} en {{Centro de trabajo}}, con las prestaciones y condiciones pactadas.",
      clauses: [
        { name: "Vigencia", active: true },
        { name: "Funciones", active: true },
        { name: "Prestaciones", active: true },
        { name: "Confidencialidad", active: true },
        { name: "Cierre de contrato", active: true }
      ]
    },
    {
      name: "Periodo de prueba",
      type: "Laboral",
      version: "v1.4",
      status: "Activo",
      updatedAt: "2026-06-24",
      body: "El empleado ingresará en periodo de prueba para acreditar conocimientos, habilidades y desempeño en el puesto de {{Puesto}}. Al concluir el periodo, la empresa podrá confirmar la continuidad de la relación laboral.",
      clauses: [
        { name: "Evaluación", active: true },
        { name: "Duración", active: true },
        { name: "Indicadores", active: true },
        { name: "Terminación", active: true }
      ]
    },
    {
      name: "Convenio modificatorio",
      type: "Convenio",
      version: "v1.8",
      status: "Activo",
      updatedAt: "2026-06-30",
      body: "Las partes acuerdan modificar las condiciones del contrato vigente a partir de {{Fecha de firma}}, manteniendo la relación laboral y actualizando puesto, sueldo, jornada o centro de trabajo conforme a las nuevas condiciones autorizadas.",
      clauses: [
        { name: "Cambio de sueldo", active: true },
        { name: "Cambio de puesto", active: true },
        { name: "Ratificación de condiciones", active: true }
      ]
    },
    {
      name: "Convenio de terminación",
      type: "Terminación",
      version: "v1.2",
      status: "Activo",
      updatedAt: "2026-05-19",
      body: "La empresa y {{Nombre del empleado}} acuerdan concluir la relación laboral en la fecha pactada, dejando constancia de finiquito, devolución de herramientas, pagos pendientes y cierre del expediente.",
      clauses: [
        { name: "Fecha de baja", active: true },
        { name: "Finiquito", active: true },
        { name: "Devolución de activos", active: true },
        { name: "No adeudo", active: true }
      ]
    },
    {
      name: "Acuerdo de confidencialidad",
      type: "Legal",
      version: "v1.0",
      status: "Activo",
      updatedAt: "2026-05-02",
      body: "{{Nombre del empleado}} se obliga a proteger información confidencial, documentos, sistemas, datos personales, clientes, procesos internos y cualquier información reservada a la que tenga acceso durante la relación laboral.",
      clauses: [
        { name: "Información confidencial", active: true },
        { name: "Protección de datos", active: true },
        { name: "Vigencia posterior", active: true },
        { name: "Sanciones", active: true }
      ]
    }
  ];
}

function templateKey(value) {
  return normalizeText(value).trim();
}

function templateIdentityNames(template) {
  return [template?.name, ...(template?.aliases || [])].map(templateKey).filter(Boolean);
}

function seedTemplateDefinitions() {
  return [...uploadedContractTemplates(), ...defaultContractTemplates()];
}

function normalizeTemplates(data) {
  data.templates = Array.isArray(data.templates) ? data.templates : [];
  data.deletedTemplateNames = Array.isArray(data.deletedTemplateNames) ? data.deletedTemplateNames : [];
  const deletedTemplateNames = new Set(data.deletedTemplateNames.map(templateKey).filter(Boolean));
  const findExistingTemplate = (source) => {
    const names = templateIdentityNames(source);
    return data.templates.find((template) => names.includes(templateKey(template.name)));
  };
  let nextId = Math.max(0, ...data.templates.map((template) => Number(template.id) || 0)) + 1;
  seedTemplateDefinitions().forEach((template) => {
    if (templateIdentityNames(template).some((name) => deletedTemplateNames.has(name))) return;
    const { aliases = [], ...templateData } = template;
    const existing = findExistingTemplate(template);
    if (existing && template.sourceFileName) {
      Object.assign(existing, templateData);
    } else if (!existing) {
      data.templates.push({ id: nextId, ...templateData });
      nextId += 1;
    }
  });
  data.templates = data.templates.map((template, index) => ({
    id: Number(template.id) || index + 1,
    name: template.name || `Plantilla ${index + 1}`,
    type: template.type || (String(template.name || "").includes("Convenio") ? "Convenio" : "Laboral"),
    version: template.version || "v1.0",
    status: template.status || "Activo",
    updatedAt: template.updatedAt || "2026-07-20",
    body: template.body || "Texto base de contrato pendiente de configuración.",
    sourceFileName: template.sourceFileName || "",
    sourceUrl: template.sourceUrl || "",
    clauses: Array.isArray(template.clauses) && template.clauses.length
      ? template.clauses.map((clause) => typeof clause === "string" ? { name: clause, active: true } : { name: clause.name || "Cláusula", active: clause.active !== false })
      : [{ name: "Confidencialidad", active: true }]
  }));
  data.deletedTemplateNames = [...deletedTemplateNames];
}

function defaultUi() {
  return {
    route: "login",
    role: "Superadministrador",
    company: "NovaTalento Operadora",
    sidebarCollapsed: false,
    selectedEmployeeId: 1,
    selectedContractId: 1,
    selectedDueContractId: null,
    selectedReceiptId: 1,
    selectedPayrollEmployeeId: 1,
    employeeTab: "Resumen",
    portalTab: "Inicio",
    employeeFilterOpen: false,
    employeeListOpen: false,
    employeeNameFilter: [],
    contractPersonType: "candidate",
    contractEmployeeNewHireOnly: false,
    tableColumnFilterOpen: null,
    candidateFormOpen: false,
    candidateEditingId: null,
    vacationStatusOpen: null,
    selectedTemplateId: 1,
    templateEditing: false,
    contractAlertsOpen: false,
    payrollSummaryCollapsed: false,
    payrollSummaryCollapsedByPeriod: {},
    contractStep: 1,
    payrollStep: 9,
    contractDraft: {
      employeeId: 1,
      candidateId: null,
      type: "Tiempo indeterminado",
      folio: "CNT-2026-NUEVO",
      company: "NovaTalento Operadora",
      legalRep: "Mónica Salcedo Herrera",
      signingPlace: "Ciudad de México",
      signDate: "2026-07-20",
      startDate: "2026-08-01",
      endDate: "",
      trialPeriod: "No aplica",
      position: "Analista de Nómina",
      department: "Recursos Humanos",
      salary: 32000,
      frequency: "Quincenal",
      bonuses: 1200,
      bonusesNotApplicable: false,
      bonusCondition: "Pago sujeto a cumplimiento de metas mensuales",
      commissions: "Variable por cumplimiento",
      commissionCondition: "Comisión variable conforme a resultados autorizados",
      newHire: false,
      clauses: ["Confidencialidad", "Propiedad intelectual", "Protección de datos", "Terminación"]
    },
    modal: null
  };
}

function saveState() {
  const cleanUi = { ...state.ui, modal: null, vacationStatusOpen: null };
  localStorage.setItem(STORAGE_KEY, JSON.stringify({ data: state.data, ui: cleanUi }));
}

function updateContractDraftField(draftField) {
  const key = draftField.dataset.draft;
  const value = draftField.type === "checkbox" ? draftField.checked : draftField.value;
  state.ui.contractDraft[key] = value;
  if (key === "bonusesNotApplicable") {
    state.ui.contractDraft.bonusesNotApplicable = Boolean(value);
    if (value) state.ui.contractDraft.bonuses = 0;
    saveState();
    render();
    return;
  }
  saveState();
}

function safe(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function normalizeText(value) {
  return String(value || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

function icon(name, className = "") {
  return `<span class="${className}" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">${icons[name] || icons.file}</svg></span>`;
}

function money(value) {
  return new Intl.NumberFormat("es-MX", { style: "currency", currency: "MXN", maximumFractionDigits: 2 }).format(Number(value || 0));
}

function number(value) {
  return new Intl.NumberFormat("es-MX").format(Number(value || 0));
}

function date(value) {
  if (!value) return "Sin fecha";
  const parsed = new Date(`${value}T12:00:00`);
  return new Intl.DateTimeFormat("es-MX", { day: "2-digit", month: "short", year: "numeric" }).format(parsed);
}

function plainText(value) {
  const node = document.createElement("div");
  node.innerHTML = String(value ?? "");
  return node.textContent || node.innerText || "";
}

function columnFilterValue(row, column) {
  const value = column.filterValue
    ? column.filterValue(row)
    : Object.prototype.hasOwnProperty.call(row, column.key)
      ? row[column.key]
      : column.render
        ? column.render(row)
        : column.sortValue
          ? column.sortValue(row)
          : "";
  if (Array.isArray(value)) return value.join(", ");
  if (typeof value === "object" && value !== null) return JSON.stringify(value);
  return String(value ?? "");
}

function columnFilterOptions(rows, column) {
  return [...new Set(rows.map((row) => columnFilterValue(row, column)).map((value) => plainText(value).trim() || "Sin dato"))]
    .sort((a, b) => a.localeCompare(b, "es", { numeric: true }));
}

const monthNames = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];

function isIsoDateValue(value) {
  return /^\d{4}-\d{2}-\d{2}$/.test(String(value || ""));
}

function isDateFilterColumn(column, options) {
  if (column.filterType === "date-tree") return true;
  const label = normalizeText(`${column.key || ""} ${column.label || ""}`);
  const dateLikeLabel = /date|fecha|ingreso|inicio|termino|vencimiento|registro|pago|emision|actualizacion|baja|alta/.test(label);
  return dateLikeLabel && options.some(isIsoDateValue);
}

function renderDateFilterTree(options, activeSet, allChecked) {
  const dateOptions = options.filter(isIsoDateValue).sort();
  const otherOptions = options.filter((option) => !isIsoDateValue(option));
  const years = {};
  dateOptions.forEach((option) => {
    const [year, month, day] = option.split("-");
    years[year] = years[year] || {};
    years[year][month] = years[year][month] || [];
    years[year][month].push({ day, value: option });
  });

  const isChecked = (value) => allChecked || activeSet.has(value);
  const groupChecked = (values) => values.every(isChecked);
  const yearKeys = Object.keys(years).sort((a, b) => Number(b) - Number(a));

  return `
    <span class="date-filter-tree">
      ${yearKeys.map((year, yearIndex) => {
        const months = years[year];
        const yearValues = Object.values(months).flat().map((item) => item.value);
        return `
          <details class="date-filter-node date-filter-year" data-date-filter-node data-search="${safe(year)}" ${yearIndex === 0 ? "open" : ""}>
            <summary>
              <input type="checkbox" data-date-filter-group ${groupChecked(yearValues) ? "checked" : ""} />
              <span>${safe(year)}</span>
            </summary>
            <span class="date-filter-children">
              ${Object.keys(months).sort().map((month, monthIndex) => {
                const items = months[month].sort((a, b) => Number(a.day) - Number(b.day));
                const monthValues = items.map((item) => item.value);
                const monthLabel = monthNames[Number(month) - 1] || month;
                return `
                  <details class="date-filter-node date-filter-month" data-date-filter-node data-search="${safe(`${year} ${monthLabel}`)}" ${yearIndex === 0 && monthIndex === 0 ? "open" : ""}>
                    <summary>
                      <input type="checkbox" data-date-filter-group ${groupChecked(monthValues) ? "checked" : ""} />
                      <span>${safe(monthLabel)}</span>
                    </summary>
                    <span class="date-filter-children">
                      ${items.map((item) => `
                        <label class="excel-filter-option date-filter-day" data-excel-filter-option data-search="${safe(`${item.value} ${year} ${monthLabel} ${item.day}`)}">
                          <input type="checkbox" data-excel-filter-value value="${safe(item.value)}" ${isChecked(item.value) ? "checked" : ""} />
                          <span>${safe(item.day)}</span>
                        </label>
                      `).join("")}
                    </span>
                  </details>
                `;
              }).join("")}
            </span>
          </details>
        `;
      }).join("")}
      ${otherOptions.map((option) => `
        <label class="excel-filter-option" data-excel-filter-option data-search="${safe(option)}">
          <input type="checkbox" data-excel-filter-value value="${safe(option)}" ${isChecked(option) ? "checked" : ""} />
          <span>${safe(option)}</span>
        </label>
      `).join("")}
    </span>
  `;
}

function tableColumnFilterPanel(tableId, column, rows, current, direction = "left") {
  const key = String(column.key || "").toLowerCase();
  if (column.filterable === false || !column.key || key === "actions" || key.endsWith("action") || column.label === "Acciones") return "";
  const options = columnFilterOptions(rows, column);
  if (!options.length || (options.length === 1 && options[0] === "Sin dato")) return "";
  const open = state.ui.tableColumnFilterOpen?.table === tableId && state.ui.tableColumnFilterOpen?.key === column.key;
  const activeValues = current.columnFilters?.[column.key] || [];
  const activeSet = new Set(activeValues);
  const allChecked = !activeValues.length || activeValues.length === options.length;
  const isDateTree = isDateFilterColumn(column, options);
  const directionClass = direction === "right" ? "opens-right" : "opens-left";
  return `
    <span class="table-column-filter">
      <button class="table-filter-trigger ${activeValues.length ? "has-filter" : ""} ${open ? "is-active" : ""}" data-action="toggle-table-column-filter" data-table="${safe(tableId)}" data-key="${safe(column.key)}" aria-label="Filtrar ${safe(column.label)}">
        ${icon("filter")}
      </button>
      ${open ? `
        <span class="excel-filter-panel table-excel-filter-panel ${directionClass} ${isDateTree ? "date-filter-panel" : ""}" data-table-column-filter-panel data-table="${safe(tableId)}" data-key="${safe(column.key)}">
          <input class="input excel-filter-search" data-excel-filter-search placeholder="${isDateTree ? "Buscar (Todos)" : "Buscar"}" autocomplete="off" />
          <span class="excel-filter-list">
            <label class="excel-filter-option excel-filter-all">
              <input type="checkbox" data-excel-filter-select-all ${allChecked ? "checked" : ""} />
              <span>(Seleccionar todo)</span>
            </label>
            ${isDateTree ? renderDateFilterTree(options, activeSet, allChecked) : options.map((option) => {
                const checked = allChecked || activeSet.has(option);
                return `
                  <label class="excel-filter-option" data-excel-filter-option data-search="${safe(option)}">
                    <input type="checkbox" data-excel-filter-value value="${safe(option)}" ${checked ? "checked" : ""} />
                    <span>${safe(option)}</span>
                  </label>
                `;
              }).join("")}
          </span>
          <span class="excel-filter-actions">
            <button class="btn compact" data-action="apply-table-column-filter">ACEPTAR</button>
            <button class="btn secondary compact" data-action="cancel-table-column-filter">Cancelar</button>
          </span>
        </span>
      ` : ""}
    </span>
  `;
}

function applyColumnFilters(rows, columns, current) {
  const columnFilters = current.columnFilters || {};
  return rows.filter((row) => columns.every((column) => {
    const selected = columnFilters[column.key];
    if (!selected?.length) return true;
    const value = plainText(columnFilterValue(row, column)).trim() || "Sin dato";
    return selected.includes(value);
  }));
}

function updateDateFilterGroupStates(panel) {
  if (!panel) return;
  panel.querySelectorAll("[data-date-filter-group]").forEach((group) => {
    const node = group.closest("[data-date-filter-node]");
    const values = [...(node?.querySelectorAll("[data-excel-filter-value]") || [])];
    if (!values.length) return;
    const checkedCount = values.filter((checkbox) => checkbox.checked).length;
    group.checked = checkedCount === values.length;
    group.indeterminate = checkedCount > 0 && checkedCount < values.length;
  });
  const values = [...panel.querySelectorAll("[data-excel-filter-value]")];
  const selectAll = panel.querySelector("[data-excel-filter-select-all]");
  if (selectAll && values.length) {
    const checkedCount = values.filter((checkbox) => checkbox.checked).length;
    selectAll.checked = checkedCount === values.length;
    selectAll.indeterminate = checkedCount > 0 && checkedCount < values.length;
  }
}

function today() {
  return new Date().toISOString().slice(0, 10);
}

function statusClass(status) {
  const normalized = String(status || "").toLowerCase();
  if (["activo", "aprobado", "aprobada", "pagado", "pagada", "firmado", "firmada", "emitido", "publicado", "cerrada", "completo", "completado", "completada", "confirmado", "reprocesado"].some((word) => normalized.includes(word))) return "green";
  if (["pendiente", "revisión", "revision", "procesando", "generado", "próximo", "proximo", "advertencia", "por vencer", "preselección", "preseleccion", "entrevista", "nuevo"].some((word) => normalized.includes(word))) return "amber";
  if (["vencido", "rechazado", "rechazada", "crítica", "critica", "error", "baja", "cancelado", "suspendido"].some((word) => normalized.includes(word))) return "red";
  if (["informativa", "enviado", "oferta", "negociación", "negociacion"].some((word) => normalized.includes(word))) return "blue";
  return "gray";
}

function badge(value) {
  return `<span class="status ${statusClass(value)}">${safe(value)}</span>`;
}

function tag(value, tone = "blue") {
  const label = String(value || "").includes("mina aprobada") ? "Nomina aprobada" : value;
  return `<span class="tag ${tone}">${safe(label)}</span>`;
}

function statusButton(contract) {
  return `<button class="status-action" data-action="show-contract-process" data-id="${contract.id}" data-tooltip="Ver proceso">${badge(displayContractStatus(contract))}</button>`;
}

function findTemplateByName(name) {
  const target = String(name || "").toLowerCase();
  return state.data.templates.find((item) => String(item.name || "").toLowerCase() === target);
}

function contractModel(contract) {
  const inferredName = contract.type === "Tiempo indeterminado" ? "Contrato por tiempo indeterminado" : "Contrato por tiempo determinado";
  const template = findTemplateByName(contract.contractModelName) || findTemplateByName(inferredName) || state.data.templates[0];
  const name = template?.name || contract.contractModelName || inferredName;
  return {
    name,
    version: contract.contractModelVersion || template?.version || "v1.0",
    file: contract.contractModelFile || template?.sourceFileName || `${name}.docx`,
    attachedAt: contract.contractModelAttachedAt || contract.startDate || today()
  };
}

function contractModelCell(contract) {
  const model = contractModel(contract);
  return `
    <button class="model-attachment" data-action="open-contract-model" data-id="${contract.id}" data-tooltip="Adjuntar o actualizar formato">
      ${icon("file", "mini-icon")}
      <span>
        <strong>${safe(model.name)}</strong>
        <small>${safe(model.version)} · ${safe(model.file)}</small>
      </span>
    </button>
  `;
}

function signedContractFileName(contract) {
  if (contract.signedContractFile) return contract.signedContractFile;
  const isFullySigned = contract.employeeSignature === "Firmado" && contract.companySignature === "Firmado";
  return isFullySigned ? `Contrato firmado ${contract.folio}.pdf` : "";
}

function isLegalDirectionApproved(contract) {
  return Boolean((contract?.approvals || []).find((step) => step.step === "Aprobación Dirección Jurídica" && step.status === "Aprobado"));
}

function legalApprovalStep(contract) {
  return (contract?.approvals || []).find((step) => step.step === "Aprobación Dirección Jurídica");
}

function pendingLegalApprovalContracts() {
  return (state.data.contracts || []).filter((contract) => {
    const step = legalApprovalStep(contract);
    return step ? step.status !== "Aprobado" : displayContractStatus(contract) === "En aprobación";
  });
}

function blockContractOutputUntilLegalApproval(contract) {
  if (isLegalDirectionApproved(contract)) return false;
  toast("Dirección Jurídica debe aprobar el contrato antes de imprimirlo o pasarlo a firma.", "warning");
  return true;
}

function signedContractCell(contract) {
  const fileName = signedContractFileName(contract);
  const isAttached = Boolean(contract.signedContractFile);
  const isAvailable = Boolean(fileName);
  return `
    <button class="signed-contract-chip ${isAvailable ? "is-available" : ""}" data-action="view-signed-contract" data-contract-id="${contract.id}" data-tooltip="Ver PDF firmado y adjuntar documento">
      ${icon("file-signature", "mini-icon")}
      <span>
        <strong>${isAvailable ? "PDF firmado" : "Pendiente"}</strong>
        <small>${safe(isAvailable ? fileName : "RH debe adjuntar")}${isAvailable && !isAttached ? " · Simulado" : ""}</small>
      </span>
    </button>
  `;
}

function avatar(employee, size = "") {
  return `<span class="avatar ${size}" style="background:${safe(employee?.avatarColor || "#3157d5")}">${safe(employee?.initials || "RH")}</span>`;
}

function masked(value, visible = 4) {
  const text = String(value || "");
  if (text.length <= visible) return text;
  return `${"•".repeat(Math.max(0, text.length - visible))}${text.slice(-visible)}`;
}

function daysRemaining(endDate) {
  if (!endDate) return "Indefinido";
  const diff = Math.ceil((new Date(`${endDate}T12:00:00`) - new Date("2026-07-20T12:00:00")) / 86400000);
  if (diff < 0) return `${Math.abs(diff)} vencidos`;
  return `${diff} días`;
}

function employeeById(id) {
  return state.data.employees.find((employee) => String(employee.id) === String(id))
    || (state.data.contractCandidateEmployees || []).find((employee) => String(employee.id) === String(id))
    || state.data.employees[0];
}

function candidateById(id) {
  return (state.data.candidates || []).find((candidate) => Number(candidate.id) === Number(id)) || null;
}

function employeeNumberById(id) {
  const employee = employeeById(id);
  return employee?.number || `EMP-${String(id || 0).padStart(3, "0")}`;
}

function contractById(id) {
  return state.data.contracts.find((contract) => Number(contract.id) === Number(id)) || state.data.contracts[0];
}

function contractForEmployee(employeeId) {
  const employeeContracts = state.data.contracts.filter((contract) => Number(contract.employeeId) === Number(employeeId));
  return employeeContracts.find((contract) => ["Activo", "Pendiente de firma", "En aprobación", "En aprobaciÃ³n", "Próximo a vencer", "PrÃ³ximo a vencer", "Nuevo ingreso"].includes(contract.status)) || employeeContracts[0] || null;
}

function isNewHireContractPending(employeeOrId, contract = null) {
  const employee = typeof employeeOrId === "object" ? employeeOrId : employeeById(employeeOrId);
  const selectedContract = contract || (employee ? contractForEmployee(employee.id) : null);
  const flaggedAsNewHire = Boolean(employee?.newHire || selectedContract?.isNewHire);
  const isSigned = selectedContract?.employeeSignature === "Firmado";
  return flaggedAsNewHire && !isSigned;
}

function displayContractStatus(contract) {
  if (!contract) return "Sin Contrato";
  return isNewHireContractPending(contract.employeeId, contract) ? "Nuevo ingreso" : contract.status;
}

function displayEmployeeContractStatus(employee) {
  const contract = contractForEmployee(employee?.id);
  if (isNewHireContractPending(employee, contract)) return "Nuevo ingreso";
  return contract ? displayContractStatus(contract) : "Sin Contrato";
}

function receiptById(id) {
  return state.data.receipts.find((receipt) => Number(receipt.id) === Number(id)) || state.data.receipts[0];
}

function managersList() {
  return state.data.managers || [];
}

function activeBranchManager() {
  return managersList().find((manager) => manager.company === state.ui.company) || managersList()[0] || null;
}

function managerBranches(manager = activeBranchManager()) {
  return manager?.branches?.length ? manager.branches : [];
}

function branchEmployees(branchesToUse) {
  const allowedBranches = new Set(branchesToUse || []);
  return state.data.employees.filter((employee) => allowedBranches.has(employee.branch));
}

function addAudit(action, module, record, oldValue, newValue) {
  state.data.audit.unshift({
    id: state.data.audit.length + 1,
    user: state.ui.role,
    action,
    module,
    record,
    oldValue,
    newValue,
    date: today(),
    time: new Date().toLocaleTimeString("es-MX", { hour: "2-digit", minute: "2-digit" }),
    ip: `187.190.22.${Math.floor(20 + Math.random() * 70)}`
  });
}

function toast(message, type = "success") {
  const node = document.createElement("div");
  node.className = `toast ${type}`;
  node.innerHTML = `${icon(type === "error" ? "alert" : "check", "mini-icon")}<strong>${safe(message)}</strong>`;
  toastRegion.appendChild(node);
  setTimeout(() => node.remove(), 3400);
}

function navigate(route) {
  state.ui.route = route;
  if (route === "concepts") state.ui.route = "perceptions";
  if (route === "employees") state.ui.employeeTab = "Resumen";
  if (route === "employee-portal") state.ui.route = "dashboard";
  if (route !== "candidates") {
    state.ui.candidateFormOpen = false;
    state.ui.candidateEditingId = null;
  }
  saveState();
  render();
  window.scrollTo({ top: 0, behavior: "smooth" });
}

function activeFor(route) {
  if (state.ui.route === route) return true;
  return (screenGroups[route] || []).includes(state.ui.route);
}

function render() {
  if (!isEmbedded && state.ui.route === "login") {
    app.innerHTML = renderLogin();
    return;
  }

  app.innerHTML = isEmbedded
    ? `
    <div class="hr-embedded-app">
      <main class="main">
        <div class="content">
          ${renderRoute()}
        </div>
      </main>
      ${renderModal()}
    </div>
  `
    : `
    <div class="app-shell ${state.ui.sidebarCollapsed ? "is-collapsed" : ""}">
      ${renderSidebar()}
      <main class="main">
        <div class="content">
          ${renderRoute()}
        </div>
      </main>
      ${renderModal()}
    </div>
  `;
  requestAnimationFrame(() => {
    renderCharts();
    setupStickyHorizontalScrollbars();
    setupPanCarousels();
    setupTableColumnFilterPanels();
  });
}

function setupTableColumnFilterPanels() {
  window.__tableFilterPanelCleanup?.();
  window.__tableFilterPanelCleanup = null;

  const panel = document.querySelector("[data-table-column-filter-panel]");
  const trigger = panel?.closest(".table-column-filter")?.querySelector(".table-filter-trigger");
  if (!panel || !trigger) return;

  const scrollParent = trigger.closest(".table-wrap");
  const update = () => {
    const margin = 12;
    panel.classList.remove("is-positioned");
    panel.style.maxWidth = "calc(100vw - 24px)";

    const triggerRect = trigger.getBoundingClientRect();
    const panelWidth = Math.min(panel.offsetWidth || 290, Math.max(240, window.innerWidth - (margin * 2)));
    const panelHeight = panel.offsetHeight || 260;
    let left = triggerRect.left;
    let top = triggerRect.bottom + 8;

    if (left + panelWidth > window.innerWidth - margin) {
      left = window.innerWidth - panelWidth - margin;
    }
    if (top + panelHeight > window.innerHeight - margin) {
      top = triggerRect.top - panelHeight - 8;
    }

    panel.style.setProperty("--table-filter-left", `${Math.max(margin, left)}px`);
    panel.style.setProperty("--table-filter-top", `${Math.max(margin, top)}px`);
    panel.classList.add("is-positioned");
  };

  update();
  window.addEventListener("resize", update, { passive: true });
  window.addEventListener("scroll", update, { passive: true });
  scrollParent?.addEventListener("scroll", update, { passive: true });
  window.__tableFilterPanelCleanup = () => {
    window.removeEventListener("resize", update);
    window.removeEventListener("scroll", update);
    scrollParent?.removeEventListener("scroll", update);
  };
}

function setupStickyHorizontalScrollbars() {
  window.__stickyXScrollCleanups?.forEach((cleanup) => cleanup());
  window.__stickyXScrollCleanups = [];

  document.querySelectorAll("[data-sticky-x-scroll]").forEach((sticky) => {
    const card = sticky.closest(".table-card, .payroll-fortnight-card, .template-detail-card");
    const targetSelector = sticky.dataset.stickyTarget || ".table-wrap";
    const wrap = card?.querySelector(targetSelector);
    const spacer = sticky.firstElementChild;
    if (!wrap || !spacer) return;

    const update = () => {
      const hasOverflow = wrap.scrollWidth > wrap.clientWidth;
      const wrapRect = wrap.getBoundingClientRect();
      const cardRect = card.getBoundingClientRect();
      const isVisible = cardRect.bottom > 24 && cardRect.top < window.innerHeight - 8;
      sticky.hidden = !hasOverflow || !isVisible;
      sticky.style.left = `${Math.max(0, wrapRect.left)}px`;
      sticky.style.width = `${Math.min(wrapRect.width, window.innerWidth - Math.max(0, wrapRect.left))}px`;
      spacer.style.width = `${wrap.scrollWidth}px`;
      sticky.scrollLeft = wrap.scrollLeft;
    };

    let syncing = false;
    sticky.addEventListener("scroll", () => {
      if (syncing) return;
      syncing = true;
      wrap.scrollLeft = sticky.scrollLeft;
      syncing = false;
    });
    wrap.addEventListener("scroll", () => {
      if (syncing) return;
      syncing = true;
      sticky.scrollLeft = wrap.scrollLeft;
      syncing = false;
    });

    update();
    window.addEventListener("resize", update, { passive: true });
    window.addEventListener("scroll", update, { passive: true });
    let observer;
    if ("ResizeObserver" in window) {
      observer = new ResizeObserver(update);
      observer.observe(wrap);
      const table = wrap.querySelector("table");
      if (table) observer.observe(table);
      const content = wrap.firstElementChild;
      if (content) observer.observe(content);
    }
    window.__stickyXScrollCleanups.push(() => {
      window.removeEventListener("resize", update);
      window.removeEventListener("scroll", update);
      observer?.disconnect();
    });
  });
}

function setupPanCarousels() {
  document.querySelectorAll("[data-pan-carousel]").forEach((carousel) => {
    let isPanning = false;
    let startX = 0;
    let startScrollLeft = 0;
    let moved = false;
    let suppressClick = false;

    carousel.addEventListener("pointerdown", (event) => {
      if (event.button !== undefined && event.button !== 0) return;
      isPanning = true;
      moved = false;
      startX = event.clientX;
      startScrollLeft = carousel.scrollLeft;
      carousel.classList.add("is-panning");
      carousel.setPointerCapture?.(event.pointerId);
    });

    carousel.addEventListener("pointermove", (event) => {
      if (!isPanning) return;
      const deltaX = event.clientX - startX;
      if (Math.abs(deltaX) > 4) {
        moved = true;
        suppressClick = true;
        event.preventDefault();
      }
      carousel.scrollLeft = startScrollLeft - deltaX;
    });

    const stopPan = (event) => {
      if (!isPanning) return;
      isPanning = false;
      carousel.classList.remove("is-panning");
      carousel.releasePointerCapture?.(event.pointerId);
      if (!moved) suppressClick = false;
    };

    carousel.addEventListener("pointerup", stopPan);
    carousel.addEventListener("pointercancel", stopPan);
    carousel.addEventListener("pointerleave", stopPan);
    carousel.addEventListener("click", (event) => {
      if (!suppressClick) return;
      event.preventDefault();
      event.stopPropagation();
      suppressClick = false;
    }, true);
  });
}

function renderLogin() {
  return `
    <section class="login-screen">
      <div class="login-panel">
        <div class="brand">
          <span class="brand-mark">${icon("users")}</span>
          <span class="brand-text">
            <h1>HR Suite México</h1>
            <p>Gestión humana inteligente</p>
          </span>
        </div>
        <form id="login-form" class="login-form">
          <div class="section-header">
            <div>
              <p class="eyebrow">Acceso seguro</p>
              <h2>Inicio de sesión</h2>
              <p>Selecciona un rol para ver permisos y flujos simulados.</p>
            </div>
          </div>
          ${field("Correo", "email", "email", "admin@novatalento.mx", true)}
          ${field("Contraseña", "password", "password", "••••••••", true)}
          <label class="form-field">
            <span>Rol</span>
            <select class="select" name="role">${roles.map((role) => `<option ${role === state.ui.role ? "selected" : ""}>${safe(role)}</option>`).join("")}</select>
          </label>
          <label class="form-field">
            <span>Empresa</span>
            <select class="select" name="company">${state.data.companies.map((company) => `<option ${company === state.ui.company ? "selected" : ""}>${safe(company)}</option>`).join("")}</select>
          </label>
          <button class="btn" type="submit">${icon("lock", "btn-icon")}Entrar</button>
        </form>
      </div>
      <div class="login-art">
        <div class="section-header">
          <div>
            <p class="eyebrow">Mapa de operación</p>
            <h2>Contratos, nómina, pagos y recibos conectados</h2>
          </div>
        </div>
        <div class="flow-preview">
          <img src="./assets/media/flujo-referencia.png" alt="Flujo visual de módulos de recursos humanos" />
        </div>
      </div>
    </section>
  `;
}

function renderSidebar() {
  const visibleItems = menuItems.filter((item) => !item.superOnly || state.ui.role === "Superadministrador");
  const activeEmployees = state.data.employees.filter((employee) => employee.status === "Activo").length;
  const expiringContracts = expiringContractsForPanel().length;
  const pendingApprovals = pendingLegalApprovalContracts().length;
  return `
    <aside class="sidebar" aria-label="Menú principal">
      <div class="brand">
        <span class="brand-mark">${icon("users")}</span>
        <span class="brand-text">
          <h1>HR Suite</h1>
          <p>México</p>
        </span>
      </div>
      <nav class="sidebar-nav">
        ${visibleItems.map((item) => `
          <button class="nav-item ${activeFor(item.route) ? "is-active" : ""}" data-route="${safe(item.route)}" data-tooltip="${safe(item.label)}">
            ${icon(item.icon, "nav-icon")}
            <span class="nav-label">${safe(item.label)}</span>
            ${item.route === "pending-approvals" ? `<span class="nav-count">${number(pendingApprovals)}</span>` : ""}
            ${item.route === "employees" ? `<span class="nav-count">${number(activeEmployees)}</span>` : ""}
            ${item.route === "contracts" ? `<span class="nav-count nav-count-danger">${number(expiringContracts)}</span>` : ""}
          </button>
        `).join("")}
      </nav>
      <div class="sidebar-footer">
        <button class="collapse-toggle" data-action="toggle-sidebar">
          ${icon("menu", "btn-icon")}
          <span class="collapse-text">${state.ui.sidebarCollapsed ? "Expandir menú" : "Contraer menú"}</span>
        </button>
      </div>
    </aside>
  `;
}

function renderTopbar() {
  const openAlerts = state.data.alerts.filter((alert) => alert.status === "Abierta").length;
  return `
    <header class="topbar">
      <div class="topbar-title">
        <p class="eyebrow">${safe(state.ui.company)}</p>
        <h2>${safe(routeTitles[state.ui.route] || "HR Suite México")}</h2>
      </div>
      <div class="topbar-actions">
        <select class="select" data-action="change-role" aria-label="Rol activo">
          ${roles.map((role) => `<option ${role === state.ui.role ? "selected" : ""}>${safe(role)}</option>`).join("")}
        </select>
        <select class="select" data-action="change-company" aria-label="Empresa activa">
          ${state.data.companies.map((company) => `<option ${company === state.ui.company ? "selected" : ""}>${safe(company)}</option>`).join("")}
        </select>
        <button class="icon-btn" data-route="audit" data-tooltip="Bitácora">${icon("shield")}</button>
        <button class="icon-btn" data-route="validations" data-tooltip="Alertas">${icon("bell")}${openAlerts ? '<span class="badge-dot"></span>' : ""}</button>
        <button class="icon-btn" data-action="logout" data-tooltip="Salir">${icon("logout")}</button>
      </div>
    </header>
  `;
}

function renderRoute() {
  switch (state.ui.route) {
    case "pending-approvals": return renderPendingApprovals();
    case "dashboard": return renderDashboard();
    case "candidates": return renderCandidates();
    case "employees": return renderEmployees();
    case "employee-new": return renderEmployeeForm();
    case "employee-profile": return renderEmployeeProfile();
    case "employee-documents": return renderEmployeeDocuments();
    case "contracts": return renderContractsList();
    case "contracts-list": return renderContractsList();
    case "contracts-drafts": return renderContractDrafts();
    case "contract-create": return renderContractWizard();
    case "contract-editor": return renderContractEditor();
    case "contract-approval": return renderContractApproval();
    case "contract-signature": return renderContractSignature();
    case "contract-renewal": return renderContractRenewal();
    case "termination": return renderTermination();
    case "payroll": return renderPayrollDashboard();
    case "payroll-history": return renderPayrollHistory();
    case "overtime": return renderOvertime();
    case "payroll-period": return renderPayrollPeriod();
    case "incidences": return renderIncidences();
    case "perceptions": return renderPerceptions();
    case "deductions": return renderDeductions();
    case "payroll-calc": return renderPayrollCalc();
    case "payroll-summary": return renderPayrollSummary();
    case "validations": return renderValidations();
    case "payroll-authorization": return renderPayrollAuthorization();
    case "payments": return renderPayments();
    case "dispersion": return renderDispersion();
    case "rejected-payments": return renderRejectedPayments();
    case "receipts": return renderReceipts();
    case "receipt-view": return renderReceiptView();
    case "settlement": return renderSettlement();
    case "reports": return renderReports();
    case "managers": return renderManagers();
    case "config": return renderConfig();
    case "template-editor": return renderTemplateEditor();
    case "vacations": return renderVacations();
    case "audit": return renderAudit();
    default: return renderDashboard();
  }
}

function pageHeader(title, subtitle, actions = "") {
  return `
    <div class="section-header">
      <div>
        <p class="eyebrow">HR Suite México</p>
        <h3>${safe(title)}</h3>
        ${subtitle ? `<p>${safe(subtitle)}</p>` : ""}
      </div>
      ${actions ? `<div class="actions">${actions}</div>` : ""}
    </div>
  `;
}

function field(label, name, type = "text", value = "", required = false, attrs = "") {
  return `
    <label class="form-field">
      <span>${safe(label)}${required ? " *" : ""}</span>
      <input class="input" type="${safe(type)}" name="${safe(name)}" value="${safe(value)}" ${required ? "required" : ""} ${attrs} />
    </label>
  `;
}

function bonusField(draft) {
  const disabled = Boolean(draft.bonusesNotApplicable);
  return `
    <label class="form-field ${disabled ? "is-disabled" : ""}">
      <span class="field-label-with-check">
        <span>Bonos</span>
        <span class="inline-checkbox">
          <input type="checkbox" data-draft="bonusesNotApplicable" ${disabled ? "checked" : ""} />
          No Aplica
        </span>
      </span>
      <input class="input" type="number" name="bonuses" value="${safe(disabled ? 0 : draft.bonuses ?? "1200")}" data-draft="bonuses" min="0" step="0.01" ${disabled ? "disabled aria-disabled=\"true\"" : ""} />
    </label>
  `;
}

function selectField(label, name, options, value = "", required = false, attrs = "") {
  return `
    <label class="form-field">
      <span>${safe(label)}${required ? " *" : ""}</span>
      <select class="select" name="${safe(name)}" ${required ? "required" : ""} ${attrs}>
        ${options.map((option) => `<option value="${safe(option)}" ${String(option) === String(value) ? "selected" : ""}>${safe(option)}</option>`).join("")}
      </select>
    </label>
  `;
}

function textareaField(label, name, value = "", attrs = "") {
  return `
    <label class="form-field">
      <span>${safe(label)}</span>
      <textarea class="textarea" name="${safe(name)}" ${attrs}>${safe(value)}</textarea>
    </label>
  `;
}

function kpi(label, value, sub, iconName, tone = "blue", trend = "") {
  const toneColor = tone === "green" ? "var(--green)" : tone === "amber" ? "var(--amber)" : tone === "red" ? "var(--red)" : tone === "teal" ? "var(--teal)" : "var(--primary)";
  const bg = tone === "green" ? "var(--green-soft)" : tone === "amber" ? "var(--amber-soft)" : tone === "red" ? "var(--red-soft)" : tone === "teal" ? "#e2fbf8" : "var(--primary-soft)";
  return `
    <div class="card kpi-card">
      <div class="kpi-top">
        <div>
          <strong>${safe(value)}</strong>
          <span>${safe(label)}</span>
        </div>
        <span class="kpi-icon" style="color:${toneColor};background:${bg}">${icon(iconName)}</span>
      </div>
      <div class="split">
        <span class="small muted">${safe(sub)}</span>
        ${trend ? `<span class="trend ${trend.startsWith("-") ? "down" : ""}">${safe(trend)}</span>` : ""}
      </div>
    </div>
  `;
}

function quickAction(label, route, iconName, action = "") {
  return `
    <button class="quick-action" ${action ? `data-action="${safe(action)}"` : `data-route="${safe(route)}"`}>
      ${icon(iconName, "mini-icon")}
      <span>${safe(label)}</span>
    </button>
  `;
}

function renderTable({ id, rows, columns, filters = [], searchPlaceholder = "Buscar", pageSize = 8, emptyMessage = "Sin registros", footerHtml = "", toolbarExtra = "", searchPosition = "start", paginate = true, scrollY = "" }) {
  const current = tableState[id] || { search: "", page: 1, sortKey: "", sortDir: "asc", filters: {} };
  current.columnFilters = current.columnFilters || {};
  tableState[id] = current;

  let filtered = [...rows];
  if (current.search) {
    const needle = current.search.toLowerCase();
    filtered = filtered.filter((row) => {
      const haystack = row._search || Object.values(row).join(" ");
      return String(haystack).toLowerCase().includes(needle);
    });
  }

  filters.forEach((filter) => {
    const selected = current.filters[filter.key] || "";
    if (selected) {
      filtered = filtered.filter((row) => String(filter.getValue(row)) === String(selected));
    }
  });

  const columnOptionRows = [...filtered];
  filtered = applyColumnFilters(filtered, columns, current);

  if (current.sortKey) {
    const column = columns.find((col) => col.key === current.sortKey);
    filtered.sort((a, b) => {
      const av = column?.sortValue ? column.sortValue(a) : a[current.sortKey];
      const bv = column?.sortValue ? column.sortValue(b) : b[current.sortKey];
      const compare = Number.isFinite(Number(av)) && Number.isFinite(Number(bv))
        ? Number(av) - Number(bv)
        : String(av ?? "").localeCompare(String(bv ?? ""), "es");
      return current.sortDir === "asc" ? compare : -compare;
    });
  }

  const totalPages = paginate ? Math.max(1, Math.ceil(filtered.length / pageSize)) : 1;
  current.page = Math.min(current.page, totalPages);
  const start = (current.page - 1) * pageSize;
  const visible = paginate ? filtered.slice(start, start + pageSize) : filtered;

  const filterHtml = filters.map((filter) => {
    const options = typeof filter.options === "function" ? filter.options() : filter.options;
    return `
      <select class="select" data-table-filter="${safe(id)}" data-filter-key="${safe(filter.key)}" aria-label="${safe(filter.label)}">
        <option value="">${safe(filter.label)}</option>
        ${options.map((option) => `<option value="${safe(option)}" ${(current.filters[filter.key] || "") === option ? "selected" : ""}>${safe(option)}</option>`).join("")}
      </select>
    `;
  }).join("");

  const pages = paginate ? Array.from({ length: totalPages }, (_, index) => index + 1)
    .slice(Math.max(0, current.page - 3), Math.max(5, current.page + 2))
    .map((page) => `<button class="page-btn ${page === current.page ? "is-active" : ""}" data-action="table-page" data-table="${safe(id)}" data-page="${page}">${page}</button>`)
    .join("") : "";

  const searchHtml = `<input class="input" data-table-search="${safe(id)}" placeholder="${safe(searchPlaceholder)}" value="${safe(current.search)}" />`;
  const scrollStyle = scrollY ? ` style="--table-scroll-y:${safe(scrollY)}"` : "";
  const hasStickyScroll = id === "contracts-list" || id === "overtime";
  const stickyScrollHtml = hasStickyScroll ? `<div class="table-sticky-scroll" data-sticky-x-scroll aria-hidden="true"><div></div></div>` : "";

  return `
    <div class="table-card">
      <div class="table-toolbar">
        ${searchPosition === "start" ? searchHtml : ""}
        ${toolbarExtra}
        ${filterHtml}
        ${searchPosition === "end" ? searchHtml : ""}
      </div>
      <div class="table-wrap ${scrollY ? "table-wrap-vertical" : ""} ${hasStickyScroll ? "has-sticky-x-scroll" : ""}"${scrollStyle}>
        <table>
          <thead>
            <tr>
              ${columns.map((col, index) => `
                <th class="${safe(col.className || "")}">
                  <span class="table-heading">
                  ${col.sortable === false ? safe(col.label) : `<button data-action="sort-table" data-table="${safe(id)}" data-key="${safe(col.key)}">${safe(col.label)} ${current.sortKey === col.key ? (current.sortDir === "asc" ? "↑" : "↓") : ""}</button>`}
                    ${tableColumnFilterPanel(id, col, columnOptionRows, current, "right")}
                  </span>
                </th>
              `).join("")}
            </tr>
          </thead>
          <tbody>
            ${visible.length ? visible.map((row) => `
              <tr>${columns.map((col) => `<td class="${safe(col.tdClassName || "")}">${col.render ? col.render(row) : safe(row[col.key])}</td>`).join("")}</tr>
            `).join("") : `<tr><td colspan="${columns.length}"><div class="empty-state">${safe(emptyMessage)}</div></td></tr>`}
          </tbody>
          ${footerHtml ? `<tfoot>${footerHtml}</tfoot>` : ""}
        </table>
      </div>
      ${stickyScrollHtml}
      <div class="table-footer">
        <span class="small muted">${number(filtered.length)} registros</span>
        ${paginate ? `<div class="pagination">${pages}</div>` : ""}
      </div>
    </div>
  `;
}

function renderDashboardVacationCalendar() {
  const employeesById = new Map(state.data.employees.map((employee) => [Number(employee.id), employee]));
  const events = [
    { employeeId: 3, fallbackName: "Ana López Ríos", start: "2026-07-07", end: "2026-07-07", color: "#bfe8d2" },
    { employeeId: 1, fallbackName: "María González Luna", start: "2026-07-13", end: "2026-07-13", color: "#d9c8ff" },
    { employeeId: 2, fallbackName: "Juan Pérez Morales", start: "2026-07-15", end: "2026-07-18", color: "#cfe0ff" },
    { employeeId: 5, fallbackName: "Fernanda Castillo Vega", start: "2026-07-20", end: "2026-07-20", color: "#ffd1d7" },
    { employeeId: 20, fallbackName: "Ricardo Medina Ochoa", start: "2026-07-24", end: "2026-07-24", color: "#aee3dc" },
    { employeeId: 8, fallbackName: "Luis Hernández Nava", start: "2026-06-10", end: "2026-06-12", color: "#fde68a" },
    { employeeId: 17, fallbackName: "Camila Fuentes Solís", start: "2026-08-03", end: "2026-08-05", color: "#c4b5fd" },
    { employeeId: 6, fallbackName: "Diego Santos Cruz", start: "2026-08-17", end: "2026-08-19", color: "#fecaca" },
    { employeeId: 7, fallbackName: "Sofía Martínez Pineda", start: "2026-09-08", end: "2026-09-11", color: "#bbf7d0" }
  ].map((event) => {
    const employee = employeesById.get(event.employeeId);
    return {
      ...event,
      name: employee?.fullName || event.fallbackName,
      branch: employee?.branch || "Sin sucursal",
      company: employee?.company || "Sin empresa"
    };
  });
  const monthNames = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
  const year = Number.isInteger(state.dashboardCalendarYear) ? state.dashboardCalendarYear : 2026;
  const monthIndex = Number.isInteger(state.dashboardCalendarMonth) ? state.dashboardCalendarMonth : 6;
  const monthKey = `${year}-${String(monthIndex + 1).padStart(2, "0")}`;
  const weekdays = ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"];
  const firstDay = new Date(year, monthIndex, 1).getDay();
  const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
  const monthStart = `${monthKey}-01`;
  const monthEnd = `${monthKey}-${String(daysInMonth).padStart(2, "0")}`;
  const monthEvents = events.filter((event) => event.start <= monthEnd && event.end >= monthStart);
  const previousMonthDays = new Date(year, monthIndex, 0).getDate();
  const totalCells = Math.ceil((firstDay + daysInMonth) / 7) * 7;
  const cells = Array.from({ length: totalCells }, (_, index) => {
    const day = index - firstDay + 1;
    const inMonth = day >= 1 && day <= daysInMonth;
    const visibleDay = inMonth ? day : day < 1 ? previousMonthDays + day : day - daysInMonth;
    const dateKey = inMonth ? `${monthKey}-${String(day).padStart(2, "0")}` : "";
    const dayEvents = events.filter((event) => dateKey && dateKey >= event.start && dateKey <= event.end);

    return `
      <div class="dashboard-calendar-day${inMonth ? "" : " is-muted"}">
        <span>${visibleDay}</span>
        ${dayEvents.map((event) => `
          <div class="vacation-event" style="background:${event.color}" title="${safe(`${event.name} | ${event.branch} | ${event.company}`)}">
            <span class="vacation-event-person">${safe(event.name)}</span>
            <span class="vacation-event-context">
              <span>${safe(event.branch)}</span>
              <span>${safe(event.company)}</span>
            </span>
          </div>
        `).join("")}
      </div>
    `;
  }).join("");

  return `
    <div class="card dashboard-vacation-calendar">
      <div class="dashboard-calendar-toolbar">
        <div>
          <h3>Calendario de vacaciones</h3>
          <p>Visualiza las personas que tienen vacaciones en cada fecha.</p>
        </div>
        <div class="dashboard-calendar-nav">
          <button class="btn icon-only" type="button" data-action="change-dashboard-calendar-month" data-direction="-1" aria-label="Mes anterior">&lt;</button>
          <strong>${monthNames[monthIndex]} ${year}</strong>
          <button class="btn icon-only" type="button" data-action="change-dashboard-calendar-month" data-direction="1" aria-label="Mes siguiente">&gt;</button>
        </div>
      </div>
      <div class="dashboard-calendar-scroll">
        <div class="dashboard-calendar-grid">
          ${weekdays.map((day) => `<div class="dashboard-calendar-weekday">${day}</div>`).join("")}
          ${cells}
        </div>
      </div>
      <div class="dashboard-calendar-legend">
        ${monthEvents.length ? monthEvents.map((event) => `<span><i class="legend-dot" style="background:${event.color}"></i>${event.name}</span>`).join("") : `<span>Sin vacaciones registradas este mes</span>`}
      </div>
    </div>
  `;
}

function renderDashboard() {
  const active = state.data.employees.filter((employee) => employee.status === "Activo");
  const contractsSoon = state.data.contracts.filter((contract) => contract.endDate && contract.status === "Próximo a vencer").length;
  const contractsExpired = state.data.contracts.filter((contract) => contract.endDate && contract.status === "Vencido").length;
  const receipts = state.data.receipts;
  const net = receipts.reduce((sum, receipt) => sum + receipt.net, 0);

  return `
    <div class="screen-stack">
      ${pageHeader("Inicio", "Indicadores ejecutivos de empleados, contratos, nómina, pagos e incidencias.", `
        <button class="btn" data-route="employee-new">${icon("plus", "btn-icon")}Crear empleado</button>
        <button class="btn secondary" data-action="start-contract-process">${icon("file-signature", "btn-icon")}Crear contrato</button>
      `)}
      <div class="grid kpi-grid dashboard-compact-kpis">
        ${kpi("Empleados activos", active.length, "25 registros totales", "users", "blue", "+4%")}
        ${kpi("Contratos próximos a vencer", contractsSoon, `${contractsExpired} vencidos`, "file-signature", "amber")}
        ${kpi("Neto a pagar", money(net), "Periodo NOM-2026-14", "circle-dollar", "green", "+8.7%")}
        ${kpi("Incidencias abiertas", state.data.incidences.filter((item) => item.status === "Pendiente").length, "Pendientes de aprobación", "calendar-alert", "red")}
      </div>

      <div class="card">
        <div class="section-header">
          <div>
            <h3>Accesos rápidos</h3>
            <p>Operaciones frecuentes por flujo laboral.</p>
          </div>
        </div>
        <div class="quick-actions">
          ${quickAction("Crear empleado", "employee-new", "plus")}
          ${quickAction("Crear contrato", "", "file-signature", "start-contract-process")}
          ${quickAction("Abrir periodo", "payroll-period", "calculator")}
          ${quickAction("Registrar incidencia", "incidences", "calendar-alert")}
          ${quickAction("Calcular nómina", "payroll-calc", "refresh")}
          ${quickAction("Autorizar nómina", "payroll-authorization", "check")}
          ${quickAction("Generar pagos", "payments", "wallet")}
        </div>
      </div>

      ${renderDashboardVacationCalendar()}
    </div>
  `;
}

function registeredCandidateCompanies(currentCompany = "") {
  const catalogCompanies = Array.isArray(suiteConfig.registeredCompanies)
    ? suiteConfig.registeredCompanies
    : state.data.companies;

  return [...new Set(
    [currentCompany, ...(catalogCompanies || [])]
      .map((company) => String(company || "").trim())
      .filter(Boolean)
  )];
}

function renderCandidateForm() {
  const candidate = (state.data.candidates || []).find((item) => Number(item.id) === Number(state.ui.candidateEditingId));
  const isEditing = Boolean(candidate);
  const companyOptions = registeredCandidateCompanies(candidate?.company);
  const selectedCompany = candidate?.company || companyOptions[0] || "";
  const positionOptions = [...new Set([candidate?.position, ...state.data.positions].filter(Boolean))];
  const sourceOptions = [...new Set([candidate?.source, "LinkedIn", "Computrabajo", "Referido", "Indeed", "Bolsa de trabajo", "OCC"].filter(Boolean))];
  const statusOptions = [...new Set([candidate?.status, "Nuevo", "En revisión", "Preselección", "En entrevista", "Oferta", "Contratado"].filter(Boolean))];
  return `
    <div class="screen-stack candidate-form-screen">
      ${pageHeader(isEditing ? "Editar candidato" : "Nuevo candidato", isEditing ? "Actualiza la información del candidato seleccionado." : "Captura la información principal para iniciar el proceso de contratación.", `
        <button class="btn secondary" type="button" data-action="close-candidate-form">${icon("arrow-left", "btn-icon")}Volver al registro</button>
      `)}

      <form id="candidate-form" class="card candidate-inline-form">
        <input type="hidden" name="candidateId" value="${isEditing ? safe(candidate.id) : ""}" />
        <div class="section-header candidate-inline-form-heading">
          <div>
            <h3>${isEditing ? `Editar ${safe(candidate.name)}` : "Datos del candidato"}</h3>
            <p>${isEditing ? "Modifica los campos necesarios y guarda los cambios." : "Completa los campos requeridos para agregarlo al registro."}</p>
          </div>
        </div>

        <div class="form-grid two">
          ${field("Nombre completo", "name", "text", candidate?.name || "", true)}
          ${selectField("Empresa", "company", companyOptions, selectedCompany, true)}
          ${selectField("Puesto", "position", positionOptions, candidate?.position || state.data.positions[0], true)}
          ${field("Correo", "email", "email", candidate?.email || "candidato@empresa.mx", true)}
          ${field("Teléfono", "phone", "tel", candidate?.phone || "55 0000 0000", true)}
          ${field("Experiencia", "experience", "number", candidate?.experience ?? "3", true, 'min="0" max="40"')}
          ${selectField("Fuente", "source", sourceOptions, candidate?.source || "LinkedIn", true)}
          ${selectField("Estatus", "status", statusOptions, candidate?.status || "Nuevo", true)}
          ${field("Sueldo solicitado", "requestedSalary", "number", candidate?.requestedSalary ?? "45000", false, 'min="0" step="500"')}
          ${field("Oferta propuesta", "proposedOffer", "number", candidate?.proposedOffer ?? "42000", false, 'min="0" step="500"')}
        </div>

        <div class="candidate-inline-form-actions">
          <button class="btn secondary" type="button" data-action="close-candidate-form">${icon("x", "btn-icon")}Cancelar</button>
          <button class="btn" type="submit">${icon("check", "btn-icon")}${isEditing ? "Guardar cambios" : "Guardar candidato"}</button>
        </div>
      </form>
    </div>
  `;
}

function renderCandidates() {
  if (state.ui.candidateFormOpen) return renderCandidateForm();

  const candidates = (state.data.candidates || []).map((candidate) => ({
    ...candidate,
    _search: `${candidate.name} ${candidate.company} ${candidate.position} ${candidate.email} ${candidate.source} ${candidate.status} ${candidate.negotiationStatus}`
  }));
  const candidateCompanyOptions = [...new Set([
    ...registeredCandidateCompanies(),
    ...candidates.map((candidate) => candidate.company)
  ].filter(Boolean))];
  const selected = candidates.filter((candidate) => candidate.selected);
  const negotiation = candidates.filter((candidate) => candidate.interviewResult === "Aprobado" || candidate.negotiationStatus !== "Pendiente");
  return `
    <div class="screen-stack">
      <div class="screen-stack">
          <section class="candidate-stage-card">
            <div class="stage-heading">
              <span class="stage-number">1</span>
              <div>
                <h3>Registro de candidatos</h3>
                <p>Alta, CV, fuente, estatus y seguimiento inicial.</p>
              </div>
              <button class="btn stage-heading-action" type="button" data-action="open-candidate-form">${icon("plus", "btn-icon")}Nuevo candidato</button>
            </div>
            ${renderTable({
              id: "candidate-registry",
              rows: candidates,
              pageSize: 5,
              searchPlaceholder: "Buscar candidatos, puesto o correo",
              filters: [
                { key: "company", label: "Empresa", options: candidateCompanyOptions, getValue: (row) => row.company },
                { key: "status", label: "Estatus", options: [...new Set(candidates.map((row) => row.status))], getValue: (row) => row.status },
                { key: "source", label: "Fuente", options: [...new Set(candidates.map((row) => row.source))], getValue: (row) => row.source }
              ],
              columns: [
                { key: "name", label: "Nombre", render: (row) => `<div class="employee-cell">${avatar(row)}<div><strong>${safe(row.name)}</strong><div class="small muted">${safe(row.email)}</div></div></div>` },
                { key: "company", label: "Empresa" },
                { key: "position", label: "Puesto" },
                { key: "phone", label: "Teléfono" },
                { key: "experience", label: "Experiencia", render: (row) => `${row.experience} años`, sortValue: (row) => row.experience },
                { key: "registeredAt", label: "Registro", render: (row) => date(row.registeredAt) },
                { key: "source", label: "Fuente" },
                { key: "cvUpload", label: "Subir", sortable: false, render: (row) => `
                  <label class="btn secondary compact candidate-cv-upload">
                    ${icon("upload", "btn-icon")}Subir
                    <input type="file" accept=".pdf,application/pdf" data-candidate-cv-upload="${row.id}" />
                  </label>
                ` },
                { key: "cv", label: "CV", render: (row) => row.cv ? `<button class="btn ghost compact" data-action="download-cv" data-id="${row.id}">${icon("download", "btn-icon")}PDF</button>` : badge("Pendiente"), filterValue: (row) => row.cv ? "PDF" : "Pendiente" },
                { key: "status", label: "Estatus", render: (row) => badge(row.status) },
                { key: "actions", label: "", sortable: false, render: (row) => `<button class="btn secondary compact" data-action="edit-candidate" data-id="${row.id}">${icon("edit", "btn-icon")}Editar</button>` }
              ]
            })}
          </section>

          <section class="candidate-stage-card">
            <div class="stage-heading">
              <span class="stage-number">2</span>
              <div>
                <h3>Candidatos seleccionados para entrevistas</h3>
                <p>Programación de entrevista RH, técnica y responsable.</p>
              </div>
            </div>
            ${renderTable({
              id: "candidate-interviews",
              rows: selected,
              pageSize: 5,
              searchPlaceholder: "Buscar entrevistas",
              filters: [
                { key: "company", label: "Empresa", options: candidateCompanyOptions, getValue: (row) => row.company },
                { key: "responsible", label: "Responsable", options: [...new Set(candidates.map((row) => row.responsible))], getValue: (row) => row.responsible }
              ],
              columns: [
                { key: "selected", label: "Seleccionar", render: (row) => `<input type="checkbox" ${row.selected ? "checked" : ""} data-action="toggle-candidate-selected" data-id="${row.id}" aria-label="Seleccionar candidato" />`, sortable: false, filterValue: (row) => row.selected ? "Seleccionado" : "No seleccionado" },
                { key: "name", label: "Nombre", render: (row) => safe(row.name) },
                { key: "company", label: "Empresa" },
                { key: "position", label: "Puesto" },
                { key: "registeredAt", label: "Fecha de selección", render: (row) => date(row.registeredAt) },
                { key: "rhInterview", label: "Entrevista RH", render: (row) => safe(row.rhInterview) },
                { key: "technicalInterview", label: "Entrevista técnica", render: (row) => safe(row.technicalInterview) },
                { key: "responsible", label: "Responsable" },
                { key: "status", label: "Estatus", render: (row) => badge(row.interviewResult === "Aprobado" ? "Programada" : row.interviewResult) }
              ],
              emptyMessage: "Sin candidatos seleccionados para entrevista"
            })}
          </section>

          <section class="candidate-stage-card">
            <div class="stage-heading">
              <span class="stage-number">3</span>
              <div>
                <h3>Post entrevista y negociación</h3>
                <p>Resultados, sueldo solicitado, oferta propuesta y estatus de negociación.</p>
              </div>
            </div>
            ${renderTable({
              id: "candidate-negotiation",
              rows: negotiation,
              pageSize: 5,
              searchPlaceholder: "Buscar negociación",
              filters: [
                { key: "company", label: "Empresa", options: candidateCompanyOptions, getValue: (row) => row.company },
                { key: "negotiationStatus", label: "Status negociación", options: [...new Set(candidates.map((row) => row.negotiationStatus))], getValue: (row) => row.negotiationStatus }
              ],
              columns: [
                { key: "name", label: "Nombre" },
                { key: "company", label: "Empresa" },
                { key: "position", label: "Puesto" },
                { key: "interviewResult", label: "Resultado entrevista", render: (row) => badge(row.interviewResult) },
                { key: "requestedSalary", label: "Sueldo solicitado", render: (row) => money(row.requestedSalary), sortValue: (row) => row.requestedSalary },
                { key: "proposedOffer", label: "Oferta propuesta", render: (row) => money(row.proposedOffer), sortValue: (row) => row.proposedOffer },
                { key: "benefits", label: "Prestaciones" },
                { key: "negotiationStatus", label: "Status negociación", render: (row) => badge(row.negotiationStatus) },
                { key: "lastUpdate", label: "Última actualización", render: (row) => date(row.lastUpdate) },
                { key: "actions", label: "Acciones", sortable: false, render: (row) => `<button class="btn ghost compact" data-action="send-candidate-offer" data-id="${row.id}">${icon("send", "btn-icon")}Oferta</button>` }
              ]
            })}
          </section>

          <section class="candidate-stage-card">
            <div class="stage-heading">
              <span class="stage-number">4</span>
              <div>
                <h3>Contratos con empleados</h3>
                <p>Contratos generados desde candidatos aprobados y vencimientos operativos.</p>
              </div>
            </div>
            <div class="inline-alert">${icon("alert", "mini-icon")} Alerta de vencimiento de contratos: ${state.data.contracts.filter((contract) => contract.status === "Próximo a vencer").length} contratos vencen en los próximos 30 días.</div>
            ${renderTable({
              id: "candidate-contracts",
              rows: state.data.contracts.slice(0, 12).map((contract) => ({ ...contract, displayStatus: displayContractStatus(contract), _search: `${contract.employee} ${contract.company} ${contract.position} ${displayContractStatus(contract)}` })),
              pageSize: 5,
              searchPlaceholder: "Buscar contratos",
              filters: [
                { key: "company", label: "Empresa", options: state.data.companies, getValue: (row) => row.company },
                { key: "status", label: "Estatus", options: [...new Set(["Nuevo ingreso", ...state.data.contracts.map((row) => displayContractStatus(row))])], getValue: (row) => row.displayStatus }
              ],
              columns: [
                { key: "employee", label: "Empleado" },
                { key: "company", label: "Empresa" },
                { key: "position", label: "Puesto" },
                { key: "startDate", label: "Fecha de ingreso", render: (row) => date(row.startDate) },
                { key: "type", label: "Tipo de contrato" },
                { key: "trialPeriod", label: "Vigencia" },
                { key: "endDate", label: "Fecha de vencimiento", render: (row) => row.endDate ? date(row.endDate) : "No aplica" },
                { key: "contract", label: "Contrato", render: (row) => `<button class="btn ghost compact" data-action="download-contract" data-id="${row.id}">${icon("download", "btn-icon")}PDF</button>`, sortable: false, filterValue: (row) => row.folio },
                { key: "employeeSignature", label: "Firma", render: (row) => badge(row.employeeSignature) },
                { key: "salary", label: "Sueldo mensual", render: (row) => money(row.salary), sortValue: (row) => row.salary },
                { key: "status", label: "Estatus", render: (row) => badge(row.displayStatus) }
              ]
            })}
          </section>
        </div>

        ${false ? `<aside class="candidate-side-panel">
          <div class="stage-heading">
            <span class="stage-number">5</span>
            <div>
              <h3>Contratos próximos a vencer</h3>
              <p>Vencen en los próximos 30 días.</p>
            </div>
          </div>
          <div class="side-filter">
            <span class="small muted">Empresa</span>
            <select class="select" data-table-filter="candidate-contracts" data-filter-key="company" aria-label="Empresa contratos próximos">
              <option value="">Todas</option>
              ${state.data.companies.map((company) => `<option value="${safe(company)}">${safe(company)}</option>`).join("")}
            </select>
          </div>
          <div class="contract-due-list">
            ${expiringContracts.map((contract) => {
              const days = daysRemaining(contract.endDate);
              const tone = String(days).includes("vencidos") || String(days).startsWith("0") || String(days).startsWith("1 ") || String(days).startsWith("2 ") || String(days).startsWith("3 ") || String(days).startsWith("4 ") || String(days).startsWith("5 ") ? "red" : "amber";
              return `
                <div class="contract-due-item">
                  <div>
                    <strong>${safe(contract.employee)}</strong>
                    <span>${safe(contract.company)} · ${safe(contract.position)}</span>
                    <span>Vence: ${date(contract.endDate)}</span>
                  </div>
                  ${tag(days, tone)}
                  <div class="actions">
                    <button class="btn ghost compact" data-action="renew-contract-from-list" data-id="${contract.id}">Renovar</button>
                    <button class="btn ghost compact" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-create">Nuevo contrato</button>
                  </div>
                </div>
              `;
            }).join("")}
          </div>
          <button class="btn secondary full" data-route="contracts-list">${icon("arrow-right", "btn-icon")}Ver todos los contratos por vencer</button>
        </aside>` : ""}
      </div>
    </div>
  `;
}

function alertItem(alert) {
  const severity = alert.level === "Crítica" ? "critical" : alert.level === "Advertencia" ? "warning" : "info";
  return `
    <div class="alert-item">
      <span class="severity ${severity}"></span>
      <div>
        <strong>${safe(alert.title)}</strong>
        <div class="small muted">${safe(alert.module)} · ${safe(alert.due)}</div>
      </div>
      ${badge(alert.level)}
    </div>
  `;
}

function taskItem(task) {
  return `
    <div class="task-item">
      ${icon("check", "mini-icon")}
      <div>
        <strong>${safe(task.title)}</strong>
        <div class="small muted">${safe(task.owner)} · ${safe(task.due)}</div>
      </div>
      ${badge(task.status)}
    </div>
  `;
}

function renderEmployeeExcelFilter(employees) {
  const selected = new Set(state.ui.employeeNameFilter || []);
  const selectNone = selected.has("__NONE__");
  const hasFilter = selected.size > 0;
  const checkedCount = selectNone ? 0 : hasFilter ? selected.size : employees.length;
  const allChecked = !selectNone && (!hasFilter || selected.size === employees.length);
  const filterOpen = Boolean(state.ui.employeeFilterOpen);
  return `
    <div class="employee-selector-zone">
      <button class="btn secondary employee-selector-toggle ${filterOpen ? "is-active" : ""}" data-action="toggle-employee-excel-filter">
        ${icon("users", "btn-icon")}
        Seleccionar empleados
        <span class="selector-count">${number(checkedCount)}</span>
      </button>
      ${filterOpen ? `
        <div class="employee-selector-flyout">
          <div class="excel-filter-panel employee-selector-panel">
           <input class="input excel-filter-search" data-excel-filter-search placeholder="Buscar" autocomplete="off" />
           <div class="excel-filter-list">
             <label class="excel-filter-option excel-filter-all">
               <input type="checkbox" data-excel-filter-select-all ${allChecked ? "checked" : ""} />
               <span>(Seleccionar todo)</span>
            </label>
            ${employees.map((employee) => {
              const checked = !selectNone && (!hasFilter || selected.has(employee.number));
              const haystack = `${employee.fullName} ${employee.number} ${employee.position} ${employee.department} ${employee.rfc}`;
              return `
                <label class="excel-filter-option" data-excel-filter-option data-search="${safe(haystack)}">
                  <input type="checkbox" data-excel-filter-value value="${safe(employee.number)}" ${checked ? "checked" : ""} />
                  <span>${safe(employee.fullName.toUpperCase())}</span>
                </label>
              `;
            }).join("")}
          </div>
           <div class="excel-filter-actions">
             <button class="btn compact" data-action="apply-employee-excel-filter">ACEPTAR</button>
             <button class="btn secondary compact" data-action="cancel-employee-excel-filter">Cancelar</button>
           </div>
          </div>
          <div class="employee-selector-summary">
            <strong>${number(checkedCount)}</strong>
            <span>empleados seleccionados</span>
          </div>
        </div>
      ` : ""}
    </div>
  `;
}

function renderEmployees() {
  const managerScope = state.ui.role === "Gerente de sucursal" ? managerBranches() : [];
  const employeeSource = managerScope.length ? branchEmployees(managerScope) : state.data.employees;
  const scopedEmployeeIds = new Set(employeeSource.map((employee) => Number(employee.id)));
  const allRows = employeeSource.map((employee) => {
    const contractStatus = displayEmployeeContractStatus(employee);
    return { ...employee, contractStatus, _search: `${employee.number} ${employee.fullName} ${employee.rfc} ${employee.company} ${employee.department} ${employee.position} ${employee.branch} ${employee.status} ${employee.contractType} ${contractStatus}` };
  });
  const rows = allRows;
  const vacationRequests = state.data.vacations.filter((vacation) => scopedEmployeeIds.has(Number(vacation.employeeId)));
  const pendingVacations = vacationRequests.filter((vacation) => vacation.status === "Pendiente");
  const columns = [
    { key: "id", label: "ID", render: (row) => safe(row.number), filterValue: (row) => row.number },
    { key: "fullName", label: "Nombre completo", render: (row) => `<div class="employee-cell">${avatar(row)}<div><strong>${safe(row.fullName)}</strong><div class="small muted">${safe(row.email)}</div></div></div>` },
    { key: "position", label: "Puesto" },
    { key: "department", label: "Departamento" },
    { key: "branch", label: "Sucursal" },
    { key: "hireDate", label: "Ingreso", render: (row) => date(row.hireDate) },
    { key: "contractType", label: "Contrato" },
    { key: "contractStatus", label: "Estatus contrato", render: (row) => badge(row.contractStatus) },
    { key: "grossSalary", label: "Sueldo", render: (row) => `<span class="amount">${money(row.grossSalary)}</span>`, sortValue: (row) => row.grossSalary },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) },
    { key: "nextPay", label: "Próximo pago", render: (row) => date(row.nextPay) },
    { key: "profileAction", label: "Perfil", sortable: false, className: "text-center", tdClassName: "action-column", render: (row) => `<button class="btn secondary compact employee-view-btn" data-action="view-employee" data-id="${row.id}" data-tooltip="Ver perfil">${icon("eye", "btn-icon")}Ver</button>` },
    { key: "documentsAction", label: "Expediente", sortable: false, className: "text-center", tdClassName: "action-column", render: (row) => `<button class="btn secondary compact employee-view-btn" data-action="select-employee-route" data-id="${row.id}" data-route-target="employee-documents" data-tooltip="Ver expediente">${icon("file", "btn-icon")}Ver</button>` },
    { key: "contractAction", label: "Contrato", sortable: false, className: "text-center", tdClassName: "action-column", render: (row) => `<button class="btn secondary compact employee-view-btn" data-action="view-signed-contract" data-id="${row.id}" data-tooltip="Ver contrato firmado">${icon("file-signature", "btn-icon")}Ver</button>` }
  ];
  if (state.ui.role === "Gerente de sucursal") {
    columns.push({ key: "vacationAction", label: "Vacaciones", sortable: false, className: "text-center", tdClassName: "action-column", render: (row) => `<button class="icon-btn" data-action="manager-vacation-request" data-id="${row.id}" data-tooltip="Crear solicitud de vacaciones">${icon("calendar-days")}</button>` });
  }

  return `
    <div class="screen-stack">
${pageHeader("Listado de empleados", "", `
  <button class="btn" data-route="employee-new">${icon("plus", "btn-icon")}Nuevo empleado</button>
  <button class="btn secondary" data-action="export-employees">${icon("download", "btn-icon")}CSV</button>
`)}
      <div class="card">
        <div class="section-header">
          <div>
            <h3>Vacaciones del personal</h3>
            <p>Solicitudes, saldos y aprobaciones quedan administrados dentro del panel de empleados.</p>
          </div>
          <div class="actions">
            <button class="btn secondary" data-action="portal-vacation">${icon("plus", "btn-icon")}Nueva solicitud</button>
            <button class="btn secondary" data-route="vacations">${icon("calendar-alert", "btn-icon")}Gestionar Vacaciones <span class="selector-count">${number(pendingVacations.length)}</span></button>
          </div>
        </div>
      </div>
      ${renderTable({
        id: "employees",
        rows,
        columns,
        searchPlaceholder: "Buscar por nombre, ID, empresa, puesto o RFC",
        filters: [
          { key: "company", label: "Empresa", options: state.data.companies, getValue: (row) => row.company },
          { key: "contractType", label: "Contrato", options: [...new Set(allRows.map((row) => row.contractType))], getValue: (row) => row.contractType },
          { key: "contractStatus", label: "Estatus contrato", options: [...new Set(["Nuevo ingreso", ...allRows.map((row) => row.contractStatus), "Sin Contrato"])], getValue: (row) => row.contractStatus },
          { key: "department", label: "Departamento", options: state.data.departments, getValue: (row) => row.department },
          { key: "position", label: "Puesto", options: state.data.positions, getValue: (row) => row.position },
          { key: "branch", label: "Sucursal", options: state.data.branches, getValue: (row) => row.branch },
          { key: "status", label: "Estatus", options: ["Activo", "Baja", "Suspendido"], getValue: (row) => row.status }
        ],
        paginate: false,
        scrollY: "620px",
        searchPosition: "end"
      })}
    </div>
  `;
}

function nextEmployeeNumber() {
  const highest = state.data.employees.reduce((max, employee) => {
    const match = String(employee.number || "").match(/(\d+)\s*$/);
    return match ? Math.max(max, Number(match[1])) : max;
  }, 0);
  return `EMP-${String(highest + 1).padStart(5, "0")}`;
}

function candidateNameParts(name) {
  const parts = String(name || "").trim().split(/\s+/).filter(Boolean);
  return {
    firstName: parts[0] || "Candidato",
    lastName: parts[1] || "Pendiente",
    secondLastName: parts.slice(2).join(" ")
  };
}

function ensureCandidateContractEmployee(candidate) {
  state.data.contractCandidateEmployees = state.data.contractCandidateEmployees || [];
  let proxy = state.data.contractCandidateEmployees.find(
    (employee) => Number(employee.sourceCandidateId) === Number(candidate.id)
  );
  const salary = Number(candidate.proposedOffer || candidate.requestedSalary || 0);
  if (proxy) {
    proxy.fullName = candidate.name;
    proxy.company = candidate.company || proxy.company;
    proxy.position = candidate.position || proxy.position;
    proxy.email = candidate.email || proxy.email;
    proxy.phone = candidate.phone || proxy.phone;
    proxy.grossSalary = salary;
    return proxy;
  }

  const nameParts = candidateNameParts(candidate.name);
  proxy = {
    id: `candidate-${candidate.id}`,
    number: `CAN-${String(candidate.id).padStart(5, "0")}`,
    ...nameParts,
    fullName: candidate.name || "Candidato",
    gender: "",
    initials: candidate.initials || String(candidate.name || "C").split(/\s+/).map((part) => part[0]).join("").slice(0, 2).toUpperCase(),
    avatarColor: candidate.avatarColor || "#3157d5",
    birthDate: "",
    curp: "Pendiente",
    rfc: "Pendiente",
    nss: "Pendiente",
    civilStatus: "Pendiente",
    nationality: "Mexicana",
    phone: candidate.phone || "",
    email: candidate.email || "",
    address: "Pendiente",
    emergencyContact: "Pendiente",
    company: candidate.company || state.data.companies[0] || "Sin empresa",
    branch: "Sin sucursal",
    department: "Sin departamento",
    position: candidate.position || "Pendiente",
    manager: "Pendiente",
    hireDate: candidate.registeredAt || today(),
    seniority: "0 años",
    workerType: "Pendiente",
    modality: "Presencial",
    workday: "Diurna",
    schedule: "Pendiente",
    workDays: "Pendiente",
    workplace: "Pendiente",
    riskClass: "Pendiente",
    status: "Candidato",
    grossSalary: salary,
    dailySalary: +(salary / 30).toFixed(2),
    integratedDailySalary: +((salary / 30) * 1.0493).toFixed(2),
    payFrequency: "Quincenal",
    payrollType: "Ordinaria",
    bank: "Pendiente",
    clabe: "",
    account: "",
    paymentMethod: "Pendiente",
    salaryZone: "Zona libre general",
    commissions: "No aplica",
    recurringBonus: 0,
    taxRegime: "Sueldos y salarios",
    fiscalZip: "",
    cfdiUse: "CN01 Nómina",
    taxContractType: "Tiempo indeterminado",
    taxWorkdayType: "Jornada diurna",
    taxRegimeType: "02 Sueldos",
    contractType: "Tiempo indeterminado",
    nextPay: "",
    vacationDays: 12,
    loanBalance: 0,
    documents: [],
    timeline: [],
    newHire: true,
    sourceCandidateId: candidate.id
  };
  state.data.contractCandidateEmployees.unshift(proxy);
  return proxy;
}

function renderEmployeeForm() {
  const requiredDocuments = [
    "Identificación",
    "CURP",
    "RFC",
    "Comprobante de domicilio",
    "Número de Seguridad Social",
    "Acta de nacimiento",
    "Comprobante de estudios",
    "Estado de cuenta",
    "Contrato",
    "Aviso de privacidad",
    "Carta de confidencialidad",
    "Reglamento interno",
    "Otros documentos"
  ];
  return `
    <div class="screen-stack">
      ${pageHeader("Alta de empleado", "Formulario operativo por secciones con validaciones fiscales, laborales, salariales y documentales.", `
        <button class="btn secondary" data-route="employees">${icon("x", "btn-icon")}Cancelar</button>
      `)}
      <form id="employee-form" class="form-card employee-form-compact">
        <div class="form-section">
          <div class="form-section-title"><h4>Datos personales</h4>${tag("Identidad", "blue")}</div>
          <div class="form-grid">
            ${field("Nombre", "firstName", "text", "", true)}
            ${field("Apellido paterno", "lastName", "text", "", true)}
            ${field("Apellido materno", "secondLastName", "text", "", true)}
            ${field("Fecha de nacimiento", "birthDate", "date", "1992-04-15", true)}
            ${field("CURP", "curp", "text", "GOLM920415MDFNRR09", true, 'maxlength="18"')}
            ${field("RFC", "rfc", "text", "GOLM920415A1B", true, 'maxlength="13"')}
            ${field("Número de Seguridad Social", "nss", "text", "72123456789", true)}
            ${selectField("Estado civil", "civilStatus", ["Soltero", "Casado", "Unión libre", "Divorciado"], "Soltero")}
            ${field("Nacionalidad", "nationality", "text", "Mexicana")}
            ${field("Teléfono", "phone", "tel", "55 1234 5678", true)}
            ${field("Correo", "email", "email", "nuevo.empleado@empresa.mx", true)}
            ${field("Domicilio", "address", "text", "Av. Reforma 100, CDMX", true)}
            ${field("Contacto de emergencia", "emergencyContact", "text", "Laura Pérez - 55 0000 0000")}
          </div>
        </div>

        <div class="form-section">
          <div class="form-section-title"><h4>Datos laborales</h4>${tag("Relación laboral", "teal")}</div>
          <div class="form-grid">
            ${field("Número de empleado", "number", "text", nextEmployeeNumber(), true, 'pattern="EMP-[0-9]{5}" maxlength="9" title="Use el formato EMP-00001"')}
            ${selectField("Empresa", "company", ["Todas", ...state.data.companies], "Todas", true)}
            ${selectField("Sucursal", "branch", state.data.branches, state.data.branches[0], true)}
            ${selectField("Departamento", "department", state.data.departments, state.data.departments[0], true)}
            ${selectField("Puesto", "position", state.data.positions, state.data.positions[0], true)}
            ${field("Jefe inmediato", "manager", "text", "Mónica Salcedo")}
            ${field("Fecha de ingreso", "hireDate", "date", "2026-08-01", true)}
            ${field("Antigüedad", "seniority", "text", "0 años")}
            ${selectField("Tipo de trabajador", "workerType", ["Confianza", "Sindicalizado", "Eventual"], "Confianza")}
            ${selectField("Modalidad", "modality", ["Presencial", "Remota", "Híbrida"], "Híbrida")}
            ${selectField("Jornada", "workday", ["Diurna", "Nocturna", "Mixta"], "Diurna")}
            ${field("Horario", "schedule", "text", "09:00 a 18:00")}
            ${field("Días laborales", "workDays", "text", "Lunes a viernes")}
            ${field("Centro de trabajo", "workplace", "text", "Corporativo")}
            ${selectField("Riesgo de puesto", "riskClass", ["Clase I", "Clase II", "Clase III", "Clase IV", "Clase V"], "Clase I")}
            ${selectField("Estatus", "status", ["Activo", "Suspendido", "Baja"], "Activo")}
          </div>
        </div>

        <div class="form-section">
          <div class="form-section-title"><h4>Datos salariales</h4>${tag("Nómina", "green")}</div>
          <div class="form-grid">
            ${field("Sueldo bruto mensual", "grossSalary", "number", "32000", true, 'min="0" step="0.01"')}
            ${field("Sueldo diario", "dailySalary", "number", "1066.67", true, 'min="0" step="0.01"')}
            ${field("Sueldo diario integrado", "integratedDailySalary", "number", "1119.25", true, 'min="0" step="0.01"')}
            ${selectField("Periodicidad de pago", "payFrequency", ["Semanal", "Catorcenal", "Quincenal", "Mensual"], "Quincenal")}
            ${selectField("Tipo de nómina", "payrollType", ["Ordinaria", "Extraordinaria", "Aguinaldo", "PTU", "Finiquito", "Liquidación", "Bonos", "Comisiones"], "Ordinaria")}
            ${selectField("Banco", "bank", state.data.banks, "BBVA")}
            ${field("CLABE", "clabe", "text", "012180001234567890", true, 'maxlength="18"')}
            ${field("Cuenta bancaria", "account", "text", "4381000999")}
            ${selectField("Forma de pago", "paymentMethod", ["Transferencia", "Efectivo", "Cheque"], "Transferencia")}
            ${selectField("Zona salarial", "salaryZone", ["Zona libre general", "Zona frontera norte"], "Zona libre general")}
            ${field("Esquema de comisiones", "commissions", "text", "No aplica")}
            ${field("Bonos recurrentes", "recurringBonus", "number", "0", false, 'min="0" step="0.01"')}
          </div>
        </div>

        <div class="form-section">
          <div class="form-section-title"><h4>Datos fiscales</h4>${tag("SAT e IMSS", "amber")}</div>
          <div class="form-grid">
            ${selectField("Régimen fiscal", "taxRegime", ["Sueldos y salarios", "Asimilados a salarios"], "Sueldos y salarios")}
            ${field("Código postal fiscal", "fiscalZip", "text", "06600", true)}
            ${field("Uso de CFDI", "cfdiUse", "text", "CN01 Nómina")}
            ${selectField("Tipo de contrato", "taxContractType", state.data.contractTypes, "Tiempo indeterminado")}
            ${selectField("Tipo de jornada", "taxWorkdayType", ["Jornada diurna", "Jornada nocturna", "Jornada mixta"], "Jornada diurna")}
            ${selectField("Tipo de régimen", "taxRegimeType", ["02 Sueldos", "03 Jubilados", "04 Pensionados", "99 Otro"], "02 Sueldos")}
            ${selectField("Riesgo del puesto", "taxRiskClass", ["Clase I", "Clase II", "Clase III", "Clase IV", "Clase V"], "Clase I")}
          </div>
        </div>

        <div class="form-section">
          <div class="form-section-title"><h4>Documentos</h4>${tag("Expediente digital", "blue")}</div>
          <div class="employee-document-list">
            ${requiredDocuments.map((doc, index) => `
              <div class="employee-document-row">
                <label class="employee-document-name">
                  <input type="checkbox" name="documents" value="${safe(doc)}" data-document-check="${index}" />
                  <span>${safe(doc)}</span>
                </label>
                <span class="employee-document-file" data-document-file-name="${index}">Sin archivo adjunto</span>
                <label class="btn secondary compact employee-document-upload">
                  ${icon("upload", "btn-icon")}Adjuntar
                  <input type="file" name="documentFile${index}" data-document-upload="${index}" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" />
                </label>
              </div>
            `).join("")}
          </div>
        </div>

        <div class="form-section">
          <div class="new-hire-contract-check">
            <label class="check-row">
              <input type="checkbox" name="newHire" value="1" />
              <span>Nuevo Ingreso</span>
            </label>
            <span class="small muted">El estatus de contrato aparecerá como Nuevo ingreso hasta que el empleado firme su contrato de ingreso.</span>
          </div>
          <div class="actions">
            <button class="btn" type="submit">${icon("check", "btn-icon")}Guardar empleado</button>
            <button class="btn secondary" type="button" data-route="employees">${icon("x", "btn-icon")}Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  `;
}

function renderEmployeeProfile() {
  const employee = employeeById(state.ui.selectedEmployeeId);
  return `
    <div class="screen-stack">
      ${pageHeader("Perfil del empleado", "", `
        <button class="btn secondary" data-route="employees">${icon("arrow-left", "btn-icon")}Atrás</button>
        <button class="btn" data-action="start-contract-process">${icon("file-signature", "btn-icon")}Crear contrato</button>
      `)}
      <div class="card">
        <div class="profile-head">
          ${avatar(employee, "large")}
          <div class="profile-title">
            <h3>${safe(employee.fullName)}</h3>
            <div class="muted">${safe(employee.position)} · ${safe(employee.department)} · ${safe(employee.number)}</div>
          </div>
          <div class="actions">${badge(employee.status)}${tag(employee.contractType, "blue")}</div>
        </div>
      </div>
      <div class="card">
        <div class="tabs">
          ${employeeTabs.map((tab) => `<button class="tab ${state.ui.employeeTab === tab ? "is-active" : ""}" data-action="set-employee-tab" data-tab="${safe(tab)}">${safe(tab)}</button>`).join("")}
        </div>
        <div style="padding-top:16px">${renderEmployeeTab(employee)}</div>
      </div>
    </div>
  `;
}

function renderEmployeeTab(employee) {
  const contracts = state.data.contracts.filter((contract) => contract.employeeId === employee.id);
  const receipts = state.data.receipts.filter((receipt) => receipt.employeeId === employee.id);
  const incidences = state.data.incidences.filter((item) => item.employeeId === employee.id);
  const vacations = state.data.vacations.filter((item) => item.employeeId === employee.id);

  if (state.ui.employeeTab === "Resumen") {
    return `
      <div class="grid two">
        <div class="grid">
          <div class="definition-grid">
            ${definition("Fecha de ingreso", date(employee.hireDate))}
            ${definition("Antigüedad", employee.seniority)}
            ${definition("Sueldo", money(employee.grossSalary))}
            ${definition("Jefe inmediato", employee.manager)}
            ${definition("Próximo pago", date(employee.nextPay))}
            ${definition("Días de vacaciones", employee.vacationDays)}
            ${definition("Contrato vigente", contracts[0]?.folio || "Sin contrato")}
            ${definition("Cuenta bancaria", `${employee.bank} · ${masked(employee.clabe)}`)}
          </div>
          <div class="panel">
            <h3>Línea de tiempo</h3>
            ${renderTimeline(employee.timeline)}
          </div>
        </div>
        <div class="grid">
          <div class="panel">
            <div class="split"><h3>Incidencias recientes</h3><button class="btn ghost" data-route="incidences">${icon("eye", "btn-icon")}Ver</button></div>
            ${incidences.slice(0, 4).map((item) => `<div class="task-item">${icon("calendar-alert", "mini-icon")}<div><strong>${safe(item.type)}</strong><div class="small muted">${date(item.date)} · ${money(item.amount)}</div></div>${badge(item.status)}</div>`).join("") || empty("Sin incidencias")}
          </div>
          <div class="panel">
            <div class="split"><h3>Últimos recibos</h3><button class="btn ghost" data-route="receipts">${icon("receipt", "btn-icon")}Ver</button></div>
            ${receipts.slice(0, 3).map((receipt) => `<div class="task-item">${icon("receipt", "mini-icon")}<div><strong>${safe(receipt.folio)}</strong><div class="small muted">${safe(receipt.period)} · ${date(receipt.payDate)}</div></div><strong>${money(receipt.net)}</strong></div>`).join("") || empty("Sin recibos")}
          </div>
        </div>
      </div>
    `;
  }

  if (state.ui.employeeTab === "Información personal") {
    return `<div class="definition-grid">${[
      ["Nombre", employee.fullName], ["CURP", employee.curp], ["RFC", employee.rfc], ["NSS", employee.nss],
      ["Estado civil", employee.civilStatus], ["Nacionalidad", employee.nationality], ["Teléfono", employee.phone],
      ["Correo", employee.email], ["Domicilio", employee.address], ["Contacto de emergencia", employee.emergencyContact]
    ].map(([label, value]) => definition(label, value)).join("")}</div>`;
  }

  if (state.ui.employeeTab === "Información laboral") {
    return `<div class="definition-grid">${[
      ["Empresa", employee.company], ["Sucursal", employee.branch], ["Departamento", employee.department], ["Puesto", employee.position],
      ["Modalidad", employee.modality], ["Jornada", employee.workday], ["Horario", employee.schedule], ["Días laborales", employee.workDays],
      ["Centro de trabajo", employee.workplace], ["Riesgo de puesto", employee.riskClass], ["Estatus", employee.status]
    ].map(([label, value]) => definition(label, value)).join("")}</div>`;
  }

  if (state.ui.employeeTab === "Contratos") return contracts.length ? renderContractsMini(contracts) : empty("Sin contratos registrados");
  if (state.ui.employeeTab === "Nómina") return renderPayrollMini(employee);
  if (state.ui.employeeTab === "Recibos") return receipts.length ? renderReceiptsMini(receipts) : empty("Sin recibos emitidos");
  if (state.ui.employeeTab === "Incidencias") return incidences.length ? renderIncidencesMini(incidences) : empty("Sin incidencias");
  if (state.ui.employeeTab === "Vacaciones") return vacations.length ? renderVacationsMini(vacations) : empty("Sin solicitudes de vacaciones");
  if (state.ui.employeeTab === "Documentos") return renderDocumentCards(employee);
  if (state.ui.employeeTab === "Historial") return renderTimeline(employee.timeline);
  return `${textareaField("Notas internas", "notes", "Empleado con expediente laboral vigente. Revisar documentos fiscales antes del siguiente timbrado.")}<div class="actions" style="margin-top:12px"><button class="btn" data-action="save-notes">${icon("check", "btn-icon")}Guardar notas</button></div>`;
}

function definition(label, value) {
  return `<div class="definition"><span>${safe(label)}</span><strong>${safe(value)}</strong></div>`;
}

function renderTimeline(items) {
  return `<div class="timeline">${items.map((item) => `
    <div class="timeline-item">
      <div class="timeline-dot"><strong class="small">${date(item.date)}</strong></div>
      <div><strong>${safe(item.title)}</strong><div class="small muted">${safe(item.detail)}</div></div>
    </div>
  `).join("")}</div>`;
}

function renderEmployeeDocuments() {
  const employee = employeeById(state.ui.selectedEmployeeId);
  return `
    <div class="screen-stack">
      ${pageHeader("Expediente documental", `${employee.fullName} · ${employee.number}`, `
        <button class="btn secondary" data-route="employees">${icon("arrow-left", "btn-icon")}Atrás</button>
        <button class="btn secondary" data-action="view-employee" data-id="${employee.id}">${icon("users", "btn-icon")}Perfil</button>
        <button class="btn" data-action="upload-document">${icon("upload", "btn-icon")}Subir documento</button>
      `)}
      ${renderDocumentCards(employee)}
    </div>
  `;
}

function renderDocumentCards(employee) {
  return `
    <div class="document-grid">
      ${employee.documents.map((doc) => `
        <article class="doc-card">
          <div class="doc-thumb"></div>
          <div class="split"><strong>${safe(doc.name)}</strong>${badge(doc.status)}</div>
          <span class="small muted">${date(doc.date)}</span>
          <div class="actions">
            <button class="icon-btn" data-tooltip="Ver">${icon("eye")}</button>
            <button class="icon-btn" data-tooltip="Descargar">${icon("download")}</button>
            <button class="icon-btn" data-tooltip="Reemplazar">${icon("upload")}</button>
          </div>
        </article>
      `).join("")}
    </div>
  `;
}

function renderContractsDashboard() {
  const contracts = state.data.contracts;
  const alertsOpen = Boolean(state.ui.contractAlertsOpen);
  const expiringContracts = sortedContractsByDueDate(contracts.filter((contract) => contract.endDate && ["Próximo a vencer", "Vencido"].includes(contract.status))).slice(0, 5);
  return `
    <div class="screen-stack contracts-dashboard">
      ${pageHeader("Dashboard de contratos", "Control de contratos activos, vencimientos, firmas, renovaciones y terminaciones.", `
        <button class="btn" data-action="start-contract-process">${icon("plus", "btn-icon")}Crear contrato</button>
        <button class="btn secondary" data-route="contracts-list">${icon("file", "btn-icon")}Listado</button>
      `)}
      <div class="contract-kpi-row">
        ${kpi("Contratos activos", contracts.filter((c) => c.status === "Activo").length, "Vigentes", "file-signature", "green")}
        ${kpi("Tiempo determinado", contracts.filter((c) => c.type === "Tiempo determinado").length, "Con fecha de término", "calendar-days", "amber")}
        ${kpi("Pendientes de firma", contracts.filter((c) => c.employeeSignature === "Pendiente").length, "Empleado o empresa", "edit", "blue")}
        ${kpi("Terminaciones en proceso", contracts.filter((c) => c.status === "Terminación").length || 2, "Finiquito pendiente", "logout", "red")}
        ${kpi("Periodo de prueba", contracts.filter((c) => c.type === "Periodo de prueba").length, "Seguimiento RH", "shield", "teal")}
        ${kpi("Próximos a vencer", contracts.filter((c) => c.status === "Próximo a vencer").length, "Alertas 90/60/30/15/7", "bell", "amber")}
        ${kpi("Vencidos", contracts.filter((c) => c.status === "Vencido").length, "Atención inmediata", "alert", "red")}
        ${kpi("Renovaciones", 6, "Pendientes", "refresh", "blue")}
      </div>
      <div class="card contract-alerts-panel ${alertsOpen ? "is-open" : "is-collapsed"}">
        <div class="split contract-alerts-header">
          <div class="contract-alerts-title">
            <h3>Alertas de vencimiento</h3>
            ${tag(expiringContracts.length, "blue")}
          </div>
          <div class="actions contract-alerts-actions">
            <button class="btn ghost" data-route="contract-renewal">${icon("refresh", "btn-icon")}Renovar</button>
            <button class="icon-btn contract-alerts-toggle" data-action="toggle-contract-alerts" data-tooltip="${alertsOpen ? "Ocultar información" : "Mostrar información"}" aria-label="${alertsOpen ? "Ocultar información" : "Mostrar información"}" aria-expanded="${alertsOpen}">${icon(alertsOpen ? "minus" : "plus")}</button>
          </div>
        </div>
        ${alertsOpen ? `
        <div class="table-wrap contract-alert-table">
          <table>
            <thead>
              <tr>
                <th>Folio</th>
                <th>ID empleado</th>
                <th>Empleado</th>
                <th>Empresa</th>
                <th>Puesto</th>
                <th>Tipo</th>
                <th>Inicio</th>
                <th>Término</th>
                <th>Días restantes</th>
                <th>Sueldo</th>
                <th>Estatus</th>
                <th>Firmas</th>
                <th>Modelo de contrato</th>
                <th>Renovar</th>
                <th>Autorizar</th>
                <th>Firmar</th>
              </tr>
            </thead>
            <tbody>
              ${expiringContracts.length ? expiringContracts.map((contract) => `
                <tr>
                  <td><strong>${safe(contract.folio)}</strong></td>
                  <td class="employee-id-column">${safe(employeeNumberById(contract.employeeId))}</td>
                  <td>${safe(contract.employee)}<div class="small muted">${safe(contract.department)}</div></td>
                  <td>${safe(contract.company)}</td>
                  <td>${safe(contract.position)}</td>
                  <td>${safe(contract.type)}</td>
                  <td>${date(contract.startDate)}</td>
                  <td>${contract.endDate ? date(contract.endDate) : "Indefinido"}</td>
                  <td>${tag(daysRemaining(contract.endDate), contract.status === "Vencido" ? "red" : "amber")}</td>
                  <td><span class="amount">${money(contract.salary)}</span></td>
                  <td>${statusButton(contract)}</td>
                  <td>${badge(contract.employeeSignature)} ${badge(contract.companySignature)}</td>
                  <td class="model-column">${contractModelCell(contract)}</td>
                  <td class="renew-column"><button class="btn secondary compact renew-contract-btn" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-renewal">${icon("refresh", "btn-icon")}Renovar contrato</button></td>
                  <td class="action-column"><button class="icon-btn" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-approval" data-tooltip="Autorización">${icon("check")}</button></td>
                  <td class="action-column"><button class="icon-btn" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-signature" data-tooltip="Firma">${icon("edit")}</button></td>
                </tr>
              `).join("") : `<tr><td colspan="16"><div class="empty-state">Sin alertas de vencimiento</div></td></tr>`}
            </tbody>
          </table>
        </div>
        ` : ""}
      </div>
      ${renderContractsList(true)}
    </div>
  `;
}

function contractDueSortValue(contract) {
  return contract.endDate ? new Date(`${contract.endDate}T12:00:00`).getTime() : Number.MAX_SAFE_INTEGER;
}

function sortedContractsByDueDate(contracts) {
  return [...contracts].sort((a, b) => {
    const byDate = contractDueSortValue(a) - contractDueSortValue(b);
    if (byDate !== 0) return byDate;
    return String(a.employee || "").localeCompare(String(b.employee || ""), "es");
  });
}

function expiringContractsForPanel() {
  return state.data.contracts
    .filter((contract) => contract.endDate && ["Próximo a vencer", "Vencido"].includes(contract.status))
    .sort((a, b) => {
      const byDate = contractDueSortValue(a) - contractDueSortValue(b);
      if (byDate !== 0) return byDate;
      return String(a.employee || "").localeCompare(String(b.employee || ""), "es");
    })
    .slice(0, 5);
}

function renderContractsDueStrip() {
  const expiringContracts = expiringContractsForPanel();
  const selectedId = Number(state.ui.selectedDueContractId || 0);
  return `
    <section class="card contracts-due-strip">
      <div class="split contracts-due-header">
        <div class="contract-alerts-title">
          <h3>Contratos próximos a vencer</h3>
          ${tag(expiringContracts.length, "blue")}
        </div>
        <button class="btn ghost compact" data-route="contract-renewal">${icon("refresh", "btn-icon")}Renovar</button>
      </div>
      <div class="contracts-due-carousel-shell">
        <button class="icon-btn carousel-arrow contracts-due-arrow" data-action="scroll-contracts-due-carousel" data-dir="-1" data-tooltip="Mover a la izquierda" aria-label="Mover contratos a la izquierda">
          <span class="carousel-arrow-symbol" aria-hidden="true">&lsaquo;</span>
        </button>
        <div class="contracts-due-row" data-contracts-due-carousel data-pan-carousel>
          ${expiringContracts.length ? expiringContracts.map((contract) => `
            <button class="contracts-due-card ${Number(contract.id) === selectedId ? "is-selected" : ""}" data-action="select-due-contract" data-id="${contract.id}">
              <span>
                <strong>${safe(contract.folio)}</strong>
                <small>${safe(employeeNumberById(contract.employeeId))} · ${safe(contract.employee)}</small>
                <small>${safe(contract.company)} · ${contract.endDate ? date(contract.endDate) : "Indefinido"}</small>
              </span>
              ${tag(daysRemaining(contract.endDate), contract.status === "Vencido" ? "red" : "amber")}
            </button>
          `).join("") : `<div class="empty-state compact">Sin contratos próximos a vencer</div>`}
        </div>
        <button class="icon-btn carousel-arrow contracts-due-arrow" data-action="scroll-contracts-due-carousel" data-dir="1" data-tooltip="Mover a la derecha" aria-label="Mover contratos a la derecha">
          <span class="carousel-arrow-symbol" aria-hidden="true">&rsaquo;</span>
          </button>
      </div>
      ${renderSelectedDueContractPanel()}
    </section>
  `;
}

function renderSelectedDueContractPanel() {
  const selectedId = Number(state.ui.selectedDueContractId || 0);
  const contract = selectedId ? expiringContractsForPanel().find((item) => Number(item.id) === selectedId) : null;
  if (!contract) return "";

  return `
    <div class="contracts-due-detail">
      <div class="split contracts-due-detail-header">
        <div>
          <h4>Contrato seleccionado para renovación</h4>
          <p>${safe(contract.folio)} · ${safe(employeeNumberById(contract.employeeId))} · ${safe(contract.employee)}</p>
        </div>
        <button class="icon-btn" data-action="close-due-contract" data-tooltip="Cerrar información" aria-label="Cerrar información del contrato">${icon("x")}</button>
      </div>
      <div class="table-wrap contracts-due-detail-table">
        <table>
          <thead>
            <tr>
              <th>Folio</th>
              <th>ID empleado</th>
              <th>Empleado</th>
              <th>Empresa</th>
              <th>Puesto</th>
              <th>Tipo</th>
              <th>Inicio</th>
              <th>Término</th>
              <th>Días restantes</th>
              <th>Sueldo</th>
              <th>Estatus</th>
              <th>Firmas</th>
              <th>Contrato firmado</th>
              <th>Modelo de contrato</th>
              <th>Renovar</th>
              <th>Autorizar</th>
              <th>Firmar</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>${safe(contract.folio)}</strong></td>
              <td class="employee-id-column">${safe(employeeNumberById(contract.employeeId))}</td>
              <td><strong>${safe(contract.employee)}</strong><div class="small muted">${safe(contract.department)}</div></td>
              <td>${safe(contract.company)}</td>
              <td>${safe(contract.position)}</td>
              <td>${safe(contract.type)}</td>
              <td>${date(contract.startDate)}</td>
              <td>${contract.endDate ? date(contract.endDate) : "Indefinido"}</td>
              <td>${tag(daysRemaining(contract.endDate), contract.status === "Vencido" ? "red" : "amber")}</td>
              <td><span class="amount">${money(contract.salary)}</span></td>
              <td>${statusButton(contract)}</td>
              <td>${badge(contract.employeeSignature)} ${badge(contract.companySignature)}</td>
              <td class="signed-contract-column">${signedContractCell(contract)}</td>
              <td class="model-column">${contractModelCell(contract)}</td>
              <td class="renew-column"><button class="btn secondary compact renew-contract-btn" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-renewal">${icon("refresh", "btn-icon")}Renovar contrato</button></td>
              <td class="action-column"><button class="icon-btn" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-approval" data-tooltip="Autorización">${icon("check")}</button></td>
              <td class="action-column"><button class="icon-btn" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-signature" data-tooltip="Firma">${icon("edit")}</button></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function renderPendingApprovals() {
  const rows = sortedContractsByDueDate(pendingLegalApprovalContracts()).map((contract) => {
    const legalStep = legalApprovalStep(contract);
    const approvalLabel = legalStep?.status === "Rechazado" ? "Rechazado por Dirección Jurídica" : "Pendiente Dirección Jurídica";
    return {
      ...contract,
      approvalLabel,
      employeeNumber: employeeNumberById(contract.employeeId),
      _search: `${contract.folio} ${employeeNumberById(contract.employeeId)} ${contract.employee} ${contract.company} ${contract.position} ${contract.type} ${approvalLabel} ${contract.department} ${contract.branch}`
    };
  });

  const columns = [
    { key: "folio", label: "Folio" },
    { key: "employeeNumber", label: "ID empleado", tdClassName: "employee-id-column" },
    { key: "employee", label: "Empleado", render: (row) => `<strong>${safe(row.employee)}</strong><div class="small muted">${safe(row.department)}</div>` },
    { key: "company", label: "Empresa" },
    { key: "position", label: "Puesto" },
    { key: "type", label: "Tipo" },
    { key: "startDate", label: "Inicio", render: (row) => date(row.startDate), filterType: "date-tree", filterValue: (row) => row.startDate },
    { key: "endDate", label: "Término", render: (row) => row.endDate ? date(row.endDate) : "Indefinido", filterType: "date-tree", filterValue: (row) => row.endDate || "Indefinido" },
    { key: "days", label: "Días restantes", render: (row) => daysRemaining(row.endDate), sortValue: (row) => row.endDate ? new Date(row.endDate).getTime() : 9999999999999 },
    { key: "salary", label: "Sueldo", render: (row) => `<span class="amount">${money(row.salary)}</span>`, sortValue: (row) => row.salary },
    { key: "approvalLabel", label: "Estatus", render: (row) => tag(row.approvalLabel, row.approvalLabel.includes("Rechazado") ? "red" : "amber") },
    { key: "signatures", label: "Firmas", render: (row) => `${badge(row.employeeSignature)} ${badge(row.companySignature)}`, sortable: false, filterValue: (row) => `${row.employeeSignature} / ${row.companySignature}` },
    { key: "review", label: "Acción", sortable: false, className: "text-center", render: (row) => `<button class="btn secondary compact" data-action="select-contract-route" data-id="${row.id}" data-route-target="contract-approval">${icon("check", "btn-icon")}Revisar</button>` }
  ];

  const table = renderTable({
    id: "pending-approvals",
    rows,
    columns,
    searchPlaceholder: "Buscar folio, empleado, tipo o estatus",
    filters: [
      { key: "type", label: "Tipo", options: state.data.contractTypes, getValue: (row) => row.type },
      { key: "status", label: "Estatus", options: ["Pendiente Dirección Jurídica", "Rechazado por Dirección Jurídica"], getValue: (row) => row.approvalLabel },
      { key: "department", label: "Departamento", options: state.data.departments, getValue: (row) => row.department },
      { key: "branch", label: "Sucursal", options: state.data.branches, getValue: (row) => row.branch }
    ],
    pageSize: rows.length || 8,
    paginate: false,
    scrollY: "calc(100vh - 260px)",
    emptyMessage: "No hay contratos pendientes de aprobación por Dirección Jurídica."
  });

  return `<div class="screen-stack">${pageHeader("Pendientes de Aprobación", "Contratos enviados a Dirección Jurídica antes de imprimir o pasar a firma.", `
    <button class="btn secondary" data-route="contracts-drafts">${icon("file", "btn-icon")}En Elaboración</button>
    <button class="btn secondary" data-route="contracts">${icon("arrow-left", "btn-icon")}Contratos</button>
  `)}${table}</div>`;
}

function renderContractsList(embedded = false) {
  const rows = sortedContractsByDueDate(state.data.contracts).map((contract) => ({
    ...contract,
    displayStatus: displayContractStatus(contract),
    employeeNumber: employeeNumberById(contract.employeeId),
    _search: `${contract.folio} ${employeeNumberById(contract.employeeId)} ${contract.employee} ${contract.company} ${contract.position} ${contract.type} ${displayContractStatus(contract)} ${contract.department} ${contract.branch}`
  }));
  const columns = [
    { key: "folio", label: "Folio" },
    { key: "employeeNumber", label: "ID empleado", tdClassName: "employee-id-column" },
    { key: "employee", label: "Empleado", render: (row) => `<strong>${safe(row.employee)}</strong><div class="small muted">${safe(row.department)}</div>` },
    { key: "company", label: "Empresa" },
    { key: "position", label: "Puesto" },
    { key: "type", label: "Tipo" },
    { key: "startDate", label: "Inicio", render: (row) => date(row.startDate), filterType: "date-tree", filterValue: (row) => row.startDate },
    { key: "endDate", label: "Término", render: (row) => row.endDate ? date(row.endDate) : "Indefinido", filterType: "date-tree", filterValue: (row) => row.endDate || "Indefinido" },
    { key: "days", label: "Días restantes", render: (row) => daysRemaining(row.endDate), sortValue: (row) => row.endDate ? new Date(row.endDate).getTime() : 9999999999999 },
    { key: "salary", label: "Sueldo", render: (row) => `<span class="amount">${money(row.salary)}</span>`, sortValue: (row) => row.salary },
    { key: "status", label: "Estatus", render: (row) => statusButton(row) },
    { key: "signatures", label: "Firmas", render: (row) => `${badge(row.employeeSignature)} ${badge(row.companySignature)}`, sortable: false, filterValue: (row) => `${row.employeeSignature} / ${row.companySignature}` },
    { key: "signedContract", label: "Contrato firmado", sortable: false, tdClassName: "signed-contract-column", render: (row) => signedContractCell(row), filterValue: (row) => signedContractFileName(row) || "Pendiente" },
    { key: "contractModel", label: "Modelo de contrato", sortable: false, tdClassName: "model-column", render: (row) => contractModelCell(row), filterValue: (row) => contractModel(row).name },
    { key: "renewAction", label: "Renovar", sortable: false, className: "text-center", tdClassName: "renew-column", render: (row) => `<button class="btn secondary compact renew-contract-btn" data-action="select-contract-route" data-id="${row.id}" data-route-target="contract-renewal">${icon("refresh", "btn-icon")}Renovar contrato</button>` },
    { key: "authorizeAction", label: "Autorizar", sortable: false, className: "text-center", tdClassName: "action-column", render: (row) => `<button class="icon-btn" data-action="select-contract-route" data-id="${row.id}" data-route-target="contract-approval" data-tooltip="Autorización">${icon("check")}</button>` },
    { key: "signatureAction", label: "Firmar", sortable: false, className: "text-center", tdClassName: "action-column", render: (row) => `<button class="icon-btn" data-action="select-contract-route" data-id="${row.id}" data-route-target="contract-signature" data-tooltip="Firma">${icon("edit")}</button>` }
  ];

  const table = renderTable({
    id: embedded ? "contracts-preview" : "contracts-list",
    rows,
    columns,
    searchPlaceholder: "Buscar folio, empleado, tipo o estatus",
    filters: [
      { key: "type", label: "Tipo", options: state.data.contractTypes, getValue: (row) => row.type },
      { key: "status", label: "Estatus", options: ["Nuevo ingreso", "Activo", "Pendiente de firma", "En aprobación", "Próximo a vencer", "Vencido"], getValue: (row) => row.displayStatus },
      { key: "department", label: "Departamento", options: state.data.departments, getValue: (row) => row.department },
      { key: "branch", label: "Sucursal", options: state.data.branches, getValue: (row) => row.branch }
    ],
    pageSize: embedded ? 5 : rows.length,
    paginate: embedded,
    scrollY: embedded ? "" : "calc(100vh - 320px)"
  });

  if (embedded) return table;
  return `<div class="screen-stack">${pageHeader("Listado de contratos", "Búsqueda por empleado, folio, tipo, estatus, fechas, departamento y sucursal.", `
    <button class="btn" data-action="start-contract-process">${icon("plus", "btn-icon")}Crear contrato</button>
    <button class="btn secondary" data-route="contracts-drafts">${icon("file", "btn-icon")}En Elaboración</button>
    <button class="btn secondary" data-route="template-editor">${icon("file-signature", "btn-icon")}Plantillas</button>
  `)}${renderContractsDueStrip()}${table}</div>`;
}

function renderContractDrafts() {
  const drafts = state.data.contractDrafts || [];
  const rows = drafts.map((process) => {
    const draft = process.draft || process;
    const employee = employeeById(draft.employeeId || process.employeeId);
    const currentStep = Math.min(Math.max(Number(process.step || draft.step || 1), 1), contractSteps.length);
    return {
      id: process.id,
      folio: process.folio || draft.folio || "Sin folio",
      employeeId: employee?.id,
      employeeNumber: employee?.number || employeeNumberById(draft.employeeId || process.employeeId),
      employee: employee?.fullName || process.employee || "Sin empleado",
      company: draft.company || process.company || employee?.company || "Sin empresa",
      branch: process.branch || employee?.branch || "Sin sucursal",
      department: draft.department || process.department || employee?.department || "Sin departamento",
      position: draft.position || process.position || employee?.position || "Sin puesto",
      type: draft.type || process.type || "Sin tipo",
      stepLabel: contractSteps[currentStep - 1] || "Seleccionar persona",
      updatedAt: process.updatedAt || draft.updatedAt || today(),
      status: process.status || "En elaboración",
      _search: `${process.folio || draft.folio || ""} ${employee?.number || ""} ${employee?.fullName || ""} ${draft.company || ""} ${draft.position || ""} ${draft.type || ""} ${process.status || "En elaboración"}`
    };
  });

  const columns = [
    { key: "folio", label: "Folio" },
    { key: "employeeNumber", label: "ID empleado", tdClassName: "employee-id-column" },
    { key: "employee", label: "Empleado", render: (row) => `<strong>${safe(row.employee)}</strong><div class="small muted">${safe(row.department)}</div>` },
    { key: "company", label: "Empresa" },
    { key: "branch", label: "Sucursal" },
    { key: "position", label: "Puesto" },
    { key: "type", label: "Tipo" },
    { key: "stepLabel", label: "Paso actual" },
    { key: "updatedAt", label: "Última actualización", render: (row) => date(row.updatedAt), filterType: "date-tree", filterValue: (row) => row.updatedAt },
    { key: "status", label: "Estatus", render: () => tag("En elaboración", "amber"), filterValue: () => "En elaboración" },
    { key: "continue", label: "Acción", sortable: false, className: "text-center", render: (row) => `<button class="btn secondary compact" data-action="continue-contract-process" data-id="${safe(row.id)}">${icon("arrow-right", "btn-icon")}Continuar</button>` }
  ];

  const table = renderTable({
    id: "contract-drafts",
    rows,
    columns,
    searchPlaceholder: "Buscar folio, empleado, empresa o estatus",
    filters: [
      { key: "company", label: "Empresa", options: state.data.companies, getValue: (row) => row.company },
      { key: "type", label: "Tipo", options: state.data.contractTypes, getValue: (row) => row.type },
      { key: "department", label: "Departamento", options: state.data.departments, getValue: (row) => row.department },
      { key: "branch", label: "Sucursal", options: state.data.branches, getValue: (row) => row.branch }
    ],
    pageSize: rows.length || 8,
    paginate: false,
    scrollY: "calc(100vh - 310px)",
    emptyMessage: "No hay contratos en elaboración. Crea un contrato y usa Guardar proceso para verlo aquí."
  });

  return `<div class="screen-stack">${pageHeader("Contratos en elaboración", "Procesos guardados para continuar contratos antes de enviarlos a aprobación.", `
    <button class="btn" data-action="start-contract-process">${icon("plus", "btn-icon")}Crear contrato</button>
    <button class="btn secondary" data-route="contracts">${icon("arrow-left", "btn-icon")}Contratos</button>
  `)}${table}</div>`;
}
function renderContractWizard() {
  const step = Math.min(state.ui.contractStep, contractSteps.length);
  return `
    <div class="screen-stack">
      ${pageHeader("Crear contrato", "Asistente de contratación con aprobación y guardado en expediente.", `
        <button class="btn secondary" data-route="employees">${icon("arrow-left", "btn-icon")}Atrás</button>
        <button class="btn secondary" data-route="contracts">${icon("x", "btn-icon")}Salir</button>
      `)}
      <div class="card contract-stepper-card">${renderContractStepper(step)}</div>
      <div class="form-card">
        ${renderContractStepContent(step)}
        <div class="form-section">
          <div class="actions">
            <button class="btn secondary" data-action="prev-contract-step" ${step === 1 ? "disabled" : ""}>${icon("arrow-right", "btn-icon")}Anterior</button>
            ${step < contractSteps.length ? `<button class="btn" data-action="next-contract-step">${icon("arrow-right", "btn-icon")}Siguiente</button>` : `<button class="btn success" data-action="save-contract-active">${icon("check", "btn-icon")}Guardar contrato</button>`}
            <button class="btn secondary" data-action="save-contract-process">${icon("file", "btn-icon")}Guardar proceso</button>
          </div>
        </div>
      </div>
    </div>
  `;
}

function renderContractStepper(current) {
  return `<div class="stepper contract-stepper">${contractSteps.map((label, index) => {
    const step = index + 1;
    return `<div class="step ${step < current ? "is-done" : step === current ? "is-active" : ""}"><span class="step-number">${step}</span><span>${safe(label)}</span></div>`;
  }).join("")}</div>`;
}

function renderContractStepContent(step) {
  const draft = state.ui.contractDraft;
  const employee = employeeById(draft.employeeId);
  if (step === 1) {
    const personType = state.ui.contractPersonType === "employee" ? "employee" : "candidate";
    const allEmployeeOptions = state.data.employees
      .filter((item) => item.status === "Activo")
      .sort((a, b) => String(b.hireDate || "").localeCompare(String(a.hireDate || "")) || Number(b.id) - Number(a.id));
    const candidateOptions = [...(state.data.candidates || [])]
      .sort((a, b) => String(b.registeredAt || b.lastUpdate || "").localeCompare(String(a.registeredAt || a.lastUpdate || "")) || Number(b.id) - Number(a.id));
    const isNewHireEmployee = (item) => displayEmployeeContractStatus(item) === "Nuevo ingreso";
    const newHireEmployeeOptions = allEmployeeOptions.filter(isNewHireEmployee);
    const newHireOnly = personType === "employee" && Boolean(state.ui.contractEmployeeNewHireOnly);
    const employeeOptions = newHireOnly ? newHireEmployeeOptions : allEmployeeOptions;
    const personOptions = personType === "candidate" ? candidateOptions : employeeOptions;
    const companyOptions = [...new Set(personOptions.map((item) => item.company).filter(Boolean))].sort();
    return `
      <div class="form-section">
        <div class="contract-person-carousel" aria-label="Tipo de persona para el contrato">
          <button type="button" class="contract-person-option ${personType === "candidate" ? "is-active" : ""}" data-action="set-contract-person-type" data-person-type="candidate" aria-pressed="${personType === "candidate"}">
            <span class="contract-person-icon">${icon("badge-user")}</span>
            <span class="contract-person-copy"><strong>Candidato</strong><span>${candidateOptions.length} disponibles</span></span>
          </button>
          <button type="button" class="contract-person-option ${personType === "employee" ? "is-active" : ""}" data-action="set-contract-person-type" data-person-type="employee" aria-pressed="${personType === "employee"}">
            <span class="contract-person-icon">${icon("users")}</span>
            <span class="contract-person-copy"><strong>Empleado</strong><span>${allEmployeeOptions.length} activos</span></span>
          </button>
        </div>
        <div class="form-section-title contract-employee-title">
          <div class="contract-employee-heading">
            <h4>Paso 1. Seleccionar candidato o empleado</h4>
            <div class="contract-employee-controls">
              ${personType === "employee" ? `<button class="btn secondary compact contract-new-hire-filter ${newHireOnly ? "is-active" : ""}" data-action="toggle-new-hire-contract-employees">
                ${icon("users", "btn-icon")}Nuevo Ingreso <span class="button-count">${newHireEmployeeOptions.length}</span>
              </button>` : ""}
              <label class="contract-employee-search">
                <span>Buscar ${personType === "candidate" ? "candidato" : "empleado"}</span>
                <input class="input" type="search" data-contract-person-search placeholder="Nombre, ID, puesto o empresa" autocomplete="off" />
              </label>
              <label class="contract-employee-filter">
                <span>Empresa</span>
                <select class="select" data-contract-person-company-filter>
                  <option value="">Todas las empresas</option>
                  ${companyOptions.map((company) => `<option value="${safe(company)}">${safe(company)}</option>`).join("")}
                </select>
              </label>
            </div>
          </div>
          ${tag(personType === "candidate" ? "Candidato" : "Empleado", personType === "candidate" ? "amber" : "blue")}
        </div>
        <div class="table-wrap table-wrap-vertical contract-employee-table-wrap" style="--table-scroll-y: 520px;">
          <table class="data-table contract-employee-table">
            <thead>
              ${personType === "candidate" ? `
                <tr><th>ID candidato</th><th>Candidato</th><th>Empresa</th><th>Registro</th></tr>
              ` : `
                <tr><th>ID empleado</th><th>Empleado</th><th>Empresa</th><th>Sucursal</th></tr>
              `}
            </thead>
            <tbody data-contract-person-list>
              ${personType === "candidate" ? candidateOptions.map((item) => `
                <tr class="contract-employee-row ${Number(draft.candidateId) === Number(item.id) ? "is-selected" : ""}" data-action="draft-candidate" data-id="${item.id}" data-contract-person-row data-new-hire="0" data-company="${safe(item.company || "")}" data-search="${safe(`${item.name} CAN-${String(item.id).padStart(5, "0")} ${item.position} ${item.company} ${item.email}`)}">
                  <td><strong>CAN-${String(item.id).padStart(5, "0")}</strong></td>
                  <td>
                    <div class="employee-cell">
                      ${avatar({ ...item, fullName: item.name })}
                      <span><strong>${safe(item.name)}</strong><span class="small muted">${safe(item.position)} | ${safe(item.email)}</span></span>
                    </div>
                  </td>
                  <td>${safe(item.company || "Sin empresa")}</td>
                  <td>${safe(date(item.registeredAt))}</td>
                </tr>
              `).join("") : employeeOptions.map((item) => `
                <tr class="contract-employee-row ${String(draft.employeeId) === String(item.id) && !draft.candidateId ? "is-selected" : ""}" data-action="draft-employee" data-id="${item.id}" data-contract-person-row data-new-hire="${isNewHireEmployee(item) ? "1" : "0"}" data-company="${safe(item.company)}" data-search="${safe(`${item.fullName} ${item.number} ${item.position} ${item.department} ${item.company} ${item.branch}`)}">
                  <td><strong>${safe(item.number)}</strong></td>
                  <td>
                    <div class="employee-cell">
                      ${avatar(item)}
                      <span><strong>${safe(item.fullName)}</strong><span class="small muted">${safe(item.position)} | ${safe(item.department)}</span></span>
                    </div>
                  </td>
                  <td>${safe(item.company)}</td>
                  <td>${safe(item.branch)}</td>
                </tr>
              `).join("")}
            </tbody>
          </table>
        </div>
        <div class="empty-state" data-contract-person-empty ${personOptions.length ? "hidden" : ""}>${personType === "candidate" ? "No se encontraron candidatos." : newHireOnly ? "No hay empleados marcados como Nuevo Ingreso." : "No se encontraron empleados."}</div>
        <div class="actions"><button class="btn secondary" data-route="${personType === "candidate" ? "candidates" : "employee-new"}">${icon("plus", "btn-icon")}Registrar ${personType === "candidate" ? "candidato" : "empleado"}</button></div>
      </div>
    `;
  }
  if (step === 2) {
    return `
      <div class="form-section">
        <div class="form-section-title"><h4>Paso 2. Datos generales</h4>${tag(employee.fullName, "teal")}</div>
        <div class="form-grid">
          ${selectField("Tipo de contrato", "type", state.data.contractTypes, draft.type, true, 'data-draft="type"')}
          ${field("Folio", "folio", "text", ensureContractDraftFolio(draft), true, 'data-draft="folio" readonly aria-readonly="true"')}
          ${selectField("Empresa contratante", "company", state.data.companies, draft.company, true, 'data-draft="company"')}
          ${field("Representante legal", "legalRep", "text", draft.legalRep, true, 'data-draft="legalRep"')}
          ${field("Lugar de firma", "signingPlace", "text", draft.signingPlace, true, 'data-draft="signingPlace"')}
          ${field("Fecha de firma", "signDate", "date", draft.signDate, true, 'data-draft="signDate"')}
          ${field("Fecha de inicio", "startDate", "date", draft.startDate, true, 'data-draft="startDate"')}
          ${field("Fecha de vencimiento", "endDate", "date", draft.endDate, false, 'data-draft="endDate"')}
          ${field("Periodo de prueba", "trialPeriod", "text", draft.trialPeriod, false, 'data-draft="trialPeriod"')}
        </div>
        <div class="new-hire-contract-check">
          <label class="check-row">
            <input type="checkbox" data-draft="newHire" ${draft.newHire ? "checked" : ""} />
            <span>Nuevo Ingreso</span>
          </label>
          <span class="small muted">El contrato se mostrará como Nuevo ingreso hasta que el empleado firme su contrato de ingreso.</span>
        </div>
      </div>
    `;
  }
  if (step === 3) {
    return `
      <div class="form-section">
        <div class="form-section-title"><h4>Paso 3. Condiciones laborales</h4>${tag("Trabajo", "blue")}</div>
        <div class="form-grid">
          ${field("Puesto", "position", "text", draft.position, true, 'data-draft="position" placeholder="Escribe el puesto"')}
          ${textareaField("Funciones", "functions", "Actividades propias del puesto, cumplimiento de objetivos y reportes administrativos.", 'data-draft="functions"')}
          ${selectField("Departamento", "department", state.data.departments, draft.department, true, 'data-draft="department"')}
          ${field("Centro de trabajo", "workplace", "text", employee.workplace, false, 'data-draft="workplace"')}
          ${selectField("Jornada", "workday", ["Diurna", "Nocturna", "Mixta"], employee.workday, false, 'data-draft="workday"')}
          ${field("Horario", "schedule", "text", employee.schedule, false, 'data-draft="schedule"')}
          ${field("Días laborales", "workDays", "text", employee.workDays, false, 'data-draft="workDays"')}
          ${field("Lugar de prestación de servicios", "servicePlace", "text", employee.branch, false, 'data-draft="servicePlace"')}
          ${selectField("Modalidad de trabajo", "modality", ["Presencial", "Remota", "Híbrida"], employee.modality, false, 'data-draft="modality"')}
          ${textareaField("Herramientas asignadas", "tools", "Laptop, correo corporativo, acceso a sistemas internos.", 'data-draft="tools"')}
        </div>
      </div>
    `;
  }
  if (step === 4) {
    return `
      <div class="form-section">
        <div class="form-section-title"><h4>Paso 4. Condiciones económicas</h4>${tag("Compensación", "green")}</div>
        <div class="form-grid">
          ${field("Sueldo bruto", "salary", "number", draft.salary, true, 'data-draft="salary" min="0" step="0.01"')}
          ${selectField("Periodicidad de pago", "frequency", ["Semanal", "Catorcenal", "Quincenal", "Mensual"], draft.frequency, false, 'data-draft="frequency"')}
          ${bonusField(draft)}
          ${field("Comisiones", "commissions", "text", draft.commissions ?? "Variable por cumplimiento", false, 'data-draft="commissions"')}
          ${field("Prestaciones", "benefits", "text", "Superiores a la ley", false, 'data-draft="benefits"')}
          ${field("Condición de bonos", "bonusCondition", "text", draft.bonusCondition ?? "Pago sujeto a cumplimiento de metas mensuales", false, 'data-draft="bonusCondition"')}
          ${field("Condición de comisiones", "commissionCondition", "text", draft.commissionCondition ?? "Comisión variable conforme a resultados autorizados", false, 'data-draft="commissionCondition"')}
          ${field("Vales", "vouchers", "number", "1400", false, 'data-draft="vouchers"')}
          ${field("Fondo de ahorro", "savingsFund", "text", "6% empleado y 6% empresa", false, 'data-draft="savingsFund"')}
          ${field("Seguro", "insurance", "text", "Gastos médicos mayores", false, 'data-draft="insurance"')}
          ${field("Otros beneficios", "extraBenefits", "text", "Capacitación y bienestar", false, 'data-draft="extraBenefits"')}
        </div>
      </div>
    `;
  }
  if (step === 5) {
    const clauses = ["Confidencialidad", "Propiedad intelectual", "Protección de datos", "Uso de herramientas", "No competencia", "Responsabilidad", "Terminación", "Avisos", "Jurisdicción"];
    return `
      <div class="form-section">
        <div class="form-section-title"><h4>Paso 5. Cláusulas</h4>${tag("Activar o desactivar", "amber")}</div>
        <div class="check-grid">
          ${clauses.map((clause) => `<label class="check-row"><input type="checkbox" data-action="toggle-draft-clause" value="${safe(clause)}" ${draft.clauses.includes(clause) ? "checked" : ""} />${safe(clause)}</label>`).join("")}
        </div>
      </div>
    `;
  }
  if (step === 6) {
    const model = contractDraftModel(draft);
    return `
      <div class="form-section">
        <div class="form-section-title">
          <h4>Paso 6. Vista previa</h4>
          <div class="actions contract-download-actions">
            ${tag(draft.folio, "blue")}
            ${tag(model.name, "teal")}
            <button class="btn secondary compact" data-action="download-draft-contract" data-format="pdf">${icon("download", "btn-icon")}PDF</button>
            <button class="btn secondary compact" data-action="download-draft-contract" data-format="word">${icon("file", "btn-icon")}Word</button>
          </div>
        </div>
        ${contractPreview(draft, employee)}
      </div>
    `;
  }
  if (step === 7) {
    const approvalFlow = ["Elaborado por Recursos Humanos", "Aprobación Dirección Jurídica"];
    return `
      <div class="form-section">
        <div class="form-section-title"><h4>Paso 7. Aprobaciones</h4>${tag("Flujo estándar", "green")}</div>
        <div class="workflow-line">
          ${approvalFlow.map((label, index) => `
            <div class="flow-node ${index === 0 ? "is-done" : "is-current"}">
              <span class="step-number">${index + 1}</span>
              <strong>${safe(label)}</strong>
              ${badge(index === 0 ? "Aprobado" : "Pendiente")}
            </div>
          `).join("")}
        </div>
      </div>
    `;
  }
  return empty("Paso no disponible");
}

function contractDraftModel(draft) {
  const inferredName = draft.type === "Tiempo indeterminado" ? "Contrato por tiempo indeterminado" : "Contrato por tiempo determinado";
  const template = findTemplateByName(draft.contractModelName) || findTemplateByName(inferredName) || state.data.templates[0];
  const name = template?.name || draft.contractModelName || inferredName;
  return {
    name,
    version: draft.contractModelVersion || template?.version || "v1.0",
    file: draft.contractModelFile || template?.sourceFileName || `${name}.docx`,
    body: template?.body || ""
  };
}

function contractDocumentData(draft, employee) {
  const model = contractDraftModel(draft);
  const endText = draft.endDate ? `y terminara el ${date(draft.endDate)}` : "por tiempo indeterminado";
  const clauses = draft.clauses?.length ? draft.clauses : ["Sin clausulas activas"];
  const bonusesNotApplicable = Boolean(draft.bonusesNotApplicable);
  const bonuses = bonusesNotApplicable ? 0 : Number(draft.bonuses || 0);
  const bonusesText = bonusesNotApplicable ? "No aplica" : money(bonuses);
  const bonusConditionText = bonusesNotApplicable ? "No aplica" : draft.bonusCondition || "No especificada";
  return {
    model,
    title: `${model.name} - ${safe(draft.folio)}`,
    details: [
      ["Modelo", `${model.name} (${model.version})`],
      ["Empleado", `${employee.fullName} - ${employee.number}`],
      ["CURP", employee.curp],
      ["RFC", employee.rfc],
      ["NSS", employee.nss],
      ["Domicilio", employee.address],
      ["Empresa", draft.company],
      ["Sucursal", employee.branch],
      ["Puesto", draft.position],
      ["Departamento", draft.department],
      ["Centro de trabajo", employee.workplace],
      ["Jornada", employee.workday],
      ["Horario", employee.schedule],
      ["Modalidad", employee.modality],
      ["Sueldo bruto mensual", money(draft.salary)],
      ["Sueldo diario", money(Number(draft.salary || 0) / 30)],
      ["Periodicidad de pago", draft.frequency],
      ["Bonos", bonusesText],
      ["Condicion de bonos", bonusConditionText],
      ["Comisiones", draft.commissions || "No aplica"],
      ["Condicion de comisiones", draft.commissionCondition || "No especificada"],
      ["Prestaciones", draft.benefits || "Superiores a la ley"]
    ],
    paragraphs: [
      `Entre ${draft.company}, representada por ${draft.legalRep}, y ${employee.fullName}, se celebra el presente contrato individual de trabajo para desempenar el puesto de ${draft.position} en el departamento de ${draft.department}.`,
      `La relacion laboral iniciara el ${date(draft.startDate)} ${endText}, con centro de trabajo en ${employee.workplace} y prestacion de servicios en ${employee.branch}.`,
      `La persona trabajadora prestara sus servicios en modalidad ${employee.modality}, jornada ${employee.workday}, horario ${employee.schedule} y dias laborales ${employee.workDays}.`,
      `La empresa pagara un sueldo bruto mensual de ${money(draft.salary)} con periodicidad ${draft.frequency}. ${bonusesNotApplicable ? "No aplican bonos para este contrato." : `Los bonos seran de ${money(bonuses)} bajo la condicion: ${draft.bonusCondition || "No especificada"}.`} Las comisiones se aplicaran como ${draft.commissions || "No aplica"} bajo la condicion: ${draft.commissionCondition || "No especificada"}.`,
      `Clausulas activas del modelo: ${clauses.join(", ")}.`,
      `Lugar y fecha de firma: ${draft.signingPlace}, ${date(draft.signDate)}.`
    ],
    clauses
  };
}

function contractPreview(draft, employee) {
  const doc = contractDocumentData(draft, employee);
  return `
    <article class="contract-preview">
      <div class="contract-preview-header">
        <div>
          <p class="eyebrow">Modelo de contrato</p>
          <h4>${safe(doc.model.name)}</h4>
          <p>${safe(doc.model.version)} · ${safe(doc.model.file)}</p>
        </div>
        <span class="contract-folio">${safe(draft.folio)}</span>
      </div>
      <div class="contract-preview-meta">
        ${doc.details.map(([label, value]) => `<div><span>${safe(label)}</span><strong>${safe(value)}</strong></div>`).join("")}
      </div>
      <section class="contract-preview-section">
        <h5>Texto del contrato</h5>
        ${doc.paragraphs.map((paragraph) => `<p>${safe(paragraph)}</p>`).join("")}
      </section>
      <section class="contract-preview-section">
        <h5>Clausulas seleccionadas</h5>
        <div class="clause-chip-list">${doc.clauses.map((clause) => `<span>${safe(clause)}</span>`).join("")}</div>
      </section>
      <div class="contract-signature-grid">
        <div><span>Firma del empleado</span><strong>${safe(employee.fullName)}</strong></div>
        <div><span>Representante legal</span><strong>${safe(draft.legalRep)}</strong></div>
      </div>
    </article>
  `;
}

function renderContractApproval() {
  const contract = contractById(state.ui.selectedContractId);
  return `
    <div class="screen-stack">
      ${pageHeader("Flujo de autorización", `${contract.folio} · ${contract.employee}`, `
        <button class="btn" data-action="advance-contract-approval">${icon("check", "btn-icon")}Aprobar siguiente</button>
        <button class="btn secondary" data-route="contracts-list">${icon("file", "btn-icon")}Contratos</button>
      `)}
      <div class="card">
        <div class="workflow-line">
          ${contract.approvals.map((step, index) => `
            <div class="flow-node ${step.status === "Aprobado" ? "is-done" : step.status === "En revisión" || step.status === "Pendiente" ? "is-current" : ""}">
              <span class="step-number">${index + 1}</span>
              <strong>${safe(step.step)}</strong>
              ${badge(step.status)}
              <span class="small muted">${safe(step.user || "Sin usuario")} · ${step.date ? date(step.date) : "Sin fecha"}</span>
            </div>
          `).join("")}
        </div>
      </div>
      <div class="grid two">
        ${contractPreview(contractToDraft(contract), employeeById(contract.employeeId))}
        <div class="panel">
          <h3>Registro de aprobación</h3>
          <div class="definition-grid">
            ${definition("Usuario", state.ui.role)}
            ${definition("Fecha", date(today()))}
            ${definition("Hora", new Date().toLocaleTimeString("es-MX", { hour: "2-digit", minute: "2-digit" }))}
            ${definition("Evidencia", "Autorización digital")}
          </div>
          <div class="actions" style="margin-top:14px">
            <button class="btn success" data-action="advance-contract-approval">${icon("check", "btn-icon")}Aprobar</button>
            <button class="btn warning" data-action="contract-correction">${icon("refresh", "btn-icon")}Solicitar corrección</button>
            <button class="btn danger" data-action="contract-reject">${icon("x", "btn-icon")}Rechazar</button>
          </div>
        </div>
      </div>
    </div>
  `;
}

function contractToDraft(contract) {
  return {
    ...contract,
    salary: contract.salary,
    clauses: contract.clauses || [],
    legalRep: contract.legalRep,
    signingPlace: contract.signingPlace,
    signDate: today()
  };
}

function renderContractSignature() {
  const contract = contractById(state.ui.selectedContractId);
  const employee = employeeById(contract.employeeId);
  if (!isLegalDirectionApproved(contract)) {
    return `
      <div class="screen-stack">
        ${pageHeader("Firma digital", `${contract.folio} · ${employee.fullName}`, `
          <button class="btn secondary" data-route="contract-approval">${icon("arrow-left", "btn-icon")}Aprobaciones</button>
          <button class="btn secondary" data-route="contracts-list">${icon("file", "btn-icon")}Listado</button>
        `)}
        <div class="empty-state">
          Dirección Jurídica debe aprobar el contrato antes de pasarlo a firma o imprimirlo.
        </div>
      </div>
    `;
  }
  return `
    <div class="screen-stack">
      ${pageHeader("Firma digital", `${contract.folio} · ${employee.fullName}`, `
        <button class="btn success" data-action="sign-contract">${icon("edit", "btn-icon")}Firmar y activar</button>
        <button class="btn secondary" data-route="contracts-list">${icon("file", "btn-icon")}Listado</button>
      `)}
      <div class="grid two">
        ${contractPreview(contractToDraft(contract), employee)}
        <div class="panel">
          <h3>Evidencia de aceptación</h3>
          <div class="signature-box"><span class="signature-mark">${safe(employee.fullName)}</span></div>
          <div class="definition-grid" style="margin-top:14px">
            ${definition("Firma del empleado", contract.employeeSignature)}
            ${definition("Firma de la empresa", contract.companySignature)}
            ${definition("Fecha", date(today()))}
            ${definition("Dirección IP", contract.ipEvidence)}
            ${definition("Código", contract.validationCode)}
          </div>
        </div>
      </div>
    </div>
  `;
}

function renderContractEditor() {
  const template = activeTemplate();
  const variables = ["Nombre del empleado", "CURP", "RFC", "Domicilio", "Puesto", "Departamento", "Fecha de ingreso", "Sueldo", "Jornada", "Horario", "Empresa", "Representante legal", "Fecha de firma", "Fecha de vencimiento", "Centro de trabajo", "Prestaciones"];
  return `
    <div class="screen-stack">
      ${pageHeader("Editor de contrato", `${template.name} · ${template.version}`, `
        <button class="btn" data-action="save-template">${icon("check", "btn-icon")}Guardar versión</button>
        <button class="btn secondary" data-action="duplicate-template">${icon("copy", "btn-icon")}Duplicar</button>
      `)}
      <div class="grid two">
        <div class="form-card">
          <div class="form-section">
            <div class="form-section-title"><h4>Variables disponibles</h4>${tag("Insertar", "blue")}</div>
            <div class="variable-palette">${variables.map((item) => `<button class="variable-chip" data-action="insert-variable" data-variable="{{${safe(item)}}}">{{${safe(item)}}}</button>`).join("")}</div>
          </div>
          <div class="form-section">
            ${textareaField("Texto de plantilla", "templateBody", template.body, 'id="template-body"')}
          </div>
          <div class="form-section">
            <div class="form-section-title"><h4>Cláusulas</h4>${tag("Versiones", "teal")}</div>
            <div class="check-grid">
              ${template.clauses.map((clause, index) => `<label class="check-row"><input type="checkbox" data-action="toggle-template-clause" data-index="${index}" ${clause.active ? "checked" : ""} />${safe(clause.name)}</label>`).join("")}
            </div>
          </div>
        </div>
        <div class="panel">
          <div class="split"><h3>Vista previa</h3>${badge(template.status)}</div>
          <article class="contract-preview">
            <h4>${safe(template.name)}</h4>
            <p>${safe(template.body).replaceAll("{{", "<strong>{{").replaceAll("}}", "}}</strong>")}</p>
            <p>Cláusulas activas: ${template.clauses.filter((clause) => clause.active).map((clause) => `<strong>${safe(clause.name)}</strong>`).join(", ")}.</p>
          </article>
        </div>
      </div>
    </div>
  `;
}

function renderContractRenewal() {
  const contract = contractById(state.ui.selectedContractId);
  const employee = employeeById(contract.employeeId);
  const model = contractModel(contract);
  const proposedStartDate = "2026-08-01";
  const proposedEndDate = "2027-07-31";
  const proposedSalary = contract.salary + 2500;
  return `
    <div class="screen-stack">
      ${pageHeader("Renovación de contrato", `${contract.folio} · ${employee.fullName}`, `
        <button class="btn success" data-action="renew-contract">${icon("refresh", "btn-icon")}Generar renovación</button>
      `)}
      <div class="grid two">
        <div class="panel">
          <h3>Contrato actual</h3>
          <div class="definition-grid">
            ${definition("Folio", contract.folio)}
            ${definition("Tipo", contract.type)}
            ${definition("Inicio", date(contract.startDate))}
            ${definition("Término", contract.endDate ? date(contract.endDate) : "Indefinido")}
            ${definition("Sueldo", money(contract.salary))}
            ${definition("Estatus", displayContractStatus(contract))}
            ${definition("Modelo base", model.name)}
            ${definition("Formato", model.file)}
          </div>
        </div>
        <form id="renewal-form" class="form-card">
          <div class="form-section">
            <div class="form-section-title"><h4>Nuevas condiciones</h4>${tag("Convenio modificatorio", "blue")}</div>
            <div class="definition-grid renewal-summary">
              ${definition("Inicio propuesto", date(proposedStartDate))}
              ${definition("Término propuesto", date(proposedEndDate))}
              ${definition("Sueldo propuesto", money(proposedSalary))}
              ${definition("Formato a actualizar", model.file)}
            </div>
            <div class="form-grid two">
              ${field("Nueva fecha de inicio", "startDate", "date", proposedStartDate, true)}
              ${field("Nueva fecha de término", "endDate", "date", proposedEndDate)}
              ${field("Nuevo sueldo", "salary", "number", proposedSalary, true)}
              ${selectField("Tipo de contrato", "type", state.data.contractTypes, contract.type)}
              ${textareaField("Cambios de condiciones", "changes", "Actualización salarial y ratificación de funciones.")}
            </div>
          </div>
        </form>
      </div>
    </div>
  `;
}

function renderTermination() {
  const employee = employeeById(state.ui.selectedEmployeeId);
  return `
    <div class="screen-stack">
      ${pageHeader("Terminación laboral", `${employee.fullName} · ${employee.number}`, `
        <button class="btn secondary" data-route="settlement">${icon("calculator", "btn-icon")}Calcular finiquito</button>
      `)}
      <form id="termination-form" class="form-card">
        <div class="form-section">
          <div class="form-section-title"><h4>Datos de baja</h4>${tag("Acción sensible", "red")}</div>
          <div class="form-grid">
            ${selectField("Empleado", "employeeId", state.data.employees.map((item) => `${item.id}|${item.fullName}`), `${employee.id}|${employee.fullName}`, true)}
            ${selectField("Motivo de baja", "reason", ["Renuncia", "Rescisión", "Fin de contrato", "Mutuo acuerdo", "Jubilación", "Otro"], "Fin de contrato", true)}
            ${field("Fecha de baja", "terminationDate", "date", "2026-07-31", true)}
            ${selectField("Tipo de terminación", "terminationType", ["Finiquito", "Liquidación"], "Finiquito", true)}
            ${textareaField("Devolución de activos", "assets", "Laptop, gafete y tarjeta corporativa.")}
            ${textareaField("Documentos a generar", "documents", "Convenio, carta de terminación, recibo y evidencia de pago.")}
          </div>
        </div>
        <div class="form-section">
          <div class="actions">
            <button class="btn danger" type="submit">${icon("logout", "btn-icon")}Dar de baja</button>
            <button class="btn secondary" type="button" data-route="employee-profile">${icon("x", "btn-icon")}Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  `;
}

function formatHours(value) {
  const num = Number(value || 0);
  const hours = Math.floor(num);
  const minutes = Math.round((num - hours) * 60);
  return `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}`;
}

function overtimeCutoffs() {
  return state.data.settings?.overtimeCutoffDays || { first: 15, second: 30 };
}

function overtimePeriod(row) {
  return `${date(row.periodStart)} - ${date(row.periodEnd)}`;
}

function overtimePeriodForDate(value) {
  const cutoffs = overtimeCutoffs();
  const parsed = new Date(`${value}T12:00:00`);
  const year = parsed.getFullYear();
  const month = String(parsed.getMonth() + 1).padStart(2, "0");
  const day = parsed.getDate();
  const first = Number(cutoffs.first || 15);
  const second = Number(cutoffs.second || 30);
  if (day <= first) return { start: `${year}-${month}-01`, end: `${year}-${month}-${String(first).padStart(2, "0")}` };
  return { start: `${year}-${month}-${String(first + 1).padStart(2, "0")}`, end: `${year}-${month}-${String(second).padStart(2, "0")}` };
}

function weekdayName(value) {
  return new Intl.DateTimeFormat("es-MX", { weekday: "long" }).format(new Date(`${value}T12:00:00`)).replace(/^./, (letter) => letter.toUpperCase());
}

function overtimeAmount(employee, doubleHours, tripleHours) {
  const hourlyRate = Number(employee.dailySalary || 0) / 8;
  return +((Number(doubleHours || 0) * hourlyRate * 2) + (Number(tripleHours || 0) * hourlyRate * 3)).toFixed(2);
}

function renderOvertime() {
  const cutoffs = overtimeCutoffs();
  const requests = (state.data.overtimeRequests || []).map((item) => ({
    ...item,
    _search: `${item.employee} ${item.employeeNumber} ${item.department} ${item.position} ${item.status} ${item.reason} ${overtimePeriod(item)}`
  }));
  const totalHours = requests.reduce((sum, item) => sum + Number(item.totalHours || 0), 0);
  const doubleHours = requests.reduce((sum, item) => sum + Number(item.doubleHours || 0), 0);
  const tripleHours = requests.reduce((sum, item) => sum + Number(item.tripleHours || 0), 0);
  const amount = requests.reduce((sum, item) => sum + Number(item.preliminaryAmount || 0), 0);
  const periodOptions = [...new Set(requests.map(overtimePeriod))];
  const adminActions = state.ui.role === "Superadministrador"
    ? `<button class="btn" data-action="open-overtime-cutoff">${icon("settings", "btn-icon")}Configurar corte</button>`
    : "";

  const columns = [
    { key: "employee", label: "Empleado", render: (row) => `<strong>${safe(row.employee)}</strong><div class="small muted">${safe(row.employeeNumber)} · ${safe(row.department)}</div>` },
    { key: "period", label: "Periodo", render: (row) => overtimePeriod(row), filterValue: (row) => overtimePeriod(row) },
    { key: "date", label: "Fecha", render: (row) => date(row.date) },
    { key: "day", label: "Día" },
    { key: "entry", label: "Entrada" },
    { key: "exit", label: "Salida" },
    { key: "workedHours", label: "Hrs trabajadas", render: (row) => formatHours(row.workedHours), sortValue: (row) => row.workedHours },
    { key: "deliveryTime", label: "Tiempo entrega", render: (row) => formatHours(row.deliveryTime), sortValue: (row) => row.deliveryTime },
    { key: "travel", label: "Trayecto", render: (row) => formatHours(row.travel), sortValue: (row) => row.travel },
    { key: "totalHours", label: "Total horas", render: (row) => formatHours(row.totalHours), sortValue: (row) => row.totalHours },
    { key: "doubleHours", label: "Hrs doble", render: (row) => formatHours(row.doubleHours), sortValue: (row) => row.doubleHours },
    { key: "tripleHours", label: "Hrs triple", render: (row) => formatHours(row.tripleHours), sortValue: (row) => row.tripleHours },
    { key: "preliminaryAmount", label: "Monto preliminar", render: (row) => `<span class="amount">${money(row.preliminaryAmount)}</span>`, sortValue: (row) => row.preliminaryAmount },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) }
  ];

  return `
    <div class="screen-stack">
      ${pageHeader("Horas extras", "Control de horas extras por periodo de tiempo y cortes quincenales.", `
        ${adminActions}
        <button class="btn secondary" data-action="focus-overtime-form">${icon("plus", "btn-icon")}Nueva solicitud</button>
        <button class="btn secondary" data-action="download-overtime-format">${icon("download", "btn-icon")}Formato</button>
      `)}
      <div class="grid four">
        ${kpi("Corte actual", `Día ${cutoffs.first} y ${cutoffs.second}`, "Configurable por administrador", "calendar-days", "blue")}
        ${kpi("Total horas", formatHours(totalHours), "Periodo visible", "clock", "teal")}
        ${kpi("Horas dobles", formatHours(doubleHours), "Cálculo preliminar", "calculator", "amber")}
        ${kpi("Monto preliminar", money(amount), `${formatHours(tripleHours)} hrs triples`, "circle-dollar", "green")}
      </div>

      <div class="card">
        <div class="section-header">
          <div>
            <h3>Cortes quincenales</h3>
            <p>El sistema agrupa las horas extras con corte en los días ${safe(cutoffs.first)} y ${safe(cutoffs.second)} de cada mes.</p>
          </div>
          ${state.ui.role === "Superadministrador" ? `<button class="btn secondary" data-action="open-overtime-cutoff">${icon("settings", "btn-icon")}Definir rango</button>` : ""}
        </div>
        <div class="definition-grid" style="margin-top:14px">
          ${definition("Primer corte", `Día ${cutoffs.first}`)}
          ${definition("Segundo corte", `Día ${cutoffs.second}`)}
          ${definition("Solicitudes pendientes", requests.filter((item) => item.status === "Pendiente").length)}
          ${definition("Solicitudes aprobadas", requests.filter((item) => item.status === "Aprobada").length)}
        </div>
      </div>

      ${renderTable({
        id: "overtime",
        rows: requests,
        columns,
        pageSize: 8,
        searchPlaceholder: "Buscar empleado, periodo, departamento o estatus",
        filters: [
          { key: "period", label: "Periodo", options: periodOptions, getValue: (row) => overtimePeriod(row) },
          { key: "department", label: "Departamento", options: state.data.departments, getValue: (row) => row.department },
          { key: "status", label: "Estatus", options: ["Pendiente", "Aprobada", "Rechazada"], getValue: (row) => row.status }
        ],
        scrollY: "clamp(300px, calc(100vh - 470px), 620px)"
      })}

      <div id="overtime-request-panel" class="form-card">
        <div class="form-section">
          <div class="form-section-title"><h4>Formato de solicitud de horas extras</h4>${tag("Solicitud", "blue")}</div>
          <form id="overtime-request-form" class="form-grid">
            ${selectField("Empleado", "employeeId", state.data.employees.filter((employee) => employee.status === "Activo").map((employee) => `${employee.id}|${employee.fullName}`), `${state.data.employees[0].id}|${state.data.employees[0].fullName}`, true)}
            ${field("Fecha", "date", "date", "2026-07-29", true)}
            ${field("Entrada", "entry", "time", "06:00", true)}
            ${field("Salida", "exit", "time", "09:00", true)}
            ${field("Hrs trabajadas", "workedHours", "number", "3", true, 'min="0" step="0.5"')}
            ${field("Tiempo entrega", "deliveryTime", "number", "0", false, 'min="0" step="0.5"')}
            ${field("Trayecto", "travel", "number", "0", false, 'min="0" step="0.5"')}
            ${field("Hrs normal", "normalHours", "number", "0", false, 'min="0" step="0.5"')}
            ${field("Hrs doble", "doubleHours", "number", "3", false, 'min="0" step="0.5"')}
            ${field("Hrs triple", "tripleHours", "number", "0", false, 'min="0" step="0.5"')}
            ${textareaField("Motivo", "reason", "Soporte operativo autorizado.")}
            <div class="form-field form-submit-field">
              <span>&nbsp;</span>
              <button class="btn" type="submit">${icon("check", "btn-icon")}Registrar solicitud</button>
            </div>
          </form>
        </div>
        <div class="form-section">
          ${renderOvertimeRequestFormat(requests.slice(0, 5))}
        </div>
      </div>
    </div>
  `;
}

function renderOvertimeRequestFormat(rows) {
  return `
    <div class="table-wrap overtime-format-table">
      <table>
        <thead>
          <tr>
            <th rowspan="2">Empleado</th>
            <th rowspan="2">Día</th>
            <th colspan="2">Tiempo laborado</th>
            <th rowspan="2">Hrs trabajadas</th>
            <th rowspan="2">Tiempo entrega</th>
            <th rowspan="2">Trayecto</th>
            <th rowspan="2">Total horas</th>
            <th rowspan="2">Hrs normal</th>
            <th rowspan="2">Hrs doble</th>
            <th rowspan="2">Hrs triple</th>
            <th rowspan="2">Monto preliminar</th>
          </tr>
          <tr>
            <th>Entrada</th>
            <th>Salida</th>
          </tr>
        </thead>
        <tbody>
          ${rows.map((row) => `
            <tr>
              <td><strong>${safe(row.employee)}</strong><div class="small muted">${safe(row.employeeNumber)}</div></td>
              <td>${safe(row.day)} ${date(row.date)}</td>
              <td>${safe(row.entry)}</td>
              <td>${safe(row.exit)}</td>
              <td>${formatHours(row.workedHours)}</td>
              <td>${formatHours(row.deliveryTime)}</td>
              <td>${formatHours(row.travel)}</td>
              <td>${formatHours(row.totalHours)}</td>
              <td>${formatHours(row.normalHours)}</td>
              <td>${formatHours(row.doubleHours)}</td>
              <td>${formatHours(row.tripleHours)}</td>
              <td><span class="amount">${money(row.preliminaryAmount)}</span></td>
            </tr>
          `).join("")}
        </tbody>
      </table>
    </div>
  `;
}

function currentFortnightPeriod() {
  return { label: "SEGUNDA QUINCENA DE JULIO", start: "2026-07-16", end: "2026-07-30", days: 15 };
}

function payrollPeriodView(sourcePeriod = {}) {
  const fallback = currentFortnightPeriod();
  const start = sourcePeriod.startDate || fallback.start;
  const end = sourcePeriod.endDate || fallback.end;
  const startDate = new Date(`${start}T12:00:00`);
  const endDate = new Date(`${end}T12:00:00`);
  const hasValidDates = Number.isFinite(startDate.getTime()) && Number.isFinite(endDate.getTime());
  const days = hasValidDates ? Math.max(1, Math.round((endDate - startDate) / 86400000) + 1) : fallback.days;
  return {
    label: sourcePeriod.code ? `${sourcePeriod.code} | ${date(start)} - ${date(end)}` : fallback.label,
    start,
    end,
    days
  };
}

function contractForEmployeePeriod(employee, period = currentFortnightPeriod()) {
  const contracts = state.data.contracts
    .filter((contract) => Number(contract.employeeId) === Number(employee.id))
    .sort((a, b) => new Date(`${b.startDate || "1900-01-01"}T12:00:00`) - new Date(`${a.startDate || "1900-01-01"}T12:00:00`));
  if (!contracts.length) return null;
  const validContracts = contracts
    .filter((contract) => (
      contract.status !== "Cerrado"
      && contract.status !== "Vencido"
      && (!contract.startDate || contract.startDate <= period.end)
      && (!contract.endDate || contract.endDate >= period.start)
    ))
    .sort((a, b) => {
      const priority = { Activo: 0, "Próximo a vencer": 1, "Pendiente de firma": 2, "En aprobación": 3 };
      return (priority[a.status] ?? 9) - (priority[b.status] ?? 9);
    });
  return validContracts[0] || contracts[0];
}

function payrollContractStatus(employee, period) {
  const contracts = state.data.contracts.filter((contract) => Number(contract.employeeId) === Number(employee.id));
  if (isNewHireContractPending(employee, contractForEmployeePeriod(employee, period))) return "Nuevo ingreso";
  if (!contracts.length) return "Sin Contrato";
  const hasValidContract = contracts.some((contract) => (
    contract.status !== "Cerrado"
    && contract.status !== "Vencido"
    && (!contract.startDate || contract.startDate <= period.end)
    && (!contract.endDate || contract.endDate >= period.start)
  ));
  return hasValidContract ? "Vigente" : "Vencido";
}

function contractSalaryForPayroll(employee, period) {
  const contract = contractForEmployeePeriod(employee, period);
  const monthlySalary = Number(contract?.salary || employee.grossSalary || 0);
  return {
    contract,
    monthlySalary,
    netFortnight: +(monthlySalary / 2).toFixed(2),
    dailySalary: +(monthlySalary / 30).toFixed(2)
  };
}

function payrollPaymentCode(employee) {
  if (employee.paymentMethod === "Efectivo") return "EF";
  const bankCodes = {
    BBVA: "BB",
    Banorte: "BN",
    Santander: "ST",
    Citibanamex: "CB",
    HSBC: "HS",
    Scotiabank: "SB"
  };
  return `${bankCodes[employee.bank] || employee.bank.slice(0, 2).toUpperCase()}/${employee.clabe.endsWith("0") ? "L" : "N"}`;
}

function approvedIncidencesForPeriod(employee, period) {
  return state.data.incidences.filter((item) => (
    Number(item.employeeId) === Number(employee.id)
    && item.status === "Aprobada"
    && item.date >= period.start
    && item.date <= period.end
  ));
}

function overtimeForPeriod(employee, period) {
  const requests = (state.data.overtimeRequests || []).filter((item) => (
    Number(item.employeeId) === Number(employee.id)
    && item.status !== "Rechazada"
    && item.date >= period.start
    && item.date <= period.end
  ));
  return requests.reduce((acc, item) => {
    acc.hours += Number(item.totalHours || item.doubleHours || 0);
    acc.amount += Number(item.preliminaryAmount || 0);
    return acc;
  }, { hours: 0, amount: 0 });
}

function payrollFortnightRows(sourcePeriod = state.data.payrollPeriods[0] || {}) {
  const period = payrollPeriodView(sourcePeriod);
  const selectedCompany = sourcePeriod.company || "Todas";
  const includesAllCompanies = ["TODAS", "Todas", "Todas las empresas"].includes(selectedCompany);
  const selectedBranch = sourcePeriod.branch || "Todas";
  const selectedDepartment = sourcePeriod.department || "Todos";
  const rows = state.data.employees
    .filter((employee) => (
      employee.status === "Activo"
      && (includesAllCompanies || employee.company === selectedCompany)
      && (selectedBranch === "Todas" || employee.branch === selectedBranch)
      && (selectedDepartment === "Todos" || employee.department === selectedDepartment)
    ))
    .sort((a, b) => a.company.localeCompare(b.company, "es") || a.fullName.localeCompare(b.fullName, "es"))
    .map((employee, index) => {
      const contractSalary = contractSalaryForPayroll(employee, period);
      const incidences = approvedIncidencesForPeriod(employee, period);
      const absenceDays = incidences
        .filter((item) => item.type === "Falta")
        .reduce((sum, item) => sum + Number(item.quantity || 0), 0);
      const extraDays = incidences
        .filter((item) => String(item.type).toLowerCase().includes("festivo"))
        .reduce((sum, item) => sum + Number(item.quantity || 0), 0);
      const vacationDays = incidences
        .filter((item) => item.type === "Vacaciones")
        .reduce((sum, item) => sum + Number(item.quantity || 0), 0);
      const incidentDiscounts = incidences
        .filter((item) => ["Descuento", "Anticipo"].includes(item.type) || String(item.type).toLowerCase().includes("stamo"))
        .reduce((sum, item) => sum + Number(item.amount || 0), 0);
      const overtime = overtimeForPeriod(employee, period);
      const daysWorked = Math.max(0, period.days - absenceDays);
      const netFortnight = contractSalary.netFortnight;
      const paidDaysAmount = +(contractSalary.dailySalary * daysWorked).toFixed(2);
      const extraDaysAmount = +(contractSalary.dailySalary * extraDays).toFixed(2);
      const vacationPremium = +(contractSalary.dailySalary * vacationDays * 0.25).toFixed(2);
      const recurringDiscount = employee.loanBalance ? Math.min(300, employee.loanBalance) : 0;
      const discounts = +(incidentDiscounts + recurringDiscount).toFixed(2);
      const infonavit = employee.id % 5 === 0 ? +(netFortnight * 0.05).toFixed(2) : 0;
      const netPayable = +(paidDaysAmount + extraDaysAmount + overtime.amount + vacationPremium - discounts - infonavit).toFixed(2);
      return {
        no: index + 1,
        employeeId: employee.id,
        employeeNumber: employee.number,
        employeeConsecutiveId: `ID-${String(employee.id).padStart(3, "0")}`,
        name: employee.fullName.toUpperCase(),
        company: employee.company,
        contractStatus: payrollContractStatus(employee, period),
        payCycle: "QNAL",
        payMethod: payrollPaymentCode(employee),
        netFortnight,
        daysWorked,
        dailySalary: contractSalary.dailySalary,
        paidDaysAmount,
        extraDays,
        extraDaysAmount,
        overtimeHours: +overtime.hours.toFixed(2),
        overtimeAmount: +overtime.amount.toFixed(2),
        vacationPremium,
        discounts,
        infonavit,
        netPayable
      };
    });
  return { period, rows };
}

function payrollTotals(rows) {
  return rows.reduce((acc, row) => {
    acc.netFortnight += row.netFortnight;
    acc.daysWorked += row.daysWorked;
    acc.paidDaysAmount += row.paidDaysAmount;
    acc.extraDays += row.extraDays;
    acc.extraDaysAmount += row.extraDaysAmount;
    acc.overtimeHours += row.overtimeHours;
    acc.overtimeAmount += row.overtimeAmount;
    acc.vacationPremium += row.vacationPremium;
    acc.discounts += row.discounts;
    acc.infonavit += row.infonavit;
    acc.netPayable += row.netPayable;
    return acc;
  }, {
    netFortnight: 0,
    daysWorked: 0,
    paidDaysAmount: 0,
    extraDays: 0,
    extraDaysAmount: 0,
    overtimeHours: 0,
    overtimeAmount: 0,
    vacationPremium: 0,
    discounts: 0,
    infonavit: 0,
    netPayable: 0
  });
}

function payrollHistorySnapshot(periodRecord, options = {}) {
  if (!periodRecord) return null;
  const { period, rows } = payrollFortnightRows(periodRecord);
  const totals = payrollTotals(rows);
  const status = options.status || periodRecord.status || "Abierta";
  return {
    id: options.id || periodRecord.historySnapshotId || `${periodRecord.code || "NOM"}-${Date.now()}`,
    periodCode: periodRecord.code || "NOM-2026-14",
    period: {
      ...period,
      company: periodRecord.company || "Todas las empresas",
      branch: periodRecord.branch || "Todas",
      type: periodRecord.type || "Quincenal",
      frequency: periodRecord.frequency || "Quincenal",
      payDate: periodRecord.payDate || period.end,
      status
    },
    createdAt: options.createdAt || periodRecord.createdAt || today(),
    approvedAt: options.approvedAt || periodRecord.approvedAt || "",
    status,
    employeeCount: rows.length,
    totalAmount: totals.netPayable,
    rows: rows.map((row) => ({ ...row }))
  };
}

function upsertPayrollHistory(snapshot) {
  if (!snapshot) return;
  state.data.payrollHistory = [
    snapshot,
    ...(state.data.payrollHistory || []).filter((item) => item.periodCode !== snapshot.periodCode)
  ];
}

function findPayrollHistorySnapshot(periodCode) {
  return (state.data.payrollHistory || []).find((snapshot, index) => {
    const key = payrollSnapshotKey(snapshot, index);
    return key === periodCode || snapshot.periodCode === periodCode || snapshot.id === periodCode;
  });
}

function upsertPendingPayrollTable(snapshot) {
  if (!snapshot) return;
  state.data.payrollPendingTables = [
    snapshot,
    ...(state.data.payrollPendingTables || []).filter((item) => item.periodCode !== snapshot.periodCode)
  ];
}

function pendingPayrollTables() {
  const pending = [...(state.data.payrollPendingTables || [])];
  const currentPeriod = state.data.payrollPeriods[0];
  if (
    currentPeriod?.code
    && !currentPeriod.summaryMovedToHistory
    && !currentPeriod.summaryDeleted
    && !pending.some((item) => item.periodCode === currentPeriod.code)
  ) {
    pending.unshift(payrollHistorySnapshot(currentPeriod, {
      status: currentPeriod.status || "Abierta",
      approvedAt: currentPeriod.approvedAt || ""
    }));
  }
  return pending;
}

function payrollSnapshotKey(snapshot, index = 0) {
  return snapshot?.periodCode || snapshot?.id || `payroll-${index}`;
}

function payrollSnapshotApproved(snapshot) {
  return ["Aprobada", "Pagada"].includes(snapshot?.status) || Boolean(snapshot?.approvedAt);
}

function paymentBatchPaidForPeriod(periodCode) {
  const batch = state.data.paymentBatch;
  if (!periodCode || !batch || batch.period !== periodCode) return false;
  const details = batch.details || [];
  return details.length > 0 && details.every((row) => row.status === "Pagado");
}

function payrollSnapshotPaid(snapshot, index = 0) {
  const periodCode = snapshot?.periodCode || payrollSnapshotKey(snapshot, index);
  const periodRecord = findPayrollPeriodByCode(periodCode) || {};
  return snapshot?.status === "Pagada"
    || Boolean(snapshot?.paidAt)
    || Boolean(periodRecord.summaryPaid)
    || paymentBatchPaidForPeriod(periodCode);
}

function markPayrollSnapshotPaid(periodCode) {
  if (!periodCode) return;
  const paidAt = today();
  const snapshot = (state.data.payrollPendingTables || []).find((item, index) => payrollSnapshotKey(item, index) === periodCode || item.periodCode === periodCode);
  if (snapshot) {
    snapshot.status = "Pagada";
    snapshot.paidAt = paidAt;
    snapshot.period = { ...(snapshot.period || {}), status: "Pagada" };
  }
  const periodRecord = findPayrollPeriodByCode(periodCode);
  if (periodRecord) {
    periodRecord.summaryPaid = true;
    periodRecord.paidAt = paidAt;
    periodRecord.status = "Pagada";
  }
}

function findPendingPayrollSnapshot(periodCode) {
  return pendingPayrollTables().find((snapshot, index) => payrollSnapshotKey(snapshot, index) === periodCode || snapshot.periodCode === periodCode);
}

function findPayrollPeriodByCode(periodCode) {
  return state.data.payrollPeriods.find((period) => period.code === periodCode);
}

function payrollTotalCells(label, totals, className = "payroll-total-row") {
  return `
    <tr class="${className}">
      <td colspan="7">${safe(label)}</td>
      <td class="payroll-money-cell">${money(totals.netFortnight)}</td>
      <td class="payroll-number-cell">${number(totals.daysWorked)}</td>
      <td></td>
      <td class="payroll-money-cell">${money(totals.paidDaysAmount)}</td>
      <td class="payroll-number-cell">${number(totals.extraDays)}</td>
      <td class="payroll-money-cell">${money(totals.extraDaysAmount)}</td>
      <td class="payroll-number-cell">${number(totals.overtimeHours)}</td>
      <td class="payroll-money-cell">${money(totals.overtimeAmount)}</td>
      <td class="payroll-money-cell">${money(totals.vacationPremium)}</td>
      <td class="payroll-money-cell">${money(totals.discounts)}</td>
      <td class="payroll-money-cell">${money(totals.infonavit)}</td>
      <td class="payroll-money-cell strong-total">${money(totals.netPayable)}</td>
    </tr>
  `;
}

function payrollFortnightColumns() {
  return [
    { key: "no", label: "No" },
    { key: "employeeConsecutiveId", label: "ID" },
    { key: "name", label: "Nombre" },
    { key: "company", label: "Empresa" },
    { key: "contractStatus", label: "Contrato" },
    { key: "payCycle", label: "Pago" },
    { key: "payMethod", label: "Pago" },
    { key: "netFortnight", label: "Neto quincenal", className: "is-calc", filterValue: (row) => money(row.netFortnight) },
    { key: "daysWorked", label: "Dias laborados", className: "is-calc" },
    { key: "dailySalary", label: "Salario diario", className: "is-calc", filterValue: (row) => money(row.dailySalary) },
    { key: "paidDaysAmount", label: "Pago por dias laborados", className: "is-calc", filterValue: (row) => money(row.paidDaysAmount) },
    { key: "extraDays", label: "Dias extra", className: "is-calc" },
    { key: "extraDaysAmount", label: "Monto dias extra", className: "is-calc", filterValue: (row) => row.extraDaysAmount ? money(row.extraDaysAmount) : "Sin dato" },
    { key: "overtimeHours", label: "Hrs ext", className: "is-calc" },
    { key: "overtimeAmount", label: "Monto horas extra", className: "is-calc", filterValue: (row) => row.overtimeAmount ? money(row.overtimeAmount) : "Sin dato" },
    { key: "vacationPremium", label: "Prim vac", className: "is-calc", filterValue: (row) => row.vacationPremium ? money(row.vacationPremium) : "Sin dato" },
    { key: "discounts", label: "Descuentos", className: "is-calc", filterValue: (row) => row.discounts ? money(row.discounts) : "Sin dato" },
    { key: "infonavit", label: "Infonavit", className: "is-calc", filterValue: (row) => row.infonavit ? money(row.infonavit) : "Sin dato" },
    { key: "netPayable", label: "Neto por pagar", className: "is-calc", filterValue: (row) => money(row.netPayable) }
  ];
}

function renderPayrollFortnightTable(tableId, period, rows, allowFilters = true) {
  const columns = payrollFortnightColumns();
  const current = tableState[tableId] || { search: "", page: 1, sortKey: "", sortDir: "asc", filters: {}, columnFilters: {} };
  current.columnFilters = current.columnFilters || {};
  tableState[tableId] = current;
  const filteredRows = allowFilters ? applyColumnFilters(rows, columns, current) : rows;
  const companies = [...new Set(filteredRows.map((row) => row.company))];
  const body = companies.map((company) => {
    const companyRows = filteredRows.filter((row) => row.company === company);
    const subtotal = payrollTotals(companyRows);
    return `
      <tr class="company-group-row"><td colspan="19">${safe(company)}</td></tr>
      ${companyRows.map((row) => `
        <tr>
          <td class="payroll-number-cell">${safe(row.no)}</td>
          <td class="payroll-id-cell">${safe(row.employeeConsecutiveId)}</td>
          <td class="payroll-name-cell">${safe(row.name)}</td>
          <td>${safe(row.company)}</td>
          <td>${tag(row.contractStatus || "Sin Contrato", row.contractStatus === "Vigente" ? "green" : row.contractStatus === "Vencido" ? "red" : "amber")}</td>
          <td>${safe(row.payCycle)}</td>
          <td>${safe(row.payMethod)}</td>
          <td class="payroll-money-cell">${money(row.netFortnight)}</td>
          <td class="payroll-number-cell">${number(row.daysWorked)}</td>
          <td class="payroll-money-cell">${money(row.dailySalary)}</td>
          <td class="payroll-money-cell">${money(row.paidDaysAmount)}</td>
          <td class="payroll-number-cell">${row.extraDays ? number(row.extraDays) : ""}</td>
          <td class="payroll-money-cell">${row.extraDaysAmount ? money(row.extraDaysAmount) : ""}</td>
          <td class="payroll-number-cell">${row.overtimeHours ? number(row.overtimeHours) : ""}</td>
          <td class="payroll-money-cell">${row.overtimeAmount ? money(row.overtimeAmount) : ""}</td>
          <td class="payroll-money-cell">${row.vacationPremium ? money(row.vacationPremium) : ""}</td>
          <td class="payroll-money-cell">${row.discounts ? money(row.discounts) : ""}</td>
          <td class="payroll-money-cell">${row.infonavit ? money(row.infonavit) : ""}</td>
          <td class="payroll-money-cell strong-total">${money(row.netPayable)}</td>
        </tr>
      `).join("")}
      ${payrollTotalCells(`TOTAL ${company}`, subtotal)}
    `;
  }).join("");
  const grandTotal = payrollTotals(filteredRows);

  return `
    <div class="payroll-fortnight-title">${safe(period.label)}</div>
    <div class="table-wrap payroll-fortnight-table has-sticky-x-scroll">
      <table>
        <thead>
          <tr>
            ${columns.map((column) => `
              <th class="${safe(column.className || "")}">
                <span class="table-heading">
                  <span>${safe(column.label)}</span>
                  ${allowFilters ? tableColumnFilterPanel(tableId, column, rows, current) : ""}
                </span>
              </th>
            `).join("")}
          </tr>
        </thead>
        <tbody>
          ${filteredRows.length ? `${body}${payrollTotalCells("TOTAL TODAS LAS EMPRESAS", grandTotal, "payroll-total-row payroll-grand-total")}` : `<tr><td colspan="${columns.length}" class="muted">Sin registros para este periodo.</td></tr>`}
        </tbody>
      </table>
    </div>
    <div class="table-sticky-scroll payroll-sticky-scroll" data-sticky-x-scroll aria-hidden="true"><div></div></div>
  `;
}

function renderPayrollFortnightSummaryLegacy() {
  const currentPeriod = state.data.payrollPeriods[0] || {};
  const summaryApproved = Boolean(currentPeriod.summaryApproved);
  const summaryCollapsed = Boolean(state.ui.payrollSummaryCollapsed);
  if (false && summaryApproved) {
    return `
      <div class="card payroll-fortnight-card">
        <div class="section-header">
          <div>
            <h3>Resumen de pagos quincenales por empresa</h3>
          </div>
          ${tag("En historial", "green")}
        </div>
        ${empty("Sin resumen activo. Crea un nuevo periodo para generar la siguiente nómina.")}
      </div>
    `;
  }

  const pendingTables = pendingPayrollTables();
  const currentSnapshot = pendingTables.find((snapshot) => snapshot.periodCode === currentPeriod.code)
    || (!currentPeriod.summaryMovedToHistory && currentPeriod.code ? payrollHistorySnapshot(currentPeriod) : null);
  const currentRows = currentSnapshot?.rows || [];
  const approvedPendingCount = pendingTables.filter(payrollSnapshotApproved).length;
  const paidPendingCount = pendingTables.filter(payrollSnapshotPaid).length;
  const visibleTables = pendingTables.length ? pendingTables : (currentSnapshot ? [currentSnapshot] : []);
  const currentSummaryActive = Boolean(currentSnapshot?.periodCode === currentPeriod.code && !currentPeriod.summaryMovedToHistory);
  const canPayCurrentSummary = Boolean(summaryApproved && currentSummaryActive && !payrollSnapshotPaid(currentSnapshot));
  return `
    <div class="card payroll-fortnight-card">
      <div class="section-header">
        <div>
          <h3>Resumen de pagos quincenales por empresa</h3>
          <p>Tablas quincenales acumuladas antes de enviarse al historial.</p>
        </div>
        <button class="icon-btn payroll-summary-toggle" data-action="toggle-payroll-summary-table" data-tooltip="${summaryCollapsed ? "Mostrar tabla" : "Ocultar tabla"}" aria-label="${summaryCollapsed ? "Mostrar tabla" : "Ocultar tabla"}" aria-expanded="${!summaryCollapsed}">${summaryCollapsed ? "+" : "-"}</button>
        ${tag(`${visibleTables.length} tabla${visibleTables.length === 1 ? "" : "s"}`, "amber")}
        ${paidPendingCount ? tag(`${paidPendingCount} pagada${paidPendingCount === 1 ? "" : "s"}`, "green") : approvedPendingCount ? tag(`${approvedPendingCount} aprobada${approvedPendingCount === 1 ? "" : "s"}`, "green") : tag(`${currentRows.length} empleados`, "blue")}
      </div>
      ${summaryCollapsed
        ? `<div class="payroll-summary-collapsed">${icon("table", "mini-icon")}Tablas ocultas. Usa + para mostrar la informacion.</div>`
        : (visibleTables.length
          ? visibleTables.map((snapshot, index) => renderPayrollFortnightTable(
              `payroll-pending-${snapshot.periodCode || index}`,
              snapshot.period || { label: snapshot.periodCode || "Nomina pendiente" },
              snapshot.rows || []
            )).join("")
          : empty("Sin tablas pendientes. Crea un nuevo periodo para agregar una tabla quincenal."))
      }
      <div class="payroll-approval-actions">
        <div class="payroll-approval-left">
          <button class="btn success" data-action="approve-payroll-fortnight" ${summaryApproved || !currentPeriod.code || !currentSummaryActive ? "disabled" : ""}>${icon("check", "btn-icon")}${summaryApproved ? "Aprobado" : "Aprobar"}</button>
          <button class="btn secondary" data-action="send-payroll-history" ${paidPendingCount ? "" : "disabled"}>${icon("receipt", "btn-icon")}Mandar al historial de nominas</button>
        </div>
        <button class="btn" data-action="pay-approved-payroll" ${canPayCurrentSummary ? "" : "disabled"}>${icon("wallet", "btn-icon")}Pagar</button>
      </div>
    </div>
  `;
}

function renderPayrollFortnightSummaryItem(snapshot, index) {
  const periodKey = payrollSnapshotKey(snapshot, index);
  const periodRecord = findPayrollPeriodByCode(snapshot.periodCode) || {};
  const rows = snapshot.rows || [];
  const collapsedByPeriod = state.ui.payrollSummaryCollapsedByPeriod || {};
  const isCollapsed = Boolean(collapsedByPeriod[periodKey]);
  const isApproved = payrollSnapshotApproved(snapshot) || Boolean(periodRecord.summaryApproved);
  const isPaid = payrollSnapshotPaid(snapshot, index);
  const periodLabel = snapshot.period?.label || snapshot.periodCode || `Nomina ${index + 1}`;
  const canMoveToHistory = isApproved && isPaid;
  const canPay = isApproved && !isPaid && !periodRecord.summaryMovedToHistory;

  return `
    <div class="card payroll-fortnight-card payroll-period-card">
      <div class="section-header payroll-period-header">
        <div>
          <h3>${safe(snapshot.periodCode || `Periodo ${index + 1}`)}</h3>
          <p>${safe(periodLabel)}</p>
        </div>
        <div class="actions">
          <button class="icon-btn payroll-summary-toggle" data-action="toggle-payroll-period-table" data-period="${safe(periodKey)}" data-tooltip="${isCollapsed ? "Mostrar tabla" : "Ocultar tabla"}" aria-label="${isCollapsed ? "Mostrar tabla" : "Ocultar tabla"}" aria-expanded="${!isCollapsed}">${isCollapsed ? "+" : "-"}</button>
          ${tag(`${rows.length} empleados`, "blue")}
          ${tag(isPaid ? "Pagada" : isApproved ? "Aprobada" : "Pendiente", isPaid || isApproved ? "green" : "amber")}
        </div>
      </div>
      ${isCollapsed
        ? `<div class="payroll-summary-collapsed">${icon("table", "mini-icon")}Tabla oculta. Usa + para mostrar la informacion.</div>`
        : renderPayrollFortnightTable(
            `payroll-pending-${periodKey}`,
            snapshot.period || { label: periodLabel },
            rows
          )
      }
      <div class="payroll-approval-actions payroll-approval-ordered">
        <button class="btn danger" data-action="delete-payroll-period-table" data-period="${safe(periodKey)}">${icon("trash", "btn-icon")}Eliminar</button>
        <button class="btn success" data-action="approve-payroll-period-table" data-period="${safe(periodKey)}" ${isApproved ? "disabled" : ""}>${icon("check", "btn-icon")}${isApproved ? "Aprobado" : "Aprobar"}</button>
        <button class="btn" data-action="pay-payroll-period-table" data-period="${safe(periodKey)}" ${canPay ? "" : "disabled"}>${icon("wallet", "btn-icon")}Pagar</button>
        <button class="btn secondary" data-action="send-payroll-period-history" data-period="${safe(periodKey)}" ${canMoveToHistory ? "" : "disabled"}>${icon("receipt", "btn-icon")}Mandar al historial de nominas</button>
      </div>
    </div>
  `;
}

function renderPayrollFortnightSummary() {
  const pendingTables = pendingPayrollTables();
  const approvedPendingCount = pendingTables.filter(payrollSnapshotApproved).length;
  const paidPendingCount = pendingTables.filter(payrollSnapshotPaid).length;
  return `
    <div class="payroll-summary-stack">
      <div class="card payroll-fortnight-card payroll-summary-head">
        <div class="section-header">
          <div>
            <h3>Resumen de pagos quincenales por empresa</h3>
            <p>En esta seccion se acumulan las tablas quincenales hasta mandarlas al historial.</p>
          </div>
          <div class="actions">
            ${tag(`${pendingTables.length} tabla${pendingTables.length === 1 ? "" : "s"}`, "amber")}
            ${approvedPendingCount ? tag(`${approvedPendingCount} aprobada${approvedPendingCount === 1 ? "" : "s"}`, "green") : ""}
            ${paidPendingCount ? tag(`${paidPendingCount} pagada${paidPendingCount === 1 ? "" : "s"}`, "green") : ""}
          </div>
        </div>
        ${pendingTables.length ? "" : empty("Sin tablas pendientes. Crea un nuevo periodo para agregar una tabla quincenal.")}
      </div>
      ${pendingTables.map((snapshot, index) => renderPayrollFortnightSummaryItem(snapshot, index)).join("")}
    </div>
  `;
}

function renderPayrollDashboard() {
  const receipts = state.data.receipts;
  const totalGross = state.data.employees.filter((employee) => employee.status === "Activo").reduce((sum, employee) => sum + employee.grossSalary / 2, 0);
  const totalPerceptions = receipts.reduce((sum, receipt) => sum + receipt.perceptions, 0);
  const totalDeductions = receipts.reduce((sum, receipt) => sum + receipt.deductions, 0);
  const net = receipts.reduce((sum, receipt) => sum + receipt.net, 0);
  return `
    <div class="screen-stack">
      ${pageHeader("Dashboard de nómina", "Periodo actual, cálculo, validaciones, autorización y dispersión.", `
        <button class="btn" data-route="payroll-period">${icon("plus", "btn-icon")}Crear periodo</button>
        <button class="btn secondary" data-route="payroll-calc">${icon("calculator", "btn-icon")}Calcular</button>
        <button class="btn secondary" data-route="payroll-history">${icon("receipt", "btn-icon")}Historial de nóminas</button>
      `)}
      <div class="grid payroll-kpi-strip">
        ${kpi("Periodo actual", "NOM-2026-14", "Quincenal ordinaria", "calendar-days", "blue")}
        ${kpi("Empleados incluidos", state.data.payrollPeriods[0].employeesIncluded, "Activos menos bajas", "users", "teal")}
        ${kpi("Sueldo bruto total", money(totalGross), "Base quincenal", "circle-dollar", "green")}
        ${kpi("Neto a pagar", money(net), "Fecha 18 jul 2026", "wallet", "green")}
        ${kpi("Percepciones", money(totalPerceptions), "Gravadas y exentas", "percent", "blue")}
        ${kpi("Deducciones", money(totalDeductions), "ISR, IMSS y otras", "scale", "amber")}
        ${kpi("Costo patronal", money(net * 0.32), "Carga social estimada", "chart", "teal")}
        ${kpi("Estatus", state.data.payrollPeriods[0].status, "Proceso en curso", "shield", "amber")}
      </div>
      ${renderPayrollFortnightSummary()}
      <div class="card">
        <div class="section-header">
          <div>
            <h3>Módulos integrados de nómina</h3>
            <p>Incidencias, conceptos, pagos y recibos quedan concentrados dentro del proceso de nómina.</p>
          </div>
        </div>
        <div class="quick-actions payroll-module-grid" style="margin-top:12px">
          ${quickAction("Incidencias", "incidences", "calendar-alert")}
          ${quickAction("Percepciones y deducciones", "perceptions", "scale")}
          ${quickAction("Pagos", "payments", "wallet")}
          ${quickAction("Recibos", "receipts", "receipt")}
        </div>
      </div>
    </div>
  `;
}

function renderPayrollHistory() {
  const history = state.data.payrollHistory || [];
  const collapsedByPeriod = state.ui.payrollSummaryCollapsedByPeriod || {};
  return `
    <div class="screen-stack">
      ${pageHeader("Historial de nóminas", "Tablas de nómina aprobadas y enviadas a pagos.", `
        <button class="btn secondary" data-route="payroll">${icon("arrow-left", "btn-icon")}Nómina</button>
      `)}
      ${history.length ? history.map((snapshot, index) => {
        const periodKey = payrollSnapshotKey(snapshot, index);
        const isCollapsed = Boolean(collapsedByPeriod[periodKey]);
        return `
        <div class="card payroll-fortnight-card payroll-period-card">
          <div class="section-header">
            <div>
              <h3>${safe(snapshot.periodCode || `Nómina ${index + 1}`)}</h3>
              <p>${safe(snapshot.approvedAt ? `Aprobada el ${date(snapshot.approvedAt)}` : "Nómina aprobada")}</p>
            </div>
            <div class="actions">
              <button class="btn secondary" data-action="view-payroll-history-payments" data-period="${safe(periodKey)}">${icon("wallet", "btn-icon")}Ver pagos</button>
              <button class="icon-btn payroll-summary-toggle" data-action="toggle-payroll-period-table" data-period="${safe(periodKey)}" data-tooltip="${isCollapsed ? "Mostrar tabla" : "Ocultar tabla"}" aria-label="${isCollapsed ? "Mostrar tabla" : "Ocultar tabla"}" aria-expanded="${!isCollapsed}">${isCollapsed ? "+" : "-"}</button>
              ${tag(`${(snapshot.rows || []).length} empleados`, "blue")}
              ${tag(money(snapshot.totalAmount || payrollTotals(snapshot.rows || []).netPayable), "green")}
            </div>
          </div>
          ${isCollapsed
            ? `<div class="payroll-summary-collapsed">${icon("table", "mini-icon")}Tabla oculta. Usa + para mostrar la informacion.</div>`
            : renderPayrollFortnightTable(`payroll-history-${periodKey}`, snapshot.period || { label: snapshot.label || "Nómina histórica" }, snapshot.rows || [])
          }
        </div>
      `;
      }).join("") : empty("Sin nóminas en historial. Al aprobar el resumen quincenal, la tabla se guardará aquí.")}
    </div>
  `;
}

function renderPayrollStepper(current) {
  return `<div class="stepper" style="margin-top:12px">${payrollSteps.map((label, index) => {
    const step = index + 1;
    return `<div class="step ${step < current ? "is-done" : step === current ? "is-active" : ""}"><span class="step-number">${step}</span><span>${safe(label)}</span></div>`;
  }).join("")}</div>`;
}

function renderPayrollPeriod() {
  return `
    <div class="screen-stack">
      ${pageHeader("Crear periodo de nómina", "Configuración de empresa, fechas, empleados, centro de costos y opciones de importación.", `
        <button class="btn secondary" data-route="payroll">${icon("calculator", "btn-icon")}Panel de nómina</button>
      `)}
      <form id="payroll-period-form" class="form-card">
        <div class="form-section">
          <div class="form-section-title"><h4>Datos del periodo</h4>${tag("Nómina", "blue")}</div>
          <div class="form-grid">
            ${selectField("Empresa", "company", ["TODAS", ...state.data.companies], state.ui.company, true)}
            ${selectField("Sucursal", "branch", ["Todas", ...state.data.branches], "Todas")}
            ${selectField("Tipo de nómina", "type", ["Semanal", "Catorcenal", "Quincenal", "Mensual", "Extraordinaria", "Aguinaldo", "PTU", "Finiquito", "Liquidación", "Bonos", "Comisiones", "Retroactivos"], "Quincenal", true)}
            ${selectField("Periodicidad", "frequency", ["Semanal", "Catorcenal", "Quincenal", "Mensual"], "Quincenal")}
            ${field("Fecha inicial", "startDate", "date", "2026-07-16", true)}
            ${field("Fecha final", "endDate", "date", "2026-07-31", true)}
            ${field("Fecha de corte", "cutDate", "date", "2026-08-01", true)}
            ${field("Fecha de pago", "payDate", "date", "2026-08-02", true)}
            ${field("Ejercicio", "year", "number", "2026", true)}
            ${field("Número de periodo", "number", "15", true)}
            ${selectField("Empleados incluidos", "employeesIncluded", ["Todos activos", "Por departamento", "Por sucursal", "Selección manual"], "Todos activos")}
            ${selectField("Departamento", "department", ["Todos", ...state.data.departments], "Todos")}
            ${field("Centro de costos", "costCenter", "text", "CC-001 Corporativo")}
            ${textareaField("Observaciones", "observations", "Periodo ordinario quincenal.")}
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title"><h4>Opciones</h4>${tag("Carga inicial", "teal")}</div>
          <div class="check-grid">
            ${["Copiar configuración del periodo anterior", "Incluir empleados activos", "Excluir bajas", "Incluir retroactivos", "Importar información desde archivo"].map((item, index) => `<label class="check-row"><input type="checkbox" name="options" ${index < 3 ? "checked" : ""} />${safe(item)}</label>`).join("")}
          </div>
        </div>
        <div class="form-section">
          <div class="actions">
            <button class="btn" type="submit">${icon("check", "btn-icon")}Crear periodo</button>
            <button class="btn secondary" type="button" data-route="payroll">${icon("x", "btn-icon")}Cancelar</button>
          </div>
        </div>
      </form>
    </div>
  `;
}

function renderIncidences() {
  const rows = state.data.incidences.map((item) => ({ ...item, _search: `${item.employee} ${item.type} ${item.status} ${item.approver}` }));
  const columns = [
    { key: "employee", label: "Empleado" },
    { key: "type", label: "Tipo" },
    { key: "date", label: "Fecha", render: (row) => date(row.date) },
    { key: "quantity", label: "Cantidad" },
    { key: "unit", label: "Unidad" },
    { key: "amount", label: "Importe", render: (row) => money(row.amount), sortValue: (row) => row.amount },
    { key: "evidence", label: "Evidencia" },
    { key: "createdBy", label: "Usuario" },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) },
    { key: "approver", label: "Aprobador" },
    { key: "actions", label: "Acciones", sortable: false, render: (row) => `
      <div class="actions">
        <button class="icon-btn" data-action="approve-incidence" data-id="${row.id}" data-tooltip="Aprobar">${icon("check")}</button>
        <button class="icon-btn" data-action="reject-incidence" data-id="${row.id}" data-tooltip="Rechazar">${icon("x")}</button>
      </div>
    ` }
  ];
  return `
    <div class="screen-stack">
      ${pageHeader("Registro de incidencias", "Faltas, retardos, horas extra, incapacidades, vacaciones, permisos, bonos, descuentos, préstamos y ajustes.", `
        <button class="btn secondary" data-route="payroll">${icon("arrow-left", "btn-icon")}Atrás</button>
        <button class="btn" data-action="focus-incidence-form">${icon("plus", "btn-icon")}Nueva incidencia</button>
      `)}
      <div class="grid two">
        <form id="incidence-form" class="form-card">
          <div class="form-section">
            <div class="form-section-title"><h4>Nueva incidencia</h4>${tag("Pre cálculo", "amber")}</div>
            <div class="form-grid two">
              ${selectField("Empleado", "employeeId", state.data.employees.filter((e) => e.status === "Activo").map((e) => `${e.id}|${e.fullName}`), `${state.ui.selectedEmployeeId}|${employeeById(state.ui.selectedEmployeeId).fullName}`, true)}
              ${selectField("Tipo", "type", state.data.incidenceTypes, "Horas extra", true)}
              ${field("Fecha", "date", "date", "2026-07-20", true)}
              ${field("Cantidad", "quantity", "number", "2", true, 'min="0" step="0.01"')}
              ${selectField("Unidad", "unit", ["Días", "Horas", "Importe", "Eventos"], "Horas")}
              ${field("Importe", "amount", "number", "370", false, 'min="0" step="0.01"')}
              ${field("Evidencia", "evidence", "text", "Adjunta")}
              ${field("Aprobador", "approver", "text", "Gerente directo")}
              ${textareaField("Comentarios", "comments", "Registro sujeto a aprobación antes del cálculo.")}
            </div>
          </div>
          <div class="form-section"><button class="btn" type="submit">${icon("check", "btn-icon")}Registrar incidencia</button></div>
        </form>
        <div class="card">
          <h3>Resumen</h3>
          <div class="definition-grid" style="margin-top:12px">
            ${definition("Pendientes", rows.filter((row) => row.status === "Pendiente").length)}
            ${definition("Aprobadas", rows.filter((row) => row.status === "Aprobada").length)}
            ${definition("Rechazadas", rows.filter((row) => row.status === "Rechazada").length)}
            ${definition("Impacto estimado", money(rows.reduce((sum, row) => sum + row.amount, 0)))}
          </div>
        </div>
      </div>
      ${renderTable({
        id: "incidences",
        rows,
        columns,
        searchPlaceholder: "Buscar incidencia",
        filters: [
          { key: "type", label: "Tipo", options: state.data.incidenceTypes, getValue: (row) => row.type },
          { key: "status", label: "Estatus", options: ["Pendiente", "Aprobada", "Rechazada"], getValue: (row) => row.status }
        ],
        pageSize: 8
      })}
    </div>
  `;
}

function renderPerceptions() {
  const columns = [
    { key: "key", label: "Clave" },
    { key: "name", label: "Nombre" },
    { key: "type", label: "Tipo" },
    { key: "taxed", label: "Gravado", render: (row) => row.taxed ? "Sí" : "No" },
    { key: "exempt", label: "Exento", render: (row) => row.exempt ? "Sí" : "No" },
    { key: "integrable", label: "Integrable", render: (row) => row.integrable ? "Sí" : "No" },
    { key: "recurring", label: "Recurrente", render: (row) => row.recurring ? "Sí" : "No" },
    { key: "calculationType", label: "Cálculo" },
    { key: "formula", label: "Fórmula" },
    { key: "cap", label: "Tope" },
    { key: "application", label: "Aplicación" },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) },
    { key: "actions", label: "Acciones", sortable: false, render: (row) => `<button class="icon-btn" data-action="edit-perception" data-id="${row.id}" data-tooltip="Editar">${icon("edit")}</button>` }
  ];
  return `
    <div class="screen-stack">
      ${conceptHeader("perceptions")}
      ${renderTable({ id: "perceptions", rows: state.data.perceptions, columns, searchPlaceholder: "Buscar percepción", pageSize: 8 })}
    </div>
  `;
}

function renderDeductions() {
  const columns = [
    { key: "key", label: "Clave" },
    { key: "name", label: "Nombre" },
    { key: "type", label: "Tipo" },
    { key: "automatic", label: "Automático", render: (row) => row.automatic ? "Sí" : "Manual" },
    { key: "percent", label: "Porcentaje", render: (row) => `${row.percent}%`, sortValue: (row) => row.percent },
    { key: "fixedAmount", label: "Importe fijo", render: (row) => money(row.fixedAmount), sortValue: (row) => row.fixedAmount },
    { key: "formula", label: "Fórmula" },
    { key: "cap", label: "Tope", render: (row) => row.cap ? money(row.cap) : "Sin tope" },
    { key: "priority", label: "Prioridad" },
    { key: "startDate", label: "Inicio", render: (row) => row.startDate ? date(row.startDate) : "N/A" },
    { key: "endDate", label: "Término", render: (row) => row.endDate ? date(row.endDate) : "N/A" },
    { key: "balance", label: "Saldo", render: (row) => money(row.balance), sortValue: (row) => row.balance },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) },
    { key: "actions", label: "Acciones", sortable: false, render: (row) => `<button class="icon-btn" data-action="edit-deduction" data-id="${row.id}" data-tooltip="Editar deducción">${icon("edit")}</button>` }
  ];
  return `
    <div class="screen-stack">
      ${conceptHeader("deductions")}
      ${renderTable({ id: "deductions", rows: state.data.deductions, columns, searchPlaceholder: "Buscar deducción", pageSize: 8 })}
    </div>
  `;
}

function conceptHeader(active) {
  return pageHeader(active === "perceptions" ? "Catálogo de percepciones" : "Catálogo de deducciones", "Conceptos configurables para cálculo de nómina, topes, fórmulas, estatus y aplicación.", `
    <button class="btn secondary" data-route="payroll">${icon("arrow-left", "btn-icon")}Atrás</button>
    <button class="btn ${active === "perceptions" ? "" : "secondary"}" data-route="perceptions">${icon("plus", "btn-icon")}Percepciones</button>
    <button class="btn ${active === "deductions" ? "" : "secondary"}" data-route="deductions">${icon("scale", "btn-icon")}Deducciones</button>
  `);
}

function renderPayrollCalc() {
  const employee = employeeById(state.ui.selectedPayrollEmployeeId);
  const calc = payrollCalc(employee);
  return `
    <div class="screen-stack">
      ${pageHeader("Cálculo individual de nómina", "Detalle por empleado con percepciones, deducciones, validaciones y acciones de revisión.", `
        <button class="btn" data-action="recalculate-payroll">${icon("refresh", "btn-icon")}Recalcular</button>
        <button class="btn secondary" data-route="payroll-summary">${icon("file", "btn-icon")}Resumen</button>
      `)}
      <div class="grid calc-layout">
        <div class="panel">
          <h3>Empleados incluidos</h3>
          <div class="employee-list" style="margin-top:12px">
            ${state.data.employees.filter((item) => item.status === "Activo").map((item) => `
              <button class="employee-picker ${item.id === employee.id ? "is-selected" : ""}" data-action="select-payroll-employee" data-id="${item.id}">
                ${avatar(item)}
                <span><strong>${safe(item.fullName)}</strong><span class="small muted">${money(item.grossSalary / 2)} neto estimado</span></span>
              </button>
            `).join("")}
          </div>
        </div>
        <div class="calc-panel">
          <div class="panel">
            <div class="split"><h3>${safe(employee.fullName)}</h3>${badge("En revisión")}</div>
            <div class="definition-grid" style="margin-top:12px">
              ${definition("Sueldo mensual", money(employee.grossSalary))}
              ${definition("Sueldo diario", money(employee.dailySalary))}
              ${definition("Días pagados", "15")}
              ${definition("Faltas", calc.faltas)}
              ${definition("Incapacidades", calc.incapacidades)}
              ${definition("Horas extra", calc.horasExtra)}
              ${definition("Vacaciones", calc.vacaciones)}
              ${definition("Salario base", money(calc.base))}
              ${definition("Salario integrado", money(employee.integratedDailySalary))}
            </div>
          </div>
          <div class="grid two">
            ${miniConceptTable("Percepciones", calc.perceptions, ["Concepto", "Cantidad", "Gravado", "Exento", "Total"])}
            ${miniConceptTable("Deducciones", calc.deductions, ["Concepto", "Base", "%", "Importe", "Saldo"])}
          </div>
          <div class="panel">
            <div class="split"><h3>Resultado</h3>${tag("Costo patronal " + money(calc.employerCost), "teal")}</div>
            <div class="definition-grid" style="margin-top:12px">
              ${definition("Total percepciones", money(calc.totalPerceptions))}
              ${definition("Total deducciones", money(calc.totalDeductions))}
              ${definition("Neto a pagar", money(calc.net))}
              ${definition("Costo patronal", money(calc.employerCost))}
            </div>
            <div class="actions" style="margin-top:14px">
              <button class="btn secondary" data-action="open-adjustment">${icon("plus", "btn-icon")}Agregar percepción</button>
              <button class="btn secondary" data-action="edit-deduction" data-id="5">${icon("scale", "btn-icon")}Agregar deducción</button>
              <button class="btn secondary" data-action="open-adjustment">${icon("edit", "btn-icon")}Aplicar ajuste</button>
              <button class="btn ghost" data-action="view-formula">${icon("eye", "btn-icon")}Ver fórmula</button>
              <button class="btn ghost" data-action="view-history">${icon("chart", "btn-icon")}Ver historial</button>
              <button class="btn success" data-action="mark-reviewed">${icon("check", "btn-icon")}Marcar revisado</button>
              <button class="btn danger" data-action="exclude-payroll-employee">${icon("x", "btn-icon")}Excluir</button>
            </div>
          </div>
        </div>
        <div class="panel">
          <h3>Resumen y validaciones</h3>
          <div class="alert-list" style="margin-top:12px">
            ${state.data.payrollValidations.slice(0, 6).map((item) => alertItem({ level: item.type, module: "Validación", title: `${item.title} (${item.count})`, due: item.status, status: item.status })).join("")}
          </div>
          <div class="actions" style="margin-top:14px">
            <button class="btn secondary" data-route="validations">${icon("alert", "btn-icon")}Ver validaciones</button>
          </div>
        </div>
      </div>
    </div>
  `;
}

function payrollCalc(employee) {
  const employeeIncidences = state.data.incidences.filter((item) => item.employeeId === employee.id && item.status === "Aprobada");
  const horasExtra = employeeIncidences.filter((item) => item.type === "Horas extra").reduce((sum, item) => sum + Number(item.quantity), 0);
  const faltas = employeeIncidences.filter((item) => item.type === "Falta").reduce((sum, item) => sum + Number(item.quantity), 0);
  const incapacidades = employeeIncidences.filter((item) => item.type === "Incapacidad").reduce((sum, item) => sum + Number(item.quantity), 0);
  const vacaciones = employeeIncidences.filter((item) => item.type === "Vacaciones").reduce((sum, item) => sum + Number(item.quantity), 0);
  const base = employee.grossSalary / 2 - employee.dailySalary * faltas;
  const extra = horasExtra * employee.dailySalary / 8 * 2;
  const bonus = employee.recurringBonus || 0;
  const vouchers = employee.grossSalary * 0.05;
  const isr = base * 0.105;
  const imss = base * 0.028;
  const loan = employee.loanBalance ? Math.min(650, employee.loanBalance) : 0;
  const perceptions = [
    ["Sueldo", 15, base, 0, base],
    ["Horas extra", horasExtra, extra, 0, extra],
    ["Bonos", bonus ? 1 : 0, bonus, 0, bonus],
    ["Vales", 1, 0, vouchers, vouchers]
  ];
  const deductions = [
    ["ISR", base, 10.5, isr, 0],
    ["IMSS", base, 2.8, imss, 0],
    ["Préstamo", loan ? employee.loanBalance : 0, 0, loan, Math.max(0, employee.loanBalance - loan)]
  ];
  const totalPerceptions = perceptions.reduce((sum, row) => sum + row[4], 0);
  const totalDeductions = deductions.reduce((sum, row) => sum + row[3], 0);
  const net = totalPerceptions - totalDeductions;
  return { horasExtra, faltas, incapacidades, vacaciones, base, perceptions, deductions, totalPerceptions, totalDeductions, net, employerCost: net * 1.32 };
}

function miniConceptTable(title, rows, headers) {
  return `
    <div class="panel">
      <h3>${safe(title)}</h3>
      <div class="mini-table" style="margin-top:12px">
        <table>
          <thead><tr>${headers.map((header) => `<th>${safe(header)}</th>`).join("")}</tr></thead>
          <tbody>
            ${rows.map((row) => `<tr>${row.map((value, index) => `<td>${formatMiniCell(value, headers[index], index)}</td>`).join("")}</tr>`).join("")}
          </tbody>
        </table>
      </div>
    </div>
  `;
}

function formatMiniCell(value, header, index) {
  if (header === "%") return `${Number(value || 0).toFixed(2)}%`;
  if (typeof value === "number" && index > 1) return money(value);
  return safe(value);
}

function renderPayrollSummary() {
  const rows = state.data.employees.filter((employee) => employee.status === "Activo").map((employee) => {
    const calc = payrollCalc(employee);
    return {
      number: employee.number,
      name: employee.fullName,
      department: employee.department,
      base: calc.base,
      perceptions: calc.totalPerceptions,
      deductions: calc.totalDeductions,
      taxes: calc.deductions[0][3],
      net: calc.net,
      bankAccount: `${employee.bank} · ${masked(employee.clabe)}`,
      status: calc.net < 0 ? "Error" : "Calculado",
      employeeId: employee.id,
      _search: `${employee.number} ${employee.fullName} ${employee.department} ${employee.bank}`
    };
  });
  const totals = rows.reduce((acc, row) => {
    acc.base += row.base;
    acc.perceptions += row.perceptions;
    acc.deductions += row.deductions;
    acc.taxes += row.taxes;
    acc.net += row.net;
    return acc;
  }, { base: 0, perceptions: 0, deductions: 0, taxes: 0, net: 0 });
  const columns = [
    { key: "number", label: "Número" },
    { key: "name", label: "Nombre" },
    { key: "department", label: "Departamento" },
    { key: "base", label: "Sueldo base", render: (row) => money(row.base), sortValue: (row) => row.base },
    { key: "perceptions", label: "Percepciones", render: (row) => money(row.perceptions), sortValue: (row) => row.perceptions },
    { key: "deductions", label: "Deducciones", render: (row) => money(row.deductions), sortValue: (row) => row.deductions },
    { key: "taxes", label: "Impuestos", render: (row) => money(row.taxes), sortValue: (row) => row.taxes },
    { key: "net", label: "Neto", render: (row) => `<strong>${money(row.net)}</strong>`, sortValue: (row) => row.net },
    { key: "bankAccount", label: "Cuenta bancaria" },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) },
    { key: "actions", label: "Acciones", sortable: false, render: (row) => `<button class="icon-btn" data-action="select-payroll-employee-route" data-id="${row.employeeId}" data-route-target="payroll-calc" data-tooltip="Ver cálculo">${icon("eye")}</button>` }
  ];
  const footer = `<tr><td colspan="3">Totales generales</td><td>${money(totals.base)}</td><td>${money(totals.perceptions)}</td><td>${money(totals.deductions)}</td><td>${money(totals.taxes)}</td><td>${money(totals.net)}</td><td colspan="3"></td></tr>`;
  return `
    <div class="screen-stack">
      ${pageHeader("Resumen general de nómina", "Totales por empleado, variaciones y comparación contra el periodo anterior.", `
        <button class="btn" data-route="payroll-authorization">${icon("check", "btn-icon")}Enviar a autorización</button>
        <button class="btn secondary" data-action="export-payroll">${icon("download", "btn-icon")}Exportar CSV</button>
      `)}
      <div class="grid four">
        ${kpi("Variación sueldo", "+3.1%", "Contra periodo anterior", "chart", "green")}
        ${kpi("Variación percepciones", "+8.7%", "Bonos trimestrales", "percent", "green")}
        ${kpi("Variación deducciones", "+2.4%", "ISR e IMSS", "scale", "amber")}
        ${kpi("Altas / Bajas", "4 / 2", "Julio 2026", "users", "blue")}
      </div>
      ${renderTable({ id: "payroll-summary", rows, columns, searchPlaceholder: "Buscar empleado o departamento", footerHtml: footer, pageSize: 8 })}
    </div>
  `;
}

function renderValidations() {
  const groups = ["Crítica", "Advertencia", "Informativa"];
  const criticalOpen = state.data.payrollValidations.some((item) => item.type === "Crítica" && item.status === "Abierta");
  return `
    <div class="screen-stack">
      ${pageHeader("Validaciones y errores", "Panel automático de excepciones previo a autorización de nómina.", `
        <button class="btn ${criticalOpen ? "danger" : "success"}" data-action="resolve-critical-validations">${icon("check", "btn-icon")}${criticalOpen ? "Resolver críticos" : "Sin críticos"}</button>
        <button class="btn secondary" data-route="payroll-authorization">${icon("arrow-right", "btn-icon")}Autorizar</button>
      `)}
      <div class="validation-board">
        ${groups.map((group) => `
          <div class="validation-col">
            <div class="split"><h3>${safe(group)}</h3>${badge(group)}</div>
            <div class="alert-list" style="margin-top:12px">
              ${state.data.payrollValidations.filter((item) => item.type === group).map((item) => `
                <div class="alert-item">
                  <span class="severity ${group === "Crítica" ? "critical" : group === "Advertencia" ? "warning" : "info"}"></span>
                  <div><strong>${safe(item.title)}</strong><div class="small muted">${number(item.count)} registros · ${safe(item.status)}</div></div>
                  ${badge(item.status)}
                </div>
              `).join("")}
            </div>
          </div>
        `).join("")}
      </div>
      <div class="card">
        <div class="split">
          <div>
            <h3>Regla de autorización</h3>
            <p>No se permite autorizar la nómina si existen errores críticos abiertos.</p>
          </div>
          ${criticalOpen ? badge("Bloqueada") : badge("Disponible")}
        </div>
      </div>
    </div>
  `;
}

function renderPayrollAuthorization() {
  const criticalOpen = state.data.payrollValidations.some((item) => item.type === "Crítica" && item.status === "Abierta");
  return `
    <div class="screen-stack">
      ${pageHeader("Autorización de nómina", "Flujo configurable con comentarios, evidencia, fecha y usuario por aprobación.", `
        <button class="btn success" data-action="approve-payroll" ${criticalOpen ? "disabled" : ""}>${icon("check", "btn-icon")}Aprobar</button>
        <button class="btn warning" data-action="payroll-correction">${icon("refresh", "btn-icon")}Solicitar corrección</button>
        <button class="btn danger" data-action="payroll-reject">${icon("x", "btn-icon")}Rechazar</button>
      `)}
      ${criticalOpen ? `<div class="card"><div class="split"><strong>Autorización bloqueada por errores críticos.</strong><button class="btn danger" data-route="validations">${icon("alert", "btn-icon")}Ver críticos</button></div></div>` : ""}
      <div class="card">
        <div class="workflow-line">
          ${state.data.approvalFlow.map((step) => `
            <div class="flow-node ${step.status === "Aprobado" ? "is-done" : step.status === "Pendiente" ? "is-current" : ""}">
              <span class="step-number">${step.id}</span>
              <strong>${safe(step.name)}</strong>
              ${badge(step.status)}
              <span class="small muted">${safe(step.role)} · ${safe(step.user || "Sin usuario")}</span>
            </div>
          `).join("")}
        </div>
      </div>
      <div class="panel">
        <h3>Comentario de aprobación</h3>
        ${textareaField("Comentario", "approvalComment", "Importes revisados contra incidencias autorizadas y periodo anterior.")}
      </div>
    </div>
  `;
}

function renderPayments() {
  const batch = state.data.paymentBatch;
  return `
    <div class="screen-stack">
      ${pageHeader("Generación de pagos", "Dispersión bancaria, envío, respuesta, comprobantes y conciliación.", `
        <button class="btn secondary" data-route="payroll">${icon("arrow-left", "btn-icon")}Atrás</button>
        <button class="btn" data-action="generate-dispersion">${icon("download", "btn-icon")}Generar archivo</button>
        <button class="btn secondary" data-action="send-bank">${icon("send", "btn-icon")}Enviar al banco</button>
        <button class="btn success" data-action="mark-payments-paid">${icon("check", "btn-icon")}Marcar pagados</button>
      `)}
      <div class="grid three">
        <button class="quick-action" data-route="dispersion">${icon("wallet", "mini-icon")}<span>Detalle de dispersión</span></button>
        <button class="quick-action" data-route="rejected-payments">${icon("alert", "mini-icon")}<span>Pagos rechazados</span></button>
        <button class="quick-action" data-action="attach-proof">${icon("upload", "mini-icon")}<span>Adjuntar comprobantes</span></button>
      </div>
      ${renderDispersion(true)}
    </div>
  `;
}

function renderDispersion(embedded = false) {
  const rows = state.data.paymentBatch.details
    .map((row) => ({ ...row, _search: `${row.employee} ${row.bank} ${row.clabe} ${row.status} ${row.reference}` }))
    .sort((a, b) => Number(a.id || 0) - Number(b.id || 0));
  const totalPayable = rows.reduce((sum, row) => sum + Number(row.amount || 0), 0);
  const columns = [
    { key: "employee", label: "Empleado" },
    { key: "bank", label: "Banco" },
    { key: "account", label: "Cuenta", render: (row) => masked(row.account) },
    { key: "clabe", label: "CLABE", render: (row) => masked(row.clabe) },
    { key: "amount", label: "Importe", render: (row) => money(row.amount), sortValue: (row) => row.amount },
    { key: "reference", label: "Referencia" },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) },
    { key: "rejectReason", label: "Motivo rechazo", render: (row) => safe(row.rejectReason || "N/A") },
    { key: "payDate", label: "Fecha de pago", render: (row) => date(row.payDate) },
    { key: "proof", label: "Comprobante", render: (row) => safe(row.proof || "Pendiente") },
    { key: "actions", label: "Acciones", sortable: false, render: (row) => {
      if (row.status === "Rechazado") return `<button class="icon-btn" data-action="reprocess-payment" data-id="${row.id}" data-tooltip="Reprocesar">${icon("refresh")}</button>`;
      if (row.status === "Pagado") return `<button class="icon-btn paid-check" disabled aria-label="Pago realizado" data-tooltip="Pago realizado">${icon("check")}</button>`;
      return `<button class="icon-btn" data-action="mark-payment-paid" data-id="${row.id}" data-tooltip="Marcar pagado">${icon("check")}</button>`;
    } }
  ];
  const table = renderTable({
    id: embedded ? "dispersion-preview" : "dispersion",
    rows,
    columns,
    searchPlaceholder: "Buscar empleado, banco o referencia",
    filters: [
      { key: "status", label: "Estatus", options: ["Pendiente", "Generado", "Enviado", "Procesando", "Pagado", "Rechazado", "Reprocesado", "Cancelado"], getValue: (row) => row.status },
      { key: "bank", label: "Banco", options: state.data.banks, getValue: (row) => row.bank }
    ],
    paginate: false,
    footerHtml: `<tr class="payment-total-row"><td colspan="4">Total a pagar</td><td>${money(totalPayable)}</td><td colspan="6"></td></tr>`,
    scrollY: embedded ? "clamp(360px, calc(100vh - 440px), 720px)" : "clamp(420px, calc(100vh - 260px), 760px)"
  });
  if (embedded) return table;
  return `<div class="screen-stack">${pageHeader("Detalle de dispersión", state.data.paymentBatch.folio, `<button class="btn secondary" data-route="payments">${icon("wallet", "btn-icon")}Pagos</button>`)}${table}</div>`;
}

function renderRejectedPayments() {
  const rejected = state.data.paymentBatch.details.filter((row) => row.status === "Rechazado").map((row) => ({ ...row, _search: `${row.employee} ${row.rejectReason}` }));
  const columns = [
    { key: "employee", label: "Empleado" },
    { key: "bank", label: "Banco" },
    { key: "clabe", label: "CLABE", render: (row) => masked(row.clabe) },
    { key: "amount", label: "Importe", render: (row) => money(row.amount) },
    { key: "rejectReason", label: "Motivo" },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) },
    { key: "actions", label: "Acciones", sortable: false, render: (row) => `<button class="btn secondary" data-action="reprocess-payment" data-id="${row.id}">${icon("refresh", "btn-icon")}Reprocesar</button>` }
  ];
  return `
    <div class="screen-stack">
      ${pageHeader("Pagos rechazados", "Corrección y reproceso de dispersiones rechazadas por banco.", `
        <button class="btn" data-action="reprocess-all-payments">${icon("refresh", "btn-icon")}Reprocesar todos</button>
      `)}
      ${rejected.length ? renderTable({ id: "rejected-payments", rows: rejected, columns, searchPlaceholder: "Buscar rechazo", pageSize: 8 }) : empty("No hay pagos rechazados")}
    </div>
  `;
}

function renderReceipts() {
  const rows = state.data.receipts.map((receipt) => ({ ...receipt, _search: `${receipt.folio} ${receipt.employee} ${receipt.period} ${receipt.status}` }));
  const columns = [
    { key: "folio", label: "Folio" },
    { key: "employee", label: "Empleado" },
    { key: "period", label: "Periodo" },
    { key: "payDate", label: "Fecha pago", render: (row) => date(row.payDate) },
    { key: "perceptions", label: "Percepciones", render: (row) => money(row.perceptions), sortValue: (row) => row.perceptions },
    { key: "deductions", label: "Deducciones", render: (row) => money(row.deductions), sortValue: (row) => row.deductions },
    { key: "net", label: "Neto", render: (row) => `<strong>${money(row.net)}</strong>`, sortValue: (row) => row.net },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) },
    { key: "issueDate", label: "Emisión", render: (row) => date(row.issueDate) },
    { key: "actions", label: "Acciones", sortable: false, render: (row) => `
      <div class="actions">
        <button class="icon-btn" data-action="view-receipt" data-id="${row.id}" data-tooltip="Ver recibo">${icon("eye")}</button>
        <button class="icon-btn" data-action="download-receipt" data-id="${row.id}" data-tooltip="Descargar">${icon("download")}</button>
        <button class="icon-btn" data-action="send-receipt" data-id="${row.id}" data-tooltip="Enviar por correo">${icon("send")}</button>
        <button class="icon-btn" data-action="publish-receipt" data-id="${row.id}" data-tooltip="Publicar">${icon("upload")}</button>
      </div>
    ` }
  ];
  return `
    <div class="screen-stack">
      ${pageHeader("Recibos de nómina", "Generación, publicación, descarga, envío y firma de conformidad.", `
        <button class="btn secondary" data-route="payroll">${icon("arrow-left", "btn-icon")}Atrás</button>
        <button class="btn" data-action="generate-receipts">${icon("receipt", "btn-icon")}Generar recibos</button>
        <button class="btn secondary" data-action="download-all-receipts">${icon("download", "btn-icon")}Descargar todos</button>
      `)}
      ${renderTable({ id: "receipts", rows, columns, searchPlaceholder: "Buscar recibo", pageSize: 8 })}
    </div>
  `;
}

function renderReceiptView() {
  const receipt = receiptById(state.ui.selectedReceiptId);
  const employee = employeeById(receipt.employeeId);
  const calc = payrollCalc(employee);
  return `
    <div class="screen-stack">
      ${pageHeader("Vista del recibo", `${receipt.folio} · ${receipt.employee}`, `
        <button class="btn" data-action="download-receipt" data-id="${receipt.id}">${icon("download", "btn-icon")}Descargar PDF</button>
        <button class="btn secondary" data-action="print-receipt">${icon("printer", "btn-icon")}Imprimir</button>
        <button class="btn secondary" data-route="receipts">${icon("receipt", "btn-icon")}Recibos</button>
      `)}
      <article class="receipt" id="receipt-printable">
        <header class="receipt-header">
          <div>
            <h3>${safe(employee.company)}</h3>
            <div class="small muted">RFC NOV260101AB1 · Régimen general de ley</div>
          </div>
          <div class="text-right">
            <strong>${safe(receipt.folio)}</strong>
            <div class="small muted">Fecha de emisión: ${date(receipt.issueDate)}</div>
          </div>
        </header>
        <div class="receipt-body">
          <div class="definition-grid">
            ${definition("Empleado", employee.fullName)}
            ${definition("Número", employee.number)}
            ${definition("CURP", employee.curp)}
            ${definition("RFC", employee.rfc)}
            ${definition("Periodo", receipt.period)}
            ${definition("Fecha de pago", date(receipt.payDate))}
            ${definition("Forma de pago", employee.paymentMethod)}
            ${definition("Cuenta", `${employee.bank} · ${masked(employee.clabe)}`)}
          </div>
          <div class="grid two">
            ${miniConceptTable("Percepciones", calc.perceptions, ["Concepto", "Cantidad", "Gravado", "Exento", "Total"])}
            ${miniConceptTable("Deducciones", calc.deductions, ["Concepto", "Base", "%", "Importe", "Saldo"])}
          </div>
          <div class="receipt-total"><span>Neto pagado</span><span>${money(receipt.net)}</span></div>
          <div class="grid two">
            <div class="signature-box"><span class="signature-mark">${receipt.confirmed ? employee.fullName : "Pendiente de firma"}</span></div>
            <div class="definition-grid">
              ${definition("Confirmación digital", receipt.confirmed ? "Confirmada" : "Pendiente")}
              ${definition("Estatus", receipt.status)}
              ${definition("Código", `REC-${receipt.id}-MX`)}
            </div>
          </div>
        </div>
      </article>
    </div>
  `;
}

function renderSettlement() {
  const employee = employeeById(state.ui.selectedEmployeeId);
  const seniorityYears = Math.max(1, Math.floor((new Date("2026-07-31") - new Date(employee.hireDate)) / 31536000000));
  const pendingVacation = Math.min(employee.vacationDays, 8);
  const workedDays = 15;
  const vacationPay = pendingVacation * employee.dailySalary;
  const vacationPremium = vacationPay * 0.25;
  const aguinaldo = employee.dailySalary * 15 * (212 / 365);
  const indemnity = employee.dailySalary * 90;
  const seniorityPremium = employee.dailySalary * 12 * seniorityYears;
  const gross = workedDays * employee.dailySalary + vacationPay + vacationPremium + aguinaldo + indemnity + seniorityPremium;
  const taxes = gross * 0.12;
  const loans = employee.loanBalance;
  const net = gross - taxes - loans;
  return `
    <div class="screen-stack">
      ${pageHeader("Cálculo de finiquito", `${employee.fullName} · ${employee.number}`, `
        <button class="btn" data-action="generate-settlement-docs">${icon("file", "btn-icon")}Generar documentos</button>
        <button class="btn secondary" data-route="termination">${icon("logout", "btn-icon")}Terminación</button>
      `)}
      <div class="grid two">
        <form class="form-card">
          <div class="form-section">
            <div class="form-section-title"><h4>Variables de cálculo</h4>${tag("Simulado", "amber")}</div>
            <div class="form-grid two">
              ${field("Fecha de ingreso", "hireDate", "date", employee.hireDate)}
              ${field("Fecha de baja", "terminationDate", "date", "2026-07-31")}
              ${field("Antigüedad", "seniority", "text", `${seniorityYears} años`)}
              ${field("Sueldo diario", "dailySalary", "number", employee.dailySalary)}
              ${field("Días trabajados", "workedDays", "number", workedDays)}
              ${field("Vacaciones pendientes", "vacationDays", "number", pendingVacation)}
              ${field("Bonos pendientes", "bonus", "number", employee.recurringBonus)}
              ${field("Comisiones pendientes", "commissions", "number", "0")}
              ${field("Préstamos", "loans", "number", loans)}
            </div>
          </div>
        </form>
        <div class="panel">
          <h3>Resumen</h3>
          <div class="definition-grid" style="margin-top:12px">
            ${definition("Vacaciones", money(vacationPay))}
            ${definition("Prima vacacional", money(vacationPremium))}
            ${definition("Aguinaldo proporcional", money(aguinaldo))}
            ${definition("Indemnización", money(indemnity))}
            ${definition("Prima de antigüedad", money(seniorityPremium))}
            ${definition("Total bruto", money(gross))}
            ${definition("Impuestos", money(taxes))}
            ${definition("Neto a pagar", money(net))}
          </div>
          <div class="actions" style="margin-top:14px">
            <button class="btn secondary" data-action="download-settlement">${icon("download", "btn-icon")}Resumen</button>
            <button class="btn secondary" data-route="contract-editor">${icon("file-signature", "btn-icon")}Convenio</button>
            <button class="btn secondary" data-route="receipt-view">${icon("receipt", "btn-icon")}Recibo</button>
          </div>
        </div>
      </div>
    </div>
  `;
}

function renderManagers() {
  const isBranchManager = state.ui.role === "Gerente de sucursal";
  const managers = isBranchManager ? [activeBranchManager()].filter(Boolean) : managersList();
  const rows = managers.map((manager) => {
    const staff = branchEmployees(manager.branches || []).filter((employee) => employee.status === "Activo");
    return {
      ...manager,
      staffCount: staff.length,
      _search: `${manager.name} ${manager.email} ${manager.company} ${(manager.branches || []).join(" ")} ${manager.status}`
    };
  });
  const columns = [
    {
      key: "name",
      label: "Gerente",
      render: (row) => `<div class="employee-cell">${avatar({ initials: row.initials, avatarColor: "#3157d5" })}<div><strong>${safe(row.name)}</strong><div class="small muted">${safe(row.email)}</div></div></div>`
    },
    { key: "company", label: "Empresa" },
    { key: "branches", label: "Sucursales", render: (row) => (row.branches || []).map((branch) => tag(branch, "blue")).join(" ") },
    { key: "staffCount", label: "Personal", render: (row) => `<span class="amount">${number(row.staffCount)}</span>` },
    { key: "vacationPermission", label: "Vacaciones", render: (row) => tag(row.vacationPermission, "green") },
    { key: "status", label: "Estatus", render: (row) => badge(row.status) }
  ];
  return `
    <div class="screen-stack">
      ${pageHeader("Gerentes de sucursal", "Accesos de gerentes, sucursales operadas y personal autorizado para solicitar vacaciones.", `
        <button class="btn secondary" data-route="employees">${icon("users", "btn-icon")}Ver empleados</button>
        <button class="btn secondary" data-route="vacations">${icon("calendar-days", "btn-icon")}Ver vacaciones</button>
      `)}
      ${renderTable({ id: "managers", rows, columns, searchPlaceholder: "Buscar gerente, empresa o sucursal", pageSize: 6, scrollY: "210px" })}
      <div class="manager-branch-grid">
        ${rows.map((manager) => {
          const staff = branchEmployees(manager.branches || []).filter((employee) => employee.status === "Activo");
          return `
            <section class="manager-branch-panel">
              <div class="split">
                <div>
                  <h3>${safe(manager.name)}</h3>
                  <p>${safe((manager.branches || []).join(", "))}</p>
                </div>
                ${badge(manager.status)}
              </div>
              <div class="manager-staff-list">
                ${staff.slice(0, 7).map((employee) => `
                  <div class="manager-staff-row">
                    <div class="employee-cell">
                      ${avatar(employee)}
                      <div>
                        <strong>${safe(employee.fullName)}</strong>
                        <div class="small muted">${safe(employee.position)} · ${safe(employee.number)}</div>
                      </div>
                    </div>
                    <button class="icon-btn" data-action="manager-vacation-request" data-id="${employee.id}" data-tooltip="Crear solicitud de vacaciones">${icon("calendar-days")}</button>
                  </div>
                `).join("") || empty("Sin personal activo en la sucursal")}
              </div>
              <div class="manager-panel-footer">
                <span class="small muted">${number(staff.length)} empleados activos</span>
                <button class="btn secondary compact" data-route="employees">${icon("users", "btn-icon")}Ver listado</button>
              </div>
            </section>
          `;
        }).join("")}
      </div>
    </div>
  `;
}

function renderReports() {
  const reports = ["Nómina por periodo", "Nómina por empleado", "Nómina por departamento", "Nómina por sucursal", "Costo laboral", "Percepciones", "Deducciones", "Impuestos", "Seguridad social", "Horas extra", "Vacaciones", "Incidencias", "Préstamos", "Finiquitos", "Contratos activos", "Contratos vencidos", "Contratos próximos a vencer", "Renovaciones", "Altas", "Bajas", "Historial salarial", "Comparativo mensual", "Dispersión bancaria", "Pagos rechazados"];
  return `
    <div class="screen-stack">
      ${pageHeader("Reportes", "Consultas listas para exportación a Excel, PDF y CSV.", `
        <button class="btn" data-action="export-report" data-format="Excel">${icon("download", "btn-icon")}Excel</button>
        <button class="btn secondary" data-action="export-report" data-format="PDF">${icon("file", "btn-icon")}PDF</button>
        <button class="btn secondary" data-action="export-report" data-format="CSV">${icon("download", "btn-icon")}CSV</button>
      `)}
      <div class="grid two">
        <div class="card chart-card">
          <h3>Histórico mensual</h3>
          <div class="chart-wrap"><canvas id="chart-report-trend"></canvas></div>
        </div>
        <div class="card">
          <h3>Exportación</h3>
          <div class="form-grid two" style="margin-top:12px">
            ${selectField("Formato", "format", ["Excel", "PDF", "CSV"], "Excel")}
            ${selectField("Periodo", "period", state.data.payrollPeriods.map((p) => p.code), "NOM-2026-14")}
            ${selectField("Detalle", "detail", ["Resumen", "Detalle completo", "Por departamento"], "Detalle completo")}
            ${selectField("Empresa", "company", state.data.companies, state.ui.company)}
          </div>
        </div>
      </div>
      <div class="document-grid">
        ${reports.map((report) => `<article class="doc-card"><div class="split">${icon("chart", "mini-icon")} ${badge("Listo")}</div><strong>${safe(report)}</strong><div class="actions"><button class="btn secondary" data-action="export-report" data-format="CSV">${icon("download", "btn-icon")}Exportar</button></div></article>`).join("")}
      </div>
    </div>
  `;
}

function renderConfig() {
  const catalogs = [
    ["Empresas", state.data.companies.length],
    ["Sucursales", state.data.branches.length],
    ["Departamentos", state.data.departments.length],
    ["Puestos", state.data.positions.length],
    ["Centros de costos", 8],
    ["Bancos", state.data.banks.length],
    ["Formas de pago", 3],
    ["Tipos de contrato", state.data.contractTypes.length],
    ["Jornadas", 3],
    ["Horarios", 6],
    ["Conceptos de nómina", state.data.perceptions.length + state.data.deductions.length],
    ["Periodicidades", 4],
    ["Motivos de baja", 6],
    ["Prestaciones", 9],
    ["Usuarios", roles.length + 8],
    ["Roles", roles.length],
    ["Flujos de aprobación", 4],
    ["Plantillas de contrato", state.data.templates.length],
    ["Firmantes autorizados", 3]
  ];
  return `
    <div class="screen-stack">
      ${pageHeader("Configuración", "Catálogos editables, seguridad, flujos y parámetros generales.", `
        <button class="btn" data-route="template-editor">${icon("file-signature", "btn-icon")}Plantillas</button>
        <button class="btn secondary" data-route="audit">${icon("shield", "btn-icon")}Auditoría</button>
      `)}
      <div class="grid four">
        ${kpi("Control por roles", "6", "Permisos activos", "shield", "blue")}
        ${kpi("Periodos cerrados", "Bloqueados", "Protección activa", "lock", "green")}
        ${kpi("Datos bancarios", "Enmascarados", "CLABE protegida", "wallet", "teal")}
        ${kpi("Alertas contrato", "90/60/30/15/7/0", "Días configurados", "bell", "amber")}
      </div>
      <div class="document-grid">
        ${catalogs.map(([name, count]) => `<article class="doc-card"><div class="split">${icon("settings", "mini-icon")}${tag(`${count} registros`, "blue")}</div><strong>${safe(name)}</strong><div class="actions"><button class="btn secondary" data-action="edit-catalog" data-name="${safe(name)}">${icon("edit", "btn-icon")}Editar</button></div></article>`).join("")}
      </div>
    </div>
  `;
}

function activeTemplate() {
  const templates = state.data.templates || [];
  return templates.find((template) => Number(template.id) === Number(state.ui.selectedTemplateId)) || templates[0];
}

function renderTemplateCard(template, index, selectedId) {
  const tones = ["blue", "green", "teal", "amber", "red", "blue"];
  const tone = tones[index % tones.length];
  const activeClauses = (template.clauses || []).filter((clause) => clause.active).length;
  return `
    <button class="template-card ${Number(template.id) === Number(selectedId) ? "is-selected" : ""}" data-action="select-template" data-id="${template.id}">
      <span class="template-icon ${tone}">${icon(index % 3 === 0 ? "file-signature" : index % 3 === 1 ? "users" : "shield")}</span>
      <strong>${safe(template.name)}</strong>
      <span>Tipo: ${safe(template.type || "Laboral")}</span>
      <span>Actualizado: ${date(template.updatedAt)}</span>
      <span>${safe(template.version || "v1.0")}</span>
      <small>${number(activeClauses)} cláusulas activas</small>
      ${template.sourceFileName ? `<small>DOCX adjunto</small>` : ""}
    </button>
  `;
}

function templateSourceButton(template) {
  if (!template?.sourceUrl) return "";
  const fileName = template.sourceFileName || `${template.name}.docx`;
  return `<a class="btn secondary compact" href="${safe(template.sourceUrl)}" download="${safe(fileName)}">${icon("download", "btn-icon")}Descargar Word</a>`;
}

function renderTemplatePreview(template) {
  const clauses = (template.clauses || []).filter((clause) => clause.active);
  return `
    <article class="contract-preview template-preview-document">
      <div class="contract-preview-header">
        <div>
          <h4>${safe(template.name)}</h4>
          <p>${safe(template.type || "Laboral")} · Última actualización: ${date(template.updatedAt)}</p>
          ${template.sourceFileName ? `<p>Archivo adjunto: <strong>${safe(template.sourceFileName)}</strong></p>` : ""}
        </div>
        <div class="actions">
          ${templateSourceButton(template)}
          <span class="contract-folio">${safe(template.version || "v1.0")}</span>
        </div>
      </div>
      <div class="contract-preview-section">
        <h5>Modelo base del contrato</h5>
        ${String(template.body || "").split("\n").filter(Boolean).map((paragraph) => `
          <p>${safe(paragraph).replaceAll("{{", "<strong>{{").replaceAll("}}", "}}</strong>")}</p>
        `).join("")}
      </div>
      <div class="contract-preview-section">
        <h5>Cláusulas activas</h5>
        <div class="clause-chip-list">
          ${clauses.length ? clauses.map((clause) => `<span>${safe(clause.name)}</span>`).join("") : `<span>Sin cláusulas activas</span>`}
        </div>
      </div>
    </article>
  `;
}

function renderTemplateEditor() {
  const templates = state.data.templates || [];
  const selected = activeTemplate();
  const isEditing = Boolean(state.ui.templateEditing);
  const variables = ["Nombre del empleado", "CURP", "RFC", "Domicilio", "Puesto", "Departamento", "Fecha de ingreso", "Sueldo", "Jornada", "Horario", "Empresa", "Representante legal", "Fecha de firma", "Fecha de vencimiento", "Centro de trabajo", "Prestaciones"];
  return `
    <div class="screen-stack template-screen">
      ${pageHeader("Plantillas de contratos", "Administra los modelos de contratos disponibles para la organización.", `
        <button class="btn secondary" data-route="contracts-list">${icon("arrow-left", "btn-icon")}Contratos</button>
        <button class="btn" data-action="duplicate-template">${icon("plus", "btn-icon")}Nueva plantilla</button>
      `)}

      <section class="card template-library-card">
        <div class="template-carousel-shell">
          <button class="icon-btn carousel-arrow carousel-arrow-left" data-action="scroll-template-carousel" data-dir="-1" data-tooltip="Mover a la izquierda" aria-label="Mover carrusel a la izquierda">
            <span class="carousel-arrow-symbol" aria-hidden="true">&lsaquo;</span>
          </button>
          <div class="template-carousel" data-template-carousel data-pan-carousel>
            ${templates.map((template, index) => renderTemplateCard(template, index, selected?.id)).join("")}
          </div>
          <button class="icon-btn carousel-arrow carousel-arrow-right" data-action="scroll-template-carousel" data-dir="1" data-tooltip="Mover a la derecha" aria-label="Mover carrusel a la derecha">
            <span class="carousel-arrow-symbol" aria-hidden="true">&rsaquo;</span>
          </button>
        </div>
      </section>

      <section class="card template-detail-card">
        <div class="section-header">
          <div>
            <h3>${safe(selected?.name || "Plantilla")}</h3>
            <p>${safe(selected?.type || "Laboral")} · ${safe(selected?.version || "v1.0")} · Actualizado ${date(selected?.updatedAt)}</p>
          </div>
          <div class="actions">
            ${isEditing ? `
              <button class="btn success" type="submit" form="template-edit-form">${icon("check", "btn-icon")}Guardar cambios</button>
              <button class="btn secondary" data-action="cancel-template-edit">${icon("x", "btn-icon")}Cancelar</button>
            ` : `
              <button class="btn danger" data-action="delete-template">${icon("trash", "btn-icon")}Eliminar plantilla</button>
              <button class="btn secondary" data-action="edit-template">${icon("edit", "btn-icon")}Editar</button>
            `}
          </div>
        </div>

        ${isEditing ? `
          <form id="template-edit-form" class="template-edit-layout">
            <div class="form-card compact-form-card">
              <div class="form-section">
                <div class="form-grid three">
                  ${field("Nombre de la plantilla", "name", "text", selected?.name || "", true)}
                  ${field("Tipo", "type", "text", selected?.type || "Laboral", true)}
                  ${field("Versión", "version", "text", selected?.version || "v1.0", true)}
                </div>
              </div>
              <div class="form-section">
                ${textareaField("Texto del contrato", "body", selected?.body || "", 'rows="12"')}
              </div>
            </div>
            <aside class="panel">
              <div class="split"><h3>Variables</h3>${tag("Insertar", "blue")}</div>
              <div class="variable-palette">
                ${variables.map((item) => `<button type="button" class="variable-chip" data-action="insert-variable" data-variable="{{${safe(item)}}}">{{${safe(item)}}}</button>`).join("")}
              </div>
              <div class="split" style="margin-top:18px"><h3>Cláusulas</h3>${tag("Activar", "teal")}</div>
              <div class="template-clause-list">
                ${(selected?.clauses || []).map((clause, index) => `
                  <label class="check-row">
                    <input type="checkbox" name="clause-${index}" value="${safe(clause.name)}" ${clause.active ? "checked" : ""} />
                    ${safe(clause.name)}
                  </label>
                `).join("")}
              </div>
            </aside>
          </form>
        ` : `
          <div class="template-preview-scroll">
            ${renderTemplatePreview(selected)}
          </div>
          <div class="table-sticky-scroll template-sticky-scroll" data-sticky-x-scroll data-sticky-target=".template-preview-scroll" aria-hidden="true"><div></div></div>
        `}
      </section>
    </div>
  `;
}

function renderEmployeePortal() {
  const employee = employeeById(state.ui.selectedEmployeeId);
  const contracts = state.data.contracts.filter((contract) => contract.employeeId === employee.id);
  const receipts = state.data.receipts.filter((receipt) => receipt.employeeId === employee.id);
  return `
    <div class="portal-shell">
      ${pageHeader("Portal del empleado", `${employee.fullName} · ${employee.position}`, `
        <button class="btn secondary" data-action="view-employee" data-id="${employee.id}">${icon("users", "btn-icon")}Vista RH</button>
      `)}
      <div class="portal-hero">
        ${avatar(employee, "large")}
        <div>
          <h3>${safe(employee.fullName)}</h3>
          <div class="muted">${safe(employee.department)} · ${safe(employee.branch)}</div>
        </div>
        ${badge(employee.status)}
      </div>
      <div class="card">
        <div class="portal-nav">
          ${portalTabs.map((tab) => `<button class="tab ${state.ui.portalTab === tab ? "is-active" : ""}" data-action="set-portal-tab" data-tab="${safe(tab)}">${safe(tab)}</button>`).join("")}
        </div>
      </div>
      <div class="grid portal-grid">
        <div class="panel">
          <h3>${safe(state.ui.portalTab)}</h3>
          <div style="margin-top:12px">${renderPortalTab(employee, contracts, receipts)}</div>
        </div>
        <div class="panel">
          <h3>Acciones</h3>
          <div class="quick-actions" style="grid-template-columns:repeat(2,minmax(0,1fr));margin-top:12px">
            ${quickAction("Firmar contrato", "", "edit", "portal-sign-contract")}
            ${quickAction("Descargar recibo", "", "download", "portal-download-receipt")}
            ${quickAction("Solicitar vacaciones", "", "calendar-days", "portal-vacation")}
            ${quickAction("Registrar incidencia", "incidences", "calendar-alert")}
            ${quickAction("Actualizar cuenta", "", "wallet", "portal-update-bank")}
            ${quickAction("Subir documento", "", "upload", "portal-upload-document")}
          </div>
        </div>
      </div>
    </div>
  `;
}

function renderPortalTab(employee, contracts, receipts) {
  if (state.ui.portalTab === "Inicio") {
    return `<div class="definition-grid">
      ${definition("Contrato", contracts[0]?.status || "Sin contrato")}
      ${definition("Recibos", receipts.length)}
      ${definition("Vacaciones disponibles", employee.vacationDays)}
      ${definition("Saldo de préstamos", money(employee.loanBalance))}
      ${definition("Próximo pago", date(employee.nextPay))}
      ${definition("Avisos", "2")}
    </div>`;
  }
  if (state.ui.portalTab === "Mis datos") {
    return `<div class="definition-grid">${definition("Correo", employee.email)}${definition("Teléfono", employee.phone)}${definition("Cuenta", `${employee.bank} · ${masked(employee.clabe)}`)}${definition("Domicilio", employee.address)}</div>`;
  }
  if (state.ui.portalTab === "Mis contratos") return contracts.length ? renderContractsMini(contracts) : empty("Sin contratos");
  if (state.ui.portalTab === "Mis recibos") return receipts.length ? renderReceiptsMini(receipts) : empty("Sin recibos");
  if (state.ui.portalTab === "Mis vacaciones") return renderVacationsMini(state.data.vacations.filter((item) => item.employeeId === employee.id));
  if (state.ui.portalTab === "Mis incidencias") return renderIncidencesMini(state.data.incidences.filter((item) => item.employeeId === employee.id));
  if (state.ui.portalTab === "Mis documentos") return renderDocumentCards(employee);
  return `<div class="check-grid">${["Vacaciones", "Carta constancia", "Corrección de datos", "Préstamo", "Permiso", "Documento"].map((item) => `<button class="quick-action" data-action="portal-request">${icon("plus", "mini-icon")}<span>${safe(item)}</span></button>`).join("")}</div>`;
}

function vacationTakenDetails(employeeId) {
  return state.data.vacations
    .filter((item) => Number(item.employeeId) === Number(employeeId) && item.status === "Aprobada")
    .sort((a, b) => String(a.startDate).localeCompare(String(b.startDate)));
}

function vacationTakenDays(employeeId) {
  return vacationTakenDetails(employeeId).reduce((sum, item) => sum + Number(item.requested || 0), 0);
}

function vacationStatusControl(row, interactive = false) {
  if (!interactive) return badge(row.status);
  const isOpen = Number(state.ui.vacationStatusOpen) === Number(row.id);
  return `
    <div class="vacation-status-control">
      <button type="button" class="status-action vacation-status-trigger" data-action="toggle-vacation-status" data-id="${row.id}" aria-expanded="${isOpen}" aria-haspopup="menu" aria-label="Cambiar estatus de la solicitud de ${safe(row.employee)}">
        ${badge(row.status)}
        <span class="vacation-status-chevron" aria-hidden="true"></span>
      </button>
      ${isOpen ? `
        <div class="vacation-status-options" role="menu" aria-label="Resolver solicitud">
          <button type="button" class="vacation-status-option accept" data-action="accept-vacation" data-id="${row.id}" role="menuitem">${icon("check", "mini-icon")}Aceptar</button>
          <button type="button" class="vacation-status-option reject" data-action="reject-vacation" data-id="${row.id}" role="menuitem">${icon("x", "mini-icon")}Rechazar</button>
        </div>
      ` : ""}
    </div>
  `;
}

function vacationColumns({ interactiveStatus = false } = {}) {
  const columns = [
    { key: "employee", label: "Empleado" },
    { key: "available", label: "Disponibles" },
    { key: "requested", label: "Solicitados" },
    {
      key: "takenDays",
      label: "Días tomados",
      className: "text-center",
      tdClassName: "text-center",
      sortValue: (row) => vacationTakenDays(row.employeeId),
      render: (row) => `<button class="vacation-days-button" data-action="show-vacation-detail" data-id="${row.employeeId}" data-tooltip="Ver fechas tomadas">${number(vacationTakenDays(row.employeeId))}</button>`
    },
    { key: "startDate", label: "Inicio", render: (row) => date(row.startDate) },
    { key: "endDate", label: "Fin", render: (row) => date(row.endDate) },
    { key: "requestedBy", label: "Solicitante", render: (row) => safe(row.requestedBy || "Gerente de sucursal") },
    { key: "approver", label: "Aprobador" },
    { key: "status", label: "Estatus", render: (row) => vacationStatusControl(row, interactiveStatus) }
  ];
  return columns;
}

function renderVacations() {
  const managerScope = state.ui.role === "Gerente de sucursal" ? managerBranches() : [];
  const scopedEmployeeIds = managerScope.length ? new Set(branchEmployees(managerScope).map((employee) => Number(employee.id))) : null;
  const vacationRows = state.data.vacations.filter((row) => !scopedEmployeeIds || scopedEmployeeIds.has(Number(row.employeeId))).map((row) => ({
    ...row,
    _search: `${row.employee} ${row.status} ${row.approver} ${row.requestedBy || ""} ${row.startDate} ${row.endDate}`
  }));
  const pendingRows = vacationRows.filter((row) => row.status === "Pendiente");
  const resolvedRows = vacationRows.filter((row) => ["Aprobada", "Rechazada"].includes(row.status));
  return `
    <div class="screen-stack">
      ${pageHeader("Vacaciones de empleados", "Consulta de saldos, solicitudes y aprobaciones dentro del módulo de empleados.", `
        <button class="btn" data-action="portal-vacation">${icon("plus", "btn-icon")}Nueva solicitud</button>
        <button class="btn secondary" data-route="employees">${icon("users", "btn-icon")}Empleados</button>
      `)}
      <div class="vacation-section-heading">
        <div>
          <h3>Solicitudes pendientes</h3>
          <p>Solicitudes creadas por gerentes de sucursal y pendientes de aprobación por Recursos Humanos.</p>
        </div>
        ${tag(`${number(pendingRows.length)} pendientes`, "amber")}
      </div>
      ${renderTable({ id: "vacations-pending", rows: pendingRows, columns: vacationColumns({ interactiveStatus: true }), searchPlaceholder: "Buscar solicitudes pendientes", paginate: false, scrollY: "520px", emptyMessage: "Sin solicitudes pendientes" })}
      <div class="vacation-section-heading">
        <div>
          <h3>Historial de solicitudes</h3>
          <p>Solicitudes aceptadas o rechazadas por Recursos Humanos.</p>
        </div>
        ${tag(`${number(resolvedRows.length)} resueltas`, "green")}
      </div>
      ${renderTable({ id: "vacations-history", rows: resolvedRows, columns: vacationColumns(), searchPlaceholder: "Buscar historial", paginate: false, scrollY: "520px", emptyMessage: "Sin solicitudes resueltas" })}
    </div>
  `;
}

function renderAudit() {
  const columns = [
    { key: "user", label: "Usuario" },
    { key: "action", label: "Acción" },
    { key: "module", label: "Módulo" },
    { key: "record", label: "Registro" },
    { key: "oldValue", label: "Valor anterior" },
    { key: "newValue", label: "Valor nuevo" },
    { key: "date", label: "Fecha", render: (row) => date(row.date) },
    { key: "time", label: "Hora" },
    { key: "ip", label: "Dirección IP" }
  ];
  const rows = state.data.audit.map((row) => ({ ...row, _search: `${row.user} ${row.action} ${row.module} ${row.record}` }));
  return `
    <div class="screen-stack">
      ${pageHeader("Historial y auditoría", "Bitácora de cambios con usuario, módulo, valores, fecha, hora e IP.", `
        <button class="btn secondary" data-action="export-audit">${icon("download", "btn-icon")}Exportar</button>
      `)}
      <div class="grid four">
        ${kpi("Acciones", rows.length, "Registradas", "shield", "blue")}
        ${kpi("Acciones sensibles", rows.filter((row) => ["Nómina", "Pagos", "Deducciones"].includes(row.module)).length, "Salarios y pagos", "lock", "amber")}
        ${kpi("Usuarios", new Set(rows.map((row) => row.user)).size, "Con actividad", "users", "teal")}
        ${kpi("Última acción", rows[0]?.time || "N/A", rows[0]?.date || "", "bell", "green")}
      </div>
      ${renderTable({ id: "audit", rows, columns, searchPlaceholder: "Buscar en bitácora", pageSize: 10 })}
    </div>
  `;
}

function renderContractsMini(contracts) {
  return `<div class="mini-table"><table><thead><tr><th>Folio</th><th>Tipo</th><th>Inicio</th><th>Término</th><th>Estatus</th><th>Acciones</th></tr></thead><tbody>${contracts.map((contract) => `<tr><td>${safe(contract.folio)}</td><td>${safe(contract.type)}</td><td>${date(contract.startDate)}</td><td>${contract.endDate ? date(contract.endDate) : "Indefinido"}</td><td>${badge(displayContractStatus(contract))}</td><td><button class="icon-btn" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-signature" data-tooltip="Firmar">${icon("edit")}</button></td></tr>`).join("")}</tbody></table></div>`;
}

function renderReceiptsMini(receipts) {
  return `<div class="mini-table"><table><thead><tr><th>Folio</th><th>Periodo</th><th>Neto</th><th>Estatus</th><th>Acciones</th></tr></thead><tbody>${receipts.map((receipt) => `<tr><td>${safe(receipt.folio)}</td><td>${safe(receipt.period)}</td><td>${money(receipt.net)}</td><td>${badge(receipt.status)}</td><td><button class="icon-btn" data-action="view-receipt" data-id="${receipt.id}" data-tooltip="Ver">${icon("eye")}</button></td></tr>`).join("")}</tbody></table></div>`;
}

function renderIncidencesMini(incidences) {
  return `<div class="mini-table"><table><thead><tr><th>Tipo</th><th>Fecha</th><th>Importe</th><th>Estatus</th></tr></thead><tbody>${incidences.map((item) => `<tr><td>${safe(item.type)}</td><td>${date(item.date)}</td><td>${money(item.amount)}</td><td>${badge(item.status)}</td></tr>`).join("")}</tbody></table></div>`;
}

function renderVacationsMini(vacations) {
  return vacations.length ? `<div class="mini-table"><table><thead><tr><th>Inicio</th><th>Fin</th><th>Días</th><th>Estatus</th></tr></thead><tbody>${vacations.map((item) => `<tr><td>${date(item.startDate)}</td><td>${date(item.endDate)}</td><td>${item.requested}</td><td>${badge(item.status)}</td></tr>`).join("")}</tbody></table></div>` : empty("Sin solicitudes");
}

function renderPayrollMini(employee) {
  const calc = payrollCalc(employee);
  return `<div class="definition-grid">${definition("Sueldo mensual", money(employee.grossSalary))}${definition("Sueldo diario", money(employee.dailySalary))}${definition("SDI", money(employee.integratedDailySalary))}${definition("Periodicidad", employee.payFrequency)}${definition("Banco", employee.bank)}${definition("CLABE", masked(employee.clabe))}${definition("Percepciones periodo", money(calc.totalPerceptions))}${definition("Deducciones periodo", money(calc.totalDeductions))}${definition("Neto estimado", money(calc.net))}</div>`;
}

function empty(message) {
  return `<div class="empty-state">${safe(message)}</div>`;
}

function processStatus(status) {
  const text = String(status || "").toLowerCase();
  return ["aprobado", "firmado", "activo", "completo", "completado"].some((word) => text.includes(word)) ? "Completado" : "Pendiente";
}

function contractProcessRows(contract) {
  return (contract.approvals || []).map((step) => ({
    step: step.step,
    responsible: step.user || step.role || "Por asignar",
    date: step.date || "",
    comment: step.comment || step.status,
    status: processStatus(step.status)
  }));
}

function renderContractProcess(contract) {
  const rows = contractProcessRows(contract);
  return `
    <div class="process-summary">
      ${definition("Folio", contract.folio)}
      ${definition("Empleado", contract.employee)}
      ${definition("Tipo", contract.type)}
      ${definition("Estatus actual", displayContractStatus(contract))}
    </div>
    <div class="table-wrap process-table">
      <table>
        <thead>
          <tr>
            <th>Paso</th>
            <th>Responsable</th>
            <th>Fecha</th>
            <th>Comentario</th>
            <th>Estatus</th>
          </tr>
        </thead>
        <tbody>
          ${rows.map((row, index) => `
            <tr>
              <td><span class="process-step-number">${index + 1}</span><strong>${safe(row.step)}</strong></td>
              <td>${safe(row.responsible)}</td>
              <td>${row.date ? date(row.date) : "Pendiente"}</td>
              <td>${safe(row.comment || "Sin comentario")}</td>
              <td>${badge(row.status)}</td>
            </tr>
          `).join("")}
        </tbody>
      </table>
    </div>
  `;
}

function renderModal() {
  const modal = state.ui.modal;
  if (!modal) return "";
  if (modal.type === "confirm") {
    return `
      <div class="modal-backdrop" role="dialog" aria-modal="true">
        <div class="modal">
          <header><h3>${safe(modal.title)}</h3><button class="icon-btn" data-action="close-modal">${icon("x")}</button></header>
          <div class="modal-body"><p>${safe(modal.body)}</p></div>
          <footer>
            <button class="btn secondary" data-action="close-modal">${icon("x", "btn-icon")}Cancelar</button>
            <button class="btn ${modal.tone || ""}" data-action="${safe(modal.confirmAction)}" data-payload="${safe(JSON.stringify(modal.payload || {}))}">${icon("check", "btn-icon")}Confirmar</button>
          </footer>
        </div>
      </div>
    `;
  }
  if (modal.type === "deduction") {
    const deduction = state.data.deductions.find((item) => item.id === modal.id);
    return `
      <div class="modal-backdrop" role="dialog" aria-modal="true">
        <form id="deduction-modal-form" class="modal">
          <header><h3>Editar deducción</h3><button class="icon-btn" type="button" data-action="close-modal">${icon("x")}</button></header>
          <div class="modal-body">
            <input type="hidden" name="id" value="${deduction.id}" />
            <div class="form-grid two">
              ${field("Clave", "key", "text", deduction.key, true)}
              ${field("Nombre", "name", "text", deduction.name, true)}
              ${field("Porcentaje", "percent", "number", deduction.percent, false, 'step="0.01"')}
              ${field("Importe fijo", "fixedAmount", "number", deduction.fixedAmount, false, 'step="0.01"')}
              ${field("Tope", "cap", "number", deduction.cap, false, 'step="0.01"')}
              ${field("Saldo pendiente", "balance", "number", deduction.balance, false, 'step="0.01"')}
              ${field("Fórmula", "formula", "text", deduction.formula)}
              ${selectField("Estatus", "status", ["Activo", "Inactivo"], deduction.status)}
            </div>
          </div>
          <footer>
            <button class="btn secondary" type="button" data-action="close-modal">${icon("x", "btn-icon")}Cancelar</button>
            <button class="btn" type="submit">${icon("check", "btn-icon")}Guardar</button>
          </footer>
        </form>
      </div>
    `;
  }
  if (modal.type === "contract-process") {
    const contract = contractById(modal.id);
    return `
      <div class="modal-backdrop" role="dialog" aria-modal="true">
        <div class="modal process-modal">
          <header><h3>Proceso del contrato</h3><button class="icon-btn" type="button" data-action="close-modal">${icon("x")}</button></header>
          <div class="modal-body">
            ${renderContractProcess(contract)}
          </div>
          <footer>
            <button class="btn secondary" type="button" data-action="close-modal">${icon("x", "btn-icon")}Cerrar</button>
            <button class="btn" type="button" data-action="select-contract-route" data-id="${contract.id}" data-route-target="contract-approval">${icon("check", "btn-icon")}Ver autorización</button>
          </footer>
        </div>
      </div>
    `;
  }
  if (modal.type === "contract-model") {
    const contract = contractById(modal.id);
    const model = contractModel(contract);
    return `
      <div class="modal-backdrop" role="dialog" aria-modal="true">
        <form id="contract-model-form" class="modal process-modal">
          <header><h3>Modelo de contrato</h3><button class="icon-btn" type="button" data-action="close-modal">${icon("x")}</button></header>
          <div class="modal-body">
            <input type="hidden" name="contractId" value="${contract.id}" />
            <div class="process-summary">
              ${definition("Contrato", contract.folio)}
              ${definition("Empleado", contract.employee)}
              ${definition("Modelo actual", model.name)}
              ${definition("Formato adjunto", model.file)}
            </div>
            <div class="form-grid two">
              <label class="form-field">
                <span>Plantilla base *</span>
                <select class="select" name="templateId" required>
                  ${state.data.templates.map((template) => `<option value="${template.id}" ${template.name === model.name ? "selected" : ""}>${safe(template.name)} · ${safe(template.version)}</option>`).join("")}
                </select>
              </label>
              ${field("Versión del modelo", "version", "text", model.version, true)}
              ${field("Nombre del formato", "modelFileName", "text", model.file, true)}
              <label class="form-field">
                <span>Adjuntar formato</span>
                <input class="input" type="file" name="modelFile" accept=".doc,.docx,.pdf" />
              </label>
            </div>
            <div class="inline-alert model-note">${icon("file", "mini-icon")} El nuevo contrato tomará este formato como base para sus actualizaciones.</div>
          </div>
          <footer>
            <button class="btn secondary" type="button" data-action="prepare-contract-from-model" data-id="${contract.id}">${icon("copy", "btn-icon")}Usar para nuevo contrato</button>
            <button class="btn secondary" type="button" data-action="close-modal">${icon("x", "btn-icon")}Cancelar</button>
            <button class="btn" type="submit">${icon("check", "btn-icon")}Guardar modelo</button>
          </footer>
        </form>
      </div>
    `;
  }
  if (modal.type === "signed-contract") {
    const contract = modal.contractId ? contractById(modal.contractId) : contractForEmployee(modal.employeeId);
    const employee = employeeById(contract?.employeeId || modal.employeeId);
    const signedFile = contract ? signedContractFileName(contract) : "";
    const attachedFile = contract?.signedContractFile || "";
    return `
      <div class="modal-backdrop" role="dialog" aria-modal="true">
        <div class="modal signed-contract-modal">
          <header><h3>Contrato firmado</h3><button class="icon-btn" type="button" data-action="close-modal">${icon("x")}</button></header>
          <div class="modal-body">
            ${contract ? `
              <div class="signed-contract-layout">
                <section class="signed-contract-preview">
                  <div class="pdf-toolbar">
                    <strong>${icon("file", "mini-icon")} PDF del contrato firmado</strong>
                    ${tag(signedFile ? (attachedFile ? "Adjunto" : "PDF firmado") : "Pendiente de adjuntar", signedFile ? "green" : "amber")}
                  </div>
                  ${contractPreview(contractToDraft(contract), employee)}
                </section>
                <aside class="signed-contract-side">
                  <h4>Documento adjunto por RH</h4>
                  <div class="definition-grid">
                    ${definition("Empleado", employee.fullName)}
                    ${definition("Contrato", contract.folio)}
                    ${definition("Estatus", displayContractStatus(contract))}
                    ${definition("Firmas", `${contract.employeeSignature} / ${contract.companySignature}`)}
                  </div>
                  <div class="signed-contract-file ${signedFile ? "is-attached" : ""}">
                    ${icon("file", "mini-icon")}
                    <div>
                      <strong>${safe(signedFile || "Sin PDF firmado adjunto")}</strong>
                      <span>${attachedFile ? `Adjuntado ${date(contract.signedContractAttachedAt || today())}` : signedFile ? "PDF firmado simulado. RH puede adjuntar el documento final." : "El area de RH debe subir el documento firmado."}</span>
                    </div>
                  </div>
                  <label class="btn secondary signed-contract-upload">
                    ${icon("upload", "btn-icon")}Adjuntar PDF firmado
                    <input type="file" accept=".pdf" data-signed-contract-upload="${contract.id}" />
                  </label>
                </aside>
              </div>
            ` : empty("Este empleado no tiene contrato registrado para mostrar.")}
          </div>
          <footer>
            <button class="btn secondary" type="button" data-action="close-modal">${icon("x", "btn-icon")}Cerrar</button>
          </footer>
        </div>
      </div>
    `;
  }
  if (modal.type === "vacation-detail") {
    const employee = employeeById(modal.employeeId);
    const details = vacationTakenDetails(employee.id);
    const totalDays = vacationTakenDays(employee.id);
    return `
      <div class="modal-backdrop" role="dialog" aria-modal="true">
        <div class="modal compact-modal">
          <header><h3>Fechas de vacaciones tomadas</h3><button class="icon-btn" type="button" data-action="close-modal">${icon("x")}</button></header>
          <div class="modal-body">
            <div class="definition-grid vacation-detail-summary">
              ${definition("Empleado", employee.fullName)}
              ${definition("Días tomados", `${number(totalDays)} días`)}
            </div>
            ${details.length ? `
              <div class="mini-table vacation-detail-table">
                <table>
                  <thead><tr><th>Inicio</th><th>Fin</th><th>Días</th><th>Estatus</th></tr></thead>
                  <tbody>
                    ${details.map((item) => `
                      <tr>
                        <td>${date(item.startDate)}</td>
                        <td>${date(item.endDate)}</td>
                        <td>${number(item.requested)}</td>
                        <td>${badge(item.status)}</td>
                      </tr>
                    `).join("")}
                  </tbody>
                </table>
              </div>
            ` : empty("Este empleado todavía no tiene vacaciones tomadas.")}
          </div>
          <footer>
            <button class="btn secondary" type="button" data-action="close-modal">${icon("x", "btn-icon")}Cerrar</button>
          </footer>
        </div>
      </div>
    `;
  }
  if (modal.type === "overtime-cutoff") {
    const cutoffs = overtimeCutoffs();
    return `
      <div class="modal-backdrop" role="dialog" aria-modal="true">
        <form id="overtime-cutoff-form" class="modal">
          <header><h3>Rango de corte de horas extras</h3><button class="icon-btn" type="button" data-action="close-modal">${icon("x")}</button></header>
          <div class="modal-body">
            <p class="muted">Define los dias del mes en los que se cerrara el periodo de horas extras. Por defecto se usa corte quincenal: dia 15 y dia 30.</p>
            <div class="form-grid two" style="margin-top:14px">
              ${field("Primer dia de corte", "first", "number", cutoffs.first, true, 'min="1" max="31"')}
              ${field("Segundo dia de corte", "second", "number", cutoffs.second, true, 'min="1" max="31"')}
            </div>
            <div class="inline-alert model-note">${icon("calendar-days", "mini-icon")} Las nuevas solicitudes se agruparan automaticamente con estos cortes.</div>
          </div>
          <footer>
            <button class="btn secondary" type="button" data-action="close-modal">${icon("x", "btn-icon")}Cancelar</button>
            <button class="btn" type="submit">${icon("check", "btn-icon")}Guardar rango</button>
          </footer>
        </form>
      </div>
    `;
  }
  return "";
}

function openConfirm(title, body, confirmAction, payload = {}, tone = "") {
  state.ui.modal = { type: "confirm", title, body, confirmAction, payload, tone };
  render();
}

function filterContractPersonRows() {
  const query = normalizeText(document.querySelector("[data-contract-person-search]")?.value);
  const company = document.querySelector("[data-contract-person-company-filter]")?.value || "";
  const newHireOnly = state.ui.contractPersonType === "employee" && Boolean(state.ui.contractEmployeeNewHireOnly);
  let visibleCount = 0;
  document.querySelectorAll("[data-contract-person-row]").forEach((row) => {
    const haystack = normalizeText(row.dataset.search);
    const matchesSearch = !query || haystack.includes(query);
    const matchesCompany = !company || row.dataset.company === company;
    const matchesNewHire = !newHireOnly || row.dataset.newHire === "1";
    const visible = matchesSearch && matchesCompany && matchesNewHire;
    row.hidden = !visible;
    if (visible) visibleCount += 1;
  });
  const emptyState = document.querySelector("[data-contract-person-empty]");
  if (emptyState) emptyState.hidden = visibleCount > 0;
}

document.addEventListener("click", (event) => {
  if (event.target.closest("[data-date-filter-group]")) {
    event.stopPropagation();
    return;
  }

  const routeTarget = event.target.closest("[data-route]");
  if (routeTarget) {
    event.preventDefault();
    navigate(routeTarget.dataset.route);
    return;
  }

  const actionTarget = event.target.closest("[data-action]");
  if (actionTarget) {
    const isCheckableControl = actionTarget.matches('input[type="checkbox"], input[type="radio"]');
    if (!isCheckableControl) event.preventDefault();
    handleAction(actionTarget.dataset.action, actionTarget);
  }
});

document.addEventListener("input", (event) => {
  const search = event.target.closest("[data-table-search]");
  if (search) {
    const id = search.dataset.tableSearch;
    tableState[id] = tableState[id] || { search: "", page: 1, sortKey: "", sortDir: "asc", filters: {} };
    tableState[id].search = search.value;
    tableState[id].page = 1;
    render();
    return;
  }

  const contractPersonSearch = event.target.closest("[data-contract-person-search]");
  if (contractPersonSearch) {
    filterContractPersonRows();
    return;
  }

  const excelFilterSearch = event.target.closest("[data-excel-filter-search]");
  if (excelFilterSearch) {
    const query = String(excelFilterSearch.value || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    const panel = excelFilterSearch.closest(".excel-filter-panel");
    panel?.querySelectorAll("[data-excel-filter-option]").forEach((option) => {
      const haystack = String(option.dataset.search || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
      option.hidden = Boolean(query) && !haystack.includes(query);
    });
    panel?.querySelectorAll("[data-date-filter-node]").forEach((node) => {
      const haystack = String(node.dataset.search || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
      const hasVisibleChild = [...node.querySelectorAll("[data-excel-filter-option]")].some((option) => !option.hidden);
      node.hidden = Boolean(query) && !haystack.includes(query) && !hasVisibleChild;
      if (query && !node.hidden) node.open = true;
    });
    return;
  }

  if (event.target.id === "template-body") {
    activeTemplate().body = event.target.value;
    saveState();
    return;
  }

  const draftField = event.target.closest("[data-draft]");
  if (draftField) {
    updateContractDraftField(draftField);
  }
});

document.addEventListener("change", (event) => {
  const selectAllExcelFilter = event.target.closest("[data-excel-filter-select-all]");
  if (selectAllExcelFilter) {
    const panel = selectAllExcelFilter.closest(".excel-filter-panel");
    panel?.querySelectorAll("[data-excel-filter-value]").forEach((checkbox) => {
      checkbox.checked = selectAllExcelFilter.checked;
    });
    panel?.querySelectorAll("[data-date-filter-group]").forEach((checkbox) => {
      checkbox.checked = selectAllExcelFilter.checked;
      checkbox.indeterminate = false;
    });
    return;
  }

  const dateFilterGroup = event.target.closest("[data-date-filter-group]");
  if (dateFilterGroup) {
    const node = dateFilterGroup.closest("[data-date-filter-node]");
    node?.querySelectorAll("[data-excel-filter-value]").forEach((checkbox) => {
      checkbox.checked = dateFilterGroup.checked;
    });
    updateDateFilterGroupStates(dateFilterGroup.closest(".excel-filter-panel"));
    return;
  }

  const excelFilterValue = event.target.closest("[data-excel-filter-value]");
  if (excelFilterValue) {
    const panel = excelFilterValue.closest(".excel-filter-panel");
    const options = [...(panel?.querySelectorAll("[data-excel-filter-value]") || [])];
    const selectAll = panel?.querySelector("[data-excel-filter-select-all]");
    if (selectAll) selectAll.checked = options.length > 0 && options.every((checkbox) => checkbox.checked);
    updateDateFilterGroupStates(panel);
    return;
  }

  const contractPersonCompanyFilter = event.target.closest("[data-contract-person-company-filter]");
  if (contractPersonCompanyFilter) {
    filterContractPersonRows();
    return;
  }

  const candidateCvUpload = event.target.closest("[data-candidate-cv-upload]");
  if (candidateCvUpload) {
    const file = candidateCvUpload.files?.[0];
    if (!file) return;
    uploadCandidateCv(Number(candidateCvUpload.dataset.candidateCvUpload), file.name);
    return;
  }

  const documentUpload = event.target.closest("[data-document-upload]");
  if (documentUpload) {
    const index = documentUpload.dataset.documentUpload;
    const fileName = documentUpload.files?.[0]?.name || "Sin archivo adjunto";
    const row = documentUpload.closest(".employee-document-row");
    const check = document.querySelector(`[data-document-check="${index}"]`);
    const label = document.querySelector(`[data-document-file-name="${index}"]`);
    if (check && documentUpload.files?.length) check.checked = true;
    if (label) label.textContent = fileName;
    if (row) row.classList.toggle("is-attached", Boolean(documentUpload.files?.length));
    return;
  }

  const signedContractUpload = event.target.closest("[data-signed-contract-upload]");
  if (signedContractUpload) {
    const file = signedContractUpload.files?.[0];
    if (!file) return;
    const contract = contractById(Number(signedContractUpload.dataset.signedContractUpload));
    const employee = employeeById(contract.employeeId);
    contract.signedContractFile = file.name;
    contract.signedContractAttachedAt = today();
    const existingDoc = employee.documents.find((doc) => String(doc.name || "").includes(contract.folio));
    if (existingDoc) {
      existingDoc.status = "Completo";
      existingDoc.date = today();
      existingDoc.file = file.name;
    } else {
      employee.documents.push({ name: `Contrato firmado ${contract.folio}`, status: "Completo", date: today(), file: file.name });
    }
    addAudit("AdjuntÃ³ contrato firmado", "Empleados", employee.number, "Sin PDF firmado", file.name);
    toast("Contrato firmado adjuntado al expediente.", "success");
    saveState();
    render();
    return;
  }

  const actionControl = event.target.closest("[data-action]");
  if (actionControl && ["change-role", "change-company"].includes(actionControl.dataset.action)) {
    handleAction(actionControl.dataset.action, actionControl);
    return;
  }

  const filter = event.target.closest("[data-table-filter]");
  if (filter) {
    const id = filter.dataset.tableFilter;
    tableState[id] = tableState[id] || { search: "", page: 1, sortKey: "", sortDir: "asc", filters: {} };
    tableState[id].filters[filter.dataset.filterKey] = filter.value;
    tableState[id].page = 1;
    render();
    return;
  }

  const draftField = event.target.closest("[data-draft]");
  if (draftField) {
    updateContractDraftField(draftField);
  }
});

document.addEventListener("submit", (event) => {
  if (event.target.id === "login-form") {
    event.preventDefault();
    const form = new FormData(event.target);
    state.ui.role = form.get("role");
    state.ui.company = form.get("company");
    navigate("dashboard");
  }

  if (event.target.id === "employee-form") {
    event.preventDefault();
    createEmployee(new FormData(event.target));
  }

  if (event.target.id === "payroll-period-form") {
    event.preventDefault();
    createPayrollPeriod(new FormData(event.target));
  }

  if (event.target.id === "incidence-form") {
    event.preventDefault();
    createIncidence(new FormData(event.target));
  }

  if (event.target.id === "deduction-modal-form") {
    event.preventDefault();
    updateDeduction(new FormData(event.target));
  }

  if (event.target.id === "termination-form") {
    event.preventDefault();
    const form = new FormData(event.target);
    const employeeId = String(form.get("employeeId")).split("|")[0];
    state.ui.selectedEmployeeId = Number(employeeId);
    openConfirm("Dar de baja empleado", "Se actualizará el estatus del empleado, se cerrará su expediente y se generará evidencia de terminación.", "confirm-termination", { employeeId: Number(employeeId) }, "danger");
  }

  if (event.target.id === "candidate-form") {
    event.preventDefault();
    saveCandidate(new FormData(event.target));
  }

  if (event.target.id === "contract-model-form") {
    event.preventDefault();
    saveContractModel(new FormData(event.target));
  }

  if (event.target.id === "template-edit-form") {
    event.preventDefault();
    saveSelectedTemplate(new FormData(event.target));
  }

  if (event.target.id === "overtime-request-form") {
    event.preventDefault();
    createOvertimeRequest(new FormData(event.target));
  }

  if (event.target.id === "overtime-cutoff-form") {
    event.preventDefault();
    saveOvertimeCutoff(new FormData(event.target));
  }
});

function handleAction(action, target) {
  const data = target?.dataset || {};
  switch (action) {
    case "change-dashboard-calendar-month": {
      const direction = Number(data.direction || 0);
      const currentYear = Number.isInteger(state.dashboardCalendarYear) ? state.dashboardCalendarYear : 2026;
      const currentMonth = Number.isInteger(state.dashboardCalendarMonth) ? state.dashboardCalendarMonth : 6;
      const nextDate = new Date(currentYear, currentMonth + direction, 1);
      state.dashboardCalendarYear = nextDate.getFullYear();
      state.dashboardCalendarMonth = nextDate.getMonth();
      saveState();
      render();
      break;
    }
    case "toggle-sidebar":
      state.ui.sidebarCollapsed = !state.ui.sidebarCollapsed;
      saveState();
      render();
      break;
    case "logout":
      state.ui.route = "login";
      saveState();
      render();
      break;
    case "change-role":
      state.ui.role = target.value;
      saveState();
      render();
      break;
    case "change-company":
      state.ui.company = target.value;
      saveState();
      render();
      break;
    case "toggle-employee-excel-filter":
      state.ui.employeeFilterOpen = !state.ui.employeeFilterOpen;
      saveState();
      render();
      break;
    case "toggle-employee-list":
      state.ui.employeeListOpen = !state.ui.employeeListOpen;
      saveState();
      render();
      break;
    case "apply-employee-excel-filter": {
      const panel = target.closest(".excel-filter-panel");
      const options = [...(panel?.querySelectorAll("[data-excel-filter-value]") || [])];
      const selected = options.filter((option) => option.checked).map((option) => option.value);
      state.ui.employeeNameFilter = selected.length === options.length ? [] : selected.length ? selected : ["__NONE__"];
      state.ui.employeeFilterOpen = false;
      tableState.employees = tableState.employees || { search: "", page: 1, sortKey: "", sortDir: "asc", filters: {} };
      tableState.employees.page = 1;
      saveState();
      render();
      break;
    }
    case "cancel-employee-excel-filter":
      state.ui.employeeFilterOpen = false;
      saveState();
      render();
      break;
    case "toggle-table-column-filter": {
      const next = { table: data.table, key: data.key };
      const currentOpen = state.ui.tableColumnFilterOpen;
      state.ui.tableColumnFilterOpen = currentOpen?.table === next.table && currentOpen?.key === next.key ? null : next;
      render();
      break;
    }
    case "apply-table-column-filter": {
      const panel = target.closest("[data-table-column-filter-panel]");
      const table = panel?.dataset.table;
      const key = panel?.dataset.key;
      if (!table || !key) break;
      const options = [...panel.querySelectorAll("[data-excel-filter-value]")];
      const selected = options.filter((option) => option.checked).map((option) => option.value);
      tableState[table] = tableState[table] || { search: "", page: 1, sortKey: "", sortDir: "asc", filters: {}, columnFilters: {} };
      tableState[table].columnFilters = tableState[table].columnFilters || {};
      if (!selected.length) tableState[table].columnFilters[key] = ["__SIN_RESULTADOS__"];
      else if (selected.length === options.length) delete tableState[table].columnFilters[key];
      else tableState[table].columnFilters[key] = selected;
      tableState[table].page = 1;
      state.ui.tableColumnFilterOpen = null;
      render();
      break;
    }
    case "cancel-table-column-filter":
      state.ui.tableColumnFilterOpen = null;
      render();
      break;
    case "open-overtime-cutoff":
      state.ui.modal = { type: "overtime-cutoff" };
      render();
      break;
    case "focus-overtime-form":
      document.querySelector("#overtime-request-panel")?.scrollIntoView({ behavior: "smooth" });
      break;
    case "download-overtime-format":
      downloadOvertimeFormat();
      break;
    case "show-contract-process":
      state.ui.modal = { type: "contract-process", id: Number(data.id) };
      render();
      break;
    case "toggle-contract-alerts":
      state.ui.contractAlertsOpen = !state.ui.contractAlertsOpen;
      saveState();
      render();
      break;
    case "open-contract-model":
      state.ui.modal = { type: "contract-model", id: Number(data.id) };
      render();
      break;
    case "prepare-contract-from-model":
      prepareContractFromModel(Number(data.id));
      break;
    case "open-candidate-form":
      state.ui.candidateFormOpen = true;
      state.ui.candidateEditingId = null;
      saveState();
      render();
      requestAnimationFrame(() => document.querySelector('#candidate-form input[name="name"]')?.focus({ preventScroll: true }));
      break;
    case "edit-candidate":
      state.ui.candidateFormOpen = true;
      state.ui.candidateEditingId = Number(data.id);
      saveState();
      render();
      requestAnimationFrame(() => document.querySelector('#candidate-form input[name="name"]')?.focus({ preventScroll: true }));
      break;
    case "close-candidate-form":
      state.ui.candidateFormOpen = false;
      state.ui.candidateEditingId = null;
      saveState();
      render();
      break;
    case "upload-cv":
      uploadCandidateCv();
      break;
    case "enrich-candidate-data":
      enrichCandidateData();
      break;
    case "refresh-candidates":
      refreshCandidates();
      break;
    case "export-candidates":
      exportCandidates();
      break;
    case "advance-candidate":
      advanceCandidate(Number(data.id));
      break;
    case "schedule-candidate":
      scheduleCandidate(Number(data.id));
      break;
    case "toggle-candidate-selected":
      toggleCandidateSelected(Number(data.id));
      break;
    case "send-candidate-offer":
      sendCandidateOffer(Number(data.id));
      break;
    case "download-cv":
      downloadCandidateCv(Number(data.id));
      break;
    case "download-contract": {
      const contract = contractById(Number(data.id));
      if (blockContractOutputUntilLegalApproval(contract)) break;
      downloadText(`contrato-${data.id}.txt`, `Contrato laboral simulado ${data.id}`);
      toast("Contrato preparado para descarga.", "success");
      break;
    }
    case "view-signed-contract": {
      const contract = data.contractId ? contractById(Number(data.contractId)) : contractForEmployee(Number(data.id));
      if (contract && blockContractOutputUntilLegalApproval(contract)) break;
      const employeeId = contract?.employeeId || Number(data.id);
      state.ui.selectedEmployeeId = employeeId;
      state.ui.modal = { type: "signed-contract", employeeId, contractId: contract?.id || null };
      render();
      break;
    }
    case "download-draft-contract":
      toast("Guarda el contrato y espera la aprobación de Dirección Jurídica antes de imprimirlo.", "warning");
      break;
    case "renew-contract-from-list":
      state.ui.selectedContractId = Number(data.id);
      navigate("contract-renewal");
      break;
    case "view-employee":
      state.ui.selectedEmployeeId = Number(data.id);
      state.ui.selectedPayrollEmployeeId = Number(data.id);
      navigate("employee-profile");
      break;
    case "select-employee-route":
      state.ui.selectedEmployeeId = Number(data.id);
      state.ui.selectedPayrollEmployeeId = Number(data.id);
      navigate(data.routeTarget);
      break;
    case "select-contract-route":
      state.ui.selectedContractId = Number(data.id);
      state.ui.modal = null;
      if (data.routeTarget === "contract-signature") {
        const contract = contractById(state.ui.selectedContractId);
        if (blockContractOutputUntilLegalApproval(contract)) {
          navigate("contract-approval");
          break;
        }
      }
      navigate(data.routeTarget);
      break;
    case "start-contract-process":
      startContractProcess();
      break;
    case "save-contract-process":
      saveContractProcess();
      break;
    case "continue-contract-process":
      continueContractProcess(data.id);
      break;
    case "toggle-new-hire-contract-employees":
      state.ui.contractEmployeeNewHireOnly = !state.ui.contractEmployeeNewHireOnly;
      saveState();
      render();
      break;
    case "view-receipt":
      state.ui.selectedReceiptId = Number(data.id);
      navigate("receipt-view");
      break;
    case "select-payroll-employee":
      state.ui.selectedPayrollEmployeeId = Number(data.id);
      saveState();
      render();
      break;
    case "select-payroll-employee-route":
      state.ui.selectedPayrollEmployeeId = Number(data.id);
      navigate(data.routeTarget);
      break;
    case "set-employee-tab":
      state.ui.employeeTab = data.tab;
      saveState();
      render();
      break;
    case "set-portal-tab":
      state.ui.portalTab = data.tab;
      saveState();
      render();
      break;
    case "sort-table":
      sortTable(data.table, data.key);
      break;
    case "table-page":
      tableState[data.table].page = Number(data.page);
      render();
      break;
    case "set-contract-person-type":
      state.ui.contractPersonType = data.personType === "employee" ? "employee" : "candidate";
      state.ui.contractEmployeeNewHireOnly = false;
      saveState();
      render();
      break;
    case "draft-candidate": {
      const candidate = candidateById(data.id);
      if (!candidate) {
        toast("No se encontró el candidato.", "error");
        break;
      }
      const candidateEmployee = ensureCandidateContractEmployee(candidate);
      state.ui.contractPersonType = "candidate";
      state.ui.contractDraft.employeeId = candidateEmployee.id;
      state.ui.contractDraft.candidateId = candidate.id;
      state.ui.contractDraft.position = candidateEmployee.position;
      state.ui.contractDraft.department = candidateEmployee.department;
      state.ui.contractDraft.salary = candidateEmployee.grossSalary;
      state.ui.contractDraft.company = candidateEmployee.company;
      state.ui.contractDraft.newHire = true;
      state.ui.contractStep = 2;
      saveState();
      render();
      break;
    }
    case "draft-employee": {
      state.ui.contractPersonType = "employee";
      state.ui.contractDraft.employeeId = Number(data.id);
      state.ui.contractDraft.candidateId = null;
      const employee = employeeById(data.id);
      state.ui.contractDraft.position = employee.position;
      state.ui.contractDraft.department = employee.department;
      state.ui.contractDraft.salary = employee.grossSalary;
      state.ui.contractDraft.company = employee.company;
      state.ui.contractDraft.newHire = Boolean(employee.newHire);
      state.ui.contractStep = 2;
      saveState();
      render();
      break;
    }
    case "toggle-draft-clause":
      toggleDraftClause(target.value, target.checked);
      break;
    case "prev-contract-step":
      state.ui.contractStep = Math.max(1, state.ui.contractStep - 1);
      saveState();
      render();
      break;
    case "next-contract-step":
      state.ui.contractStep = Math.min(contractSteps.length, state.ui.contractStep + 1);
      saveState();
      render();
      break;
    case "save-contract-active":
      saveContractFromDraft();
      break;
    case "advance-contract-approval":
      advanceContractApproval();
      break;
    case "contract-correction":
    case "payroll-correction":
      toast("Corrección solicitada y registrada en bitácora.", "success");
      addAudit("Solicitó corrección", routeTitles[state.ui.route], "Flujo", "Pendiente", "Corrección");
      saveState();
      break;
    case "contract-reject":
    case "payroll-reject":
      toast("Rechazo registrado.", "error");
      addAudit("Rechazó flujo", routeTitles[state.ui.route], "Flujo", "Pendiente", "Rechazado");
      saveState();
      break;
    case "sign-contract":
      if (blockContractOutputUntilLegalApproval(contractById(state.ui.selectedContractId))) break;
      openConfirm("Firmar contrato", "Se registrará firma electrónica, evidencia de aceptación y el contrato pasará a activo.", "confirm-sign-contract", { id: state.ui.selectedContractId }, "success");
      break;
    case "renew-contract":
      renewContract();
      break;
    case "approve-incidence":
      updateIncidence(Number(data.id), "Aprobada");
      break;
    case "reject-incidence":
      updateIncidence(Number(data.id), "Rechazada");
      break;
    case "focus-incidence-form":
      document.querySelector("#incidence-form")?.scrollIntoView({ behavior: "smooth" });
      break;
    case "edit-deduction":
      state.ui.modal = { type: "deduction", id: Number(data.id) };
      render();
      break;
    case "edit-perception":
      toast("El editor de percepciones quedó listo para extenderse con la misma estructura de deducciones.", "success");
      break;
    case "recalculate-payroll":
      state.ui.payrollStep = 9;
      addAudit("Recalculó nómina", "Nómina", "NOM-2026-14", "Calculada", "Recalculada");
      toast("Nómina recalculada con incidencias autorizadas.", "success");
      saveState();
      render();
      break;
    case "mark-reviewed":
      toast("Empleado marcado como revisado.", "success");
      break;
    case "exclude-payroll-employee":
      toast("Empleado excluido visualmente del periodo.", "success");
      break;
    case "view-formula":
      openConfirm("Fórmula de cálculo", "Sueldo base más percepciones gravadas y exentas, menos ISR, IMSS y deducciones configuradas. El costo patronal se estima con carga social.", "close-modal", {}, "");
      break;
    case "view-history":
      navigate("audit");
      break;
    case "resolve-critical-validations":
      resolveCriticalValidations();
      break;
    case "approve-payroll":
      approvePayroll();
      break;
    case "approve-payroll-fortnight":
      approvePayrollFortnightSummary();
      break;
    case "toggle-payroll-summary-table":
      state.ui.payrollSummaryCollapsed = !state.ui.payrollSummaryCollapsed;
      saveState();
      render();
      break;
    case "toggle-payroll-period-table":
      state.ui.payrollSummaryCollapsedByPeriod = state.ui.payrollSummaryCollapsedByPeriod || {};
      state.ui.payrollSummaryCollapsedByPeriod[data.period] = !state.ui.payrollSummaryCollapsedByPeriod[data.period];
      saveState();
      render();
      break;
    case "send-payroll-history":
      sendPayrollFortnightToHistory();
      break;
    case "pay-approved-payroll":
      payApprovedPayrollFortnight();
      break;
    case "approve-payroll-period-table":
      approvePayrollPeriodTable(data.period);
      break;
    case "send-payroll-period-history":
      sendPayrollPeriodTableToHistory(data.period);
      break;
    case "delete-payroll-period-table":
      deletePayrollPeriodTable(data.period);
      break;
    case "pay-payroll-period-table":
      payPayrollPeriodTable(data.period);
      break;
    case "view-payroll-history-payments":
      viewPayrollHistoryPayments(data.period);
      break;
    case "generate-dispersion":
      state.data.paymentBatch.status = "Generado";
      state.data.paymentBatch.details.forEach((row) => { if (row.status === "Pendiente") row.status = "Generado"; });
      addAudit("Generó archivo de dispersión", "Pagos", state.data.paymentBatch.folio, "Pendiente", "Generado");
      toast("Archivo de dispersión generado.", "success");
      saveState();
      render();
      break;
    case "send-bank":
      state.data.paymentBatch.status = "Enviado";
      state.data.paymentBatch.details.forEach((row) => { if (row.status === "Generado") row.status = "Enviado"; });
      addAudit("Envió dispersión al banco", "Pagos", state.data.paymentBatch.folio, "Generado", "Enviado");
      toast("Envío bancario simulado.", "success");
      saveState();
      render();
      break;
    case "mark-payments-paid":
      openConfirm("Marcar pagos como realizados", "Todos los pagos del periodo quedaran como pagados y se registrara comprobante.", "confirm-payments-paid", {}, "success");
      break;
    case "mark-payment-paid":
      markPaymentPaid(Number(data.id));
      break;
    case "reprocess-payment":
      reprocessPayment(Number(data.id));
      break;
    case "reprocess-all-payments":
      state.data.paymentBatch.details.filter((row) => row.status === "Rechazado").forEach((row) => {
        row.status = "Reprocesado";
        row.rejectReason = "";
        row.proof = "Reproceso generado";
      });
      toast("Pagos rechazados reprocesados.", "success");
      addAudit("Reprocesó pagos rechazados", "Pagos", state.data.paymentBatch.folio, "Rechazado", "Reprocesado");
      saveState();
      render();
      break;
    case "generate-receipts":
      state.data.receipts.forEach((receipt) => { receipt.status = "Emitido"; });
      addAudit("Generó recibos", "Recibos", "NOM-2026-14", "Pendiente", "Emitido");
      toast("Recibos generados.", "success");
      saveState();
      render();
      break;
    case "download-receipt":
      downloadReceipt(Number(data.id));
      break;
    case "download-all-receipts":
      downloadText("paquete-recibos-nom-2026-14.txt", state.data.receipts.map((receipt) => `${receipt.folio} | ${receipt.employee} | ${money(receipt.net)}`).join("\n"));
      toast("Paquete de recibos generado.", "success");
      break;
    case "send-receipt":
      toast("Recibo enviado por correo simulado.", "success");
      break;
    case "publish-receipt":
      publishReceipt(Number(data.id));
      break;
    case "print-receipt":
      window.print();
      break;
    case "confirm-sign-contract":
      confirmSignContract(data.payload || target.dataset.payload);
      break;
    case "confirm-payments-paid":
      confirmPaymentsPaid();
      break;
    case "confirm-termination":
      confirmTermination(data.payload || target.dataset.payload);
      break;
    case "close-modal":
      state.ui.modal = null;
      render();
      break;
    case "portal-sign-contract":
      if (state.data.contracts.find((c) => c.employeeId === state.ui.selectedEmployeeId)) {
        const contract = state.data.contracts.find((c) => c.employeeId === state.ui.selectedEmployeeId);
        state.ui.selectedContractId = contract.id;
        if (blockContractOutputUntilLegalApproval(contract)) {
          navigate("contract-approval");
          break;
        }
        navigate("contract-signature");
      } else {
        toast("No hay contrato pendiente para este empleado.", "error");
      }
      break;
    case "portal-download-receipt":
      downloadReceipt(state.data.receipts.find((r) => r.employeeId === state.ui.selectedEmployeeId)?.id || 1);
      break;
    case "portal-vacation":
      createVacationRequest();
      break;
    case "manager-vacation-request": {
      const employee = employeeById(Number(data.id));
      if (!employee) break;
      const manager = managersList().find((item) => (item.branches || []).includes(employee.branch)) || activeBranchManager();
      state.ui.selectedEmployeeId = employee.id;
      createVacationRequest(employee.id, manager?.name || "Gerente de sucursal");
      break;
    }
    case "portal-update-bank":
      toast("Solicitud de actualización bancaria registrada.", "success");
      addAudit("Solicitó cambio de cuenta bancaria", "Portal", employeeById(state.ui.selectedEmployeeId).number, "Actual", "En revisión");
      saveState();
      break;
    case "portal-upload-document":
    case "upload-document":
      employeeById(state.ui.selectedEmployeeId).documents.push({ name: "Documento subido", status: "En revisión", date: today() });
      toast("Documento agregado al expediente.", "success");
      saveState();
      render();
      break;
    case "toggle-vacation-status":
      state.ui.vacationStatusOpen = Number(state.ui.vacationStatusOpen) === Number(data.id) ? null : Number(data.id);
      render();
      break;
    case "accept-vacation":
      resolveVacation(Number(data.id), "Aprobada");
      break;
    case "reject-vacation":
      resolveVacation(Number(data.id), "Rechazada");
      break;
    case "show-vacation-detail":
      state.ui.modal = { type: "vacation-detail", employeeId: Number(data.id) };
      render();
      break;
    case "select-template":
      state.ui.selectedTemplateId = Number(data.id);
      state.ui.templateEditing = false;
      saveState();
      render();
      break;
    case "edit-template":
      state.ui.templateEditing = true;
      saveState();
      render();
      break;
    case "cancel-template-edit":
      state.ui.templateEditing = false;
      saveState();
      render();
      break;
    case "scroll-template-carousel": {
      const carousel = document.querySelector("[data-template-carousel]");
      carousel?.scrollBy({ left: Number(data.dir || 1) * 360, behavior: "smooth" });
      break;
    }
    case "scroll-contracts-due-carousel": {
      const carousel = document.querySelector("[data-contracts-due-carousel]");
      carousel?.scrollBy({ left: Number(data.dir || 1) * 310, behavior: "smooth" });
      break;
    }
    case "select-due-contract":
      state.ui.selectedDueContractId = Number(data.id);
      state.ui.selectedContractId = Number(data.id);
      saveState();
      render();
      break;
    case "close-due-contract":
      state.ui.selectedDueContractId = null;
      saveState();
      render();
      break;
    case "save-template":
      activeTemplate().updatedAt = today();
      toast("Nueva versión de plantilla guardada.", "success");
      addAudit("Guardó plantilla", "Contratos", activeTemplate().name, "Versión anterior", activeTemplate().version);
      saveState();
      render();
      break;
    case "duplicate-template":
      duplicateSelectedTemplate();
      toast("Plantilla duplicada.", "success");
      saveState();
      render();
      break;
    case "delete-template":
      deleteSelectedTemplate();
      break;
    case "insert-variable":
      insertVariable(data.variable);
      break;
    case "toggle-template-clause":
      activeTemplate().clauses[Number(data.index)].active = target.checked;
      saveState();
      render();
      break;
    case "edit-catalog":
      toast(`Catálogo ${data.name} listo para edición.`, "success");
      break;
    case "export-employees":
      exportEmployees();
      break;
    case "export-payroll":
      exportPayroll();
      break;
    case "export-report":
      downloadText(`reporte-${(data.format || "csv").toLowerCase()}-hr-suite.txt`, `Reporte ${data.format || "CSV"} generado para ${state.ui.company}`);
      toast(`Reporte ${data.format || "CSV"} generado.`, "success");
      break;
    case "export-audit":
      downloadText("auditoria-hr-suite.csv", rowsToCsv(state.data.audit));
      toast("Auditoría exportada.", "success");
      break;
    case "generate-settlement-docs":
    case "download-settlement":
      downloadText("finiquito-resumen.txt", `Resumen de finiquito para ${employeeById(state.ui.selectedEmployeeId).fullName}`);
      toast("Documentos de finiquito generados.", "success");
      break;
    case "open-adjustment":
      toast("Ajuste agregado al cálculo simulado.", "success");
      break;
    case "attach-proof":
      toast("Comprobante adjuntado a la dispersión.", "success");
      break;
    case "save-notes":
      toast("Notas guardadas.", "success");
      break;
    case "portal-request":
      toast("Solicitud registrada en portal.", "success");
      break;
    default:
      toast("Acción registrada.", "success");
  }
}

function sortTable(table, key) {
  tableState[table] = tableState[table] || { search: "", page: 1, sortKey: "", sortDir: "asc", filters: {} };
  if (tableState[table].sortKey === key) {
    tableState[table].sortDir = tableState[table].sortDir === "asc" ? "desc" : "asc";
  } else {
    tableState[table].sortKey = key;
    tableState[table].sortDir = "asc";
  }
  render();
}

function toggleDraftClause(clause, checked) {
  const clauses = new Set(state.ui.contractDraft.clauses);
  if (checked) clauses.add(clause);
  else clauses.delete(clause);
  state.ui.contractDraft.clauses = [...clauses];
  saveState();
}

function createEmployee(form) {
  const employeeNumber = String(form.get("number") || "").trim().toUpperCase();
  const curp = String(form.get("curp") || "").trim().toUpperCase();
  const rfc = String(form.get("rfc") || "").trim().toUpperCase();
  const clabe = String(form.get("clabe") || "").trim();
  if (!/^EMP-\d{5}$/.test(employeeNumber)) return toast("Número de empleado debe usar el formato EMP-00001.", "error");
  if (state.data.employees.some((employee) => String(employee.number).toUpperCase() === employeeNumber)) return toast("El número de empleado ya está registrado.", "error");
  if (curp.length !== 18) return toast("CURP debe tener 18 caracteres.", "error");
  if (rfc.length < 12 || rfc.length > 13) return toast("RFC debe tener 12 o 13 caracteres.", "error");
  if (clabe.length !== 18) return toast("CLABE debe tener 18 dígitos.", "error");

  const id = state.data.employees.length + 1;
  const firstName = form.get("firstName");
  const lastName = form.get("lastName");
  const secondLastName = form.get("secondLastName");
  const selectedDocs = form.getAll("documents");
  const newHire = form.get("newHire") === "1";
  const employee = {
    id,
    number: employeeNumber,
    firstName,
    lastName,
    secondLastName,
    fullName: `${firstName} ${lastName} ${secondLastName}`,
    initials: `${String(firstName)[0] || "N"}${String(lastName)[0] || "E"}`.toUpperCase(),
    avatarColor: ["#3157d5", "#0f9f9a", "#7657d8", "#e06b1d"][id % 4],
    birthDate: form.get("birthDate"),
    curp,
    rfc,
    nss: form.get("nss"),
    civilStatus: form.get("civilStatus"),
    nationality: form.get("nationality"),
    phone: form.get("phone"),
    email: form.get("email"),
    address: form.get("address"),
    emergencyContact: form.get("emergencyContact"),
    company: form.get("company"),
    branch: form.get("branch"),
    department: form.get("department"),
    position: form.get("position"),
    manager: form.get("manager"),
    hireDate: form.get("hireDate"),
    seniority: form.get("seniority"),
    workerType: form.get("workerType"),
    modality: form.get("modality"),
    workday: form.get("workday"),
    schedule: form.get("schedule"),
    workDays: form.get("workDays"),
    workplace: form.get("workplace"),
    riskClass: form.get("riskClass"),
    status: form.get("status"),
    grossSalary: Number(form.get("grossSalary")),
    dailySalary: Number(form.get("dailySalary")),
    integratedDailySalary: Number(form.get("integratedDailySalary")),
    payFrequency: form.get("payFrequency"),
    payrollType: form.get("payrollType"),
    bank: form.get("bank"),
    clabe,
    account: form.get("account"),
    paymentMethod: form.get("paymentMethod"),
    salaryZone: form.get("salaryZone"),
    commissions: form.get("commissions"),
    recurringBonus: Number(form.get("recurringBonus")),
    taxRegime: form.get("taxRegime"),
    fiscalZip: form.get("fiscalZip"),
    cfdiUse: form.get("cfdiUse"),
    taxContractType: form.get("taxContractType"),
    taxWorkdayType: form.get("taxWorkdayType"),
    taxRegimeType: form.get("taxRegimeType"),
    contractType: form.get("taxContractType"),
    newHire,
    nextPay: "2026-08-02",
    vacationDays: 12,
    loanBalance: 0,
    documents: selectedDocs.map((name) => ({ name, status: "Completo", date: today() })),
    timeline: [{ date: today(), title: "Alta de empleado", detail: "Registro creado desde RH" }]
  };
  state.data.employees.push(employee);
  state.ui.selectedEmployeeId = id;
  addAudit("Creó empleado", "Empleados", employee.number, "Sin registro", employee.fullName);
  toast("Empleado creado y expediente inicial generado.", "success");
  saveState();
  navigate("employee-profile");
}

function nextContractFolio() {
  const savedFolios = [
    ...(state.data.contracts || []).map((contract) => contract.folio),
    ...(state.data.contractDrafts || []).map((process) => process.folio || process.draft?.folio)
  ].filter(Boolean);
  const highest = savedFolios.reduce((max, folio) => {
    const match = String(folio).match(/(\d+)\s*$/);
    return match ? Math.max(max, Number(match[1])) : max;
  }, 0);
  return `CNT-2026-${String(highest + 1).padStart(3, "0")}`;
}

function ensureContractDraftFolio(draft) {
  if (!/^CNT-\d{4}-\d{3,}$/.test(String(draft.folio || ""))) {
    draft.folio = nextContractFolio();
  }
  return draft.folio;
}

function createBlankContractDraft() {
  const base = defaultUi().contractDraft;
  const employee = state.data.employees.find((item) => item.status === "Activo") || state.data.employees[0];
  const draft = {
    ...base,
    clauses: [...(base.clauses || [])],
    folio: nextContractFolio(),
    signDate: today(),
    startDate: today(),
    endDate: ""
  };
  if (employee) {
    draft.employeeId = employee.id;
    draft.company = employee.company || draft.company;
    draft.position = employee.position || draft.position;
    draft.department = employee.department || draft.department;
    draft.salary = employee.grossSalary || draft.salary;
    draft.type = employee.contractType || draft.type;
    draft.newHire = Boolean(employee.newHire);
  }
  delete draft.processId;
  return draft;
}

function startContractProcess() {
  state.ui.contractDraft = createBlankContractDraft();
  state.ui.contractStep = 1;
  state.ui.contractPersonType = "candidate";
  state.ui.contractEmployeeNewHireOnly = false;
  state.ui.modal = null;
  navigate("contract-create");
}

function saveContractProcess() {
  const draft = state.ui.contractDraft || createBlankContractDraft();
  const employee = employeeById(draft.employeeId);
  state.data.contractDrafts = state.data.contractDrafts || [];
  const id = draft.processId || `ELAB-${Date.now()}`;
  const process = {
    id,
    folio: draft.folio || nextContractFolio(),
    employeeId: employee.id,
    employee: employee.fullName,
    company: draft.company || employee.company,
    branch: employee.branch,
    position: draft.position || employee.position,
    department: draft.department || employee.department,
    type: draft.type || employee.contractType || "Tiempo indeterminado",
    step: Math.min(Math.max(Number(state.ui.contractStep) || 1, 1), contractSteps.length),
    updatedAt: today(),
    status: "En elaboración",
    draft: {
      ...draft,
      employeeId: employee.id,
      processId: id,
      clauses: [...(draft.clauses || [])]
    }
  };
  const index = state.data.contractDrafts.findIndex((item) => String(item.id) === String(id));
  if (index >= 0) state.data.contractDrafts[index] = process;
  else state.data.contractDrafts.unshift(process);
  state.ui.contractDraft = { ...process.draft, clauses: [...process.draft.clauses] };
  addAudit("Guardó proceso", "Contratos", process.folio, "Borrador", "En elaboración");
  toast("Proceso guardado en En Elaboración.", "success");
  saveState();
  navigate("contracts-drafts");
}

function continueContractProcess(id) {
  const process = (state.data.contractDrafts || []).find((item) => String(item.id) === String(id));
  if (!process) {
    toast("No se encontró el proceso en elaboración.", "error");
    return;
  }
  const draft = process.draft || process;
  state.ui.contractDraft = {
    ...draft,
    processId: process.id,
    employeeId: draft.employeeId || process.employeeId,
    clauses: [...(draft.clauses || [])]
  };
  state.ui.contractStep = Math.min(Math.max(Number(process.step || draft.step || 1), 1), contractSteps.length);
  state.ui.contractPersonType = state.ui.contractDraft.candidateId ? "candidate" : "employee";
  state.ui.modal = null;
  saveState();
  navigate("contract-create");
}
function saveContractFromDraft() {
  const draft = state.ui.contractDraft;
  const draftProcessId = draft.processId;
  const employee = employeeById(draft.employeeId);
  const id = state.data.contracts.length + 1;
  const contract = {
    id,
    folio: draft.folio || nextContractFolio(),
    employeeId: employee.id,
    sourceCandidateId: draft.candidateId || employee.sourceCandidateId || null,
    employee: employee.fullName,
    company: draft.company,
    position: draft.position,
    department: draft.department,
    branch: employee.branch,
    type: draft.type,
    startDate: draft.startDate,
    endDate: draft.endDate,
    salary: Number(draft.salary),
    bonuses: draft.bonusesNotApplicable ? 0 : Number(draft.bonuses || 0),
    bonusesNotApplicable: Boolean(draft.bonusesNotApplicable),
    bonusCondition: draft.bonusCondition || "",
    commissions: draft.commissions || "",
    commissionCondition: draft.commissionCondition || "",
    isNewHire: Boolean(draft.newHire || employee.newHire),
    status: "En aprobación",
    employeeSignature: "Pendiente",
    companySignature: "Pendiente",
    legalRep: draft.legalRep,
    signingPlace: draft.signingPlace,
    trialPeriod: draft.trialPeriod,
    contractModelName: draft.contractModelName || state.data.templates[0]?.name || "Contrato por tiempo indeterminado",
    contractModelVersion: draft.contractModelVersion || state.data.templates[0]?.version || "v1.0",
    contractModelFile: draft.contractModelFile || `${draft.contractModelName || state.data.templates[0]?.name || "Contrato"}.docx`,
    contractModelAttachedAt: today(),
    clauses: draft.clauses,
    approvals: [
      { step: "Elaborado por Recursos Humanos", status: "Aprobado", user: state.ui.role, date: today(), comment: "Creado" },
      { step: "Aprobación Dirección Jurídica", status: "Pendiente", user: "", date: "", comment: "" }
    ],
    validationCode: `MX-NEW-${id}`,
    ipEvidence: "187.190.22.88"
  };
  state.data.contracts.unshift(contract);
  if (contract.isNewHire) employee.newHire = true;
  if (draftProcessId) {
    state.data.contractDrafts = (state.data.contractDrafts || []).filter((item) => String(item.id) !== String(draftProcessId));
  }
  employee.documents.push({ name: "Contrato", status: "Completo", date: today() });
  employee.timeline.push({ date: today(), title: "Contrato guardado", detail: contract.folio });
  state.ui.selectedContractId = id;
  state.ui.contractStep = 1;
  state.ui.contractDraft = createBlankContractDraft();
  addAudit("Guardó contrato", "Contratos", contract.folio, "Borrador", "Guardado");
  toast("Contrato guardado. Requiere aprobación de Dirección Jurídica.", "success");
  saveState();
  navigate("contract-approval");
}

function advanceContractApproval() {
  const contract = contractById(state.ui.selectedContractId);
  const next = contract.approvals.find((step) => step.status !== "Aprobado");
  if (!next) {
    if (isLegalDirectionApproved(contract)) {
      toast("El contrato ya fue aprobado por Dirección Jurídica. Ya puede pasar a firma.", "success");
      navigate("contract-signature");
      return;
    }
    toast("Todas las aprobaciones están completas.", "success");
    return;
  }
  next.status = "Aprobado";
  next.user = state.ui.role;
  next.date = today();
  next.comment = "Aprobado desde flujo";
  const isComplete = contract.approvals.every((step) => step.status === "Aprobado");
  if (isComplete) {
    contract.status = "Pendiente de firma";
    addAudit("Aprobó contrato", "Contratos", contract.folio, "Pendiente", next.step);
    toast("Dirección Jurídica aprobó el contrato. Ya puede pasar a firma.", "success");
    saveState();
    navigate("contract-signature");
    return;
  }
  addAudit("Aprobó contrato", "Contratos", contract.folio, "Pendiente", next.step);
  toast("Aprobación registrada.", "success");
  saveState();
  render();
}

function confirmSignContract(rawPayload) {
  const payload = typeof rawPayload === "string" ? JSON.parse(rawPayload || "{}") : rawPayload;
  const contract = contractById(payload.id);
  contract.employeeSignature = "Firmado";
  contract.companySignature = "Firmado";
  contract.status = "Activo";
  contract.isNewHire = false;
  let employee = employeeById(contract.employeeId);
  if (String(employee?.id || "").startsWith("candidate-")) {
    const proxyId = employee.id;
    const employeeId = state.data.employees.reduce((max, item) => Math.max(max, Number(item.id) || 0), 0) + 1;
    employee = {
      ...employee,
      id: employeeId,
      number: nextEmployeeNumber(),
      status: "Activo",
      newHire: false,
      hireDate: contract.startDate || today(),
      contractType: contract.type || employee.contractType,
      timeline: [...(employee.timeline || [])],
      documents: [...(employee.documents || [])]
    };
    state.data.contractCandidateEmployees = (state.data.contractCandidateEmployees || [])
      .filter((item) => String(item.id) !== String(proxyId));
    state.data.employees.unshift(employee);
    contract.employeeId = employee.id;
    moveCandidateToEmployees(contract.sourceCandidateId || employee.sourceCandidateId, employee, contract);
  }
  employee.newHire = false;
  employee.timeline.push({ date: today(), title: "Contrato firmado", detail: contract.folio });
  employee.documents.push({ name: `Contrato ${contract.folio}`, status: "Completo", date: today() });
  state.ui.modal = null;
  addAudit("Firmó contrato", "Contratos", contract.folio, "Pendiente", "Activo");
  toast("Contrato firmado y activado.", "success");
  saveState();
  navigate("contracts-list");
}

function moveCandidateToEmployees(candidateId, employee, contract) {
  const candidate = candidateById(candidateId);
  if (!candidate) return;

  state.data.hiredCandidates = (state.data.hiredCandidates || [])
    .filter((item) => Number(item.id) !== Number(candidate.id));
  state.data.hiredCandidates.unshift({
    ...candidate,
    status: "Contratado",
    selected: true,
    lastUpdate: today(),
    convertedAt: today(),
    employeeId: employee.id,
    contractId: contract.id,
    contractFolio: contract.folio
  });
  state.data.candidates = (state.data.candidates || [])
    .filter((item) => Number(item.id) !== Number(candidate.id));
}

function renewContract() {
  const base = contractById(state.ui.selectedContractId);
  const renewalForm = document.querySelector("#renewal-form");
  const form = renewalForm ? new FormData(renewalForm) : null;
  const id = state.data.contracts.length + 1;
  const model = contractModel(base);
  const renewed = {
    ...base,
    id,
    folio: `CNT-2026-R${String(id).padStart(3, "0")}`,
    type: form?.get("type") || base.type,
    startDate: form?.get("startDate") || "2026-08-01",
    endDate: form?.get("endDate") || "2027-07-31",
    salary: Number(form?.get("salary") || base.salary + 2500),
    contractModelName: model.name,
    contractModelVersion: model.version,
    contractModelFile: model.file,
    contractModelAttachedAt: today(),
    status: "En aprobación",
    isNewHire: false,
    employeeSignature: "Pendiente",
    companySignature: "Pendiente"
  };
  base.status = "Cerrado";
  state.data.contracts.unshift(renewed);
  state.ui.selectedContractId = id;
  addAudit("Renovó contrato", "Contratos", renewed.folio, base.folio, renewed.folio);
  toast("Renovación creada y contrato anterior cerrado.", "success");
  saveState();
  navigate("contract-approval");
}

function createPayrollPeriod(form) {
  const id = state.data.payrollPeriods.length + 1;
  const year = Number(form.get("year")) || new Date().getFullYear();
  const usedCodes = new Set(state.data.payrollPeriods.map((period) => period.code));
  const numbersForYear = state.data.payrollPeriods
    .filter((period) => Number(period.year) === year)
    .map((period) => Number(period.number) || 0);
  let periodNumber = Number(form.get("number"));
  if (!Number.isFinite(periodNumber) || periodNumber < 1) {
    periodNumber = Math.max(0, ...numbersForYear) + 1;
  }
  let periodCode = `NOM-${year}-${String(periodNumber).padStart(2, "0")}`;
  while (usedCodes.has(periodCode)) {
    periodNumber += 1;
    periodCode = `NOM-${year}-${String(periodNumber).padStart(2, "0")}`;
  }
  const period = {
    id,
    code: periodCode,
    company: form.get("company"),
    branch: form.get("branch"),
    type: form.get("type"),
    frequency: form.get("frequency"),
    startDate: form.get("startDate"),
    endDate: form.get("endDate"),
    cutDate: form.get("cutDate"),
    payDate: form.get("payDate"),
    year,
    number: periodNumber,
    employeesIncluded: 0,
    department: form.get("department"),
    costCenter: form.get("costCenter"),
    status: "Abierta",
    observations: form.get("observations"),
    locked: false,
    summaryMovedToHistory: false,
    summaryDeleted: false
  };
  state.data.payrollPeriods.unshift(period);
  const snapshot = payrollHistorySnapshot(period, { status: "Abierta", createdAt: today() });
  period.historySnapshotId = snapshot.id;
  period.employeesIncluded = snapshot.rows.length;
  upsertPendingPayrollTable(snapshot);
  state.ui.payrollStep = 2;
  addAudit("Creó periodo", "Nómina", period.code, "Sin periodo", "Abierta");
  toast("Periodo de nómina creado.", "success");
  saveState();
  navigate("payroll");
}

function createIncidence(form) {
  const [employeeId, employeeName] = String(form.get("employeeId")).split("|");
  const id = state.data.incidences.length + 1;
  state.data.incidences.unshift({
    id,
    employeeId: Number(employeeId),
    employee: employeeName,
    type: form.get("type"),
    date: form.get("date"),
    quantity: Number(form.get("quantity")),
    unit: form.get("unit"),
    amount: Number(form.get("amount")),
    evidence: form.get("evidence"),
    comments: form.get("comments"),
    createdBy: state.ui.role,
    status: "Pendiente",
    approver: form.get("approver")
  });
  addAudit("Registró incidencia", "Incidencias", employeeName, "Sin registro", form.get("type"));
  toast("Incidencia registrada para aprobación.", "success");
  saveState();
  render();
}

function updateIncidence(id, status) {
  const item = state.data.incidences.find((incidence) => incidence.id === id);
  if (!item) return;
  const old = item.status;
  item.status = status;
  addAudit(`${status === "Aprobada" ? "Aprobó" : "Rechazó"} incidencia`, "Incidencias", item.employee, old, status);
  toast(`Incidencia ${status.toLowerCase()}.`, status === "Aprobada" ? "success" : "error");
  saveState();
  render();
}

function updateDeduction(form) {
  const deduction = state.data.deductions.find((item) => item.id === Number(form.get("id")));
  if (!deduction) return;
  const old = money(deduction.fixedAmount);
  deduction.key = form.get("key");
  deduction.name = form.get("name");
  deduction.percent = Number(form.get("percent"));
  deduction.fixedAmount = Number(form.get("fixedAmount"));
  deduction.cap = Number(form.get("cap"));
  deduction.balance = Number(form.get("balance"));
  deduction.formula = form.get("formula");
  deduction.status = form.get("status");
  state.ui.modal = null;
  addAudit("Modificó deducción", "Deducciones", deduction.key, old, money(deduction.fixedAmount));
  toast("Deducción actualizada y lista para recalcular.", "success");
  saveState();
  render();
}

function resolveCriticalValidations() {
  state.data.payrollValidations.forEach((item) => {
    if (item.type === "Crítica") item.status = "Atendida";
  });
  state.data.alerts.forEach((item) => {
    if (item.level === "Crítica") item.status = "Atendida";
  });
  addAudit("Atendió validaciones críticas", "Nómina", "NOM-2026-14", "Bloqueada", "Disponible");
  toast("Errores críticos atendidos. La nómina puede avanzar.", "success");
  saveState();
  render();
}

function approvePayroll() {
  const next = state.data.approvalFlow.find((step) => step.status !== "Aprobado");
  if (!next) {
    toast("La nómina ya está completamente autorizada.", "success");
    return;
  }
  next.status = "Aprobado";
  next.user = state.ui.role;
  next.date = today();
  next.comment = "Aprobado";
  state.ui.payrollStep = Math.min(15, 10 + next.id);
  state.data.payrollPeriods[0].status = next.id >= 5 ? "Autorizada" : "En autorización";
  addAudit("Aprobó nómina", "Nómina", state.data.payrollPeriods[0].code, "Pendiente", next.name);
  toast("Aprobación de nómina registrada.", "success");
  saveState();
  render();
}

function approvePayrollFortnightSummaryLegacy() {
  const currentPeriod = state.data.payrollPeriods[0] || {};
  const { period, rows } = payrollFortnightRows();
  const details = rows.map((row, index) => {
    const employee = employeeById(row.employeeId);
    return {
      id: index + 1,
      employeeId: employee.id,
      employee: employee.fullName,
      bank: employee.bank,
      account: employee.account,
      clabe: employee.clabe,
      amount: row.netPayable,
      reference: `${currentPeriod.code || "NOM-2026-14"}-${employee.number}`,
      status: "Pendiente",
      rejectReason: "",
      payDate: currentPeriod.payDate || currentFortnightPeriod().end,
      proof: "Pendiente"
    };
  });
  const totalAmount = details.reduce((sum, row) => sum + Number(row.amount || 0), 0);

  state.data.paymentBatch = {
    id: state.data.paymentBatch?.id || 1,
    period: currentPeriod.code || "NOM-2026-14",
    company: "Todas las empresas",
    payDate: currentPeriod.payDate || currentFortnightPeriod().end,
    bank: "Bancos por empleado",
    method: "Transferencia SPEI",
    employeeCount: details.length,
    totalAmount,
    status: "Pendiente de dispersión",
    folio: `DSP-${today().replaceAll("-", "")}-NOM`,
    source: "payroll-summary-approved",
    details
  };
  currentPeriod.summaryApproved = true;
  currentPeriod.summaryMovedToHistory = false;
  currentPeriod.status = "Aprobada";
  currentPeriod.approvedAt = today();
  upsertPendingPayrollTable(payrollHistorySnapshot(currentPeriod, {
    status: "Aprobada",
    approvedAt: currentPeriod.approvedAt
  }));
  state.ui.payrollStep = Math.max(Number(state.ui.payrollStep) || 1, 12);
  state.data.approvalFlow.forEach((step) => {
    if (Number(step.id) <= 5 && step.status !== "Aprobado") {
      step.status = "Aprobado";
      step.user = state.ui.role;
      step.date = today();
      step.comment = "Aprobado desde resumen quincenal";
    }
  });
  addAudit("Aprobó resumen quincenal", "Nómina", currentPeriod.code || "NOM-2026-14", "Calculada", "Pagos pendientes");
  toast("Nómina aprobada. Pagos generados con netos y cuentas bancarias.", "success");
  saveState();
  render();
}

function payApprovedPayrollFortnightLegacy() {
  const currentPeriod = state.data.payrollPeriods[0] || {};
  if (!currentPeriod.summaryApproved) {
    toast("Primero aprueba el listado de nÃ³mina para poder pagar.", "error");
    return;
  }
  navigate("payments");
}

function sendPayrollFortnightToHistoryLegacy() {
  const pending = pendingPayrollTables();
  const paidPending = pending.filter(payrollSnapshotPaid);
  if (!paidPending.length) {
    toast("Primero paga al menos una tabla de nomina para mandarla al historial.", "error");
    return;
  }
  paidPending.forEach((snapshot) => upsertPayrollHistory({ ...snapshot, status: "Pagada", paidAt: snapshot.paidAt || today(), period: { ...(snapshot.period || {}), status: "Pagada" } }));
  const movedCodes = new Set(paidPending.map((snapshot) => snapshot.periodCode));
  state.data.payrollPendingTables = (state.data.payrollPendingTables || []).filter((snapshot) => !movedCodes.has(snapshot.periodCode));
  state.data.payrollPeriods.forEach((period) => {
    if (movedCodes.has(period.code)) period.summaryMovedToHistory = true;
  });
  addAudit("Mando resumen al historial", "Nomina", [...movedCodes].join(", "), "Pagada", "Historial");
  toast(`${paidPending.length} tabla(s) enviadas al historial de nominas.`, "success");
  saveState();
  navigate("payroll-history");
}

function buildPaymentBatchForSnapshot(snapshot) {
  const details = (snapshot.rows || []).map((row, index) => {
    const employee = employeeById(row.employeeId) || {};
    return {
      id: index + 1,
      employeeId: employee.id || row.employeeId,
      employee: employee.fullName || row.name,
      bank: employee.bank || "Banco pendiente",
      account: employee.account || "Pendiente",
      clabe: employee.clabe || "Pendiente",
      amount: row.netPayable,
      reference: `${snapshot.periodCode || "NOM"}-${employee.number || row.employeeConsecutiveId || index + 1}`,
      status: "Pendiente",
      rejectReason: "",
      payDate: snapshot.period?.payDate || snapshot.period?.end || currentFortnightPeriod().end,
      proof: "Pendiente"
    };
  });
  const totalAmount = details.reduce((sum, row) => sum + Number(row.amount || 0), 0);
  state.data.paymentBatch = {
    id: state.data.paymentBatch?.id || 1,
    period: snapshot.periodCode || "NOM-2026-14",
    company: ["TODAS", "Todas", "Todas las empresas"].includes(snapshot.period?.company) ? "Todas las empresas" : (snapshot.period?.company || "Todas las empresas"),
    payDate: snapshot.period?.payDate || snapshot.period?.end || currentFortnightPeriod().end,
    bank: "Bancos por empleado",
    method: "Transferencia SPEI",
    employeeCount: details.length,
    totalAmount,
    status: "Pendiente de dispersion",
    folio: `DSP-${today().replaceAll("-", "")}-${snapshot.periodCode || "NOM"}`,
    source: "payroll-summary-approved",
    details
  };
}

function viewPayrollHistoryPayments(periodCode) {
  const snapshot = findPayrollHistorySnapshot(periodCode);
  if (!snapshot) {
    toast("No se encontro la nomina historica para consultar pagos.", "error");
    return;
  }
  buildPaymentBatchForSnapshot(snapshot);
  state.data.paymentBatch.source = "payroll-history";
  if (payrollSnapshotPaid(snapshot)) {
    state.data.paymentBatch.status = "Pagado";
    state.data.paymentBatch.details.forEach((row) => {
      row.status = "Pagado";
      row.rejectReason = "";
      row.proof = "Comprobante adjunto";
    });
  }
  saveState();
  navigate("payments");
}

function approvePayrollPeriodTable(periodCode) {
  const snapshot = findPendingPayrollSnapshot(periodCode);
  if (!snapshot) {
    toast("No se encontro la tabla de nomina para aprobar.", "error");
    return;
  }
  if (payrollSnapshotApproved(snapshot)) {
    toast("Esta tabla ya esta aprobada.", "success");
    return;
  }
  const periodRecord = findPayrollPeriodByCode(snapshot.periodCode);
  const approvedAt = today();
  let approvedSnapshot = {
    ...snapshot,
    status: "Aprobada",
    approvedAt,
    period: { ...(snapshot.period || {}), status: "Aprobada" }
  };

  if (periodRecord) {
    periodRecord.summaryApproved = true;
    periodRecord.summaryMovedToHistory = false;
    periodRecord.status = "Aprobada";
    periodRecord.approvedAt = approvedAt;
    periodRecord.employeesIncluded = (snapshot.rows || []).length;
    approvedSnapshot = payrollHistorySnapshot(periodRecord, { status: "Aprobada", approvedAt });
  }

  upsertPendingPayrollTable(approvedSnapshot);
  buildPaymentBatchForSnapshot(approvedSnapshot);
  state.ui.payrollStep = Math.max(Number(state.ui.payrollStep) || 1, 12);
  state.data.approvalFlow.forEach((step) => {
    if (Number(step.id) <= 5 && step.status !== "Aprobado") {
      step.status = "Aprobado";
      step.user = state.ui.role;
      step.date = approvedAt;
      step.comment = "Aprobado desde resumen quincenal";
    }
  });
  addAudit("Aprobo tabla quincenal", "Nomina", approvedSnapshot.periodCode || periodCode, "Calculada", "Pagos pendientes");
  toast("Tabla de nomina aprobada. Pagos generados con netos y cuentas bancarias.", "success");
  saveState();
  render();
}

function approvePayrollFortnightSummary() {
  const currentPeriod = state.data.payrollPeriods[0] || {};
  const currentSnapshot = currentPeriod.code ? findPendingPayrollSnapshot(currentPeriod.code) : null;
  const firstPending = pendingPayrollTables().find((snapshot) => !payrollSnapshotApproved(snapshot));
  approvePayrollPeriodTable(currentSnapshot?.periodCode || firstPending?.periodCode || firstPending?.id || "");
}

function payPayrollPeriodTable(periodCode) {
  const snapshot = findPendingPayrollSnapshot(periodCode);
  if (!snapshot || !payrollSnapshotApproved(snapshot)) {
    toast("Primero aprueba esta tabla de nomina para poder pagar.", "error");
    return;
  }
  buildPaymentBatchForSnapshot(snapshot);
  saveState();
  navigate("payments");
}

function payApprovedPayrollFortnight() {
  const currentPeriod = state.data.payrollPeriods[0] || {};
  payPayrollPeriodTable(currentPeriod.code);
}

function sendPayrollPeriodTableToHistory(periodCode) {
  const snapshot = findPendingPayrollSnapshot(periodCode);
  if (!snapshot || !payrollSnapshotApproved(snapshot)) {
    toast("Primero aprueba esta tabla de nomina para mandarla al historial.", "error");
    return;
  }
  if (!payrollSnapshotPaid(snapshot)) {
    toast("Primero paga esta tabla de nomina en la seccion de pagos para mandarla al historial.", "error");
    return;
  }
  upsertPayrollHistory({ ...snapshot, status: "Pagada", paidAt: snapshot.paidAt || today(), period: { ...(snapshot.period || {}), status: "Pagada" } });
  state.data.payrollPendingTables = (state.data.payrollPendingTables || []).filter((item) => payrollSnapshotKey(item) !== payrollSnapshotKey(snapshot));
  const periodRecord = findPayrollPeriodByCode(snapshot.periodCode);
  if (periodRecord) {
    periodRecord.summaryMovedToHistory = true;
    periodRecord.status = "En historial";
  }
  if (state.ui.payrollSummaryCollapsedByPeriod) delete state.ui.payrollSummaryCollapsedByPeriod[payrollSnapshotKey(snapshot)];
  addAudit("Mando tabla al historial", "Nomina", snapshot.periodCode || periodCode, "Pagada", "Historial");
  toast("Tabla enviada al historial de nominas.", "success");
  saveState();
  navigate("payroll-history");
}

function deletePayrollPeriodTable(periodCode) {
  const snapshot = findPendingPayrollSnapshot(periodCode);
  if (!snapshot) {
    toast("No se encontro la tabla de nomina para eliminar.", "error");
    return;
  }
  const snapshotKey = payrollSnapshotKey(snapshot);
  state.data.payrollPendingTables = (state.data.payrollPendingTables || []).filter((item) => payrollSnapshotKey(item) !== snapshotKey);
  const periodRecord = findPayrollPeriodByCode(snapshot.periodCode);
  if (periodRecord) {
    periodRecord.summaryDeleted = true;
    periodRecord.summaryMovedToHistory = false;
    periodRecord.status = "Eliminada";
  }
  if (state.ui.payrollSummaryCollapsedByPeriod) delete state.ui.payrollSummaryCollapsedByPeriod[snapshotKey];
  addAudit("Elimino tabla quincenal", "Nomina", snapshot.periodCode || periodCode, snapshot.status || "Pendiente", "Eliminada");
  toast("Tabla eliminada del panel de resumen.", "success");
  saveState();
  render();
}

function sendPayrollFortnightToHistory() {
  const paidPending = pendingPayrollTables().filter(payrollSnapshotPaid);
  if (!paidPending.length) {
    toast("Primero paga al menos una tabla de nomina para mandarla al historial.", "error");
    return;
  }
  paidPending.forEach((snapshot) => upsertPayrollHistory({ ...snapshot, status: "Pagada", paidAt: snapshot.paidAt || today(), period: { ...(snapshot.period || {}), status: "Pagada" } }));
  const movedCodes = new Set(paidPending.map((snapshot) => snapshot.periodCode));
  state.data.payrollPendingTables = (state.data.payrollPendingTables || []).filter((snapshot) => !movedCodes.has(snapshot.periodCode));
  state.data.payrollPeriods.forEach((period) => {
    if (movedCodes.has(period.code)) {
      period.summaryMovedToHistory = true;
      period.status = "En historial";
    }
  });
  addAudit("Mando resumen al historial", "Nomina", [...movedCodes].join(", "), "Pagada", "Historial");
  toast(`${paidPending.length} tabla(s) enviadas al historial de nominas.`, "success");
  saveState();
  navigate("payroll-history");
}

function markPaymentPaid(id) {
  const row = state.data.paymentBatch.details.find((payment) => payment.id === id);
  if (!row) return;
  row.status = "Pagado";
  row.proof = "Comprobante adjunto";
  if (paymentBatchPaidForPeriod(state.data.paymentBatch.period)) {
    state.data.paymentBatch.status = "Pagado";
    markPayrollSnapshotPaid(state.data.paymentBatch.period);
  } else {
    state.data.paymentBatch.status = "Pagado parcial";
  }
  addAudit("Marcó pago realizado", "Pagos", row.reference, "Pendiente", "Pagado");
  toast("Pago marcado como realizado.", "success");
  saveState();
  render();
}

function confirmPaymentsPaid() {
  state.data.paymentBatch.details.forEach((row) => {
    row.status = "Pagado";
    row.rejectReason = "";
    row.proof = "Comprobante adjunto";
  });
  state.data.paymentBatch.status = "Pagado";
  markPayrollSnapshotPaid(state.data.paymentBatch.period);
  state.ui.modal = null;
  addAudit("Marcó pagos realizados", "Pagos", state.data.paymentBatch.folio, "Enviado", state.data.paymentBatch.status);
  toast("Pagos marcados como realizados.", "success");
  saveState();
  render();
}

function reprocessPayment(id) {
  const row = state.data.paymentBatch.details.find((payment) => payment.id === id);
  if (!row) return;
  row.status = "Reprocesado";
  row.rejectReason = "";
  row.proof = "Reproceso generado";
  addAudit("Reprocesó pago", "Pagos", row.reference, "Rechazado", "Reprocesado");
  toast("Pago enviado a reproceso.", "success");
  saveState();
  render();
}

function publishReceipt(id) {
  const receipt = receiptById(id);
  receipt.status = "Publicado";
  addAudit("Publicó recibo", "Recibos", receipt.folio, "Emitido", "Publicado");
  toast("Recibo publicado en portal del empleado.", "success");
  saveState();
  render();
}

function downloadReceipt(id) {
  const receipt = receiptById(id);
  const employee = employeeById(receipt.employeeId);
  const content = [
    "HR Suite México - Recibo de nómina",
    `Folio: ${receipt.folio}`,
    `Empleado: ${employee.fullName}`,
    `Periodo: ${receipt.period}`,
    `Percepciones: ${money(receipt.perceptions)}`,
    `Deducciones: ${money(receipt.deductions)}`,
    `Neto: ${money(receipt.net)}`,
    `Cuenta: ${employee.bank} ${masked(employee.clabe)}`
  ].join("\n");
  downloadText(`${receipt.folio}.txt`, content);
  toast("Recibo descargado.", "success");
}

function confirmTermination(rawPayload) {
  const payload = typeof rawPayload === "string" ? JSON.parse(rawPayload || "{}") : rawPayload;
  const employee = employeeById(payload.employeeId);
  const old = employee.status;
  employee.status = "Baja";
  employee.timeline.push({ date: today(), title: "Terminación laboral", detail: "Expediente cerrado con finiquito" });
  state.data.contracts.filter((contract) => contract.employeeId === employee.id).forEach((contract) => { contract.status = "Cerrado"; });
  state.ui.modal = null;
  addAudit("Dio de baja empleado", "Empleados", employee.number, old, "Baja");
  toast("Empleado dado de baja y expediente cerrado.", "success");
  saveState();
  navigate("employee-profile");
}

function createVacationRequest(employeeId = state.ui.selectedEmployeeId, requester = "") {
  let employee = employeeById(employeeId);
  if (state.ui.role === "Gerente de sucursal") {
    const scopedEmployees = branchEmployees(managerBranches()).filter((item) => item.status === "Activo");
    const isInScope = scopedEmployees.some((item) => Number(item.id) === Number(employee?.id));
    employee = isInScope ? employee : scopedEmployees[0];
  }
  if (!employee) {
    toast("No hay empleados disponibles para solicitar vacaciones.", "warning");
    return;
  }
  const requesterName = requester || activeBranchManager()?.name || state.ui.role;
  state.data.vacations.unshift({
    id: state.data.vacations.length + 1,
    employeeId: employee.id,
    employee: employee.fullName,
    available: employee.vacationDays,
    requested: 3,
    startDate: "2026-08-12",
    endDate: "2026-08-14",
    status: "Pendiente",
    requestedBy: requesterName,
    approver: "Recursos Humanos"
  });
  addAudit("Solicitó vacaciones", "Vacaciones", employee.number, "Sin solicitud", "Pendiente");
  toast("Solicitud de vacaciones registrada.", "success");
  saveState();
  render();
}

function resolveVacation(id, status) {
  const item = state.data.vacations.find((vacation) => vacation.id === id);
  if (!item) return;
  const previousStatus = item.status;
  item.status = status;
  item.approver = "Recursos Humanos";
  item.resolvedAt = today();
  if (status === "Aprobada") item.approvedAt = item.resolvedAt;
  if (status === "Rechazada") item.rejectedAt = item.resolvedAt;
  state.ui.vacationStatusOpen = null;
  const employee = employeeById(item.employeeId);
  const accepted = status === "Aprobada";
  addAudit(accepted ? "Aceptó vacaciones" : "Rechazó vacaciones", "Vacaciones", employee?.number || item.employee, previousStatus, status);
  toast(accepted ? "Solicitud de vacaciones aceptada." : "Solicitud de vacaciones rechazada.", accepted ? "success" : "error");
  saveState();
  render();
}

function saveContractModel(form) {
  const contract = contractById(form.get("contractId"));
  const template = state.data.templates.find((item) => Number(item.id) === Number(form.get("templateId"))) || state.data.templates[0];
  const uploaded = form.get("modelFile");
  const fileName = uploaded?.name || String(form.get("modelFileName") || "").trim() || `${template.name}.docx`;
  const oldModel = contractModel(contract);
  contract.contractModelName = template.name;
  contract.contractModelVersion = String(form.get("version") || template.version || "v1.0").trim();
  contract.contractModelFile = fileName;
  contract.contractModelAttachedAt = today();
  state.ui.modal = null;
  addAudit("Adjuntó modelo de contrato", "Contratos", contract.folio, oldModel.name, contract.contractModelName);
  toast("Modelo de contrato adjuntado.", "success");
  saveState();
  render();
}

function prepareContractFromModel(id) {
  const contract = contractById(id);
  const employee = employeeById(contract.employeeId);
  const model = contractModel(contract);
  state.ui.contractDraft = {
    ...state.ui.contractDraft,
    employeeId: contract.employeeId,
    type: contract.type,
    folio: nextContractFolio(),
    company: contract.company,
    legalRep: contract.legalRep,
    signingPlace: contract.signingPlace,
    signDate: today(),
    startDate: today(),
    endDate: contract.endDate,
    trialPeriod: contract.trialPeriod,
    position: contract.position,
    department: contract.department,
    salary: contract.salary,
    frequency: employee.payFrequency,
    clauses: [...(contract.clauses || [])],
    contractModelName: model.name,
    contractModelVersion: model.version,
    contractModelFile: model.file
  };
  state.ui.selectedContractId = contract.id;
  state.ui.selectedEmployeeId = contract.employeeId;
  state.ui.contractStep = 2;
  state.ui.modal = null;
  addAudit("Preparó nuevo contrato desde modelo", "Contratos", contract.folio, "Modelo actual", model.name);
  toast("Nuevo contrato preparado con el modelo adjunto.", "success");
  saveState();
  navigate("contract-create");
}

function createOvertimeRequest(form) {
  const employeeId = Number(String(form.get("employeeId") || "").split("|")[0]);
  const employee = employeeById(employeeId);
  const dateValue = String(form.get("date") || today());
  const workedHours = Number(form.get("workedHours") || 0);
  const deliveryTime = Number(form.get("deliveryTime") || 0);
  const travel = Number(form.get("travel") || 0);
  const totalHours = +(workedHours + deliveryTime + travel).toFixed(2);
  const normalHours = Number(form.get("normalHours") || 0);
  const doubleHours = Number(form.get("doubleHours") || Math.min(totalHours, 3));
  const tripleHours = Number(form.get("tripleHours") || Math.max(0, totalHours - doubleHours - normalHours));
  const period = overtimePeriodForDate(dateValue);
  const nextId = Math.max(0, ...(state.data.overtimeRequests || []).map((item) => Number(item.id) || 0)) + 1;
  const row = {
    id: nextId,
    employeeId: employee.id,
    employee: employee.fullName,
    employeeNumber: employee.number,
    department: employee.department,
    position: employee.position,
    date: dateValue,
    day: weekdayName(dateValue),
    entry: form.get("entry"),
    exit: form.get("exit"),
    workedHours,
    deliveryTime,
    travel,
    totalHours,
    normalHours,
    doubleHours,
    tripleHours,
    hourlyRate: +(Number(employee.dailySalary || 0) / 8).toFixed(2),
    preliminaryAmount: overtimeAmount(employee, doubleHours, tripleHours),
    periodStart: period.start,
    periodEnd: period.end,
    cutDate: period.end,
    reason: String(form.get("reason") || "Solicitud operativa").trim(),
    status: "Pendiente",
    requestedBy: state.ui.role,
    approvedBy: ""
  };
  state.data.overtimeRequests = state.data.overtimeRequests || [];
  state.data.overtimeRequests.unshift(row);
  addAudit("Registro solicitud de horas extras", "Horas extras", employee.number, "Sin solicitud", money(row.preliminaryAmount));
  toast("Solicitud de horas extras registrada.", "success");
  saveState();
  render();
}

function saveOvertimeCutoff(form) {
  const first = Math.max(1, Math.min(31, Number(form.get("first") || 15)));
  const secondInput = Math.max(1, Math.min(31, Number(form.get("second") || 30)));
  const second = Math.max(first, secondInput);
  state.data.settings = state.data.settings || {};
  state.data.settings.overtimeCutoffDays = { first, second };
  state.ui.modal = null;
  addAudit("Actualizo corte de horas extras", "Horas extras", "Configuracion", "Anterior", `Dia ${first} y ${second}`);
  toast("Rango de corte actualizado.", "success");
  saveState();
  render();
}

function downloadOvertimeFormat() {
  const rows = (state.data.overtimeRequests || []).map((row) => ({
    empleado: row.employee,
    numeroEmpleado: row.employeeNumber,
    periodo: overtimePeriod(row),
    dia: `${row.day} ${date(row.date)}`,
    entrada: row.entry,
    salida: row.exit,
    horasTrabajadas: formatHours(row.workedHours),
    tiempoEntrega: formatHours(row.deliveryTime),
    trayecto: formatHours(row.travel),
    totalHoras: formatHours(row.totalHours),
    horasNormal: formatHours(row.normalHours),
    horasDoble: formatHours(row.doubleHours),
    horasTriple: formatHours(row.tripleHours),
    montoPreliminar: row.preliminaryAmount,
    estatus: row.status
  }));
  downloadText("formato-solicitud-horas-extras.csv", rowsToCsv(rows));
  toast("Formato de horas extras generado.", "success");
}

function saveCandidate(form) {
  const candidateId = Number(form.get("candidateId"));
  if (Number.isFinite(candidateId) && candidateId > 0) {
    updateCandidate(candidateId, form);
    return;
  }
  createCandidate(form);
}

function createCandidate(form) {
  state.data.candidates = state.data.candidates || [];
  const id = state.data.candidates.length + 1;
  const name = String(form.get("name") || "").trim();
  const email = String(form.get("email") || "").trim();
  if (!name) return toast("Captura el nombre del candidato.", "error");
  if (!email.includes("@")) return toast("Captura un correo válido.", "error");

  const candidate = {
    id,
    name,
    company: form.get("company"),
    position: form.get("position"),
    email,
    phone: form.get("phone"),
    experience: Number(form.get("experience") || 0),
    registeredAt: today(),
    source: form.get("source"),
    status: form.get("status"),
    cv: false,
    rhInterview: `${today()} 10:00`,
    technicalInterview: `${today()} 12:00`,
    responsible: "Recursos Humanos",
    requestedSalary: Number(form.get("requestedSalary") || 0),
    proposedOffer: Number(form.get("proposedOffer") || 0),
    negotiationStatus: "En negociación",
    interviewResult: "Pendiente",
    benefits: "De acuerdo a la ley",
    lastUpdate: today(),
    selected: false,
    avatarColor: ["#3157d5", "#0f9f9a", "#7657d8", "#e06b1d"][id % 4],
    initials: name.split(" ").slice(0, 2).map((part) => part[0]).join("").toUpperCase()
  };

  state.data.candidates.unshift(candidate);
  state.ui.candidateFormOpen = false;
  state.ui.candidateEditingId = null;
  addAudit("Registró candidato", "Candidatos", name, "Sin registro", candidate.status);
  toast("Candidato registrado.", "success");
  saveState();
  render();
}

function updateCandidate(id, form) {
  const candidate = (state.data.candidates || []).find((item) => Number(item.id) === Number(id));
  if (!candidate) return toast("No se encontró el candidato.", "error");

  const name = String(form.get("name") || "").trim();
  const email = String(form.get("email") || "").trim();
  if (!name) return toast("Captura el nombre del candidato.", "error");
  if (!email.includes("@")) return toast("Captura un correo válido.", "error");

  const previousStatus = candidate.status;
  Object.assign(candidate, {
    name,
    company: form.get("company"),
    position: form.get("position"),
    email,
    phone: form.get("phone"),
    experience: Number(form.get("experience") || 0),
    source: form.get("source"),
    status: form.get("status"),
    requestedSalary: Number(form.get("requestedSalary") || 0),
    proposedOffer: Number(form.get("proposedOffer") || 0),
    lastUpdate: today(),
    initials: name.split(" ").slice(0, 2).map((part) => part[0]).join("").toUpperCase()
  });
  candidate.selected = ["Preselección", "En entrevista", "Oferta", "Contratado"].includes(candidate.status);
  state.ui.candidateFormOpen = false;
  state.ui.candidateEditingId = null;
  addAudit("Actualizó candidato", "Candidatos", candidate.name, previousStatus, candidate.status);
  toast("Información del candidato actualizada.", "success");
  saveState();
  render();
}

function uploadCandidateCv(candidateId, fileName = "CV adjunto") {
  const candidates = state.data.candidates || [];
  const candidate = Number.isFinite(candidateId)
    ? candidates.find((item) => item.id === candidateId)
    : candidates.find((item) => !item.cv) || candidates[0];
  if (!candidate) return toast("No hay candidatos para actualizar.", "error");
  candidate.cv = true;
  candidate.cvFileName = fileName;
  candidate.status = candidate.status === "Nuevo" ? "En revisión" : candidate.status;
  candidate.lastUpdate = today();
  addAudit("Subió CV", "Candidatos", candidate.name, "Sin CV", fileName);
  toast(`CV agregado a ${candidate.name}.`, "success");
  saveState();
  render();
}

function enrichCandidateData() {
  (state.data.candidates || []).slice(0, 3).forEach((candidate) => {
    candidate.cv = true;
    candidate.status = candidate.status === "Nuevo" ? "En revisión" : candidate.status;
    candidate.lastUpdate = today();
  });
  addAudit("Actualizó datos de CV", "Candidatos", "Centro de datos", "Pendiente", "Normalizado");
  toast("Datos del CV normalizados para revisión.", "success");
  saveState();
  render();
}

function refreshCandidates() {
  (state.data.candidates || []).forEach((candidate) => { candidate.lastUpdate = today(); });
  toast("Panel de candidatos actualizado.", "success");
  saveState();
  render();
}

function exportCandidates() {
  const rows = (state.data.candidates || []).map((candidate) => ({
    nombre: candidate.name,
    empresa: candidate.company,
    puesto: candidate.position,
    correo: candidate.email,
    experiencia: candidate.experience,
    fuente: candidate.source,
    estatus: candidate.status,
    negociacion: candidate.negotiationStatus
  }));
  downloadText("candidatos-hr-suite.csv", rowsToCsv(rows));
  toast("Candidatos exportados.", "success");
}

function advanceCandidate(id) {
  const candidate = (state.data.candidates || []).find((item) => item.id === id);
  if (!candidate) return;
  const flow = ["Nuevo", "En revisión", "Preselección", "En entrevista", "Oferta", "Contratado"];
  const index = flow.indexOf(candidate.status);
  candidate.status = flow[Math.min(flow.length - 1, Math.max(0, index) + 1)];
  candidate.selected = ["Preselección", "En entrevista", "Oferta", "Contratado"].includes(candidate.status);
  candidate.lastUpdate = today();
  addAudit("Avanzó candidato", "Candidatos", candidate.name, flow[index] || "Sin etapa", candidate.status);
  toast(`${candidate.name} avanzó a ${candidate.status}.`, "success");
  saveState();
  render();
}

function scheduleCandidate(id) {
  const candidate = (state.data.candidates || []).find((item) => item.id === id);
  if (!candidate) return;
  candidate.selected = true;
  candidate.status = "En entrevista";
  candidate.interviewResult = "Programada";
  candidate.rhInterview = `${today()} 10:00`;
  candidate.technicalInterview = `${today()} 12:00`;
  candidate.lastUpdate = today();
  toast("Entrevista programada.", "success");
  saveState();
  render();
}

function toggleCandidateSelected(id) {
  const candidate = (state.data.candidates || []).find((item) => item.id === id);
  if (!candidate) return;
  candidate.selected = !candidate.selected;
  candidate.status = candidate.selected ? "Preselección" : "En revisión";
  candidate.lastUpdate = today();
  saveState();
  render();
}

function sendCandidateOffer(id) {
  const candidate = (state.data.candidates || []).find((item) => item.id === id);
  if (!candidate) return;
  candidate.status = "Oferta";
  candidate.negotiationStatus = "Pendiente firma";
  candidate.interviewResult = "Aprobado";
  candidate.lastUpdate = today();
  addAudit("Envió oferta", "Candidatos", candidate.name, "Negociación", "Pendiente firma");
  toast("Oferta enviada al candidato.", "success");
  saveState();
  render();
}

function downloadCandidateCv(id) {
  const candidate = (state.data.candidates || []).find((item) => item.id === id);
  if (!candidate) return;
  downloadText(`cv-${candidate.name.toLowerCase().replaceAll(" ", "-")}.txt`, `CV simulado\n${candidate.name}\n${candidate.position}\n${candidate.email}`);
  toast("CV preparado para descarga.", "success");
}

function duplicateSelectedTemplate() {
  const source = activeTemplate() || state.data.templates[0];
  const nextId = Math.max(0, ...(state.data.templates || []).map((template) => Number(template.id) || 0)) + 1;
  const copy = {
    ...source,
    id: nextId,
    name: `${source.name} copia`,
    version: "v1.0",
    updatedAt: today(),
    clauses: (source.clauses || []).map((clause) => ({ ...clause }))
  };
  state.data.templates.push(copy);
  state.ui.selectedTemplateId = copy.id;
  state.ui.templateEditing = true;
}

function deletedSeedNamesForTemplate(template) {
  const target = templateKey(template?.name);
  const seed = seedTemplateDefinitions().find((item) => templateIdentityNames(item).includes(target));
  return seed ? templateIdentityNames(seed) : [];
}

function deleteSelectedTemplate() {
  const template = activeTemplate();
  const templates = state.data.templates || [];
  if (!template) return;
  if (templates.length <= 1) {
    toast("Debe existir al menos una plantilla disponible.", "warning");
    return;
  }

  const removedName = template.name;
  const seedNames = deletedSeedNamesForTemplate(template);
  if (seedNames.length) {
    const deleted = new Set((state.data.deletedTemplateNames || []).map(templateKey).filter(Boolean));
    seedNames.forEach((name) => deleted.add(name));
    state.data.deletedTemplateNames = [...deleted];
  }

  state.data.templates = templates.filter((item) => Number(item.id) !== Number(template.id));
  state.ui.selectedTemplateId = state.data.templates[0]?.id || null;
  state.ui.templateEditing = false;
  state.ui.modal = null;
  addAudit("Eliminó plantilla", "Contratos", removedName, "Activa", "Eliminada");
  toast("Plantilla eliminada.", "success");
  saveState();
  render();
}

function saveSelectedTemplate(form) {
  const template = activeTemplate();
  if (!template) return;
  const oldName = template.name;
  template.name = String(form.get("name") || template.name).trim();
  template.type = String(form.get("type") || template.type || "Laboral").trim();
  template.version = String(form.get("version") || template.version || "v1.0").trim();
  template.body = String(form.get("body") || "").trim();
  template.updatedAt = today();
  template.clauses = (template.clauses || []).map((clause, index) => ({
    ...clause,
    active: form.has(`clause-${index}`)
  }));
  state.ui.templateEditing = false;
  addAudit("Actualizó plantilla", "Contratos", template.name, oldName, `${template.name} ${template.version}`);
  toast("Plantilla actualizada.", "success");
  saveState();
  render();
}

function insertVariable(variable) {
  const textarea = document.querySelector("[name=\"body\"]") || document.querySelector("#template-body");
  if (!textarea) return;
  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;
  textarea.value = `${textarea.value.slice(0, start)}${variable}${textarea.value.slice(end)}`;
  if (textarea.id === "template-body") {
    activeTemplate().body = textarea.value;
    saveState();
    render();
  } else {
    textarea.focus();
    textarea.setSelectionRange(start + String(variable).length, start + String(variable).length);
  }
}

function exportEmployees() {
  const rows = state.data.employees.map((employee) => ({
    numero: employee.number,
    nombre: employee.fullName,
    puesto: employee.position,
    departamento: employee.department,
    sucursal: employee.branch,
    estatus: employee.status,
    sueldo: employee.grossSalary
  }));
  downloadText("empleados-hr-suite.csv", rowsToCsv(rows));
  toast("Empleados exportados.", "success");
}

function exportPayroll() {
  const rows = state.data.employees.filter((employee) => employee.status === "Activo").map((employee) => {
    const calc = payrollCalc(employee);
    return {
      numero: employee.number,
      nombre: employee.fullName,
      departamento: employee.department,
      percepciones: calc.totalPerceptions,
      deducciones: calc.totalDeductions,
      neto: calc.net
    };
  });
  downloadText("nomina-resumen.csv", rowsToCsv(rows));
  toast("Resumen de nómina exportado.", "success");
}

function downloadDraftContract(format = "pdf") {
  const draft = state.ui.contractDraft;
  const employee = employeeById(draft.employeeId);
  const doc = contractDocumentData(draft, employee);
  const filenameBase = String(draft.folio || `contrato-${employee.number}`).toLowerCase().replace(/[^a-z0-9-]+/g, "-");
  if (format === "word") {
    const html = `
      <!doctype html>
      <html>
        <head>
          <meta charset="utf-8" />
          <style>
            body { font-family: Arial, sans-serif; color: #152033; line-height: 1.5; }
            h1 { text-align: center; font-size: 18px; }
            h2 { font-size: 14px; margin-top: 22px; }
            table { width: 100%; border-collapse: collapse; margin: 14px 0; }
            td { border: 1px solid #cbd5e1; padding: 7px; vertical-align: top; }
            .signatures { display: table; width: 100%; margin-top: 50px; }
            .signature { display: table-cell; width: 50%; text-align: center; padding-top: 42px; border-top: 1px solid #152033; }
          </style>
        </head>
        <body>
          <h1>${safe(doc.model.name)}</h1>
          <p><strong>Folio:</strong> ${safe(draft.folio)} | <strong>Modelo:</strong> ${safe(doc.model.version)} | <strong>Archivo base:</strong> ${safe(doc.model.file)}</p>
          <h2>Datos del empleado y condiciones</h2>
          <table>${doc.details.map(([label, value]) => `<tr><td><strong>${safe(label)}</strong></td><td>${safe(value)}</td></tr>`).join("")}</table>
          <h2>Texto del contrato</h2>
          ${doc.paragraphs.map((paragraph) => `<p>${safe(paragraph)}</p>`).join("")}
          <h2>Clausulas seleccionadas</h2>
          <p>${safe(doc.clauses.join(", "))}</p>
          <div class="signatures">
            <div class="signature">${safe(employee.fullName)}<br/>Empleado</div>
            <div class="signature">${safe(draft.legalRep)}<br/>Representante legal</div>
          </div>
        </body>
      </html>
    `;
    downloadBlob(`${filenameBase}.doc`, html, "application/msword;charset=utf-8");
    toast("Contrato descargado en Word.", "success");
    return;
  }

  const lines = [
    `Folio: ${draft.folio}`,
    `Modelo: ${doc.model.name} ${doc.model.version}`,
    "",
    "DATOS DEL EMPLEADO Y CONDICIONES",
    ...doc.details.map(([label, value]) => `${label}: ${value}`),
    "",
    "TEXTO DEL CONTRATO",
    ...doc.paragraphs,
    "",
    `Clausulas seleccionadas: ${doc.clauses.join(", ")}`,
    "",
    `Firma del empleado: ${employee.fullName}`,
    `Representante legal: ${draft.legalRep}`
  ];
  downloadBlob(`${filenameBase}.pdf`, buildSimplePdf(doc.model.name, lines), "application/pdf");
  toast("Contrato descargado en PDF.", "success");
}

function rowsToCsv(rows) {
  if (!rows.length) return "";
  const headers = Object.keys(rows[0]);
  const lines = [headers.join(",")];
  rows.forEach((row) => {
    lines.push(headers.map((header) => `"${String(row[header] ?? "").replaceAll('"', '""')}"`).join(","));
  });
  return lines.join("\n");
}

function downloadBlob(filename, content, type) {
  const blob = new Blob([content], { type });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = filename;
  link.click();
  URL.revokeObjectURL(url);
}

function downloadText(filename, content) {
  downloadBlob(filename, content, "text/plain;charset=utf-8");
}

function pdfText(value) {
  return String(value || "")
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/[^\x20-\x7E]/g, "")
    .replace(/\\/g, "\\\\")
    .replace(/\(/g, "\\(")
    .replace(/\)/g, "\\)");
}

function wrapPdfLine(line, max = 92) {
  const words = String(line || "").split(/\s+/);
  const wrapped = [];
  let current = "";
  words.forEach((word) => {
    if (`${current} ${word}`.trim().length > max) {
      wrapped.push(current);
      current = word;
    } else {
      current = `${current} ${word}`.trim();
    }
  });
  if (current) wrapped.push(current);
  return wrapped.length ? wrapped : [""];
}

function buildSimplePdf(title, lines) {
  const visibleLines = [title, "", ...lines].flatMap((line) => wrapPdfLine(line)).slice(0, 56);
  const stream = [
    "BT",
    "/F1 12 Tf",
    "50 760 Td",
    ...visibleLines.flatMap((line, index) => [`(${pdfText(line)}) Tj`, index === 1 ? "/F1 10 Tf" : "", "0 -13 Td"].filter(Boolean)),
    "ET"
  ].join("\n");
  const objects = [
    "<< /Type /Catalog /Pages 2 0 R >>",
    "<< /Type /Pages /Kids [3 0 R] /Count 1 >>",
    "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>",
    "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>",
    `<< /Length ${stream.length} >>\nstream\n${stream}\nendstream`
  ];
  let body = "%PDF-1.4\n";
  const offsets = [0];
  objects.forEach((object, index) => {
    offsets.push(body.length);
    body += `${index + 1} 0 obj\n${object}\nendobj\n`;
  });
  const xrefOffset = body.length;
  body += `xref\n0 ${objects.length + 1}\n0000000000 65535 f \n`;
  offsets.slice(1).forEach((offset) => {
    body += `${String(offset).padStart(10, "0")} 00000 n \n`;
  });
  body += `trailer\n<< /Size ${objects.length + 1} /Root 1 0 R >>\nstartxref\n${xrefOffset}\n%%EOF`;
  return body;
}

function renderCharts() {
  const payrollTrendChart = document.querySelector("#chart-payroll-trend");
  if (payrollTrendChart) {
    drawLineChart(payrollTrendChart, [865000, 902000, 918000, 960000, 1015000, 1068000], ["Feb", "Mar", "Abr", "May", "Jun", "Jul"]);
  }
  const deptDistributionChart = document.querySelector("#chart-dept-distribution");
  if (deptDistributionChart) {
    const deptCounts = state.data.departments.map((dept) => state.data.employees.filter((employee) => employee.department === dept && employee.status === "Activo").length);
    drawBars(deptDistributionChart, deptCounts, state.data.departments);
  }
  drawDoughnut(document.querySelector("#chart-payroll-composition"), [state.data.receipts.reduce((sum, receipt) => sum + receipt.perceptions, 0), state.data.receipts.reduce((sum, receipt) => sum + receipt.deductions, 0), state.data.receipts.reduce((sum, receipt) => sum + receipt.taxes, 0)], ["Percepciones", "Deducciones", "Impuestos"]);
  drawLineChart(document.querySelector("#chart-report-trend"), [740000, 780000, 805000, 812000, 930000, 1015000], ["Feb", "Mar", "Abr", "May", "Jun", "Jul"]);
}

function setupCanvas(canvas) {
  if (!canvas) return null;
  const rect = canvas.getBoundingClientRect();
  const ratio = window.devicePixelRatio || 1;
  canvas.width = Math.max(1, Math.floor(rect.width * ratio));
  canvas.height = Math.max(1, Math.floor(rect.height * ratio));
  const ctx = canvas.getContext("2d");
  ctx.scale(ratio, ratio);
  ctx.clearRect(0, 0, rect.width, rect.height);
  return { ctx, width: rect.width, height: rect.height };
}

function drawLineChart(canvas, values, labels) {
  const setup = setupCanvas(canvas);
  if (!setup) return;
  const { ctx, width, height } = setup;
  const padding = 28;
  const max = Math.max(...values) * 1.08;
  const min = Math.min(...values) * 0.92;
  ctx.strokeStyle = "#dbe3ef";
  ctx.lineWidth = 1;
  for (let i = 0; i < 4; i += 1) {
    const y = padding + i * ((height - padding * 2) / 3);
    ctx.beginPath();
    ctx.moveTo(padding, y);
    ctx.lineTo(width - padding, y);
    ctx.stroke();
  }
  ctx.strokeStyle = "#3157d5";
  ctx.lineWidth = 3;
  ctx.beginPath();
  values.forEach((value, index) => {
    const x = padding + index * ((width - padding * 2) / (values.length - 1));
    const y = height - padding - ((value - min) / (max - min)) * (height - padding * 2);
    if (index === 0) ctx.moveTo(x, y);
    else ctx.lineTo(x, y);
  });
  ctx.stroke();
  ctx.fillStyle = "#3157d5";
  values.forEach((value, index) => {
    const x = padding + index * ((width - padding * 2) / (values.length - 1));
    const y = height - padding - ((value - min) / (max - min)) * (height - padding * 2);
    ctx.beginPath();
    ctx.arc(x, y, 4, 0, Math.PI * 2);
    ctx.fill();
    ctx.fillStyle = "#607086";
    ctx.font = "11px Inter, sans-serif";
    ctx.fillText(labels[index], x - 10, height - 8);
    ctx.fillStyle = "#3157d5";
  });
}

function drawBars(canvas, values, labels) {
  const setup = setupCanvas(canvas);
  if (!setup) return;
  const { ctx, width, height } = setup;
  const padding = 26;
  const max = Math.max(...values, 1);
  const barWidth = (width - padding * 2) / values.length - 12;
  const colors = ["#3157d5", "#0f9f9a", "#7657d8", "#e06b1d", "#12805c"];
  values.forEach((value, index) => {
    const x = padding + index * ((width - padding * 2) / values.length) + 6;
    const barHeight = (value / max) * (height - padding * 2);
    const y = height - padding - barHeight;
    ctx.fillStyle = colors[index % colors.length];
    roundedRect(ctx, x, y, barWidth, barHeight, 6);
    ctx.fillStyle = "#607086";
    ctx.font = "11px Inter, sans-serif";
    ctx.fillText(labels[index].slice(0, 8), x, height - 8);
    ctx.fillStyle = "#152033";
    ctx.font = "700 12px Inter, sans-serif";
    ctx.fillText(value, x + barWidth / 2 - 4, y - 8);
  });
}

function drawDoughnut(canvas, values, labels) {
  const setup = setupCanvas(canvas);
  if (!setup) return;
  const { ctx, width, height } = setup;
  const total = values.reduce((sum, value) => sum + value, 0);
  const cx = width * 0.34;
  const cy = height * 0.5;
  const radius = Math.min(width, height) * 0.28;
  const colors = ["#3157d5", "#e06b1d", "#0f9f9a"];
  let start = -Math.PI / 2;
  values.forEach((value, index) => {
    const slice = (value / total) * Math.PI * 2;
    ctx.beginPath();
    ctx.moveTo(cx, cy);
    ctx.arc(cx, cy, radius, start, start + slice);
    ctx.closePath();
    ctx.fillStyle = colors[index];
    ctx.fill();
    start += slice;
  });
  ctx.globalCompositeOperation = "destination-out";
  ctx.beginPath();
  ctx.arc(cx, cy, radius * 0.58, 0, Math.PI * 2);
  ctx.fill();
  ctx.globalCompositeOperation = "source-over";
  ctx.font = "12px Inter, sans-serif";
  labels.forEach((label, index) => {
    const x = width * 0.62;
    const y = height * 0.35 + index * 28;
    ctx.fillStyle = colors[index];
    ctx.fillRect(x, y - 10, 12, 12);
    ctx.fillStyle = "#152033";
    ctx.fillText(`${label} ${Math.round((values[index] / total) * 100)}%`, x + 20, y);
  });
}

function roundedRect(ctx, x, y, width, height, radius) {
  ctx.beginPath();
  ctx.moveTo(x + radius, y);
  ctx.lineTo(x + width - radius, y);
  ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
  ctx.lineTo(x + width, y + height);
  ctx.lineTo(x, y + height);
  ctx.lineTo(x, y + radius);
  ctx.quadraticCurveTo(x, y, x + radius, y);
  ctx.fill();
}

render();
