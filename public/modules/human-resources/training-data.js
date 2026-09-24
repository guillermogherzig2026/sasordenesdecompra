export const TRAINING_STORAGE_KEY = "hr-suite-mexico-training-v1";

export function normalizeTrainingData(value) {
  return {
    programs: Array.isArray(value?.programs) ? value.programs.map((program) => ({
      ...program,
      modules: Array.isArray(program.modules) ? program.modules : [],
      exams: Array.isArray(program.exams) ? program.exams : [],
      tasks: Array.isArray(program.tasks) ? program.tasks : []
    })) : [],
    assignments: Array.isArray(value?.assignments) ? value.assignments.map((assignment) => ({
      ...assignment,
      completedModuleIds: Array.isArray(assignment.completedModuleIds) ? assignment.completedModuleIds : [],
      completedTaskIds: Array.isArray(assignment.completedTaskIds) ? assignment.completedTaskIds : [],
      examScores: assignment.examScores && typeof assignment.examScores === "object" ? assignment.examScores : {}
    })) : []
  };
}

export function loadTrainingData(storage) {
  try {
    return normalizeTrainingData(JSON.parse(storage.getItem(TRAINING_STORAGE_KEY)));
  } catch {
    return normalizeTrainingData(null);
  }
}

export function saveTrainingData(storage, data) {
  storage.setItem(TRAINING_STORAGE_KEY, JSON.stringify(data));
}

export function nextId(items) {
  return Math.max(0, ...items.map((item) => Number(item.id) || 0)) + 1;
}

export function assignmentProgress(assignment, program) {
  const moduleIds = new Set((program?.modules || []).map((module) => Number(module.id)));
  const completed = new Set(assignment?.completedModuleIds || []);
  const done = [...moduleIds].filter((id) => completed.has(id)).length;
  return {
    done,
    total: moduleIds.size,
    percent: moduleIds.size ? Math.round((done / moduleIds.size) * 100) : 0,
    status: moduleIds.size && done === moduleIds.size ? "Concluida" : "En curso"
  };
}

export function assignProgram(data, employees, employeeId, programId, startDate, dueDate) {
  const employee = employees.find((item) => Number(item.id) === Number(employeeId));
  const program = data.programs.find((item) => Number(item.id) === Number(programId));
  if (!employee || employee.status !== "Activo") throw new Error("Selecciona un empleado activo.");
  if (!program || program.status !== "Activo" || !program.modules.length) {
    throw new Error("Selecciona un programa activo con al menos un módulo.");
  }
  if (!startDate) throw new Error("Indica la fecha de inicio.");
  if (dueDate && dueDate < startDate) throw new Error("La fecha límite debe ser posterior al inicio.");
  if (data.assignments.some((assignment) => Number(assignment.employeeId) === Number(employeeId)
    && Number(assignment.programId) === Number(programId)
    && assignmentProgress(assignment, program).status === "En curso")) {
    throw new Error("El empleado ya tiene este programa en curso.");
  }
  const assignment = {
    id: nextId(data.assignments),
    employeeId: Number(employeeId),
    employeeName: employee.fullName,
    company: employee.company || "",
    branch: employee.branch || "",
    programId: Number(programId),
    startDate,
    dueDate: dueDate || "",
    completedModuleIds: [],
    completedTaskIds: [],
    examScores: {}
  };
  data.assignments.unshift(assignment);
  return assignment;
}

export function toggleModuleCompletion(data, assignmentId, moduleId) {
  const assignment = data.assignments.find((item) => Number(item.id) === Number(assignmentId));
  const program = data.programs.find((item) => Number(item.id) === Number(assignment?.programId));
  if (!assignment || !program?.modules.some((module) => Number(module.id) === Number(moduleId))) {
    throw new Error("No se encontró el módulo asignado.");
  }
  const id = Number(moduleId);
  if (!assignment.completedModuleIds.includes(id)) {
    const hasPendingTask = program.tasks.some((task) => Number(task.moduleId) === id
      && !assignment.completedTaskIds.includes(Number(task.id)));
    const hasFailedExam = program.exams.some((exam) => Number(exam.moduleId) === id
      && !(Number(assignment.examScores[exam.id]) >= Number(exam.minimum)));
    if (hasPendingTask || hasFailedExam) {
      throw new Error("Completa las tareas y alcanza la calificación mínima antes de cerrar el módulo.");
    }
  }
  assignment.completedModuleIds = assignment.completedModuleIds.includes(id)
    ? assignment.completedModuleIds.filter((value) => value !== id)
    : [...assignment.completedModuleIds, id];
  return assignmentProgress(assignment, program);
}
