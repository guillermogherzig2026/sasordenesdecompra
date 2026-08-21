const names = [
  ["María", "González", "Luna", "F"],
  ["Juan", "Pérez", "Morales", "M"],
  ["Ana", "López", "Ríos", "F"],
  ["Carlos", "Ramírez", "Díaz", "M"],
  ["Fernanda", "Castillo", "Vega", "F"],
  ["Diego", "Santos", "Cruz", "M"],
  ["Sofía", "Martínez", "Pineda", "F"],
  ["Luis", "Hernández", "Nava", "M"],
  ["Valeria", "Ortega", "Salas", "F"],
  ["Roberto", "Mendoza", "León", "M"],
  ["Daniela", "Flores", "Arias", "F"],
  ["Miguel", "Torres", "Campos", "M"],
  ["Paola", "Vargas", "Ibarra", "F"],
  ["Andrés", "Reyes", "Mejía", "M"],
  ["Gabriela", "Navarro", "Soto", "F"],
  ["Jorge", "Cárdenas", "Paz", "M"],
  ["Camila", "Fuentes", "Solís", "F"],
  ["Emilio", "Aguilar", "Mora", "M"],
  ["Renata", "Silva", "Bravo", "F"],
  ["Ricardo", "Medina", "Ochoa", "M"],
  ["Natalia", "Cortés", "Valdés", "F"],
  ["Hugo", "Escobar", "Varela", "M"],
  ["Mónica", "Delgado", "Prado", "F"],
  ["Iván", "Rojas", "Trejo", "M"],
  ["Elena", "Pacheco", "Franco", "F"]
];

