import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { test } from "node:test";
import vm from "node:vm";
import jsQR from "jsqr";
import * as credentials from "../../public/hr-suite/assets/js/employee-credential.js";
import { createInitialData, menuItems, roles, routeTitles, screenGroups } from "../../public/hr-suite/assets/js/data.js";

function decodeEmployeeQr(employee) {
  const qr = credentials.employeeQrCode(employee);
  const scale = 8;
  const margin = 4;
  const width = (qr.getModuleCount() + margin * 2) * scale;
  const pixels = new Uint8ClampedArray(width * width * 4);
  for (let y = 0; y < width; y++) {
    for (let x = 0; x < width; x++) {
      const row = Math.floor(y / scale) - margin;
      const col = Math.floor(x / scale) - margin;
      const dark = row >= 0 && col >= 0 && row < qr.getModuleCount() && col < qr.getModuleCount() && qr.isDark(row, col);
      const offset = (y * width + x) * 4;
      pixels[offset] = pixels[offset + 1] = pixels[offset + 2] = dark ? 0 : 255;
      pixels[offset + 3] = 255;
    }
  }
  return jsQR(pixels, width, width)?.data;
}

function application(saved = null) {
  const storage = new Map(saved ? [["hr-suite-mexico-state-v1", JSON.stringify(saved)]] : []);
  const messages = [];
  const context = vm.createContext({
    ...credentials, createInitialData, menuItems, roles, routeTitles, screenGroups,
    document: { querySelector: () => null, addEventListener: () => {} },
    window: { HR_SUITE_CONFIG: { embedded: true, route: "employees" }, scrollTo: () => {} },
    localStorage: { getItem: (key) => storage.get(key) || null, setItem: (key, value) => storage.set(key, value) },
    requestAnimationFrame: () => {},
    Intl, Date, URL, FormData
  });
  const source = readFileSync(new URL("../../public/hr-suite/assets/js/app.js", import.meta.url), "utf8")
    .replace(/^\uFEFF?import .*;\r?$/gm, "");
  // Exercise the real application handlers without starting its DOM renderer.
  vm.runInContext(source.slice(0, source.lastIndexOf("\nrender();")), context);
  context.render = () => {};
  context.toast = (message, type) => messages.push({ message, type });
  return { context, storage, messages, state: vm.runInContext("state", context) };
}

function employeeForm(number = "EMP-00026") {
  const values = {
    number, firstName: "Prueba", lastName: "Empleado", secondLastName: "QR",
    curp: "GOLM920415MDFNRR09", rfc: "GOLM920415A1B", clabe: "012345678901234567",
    company: "Empresa de prueba", branch: "Centro", position: "Analista", department: "RH",
    status: "Activo", email: "prueba@example.test", hireDate: "2026-09-28"
  };
  const form = new FormData();
  for (const [key, value] of Object.entries(values)) form.set(key, value);
  return form;
}

test("QRs scan to only the employee number for existing and new employees", () => {
  for (const number of ["EMP-001", "EMP-00026", "EMP-999999"]) {
    const employee = { number, fullName: "Persona", rfc: "privado", grossSalary: 12345, clabe: "privado" };
    assert.equal(decodeEmployeeQr(employee), number);
    assert.match(credentials.employeeQrImage(employee), /^data:image\/svg\+xml/);
  }
  assert.notEqual(credentials.employeeQrImage({ number: "EMP-001" }), credentials.employeeQrImage({ number: "EMP-002" }));
  assert.throws(() => credentials.employeeQrValue({ number: '<script>alert(1)</script>' }), /válido/);
});

test("credential initialization is stable and survives serialization", () => {
  const employee = credentials.initializeEmployeeCredential({ number: "EMP-00026" });
  const reloaded = JSON.parse(JSON.stringify(employee));
  assert.equal(reloaded.qrValue, "EMP-00026");
  assert.equal(credentials.employeeQrImage(employee), credentials.employeeQrImage(reloaded));
  reloaded.number = "EMP-00027";
  credentials.initializeEmployeeCredential(reloaded);
  assert.equal(decodeEmployeeQr(reloaded), "EMP-00027");
});

