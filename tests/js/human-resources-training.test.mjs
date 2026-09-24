import assert from "node:assert/strict";
import { test } from "node:test";
import {
  assignmentProgress,
  assignProgram,
  normalizeTrainingData,
  toggleModuleCompletion
} from "../../public/modules/human-resources/training-data.js";

function setup() {
  const data = normalizeTrainingData({ programs: [{
    id: 1,
    title: "Inducción",
    status: "Activo",
    modules: [{ id: 1, title: "Seguridad" }, { id: 2, title: "Procesos" }],
    exams: [],
    tasks: []
  }] });
  const employees = [{ id: 5, fullName: "Ana López", status: "Activo", company: "Empresa A", branch: "Centro" }];
  return { data, employees };
}

test("normalizes missing training collections", () => {
  assert.deepEqual(normalizeTrainingData(null), { programs: [], assignments: [] });
  assert.deepEqual(normalizeTrainingData({ programs: [{ id: 1 }], assignments: [{ id: 2 }] }).programs[0].modules, []);
});

test("assigns an active program to an active employee and tracks completion", () => {
  const { data, employees } = setup();
  const assignment = assignProgram(data, employees, 5, 1, "2026-09-23", "2026-10-01");
  assert.equal(assignment.employeeName, "Ana López");
  assert.deepEqual(assignmentProgress(assignment, data.programs[0]), { done: 0, total: 2, percent: 0, status: "En curso" });
  toggleModuleCompletion(data, assignment.id, 1);
  assert.equal(assignmentProgress(assignment, data.programs[0]).percent, 50);
  toggleModuleCompletion(data, assignment.id, 2);
  assert.equal(assignmentProgress(assignment, data.programs[0]).status, "Concluida");
  toggleModuleCompletion(data, assignment.id, 1);
  assert.equal(assignmentProgress(assignment, data.programs[0]).status, "En curso");
});

test("rejects duplicate active assignments, inactive employees, and invalid dates", () => {
  const { data, employees } = setup();
  assignProgram(data, employees, 5, 1, "2026-09-23", "");
  assert.throws(() => assignProgram(data, employees, 5, 1, "2026-09-23", ""), /ya tiene/);
  assert.throws(() => assignProgram(data, employees, 5, 1, "2026-09-23", "2026-09-22"), /fecha límite/);
  assert.throws(() => assignProgram(data, [{ ...employees[0], status: "Baja" }], 5, 1, "2026-09-23", ""), /activo/);
});

test("requires a valid active program with modules", () => {
  const { data, employees } = setup();
  data.programs[0].status = "Borrador";
  assert.throws(() => assignProgram(data, employees, 5, 1, "2026-09-23", ""), /programa activo/);
  data.programs[0].status = "Activo";
  data.programs[0].modules = [];
  assert.throws(() => assignProgram(data, employees, 5, 1, "2026-09-23", ""), /módulo/);
});

test("a module stays open until its task and exam are completed", () => {
  const { data, employees } = setup();
  data.programs[0].tasks = [{ id: 7, moduleId: 1, title: "Revisar protocolo" }];
  data.programs[0].exams = [{ id: 8, moduleId: 1, minimum: 80 }];
  const assignment = assignProgram(data, employees, 5, 1, "2026-09-23", "");
  assert.throws(() => toggleModuleCompletion(data, assignment.id, 1), /tareas/);
  assignment.completedTaskIds.push(7);
  assignment.examScores[8] = 70;
  assert.throws(() => toggleModuleCompletion(data, assignment.id, 1), /calificación/);
  assignment.examScores[8] = 85;
  assert.equal(toggleModuleCompletion(data, assignment.id, 1).percent, 50);
});