const departments = [
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
const branches = ["CDMX Reforma", "Monterrey Centro", "Guadalajara Andares"];
const companies = ["NovaTalento Operadora", "Industrias Kairós", "Servicios Nébula"];
const positions = [
  "Analista de Nómina",
  "Generalista RH",
  "Desarrollador Frontend",
  "Contador Senior",
  "Coordinador Operativo",
  "Ejecutivo Comercial",
  "Gerente de Finanzas",
  "Especialista Legal Laboral",
  "Diseñador UX/UI",
  "Supervisor de Planta"
];

const contractTypes = [
  "Tiempo indeterminado",
  "Tiempo determinado",
  "Obra determinada",
  "Temporada",
  "Periodo de prueba",
  "Capacitación inicial",
  "Convenio modificatorio",
  "Carta oferta",
  "Acuerdo de confidencialidad",
  "Convenio de terminación",
  "Personalizado"
];

const incidenceTypes = [
  "Falta",
  "Retardo",
  "Horas extra",
  "Incapacidad",
  "Vacaciones",
  "Permiso con goce",
  "Permiso sin goce",
  "Bono",
  "Comisión",
  "Descuento",
  "Préstamo",
  "Anticipo",
  "Ajuste",
  "Retroactivo",
  "Prima dominical",
  "Prima vacacional",
  "Día festivo trabajado"
];

const banks = ["BBVA", "Banorte", "Santander", "Citibanamex", "HSBC", "Scotiabank"];
const avatarColors = ["#3b82f6", "#14b8a6", "#8b5cf6", "#f97316", "#10b981", "#ef4444", "#6366f1", "#0ea5e9"];

function pad(value, size = 3) {
  return String(value).padStart(size, "0");
}

function addDays(date, days) {
  const copy = new Date(date);
  copy.setDate(copy.getDate() + days);
  return copy.toISOString().slice(0, 10);
}

function monthAgo(months) {
  const date = new Date("2026-07-15T12:00:00");
  date.setMonth(date.getMonth() - months);
  return date.toISOString().slice(0, 10);
}

function initials(firstName, lastName) {
  return `${firstName[0]}${lastName[0]}`.toUpperCase();
}

function makeEmployees() {
  return names.map((entry, index) => {
    const id = index + 1;
    const [firstName, lastName, secondLastName, gender] = entry;
    const department = departments[index % departments.length];
    const branch = branches[index % branches.length];
    const company = companies[index % companies.length];
    const position = positions[index % positions.length];
    const salary = 18000 + (index % 9) * 4200 + (index > 17 ? 7000 : 0);
    const hireDate = monthAgo(2 + index);
    const status = index === 21 || index === 24 ? "Baja" : index === 8 ? "Suspendido" : "Activo";
    const contractType = index % 5 === 0 ? "Tiempo determinado" : index % 6 === 0 ? "Periodo de prueba" : "Tiempo indeterminado";
    const birthYear = 1986 + (index % 14);
    const curpCore = `${lastName.slice(0, 2)}${secondLastName[0]}${firstName[0]}`.toUpperCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    const rfcCore = `${lastName.slice(0, 2)}${secondLastName[0]}${firstName[0]}`.toUpperCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    return {
      id,
      number: `EMP-${pad(id)}`,
      firstName,
      lastName,
      secondLastName,
      fullName: `${firstName} ${lastName} ${secondLastName}`,
      gender,
      initials: initials(firstName, lastName),
      avatarColor: avatarColors[index % avatarColors.length],
      birthDate: `${birthYear}-${pad((index % 12) + 1, 2)}-${pad((index % 26) + 1, 2)}`,
      curp: `${curpCore}${String(birthYear).slice(2)}${pad((index % 12) + 1, 2)}${pad((index % 26) + 1, 2)}HDFRNN${pad(index, 2)}`.slice(0, 18),
      rfc: `${rfcCore}${String(birthYear).slice(2)}${pad((index % 12) + 1, 2)}${pad((index % 26) + 1, 2)}${["A1B", "K9P", "R7M", "X2Q"][index % 4]}`.slice(0, 13),
      nss: `72${pad(100000000 + index * 93021, 9)}`.slice(0, 11),
      civilStatus: ["Soltero", "Casado", "Unión libre", "Divorciado"][index % 4],
      nationality: "Mexicana",
      phone: `55 ${pad(1000 + index * 37, 4)} ${pad(2200 + index * 23, 4)}`,
      email: `${firstName}.${lastName}@novatalento.mx`.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, ""),
      address: `Av. Principal ${120 + index}, Col. Centro, Ciudad de México`,
      emergencyContact: `${["Laura", "Rafael", "Claudia", "Sergio"][index % 4]} ${lastName} - 55 ${pad(7720 + index, 4)} ${pad(8300 + index, 4)}`,
      company,
      branch,
      department,
      position,
      manager: names[(index + 4) % names.length][0] + " " + names[(index + 4) % names.length][1],
      hireDate,
      seniority: `${Math.max(1, Math.floor((25 + index) / 12))} años`,
      workerType: index % 3 === 0 ? "Sindicalizado" : "Confianza",
      modality: ["Presencial", "Remota", "Híbrida"][index % 3],
      workday: index % 5 === 0 ? "Mixta" : "Diurna",
      schedule: index % 3 === 0 ? "08:00 a 17:00" : "09:00 a 18:00",
      workDays: "Lunes a viernes",
      workplace: ["Corporativo", "Planta Norte", "Centro de servicios"][index % 3],
      riskClass: ["Clase I", "Clase II", "Clase III"][index % 3],
      status,
      grossSalary: salary,
      dailySalary: +(salary / 30).toFixed(2),
      integratedDailySalary: +((salary / 30) * 1.0493).toFixed(2),
      payFrequency: index % 2 === 0 ? "Quincenal" : "Mensual",
      payrollType: index % 6 === 0 ? "Extraordinaria" : "Ordinaria",
      bank: banks[index % banks.length],
      clabe: `01218000${pad(1000000000 + index * 7391, 10)}`.slice(0, 18),
      account: `${pad(43810000 + index * 941, 10)}`,
      paymentMethod: index % 9 === 0 ? "Efectivo" : "Transferencia",
      salaryZone: "Zona libre general",
      commissions: index % 5 === 0 ? "Variable mensual" : "No aplica",
      recurringBonus: index % 4 === 0 ? 1200 : 0,
      taxRegime: "Sueldos y salarios",
      fiscalZip: `${64000 + (index % 9) * 101}`,
      cfdiUse: "CN01 Nómina",
      taxContractType: contractType,
      taxWorkdayType: "Jornada diurna",
      taxRegimeType: "02 Sueldos",
      contractType,
      nextPay: "2026-07-31",
      vacationDays: 12 + (index % 10),
      loanBalance: index % 7 === 0 ? 3500 + index * 200 : 0,
      documents: [
        { name: "Identificación oficial", status: "Completo", date: addDays(hireDate, 1) },
        { name: "CURP", status: "Completo", date: addDays(hireDate, 1) },
        { name: "RFC", status: index % 8 === 0 ? "Pendiente" : "Completo", date: addDays(hireDate, 2) },
        { name: "Comprobante de domicilio", status: index % 6 === 0 ? "Por vencer" : "Completo", date: addDays(hireDate, 3) },
        { name: "Contrato", status: index % 5 === 0 ? "Pendiente de firma" : "Completo", date: addDays(hireDate, 4) }
      ],
      timeline: [
        { date: hireDate, title: "Alta de empleado", detail: `Ingreso a ${department}` },
        { date: addDays(hireDate, 15), title: "Contrato generado", detail: contractType },
        { date: addDays(hireDate, 45), title: "Expediente revisado", detail: "Documentos base validados" }
      ]
    };
  });
}