test("credential data excludes financial and government identifiers", () => {
  const data = credentials.employeeCredentialData({ number: "EMP-001", fullName: "Persona", rfc: "privado", curp: "privado", clabe: "privado", grossSalary: 10000 });
  assert.equal(data.fullName, "Persona");
  assert.equal(data.photo, "");
  for (const key of ["rfc", "curp", "clabe", "grossSalary"]) assert.equal(key in data, false);
});

test("profile photos accept local raster images and reject unsafe sources", () => {
  assert.equal(credentials.employeeProfilePhoto({ profilePhoto: "data:image/jpeg;base64,YWJj" }), "data:image/jpeg;base64,YWJj");
  for (const profilePhoto of ["javascript:alert(1)", "https://example.test/photo.png", "data:image/svg+xml;base64,YWJj"]) {
    assert.equal(credentials.employeeProfilePhoto({ profilePhoto }), "");
  }
  assert.throws(() => credentials.validateEmployeePhoto({ size: 10, type: "image/svg+xml" }), /JPG/);
  assert.throws(() => credentials.validateEmployeePhoto({ size: 6 * 1024 * 1024, type: "image/jpeg" }), /5 MB/);
});

test("creating an employee automatically saves a readable QR and profile photo", async () => {
  const { context, storage, state } = application();
  context.readEmployeeProfilePhoto = async () => "data:image/jpeg;base64,YWJj";
  await context.createEmployee(employeeForm());
  const employee = state.data.employees.at(-1);
  assert.equal(employee.qrValue, "EMP-00026");
  assert.equal(decodeEmployeeQr(employee), employee.number);
  const saved = JSON.parse(storage.get("hr-suite-mexico-state-v1"));
  assert.equal(saved.data.employees.at(-1).profilePhoto, "data:image/jpeg;base64,YWJj");
  const reloaded = application(saved).state.data.employees.at(-1);
  assert.equal(reloaded.qrValue, employee.qrValue);
  assert.equal(credentials.employeeQrImage(reloaded), credentials.employeeQrImage(employee));
});

test("storage failure does not report a saved employee or leave a duplicate", async () => {
  const { context, state, messages } = application();
  const count = state.data.employees.length;
  context.localStorage.setItem = () => { throw new Error("Quota exceeded"); };
  await context.createEmployee(employeeForm());
  assert.equal(state.data.employees.length, count);
  assert.equal(messages.at(-1).type, "error");
});

test("candidate conversion also creates and saves the employee QR", () => {
  const { context, state, storage } = application();
  state.data.contractCandidateEmployees.push({ id: "candidate-999", number: "CAND-999", fullName: "Candidato de prueba", timeline: [], documents: [] });
  state.data.contracts.push({ id: 999, employeeId: "candidate-999", folio: "CNT-2026-999", startDate: "2026-09-28", type: "Tiempo indeterminado" });
  context.confirmSignContract({ id: 999 });
  const employee = state.data.employees[0];
  assert.equal(employee.qrValue, "EMP-00026");
  assert.equal(decodeEmployeeQr(employee), employee.number);
  assert.equal(JSON.parse(storage.get("hr-suite-mexico-state-v1")).data.employees[0].qrValue, employee.number);
});

test("saving a credential photo preserves the employee QR", async () => {
  const { context, state, storage } = application();
  const employee = state.data.employees[0];
  const before = credentials.employeeQrImage(employee);
  state.ui.modal = { type: "employee-credential", id: employee.id };
  const photoForm = new FormData();
  photoForm.set("employeeId", employee.id);
  context.FormData = class { constructor(form) { return form.data; } };
  context.readEmployeeProfilePhoto = async () => "data:image/jpeg;base64,YWJj";
  const button = { disabled: false };
  await context.saveEmployeeCredentialPhoto({ data: photoForm, querySelector: () => button });
  assert.equal(JSON.parse(storage.get("hr-suite-mexico-state-v1")).data.employees[0].profilePhoto, "data:image/jpeg;base64,YWJj");
  assert.equal(credentials.employeeQrImage(employee), before);
  assert.equal(button.disabled, false);
});

