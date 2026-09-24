import assert from "node:assert/strict";
import { test } from "node:test";
import {
  detectedPersonnel,
  HR_PERSONNEL_STORAGE_KEY,
  loadPersonnelCatalog,
  loadPersonnelDetections,
  PERSONNEL_DETECTIONS_STORAGE_KEY
} from "../../public/modules/security/personnel-analytics.js";

const employees = [
  { number: "EMP-001", fullName: "Ana López", position: "Vigilante" },
  { number: "EMP-002", fullName: "Luis Pérez", position: "Supervisor" }
];

test("reads the personnel catalog and detection events from their own stores", () => {
  const saved = new Map([
    [HR_PERSONNEL_STORAGE_KEY, JSON.stringify({ data: { employees } })],
    [PERSONNEL_DETECTIONS_STORAGE_KEY, JSON.stringify([{ branchId: 7, employeeNumber: "EMP-001" }])]
  ]);
  const storage = { getItem: (key) => saved.get(key) ?? null };

  assert.deepEqual(loadPersonnelCatalog(storage), employees);
  assert.equal(loadPersonnelDetections(storage).length, 1);
  assert.deepEqual(loadPersonnelCatalog({ getItem: () => "invalid" }, employees), employees);
  assert.deepEqual(loadPersonnelDetections({ getItem: () => "invalid" }), []);
});

test("lists only registered personnel detected at the chosen branch and Mexico City date", () => {
  const events = [
    { branchId: 7, employeeNumber: "EMP-001", detectedAt: "2026-09-24T08:00:00-06:00", cameraName: "Entrada" },
    { branchId: 7, employeeNumber: "EMP-001", detectedAt: "2026-09-24T10:00:00-06:00", cameraName: "Pasillo" },
    { branchId: 7, employeeNumber: "EMP-002", detectedAt: "2026-09-25T04:00:00Z", cameraName: "Acceso" },
    { branchId: 8, employeeNumber: "EMP-002", detectedAt: "2026-09-24T11:00:00-06:00" },
    { branchId: 7, employeeNumber: "DESCONOCIDO", detectedAt: "2026-09-24T11:00:00-06:00" },
    { branchId: 7, employeeNumber: "EMP-002", detectedAt: "invalid" }
  ];

  const rows = detectedPersonnel(events, employees, 7, "2026-09-24");
  assert.equal(rows.length, 2);
  assert.equal(rows[0].employee.number, "EMP-002");
  assert.equal(rows[1].employee.number, "EMP-001");
  assert.equal(rows[1].count, 2);
  assert.equal(rows[1].cameraName, "Pasillo");
  assert.deepEqual(detectedPersonnel(events, employees, "", "2026-09-24"), []);
  assert.deepEqual(detectedPersonnel(events, employees, 7, "2026-09-23"), []);
});