function makeContracts(employees) {
  const statuses = ["Activo", "Activo", "Pendiente de firma", "En aprobación", "Próximo a vencer", "Vencido"];
  return employees.slice(0, 20).map((employee, index) => {
    const start = employee.hireDate;
    const end = employee.contractType === "Tiempo indeterminado" ? "" : addDays("2026-07-20", [12, 28, 45, 75, -8, 118][index % 6]);
    const status = statuses[index % statuses.length];
    const modelName = employee.contractType === "Tiempo indeterminado" ? "Contrato por tiempo indeterminado" : "Contrato por tiempo determinado";
    return {
      id: index + 1,
      folio: `CNT-2026-${pad(index + 1)}`,
      employeeId: employee.id,
      employee: employee.fullName,
      company: employee.company,
      position: employee.position,
      department: employee.department,
      branch: employee.branch,
      type: employee.contractType,
      startDate: start,
      endDate: end,
      salary: employee.grossSalary,
      contractModelName: modelName,
      contractModelVersion: modelName === "Contrato por tiempo indeterminado" ? "v3.2" : "v2.4",
      contractModelFile: `${modelName}.docx`,
      contractModelAttachedAt: addDays(start, 1),
      status,
      employeeSignature: status === "Activo" ? "Firmado" : "Pendiente",
      companySignature: status === "Activo" || status === "Próximo a vencer" ? "Firmado" : "Pendiente",
      legalRep: "Mónica Salcedo Herrera",
      signingPlace: "Ciudad de México",
      trialPeriod: index % 6 === 0 ? "30 días" : "No aplica",
      clauses: ["Confidencialidad", "Propiedad intelectual", "Protección de datos", "Uso de herramientas", "Terminación"],
      approvals: [
        { step: "Elaborado por Recursos Humanos", status: "Aprobado", user: "Laura RH", date: "2026-07-12", comment: "Plantilla validada" },
        { step: "Aprobación Dirección Jurídica", status: index % 4 === 0 ? "Pendiente" : "Aprobado", user: index % 4 === 0 ? "" : "Dirección Jurídica", date: index % 4 === 0 ? "" : "2026-07-14", comment: index % 4 === 0 ? "" : "Aprobación jurídica registrada" }
      ],
      validationCode: `MX-${pad(index + 427, 4)}-${pad(employee.id, 3)}`,
      ipEvidence: `187.190.22.${20 + index}`
    };
  });
}

function makePayrollPeriods() {
  return [
    {
      id: 1,
      code: "NOM-2026-14",
      company: "NovaTalento Operadora",
      branch: "CDMX Reforma",
      type: "Ordinaria",
      frequency: "Quincenal",
      startDate: "2026-07-01",
      endDate: "2026-07-15",
      cutDate: "2026-07-16",
      payDate: "2026-07-18",
      year: 2026,
      number: 14,
      employeesIncluded: 23,
      department: "Todos",
      costCenter: "CC-001 Corporativo",
      status: "Calculada",
      observations: "Periodo con bonos trimestrales",
      locked: false
    },
    {
      id: 2,
      code: "NOM-2026-13",
      company: "NovaTalento Operadora",
      branch: "CDMX Reforma",
      type: "Ordinaria",
      frequency: "Quincenal",
      startDate: "2026-06-16",
      endDate: "2026-06-30",
      cutDate: "2026-07-01",
      payDate: "2026-07-03",
      year: 2026,
      number: 13,
      employeesIncluded: 24,
      department: "Todos",
      costCenter: "CC-001 Corporativo",
      status: "Cerrada",
      observations: "Periodo cerrado",
      locked: true
    },
    {
      id: 3,
      code: "NOM-2026-12",
      company: "Industrias Kairós",
      branch: "Monterrey Centro",
      type: "Bonos",
      frequency: "Extraordinaria",
      startDate: "2026-06-01",
      endDate: "2026-06-15",
      cutDate: "2026-06-16",
      payDate: "2026-06-20",
      year: 2026,
      number: 12,
      employeesIncluded: 18,
      department: "Operaciones",
      costCenter: "CC-210 Planta",
      status: "Pagada",
      observations: "Bono productividad",
      locked: true
    }
  ];
}

function makeIncidences(employees) {
  const statuses = ["Aprobada", "Pendiente", "Rechazada", "Aprobada", "Pendiente"];
  return Array.from({ length: 20 }, (_, index) => {
    const employee = employees[index % employees.length];
    const type = incidenceTypes[index % incidenceTypes.length];
    const quantity = type === "Horas extra" ? 4 + (index % 5) : type.includes("Permiso") || type === "Falta" ? 1 : 2;
    const amount = ["Bono", "Comisión", "Descuento", "Préstamo", "Anticipo", "Retroactivo"].includes(type)
      ? 800 + index * 115
      : type === "Horas extra"
        ? quantity * 185
        : 0;
    return {
      id: index + 1,
      employeeId: employee.id,
      employee: employee.fullName,
      type,
      date: addDays("2026-07-01", index),
      quantity,
      unit: type === "Horas extra" ? "Horas" : "Días",
      amount,
      evidence: index % 4 === 0 ? "Adjunta" : "No requerida",
      comments: index % 3 === 0 ? "Validar con jefe inmediato" : "Registro ordinario",
      createdBy: ["Recursos Humanos", "Nómina", "Empleado"][index % 3],
      status: statuses[index % statuses.length],
      approver: ["Laura RH", "Gerente directo", "Nómina"][index % 3]
    };
  });
}

