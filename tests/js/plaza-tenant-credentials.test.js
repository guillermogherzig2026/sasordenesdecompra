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

test("tenant rows use the linked platform account for username and password availability", () => {
  const users = [{ id: "tenant-1", name: "Cafe Aurora", username: "cafe.aurora", password: "TestSecret2026" }];
  const rows = loadFunction("tenantRows", "tenantProfileDefaults", {
    state: {
      tenantProfiles: [
        { id: "profile-1", userId: "tenant-1", name: "Cafe Aurora", commercialName: "Aurora Cafe", propertyIds: [] },
        { id: "profile-2", userId: null, name: "Sin acceso", propertyIds: [] }
      ],
      units: []
    },
    getUser: (id) => users.find((user) => user.id === id),
    tenantProfileDefaults: () => ({}),
    normalizeText: (value) => String(value || "").toLowerCase(),
    getProperty: () => null
  })();

  assert.equal(rows[0].username, "cafe.aurora");
  assert.equal(rows[0].commercialName, "Aurora Cafe");
  assert.equal(rows[0].hasPassword, true);
  assert.equal(rows[1].username, "");
  assert.equal(rows[1].hasPassword, false);
  assert.equal(Object.hasOwn(rows[0], "password"), false);
});

test("the tenant catalog shows commercial name before username and masked password", () => {
  const table = loadFunction("tenantCatalogTableMarkup", "tenantCatalogRowMarkup", {
    tenantCatalogRowMarkup: () => "<tr></tr>"
  })([{}]);
  assert.match(table, /<th>Arrendatario<br>Razón Social<\/th>\s*<th>Nombre Comercial<\/th>\s*<th>Usuario<\/th>\s*<th>Contraseña<\/th>/);

  const row = loadFunction("tenantCatalogRowMarkup", "tenantLeaseContractStatus", {
    tenantLeaseContractStatus: () => ({ label: "Vigente", className: "status-paid", description: "Vigente" }),
    escapeAttribute: (value) => String(value),
    view: { roleId: "superadmin" }
  })({
    id: "profile-1", userId: "tenant-1", name: "Cafe Aurora", commercialName: "Aurora Cafe", type: "Persona moral",
    rfc: "ABC123", unitsLabel: "Local L-01", username: "cafe.aurora",
    hasPortalAccess: true, hasPassword: true
  });

  assert.match(row, /<code>cafe\.aurora<\/code>/);
  assert.match(row, /class="tenant-commercial-name-column">Aurora Cafe<\/td>/);
  assert.match(row, /data-tenant-password-toggle="tenant-1"/);
  assert.match(row, /aria-pressed="false"/);
  assert.doesNotMatch(row, /TestSecret2026/);
});

test("existing tenants without a commercial name are marked as unregistered", () => {
  const row = loadFunction("tenantCatalogRowMarkup", "tenantLeaseContractStatus", {
    tenantLeaseContractStatus: () => ({ label: "Sin contrato", className: "status-neutral", description: "Sin contrato" }),
    escapeAttribute: (value) => String(value),
    view: { roleId: "admin" }
  })({
    id: "profile-2", name: "Estetica Urbana", type: "Persona moral",
    unitsLabel: "Sin unidad", hasPortalAccess: false, hasPassword: false
  });

  assert.match(row, /class="tenant-commercial-name-column">Sin registrar<\/td>/);
});

test("editing a tenant from a unit saves its commercial name", () => {
  const profile = { id: "profile-1", name: "Cafe Aurora", propertyIds: [] };
  const upsert = loadFunction("upsertTenantProfileFromUnit", "openMarkUnitAvailableConfirmation", {
    state: { tenantProfiles: [profile], users: [] },
    normalizeText: (value) => String(value || "").toLowerCase()
  });

  const data = new Map([
    ["tenantProfileId", "profile-1"], ["commercialName", "Aurora Cafe"]
  ]);
  upsert(data, "plaza-1");
  assert.equal(profile.commercialName, "Aurora Cafe");
});

test("other roles cannot reveal tenant passwords", () => {
  const row = loadFunction("tenantCatalogRowMarkup", "tenantLeaseContractStatus", {
    tenantLeaseContractStatus: () => ({ label: "Vigente", className: "status-paid", description: "Vigente" }),
    escapeAttribute: (value) => String(value),
    view: { roleId: "admin" }
  })({
    id: "profile-1", userId: "tenant-1", name: "Cafe Aurora", type: "Persona moral",
    rfc: "ABC123", unitsLabel: "Local L-01", username: "cafe.aurora",
    hasPortalAccess: true, hasPassword: true
  });
  assert.doesNotMatch(row, /data-tenant-password-toggle/);
});

test("the reveal button toggles only the selected tenant credential", () => {
  let toggle;
  const attributes = { "aria-pressed": "false" };
  const value = { textContent: "••••••••" };
  const button = {
    dataset: { tenantPasswordToggle: "tenant-1" },
    addEventListener: (_event, listener) => { toggle = listener; },
    closest: () => ({ querySelector: () => value }),
    getAttribute: (name) => attributes[name],
    setAttribute: (name, content) => { attributes[name] = content; }
  };
  const bind = loadFunction("bindTenantCatalogForPropertyActions", "tenantCatalogTableMarkup", {
    els: { contentArea: {
      querySelector: () => null,
      querySelectorAll: (selector) => selector === "[data-tenant-password-toggle]" ? [button] : []
    } },
    view: { roleId: "superadmin" },
    getUser: () => ({ name: "Cafe Aurora", password: "TestSecret2026" })
  });

  bind(null);
  toggle();
  assert.equal(value.textContent, "TestSecret2026");
  assert.equal(attributes["aria-pressed"], "true");
  toggle();
  assert.equal(value.textContent, "••••••••");
  assert.equal(attributes["aria-pressed"], "false");
});

test("saved tenant passwords survive state normalization", () => {
  const normalize = loadFunction("normalizeUsers", "defaultUsername", {
    DEMO_PASSWORD: "Demo360!",
    defaultUsername: () => "tenant.default"
  });
  const users = normalize([{ id: "tenant-1", username: "tenant.one", password: "TestSecret2026" }]);
  assert.equal(users[0].password, "TestSecret2026");
});
