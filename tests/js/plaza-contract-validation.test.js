import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { runInNewContext } from "node:vm";
import test from "node:test";

const source = readFileSync(new URL("../../public/modules/rentas360-plazas/app.js", import.meta.url), "utf8");

function loadFunction(name, nextName, context = {}) {
  const start = source.indexOf(`function ${name}(`);
  const end = source.indexOf(`\nfunction ${nextName}(`, start);
  assert.ok(start >= 0 && end > start, `${name} must exist`);
  return runInNewContext(`${source.slice(start, end)}; ${name}`, context);
}

const canValidateUnitContractTerm = loadFunction(
  "canValidateUnitContractTerm",
  "bindPropertyUnitContractValidationActions",
  { isUnitAvailable: () => false }
);

test("expired contracts remain eligible for manual validation", () => {
  const unit = { id: "unit-1", contractStart: "2025-06-01", contractEnd: "2026-08-30" };
  assert.equal(canValidateUnitContractTerm(unit), true);

  const isContractTermValidated = loadFunction(
    "isContractTermValidated",
    "contractTermValidationLabel",
    { isUnitAvailable: () => false, isContractExpired: () => true }
  );
  assert.equal(isContractTermValidated(unit), false);
  unit.contractTermValidated = true;
  assert.equal(isContractTermValidated(unit), true);
});

test("an unvalidated contract checkbox is editable for the superadministrator", () => {
  const markup = loadFunction(
    "propertyUnitContractValidationCellMarkup",
    "propertyUnitContractValidationActionsMarkup",
    {
      canValidateUnitContractTerm,
      isContractTermValidated: () => false,
      canManageValidation: () => true
    }
  )({ id: "unit-1", unit: "Local L-01", contractStart: "2025-06-01", contractEnd: "2026-08-30" });

  assert.match(markup, /data-unit-contract-validation-check/);
  assert.doesNotMatch(markup, /disabled/);
});

test("saving a selected expired contract persists its validation", () => {
  const unit = { id: "unit-1", contractStart: "2025-06-01", contractEnd: "2026-08-30" };
  const checkbox = { dataset: { unitId: unit.id }, checked: true, disabled: false, classList: { add() {} } };
  const panel = { querySelectorAll: () => [checkbox] };
  const effects = [];
  const confirm = loadFunction(
    "confirmPropertyUnitContractValidation",
    "editPropertyUnitContractValidation",
    {
      canManageValidation: () => true,
      canValidateUnitContractTerm,
      state: { units: [unit] },
      saveState: () => effects.push("save"),
      render: () => effects.push("render"),
      openValidationMessageModal: () => effects.push("message")
    }
  );

  confirm({ closest: () => panel });

  assert.equal(unit.contractTermValidated, true);
  assert.ok(unit.contractTermValidatedAt);
  assert.equal(checkbox.disabled, true);
  assert.deepEqual(effects, ["save", "render", "message"]);
});

test("the bottom action unlocks contract validation checkboxes", () => {
  const unit = { id: "unit-1", unit: "Local L-01", contractStart: "2025-06-01", contractEnd: "2026-08-30" };
  const removed = [];
  const checkbox = { disabled: true, classList: { remove: (name) => removed.push(name) } };
  const table = {};
  const panel = {
    querySelectorAll: (selector) => selector === "[data-unit-contract-validation-check]" ? [checkbox] : [table]
  };
  const button = { closest: () => panel };
  let headerUpdated = false;
  const edit = loadFunction(
    "editPropertyUnitContractValidation",
    "propertyUnitCatalogTableMarkup",
    {
      canManageValidation: () => true,
      canValidateUnitContractTerm,
      state: { units: [unit] },
      updatePropertyUnitContractValidationHeader: (value) => { headerUpdated = value === table; },
      openValidationMessageModal: () => {}
    }
  );

  checkbox.dataset = { unitId: unit.id };
  edit(button);

  assert.equal(checkbox.disabled, false);
  assert.deepEqual(removed, ["is-validated"]);
  assert.equal(headerUpdated, true);
});

test("the legal table omits separate validation and date edit columns", () => {
  const markup = loadFunction("propertyLegalPanelTableMarkup", "propertyLegalPanelRowMarkup", {
    sortedUnitsByNumber: (units) => units,
    propertyUnitContractValidationHeaderMarkup: () => "<th>Validar</th>",
    propertyLegalPanelRowMarkup: () => "<tr><td>Local L-01</td></tr>",
    propertyUnitContractValidationActionsMarkup: () => "<button>Editar Validacion</button>"
  })([{ id: "unit-1" }]);

  assert.doesNotMatch(markup, /contract-validation-edit-column|legal-term-action-column/);
  assert.match(markup, /<th>Vigencia de contrato<\/th>/);
  assert.match(markup, /<th class="contract-validation-status-column">Validacion<\/th>/);
  assert.match(markup, /<button>Editar Validacion<\/button>/);
});

