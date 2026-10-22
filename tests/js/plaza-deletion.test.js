import assert from "node:assert/strict";
import { readFileSync } from "node:fs";
import { runInNewContext } from "node:vm";
import test from "node:test";

const source = readFileSync(new URL("../../public/modules/rentas360-plazas/app.js", import.meta.url), "utf8");
const start = source.indexOf("function removePropertyData(");
const end = source.indexOf("\nfunction plazaArgentinaDemoPropertyIds(", start);
assert.ok(start >= 0 && end > start, "The property cleanup function must exist");
const removePropertyData = runInNewContext(`${source.slice(start, end)}; removePropertyData`);
const normalizeStart = source.indexOf("function normalizeState(");
const normalizeEnd = source.indexOf("\nfunction removePropertyData(", normalizeStart);
assert.ok(normalizeStart >= 0 && normalizeEnd > normalizeStart, "State normalization must exist");
const deleteStart = source.indexOf("function deletePlazaAndReturn(");
const deleteEnd = source.indexOf("\nfunction renderPlazaContracts(", deleteStart);
assert.ok(deleteStart >= 0 && deleteEnd > deleteStart, "The plaza deletion action must exist");

test("removing a plaza clears its records and access without changing other plazas", () => {
  const state = {
    properties: [{ id: "keep" }, { id: "remove" }],
    units: [{ propertyId: "keep" }, { propertyId: "remove" }],
    localMarketingScreens: [{ propertyId: "keep" }, { propertyId: "remove" }],
    localMarketingPlaylists: [{ propertyId: "keep" }, { propertyId: "remove" }],
    localMarketingMaintenanceTickets: [{ propertyId: "keep" }, { propertyId: "remove" }],
    users: [{
      propertyIds: ["keep", "remove"],
      menuPermissionsByProperty: { keep: ["read"], remove: ["write"] }
    }],
    organizations: [{ propertyIds: ["keep", "remove"] }],
    tenantProfiles: [{ propertyIds: ["keep", "remove"] }]
  };

  removePropertyData(state, "remove");

  for (const collection of [
    state.properties,
    state.units,
    state.localMarketingScreens,
    state.localMarketingPlaylists,
    state.localMarketingMaintenanceTickets
  ]) {
    assert.deepEqual(collection.map((item) => item.propertyId || item.id), ["keep"]);
  }
  assert.deepEqual(state.users[0].propertyIds, ["keep"]);
  assert.deepEqual(state.users[0].menuPermissionsByProperty, { keep: ["read"] });
  assert.deepEqual(state.organizations[0].propertyIds, ["keep"]);
  assert.deepEqual(state.tenantProfiles[0].propertyIds, ["keep"]);
});

test("confirmed deletion records a tombstone and clears a deleted plaza selection", () => {
  const state = {
    deletedPropertyIds: [],
    properties: [{ id: "keep" }, { id: "remove" }],
    units: [{ propertyId: "remove" }],
    localMarketingScreens: [],
    localMarketingPlaylists: [],
    localMarketingMaintenanceTickets: [],
    users: [],
    organizations: [],
    tenantProfiles: []
  };
  const view = {
    roleId: "superadmin",
    propertyFilter: "remove",
    administrationPropertyId: "remove",
    contractsPropertyId: "keep",
    propertyDetailId: "remove"
  };
  const effects = [];
  const context = {
    state,
    view,
    getProperty: (id) => state.properties.find((property) => property.id === id),
    removePropertyData,
    loadSelectedPropertyId: () => "remove",
    localStorage: { removeItem: (key) => effects.push(`remove:${key}`) },
    SELECTED_PROPERTY_KEY: "selected-property",
    saveState: () => effects.push("save"),
    closeModal: () => effects.push("close"),
    render: () => effects.push("render"),
    toast: (message) => effects.push(message)
  };
  const deletePlazaAndReturn = runInNewContext(
    `${source.slice(deleteStart, deleteEnd)}; deletePlazaAndReturn`, context
  );

  deletePlazaAndReturn("remove");

  assert.deepEqual(Array.from(state.deletedPropertyIds), ["remove"]);
  assert.deepEqual(state.properties.map((property) => property.id), ["keep"]);
  assert.equal(state.units.length, 0);
  assert.equal(view.propertyFilter, "all");
  assert.equal(view.administrationPropertyId, "all");
  assert.equal(view.contractsPropertyId, "keep");
  assert.equal(view.propertyDetailId, "");
  assert.deepEqual(effects, ["remove:selected-property", "save", "close", "render", "Plaza eliminada"]);

  view.roleId = "admin";
  deletePlazaAndReturn("keep");
  assert.deepEqual(state.properties.map((property) => property.id), ["keep"]);
});

test("loading merged seed data does not restore a deleted plaza", () => {
  const normalizeState = runInNewContext(
    `${source.slice(normalizeStart, normalizeEnd)}; normalizeState`,
    {
      normalizeUsers: (users) => users,
      ensureDedicatedPanelUser: () => {},
      normalizeUnitPayments: (unit) => unit,
      removePropertyData,
      applyPlazaArgentinaDemoContractDates: () => {},
      applyPlazaArgentinaDemoTenants: () => {},
      syncExpiredContractValidations: () => {},
      normalizeTenantProfiles: (state) => state.tenantProfiles
    }
  );
  const restoredState = normalizeState({
    deletedPropertyIds: ["remove"],
    properties: [{ id: "keep" }, { id: "remove" }],
    units: [{ propertyId: "keep" }, { propertyId: "remove" }],
    users: [],
    organizations: [],
    tenantProfiles: [],
    localMarketingScreens: [],
    localMarketingPlaylists: [],
    localMarketingMaintenanceTickets: []
  });

  assert.deepEqual(restoredState.properties.map((property) => property.id), ["keep"]);
  assert.deepEqual(restoredState.units.map((unit) => unit.propertyId), ["keep"]);
});
