import { createInitialData } from "../../hr-suite/assets/js/data.js";

export const HR_PERSONNEL_STORAGE_KEY = "hr-suite-mexico-state-v1";
export const PERSONNEL_DETECTIONS_STORAGE_KEY = "security-personnel-detections-v1";

const dateFormatter = new Intl.DateTimeFormat("en-US", {
  timeZone: "America/Mexico_City", year: "numeric", month: "2-digit", day: "2-digit"
});
const timeFormatter = new Intl.DateTimeFormat("es-MX", {
  timeZone: "America/Mexico_City", hour: "2-digit", minute: "2-digit", hour12: false
});

function dateKey(date) {
  const parts = Object.fromEntries(dateFormatter.formatToParts(date).map(({ type, value }) => [type, value]));
  return `${parts.year}-${parts.month}-${parts.day}`;
}

export function loadPersonnelCatalog(storage, fallbackEmployees = []) {
  try {
    const saved = JSON.parse(storage.getItem(HR_PERSONNEL_STORAGE_KEY));
    if (Array.isArray(saved?.data?.employees)) return saved.data.employees;
  } catch {
    // Use the same initial catalog as Personal y capacitación when no saved state exists.
  }
  return fallbackEmployees;
}

export function loadPersonnelDetections(storage) {
  try {
    const saved = JSON.parse(storage.getItem(PERSONNEL_DETECTIONS_STORAGE_KEY));
    return Array.isArray(saved) ? saved : [];
  } catch {
    return [];
  }
}

export function detectedPersonnel(events, employees, branchId, analyticsDate) {
  const branch = Number(branchId);
  if (!Number.isInteger(branch) || branch < 1 || !analyticsDate) return [];

  const personnelByNumber = new Map(employees
    .filter((employee) => employee?.number)
    .map((employee) => [String(employee.number).trim().toUpperCase(), employee]));
  const rows = new Map();

  // Each event identifies a catalog employee by number and the branch where the camera saw them.
  for (const event of events) {
    if (!event || Number(event.branchId) !== branch) continue;
    const employeeNumber = String(event.employeeNumber ?? "").trim().toUpperCase();
    const employee = personnelByNumber.get(employeeNumber);
    const detectedAt = new Date(event.detectedAt);
    if (!employee || Number.isNaN(detectedAt.getTime()) || dateKey(detectedAt) !== analyticsDate) continue;

    const previous = rows.get(employeeNumber);
    if (previous) {
      previous.count += 1;
      if (detectedAt > previous.detectedAt) {
        previous.detectedAt = detectedAt;
        previous.cameraName = event.cameraName || "Sin cámara";
      }
    } else {
      rows.set(employeeNumber, {
        employee,
        detectedAt,
        cameraName: event.cameraName || "Sin cámara",
        count: 1
      });
    }
  }

  return [...rows.values()].sort((a, b) => b.detectedAt - a.detectedAt);
}

function renderPersonnelAnalytics() {
  const panel = document.querySelector("[data-security-personnel-analytics]");
  if (!panel) return;

  const count = panel.querySelector("[data-security-personnel-count]");
  const empty = panel.querySelector("[data-security-personnel-empty]");
  const list = panel.querySelector("[data-security-personnel-list]");
  const body = panel.querySelector("[data-security-personnel-rows]");
  if (!panel.dataset.branchId) return;

  const employees = loadPersonnelCatalog(localStorage, createInitialData().employees);
  const events = loadPersonnelDetections(localStorage);
  const rows = detectedPersonnel(events, employees, panel.dataset.branchId, panel.dataset.analyticsDate);
  count.textContent = String(rows.length);
  empty.hidden = rows.length > 0;
  list.hidden = rows.length === 0;
  if (!rows.length) {
    empty.textContent = "Sin detecciones confirmadas de personal registrado para esta sucursal y fecha.";
    body.replaceChildren();
    return;
  }

  body.replaceChildren(...rows.map(({ employee, detectedAt, cameraName, count: detections }) => {
    const row = document.createElement("tr");
    const person = document.createElement("td");
    const name = document.createElement("strong");
    name.textContent = employee.fullName || employee.number;
    const number = document.createElement("small");
    number.textContent = employee.number;
    person.append(name, number);
    row.append(person);
    for (const value of [employee.position || "Sin puesto", cameraName, timeFormatter.format(detectedAt), String(detections)]) {
      const cell = document.createElement("td");
      cell.textContent = value;
      row.append(cell);
    }
    return row;
  }));
}

if (typeof document !== "undefined") {
  renderPersonnelAnalytics();
  window.addEventListener("storage", (event) => {
    if ([HR_PERSONNEL_STORAGE_KEY, PERSONNEL_DETECTIONS_STORAGE_KEY].includes(event.key)) renderPersonnelAnalytics();
  });
  window.addEventListener("security:personnel-detections-updated", renderPersonnelAnalytics);
}