test("contract dates are clickable in the table only while unvalidated", () => {
  const unit = {
    id: "unit-1", unit: "Local L-01", tenant: "Cafe Aurora",
    contractStart: "2025-06-01", contractEnd: "2026-08-30"
  };
  const rowMarkup = loadFunction("propertyLegalPanelRowMarkup", "legalNextPeriodActionsMarkup", {
    isUnitAvailable: () => false,
    tenantCellMarkup: (value) => value.tenant,
    contractStatus: () => ({ className: "status-danger", label: "Vencido" }),
    paymentTotalAmount: () => 0,
    formatCurrency: () => "$0",
    formatDate: (value) => value,
    isContractTermValidated: (value) => value.contractTermValidated === true,
    propertyUnitContractValidationCellMarkup: () => "<td></td>",
    contractTermValidationMarkup: () => "<span>Estado</span>",
    legalTemplateFileActionsMarkup: () => "",
    legalSignedContractActionsMarkup: () => "",
    legalNextPeriodActionsMarkup: () => "",
    escapeAttribute: (value) => value,
    canValidateUnitContractTerm,
    canManageValidation: () => true
  });

  assert.match(rowMarkup(unit), /class="contract-term-date-button"/);
  assert.match(rowMarkup(unit), /data-edit-contract-term="unit-1"/);
  assert.doesNotMatch(rowMarkup(unit), /contract-validation-edit-column|legal-term-action-column/);
  unit.contractTermValidated = true;
  assert.doesNotMatch(rowMarkup(unit), /class="contract-term-date-button"/);
  assert.doesNotMatch(rowMarkup(unit), /data-edit-contract-term="unit-1"/);
});

test("the date editor and save path reject validated contracts", () => {
  const unit = {
    id: "unit-1", propertyId: "plaza-1", unit: "Local L-01",
    contractStart: "2025-06-01", contractEnd: "2026-08-30", contractTermValidated: true
  };
  const effects = [];
  const els = {
    modalEyebrow: { textContent: "" },
    modalTitle: { textContent: "" },
    modalBody: { innerHTML: "", querySelector: () => null }
  };
  const isContractTermValidated = (value) => value.contractTermValidated === true;
  const openContractTermModal = loadFunction("openContractTermModal", "openContractTermValidatedBlockedModal", {
    state: { units: [unit] },
    isContractTermValidated,
    openContractTermValidatedBlockedModal: () => effects.push("blocked"),
    els,
    escapeAttribute: (value) => value,
    defaultUnitContractStart: () => "2025-06-01",
    defaultUnitContractEnd: () => "2026-08-30",
    injectIcons: () => {},
    openModal: () => effects.push("open")
  });
  const saveContractTerm = loadFunction("saveContractTerm", "openNextPeriodContractModal", {
    state: { units: [unit] },
    isContractTermValidated,
    openContractTermValidatedBlockedModal: () => effects.push("blocked"),
    saveState: () => effects.push("save"),
    closeModal: () => effects.push("close"),
    renderLegalWorkspace: () => effects.push("render"),
    toast: () => {}
  });

  openContractTermModal(unit.id, unit.propertyId);
  saveContractTerm(unit.id, unit.propertyId, { start: "2026-09-01", end: "2027-08-31" });
  assert.deepEqual(effects, ["blocked", "blocked"]);
  assert.equal(unit.contractEnd, "2026-08-30");

  unit.contractTermValidated = false;
  openContractTermModal(unit.id, unit.propertyId);
  assert.match(els.modalBody.innerHTML, /name="contractStart"/);
  saveContractTerm(unit.id, unit.propertyId, { start: "2026-09-01", end: "2027-08-31" });
  assert.equal(unit.contractEnd, "2027-08-31");
  assert.deepEqual(effects.slice(2), ["open", "save", "close", "render"]);
});

test("a validated contract cannot lose its dates by marking the unit available", () => {
  const canMarkUnitAsAvailable = loadFunction("canMarkUnitAsAvailable", "markUnitAsAvailable", {
    isUnitAvailable: () => false,
    isContractTermValidated: (unit) => unit.contractTermValidated === true
  });
  const unit = { contractEnd: "2026-08-30", contractTermValidated: true };
  assert.equal(canMarkUnitAsAvailable(unit), false);
  unit.contractTermValidated = false;
  assert.equal(canMarkUnitAsAvailable(unit), true);
});

test("updating a tenant preserves an existing contract validation and its dates", () => {
  const unit = {
    id: "unit-1", propertyId: "plaza-1", contractStart: "2025-06-01",
    contractEnd: "2026-08-30", contractTermValidated: true
  };
  const effects = [];
  const saveUnitTenantFromForm = loadFunction("saveUnitTenantFromForm", "upsertTenantProfileFromUnit", {
    state: { units: [unit] },
    getProperty: () => ({ id: "plaza-1" }),
    FormData: class {},
    upsertTenantProfileFromUnit: () => ({ id: "tenant-1", name: "Nuevo arrendatario" }),
    defaultUnitContractStart: () => "2026-09-01",
    defaultUnitContractEnd: () => "2027-08-31",
    saveState: () => effects.push("save"),
    returnToPropertiesCatalog: () => {},
    toast: () => {}
  });

  saveUnitTenantFromForm({ preventDefault() {}, currentTarget: {} }, "plaza-1", "unit-1");

  assert.equal(unit.contractTermValidated, true);
  assert.equal(unit.contractStart, "2025-06-01");
  assert.equal(unit.contractEnd, "2026-08-30");
  assert.deepEqual(effects, ["save"]);
});