function makeConcepts() {
  const perceptions = [
    ["P001", "Sueldo", "Ordinaria", true, false, true, true, "Días pagados", "sueldoDiario * días", 0, "Automática"],
    ["P002", "Horas extra", "Extraordinaria", true, true, true, false, "Horas", "horas * tarifa", 9, "Incidencia"],
    ["P003", "Bonos", "Variable", true, true, false, false, "Importe", "importe autorizado", 0, "Manual"],
    ["P004", "Comisiones", "Variable", true, false, false, false, "Porcentaje", "ventas * %", 0, "Manual"],
    ["P005", "Aguinaldo", "Prestación", true, true, false, false, "Proporcional", "días aguinaldo", 30, "Especial"],
    ["P006", "Prima vacacional", "Prestación", true, true, false, false, "Porcentaje", "vacaciones * 25%", 0, "Automática"],
    ["P007", "Prima dominical", "Prestación", true, true, true, false, "Días", "días * 25%", 0, "Incidencia"],
    ["P008", "PTU", "Prestación", true, true, false, false, "Importe", "base PTU", 0, "Especial"],
    ["P009", "Premios", "Variable", true, true, false, false, "Importe", "importe", 0, "Manual"],
    ["P010", "Gratificaciones", "Variable", true, true, false, false, "Importe", "importe", 0, "Manual"],
    ["P011", "Retroactivos", "Ajuste", true, false, true, false, "Diferencia", "nuevo - anterior", 0, "Ajuste"],
    ["P012", "Vales", "Previsión social", false, true, false, true, "Porcentaje", "sueldo * 10%", 0, "Automática"],
    ["P013", "Fondo de ahorro", "Previsión social", false, true, true, true, "Porcentaje", "sueldo * 6%", 0, "Automática"],
    ["P014", "Despensa", "Previsión social", false, true, false, true, "Importe fijo", "monto mensual", 0, "Automática"],
    ["P015", "Otras percepciones", "Otra", true, true, false, false, "Importe", "importe", 0, "Manual"]
  ].map((item, index) => ({
    id: index + 1,
    key: item[0],
    name: item[1],
    type: item[2],
    taxed: item[3],
    exempt: item[4],
    integrable: item[5],
    recurring: item[6],
    calculationType: item[7],
    formula: item[8],
    cap: item[9],
    application: item[10],
    status: "Activo"
  }));

  const deductions = [
    ["D001", "ISR", "Fiscal", true, 0, 0, "Tarifa ISR mensual", 0, 1, "", "", 0],
    ["D002", "IMSS", "Seguridad social", true, 0, 0, "SBC * cuotas obreras", 0, 2, "", "", 0],
    ["D003", "INFONAVIT", "Vivienda", true, 5, 0, "SBC * factor", 0, 3, "2026-01-01", "", 0],
    ["D004", "FONACOT", "Crédito", true, 0, 650, "Importe fijo", 650, 4, "2026-02-01", "2026-12-31", 5200],
    ["D005", "Préstamos", "Crédito interno", false, 0, 900, "Saldo / parcialidades", 0, 5, "2026-05-01", "", 6300],
    ["D006", "Anticipos", "Ajuste", false, 0, 1200, "Importe autorizado", 0, 6, "", "", 0],
    ["D007", "Pensión alimenticia", "Judicial", true, 20, 0, "Neto * porcentaje", 0, 1, "2026-01-01", "", 0],
    ["D008", "Faltas", "Laboral", true, 0, 0, "sueldoDiario * días", 0, 2, "", "", 0],
    ["D009", "Descuentos", "Manual", false, 0, 0, "Importe", 0, 7, "", "", 0],
    ["D010", "Fondo de ahorro", "Previsión social", true, 6, 0, "sueldo * 6%", 0, 5, "", "", 0],
    ["D011", "Caja de ahorro", "Ahorro", false, 0, 500, "Importe fijo", 0, 6, "", "", 2400],
    ["D012", "Seguro", "Prestación", true, 0, 220, "Importe fijo", 0, 8, "", "", 0],
    ["D013", "Deducción comedor", "Servicio", false, 0, 310, "Consumos", 0, 9, "", "", 0],
    ["D014", "Ajuste redondeo", "Ajuste", true, 0, 0, "Redondeo", 0, 10, "", "", 0],
    ["D015", "Otras deducciones", "Otra", false, 0, 0, "Manual", 0, 10, "", "", 0]
  ].map((item, index) => ({
    id: index + 1,
    key: item[0],
    name: item[1],
    type: item[2],
    automatic: item[3],
    percent: item[4],
    fixedAmount: item[5],
    formula: item[6],
    cap: item[7],
    priority: item[8],
    startDate: item[9],
    endDate: item[10],
    balance: item[11],
    status: "Activo"
  }));

  return { perceptions, deductions };
}

function makeReceipts(employees) {
  return employees.map((employee, index) => {
    const perceptions = +(employee.grossSalary / (employee.payFrequency === "Quincenal" ? 2 : 1) + employee.recurringBonus).toFixed(2);
    const taxes = +(perceptions * 0.105).toFixed(2);
    const social = +(perceptions * 0.028).toFixed(2);
    const deductions = +(taxes + social + (employee.loanBalance ? 650 : 0)).toFixed(2);
    return {
      id: index + 1,
      folio: `REC-2026-07-${pad(index + 1)}`,
      employeeId: employee.id,
      employee: employee.fullName,
      period: "NOM-2026-14",
      payDate: "2026-07-18",
      perceptions,
      deductions,
      taxes,
      net: +(perceptions - deductions).toFixed(2),
      status: index % 5 === 0 ? "Publicado" : index % 7 === 0 ? "Pendiente firma" : "Emitido",
      issueDate: "2026-07-18",
      confirmed: index % 6 !== 0
    };
  });
}

