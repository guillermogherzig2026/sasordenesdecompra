import { qrcode } from "./vendor/qrcode-generator.mjs";

const qrCodes = new Map();

export function employeeQrValue(employee) {
  const number = String(employee?.number || "").trim();
  if (!/^EMP-\d{1,12}$/.test(number)) throw new Error("El empleado no tiene un número válido para su QR.");
  return number;
}

export function employeeQrCode(employee) {
  const value = employeeQrValue(employee);
  if (!qrCodes.has(value)) {
    const code = qrcode(0, "M");
    code.addData(value, "Alphanumeric");
    code.make();
    qrCodes.set(value, code);
  }
  return qrCodes.get(value);
}

export function employeeQrImage(employee) {
  const svg = employeeQrCode(employee).createSvgTag(8, 32);
  return `data:image/svg+xml;charset=utf-8,${encodeURIComponent(svg)}`;
}

export function initializeEmployeeCredential(employee) {
  employee.qrValue = employeeQrValue(employee);
  employeeQrCode(employee);
  return employee;
}

export function employeeProfilePhoto(employee) {
  const photo = String(employee?.profilePhoto || "");
  return /^data:image\/(jpeg|png|webp);base64,[A-Za-z0-9+/=]+$/.test(photo) ? photo : "";
}

export function validateEmployeePhoto(file) {
  if (!file || !file.size) return;
  if (!["image/jpeg", "image/png", "image/webp"].includes(file.type)) {
    throw new Error("Selecciona una foto JPG, PNG o WebP.");
  }
  if (file.size > 5 * 1024 * 1024) throw new Error("La foto no debe superar 5 MB.");
}

export function employeeCredentialData(employee) {
  return {
    number: employeeQrValue(employee),
    fullName: employee.fullName || "",
    company: employee.company || "",
    position: employee.position || "",
    department: employee.department || "",
    branch: employee.branch || "",
    hireDate: employee.hireDate || "",
    email: employee.email || "",
    status: employee.status || "",
    photo: employeeProfilePhoto(employee)
  };
}
