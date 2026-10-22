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

function monthPanelMarkup(lockState, canManage = true) {
  const render = loadFunction("paymentMethodMonthPanelMarkup", "paymentMethodAdvancePanelMarkup", {
    paymentMethodMonthLockState: () => lockState,
    paymentMethodValidationConcepts: ["rent", "extraordinary", "maintenance", "services"],
    isPaymentMethodConceptValidated: () => false,
    formatMonthLabel: (monthKey) => monthKey,
    formatMonthShort: (monthKey) => monthKey,
    escapeAttribute: (value) => value,
    paymentMethodConceptValidationHeaderMarkup: () => "",
    paymentMethodRowMarkup: () => "",
    paymentMethodTotalsRowMarkup: () => "",
    canManageValidation: () => canManage
  });
  const units = [{ id: "unit-1", propertyId: "plaza-1" }];
  return ["2026-10", "2026-09"].map((monthKey, index) => render(units, monthKey, index)).join("");
}

test("every payment month shows Editar Validacion beside Validar", () => {
  const markup = monthPanelMarkup(false);
  for (const monthKey of ["2026-10", "2026-09"]) {
    assert.match(markup, new RegExp(`data-edit-payment-method-validation="${monthKey}" disabled`));
    assert.match(markup, new RegExp(`data-confirm-payment-method-rent-validation="${monthKey}"`));
  }
  assert.equal((markup.match(/Editar Validacion/g) || []).length, 2);
  assert.equal((markup.match(/data-confirm-payment-method-rent-validation/g) || []).length, 2);
});

test("a validated month enables editing while keeping the validation button visible", () => {
  const markup = monthPanelMarkup(true);
  assert.match(markup, /data-edit-payment-method-validation="2026-10"/);
  assert.doesNotMatch(markup, /data-edit-payment-method-validation="2026-10" disabled/);
  assert.match(markup, /data-confirm-payment-method-rent-validation="2026-10" disabled/);
  assert.match(markup, /data-confirm-payment-method-rent-validation="2026-09" disabled/);
  assert.doesNotMatch(monthPanelMarkup(false, false), /data-edit-payment-method-validation/);
});

test("monthly action buttons switch enabled state without disappearing", () => {
  const editButton = { disabled: true, hidden: false };
  const validateButton = { disabled: false, hidden: false };
  const panel = {
    querySelectorAll: () => [],
    querySelector: (selector) => selector === "[data-edit-payment-method-validation]" ? editButton : validateButton
  };
  const sync = loadFunction("syncPaymentValidationLockUI", "applyPaymentMethodValidation", {
    canManageValidation: () => true
  });

  sync(panel, "2026-10", true, false);
  assert.equal(editButton.disabled, false);
  assert.equal(validateButton.disabled, true);
  sync(panel, "2026-10", false, false);
  assert.equal(editButton.disabled, true);
  assert.equal(validateButton.disabled, false);
  assert.equal(editButton.hidden, false);
  assert.equal(validateButton.hidden, false);
});

test("editing a monthly validation unlocks only that month", () => {
  const effects = [];
  const table = {};
  const panel = {
    dataset: { propertyId: "plaza-1" },
    querySelector: (selector) => selector === ".payment-method-table" ? table : {}
  };
  const edit = loadFunction("editPaymentMethodValidation", "syncPaymentValidationLockUI", {
    canManageValidation: () => true,
    propertyUnits: () => [{ id: "unit-1" }],
    paymentMethodMonthLockState: () => true,
    paymentMethodValidationConcepts: ["rent", "extraordinary", "maintenance", "services"],
    isPaymentMethodConceptValidated: () => false,
    setMonthValidationLock: (...args) => { effects.push(args); return true; },
    saveState: () => effects.push("save"),
    syncPaymentValidationLockUI: (_panel, monthKey, locked) => effects.push([monthKey, locked]),
    updatePaymentMethodValidationHeader: () => {},
    openValidationMessageModal: () => effects.push("message")
  });

  edit({ dataset: { editPaymentMethodValidation: "2026-09" }, closest: () => panel });
  assert.deepEqual(effects, [
    ["plaza-1", "2026-09", "paymentMethodValidationLocks", false],
    "save", ["2026-09", false], "message"
  ]);
});