function makeAlerts() {
  return [
    { id: 1, level: "Crítica", module: "Nómina", title: "3 empleados sin cuenta bancaria", due: "Hoy", status: "Abierta" },
    { id: 2, level: "Crítica", module: "Contratos", title: "2 contratos vencidos sin renovación", due: "Hoy", status: "Abierta" },
    { id: 3, level: "Advertencia", module: "Incidencias", title: "5 incidencias pendientes de autorización", due: "24 h", status: "Abierta" },
    { id: 4, level: "Advertencia", module: "Nómina", title: "Variación neta mayor al 15% en Tecnología", due: "48 h", status: "Abierta" },
    { id: 5, level: "Informativa", module: "Recibos", title: "18 recibos publicados en expediente", due: "Semana", status: "Atendida" },
    { id: 6, level: "Advertencia", module: "Contratos", title: "6 contratos vencen en menos de 30 días", due: "30 días", status: "Abierta" },
    { id: 7, level: "Informativa", module: "Vacaciones", title: "12 solicitudes por revisar", due: "Semana", status: "Abierta" },
    { id: 8, level: "Crítica", module: "Pagos", title: "5 pagos rechazados requieren reproceso", due: "Hoy", status: "Abierta" },
    { id: 9, level: "Advertencia", module: "Expediente", title: "7 documentos por vencer", due: "15 días", status: "Abierta" },
    { id: 10, level: "Informativa", module: "Reportes", title: "Comparativo mensual listo", due: "Mes", status: "Atendida" }
  ];
}

function makePayments(employees) {
  const paidEmployees = employees.filter((employee) => employee.status === "Activo").slice(0, 20);
  const details = paidEmployees.map((employee, index) => {
    const receiptNet = +(employee.grossSalary / 2 - employee.grossSalary * 0.067 + employee.recurringBonus).toFixed(2);
    const rejected = [3, 7, 11, 15, 19].includes(index);
    return {
      id: index + 1,
      employeeId: employee.id,
      employee: employee.fullName,
      bank: employee.bank,
      account: employee.account,
      clabe: employee.clabe,
      amount: receiptNet,
      reference: `NOM14-${employee.number}`,
      status: rejected ? "Rechazado" : index % 4 === 0 ? "Pendiente" : "Generado",
      rejectReason: rejected ? ["CLABE inválida", "Cuenta cancelada", "Banco no disponible", "Límite excedido", "Nombre no coincide"][index % 5] : "",
      payDate: "2026-07-18",
      proof: rejected ? "" : "Pendiente"
    };
  });

  return {
    id: 1,
    period: "NOM-2026-14",
    company: "NovaTalento Operadora",
    payDate: "2026-07-18",
    bank: "BBVA",
    method: "Transferencia SPEI",
    employeeCount: details.length,
    totalAmount: details.reduce((sum, row) => sum + row.amount, 0),
    status: "Generado",
    folio: "DSP-2026-0718-001",
    details
  };
}

function makeAudit() {
  return [
    { id: 1, user: "Laura RH", action: "Creó contrato", module: "Contratos", record: "CNT-2026-003", oldValue: "Sin contrato", newValue: "Pendiente de firma", date: "2026-07-15", time: "10:22", ip: "187.190.22.31" },
    { id: 2, user: "Mónica Dirección", action: "Aprobó nómina", module: "Nómina", record: "NOM-2026-14", oldValue: "En revisión", newValue: "Aprobada dirección", date: "2026-07-16", time: "16:48", ip: "187.190.22.12" },
    { id: 3, user: "Carlos Nómina", action: "Editó deducción", module: "Deducciones", record: "D005", oldValue: "$800.00", newValue: "$900.00", date: "2026-07-16", time: "17:15", ip: "187.190.22.18" },
    { id: 4, user: "Finanzas", action: "Generó dispersión", module: "Pagos", record: "DSP-2026-0718-001", oldValue: "Pendiente", newValue: "Generado", date: "2026-07-17", time: "12:06", ip: "187.190.22.44" },
    { id: 5, user: "Empleado", action: "Confirmó recibo", module: "Recibos", record: "REC-2026-07-001", oldValue: "Publicado", newValue: "Confirmado", date: "2026-07-18", time: "09:21", ip: "187.190.22.82" }
  ];
}

function makeVacations(employees) {
  return employees.slice(0, 12).map((employee, index) => ({
    id: index + 1,
    employeeId: employee.id,
    employee: employee.fullName,
    available: employee.vacationDays,
    requested: 2 + (index % 5),
    startDate: addDays("2026-08-01", index * 3),
    endDate: addDays("2026-08-03", index * 3),
    status: ["Pendiente", "Aprobada", "Rechazada"][index % 3],
    requestedBy: employee.manager,
    approver: "Recursos Humanos"
  }));
}

