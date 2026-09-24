import { createInitialData } from "../../hr-suite/assets/js/data.js";
import {
  assignmentProgress,
  assignProgram,
  loadTrainingData,
  nextId,
  saveTrainingData,
  toggleModuleCompletion
} from "./training-data.js";

const HR_STORAGE_KEY = "hr-suite-mexico-state-v1";
const app = document.querySelector("#app");
const toastRegion = document.querySelector("#toast-region");
const training = loadTrainingData(localStorage);
const ui = {
  section: "personnel",
  programId: null,
  programTab: "modules",
  company: "",
  search: "",
  assignmentFilter: "all",
  modal: null
};

function employees() {
  try {
    const saved = JSON.parse(localStorage.getItem(HR_STORAGE_KEY));
    if (Array.isArray(saved?.data?.employees)) return saved.data.employees;
  } catch {
    // The HR catalog will use its initial data when no saved state is available.
  }
  return createInitialData().employees;
}

function escapeHtml(value) {
  return String(value ?? "").replaceAll("&", "&amp;").replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;").replaceAll('"', "&quot;").replaceAll("'", "&#039;");
}

function key(value) {
  return String(value ?? "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
}

function localDate() {
  const date = new Date();
  return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, "0")}-${String(date.getDate()).padStart(2, "0")}`;
}

function safeResourceUrl(value) {
  try {
    const url = new URL(value);
    return ["http:", "https:"].includes(url.protocol) ? url.href : "";
  } catch {
    return "";
  }
}

function notify(message, error = false) {
  const item = document.createElement("div");
  item.className = `toast ${error ? "error" : "success"}`;
  item.textContent = message;
  toastRegion.append(item);
  setTimeout(() => item.remove(), 3400);
}

function persist() {
  try {
    saveTrainingData(localStorage, training);
    render();
    return true;
  } catch {
    notify("No se pudieron guardar los cambios en este navegador.", true);
    return false;
  }
}

function selectedProgram() {
  return training.programs.find((program) => Number(program.id) === Number(ui.programId)) || null;
}

function findAssignment(id) {
  return training.assignments.find((assignment) => Number(assignment.id) === Number(id)) || null;
}

function programFor(assignment) {
  return training.programs.find((program) => Number(program.id) === Number(assignment.programId)) || null;
}

function activePrograms() {
  return training.programs.filter((program) => program.status === "Activo" && program.modules.length > 0);
}

function button(label, action, extra = "", type = "button") {
  return `<button class="btn ${extra}" type="${type}" data-action="${action}">${label}</button>`;
}

function empty(title, detail = "") {
  return `<div class="training-empty"><strong>${escapeHtml(title)}</strong>${detail ? `<span>${escapeHtml(detail)}</span>` : ""}</div>`;
}

function pill(label) {
  const tone = label === "Activo" ? "active" : label === "Borrador" ? "draft"
    : label === "Concluida" ? "complete" : label === "En curso" ? "progress" : "inactive";
  return `<span class="training-pill ${tone}">${escapeHtml(label)}</span>`;
}

function renderHeader() {
  return `<header class="section-header">
    <div><p class="eyebrow">Recursos Humanos</p><h3>Personal y capacitación</h3></div>
    <div class="actions">
      ${button("+ Nuevo programa", "new-program", "secondary")}
      ${button("+ Nueva capacitación", "new-assignment", "", "button")}
    </div>
  </header>`;
}

function renderTabs() {
  return `<nav class="training-tabs" role="tablist" aria-label="Capacitación">
    ${[["personnel", "Personal"], ["programs", "Programas"], ["tracking", "Seguimiento"]]
      .map(([id, label]) => `<button type="button" role="tab" aria-selected="${ui.section === id}" data-action="section" data-section="${id}">${label}</button>`).join("")}
  </nav>`;
}

function renderPrograms() {
  if (!training.programs.length) return empty("No hay programas registrados.", "Crea el primer programa para organizar la capacitación.");
  const program = selectedProgram();
  const cards = training.programs.map((item) => `<article class="training-program-card ${Number(item.id) === Number(program?.id) ? "is-selected" : ""}">
      <button type="button" data-action="select-program" data-id="${item.id}" aria-pressed="${Number(item.id) === Number(program?.id)}">
        ${pill(item.status)}<strong>${escapeHtml(item.title)}</strong>
        <small>${escapeHtml(item.owner || "Sin responsable")} · ${item.modules.length} módulos</small>
      </button>
      <button class="training-card-edit" type="button" data-action="edit-program" data-id="${item.id}" aria-label="Editar ${escapeHtml(item.title)}" title="Editar programa">✎</button>
    </article>`).join("");
  return `<div class="training-program-strip" role="region" aria-label="Programas de capacitación">${cards}</div>
    <section class="training-detail" aria-label="Detalle del programa">
      <div class="training-detail-head">
        <div><h4>${escapeHtml(program.title)}</h4><p>${escapeHtml(program.description || "Sin descripción")}</p></div>
        <div class="actions">${button("✎ Editar", "edit-program", "secondary")}${button("Eliminar", "delete-program", "secondary")}</div>
      </div>
      <nav class="training-tabs" role="tablist" aria-label="Contenido del programa">
        ${[["modules", "Módulos"], ["exams", "Exámenes"], ["tasks", "Tareas"]]
          .map(([id, label]) => `<button type="button" role="tab" aria-selected="${ui.programTab === id}" data-action="program-tab" data-tab="${id}">${label}</button>`).join("")}
      </nav>
      ${renderProgramContent(program)}
    </section>`;
}

function contentTable(headers, rows) {
  return `<div class="training-table-wrap"><table class="training-table"><thead><tr>${headers.map((header) => `<th scope="col">${header}</th>`).join("")}</tr></thead><tbody>${rows}</tbody></table></div>`;
}

function itemActions(type, id) {
  return `<div class="actions"><button class="btn secondary compact" type="button" data-action="edit-item" data-kind="${type}" data-id="${id}">Editar</button><button class="btn secondary compact" type="button" data-action="delete-item" data-kind="${type}" data-id="${id}">Eliminar</button></div>`;
}

function renderProgramContent(program) {
  const tab = ui.programTab;
  const labels = { modules: "módulo", exams: "pregunta", tasks: "tarea" };
  const titles = { modules: "Módulos", exams: "Exámenes", tasks: "Tareas" };
  const list = program[tab];
  let table = empty(`No hay ${titles[tab].toLowerCase()} registrados.`);
  if (list.length && tab === "modules") {
    table = contentTable(["Módulo", "Duración", "Recurso", "Acciones"], list.map((item) => `<tr>
      <td><strong>${escapeHtml(item.title)}</strong><small>${escapeHtml(item.description || "")}</small></td>
      <td>${escapeHtml(item.duration || "Sin definir")}</td>
      <td>${safeResourceUrl(item.resourceUrl) ? `<a href="${escapeHtml(safeResourceUrl(item.resourceUrl))}" target="_blank" rel="noopener noreferrer">Abrir recurso</a>` : "Sin recurso"}</td>
      <td>${itemActions("modules", item.id)}</td>
    </tr>`).join(""));
  } else if (list.length && tab === "exams") {
    table = contentTable(["Módulo", "Pregunta", "Respuesta", "Aprobación", "Acciones"], list.map((item) => `<tr>
      <td>${escapeHtml(program.modules.find((module) => Number(module.id) === Number(item.moduleId))?.title || "Módulo eliminado")}</td>
      <td><strong>${escapeHtml(item.question)}</strong></td>
      <td>${escapeHtml(item.options?.[Number(item.correct)] || "")}</td>
      <td>${Number(item.minimum) || 80}%</td><td>${itemActions("exams", item.id)}</td>
    </tr>`).join(""));
  } else if (list.length) {
    table = contentTable(["Módulo", "Tarea", "Descripción", "Acciones"], list.map((item) => `<tr>
      <td>${escapeHtml(program.modules.find((module) => Number(module.id) === Number(item.moduleId))?.title || "Módulo eliminado")}</td>
      <td><strong>${escapeHtml(item.title)}</strong></td><td>${escapeHtml(item.description || "")}</td>
      <td>${itemActions("tasks", item.id)}</td>
    </tr>`).join(""));
  }
  return `<div class="training-toolbar"><h4>${titles[tab]}</h4>
    <button class="btn" type="button" data-action="new-item" data-kind="${tab}" ${tab !== "modules" && !program.modules.length ? "disabled" : ""}>+ Agregar ${labels[tab]}</button>
  </div>${table}`;
}

function renderPersonnel() {
  const all = employees();
  const companies = [...new Set(all.map((employee) => employee.company).filter(Boolean))].sort((a, b) => a.localeCompare(b, "es"));
  const visible = ui.company ? all.filter((employee) => employee.company === ui.company) : all;
  const rows = visible.map((employee) => {
    const count = training.assignments.filter((assignment) => Number(assignment.employeeId) === Number(employee.id)).length;
    return `<tr data-search-row data-search="${escapeHtml(key(`${employee.fullName} ${employee.number} ${employee.company} ${employee.branch}`))}">
      <td><strong>${escapeHtml(employee.fullName)}</strong><small>${escapeHtml(employee.number)}</small></td>
      <td>${escapeHtml(employee.company || "Sin empresa")}<small>${escapeHtml(employee.branch || "")}</small></td>
      <td>${escapeHtml(employee.position || "Sin puesto")}</td><td>${pill(employee.status || "Sin estado")}</td>
      <td>${count}</td><td><button class="btn secondary compact" type="button" data-action="assign-person" data-id="${employee.id}" ${employee.status !== "Activo" ? "disabled" : ""}>Asignar</button></td>
    </tr>`;
  }).join("");
  return `<div class="training-toolbar"><div><h4>Personal</h4><p>${all.length} empleados en el catálogo de Recursos Humanos</p></div></div>
    <div class="training-filters">
      <input type="search" data-search-input value="${escapeHtml(ui.search)}" placeholder="Buscar empleado, número o sede" aria-label="Buscar empleado">
      <select data-company-filter aria-label="Filtrar por empresa"><option value="">Todas las empresas</option>
        ${companies.map((company) => `<option value="${escapeHtml(company)}" ${ui.company === company ? "selected" : ""}>${escapeHtml(company)}</option>`).join("")}
      </select>
    </div>
    ${visible.length ? contentTable(["Empleado", "Empresa y sede", "Puesto", "Estado", "Capacitaciones", "Acción"], rows) : empty("No hay empleados para esta empresa.")}`;
}

function renderTracking() {
  const list = training.assignments.filter((assignment) => {
    if (ui.assignmentFilter === "all") return true;
    const progress = assignmentProgress(assignment, programFor(assignment));
    return ui.assignmentFilter === "completed" ? progress.status === "Concluida" : progress.status === "En curso";
  });
  const rows = list.map((assignment) => {
    const program = programFor(assignment);
    const progress = assignmentProgress(assignment, program);
    const employee = employees().find((item) => Number(item.id) === Number(assignment.employeeId));
    return `<tr data-search-row data-search="${escapeHtml(key(`${employee?.fullName || assignment.employeeName} ${program?.title} ${employee?.company || assignment.company}`))}">
      <td><strong>${escapeHtml(employee?.fullName || assignment.employeeName || "Empleado eliminado")}</strong><small>${escapeHtml(employee?.company || assignment.company || "")}</small></td>
      <td>${escapeHtml(program?.title || "Programa eliminado")}</td>
      <td>${escapeHtml(assignment.startDate)}<small>${assignment.dueDate ? `Límite: ${escapeHtml(assignment.dueDate)}` : "Sin fecha límite"}</small></td>
      <td><span class="training-progress"><progress max="100" value="${progress.percent}"></progress>${progress.percent}%</span></td>
      <td>${pill(progress.status)}</td>
      <td><div class="actions"><button class="btn secondary compact" type="button" data-action="open-progress" data-id="${assignment.id}">Ver avance</button><button class="btn secondary compact" type="button" data-action="delete-assignment" data-id="${assignment.id}">Eliminar</button></div></td>
    </tr>`;
  }).join("");
  return `<div class="training-toolbar"><h4>Seguimiento de capacitación</h4>
    <div class="actions"><button class="btn ${ui.assignmentFilter === "all" ? "" : "secondary"} compact" type="button" data-action="assignment-filter" data-filter="all">Todas</button>
      <button class="btn ${ui.assignmentFilter === "progress" ? "" : "secondary"} compact" type="button" data-action="assignment-filter" data-filter="progress">En curso</button>
      <button class="btn ${ui.assignmentFilter === "completed" ? "" : "secondary"} compact" type="button" data-action="assignment-filter" data-filter="completed">Concluidas</button></div>
  </div>
  <div class="training-filters"><input type="search" data-search-input value="${escapeHtml(ui.search)}" placeholder="Buscar empleado o programa" aria-label="Buscar capacitación"></div>
  ${list.length ? contentTable(["Empleado", "Programa", "Fechas", "Avance", "Estado", "Acciones"], rows)
    : empty("No hay capacitaciones en esta vista.")}`;
}

function field(label, name, value = "", options = {}) {
  const required = options.required ? "required" : "";
  const type = options.type || "text";
  const extra = options.extra || "";
  return `<label>${label}<input name="${name}" type="${type}" value="${escapeHtml(value)}" ${required} ${extra}></label>`;
}

function selectField(label, name, values, selected) {
  return `<label>${label}<select name="${name}" required>${values.map(([value, title]) => `<option value="${escapeHtml(value)}" ${String(value) === String(selected) ? "selected" : ""}>${escapeHtml(title)}</option>`).join("")}</select></label>`;
}

function modalShell(title, body, formName, footer = "") {
  return `<div class="training-modal-backdrop" data-backdrop role="presentation">
    <div class="training-modal" role="dialog" aria-modal="true" aria-labelledby="training-modal-title">
      <form data-form="${formName}"><header><h3 id="training-modal-title">${escapeHtml(title)}</h3><button class="btn secondary compact" type="button" data-action="close-modal" aria-label="Cerrar">✕</button></header>
        <div class="training-modal-body">${body}</div>
        <footer>${footer || `${button("Cancelar", "close-modal", "secondary")}<button class="btn" type="submit">Guardar</button>`}</footer>
      </form>
    </div>
  </div>`;
}

function renderModal() {
  const modal = ui.modal;
  if (!modal) return "";
  if (modal.type === "program") {
    const program = training.programs.find((item) => Number(item.id) === Number(modal.id));
    const body = `${field("Nombre del programa *", "title", program?.title, { required: true, extra: 'maxlength="150"' })}
      <label>Descripción<textarea name="description" maxlength="600">${escapeHtml(program?.description || "")}</textarea></label>
      <div class="training-two-cols">${field("Responsable", "owner", program?.owner, { extra: 'maxlength="100"' })}
      ${selectField("Estado", "status", [["Borrador", "Borrador"], ["Activo", "Activo"]], program?.status || "Borrador")}</div>`;
    return modalShell(program ? "Editar programa" : "Nuevo programa", body, "program");
  }
  if (modal.type === "item") {
    const program = selectedProgram();
    const item = program?.[modal.kind]?.find((value) => Number(value.id) === Number(modal.id));
    const moduleOptions = program.modules.map((module) => [module.id, module.title]);
    let body = "";
    if (modal.kind === "modules") {
      body = `${field("Nombre del módulo *", "title", item?.title, { required: true, extra: 'maxlength="150"' })}
        <label>Descripción<textarea name="description" maxlength="600">${escapeHtml(item?.description || "")}</textarea></label>
        <div class="training-two-cols">${field("Duración estimada", "duration", item?.duration, { extra: 'maxlength="40" placeholder="Ej. 45 min"' })}
        ${field("Enlace al material", "resourceUrl", item?.resourceUrl, { type: "url", extra: 'placeholder="https://"' })}</div>`;
    } else if (modal.kind === "exams") {
      body = `${selectField("Módulo *", "moduleId", moduleOptions, item?.moduleId)}
        ${field("Pregunta *", "question", item?.question, { required: true, extra: 'maxlength="300"' })}
        ${[0, 1, 2].map((index) => field(`Opción ${index + 1} *`, `option${index}`, item?.options?.[index], { required: true, extra: 'maxlength="200"' })).join("")}
        <div class="training-two-cols">${selectField("Respuesta correcta", "correct", [[0, "Opción 1"], [1, "Opción 2"], [2, "Opción 3"]], item?.correct ?? 0)}
          ${field("Aprobación mínima (%)", "minimum", item?.minimum ?? 80, { type: "number", required: true, extra: 'min="1" max="100"' })}</div>`;
    } else {
      body = `${selectField("Módulo *", "moduleId", moduleOptions, item?.moduleId)}
        ${field("Nombre de la tarea *", "title", item?.title, { required: true, extra: 'maxlength="150"' })}
        <label>Descripción<textarea name="description" maxlength="600">${escapeHtml(item?.description || "")}</textarea></label>`;
    }
    const names = { modules: "módulo", exams: "pregunta", tasks: "tarea" };
    return modalShell(`${item ? "Editar" : "Agregar"} ${names[modal.kind]}`, body, "item");
  }
  if (modal.type === "assignment") {
    const people = employees().filter((employee) => employee.status === "Activo");
    const programs = activePrograms();
    const body = `${selectField("Empleado *", "employeeId", [["", "Seleccionar empleado"], ...people.map((employee) => [employee.id, `${employee.fullName} · ${employee.company || "Sin empresa"}`])], modal.employeeId)}
      ${selectField("Programa *", "programId", [["", "Seleccionar programa"], ...programs.map((program) => [program.id, program.title])], "")}
      <div class="training-two-cols">${field("Fecha de inicio *", "startDate", localDate(), { type: "date", required: true })}
      ${field("Fecha límite", "dueDate", "", { type: "date" })}</div>`;
    return modalShell("Nueva capacitación", body, "assignment", `${button("Cancelar", "close-modal", "secondary")}<button class="btn" type="submit">Asignar capacitación</button>`);
  }
  if (modal.type === "progress") {
    const assignment = findAssignment(modal.id);
    const program = programFor(assignment);
    if (!assignment || !program) return "";
    const progress = assignmentProgress(assignment, program);
    const body = `<div class="training-detail-head"><div><h4>${escapeHtml(assignment.employeeName || "Empleado")}</h4><p>${escapeHtml(program.title)} · ${progress.done} de ${progress.total} módulos</p></div>${pill(progress.status)}</div>
      <section class="training-progress-section"><h4>Módulos</h4>
        ${program.modules.map((module) => `<label class="training-check-row"><input type="checkbox" data-progress-module="${module.id}" ${assignment.completedModuleIds.includes(Number(module.id)) ? "checked" : ""}><span>${escapeHtml(module.title)}</span></label>`).join("") || "Sin módulos"}
      </section>
      ${program.tasks.length ? `<section class="training-progress-section"><h4>Tareas</h4>${program.tasks.map((task) => `<label class="training-check-row"><input type="checkbox" data-progress-task="${task.id}" ${assignment.completedTaskIds.includes(Number(task.id)) ? "checked" : ""}><span>${escapeHtml(task.title)}<small> · ${escapeHtml(program.modules.find((module) => Number(module.id) === Number(task.moduleId))?.title || "")}</small></span></label>`).join("")}</section>` : ""}
      ${program.exams.length ? `<section class="training-progress-section"><h4>Resultados de evaluación</h4>${program.exams.map((exam) => `<div class="training-score-row"><span>${escapeHtml(exam.question)}<small> · Mínimo ${Number(exam.minimum) || 80}%</small></span><label><span class="sr-only">Resultado (%)</span><input type="number" min="0" max="100" data-progress-exam="${exam.id}" value="${escapeHtml(assignment.examScores[exam.id] ?? "")}" placeholder="%"></label></div>`).join("")}</section>` : ""}`;
    return modalShell("Avance de capacitación", body, "progress", button("Cerrar", "close-modal", "secondary"));
  }
  if (modal.type === "confirm") {
    return modalShell(modal.title, `<p>${escapeHtml(modal.message)}</p>`, "confirm",
      `${button("Cancelar", "close-modal", "secondary")}<button class="btn danger" type="submit">Eliminar</button>`);
  }
  return "";
}

function applySearch() {
  const query = key(ui.search.trim());
  app.querySelectorAll("[data-search-row]").forEach((row) => {
    row.hidden = Boolean(query) && !row.dataset.search.includes(query);
  });
}

function render() {
  if (!selectedProgram()) ui.programId = training.programs[0]?.id || null;
  const modalScroll = app.querySelector(".training-modal")?.scrollTop || 0;
  app.innerHTML = `<main class="training-screen">${renderHeader()}${renderTabs()}
    ${ui.section === "programs" ? renderPrograms() : ui.section === "personnel" ? renderPersonnel() : renderTracking()}
    ${renderModal()}</main>`;
  app.querySelector(".training-modal")?.scrollTo(0, modalScroll);
  applySearch();
}

function openModal(modal) {
  ui.modal = modal;
  render();
  const frame = window.frameElement;
  if (frame) {
    const top = frame.getBoundingClientRect().top + window.parent.scrollY;
    window.parent.scrollTo({ top: Math.max(0, top - 64), behavior: "smooth" });
  }
  requestAnimationFrame(() => app.querySelector(".training-modal input:not([type=hidden]), .training-modal textarea, .training-modal select")?.focus({ preventScroll: true }));
}

function invalidateModule(programId, moduleId, assignmentId = null) {
  training.assignments.filter((assignment) => Number(assignment.programId) === Number(programId)
    && (assignmentId === null || Number(assignment.id) === Number(assignmentId))).forEach((assignment) => {
    assignment.completedModuleIds = assignment.completedModuleIds.filter((id) => Number(id) !== Number(moduleId));
  });
}

function resourceUrl(value) {
  if (!value) return "";
  const url = safeResourceUrl(value);
  if (!url) throw new Error("El enlace debe usar http o https.");
  return url;
}

function saveProgram(form) {
  const values = new FormData(form);
  const title = String(values.get("title") || "").trim();
  if (!title) throw new Error("Escribe el nombre del programa.");
  const id = ui.modal.id;
  const existing = training.programs.find((program) => Number(program.id) === Number(id));
  const properties = {
    title,
    description: String(values.get("description") || "").trim(),
    owner: String(values.get("owner") || "").trim(),
    status: values.get("status") === "Activo" ? "Activo" : "Borrador"
  };
  if (existing) Object.assign(existing, properties);
  else {
    const program = { id: nextId(training.programs), ...properties, modules: [], exams: [], tasks: [] };
    training.programs.push(program);
    ui.programId = program.id;
  }
  ui.modal = null;
  persist();
  notify(existing ? "Programa actualizado." : "Programa creado.");
}

function saveItem(form) {
  const program = selectedProgram();
  const kind = ui.modal.kind;
  const values = new FormData(form);
  const existing = program[kind].find((item) => Number(item.id) === Number(ui.modal.id));
  let properties;
  if (kind === "modules") {
    const title = String(values.get("title") || "").trim();
    if (!title) throw new Error("Escribe el nombre del módulo.");
    properties = {
      title,
      description: String(values.get("description") || "").trim(),
      duration: String(values.get("duration") || "").trim(),
      resourceUrl: resourceUrl(String(values.get("resourceUrl") || "").trim())
    };
  } else if (kind === "exams") {
    const moduleId = Number(values.get("moduleId"));
    if (!program.modules.some((module) => Number(module.id) === moduleId)) throw new Error("Selecciona un módulo válido.");
    properties = {
      moduleId,
      question: String(values.get("question") || "").trim(),
      options: [0, 1, 2].map((index) => String(values.get(`option${index}`) || "").trim()),
      correct: Number(values.get("correct")),
      minimum: Number(values.get("minimum"))
    };
    if (!properties.question || properties.options.some((option) => !option)) throw new Error("Completa la pregunta y sus tres opciones.");
    if (properties.minimum < 1 || properties.minimum > 100) throw new Error("La aprobación mínima debe estar entre 1 y 100%.");
  } else {
    const moduleId = Number(values.get("moduleId"));
    if (!program.modules.some((module) => Number(module.id) === moduleId)) throw new Error("Selecciona un módulo válido.");
    properties = { moduleId, title: String(values.get("title") || "").trim(), description: String(values.get("description") || "").trim() };
    if (!properties.title) throw new Error("Escribe el nombre de la tarea.");
  }
  if (existing) {
    if (kind !== "modules") invalidateModule(program.id, existing.moduleId);
    Object.assign(existing, properties);
  } else {
    program[kind].push({ id: nextId(program[kind]), ...properties });
  }
  if (kind !== "modules") invalidateModule(program.id, properties.moduleId);
  ui.modal = null;
  persist();
  notify("Contenido guardado.");
}

function deleteConfirmed() {
  const { entity, id, kind } = ui.modal;
  if (entity === "program") {
    if (training.assignments.some((assignment) => Number(assignment.programId) === Number(id))) {
      throw new Error("No puedes eliminar un programa con capacitaciones asignadas.");
    }
    training.programs = training.programs.filter((program) => Number(program.id) !== Number(id));
  } else if (entity === "assignment") {
    training.assignments = training.assignments.filter((assignment) => Number(assignment.id) !== Number(id));
  } else {
    const program = selectedProgram();
    program[kind] = program[kind].filter((value) => Number(value.id) !== Number(id));
    if (kind === "modules") {
      const deletedExamIds = program.exams.filter((value) => Number(value.moduleId) === Number(id)).map((value) => value.id);
      const deletedTaskIds = program.tasks.filter((value) => Number(value.moduleId) === Number(id)).map((value) => value.id);
      program.exams = program.exams.filter((value) => Number(value.moduleId) !== Number(id));
      program.tasks = program.tasks.filter((value) => Number(value.moduleId) !== Number(id));
      training.assignments.forEach((assignment) => {
        deletedExamIds.forEach((examId) => { delete assignment.examScores[examId]; });
        assignment.completedTaskIds = assignment.completedTaskIds.filter((taskId) => !deletedTaskIds.some((value) => Number(value) === Number(taskId)));
      });
      invalidateModule(program.id, id);
    } else if (kind === "exams") {
      training.assignments.forEach((assignment) => { delete assignment.examScores[id]; });
    } else {
      training.assignments.forEach((assignment) => {
        assignment.completedTaskIds = assignment.completedTaskIds.filter((taskId) => Number(taskId) !== Number(id));
      });
    }
  }
  ui.modal = null;
  persist();
  notify("Registro eliminado.");
}

app.addEventListener("click", (event) => {
  if (event.target.matches("[data-backdrop]")) { ui.modal = null; render(); return; }
  const target = event.target.closest("[data-action]");
  if (!target) return;
  const { action, id, kind } = target.dataset;
  if (action === "section") { ui.section = target.dataset.section; ui.search = ""; render(); }
  else if (action === "program-tab") { ui.programTab = target.dataset.tab; render(); }
  else if (action === "select-program") { ui.programId = Number(id); render(); }
  else if (action === "new-program") openModal({ type: "program" });
  else if (action === "edit-program") openModal({ type: "program", id: Number(id || ui.programId) });
  else if (action === "new-item") openModal({ type: "item", kind });
  else if (action === "edit-item") openModal({ type: "item", kind, id: Number(id) });
  else if (action === "new-assignment" || action === "assign-person") {
    if (!activePrograms().length) { notify("Crea y activa un programa con módulos antes de asignarlo.", true); return; }
    if (!employees().some((employee) => employee.status === "Activo")) { notify("No hay empleados activos para asignar.", true); return; }
    openModal({ type: "assignment", employeeId: action === "assign-person" ? Number(id) : null });
  }
  else if (action === "open-progress") openModal({ type: "progress", id: Number(id) });
  else if (action === "assignment-filter") { ui.assignmentFilter = target.dataset.filter; render(); }
  else if (action === "close-modal") { ui.modal = null; render(); }
  else if (action === "delete-program") openModal({ type: "confirm", entity: "program", id: ui.programId, title: "Eliminar programa", message: "Se eliminarán sus módulos, preguntas y tareas. Esta acción no se puede deshacer." });
  else if (action === "delete-item") openModal({ type: "confirm", entity: "item", kind, id: Number(id), title: "Eliminar contenido", message: "El contenido seleccionado se eliminará del programa." });
  else if (action === "delete-assignment") openModal({ type: "confirm", entity: "assignment", id: Number(id), title: "Eliminar capacitación", message: "Se eliminará la asignación y el avance registrado." });
});

app.addEventListener("submit", (event) => {
  const form = event.target.closest("[data-form]");
  if (!form) return;
  event.preventDefault();
  try {
    if (form.dataset.form === "program") saveProgram(form);
    else if (form.dataset.form === "item") saveItem(form);
    else if (form.dataset.form === "assignment") {
      const values = new FormData(form);
      assignProgram(training, employees(), values.get("employeeId"), values.get("programId"), values.get("startDate"), values.get("dueDate"));
      ui.modal = null;
      ui.section = "tracking";
      persist();
      notify("Capacitación asignada.");
    } else if (form.dataset.form === "confirm") deleteConfirmed();
  } catch (error) {
    notify(error.message || "No se pudo guardar el registro.", true);
  }
});

app.addEventListener("input", (event) => {
  if (event.target.matches("[data-search-input]")) {
    ui.search = event.target.value;
    applySearch();
  }
});

app.addEventListener("change", (event) => {
  const target = event.target;
  if (target.matches("[data-company-filter]")) { ui.company = target.value; render(); return; }
  const assignment = findAssignment(ui.modal?.id);
  const program = assignment && programFor(assignment);
  if (!assignment || !program) return;
  if (target.matches("[data-progress-task]")) {
    const id = Number(target.dataset.progressTask);
    assignment.completedTaskIds = target.checked
      ? [...new Set([...assignment.completedTaskIds, id])]
      : assignment.completedTaskIds.filter((value) => Number(value) !== id);
    if (!target.checked) invalidateModule(program.id, program.tasks.find((task) => Number(task.id) === id)?.moduleId, assignment.id);
    persist();
  } else if (target.matches("[data-progress-exam]")) {
    const id = Number(target.dataset.progressExam);
    const exam = program.exams.find((item) => Number(item.id) === id);
    if (!exam) return;
    if (target.value === "") delete assignment.examScores[id];
    else assignment.examScores[id] = Math.max(0, Math.min(100, Number(target.value)));
    if (target.value === "" || Number(assignment.examScores[id]) < Number(exam.minimum)) {
      invalidateModule(program.id, exam.moduleId, assignment.id);
    }
    persist();
  } else if (target.matches("[data-progress-module]")) {
    const moduleId = Number(target.dataset.progressModule);
    try { toggleModuleCompletion(training, assignment.id, moduleId); persist(); }
    catch (error) { target.checked = !target.checked; notify(error.message, true); }
  }
});

document.addEventListener("keydown", (event) => {
  if (event.key === "Escape" && ui.modal) { ui.modal = null; render(); }
});

window.addEventListener("storage", (event) => {
  if (event.key === HR_STORAGE_KEY) render();
});

render();