test("credential rendering uses a profile icon without a photo and escapes employee data", () => {
  const { context } = application();
  const employee = { number: "EMP-001", fullName: '<script>Empleado</script>', company: "Empresa", status: "Activo" };
  const html = context.renderEmployeeCredential(employee);
  assert.match(html, /employee-profile-placeholder/);
  assert.doesNotMatch(html, /class="employee-profile-photo"/);
  assert.match(html, /&lt;script&gt;Empleado&lt;\/script&gt;/);
  assert.match(context.renderEmployeeCredential({ ...employee, profilePhoto: "data:image/jpeg;base64,YWJj" }), /class="employee-profile-photo"/);
});

test("portrait credentials retain employee details and use physical ID-1 print dimensions", () => {
  const { context, state } = application();
  const employee = state.data.employees[0];
  const html = context.renderEmployeeCredential(employee);
  assert.match(html, /data-card-format="ID-1"/);
  assert.match(html, /employee-credential-portrait/);
  assert.match(html, /employee-credential-qr/);
  for (const value of [employee.fullName, employee.number, employee.company, employee.department, employee.branch, employee.email]) {
    assert.ok(html.includes(value));
  }
  const modal = context.renderEmployeeIdentificationModal({ type: "employee-credential", id: employee.id });
  assert.match(modal, /53\.98 × 85\.60 mm/);
  const css = readFileSync(new URL("../../public/hr-suite/assets/css/styles.css", import.meta.url), "utf8");
  assert.match(css, /\.employee-credential\s*\{[^}]*width:\s*53\.98mm;[^}]*height:\s*85\.60mm;/);
  const printCss = css.slice(css.indexOf("@media print"));
  assert.match(printCss, /@page employee-credential\s*\{\s*size:\s*53\.98mm 85\.60mm;\s*margin:\s*0;/);
  assert.match(printCss, /\.employee-credential\s*\{[^}]*zoom:\s*1;/);
  assert.match(printCss, /\.employee-credential-format\s*\{\s*display:\s*none !important;/);
  assert.doesNotMatch(printCss, /110mm/);
});

test("long credential text shrinks without changing its content or exceeding its bounds", () => {
  const { context } = application();
  const text = "Nombre completo de un empleado con varios apellidos";
  const element = {
    style: { fontSize: "6px" }, textContent: text,
    clientHeight: 20, clientWidth: 80,
    get scrollHeight() { return (parseFloat(this.style.fontSize) || 12) > 9 ? 30 : 20; },
    get scrollWidth() { return (parseFloat(this.style.fontSize) || 12) > 8 ? 100 : 80; }
  };
  context.document.querySelectorAll = () => [element];
  context.window.getComputedStyle = () => ({ fontSize: "12px" });
  context.fitEmployeeCredentialText();
  assert.equal(element.style.fontSize, "8px");
  assert.equal(element.textContent, text);
  assert.ok(element.scrollHeight <= element.clientHeight && element.scrollWidth <= element.clientWidth);
  context.window.getComputedStyle = () => ({ fontSize: "7.333px" });
  element.clientHeight = 1;
  context.fitEmployeeCredentialText();
  assert.equal(element.style.fontSize, "5px");
});

test("printing waits for card images and restores the normal screen afterward", async () => {
  const { context, state } = application();
  state.ui.modal = { type: "employee-credential", id: state.data.employees[0].id };
  const events = [];
  const classes = new Set();
  context.document.querySelectorAll = () => [
    { decode: async () => { events.push("photo"); } },
    { decode: async () => { events.push("qr"); } }
  ];
  context.document.body = { classList: { add: (value) => classes.add(value), remove: (value) => classes.delete(value) } };
  context.window.print = () => {
    assert.deepEqual(events, ["photo", "qr"]);
    assert.ok(classes.has("printing-employee-credential"));
    events.push("print");
  };
  await context.printEmployeeCredential();
  assert.deepEqual(events, ["photo", "qr", "print"]);
  assert.equal(classes.size, 0);
  context.window.print = () => { throw new Error("Printer unavailable"); };
  await assert.rejects(context.printEmployeeCredential(), /Printer unavailable/);
  assert.equal(classes.size, 0);
  state.ui.modal = { type: "employee-qr" };
  await context.printEmployeeCredential();
});