function makeManagers(employees) {
  const managerNames = [
    ["Laura", "Sánchez", "Morales"],
    ["David", "Rivas", "Ortega"],
    ["Iván", "Rojas", "Trejo"]
  ];
  return branches.map((branch, index) => {
    const [firstName, lastName, secondLastName] = managerNames[index];
    const branchEmployees = employees.filter((employee) => employee.branch === branch && employee.status === "Activo");
    return {
      id: index + 1,
      number: `GER-${pad(index + 1)}`,
      firstName,
      lastName,
      secondLastName,
      name: `${firstName} ${lastName} ${secondLastName}`,
      initials: initials(firstName, lastName),
      email: `${firstName}.${lastName}@novatalento.mx`.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, ""),
      phone: `55 ${pad(4100 + index, 4)} ${pad(7300 + index, 4)}`,
      company: companies[index % companies.length],
      branches: [branch],
      role: "Gerente de sucursal",
      status: "Activo",
      vacationPermission: "Crear solicitudes de vacaciones",
      employeeCount: branchEmployees.length
    };
  });
}

function makeOvertimeRequests(employees) {
  return employees.slice(0, 16).map((employee, index) => {
    const date = addDays("2026-07-01", index * 2);
    const dayNumber = Number(date.slice(-2));
    const workedHours = 2 + (index % 4);
    const deliveryTime = index % 3 === 0 ? 0.5 : 0;
    const travel = index % 4 === 0 ? 0.5 : 0;
    const totalHours = +(workedHours + deliveryTime + travel).toFixed(2);
    const doubleHours = Math.min(totalHours, 3);
    const tripleHours = Math.max(0, +(totalHours - 3).toFixed(2));
    const hourlyRate = employee.dailySalary / 8;
    return {
      id: index + 1,
      employeeId: employee.id,
      employee: employee.fullName,
      employeeNumber: employee.number,
      department: employee.department,
      position: employee.position,
      date,
      day: ["Lunes", "Martes", "Miércoles", "Jueves", "Viernes"][index % 5],
      entry: index % 2 === 0 ? "06:00" : "18:00",
      exit: index % 2 === 0 ? `${String(8 + workedHours).padStart(2, "0")}:00` : `${20 + (index % 3)}:30`,
      workedHours,
      deliveryTime,
      travel,
      totalHours,
      normalHours: 0,
      doubleHours,
      tripleHours,
      hourlyRate: +hourlyRate.toFixed(2),
      preliminaryAmount: +((doubleHours * hourlyRate * 2) + (tripleHours * hourlyRate * 3)).toFixed(2),
      periodStart: dayNumber <= 15 ? "2026-07-01" : "2026-07-16",
      periodEnd: dayNumber <= 15 ? "2026-07-15" : "2026-07-30",
      cutDate: dayNumber <= 15 ? "2026-07-15" : "2026-07-30",
      reason: ["Cierre de nómina", "Entrega urgente", "Inventario", "Soporte operativo"][index % 4],
      status: ["Pendiente", "Aprobada", "Pendiente", "Rechazada"][index % 4],
      requestedBy: employee.manager,
      approvedBy: index % 4 === 1 ? "Recursos Humanos" : ""
    };
  });
}

function makeCandidates() {
  const base = [
    ["Ana Laura Méndez", "NovaTalento Operadora", "Analista de RH", "ana.mendez@talento.mx", "55 1234 5678", 4, "2026-07-01", "LinkedIn", "En revisión", true, "2026-07-03 10:00", "2026-07-04 09:00", "Laura Sánchez", 62000, 58000, "En negociación"],
    ["Carlos Ramírez", "Industrias Kairós", "Desarrollador Sr.", "carlos.ramirez@talento.mx", "55 9876 5432", 6, "2026-07-02", "Computrabajo", "En entrevista", true, "2026-07-04 11:00", "2026-07-05 09:00", "David Rivas", 65000, 60000, "Negociación final"],
    ["María José Torres", "Servicios Nébula", "Especialista Financiera", "maria.torres@talento.mx", "55 2345 7890", 5, "2026-07-03", "Referido", "Preselección", true, "2026-07-04 12:00", "2026-07-05 11:00", "Laura Sánchez", 57000, 52000, "Aprobado"],
    ["Luis Enrique Gómez", "Industrias Kairós", "Ingeniero de Datos", "luis.gomez@talento.mx", "55 6789 4321", 3, "2026-07-05", "Indeed", "En revisión", false, "2026-07-05 10:00", "2026-07-06 10:00", "David Rivas", 55000, 48000, "Pendiente firma"],
    ["Paulina Vega", "NovaTalento Operadora", "Coordinadora de Nómina", "paulina.vega@talento.mx", "55 4567 8901", 2, "2026-07-06", "Bolsa de trabajo", "Nuevo", true, "2026-07-08 09:30", "2026-07-09 10:00", "Laura Sánchez", 42000, 39000, "En negociación"],
    ["Jorge Ramírez", "Servicios Nébula", "Soporte TI", "jorge.ramirez@talento.mx", "55 1200 3488", 3, "2026-07-07", "LinkedIn", "En revisión", true, "2026-07-09 12:30", "2026-07-10 10:30", "David Rivas", 36000, 34000, "Aprobado"],
    ["Clara Morales", "NovaTalento Operadora", "Marketing Manager", "clara.morales@talento.mx", "55 1122 3344", 7, "2026-07-08", "Referido", "Oferta", true, "2026-07-10 09:00", "2026-07-11 09:30", "Laura Sánchez", 70000, 66000, "Pendiente firma"],
    ["Iván Paredes", "Industrias Kairós", "Comprador Sr.", "ivan.paredes@talento.mx", "55 7788 9911", 5, "2026-07-09", "OCC", "En entrevista", false, "2026-07-11 11:00", "2026-07-12 10:00", "David Rivas", 52000, 49500, "En negociación"]
  ];

  return base.map((item, index) => ({
    id: index + 1,
    name: item[0],
    company: item[1],
    position: item[2],
    email: item[3],
    phone: item[4],
    experience: item[5],
    registeredAt: item[6],
    source: item[7],
    status: item[8],
    cv: item[9],
    rhInterview: item[10],
    technicalInterview: item[11],
    responsible: item[12],
    requestedSalary: item[13],
    proposedOffer: item[14],
    negotiationStatus: item[15],
    interviewResult: index === 3 ? "Pendiente" : "Aprobado",
    benefits: index % 3 === 0 ? "Superiores a la ley" : index % 3 === 1 ? "De acuerdo a la ley" : "Mixto",
    lastUpdate: addDays("2026-07-10", index),
    selected: index < 4,
    avatarColor: avatarColors[index % avatarColors.length],
    initials: item[0].split(" ").slice(0, 2).map((part) => part[0]).join("").toUpperCase()
  }));
}

function makeTemplates() {
  return [
    {
      id: 1,
      name: "Contrato por tiempo indeterminado",
      version: "v3.2",
      status: "Activo",
      updatedAt: "2026-07-11",
      clauses: [
        { name: "Confidencialidad", active: true },
        { name: "Propiedad intelectual", active: true },
        { name: "Protección de datos", active: true },
        { name: "No competencia", active: false },
        { name: "Terminación", active: true }
      ],
      body: "Entre {{Empresa}} y {{Nombre del empleado}}, se celebra contrato para desempeñar el puesto de {{Puesto}} en {{Departamento}}, con sueldo mensual de {{Sueldo}} y jornada {{Jornada}}."
    },
    {
      id: 2,
      name: "Convenio modificatorio",
      version: "v1.8",
      status: "Activo",
      updatedAt: "2026-06-30",
      clauses: [
        { name: "Cambio de sueldo", active: true },
        { name: "Cambio de puesto", active: true },
        { name: "Ratificación de condiciones", active: true }
      ],
      body: "Las partes acuerdan modificar las condiciones del contrato vigente a partir de {{Fecha de firma}}."
    }
  ];
}

export function createInitialData() {
  const employees = makeEmployees();
  const concepts = makeConcepts();
  const paymentBatch = makePayments(employees);
  return {
    employees,
    departments,
    branches,
    companies,
    positions,
    banks,
    contractTypes,
    incidenceTypes,
    contracts: makeContracts(employees),
    payrollPeriods: makePayrollPeriods(),
    incidences: makeIncidences(employees),
    perceptions: concepts.perceptions,
    deductions: concepts.deductions,
    receipts: makeReceipts(employees),
    alerts: makeAlerts(),
    paymentBatch,
    rejectedPayments: paymentBatch.details.filter((row) => row.status === "Rechazado"),
    audit: makeAudit(),
    vacations: makeVacations(employees),
    managers: makeManagers(employees),
    overtimeRequests: makeOvertimeRequests(employees),
    candidates: makeCandidates(),
    templates: makeTemplates(),
    approvalFlow: [
      { id: 1, name: "Nómina calculada", role: "Nómina", status: "Aprobado", user: "Carlos Nómina", date: "2026-07-16", comment: "Cálculo inicial completo" },
      { id: 2, name: "Revisión de Recursos Humanos", role: "Recursos Humanos", status: "Aprobado", user: "Laura RH", date: "2026-07-16", comment: "Incidencias revisadas" },
      { id: 3, name: "Revisión de Nómina", role: "Nómina", status: "Aprobado", user: "Carlos Nómina", date: "2026-07-16", comment: "Ajustes validados" },
      { id: 4, name: "Revisión de Finanzas", role: "Finanzas", status: "Pendiente", user: "", date: "", comment: "" },
      { id: 5, name: "Aprobación de Dirección", role: "Dirección", status: "Pendiente", user: "", date: "", comment: "" },
      { id: 6, name: "Autorización de pago", role: "Finanzas", status: "Pendiente", user: "", date: "", comment: "" },
      { id: 7, name: "Nómina pagada", role: "Finanzas", status: "Pendiente", user: "", date: "", comment: "" },
      { id: 8, name: "Periodo cerrado", role: "Nómina", status: "Pendiente", user: "", date: "", comment: "" }
    ],
    payrollValidations: [
      { id: 1, type: "Crítica", title: "Empleados sin cuenta bancaria", count: 3, status: "Abierta" },
      { id: 2, type: "Crítica", title: "Contratos vencidos", count: 2, status: "Abierta" },
      { id: 3, type: "Crítica", title: "Incidencias sin autorización", count: 5, status: "Abierta" },
      { id: 4, type: "Advertencia", title: "Diferencia contra periodo anterior", count: 4, status: "Abierta" },
      { id: 5, type: "Advertencia", title: "Sueldo modificado", count: 2, status: "Abierta" },
      { id: 6, type: "Informativa", title: "Pago extraordinario", count: 1, status: "Abierta" },
      { id: 7, type: "Informativa", title: "Empleados sin recibo", count: 2, status: "Abierta" }
    ],
    tasks: [
      { id: 1, title: "Autorizar nómina NOM-2026-14", owner: "Dirección", due: "Hoy", status: "Pendiente" },
      { id: 2, title: "Reprocesar pagos rechazados", owner: "Finanzas", due: "Hoy", status: "Pendiente" },
      { id: 3, title: "Firmar 4 contratos", owner: "Recursos Humanos", due: "24 h", status: "Pendiente" },
      { id: 4, title: "Revisar incidencias abiertas", owner: "Nómina", due: "24 h", status: "Pendiente" },
      { id: 5, title: "Publicar recibos en expediente", owner: "Nómina", due: "Semana", status: "Pendiente" }
    ],
    settings: {
      salaryAccess: true,
      closedPeriodsLocked: true,
      bankMasking: true,
      contractAlerts: [90, 60, 30, 15, 7, 0],
      overtimeCutoffDays: { first: 15, second: 30 },
      sessionTimeout: 30
    }
  };
}

export const roles = [
  "Superadministrador",
  "Recursos Humanos",
  "Nómina",
  "Finanzas",
  "Dirección",
  "Gerente de sucursal",
  "Empleado"
];

export const menuItems = [
  { route: "dashboard", label: "Inicio", icon: "layout-dashboard" },
  { route: "candidates", label: "Registro de candidatos", icon: "badge-user" },
  { route: "contracts", label: "Contratos", icon: "file-signature" },
  { route: "employees", label: "Empleados", icon: "users" },
  { route: "pending-approvals", label: "Pendientes de Aprobación", icon: "shield" },
  { route: "payroll", label: "Nómina", icon: "calculator" },
  { route: "overtime", label: "Horas extras", icon: "calendar-alert" },
  { route: "reports", label: "Reportes", icon: "chart" },
  { route: "config", label: "Configuración", icon: "settings" },
  { route: "managers", label: "Gerentes", icon: "badge-user", superOnly: true }
];

export const screenGroups = {
  "pending-approvals": ["pending-approvals"],
  candidates: ["candidates"],
  employees: ["employees", "employee-new", "employee-profile", "employee-documents", "vacations"],
  contracts: ["contracts", "contracts-list", "contracts-drafts", "contract-create", "contract-editor", "contract-approval", "contract-signature", "contract-renewal", "termination"],
  payroll: ["payroll", "payroll-history", "payroll-period", "payroll-calc", "payroll-summary", "validations", "payroll-authorization", "settlement", "incidences", "concepts", "perceptions", "deductions", "payments", "dispersion", "rejected-payments", "receipts", "receipt-view"],
  overtime: ["overtime"],
  concepts: ["concepts", "perceptions", "deductions"],
  payments: ["payments", "dispersion", "rejected-payments"],
  receipts: ["receipts", "receipt-view"],
  config: ["config", "template-editor", "audit"],
  managers: ["managers"]
};

export const routeTitles = {
  login: "Inicio de sesión",
  "pending-approvals": "Pendientes de Aprobación",
  dashboard: "Inicio",
  candidates: "Registro de candidatos",
  employees: "Listado de empleados",
  "employee-new": "Alta de empleado",
  "employee-profile": "Perfil del empleado",
  "employee-documents": "Expediente documental",
  contracts: "Listado de contratos",
  "contracts-list": "Listado de contratos",
  "contracts-drafts": "Contratos en elaboración",
  "contract-create": "Crear contrato",
  "contract-editor": "Editor de contrato",
  "contract-approval": "Flujo de autorización",
  "contract-signature": "Firma digital",
  "contract-renewal": "Renovación de contrato",
  termination: "Terminación laboral",
  payroll: "Dashboard de nómina",
  "payroll-history": "Historial de nóminas",
  overtime: "Horas extras",
  "payroll-period": "Crear periodo de nómina",
  incidences: "Registro de incidencias",
  concepts: "Conceptos de nómina",
  perceptions: "Catálogo de percepciones",
  deductions: "Catálogo de deducciones",
  "payroll-calc": "Cálculo individual de nómina",
  "payroll-summary": "Resumen general de nómina",
  validations: "Validaciones y errores",
  "payroll-authorization": "Autorización de nómina",
  payments: "Generación de pagos",
  dispersion: "Detalle de dispersión",
  "rejected-payments": "Pagos rechazados",
  receipts: "Recibos de nómina",
  "receipt-view": "Vista del recibo",
  settlement: "Cálculo de finiquito",
  reports: "Reportes",
  managers: "Gerentes",
  config: "Configuración",
  "template-editor": "Editor de plantillas",
  vacations: "Vacaciones",
  audit: "Historial y auditoría"
};

