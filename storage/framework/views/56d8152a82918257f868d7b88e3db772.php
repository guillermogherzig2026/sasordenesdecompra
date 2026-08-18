<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo e(config('app.name')); ?></title>
        <style>
            :root {
                color-scheme: light;
                --bg: #f4f7fb;
                --panel: #ffffff;
                --text: #182235;
                --muted: #667085;
                --line: #d9e2ef;
                --primary: #176b87;
                --primary-strong: #0e536a;
                --danger: #b42318;
                --success: #157347;
                --warning: #9a5b00;
                --radius: 8px;
                font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
                font-size: 14px;
            }

            * { box-sizing: border-box; }
            [hidden] { display: none !important; }
            body { margin: 0; background: var(--bg); color: var(--text); }
            button, input { font: inherit; }
            a { color: inherit; }
            .login-shell { min-height: 100vh; display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(340px, 460px); }
            .login-hero { padding: clamp(32px, 7vw, 92px); display: flex; flex-direction: column; justify-content: center; gap: 22px; color: #fff; background: linear-gradient(rgba(12, 47, 61, .82), rgba(12, 47, 61, .74)); }
            .login-hero h1 { margin: 0; max-width: 720px; font-size: clamp(2.25rem, 5vw, 4.75rem); line-height: .96; }
            .login-hero p { margin: 0; max-width: 620px; color: rgba(255, 255, 255, .84); }
            .brand-mark { width: 48px; height: 48px; border-radius: 8px; display: grid; place-items: center; color: #fff; font-weight: 800; background: var(--primary); }
            .login-card { align-self: center; justify-self: center; width: min(86%, 420px); padding: 30px; background: var(--panel); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: 0 18px 40px rgba(35, 48, 73, .12); display: grid; gap: 20px; }
            .eyebrow { margin: 0 0 6px; color: var(--primary); text-transform: uppercase; font-size: .78rem; font-weight: 850; }
            .login-hero .eyebrow { color: rgba(255, 255, 255, .72); }
            .stack { display: grid; gap: 14px; }
            label { display: grid; gap: 7px; color: #344054; font-weight: 650; }
            .form-label { color: #344054; font-weight: 650; }
            .buyer-subrole-box { display: grid; gap: 7px; align-content: start; }
            .role-subcategory-stack { width: min(100%, 520px); display: grid; gap: 12px; align-content: start; }
            .role-capabilities { display: grid; gap: 7px; }
            .role-capability-list { display: grid; gap: 7px; }
            .role-capability-card { padding: 9px 10px; border: 1px solid var(--line); border-radius: 8px; background: #f8fbff; display: grid; gap: 4px; color: #344054; }
            .role-capability-card strong { color: var(--primary-strong); font-size: .9rem; }
            .role-capability-card span { color: var(--muted); font-size: .84rem; line-height: 1.35; }
            .role-capability-card.is-active { border-color: var(--primary); background: #e5f3f7; box-shadow: inset 3px 0 0 var(--primary); }
            .role-capability-card.is-active strong { color: var(--primary); }
            input { width: 100%; min-height: 42px; border: 1px solid var(--line); border-radius: 8px; padding: 10px 12px; background: #fff; color: var(--text); }
            .button { border: 1px solid var(--line); border-radius: 8px; min-height: 40px; padding: 9px 14px; font-weight: 760; color: var(--text); background: #fff; display: inline-flex; align-items: center; justify-content: center; gap: 8px; text-decoration: none; cursor: pointer; }
            .button.primary { border-color: var(--primary); background: var(--primary); color: #fff; }
            .button.primary:hover { background: var(--primary-strong); }
            .button.danger { border-color: var(--danger); background: var(--danger); color: #fff; }
            .button.ghost { background: #fff; }
            .button.small { min-height: 34px; padding: 7px 10px; font-size: .9rem; }
            .hint, .fine-print { color: var(--muted); font-size: .9rem; }
            .form-error { color: var(--danger); margin: 0; }
            .app-shell { min-height: 100vh; display: grid; grid-template-columns: 260px minmax(0, 1fr); }
            .sidebar { position: sticky; top: 0; height: 100vh; padding: 22px; background: #102b3a; color: #fff; display: flex; flex-direction: column; gap: 28px; overflow: hidden; }
            .sidebar-brand { display: flex; align-items: center; gap: 12px; }
            .sidebar-brand span { display: block; color: rgba(255, 255, 255, .7); font-size: .86rem; margin-top: 2px; }
            .nav-list { min-height: 0; flex: 1 1 auto; display: grid; align-content: start; gap: 7px; overflow-y: auto; overflow-x: hidden; padding-right: 8px; overscroll-behavior: contain; scrollbar-gutter: stable; }
            .nav-list::-webkit-scrollbar { width: 8px; }
            .nav-list::-webkit-scrollbar-track { background: rgba(255, 255, 255, .08); border-radius: 999px; }
            .nav-list::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, .34); border-radius: 999px; }
            .nav-list::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, .5); }
            .nav-button { width: 100%; justify-content: flex-start; color: rgba(255, 255, 255, .8); background: transparent; border-color: transparent; }
            .nav-button.active { color: #fff; background: rgba(255, 255, 255, .12); }
            .nav-pending-badge { display: inline-grid; place-items: center; min-width: 1.35rem; height: 1.35rem; margin-left: .55rem; padding: 0 .3rem; border-radius: 999px; background: #dc3d36; color: #fff; font-size: .76rem; font-weight: 800; line-height: 1; }
            .nav-pending-badge.is-empty { background: rgba(255, 255, 255, .24); color: rgba(255, 255, 255, .9); }
            .nav-group { border: 1px solid rgba(255, 255, 255, .08); border-radius: 8px; padding: 5px; }
            .nav-group[open] { background: rgba(255, 255, 255, .04); }
            .nav-group summary { min-height: 38px; padding: 8px 10px; display: flex; align-items: center; justify-content: space-between; gap: 10px; color: rgba(255, 255, 255, .86); font-weight: 850; cursor: pointer; list-style: none; }
            .nav-group summary::-webkit-details-marker { display: none; }
            .nav-group summary::after { content: 'v'; color: rgba(255, 255, 255, .56); font-size: .78rem; }
            .nav-group[open] summary::after { content: '^'; }
            .nav-sublist { display: grid; gap: 4px; padding: 3px 0 3px 8px; }
            .sub-nav-button { min-height: 34px; padding: 7px 10px; font-size: .92rem; }
            .nav-box { --nav-box-border: rgba(255, 255, 255, .18); --nav-box-bg: rgba(255, 255, 255, .05); --nav-box-active: rgba(255, 255, 255, .14); display: grid; gap: 3px; padding: 6px; border: 1px solid var(--nav-box-border); border-radius: 8px; background: var(--nav-box-bg); }
            .nav-box-title { padding: 2px 8px 4px; color: rgba(255, 255, 255, .92); font-size: .72rem; font-weight: 900; letter-spacing: .02em; text-transform: uppercase; }
            .nav-box .nav-button { min-height: 31px; padding: 6px 9px; }
            .nav-box .nav-button.active { background: var(--nav-box-active); box-shadow: inset 3px 0 0 var(--nav-box-border); }
            .nav-inline-flow { display: grid; gap: 3px; margin: 3px 2px 4px 8px; padding: 5px 0 5px 7px; border-left: 2px solid var(--nav-box-border, rgba(85, 166, 255, .65)); background: var(--nav-box-bg, rgba(85, 166, 255, .07)); }
            .nav-inline-flow .nav-box-title { padding-left: 7px; }
            .nav-inline-flow .nav-button { min-width: 0; }
            .nav-box-oc, .nav-box-purchases { --nav-box-border: rgba(85, 166, 255, .58); --nav-box-bg: rgba(85, 166, 255, .10); --nav-box-active: rgba(85, 166, 255, .24); }
            .nav-box-services { --nav-box-border: rgba(69, 210, 146, .55); --nav-box-bg: rgba(69, 210, 146, .10); --nav-box-active: rgba(69, 210, 146, .22); }
            .nav-box-op { --nav-box-border: rgba(255, 128, 128, .58); --nav-box-bg: rgba(255, 128, 128, .10); --nav-box-active: rgba(255, 128, 128, .24); }
            .nav-box-os, .nav-box-supplies { --nav-box-border: rgba(255, 183, 77, .62); --nav-box-bg: rgba(255, 183, 77, .11); --nav-box-active: rgba(255, 183, 77, .25); }
            .nav-box-or, .nav-box-reimbursements { --nav-box-border: rgba(191, 128, 255, .58); --nav-box-bg: rgba(191, 128, 255, .11); --nav-box-active: rgba(191, 128, 255, .25); }
            .nav-box-construction-obras { --nav-box-border: rgba(78, 205, 196, .58); --nav-box-bg: rgba(78, 205, 196, .10); --nav-box-active: rgba(78, 205, 196, .24); }
            .nav-box-construction-operacion { --nav-box-border: rgba(255, 214, 102, .62); --nav-box-bg: rgba(255, 214, 102, .11); --nav-box-active: rgba(255, 214, 102, .25); }
            .nav-box-construction-materiales { --nav-box-border: rgba(92, 184, 92, .58); --nav-box-bg: rgba(92, 184, 92, .10); --nav-box-active: rgba(92, 184, 92, .23); }
            .nav-box-construction-materiales .nav-inline-flow { border-left: 0; background: transparent; }
            .nav-box-construction-materiales .nav-inline-flow .nav-button.active { background: #3a5663; box-shadow: inset 3px 0 0 #78909b; }
            .nav-box-construction-finanzas { --nav-box-border: rgba(100, 149, 237, .58); --nav-box-bg: rgba(100, 149, 237, .10); --nav-box-active: rgba(100, 149, 237, .24); }
            .nav-box-construction-admin { --nav-box-border: rgba(191, 128, 255, .58); --nav-box-bg: rgba(191, 128, 255, .11); --nav-box-active: rgba(191, 128, 255, .25); }
            .content-shell { min-width: 0; min-height: 100vh; display: flex; flex-direction: column; }
            .topbar { min-height: 66px; padding: 12px clamp(18px, 3vw, 32px); display: flex; align-items: center; justify-content: space-between; gap: 20px; background: rgba(244, 247, 251, .9); border-bottom: 1px solid var(--line); position: sticky; top: 0; }
            .topbar-right { display: flex; align-items: center; gap: 10px; margin-left: auto; }
            .topbar-actions { display: flex; align-items: center; gap: 8px; }
            .topbar .eyebrow { margin-bottom: 4px; }
            .topbar h1 { margin: 0; font-size: 1.55rem; line-height: 1.1; }
            .user-pill { display: flex; align-items: center; gap: 10px; background: #fff; border: 1px solid var(--line); border-radius: 999px; padding: 6px 7px 6px 14px; }
            .view { min-height: calc(100vh - 66px); padding: clamp(16px, 3vw, 30px); display: grid; align-content: start; gap: 22px; }
            .metrics-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
            .metric-card, .panel { background: var(--panel); border: 1px solid var(--line); border-radius: var(--radius); box-shadow: 0 10px 26px rgba(35, 48, 73, .06); }
            .metric-card { padding: 18px; display: grid; gap: 8px; }
            .metric-card span { color: var(--muted); font-weight: 700; }
            .metric-card strong { font-size: 1.85rem; line-height: 1; }
            .panel { padding: 20px; display: grid; align-content: start; gap: 14px; }
            .construction-carousel-panel { padding: 18px 22px 14px; gap: 12px; }
            .construction-carousel-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 24px; }
            .construction-carousel-title { display: inline-flex; align-items: center; gap: 10px; min-width: 0; }
            .construction-carousel-title h2 { margin: 0; font-size: 1.05rem; line-height: 1.2; }
            .construction-carousel-count { display: inline-grid; place-items: center; min-width: 28px; height: 28px; padding: 0 8px; border-radius: 8px; background: #b8f5df; color: #087443; font-weight: 900; line-height: 1; }
            .construction-carousel-shell { display: grid; grid-template-columns: 34px minmax(0, 1fr) 34px; align-items: center; gap: 12px; min-width: 0; }
            .construction-carousel-track { min-width: 0; display: flex; gap: 12px; overflow-x: auto; overscroll-behavior-x: contain; scroll-snap-type: x proximity; scrollbar-width: none; padding: 1px; }
            .construction-carousel-track::-webkit-scrollbar { display: none; }
            .construction-carousel-nav { width: 34px; height: 34px; min-height: 34px; padding: 0; border: 1px solid var(--line); border-radius: 999px; background: #fff; color: var(--primary-strong); box-shadow: 0 6px 18px rgba(35, 48, 73, .10); font-weight: 900; cursor: pointer; }
            .construction-carousel-nav:hover { border-color: var(--primary); background: #e5f3f7; }
            .construction-project-tile { flex: 0 0 198px; min-height: 122px; padding: 12px 14px; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: inherit; display: grid; justify-items: center; align-content: center; gap: 5px; text-align: center; text-decoration: none; scroll-snap-align: start; cursor: pointer; }
            .construction-project-tile-catalog { flex-basis: 224px; border-color: #d8b55a; background: #fffbed; }
            .construction-project-tile.active { border-color: #10a6a0; box-shadow: inset 0 0 0 1px #10a6a0; }
            .construction-project-tile-catalog:hover { border-color: #c79a2b; background: #fff7d9; }
            .construction-project-tile-catalog.active { border-color: #c79a2b; background: #fff3c4; box-shadow: inset 0 0 0 1px #c79a2b; }
            .construction-project-tile-catalog .construction-project-avatar { background: #ffedb0; color: #6f4c00; }
            .construction-project-tile-catalog .construction-project-key,
            .construction-project-tile-catalog .construction-project-status { color: #73550a; }
            .construction-project-tile-catalog .construction-project-status span { background: #d5a127; }
            .construction-project-tile:disabled { cursor: default; opacity: .82; }
            .construction-project-tile-create { background: #f5fffb; border-color: #61d6c7; }
            .construction-project-add, .construction-project-avatar { width: 40px; height: 40px; border-radius: 999px; display: grid; place-items: center; overflow: hidden; }
            .construction-project-add { background: #0aa79b; color: #fff; font-size: 1.35rem; font-weight: 900; line-height: 1; }
            .construction-project-avatar { background: #edf5f7; color: var(--primary-strong); font-weight: 900; }
            .construction-project-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .construction-project-key { color: var(--muted); font-size: .78rem; font-weight: 900; line-height: 1.1; }
            .construction-project-name { max-width: 100%; color: #071f3d; font-size: .95rem; line-height: 1.15; overflow-wrap: anywhere; }
            .construction-project-status { display: inline-flex; align-items: center; justify-content: center; gap: 7px; color: var(--muted); font-size: .78rem; font-weight: 850; line-height: 1.1; }
            .construction-project-status span { width: 8px; height: 8px; border-radius: 999px; background: #18b886; flex: 0 0 auto; }
            .construction-overview-table { min-width: 1860px; }
            .construction-overview-table th, .construction-overview-table td { vertical-align: middle; }
            .construction-overview-table td:nth-child(n+3):nth-child(-n+6) { font-variant-numeric: tabular-nums; white-space: nowrap; }
            .construction-overview-table .construction-actions-column { position: sticky; right: 0; min-width: 100px; background: #fff; text-align: right; box-shadow: -1px 0 0 var(--line); z-index: 1; }
            .construction-overview-table thead .construction-actions-column { z-index: 2; }
            .construction-overview-table tbody tr { scroll-margin-top: 18px; }
            .construction-overview-table tbody tr:target td { background: #effbf7; }
            .construction-overview-table tbody tr:target .construction-actions-column { background: #effbf7; }
            .generator-levels-panel { padding: 14px 18px; gap: 10px; }
            .generator-levels-panel .panel-header h2 { margin: 0; font-size: 1.05rem; }
            .generator-level-shell { min-width: 0; display: grid; grid-template-columns: 32px minmax(0, 1fr) 32px; align-items: center; gap: 10px; }
            .generator-level-nav { width: 32px; height: 32px; min-height: 32px; padding: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--primary-strong); font-weight: 900; cursor: pointer; }
            .generator-level-nav:hover { border-color: var(--primary); background: #eef8fb; }
            .generator-level-track { min-width: 0; display: flex; gap: 10px; overflow-x: auto; overscroll-behavior-x: contain; scroll-snap-type: x proximity; scrollbar-width: none; padding: 1px; }
            .generator-level-track::-webkit-scrollbar { display: none; }
            .generator-level-card { position: relative; flex: 0 0 178px; min-height: 68px; padding: 9px 10px; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--text); display: grid; grid-template-columns: 38px minmax(0, 1fr) 22px; align-items: center; gap: 8px; text-align: left; scroll-snap-align: start; cursor: pointer; }
            .generator-level-card:hover { border-color: #56b79a; background: #f7fcfa; }
            .generator-level-card.is-active { border-color: #2eaa78; background: #effbf5; box-shadow: inset 0 0 0 1px rgba(46, 170, 120, .18); }
            .generator-level-code { width: 38px; height: 38px; border-radius: 7px; display: grid; place-items: center; background: #edf2f7; color: #4a6075; font-size: .74rem; font-weight: 900; }
            .generator-level-card.is-active .generator-level-code { background: #dff5ea; color: #157347; }
            .generator-level-card strong, .generator-level-card small { display: block; min-width: 0; }
            .generator-level-card strong { margin-bottom: 4px; line-height: 1.1; }
            .generator-level-card small { color: var(--muted); white-space: nowrap; }
            .generator-level-check { width: 22px; height: 22px; display: grid; place-items: center; border-radius: 999px; color: #fff; background: transparent; font-size: .62rem; font-weight: 900; }
            .generator-level-card.is-active .generator-level-check { background: #198754; }
            .generator-detail-panel { min-height: 0 !important; padding: 16px 18px 20px; gap: 10px; }
            .generator-detail-header h2 { margin: 0; }
            .generator-header-actions { display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: wrap; }
            .generator-header-actions [data-generator-status] { color: var(--success); font-weight: 760; }
            .generator-tabs { display: flex; align-items: center; gap: 4px; border-bottom: 1px solid var(--line); }
            .generator-tab { min-height: 36px; padding: 8px 12px; border: 0; border-bottom: 3px solid transparent; background: transparent; color: var(--muted); font-weight: 850; cursor: pointer; }
            .generator-tab.is-active { color: var(--text); border-bottom-color: #f59e0b; }
            .generator-formula { min-height: 38px; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; gap: 9px; color: #2457a6; background: #edf6ff; font-size: .86rem; }
            .generator-formula-icon { width: 20px; height: 20px; border: 1px solid #7ba9df; border-radius: 999px; display: grid; place-items: center; font-size: .72rem; font-weight: 900; }
            .generator-capture { display: grid; gap: 4px; }
            .generator-category { display: grid; gap: 0; }
            .generator-category-heading { width: 100%; min-height: 38px; padding: 7px 12px; border: 0; border-radius: 6px; background: #edf5fc; color: var(--text); display: grid; grid-template-columns: 24px minmax(0, 1fr) auto; align-items: center; gap: 8px; text-align: left; cursor: pointer; }
            .generator-category-heading:hover { background: #e4f0fa; }
            .generator-category-heading > span:last-child { font-variant-numeric: tabular-nums; }
            .generator-category-toggle { color: var(--primary-strong); font-size: 1.15rem; font-weight: 900; line-height: 1; }
            .generator-category-details { border: 1px solid #dbe6f2; border-top: 0; border-radius: 0 0 6px 6px; background: #fff; }
            .generator-category-details form { display: grid; }
            .generator-table-scroll { max-height: 390px; scrollbar-gutter: stable both-edges; }
            .generator-table { min-width: 1200px; table-layout: fixed; }
            .generator-table th, .generator-table td { padding: 7px 6px; vertical-align: middle; }
            .generator-table th { background: #f8fbff; white-space: normal; line-height: 1.15; }
            .generator-table th:nth-child(1), .generator-table td:nth-child(1) { width: 170px; }
            .generator-table th:nth-child(2), .generator-table td:nth-child(2) { width: 140px; }
            .generator-table th:nth-child(3), .generator-table td:nth-child(3),
            .generator-table th:nth-child(4), .generator-table td:nth-child(4),
            .generator-table th:nth-child(5), .generator-table td:nth-child(5),
            .generator-table th:nth-child(6), .generator-table td:nth-child(6) { width: 82px; }
            .generator-table th:nth-child(7), .generator-table td:nth-child(7) { width: 110px; }
            .generator-table th:nth-child(8), .generator-table td:nth-child(8) { width: 75px; }
            .generator-table th:nth-child(9), .generator-table td:nth-child(9) { width: 150px; }
            .generator-table th:nth-child(10), .generator-table td:nth-child(10) { width: 160px; }
            .generator-table input, .generator-table select { min-height: 34px; padding: 6px 8px; border-radius: 6px; font-size: .86rem; }
            .generator-table input[type="number"] { font-variant-numeric: tabular-nums; }
            .generator-net-input { background: #f3f6fa; color: var(--text); font-weight: 900; text-align: right; }
            .generator-table tr:not(.is-editing) input:not(.generator-net-input),
            .generator-table tr:not(.is-editing) select { background: #f3f6fa; border-color: #dbe4ee; color: var(--text); cursor: default; opacity: 1; }
            .generator-table tr.is-editing td { background: #fffaf0; }
            .generator-row-actions { display: flex; align-items: center; gap: 6px; }
            .generator-row-actions .button { white-space: nowrap; }
            .generator-empty-state { padding: 18px; color: var(--muted); text-align: center; }
            .generator-category-footer { min-height: 46px; padding: 8px 12px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; background: #fff6eb; color: #9a4f08; }
            .generator-history { display: grid; gap: 8px; }
            .generator-history-item { min-height: 58px; padding: 10px 12px; border: 1px solid var(--line); border-radius: 6px; display: flex; align-items: center; justify-content: space-between; gap: 14px; }
            .generator-history-item div { display: grid; gap: 3px; }
            .generator-history-item span, .generator-history-item time { color: var(--muted); font-size: .86rem; }
            .materials-explosion-panel { min-height: 0 !important; padding: 16px 18px 20px; gap: 10px; scroll-margin-top: 18px; }
            .materials-explosion-panel[hidden] { display: none; }
            .materials-explosion-header { align-items: flex-start; }
            .materials-explosion-header h2 { margin: 0; }
            .materials-explosion-actions { display: flex; align-items: center; justify-content: flex-end; gap: 8px; flex-wrap: wrap; }
            .materials-explosion-actions [data-materials-status] { color: var(--success); font-weight: 760; }
            .materials-formula { min-height: 38px; padding: 8px 12px; border-radius: 6px; display: flex; align-items: center; gap: 9px; color: #2457a6; background: #edf6ff; font-size: .86rem; }
            .materials-formula-icon { width: 20px; height: 20px; border: 1px solid #7ba9df; border-radius: 999px; display: grid; place-items: center; flex: 0 0 auto; font-size: .72rem; font-weight: 900; }
            .materials-category-list { display: grid; gap: 4px; }
            .materials-category { min-width: 0; border: 1px solid #dce5ef; border-radius: 6px; overflow: hidden; background: #fff; }
            .materials-category-heading { width: 100%; min-height: 46px; padding: 6px 10px; border: 0; background: #f7f9fc; color: var(--text); display: grid; grid-template-columns: 32px minmax(220px, 1fr) 110px 24px; align-items: center; gap: 10px; text-align: left; cursor: pointer; }
            .materials-category-heading:hover { background: #f0f5fa; }
            .materials-square-toggle { width: 28px; height: 28px; min-height: 28px; padding: 0; border: 1px solid #7898bd; border-radius: 4px; background: #fff; color: #174d8f; display: grid; place-items: center; font-size: 1.05rem; font-weight: 900; line-height: 1; cursor: pointer; }
            .materials-count-badge { justify-self: center; padding: 4px 8px; border-radius: 5px; background: #eaf2ff; color: #2457a6; font-size: .76rem; font-weight: 850; white-space: nowrap; }
            .materials-chevron { color: #2457a6; font-weight: 900; text-align: center; }
            .materials-category-details { border-top: 1px solid #dce5ef; background: #fff; }
            .materials-category-note { display: inline-flex; margin: 6px 38px; padding: 4px 8px; border-radius: 5px; color: #2457a6; background: #eaf2ff; font-size: .78rem; font-weight: 800; }
            .materials-concept-scroll { max-width: calc(100% - 26px); margin-left: 26px; border-left: 2px solid #d6e4f2; overflow-x: auto; overscroll-behavior-x: contain; }
            .materials-concept-list { min-width: 960px; display: grid; }
            .materials-concept { border-top: 1px solid #e4ebf3; }
            .materials-concept:first-child { border-top: 0; }
            .materials-concept-summary { min-height: 46px; padding: 6px 10px; display: grid; grid-template-columns: 32px minmax(280px, 1fr) 90px 60px 110px 176px; align-items: center; gap: 10px; background: #fff; }
            .materials-concept-summary > span { color: #43556c; font-variant-numeric: tabular-nums; }
            .materials-concept-actions, .materials-row-actions { display: flex; align-items: center; justify-content: flex-end; gap: 6px; }
            .materials-concept-actions .button, .materials-row-actions .button { min-height: 30px; padding: 5px 8px; white-space: nowrap; }
            .materials-concept-details { border-top: 1px solid #e2eaf3; background: #fbfdff; }
            .materials-supply-scroll { max-height: 360px; scrollbar-gutter: stable both-edges; }
            .materials-supply-table { min-width: 1220px; table-layout: fixed; }
            .materials-supply-table th, .materials-supply-table td { padding: 7px 8px; vertical-align: middle; }
            .materials-supply-table th { background: #f7faff; white-space: normal; line-height: 1.15; }
            .materials-supply-table th:nth-child(1), .materials-supply-table td:nth-child(1) { width: 180px; }
            .materials-supply-table th:nth-child(2), .materials-supply-table td:nth-child(2) { width: 120px; }
            .materials-supply-table th:nth-child(3), .materials-supply-table td:nth-child(3) { width: 130px; }
            .materials-supply-table th:nth-child(4), .materials-supply-table td:nth-child(4) { width: 150px; }
            .materials-supply-table th:nth-child(5), .materials-supply-table td:nth-child(5) { width: 100px; }
            .materials-supply-table th:nth-child(6), .materials-supply-table td:nth-child(6) { width: 120px; }
            .materials-supply-table th:nth-child(7), .materials-supply-table td:nth-child(7) { width: 120px; }
            .materials-supply-table th:nth-child(8), .materials-supply-table td:nth-child(8) { width: 120px; }
            .materials-supply-table th:nth-child(9), .materials-supply-table td:nth-child(9) { width: 120px; }
            .materials-supply-table th:nth-child(10), .materials-supply-table td:nth-child(10) { width: 170px; }
            .materials-supply-table input { min-height: 32px; padding: 5px 7px; border-radius: 5px; font-size: .84rem; font-variant-numeric: tabular-nums; }
            .materials-supply-table tr:not(.is-editing) input { border-color: transparent; background: transparent; color: var(--text); cursor: default; }
            .materials-supply-table tr.is-editing td { background: #fff9e8; }
            .materials-inline-value, .materials-currency-input { display: grid; grid-template-columns: minmax(58px, 1fr) auto; align-items: center; gap: 5px; }
            .materials-currency-input { grid-template-columns: auto minmax(66px, 1fr); }
            .materials-concept-footer { min-height: 44px; padding: 8px 14px; display: flex; align-items: center; justify-content: center; gap: clamp(20px, 18vw, 300px); border-top: 1px solid #e2eaf3; background: #fff; }
            .materials-concept-footer strong:last-child { font-size: 1rem; font-variant-numeric: tabular-nums; }
            .labor-budget-panel { padding: 14px 16px; gap: 10px; }
            .labor-budget-grid { display: grid; grid-template-columns: repeat(2, minmax(130px, 190px)); gap: 8px; justify-content: start; }
            .labor-budget-card { position: relative; min-height: 74px; padding: 10px; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--text); display: grid; place-items: center; gap: 5px; font-weight: 850; cursor: pointer; }
            .labor-budget-card:hover { border-color: var(--primary); background: #f4fbfd; }
            .labor-budget-card.is-active { border-color: #0aa79b; background: #f2fffc; box-shadow: inset 0 0 0 1px #0aa79b; }
            .labor-budget-toggle { position: absolute; top: 9px; right: 11px; color: #087b74; font-size: 1rem; font-weight: 900; line-height: 1; }
            .labor-budget-icon { width: 40px; height: 28px; display: grid; place-items: center; border-radius: 8px; background: #e5f3f7; color: var(--primary-strong); font-size: .78rem; font-weight: 900; }
            .labor-payroll-catalog { padding: 12px; border: 1px solid #0aa79b; border-radius: 8px; background: #fbfffe; display: grid; gap: 8px; }
            .labor-payroll-catalog[hidden] { display: none; }
            .labor-payroll-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 14px; }
            .labor-payroll-header h3 { margin: 0 0 5px; font-size: 1.02rem; }
            .labor-payroll-header p { margin: 0; color: var(--muted); font-size: .86rem; }
            .labor-payroll-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
            .labor-catalog-collapse { width: 32px; height: 32px; min-height: 32px; padding: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--primary-strong); font-size: 1rem; font-weight: 900; cursor: pointer; }
            .labor-catalog-scroll { max-height: 310px; }
            .labor-catalog-table { min-width: 1180px; }
            .labor-catalog-table th, .labor-catalog-table td { padding: 9px 10px; white-space: nowrap; }
            .labor-catalog-table td:nth-child(3) { min-width: 220px; white-space: normal; }
            .payroll-dialog { width: min(900px, calc(100vw - 42px)); }
            .payroll-form { display: grid; gap: 14px; }
            .payroll-form-actions { justify-content: flex-end; }
            .labor-tracking-panel { gap: 16px; min-height: 0; }
            .labor-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; }
            .labor-tabs { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
            .labor-tab { min-height: 40px; padding: 9px 18px; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--muted); font-weight: 850; cursor: pointer; }
            .labor-tab.is-active { color: #fff; border-color: #0aa79b; background: #0aa79b; }
            .labor-toolbar-note { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
            .labor-table-scroll { max-height: min(58vh, 520px); }
            .labor-table { min-width: 1660px; }
            .labor-table th, .labor-table td { white-space: nowrap; }
            .labor-table td:nth-child(3) { min-width: 230px; white-space: normal; }
            .labor-table .labor-actions-column { position: sticky; right: 0; z-index: 1; min-width: 100px; text-align: right; background: #fff; box-shadow: -1px 0 0 var(--line); }
            .labor-table th.labor-actions-column { z-index: 3; }
            .labor-type-badge { display: inline-flex; align-items: center; border-radius: 6px; padding: 5px 9px; font-size: .78rem; font-weight: 900; }
            .labor-type-badge.nomina { color: #176b87; background: #e5f3f7; }
            .labor-type-badge.estimacion { color: #9a5b00; background: #fff4d6; }
            .labor-area-badge { display: inline-flex; border-radius: 6px; padding: 5px 9px; background: #edf2f7; color: #344054; font-size: .78rem; font-weight: 850; }
            .labor-progress { display: grid; gap: 5px; min-width: 95px; }
            .labor-progress-track { height: 6px; border-radius: 999px; background: #e7edf5; overflow: hidden; }
            .labor-progress-track span { display: block; height: 100%; border-radius: inherit; background: #0bb394; }
            .labor-file-actions { display: flex; align-items: center; gap: 6px; }
            .labor-file-actions form { display: inline-flex; margin: 0; }
            .file-upload-input { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; }
            .labor-file-actions .labor-view-button:disabled { border-color: #d0d5dd; background: #eef1f4; color: #98a2b3; opacity: 1; cursor: not-allowed; box-shadow: none; }
            .construction-payment-table { min-width: 1460px; }
            .construction-payment-table th, .construction-payment-table td { white-space: nowrap; }
            .construction-payment-table td:nth-child(3) { min-width: 250px; white-space: normal; }
            .unit-price-panel { min-height: calc(100vh - 130px); grid-template-rows: auto auto auto auto minmax(260px, 1fr) auto; }
            .unit-price-summary { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; padding: 10px 12px; border: 1px solid #cfe4ee; border-radius: 8px; background: #f2f9fc; color: #344054; }
            .unit-price-summary strong { color: var(--primary-strong); font-variant-numeric: tabular-nums; }
            .unit-price-filters { display: grid; grid-template-columns: minmax(260px, 1.25fr) minmax(280px, 1fr) auto; align-items: end; gap: 12px; }
            .unit-price-filter-actions { display: flex; align-items: center; gap: 8px; }
            .unit-price-source-note { margin: 0; padding: 9px 12px; border-left: 3px solid #d8b55a; background: #fff9e8; color: #5f4b19; font-size: .88rem; line-height: 1.4; }
            .unit-price-scroll { min-height: 260px; max-height: none; }
            .unit-price-table { min-width: 1320px; table-layout: fixed; }
            .unit-price-table thead th { position: sticky; top: 0; z-index: 2; background: #f8fbff; box-shadow: 0 1px 0 var(--line); }
            .unit-price-table th, .unit-price-table td { padding: 10px 12px; vertical-align: middle; }
            .unit-price-table th:nth-child(1), .unit-price-table td:nth-child(1) { width: 150px; }
            .unit-price-table th:nth-child(2), .unit-price-table td:nth-child(2) { width: auto; }
            .unit-price-table th:nth-child(3), .unit-price-table td:nth-child(3) { width: 135px; text-align: center; }
            .unit-price-table th:nth-child(n+4), .unit-price-table td:nth-child(n+4) { width: 185px; text-align: right; }
            .unit-price-table td:first-child small { display: block; margin-top: 4px; color: var(--muted); }
            .unit-price-amount { white-space: nowrap; font-variant-numeric: tabular-nums; }
            .unit-price-total { color: var(--primary-strong); font-weight: 850; }
            .unit-price-unavailable { color: var(--muted); font-size: .84rem; }
            .unit-price-pagination { min-height: 34px; }
            .labor-table tr.is-highlighted td { background: #e8fbf6; }
            .view > .panel:only-child,
            .view > form.panel:only-child,
            .view > .panel:last-child,
            .view > form.panel:last-child {
                min-height: calc(100vh - 180px);
            }
            .finance-active-panel { min-height: calc(100vh - 130px); align-content: start; }
            .panel-header, .form-actions, .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
            .grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
            .grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
            .grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
            select, textarea { width: 100%; min-height: 42px; border: 1px solid var(--line); border-radius: 8px; padding: 10px 12px; background: #fff; color: var(--text); font: inherit; }
            table { width: 100%; border-collapse: collapse; }
            th, td { padding: 12px; border-bottom: 1px solid var(--line); text-align: left; vertical-align: top; }
            th { color: var(--muted); font-size: .82rem; text-transform: uppercase; }
            .table-scroll { max-height: min(68vh, calc(100vh - 260px)); overflow: auto; overscroll-behavior: contain; scrollbar-gutter: stable; }
            .supply-orders-panel { min-height: calc(100vh - 190px); display: flex; flex-direction: column; }
            .supply-orders-panel .panel-header { flex: 0 0 auto; }
            .supply-orders-table-scroll { flex: 1 1 auto; max-height: none; min-height: 360px; padding-bottom: 4px; }
            .supply-orders-table { min-width: 1540px; }
            body { padding-bottom: 18px; }
            .th-filter { display: grid; grid-template-columns: minmax(0, 1fr) auto auto; align-items: center; gap: 6px; min-width: 118px; }
            .th-filter > span { min-width: 0; }
            .column-filter { position: relative; display: inline-block; }
            .column-filter summary { display: inline-flex; align-items: center; justify-content: center; min-height: 28px; padding: 5px 8px; border: 1px solid var(--line); border-radius: 6px; background: #fff; color: var(--primary-strong); cursor: pointer; list-style: none; text-transform: none; font-size: .76rem; font-weight: 900; user-select: none; box-shadow: 0 2px 8px rgba(35, 48, 73, .08); }
            .column-filter summary::-webkit-details-marker { display: none; }
            .column-filter summary::after { content: 'v'; margin-left: 5px; font-size: .7rem; opacity: .7; }
            .column-filter[open] summary::after { content: '^'; }
            .column-filter summary.is-filtered { color: #fff; border-color: var(--primary); background: var(--primary); }
            .column-sort-button { display: inline-grid; place-items: center; width: 28px; height: 28px; margin-left: 0; padding: 0; border: 1px solid var(--line); border-radius: 999px; background: #fff; color: var(--primary-strong); cursor: pointer; font-size: .95rem; font-weight: 900; line-height: 1; vertical-align: middle; }
            .column-sort-button:hover, .column-sort-button.is-active { border-color: var(--primary); background: #e5f3f7; }
            .column-filter-panel { position: fixed; z-index: 2100; top: 0; left: 0; width: 285px; max-height: min(360px, calc(100vh - 24px)); padding: 10px; border: 1px solid #b8b8b8; border-radius: 4px; background: #fff; color: #111827; box-shadow: 0 14px 30px rgba(35, 48, 73, .2); display: grid; gap: 8px; text-transform: none; }
            .column-filter-search { width: 100%; min-height: 34px; padding: 7px 9px; border: 1px solid #aeb5bf; border-radius: 4px; background: #fff; color: #111827; font-size: .86rem; }
            .column-filter-search:focus { outline: 2px solid rgba(23, 107, 135, .2); border-color: var(--primary); }
            .column-filter-options { max-height: 230px; overflow: auto; display: grid; gap: 3px; padding: 2px; border: 1px solid #d0d0d0; background: #fff; }
            .column-filter-option { min-height: 22px; display: flex; align-items: center; gap: 4px; color: #111827; font-size: .86rem; font-weight: 500; line-height: 1.15; text-transform: none; white-space: nowrap; }
            .column-filter-option input { width: 14px; min-height: 14px; accent-color: #176b87; }
            .column-filter-empty { padding: 8px; color: var(--muted); font-size: .84rem; text-transform: none; }
            .column-filter-actions { display: flex; justify-content: flex-end; gap: 8px; flex-wrap: wrap; padding-top: 4px; }
            .column-filter-actions .button.small { min-height: 32px; padding: 5px 12px; border-color: #8b8b8b; border-radius: 4px; font-size: .82rem; font-weight: 650; }
            .excel-filter-head { min-width: 118px; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
            .excel-filter-head span { min-width: 0; }
            .excel-filter { position: relative; display: inline-block; text-transform: none; }
            .excel-filter-toggle { width: 30px; height: 30px; min-height: 30px; padding: 0; border-radius: 999px; border: 1px solid var(--line); background: #fff; color: var(--muted); box-shadow: 0 2px 8px rgba(35, 48, 73, .08); cursor: pointer; font-size: .78rem; line-height: 1; }
            .excel-filter-toggle.active { color: #fff; border-color: var(--primary); background: var(--primary); }
            .excel-filter-panel { position: fixed; z-index: 2000; top: 0; left: 0; width: 280px; max-height: min(430px, calc(100vh - 24px)); padding: 14px; border: 1px solid var(--line); border-radius: 12px; background: #fff; box-shadow: 0 18px 42px rgba(35, 48, 73, .22); display: none; gap: 10px; color: var(--text); text-transform: none; }
            .excel-filter-panel.open { display: grid; }
            .excel-filter-sort { display: grid; gap: 2px; padding-bottom: 8px; border-bottom: 1px solid var(--line); }
            .excel-filter-sort button { min-height: 32px; border: 0; background: transparent; text-align: left; color: var(--text); font-weight: 800; cursor: pointer; }
            .excel-filter-search { min-height: 36px; padding: 8px 10px; }
            .excel-filter-options { max-height: 190px; overflow: auto; display: grid; gap: 8px; padding-right: 4px; }
            .excel-filter-option { min-height: 26px; display: flex; align-items: center; gap: 9px; color: var(--text); font-size: .88rem; font-weight: 760; }
            .excel-filter-option input { width: auto; min-height: 0; accent-color: #2684ff; }
            .excel-date-range { display: grid; gap: 10px; }
            .excel-date-range label { display: grid; gap: 5px; color: var(--muted); font-size: .78rem; font-weight: 800; }
            .excel-date-range input[type="date"] { min-height: 38px; padding: 8px 10px; font-size: .88rem; }
            .excel-filter-actions { display: flex; justify-content: flex-end; gap: 8px; padding-top: 10px; border-top: 1px solid var(--line); }
            .excel-filter-actions .button.primary { background: #102b3a; border-color: #102b3a; }
            .status { --status-color: var(--muted); --status-bg: #f8fbff; display: inline-flex; align-items: center; gap: 6px; border: 1px solid currentColor; border-radius: 6px; padding: 5px 9px; color: var(--status-color); background: var(--status-bg); font-size: .82rem; font-weight: 800; line-height: 1.1; }
            .status::before { content: ''; width: 8px; height: 8px; border: 1.7px solid currentColor; border-radius: 999px; background: transparent; flex: 0 0 auto; }
            .status.sent, .status.pending, .status.partial { --status-color: #9a6a00; --status-bg: #fff6d9; }
            .status.approved { --status-color: #176b87; --status-bg: #e5f3f7; }
            .status.paid, .status.completed, .status.received { --status-color: #087443; --status-bg: #e7f7ed; }
            .status.success { --status-color: #087443; --status-bg: #e7f7ed; }
            .status.primary { --status-color: #176b87; --status-bg: #e5f3f7; }
            .status.warning { --status-color: #9a6a00; --status-bg: #fff6d9; }
            .status.danger { --status-color: #b42318; --status-bg: #fdecec; }
            .status.domiciled { --status-color: #176b87; --status-bg: #e5f3f7; }
            .status.rejected, .status.canceled { --status-color: #b42318; --status-bg: #fdecec; }
            .status-menu { position: relative; display: inline-block; min-width: 132px; }
            .status-menu summary { cursor: pointer; list-style: none; user-select: none; }
            .status-menu summary::-webkit-details-marker { display: none; }
            .status-menu summary::after { content: 'â–¾'; font-size: .72rem; margin-left: 8px; opacity: .7; }
            .status-menu[open] summary::after { content: 'â–´'; }
            .status-menu-panel { position: fixed; z-index: 2100; top: 0; left: 0; min-width: 150px; display: grid; gap: 6px; padding: 8px; border: 1px solid var(--line); border-radius: 8px; background: #fff; box-shadow: 0 12px 28px rgba(35, 48, 73, .18); }
            .status-menu summary::after { content: 'v'; margin-left: 4px; }
            .status-menu[open] summary::after { content: '^'; }
            .status-menu-panel .button.small { width: 100%; justify-content: center; padding: 5px 8px; font-size: .78rem; }
            .status-menu-panel form { display: block; }
            .status-menu-panel .button.small { justify-content: flex-start; gap: 6px; border-radius: 6px; padding: 6px 9px; background: #fff; box-shadow: none; }
            .status-menu-panel .button.small::before { content: ''; width: 8px; height: 8px; border: 1.7px solid currentColor; border-radius: 999px; background: transparent; flex: 0 0 auto; }
            .status-menu-panel .button.primary.small { color: #087443; border-color: #087443; background: #e7f7ed; }
            .status-menu-panel .button.ghost.small { color: #9a6a00; border-color: #9a6a00; background: #fff6d9; }
            .status-menu-panel .button.danger.small { color: #b42318; border-color: #b42318; background: #fdecec; }
            .supply-detail-dialog { width: min(760px, calc(100vw - 42px)); border: 0; border-radius: 10px; padding: 0; color: var(--text); box-shadow: 0 24px 70px rgba(24, 34, 53, .28); }
            .provider-line-dialog { width: min(1180px, calc(100vw - 42px)); }
            .supply-detail-dialog::backdrop { background: rgba(16, 43, 58, .38); }
            .supply-detail-card { position: relative; padding: 20px; display: grid; gap: 14px; background: #fff; }
            .supply-detail-card h3, .supply-detail-card p { margin: 0; }
            .supply-detail-close { position: absolute; top: 10px; right: 12px; width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 999px; background: #fff; color: var(--danger); cursor: pointer; font-size: 1.25rem; font-weight: 900; line-height: 1; }
            .supply-detail-close:hover { background: #fdecec; border-color: #f1b8b4; }
            .supply-detail-lines { max-height: min(56vh, 430px); overflow: auto; display: grid; border: 1px solid var(--line); border-radius: 8px; }
            .supply-detail-line { display: grid; grid-template-columns: 90px 90px minmax(180px, 1fr) 120px 120px; gap: 10px; align-items: start; padding: 10px 12px; border-bottom: 1px solid var(--line); font-size: .9rem; }
            .supply-detail-line:last-child { border-bottom: 0; }
            .supply-detail-head { position: sticky; top: 0; z-index: 2; background: #f8fbff; color: var(--muted); font-size: .78rem; font-weight: 900; text-transform: uppercase; }
            .supply-detail-line strong { display: block; }
            .supply-detail-line small { display: block; margin-top: 2px; }
            .provider-line-list { max-height: min(58vh, 480px); }
            .provider-line-list table { min-width: 980px; }
            .provider-catalog-panel { gap: 16px; }
            .provider-catalog-summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; cursor: pointer; list-style: none; }
            .provider-catalog-summary::-webkit-details-marker { display: none; }
            .provider-catalog-summary h2, .provider-catalog-summary p { margin: 0; }
            .provider-catalog-summary h2 { margin-bottom: 8px; }
            .provider-catalog-toggle { width: 38px; height: 38px; min-height: 38px; padding: 0; font-size: 1.3rem; font-weight: 900; }
            .provider-catalog-toggle::before { content: '+'; }
            .provider-catalog-panel[open] .provider-catalog-toggle::before { content: '-'; }
            .provider-catalog-list { max-height: min(56vh, 460px); }
            .provider-catalog-list table { min-width: 1180px; }
            .provider-lines-toolbar { display: grid; justify-items: start; gap: 8px; }
            .provider-lines-toolbar input { width: min(100%, 280px); }
            .provider-lines-button-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
            .provider-line-create-inline { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
            .provider-category-cell { display: flex; align-items: center; gap: 12px; }
            .provider-line-toggle { width: 34px; height: 34px; min-height: 34px; padding: 0; font-size: 1.15rem; font-weight: 900; }
            .provider-subcategory-row td { background: #f8fbff; }
            .provider-subcategory-cell { display: flex; align-items: center; gap: 10px; padding-left: 68px; font-weight: 850; position: relative; }
            .provider-subcategory-cell::before { content: ''; position: absolute; left: 32px; top: 50%; width: 24px; height: 1px; background: #b8c7da; }
            .provider-subcategory-cell::after { content: ''; position: absolute; left: 32px; top: -24px; bottom: 50%; width: 1px; background: #b8c7da; }
            .provider-subcategory-create-row td { padding-left: 96px; }
            .provider-subcategory-create-form { display: flex; align-items: end; gap: 12px; flex-wrap: wrap; }
            .provider-subcategory-create-form label { min-width: min(360px, 100%); }
            .alert { padding: 12px 14px; border-radius: 8px; border: 1px solid #b7dfca; background: #e8f6ee; color: var(--success); font-weight: 700; }
            .error-list { padding: 12px 14px; border-radius: 8px; border: 1px solid #f1b8b4; background: #fdecec; color: var(--danger); }
            .inline-form { display: inline; }
            .item-actions { display: flex; gap: 8px; flex-wrap: wrap; }
            .attachment-pill { display: inline-flex; align-items: center; gap: 10px; border: 1px solid var(--line); background: #f8fbff; color: var(--primary-strong); border-radius: 8px; padding: 7px 10px; font-weight: 800; }
            .attachment-pill span { border-radius: 999px; background: #e5f3f7; padding: 3px 8px; font-size: .78rem; }
            .company-logo-thumb { width: 74px; height: 38px; border-radius: 6px; border: 1px solid var(--line); display: inline-grid; place-items: center; background: var(--primary); color: #fff; font-weight: 850; box-shadow: inset 0 0 0 3px #fff; }
            .service-month-panel { padding: 0; overflow: visible; }
            .service-month-panel .panel-header { padding: 14px 20px; border-bottom: 1px solid var(--line); }
            .service-month-header { align-items: center; }
            .service-month-header-compact { display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: nowrap; }
            .month-toggle { min-width: 0; border: 0; padding: 0; display: inline-flex; align-items: center; gap: 12px; color: var(--text); background: transparent; text-align: left; cursor: pointer; }
            .month-toggle-sign { width: 34px; height: 34px; flex: 0 0 auto; display: grid; place-items: center; border: 1px solid var(--line); border-radius: 8px; background: #fff; color: var(--primary-strong); font-size: 1.35rem; font-weight: 900; line-height: 1; }
            .month-toggle-copy { display: grid; gap: 4px; min-width: 230px; }
            .month-toggle h2, .service-month-header h2 { margin: 0; display: flex; align-items: baseline; gap: 10px; flex-wrap: wrap; }
            .month-total-inline { color: var(--primary-strong); font-size: .98em; }
            .month-summary-metrics { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px; flex: 1 1 auto; min-width: 680px; }
            .compact-metric { padding: 8px 10px; min-height: 0; box-shadow: none; }
            .compact-metric span { font-size: .72rem; line-height: 1.1; }
            .compact-metric strong { font-size: .98rem; line-height: 1.15; margin-top: 3px; }
            .compact-metric small { display: none; }
            .service-month-detail[hidden] { display: none; }
            .service-month-detail { display: grid; gap: 0; }
            .month-totals-grid { display: grid; grid-template-columns: repeat(3, minmax(180px, 1fr)); gap: 12px; padding: 0 20px 18px; border-bottom: 1px solid var(--line); }
            .month-totals-grid .metric-card { padding: 14px 16px; box-shadow: none; }
            .month-totals-grid .metric-card strong { font-size: 1.28rem; }
            .service-month-scroll { max-height: min(68vh, calc(100vh - 240px)); overflow: auto; overscroll-behavior: contain; scrollbar-gutter: stable; }
            .service-month-scroll table { min-width: 1440px; }
            .service-month-scroll th, .service-month-scroll td { font-size: .84rem; padding: 10px; }
            .service-month-scroll .service-month-table thead th { position: sticky; top: 0; z-index: 8; background: #fff; box-shadow: 0 1px 0 var(--line); }
            .service-month-table {
                --sticky-title-width: 180px;
                --sticky-location-width: 130px;
                --sticky-service-width: 120px;
                border-collapse: separate;
                border-spacing: 0;
            }
            .service-month-table th:nth-child(2),
            .service-month-table td:nth-child(2),
            .service-month-table th:nth-child(3),
            .service-month-table td:nth-child(3),
            .service-month-table th:nth-child(4),
            .service-month-table td:nth-child(4) {
                background: #fff;
                position: sticky;
                z-index: 6;
            }
            .service-month-table th:nth-child(2),
            .service-month-table td:nth-child(2) {
                left: 0;
                min-width: var(--sticky-title-width);
                width: var(--sticky-title-width);
            }
            .service-month-table th:nth-child(3),
            .service-month-table td:nth-child(3) {
                left: var(--sticky-title-width);
                min-width: var(--sticky-location-width);
                width: var(--sticky-location-width);
                max-width: var(--sticky-location-width);
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .service-month-table th:nth-child(4),
            .service-month-table td:nth-child(4) {
                box-shadow: 10px 0 14px -16px rgba(16, 43, 58, .45);
                left: calc(var(--sticky-title-width) + var(--sticky-location-width));
                min-width: var(--sticky-service-width);
                width: var(--sticky-service-width);
                max-width: var(--sticky-service-width);
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .service-month-table thead th:nth-child(2),
            .service-month-table thead th:nth-child(3),
            .service-month-table thead th:nth-child(4) { z-index: 10; }
            .service-row-locked td:nth-child(2),
            .service-row-locked td:nth-child(3),
            .service-row-locked td:nth-child(4) { background: #f8fbff; }
            .service-row-locked td { background: #f8fbff; color: var(--muted); }
            .service-row-locked .attachment-pill { opacity: .8; }
            .amount-edit { min-width: 150px; display: flex; align-items: center; gap: 6px; flex-wrap: nowrap; }
            .amount-edit input { width: 88px; min-height: 34px; padding: 6px 8px; }
            .service-actions { min-width: 230px; flex-wrap: nowrap; }
            .process-strip { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 18px; }
            .process-strip span { border: 1px solid var(--line); background: #e5f3f7; color: var(--primary-strong); border-radius: 999px; padding: 8px 12px; }
            .audit-list { display: grid; gap: 10px; margin: 0; padding: 0; list-style: none; }
            .audit-list li { padding: 12px; border: 1px solid var(--line); border-radius: 8px; background: #fff; }
            .audit-list small { color: var(--muted); display: block; margin-top: 4px; }
            .compact-metrics .metric-card { padding: 14px 16px; }
            .compact-metrics .metric-card strong { font-size: 1.5rem; }
            .compact-create { padding: 0; overflow: visible; }
            .compact-create > summary { cursor: pointer; list-style: none; padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .compact-create > summary::-webkit-details-marker { display: none; }
            .compact-create > summary small { display: block; color: var(--muted); font-weight: 500; margin-top: 3px; }
            .compact-create > form { padding: 0 20px 20px; }
            .user-filters { display: grid; grid-template-columns: minmax(240px, 1.5fr) minmax(180px, .8fr) minmax(160px, .7fr) auto; align-items: end; gap: 12px; }
            .filter-actions, .pagination-bar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
            .compact-table td { vertical-align: middle; }
            .compact-table td small { display: block; color: var(--muted); margin-top: 3px; }
            .role-chip { display: inline-flex; align-items: center; border: 1px solid var(--line); border-radius: 999px; padding: 5px 10px; background: #f8fbff; color: var(--primary-strong); font-weight: 800; }
            .row-actions { min-width: 230px; }
            .row-actions > * { margin-right: 6px; }
            .editor-row td { background: #f8fbff; padding: 16px; }
            .editor-row form { padding: 16px; border: 1px solid var(--line); border-radius: 8px; background: #fff; }
            .checkbox-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
            .checkbox-grid label { min-height: 36px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; border: 1px solid var(--line); border-radius: 8px; background: #fff; font-weight: 650; }
            .checkbox-grid input { width: auto; min-height: 0; }
            .buyer-subrole-box .checkbox-grid { grid-template-columns: 1fr; }
            .checkbox-inline { min-height: 42px; display: flex; align-items: center; gap: 8px; }
            .checkbox-inline input { width: auto; min-height: 0; }
            .company-selector { border: 1px solid var(--line); border-radius: 8px; background: #f8fbff; padding: 12px; display: grid; gap: 10px; }
            .company-selector-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
            .company-selector-header label { margin: 0; font-weight: 850; }
            .company-selector-header span { color: var(--primary-strong); font-size: .82rem; font-weight: 850; white-space: nowrap; }
            .company-selector-search { min-height: 40px; }
            .company-selector-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
            .company-selector-list { max-height: 230px; overflow: auto; display: grid; gap: 6px; padding-right: 4px; }
            .company-selector-option { min-height: 38px; padding: 8px 10px; border: 1px solid var(--line); border-radius: 8px; background: #fff; display: grid; grid-template-columns: auto minmax(0, 1fr); align-items: center; gap: 9px; font-weight: 700; }
            .company-selector-option.with-warehouses { grid-template-columns: 1fr; align-items: stretch; gap: 8px; height: auto; overflow: visible; }
            .company-selector-main { display: grid; grid-template-columns: auto minmax(0, 1fr); align-items: center; gap: 9px; margin: 0; }
            .warehouse-selector-list { display: flex; flex-wrap: wrap; gap: 6px; padding-left: 25px; padding-top: 2px; overflow: visible; }
            .warehouse-selector-list label { min-height: 28px; padding: 5px 8px; border: 1px solid var(--line); border-radius: 999px; background: #f8fbff; color: var(--text); display: inline-flex; align-items: center; gap: 6px; font-size: .78rem; font-weight: 750; }
            .warehouse-selector-list input { width: 13px; min-height: 13px; }
            .company-selector-option input { width: 16px; min-height: 16px; }
            .company-selector-option span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
            .company-selector-option.with-warehouses span { white-space: normal; }
            .company-selector-option:has(.company-checkbox:checked) { border-color: #8bd8b4; background: #effbf5; color: #11613b; }
            .company-selector-option.supply-warehouse-option { border-color: #8fc6e7; background: #f0f9ff; color: #0e536a; }
            .company-selector-option.supply-warehouse-option:has(.company-checkbox:checked) { border-color: #176b87; background: #e5f3f7; color: #0e536a; }
            .companies-box:has(.auth-split-selector) { grid-column: 1 / -1; }
            .auth-split-selector { display: grid; grid-template-columns: minmax(320px, .8fr) minmax(620px, 1.6fr); gap: 18px; align-items: stretch; width: 100%; }
            .auth-selector-pane { min-width: 0; border: 1px solid var(--line); border-radius: 8px; background: #fff; padding: 16px; display: grid; gap: 12px; align-content: start; }
            .auth-split-selector.company-selector { background: transparent; border: 0; padding: 0; }
            .auth-company-list { max-height: 420px; min-height: 340px; }
            .auth-company-option { grid-template-columns: auto minmax(0, 1fr); }
            .auth-warehouse-scroll { max-height: 480px; min-height: 400px; overflow: auto; border: 1px solid var(--line); border-radius: 8px; }
            .auth-warehouse-table { min-width: 900px; }
            .auth-warehouse-table th, .auth-warehouse-table td { padding: 9px 10px; vertical-align: top; }
            .auth-warehouse-table th { position: sticky; top: 0; z-index: 2; background: #f8fbff; }
            .auth-table-check { display: flex; grid-template-columns: auto minmax(0, 1fr); align-items: flex-start; gap: 8px; color: var(--text); font-weight: 760; }
            .auth-table-check input { width: 14px; min-height: 14px; margin-top: 2px; }
            .auth-table-check span { white-space: normal; }
            .auth-supply-warehouse-row td { background: #f0f9ff; }
            .reimbursement-panel { min-height: auto !important; }
            .reimbursement-form { max-width: 1180px; gap: 10px; }
            .reimbursement-form .grid-3 { grid-template-columns: minmax(240px, 360px) minmax(240px, 360px) minmax(150px, 220px); justify-content: start; gap: 10px; }
            .reimbursement-form .grid-2 { grid-template-columns: repeat(2, minmax(240px, 380px)); justify-content: start; gap: 10px; }
            .reimbursement-form label { gap: 5px; font-size: .9rem; }
            .reimbursement-form :is(input, select, textarea) { min-height: 34px; padding: 7px 10px; font-size: .9rem; }
            .reimbursement-form input[type="file"] { padding: 5px 9px; }
            .reimbursement-form textarea { min-height: 62px; }
            .reimbursement-concept { max-width: 760px; }
            .credit-control { display: grid; grid-template-columns: auto minmax(120px, 1fr); align-items: end; gap: 12px; }
            .order-total-actions { align-items: center; }
            .total-preview { margin-left: auto; display: inline-flex; align-items: center; gap: 12px; padding: 10px 14px; border: 1px solid var(--line); border-radius: 8px; background: #f8fbff; }
            .total-preview span { color: var(--muted); font-weight: 800; }
            .total-preview strong { color: var(--primary-strong); font-size: 1.15rem; }
            .empty-state { text-align: center; color: var(--muted); padding: 28px 12px; }
            .pagination-bar { justify-content: space-between; color: var(--muted); }
            .button.disabled { opacity: .55; cursor: default; }
            .confirm-dialog { width: min(420px, calc(100vw - 36px)); border: 0; border-radius: 8px; padding: 0; box-shadow: 0 24px 60px rgba(24, 34, 53, .24); }
            .confirm-dialog::backdrop { background: rgba(16, 43, 58, .32); }
            .confirm-card { padding: 20px; display: grid; gap: 14px; background: #fff; }
            .confirm-card h3, .confirm-card p { margin: 0; }
            .confirm-card p { color: var(--muted); }
            .table-export-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-left: auto; }
            .table-export-actions .button { white-space: nowrap; }
            .panel-header .table-export-actions { justify-content: flex-end; }
            .panel-header-title { display: grid; gap: 4px; min-width: 220px; }
            .panel-header-title > :is(h2, h3, p) { margin: 0; }
            @supports selector(:has(*)) {
                .panel:has(.table-scroll) { max-height: calc(100vh - 130px); overflow: hidden; }
                .panel:has(.table-scroll) .table-scroll { min-height: 0; }
                .panel.generator-detail-panel { max-height: none; overflow: visible; }
                .panel.materials-explosion-panel { max-height: none; overflow: visible; }
                .panel:has(> form .auth-split-selector) { max-height: none; overflow: visible; }
                .service-month-panel:has(.service-month-detail:not([hidden])) { max-height: calc(100vh - 120px); }
            }
            @media (max-width: 900px) {
                .login-shell, .app-shell { grid-template-columns: 1fr; }
                .sidebar { position: static; height: auto; }
                .metrics-grid, .month-totals-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                .grid-2, .grid-3, .grid-4, .user-filters, .checkbox-grid, .auth-split-selector { grid-template-columns: 1fr; }
                .construction-project-tile { flex-basis: 182px; }
            }
            @media (max-width: 560px) {
                .metrics-grid, .month-totals-grid { grid-template-columns: 1fr; }
                .topbar { align-items: flex-start; flex-direction: column; }
                .topbar-right { width: 100%; justify-content: space-between; margin-left: 0; }
                .construction-carousel-panel { padding: 16px; }
                .construction-carousel-shell { grid-template-columns: 30px minmax(0, 1fr) 30px; gap: 8px; }
                .construction-carousel-nav { width: 30px; height: 30px; min-height: 30px; }
                .construction-project-tile { flex-basis: 166px; min-height: 116px; padding: 10px; }
                .generator-levels-panel, .generator-detail-panel { padding: 14px; }
                .generator-level-shell { grid-template-columns: 28px minmax(0, 1fr) 28px; gap: 7px; }
                .generator-level-nav { width: 28px; height: 28px; min-height: 28px; }
                .generator-level-card { flex-basis: 164px; }
                .generator-tabs { overflow-x: auto; }
                .generator-tab { white-space: nowrap; }
                .generator-history-item { align-items: flex-start; flex-direction: column; }
                .materials-explosion-header { align-items: stretch; flex-direction: column; }
                .materials-explosion-actions { justify-content: flex-start; }
                .materials-category-heading { grid-template-columns: 32px minmax(0, 1fr) 24px; gap: 7px; }
                .materials-category-heading > .materials-square-toggle { grid-column: 1; grid-row: 1 / 3; }
                .materials-category-heading > strong { grid-column: 2; grid-row: 1; }
                .materials-category-heading > .materials-count-badge { grid-column: 2; grid-row: 2; justify-self: start; }
                .materials-category-heading > .materials-chevron { grid-column: 3; grid-row: 1 / 3; }
                .materials-category-note { margin: 6px 10px; }
                .materials-concept-scroll { max-width: calc(100% - 14px); margin-left: 14px; }
                .materials-concept-footer { align-items: flex-start; flex-direction: column; gap: 4px; }
                .labor-budget-grid { grid-template-columns: 1fr; }
                .labor-toolbar { align-items: flex-start; }
                .labor-tab { width: 100%; }
                .unit-price-panel { grid-template-rows: auto; }
                .unit-price-filters { grid-template-columns: 1fr; }
                .unit-price-filter-actions .button { flex: 1 1 0; }
                .unit-price-pagination { align-items: flex-start; flex-direction: column; }
            }
            .ejemplo{
                color:red;
            }
        </style>
    </head>
    <body>
        <?php echo $__env->yieldContent('body'); ?>
        <script id="global-table-tools">
            document.addEventListener('DOMContentLoaded', () => {
                const normalize = (value) => (value || '').replace(/\s+/g, ' ').trim();
                const preserveSidebarScroll = () => {
                    const navList = document.querySelector('.nav-list');
                    if (!navList) return;

                    const storageKey = 'oc.sidebar.scrollTop';
                    const save = () => {
                        try {
                            sessionStorage.setItem(storageKey, String(navList.scrollTop));
                        } catch (error) {
                            // Navegadores con storage bloqueado simplemente usan el comportamiento normal.
                        }
                    };
                    const restore = () => {
                        try {
                            const saved = Number(sessionStorage.getItem(storageKey));
                            if (!Number.isNaN(saved)) navList.scrollTop = saved;
                        } catch (error) {
                            // Sin storage disponible no hay posicion previa que restaurar.
                        }
                    };

                    requestAnimationFrame(restore);
                    window.addEventListener('load', () => requestAnimationFrame(restore));
                    window.addEventListener('pagehide', save);
                    window.addEventListener('beforeunload', save);
                    navList.addEventListener('scroll', save, { passive: true });
                    navList.addEventListener('click', (event) => {
                        if (event.target.closest('a[href]')) save();
                    }, true);
                };
                preserveSidebarScroll();
                const setupInlineNavigationToggles = () => {
                    document.querySelectorAll('.nav-box-construction-materiales > .nav-button.active').forEach((toggle) => {
                        const submenu = toggle.nextElementSibling;
                        if (!submenu?.classList.contains('nav-inline-flow')) return;

                        toggle.setAttribute('aria-expanded', 'true');
                        toggle.addEventListener('click', (event) => {
                            event.preventDefault();
                            const navList = toggle.closest('.nav-list');
                            const scrollTop = navList?.scrollTop ?? 0;

                            if (submenu.hidden) {
                                submenu.hidden = false;
                                if (navList) navList.style.paddingBottom = '';
                            } else {
                                const submenuStyles = window.getComputedStyle(submenu);
                                const parentStyles = window.getComputedStyle(submenu.parentElement);
                                const submenuHeight = submenu.getBoundingClientRect().height
                                    + Number.parseFloat(submenuStyles.marginTop || 0)
                                    + Number.parseFloat(submenuStyles.marginBottom || 0)
                                    + Number.parseFloat(parentStyles.rowGap || 0);
                                if (navList) navList.style.paddingBottom = `${submenuHeight}px`;
                                submenu.hidden = true;
                            }

                            if (navList) navList.scrollTop = scrollTop;
                            toggle.setAttribute('aria-expanded', String(!submenu.hidden));
                        });
                    });
                };
                setupInlineNavigationToggles();
                const setupProviderSubcategorySelectors = () => {
                    document.querySelectorAll('[data-provider-line-select]').forEach((lineSelect) => {
                        const form = lineSelect.closest('form');
                        const subcategorySelect = form?.querySelector('[data-provider-subcategory-select]');
                        if (!subcategorySelect) return;

                        const sync = () => {
                            const selectedLine = String(lineSelect.value || '');
                            let hasVisibleSubcategory = false;

                            Array.from(subcategorySelect.options).forEach((option) => {
                                if (!option.value) {
                                    option.hidden = false;
                                    option.disabled = false;
                                    return;
                                }

                                const matchesLine = option.dataset.lineId === selectedLine;
                                option.hidden = !matchesLine;
                                option.disabled = !matchesLine;
                                if (matchesLine) hasVisibleSubcategory = true;
                            });

                            if (subcategorySelect.selectedOptions[0]?.disabled) {
                                subcategorySelect.value = '';
                            }

                            subcategorySelect.disabled = !hasVisibleSubcategory;
                            if (!hasVisibleSubcategory) subcategorySelect.value = '';
                        };

                        lineSelect.addEventListener('change', sync);
                        sync();
                    });
                };
                setupProviderSubcategorySelectors();
                const reportTables = () => Array.from(document.querySelectorAll('.panel table')).filter((table) => {
                    return !table.matches('[data-no-column-tools]')
                        && !table.closest('form')
                        && table.querySelector('thead th')
                        && table.querySelector('tbody tr');
                });

                const closeColumnFilters = (except = null) => {
                    document.querySelectorAll('.column-filter[open]').forEach((filter) => {
                        if (filter !== except) filter.removeAttribute('open');
                    });
                };

                document.addEventListener('click', (event) => {
                    if (!event.target.closest('.column-filter')) closeColumnFilters();
                });

                const positionColumnFilterPanel = (filter) => {
                    const panel = filter.querySelector('.column-filter-panel');
                    const summary = filter.querySelector('summary');
                    if (!panel || !summary || !filter.open) return;
                    const rect = summary.getBoundingClientRect();
                    const width = panel.offsetWidth || 230;
                    const height = panel.offsetHeight || 280;
                    const margin = 12;
                    const left = Math.min(Math.max(margin, rect.left), Math.max(margin, window.innerWidth - width - margin));
                    let top = rect.bottom + 6;
                    if (top + height > window.innerHeight - margin) {
                        top = Math.max(margin, rect.top - height - 6);
                    }
                    panel.style.left = left + 'px';
                    panel.style.top = top + 'px';
                };

                const repositionOpenColumnFilters = () => {
                    document.querySelectorAll('.column-filter[open]').forEach(positionColumnFilterPanel);
                };

                const closeStatusMenus = (except = null) => {
                    document.querySelectorAll('.status-menu[open]').forEach((menu) => {
                        if (menu !== except) menu.removeAttribute('open');
                    });
                };

                const positionStatusMenuPanel = (menu) => {
                    const panel = menu.querySelector('.status-menu-panel');
                    const summary = menu.querySelector('summary');
                    if (!panel || !summary || !menu.open) return;
                    const rect = summary.getBoundingClientRect();
                    const width = panel.offsetWidth || 150;
                    const height = panel.offsetHeight || 120;
                    const margin = 12;
                    const left = Math.min(Math.max(margin, rect.left), Math.max(margin, window.innerWidth - width - margin));
                    let top = rect.bottom + 6;
                    if (top + height > window.innerHeight - margin) {
                        top = Math.max(margin, rect.top - height - 6);
                    }
                    panel.style.left = left + 'px';
                    panel.style.top = top + 'px';
                };

                const repositionOpenStatusMenus = () => {
                    document.querySelectorAll('.status-menu[open]').forEach(positionStatusMenuPanel);
                };

                const initStatusMenus = () => {
                    document.querySelectorAll('.status-menu').forEach((menu) => {
                        if (menu.dataset.floatingReady) return;
                        menu.dataset.floatingReady = 'true';
                        menu.addEventListener('toggle', () => {
                            if (menu.open) {
                                closeStatusMenus(menu);
                                requestAnimationFrame(() => positionStatusMenuPanel(menu));
                            }
                        });
                        menu.addEventListener('click', (event) => event.stopPropagation());
                    });
                };

                window.addEventListener('resize', () => { repositionOpenColumnFilters(); repositionOpenStatusMenus(); });
                document.addEventListener('scroll', () => { repositionOpenColumnFilters(); repositionOpenStatusMenus(); }, true);
                document.addEventListener('click', (event) => {
                    if (!event.target.closest('.status-menu')) closeStatusMenus();
                });

                const headerText = (cell) => {
                    const span = cell.querySelector('.th-filter > span, :scope > span');
                    return normalize(span?.textContent || cell.textContent.replace(/Filtro\s*\^?|Filtro\s*v?/gi, ''));
                };
                const cellText = (cell) => normalize(cell?.dataset?.filterValue || cell?.dataset?.excelFilterValue || cell?.innerText || cell?.textContent);

                const enhanceFilters = (table) => {
                    if (table.dataset.globalFiltersReady || table.matches('[data-excel-filter-table]') || table.matches('[data-column-filter-table]')) return;
                    const rows = Array.from(table.querySelectorAll('tbody tr')).filter((row) => row.cells.length > 1 && !row.querySelector('[colspan]'));
                    const headers = Array.from(table.querySelectorAll('thead th'));
                    if (!headers.length) return;

                    table.dataset.globalFiltersReady = 'true';
                    rows.forEach((row) => row.dataset.filterRow = 'true');
                    const filters = new Map();

                    const applyFilters = () => {
                        rows.forEach((row) => {
                            const visible = headers.every((header, column) => {
                                const selected = filters.get(column);
                                if (!selected) return true;
                                return selected.has(cellText(row.cells[column]));
                            });
                            row.hidden = !visible;
                        });
                    };

                    headers.forEach((header, column) => {
                        if (header.hasAttribute('data-no-filter')) return;
                        const label = headerText(header);
                        const values = [...new Set(rows.map((row) => cellText(row.cells[column])).filter(Boolean))]
                            .sort((a, b) => a.localeCompare(b, 'es', { numeric: true }));

                        const wrapper = document.createElement('div');
                        wrapper.className = 'th-filter';
                        const title = document.createElement('span');
                        title.textContent = label;
                        const filter = document.createElement('details');
                        filter.className = 'column-filter';
                        const summary = document.createElement('summary');
                        summary.textContent = 'Filtro';
                        summary.title = 'Filtrar esta columna';
                        summary.setAttribute('aria-label', 'Filtrar esta columna');
                        const panel = document.createElement('div');
                        panel.className = 'column-filter-panel';
                        const search = table.hasAttribute('data-filter-search')
                            ? document.createElement('input')
                            : null;
                        if (search) {
                            search.type = 'search';
                            search.className = 'column-filter-search';
                            search.placeholder = 'Buscar';
                            search.setAttribute('aria-label', `Buscar en ${label}`);
                        }
                        const options = document.createElement('div');
                        options.className = 'column-filter-options';
                        let selectedValues = new Set(values);

                        const selectAllOption = document.createElement('label');
                        selectAllOption.className = 'column-filter-option';
                        const selectAllCheckbox = document.createElement('input');
                        selectAllCheckbox.type = 'checkbox';
                        selectAllCheckbox.checked = true;
                        selectAllOption.append(selectAllCheckbox, document.createTextNode('(Seleccionar todo)'));
                        options.append(selectAllOption);

                        values.forEach((value) => {
                            const option = document.createElement('label');
                            option.className = 'column-filter-option';
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.value = value;
                            checkbox.checked = true;
                            option.append(checkbox, document.createTextNode(value));
                            options.append(option);
                        });

                        if (!values.length) {
                            selectAllOption.hidden = true;
                            const empty = document.createElement('div');
                            empty.className = 'column-filter-empty';
                            empty.textContent = 'Sin datos para filtrar';
                            options.append(empty);
                        }

                        const noMatches = document.createElement('div');
                        noMatches.className = 'column-filter-empty';
                        noMatches.textContent = 'Sin coincidencias';
                        noMatches.hidden = true;
                        options.append(noMatches);

                        const actions = document.createElement('div');
                        actions.className = 'column-filter-actions';
                        const accept = document.createElement('button');
                        accept.type = 'button';
                        accept.className = 'button ghost small';
                        accept.textContent = 'Aceptar';
                        const cancel = document.createElement('button');
                        cancel.type = 'button';
                        cancel.className = 'button ghost small';
                        cancel.textContent = 'Cancelar';
                        actions.append(accept, cancel);
                        if (search) panel.append(search);
                        panel.append(options, actions);
                        filter.append(summary, panel);
                        wrapper.append(title, filter);
                        header.textContent = '';
                        header.append(wrapper);

                        const valueCheckboxes = () => Array.from(options.querySelectorAll('input[type="checkbox"]')).filter((input) => input !== selectAllCheckbox);
                        const syncSelectAll = () => {
                            const boxes = valueCheckboxes();
                            const checked = boxes.filter((input) => input.checked).length;
                            selectAllCheckbox.checked = boxes.length > 0 && checked === boxes.length;
                            selectAllCheckbox.indeterminate = checked > 0 && checked < boxes.length;
                        };
                        const restoreSelection = () => {
                            valueCheckboxes().forEach((input) => input.checked = selectedValues.has(input.value));
                            syncSelectAll();
                        };
                        const resetSearch = () => {
                            if (!search) return;
                            search.value = '';
                            valueCheckboxes().forEach((input) => {
                                input.closest('.column-filter-option').hidden = false;
                            });
                            noMatches.hidden = true;
                        };
                        const updateFilteredState = () => {
                            summary.classList.toggle('is-filtered', selectedValues.size !== values.length);
                        };

                        search?.addEventListener('input', () => {
                            const query = normalize(search.value).toLocaleLowerCase('es-MX');
                            let visibleOptions = 0;
                            valueCheckboxes().forEach((input) => {
                                const matches = !query || normalize(input.value).toLocaleLowerCase('es-MX').includes(query);
                                input.closest('.column-filter-option').hidden = !matches;
                                if (matches) visibleOptions += 1;
                            });
                            noMatches.hidden = visibleOptions > 0;
                        });

                        filter.addEventListener('toggle', () => {
                            if (filter.open) {
                                closeColumnFilters(filter);
                                resetSearch();
                                restoreSelection();
                                requestAnimationFrame(() => {
                                    positionColumnFilterPanel(filter);
                                    search?.focus();
                                });
                            }
                        });
                        filter.addEventListener('click', (event) => event.stopPropagation());
                        selectAllCheckbox.addEventListener('change', () => {
                            valueCheckboxes().forEach((input) => input.checked = selectAllCheckbox.checked);
                            syncSelectAll();
                        });
                        options.addEventListener('change', (event) => {
                            if (event.target !== selectAllCheckbox) syncSelectAll();
                        });
                        accept.addEventListener('click', () => {
                            selectedValues = new Set(valueCheckboxes().filter((input) => input.checked).map((input) => input.value));
                            if (selectedValues.size === values.length) filters.delete(column);
                            else filters.set(column, selectedValues);
                            updateFilteredState();
                            applyFilters();
                            filter.removeAttribute('open');
                        });
                        cancel.addEventListener('click', () => {
                            restoreSelection();
                            filter.removeAttribute('open');
                        });
                    });
                };

                const exportText = (cell) => normalize(cell?.dataset?.filterValue || cell?.dataset?.excelFilterValue || cell?.innerText || cell?.textContent);
                const escapeHtml = (value) => String(value ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
                const tableHtml = (table, title) => {
                    const headers = Array.from(table.querySelectorAll('thead th')).map(headerText);
                    const rows = Array.from(table.querySelectorAll('tbody tr'))
                        .filter((row) => !row.hidden && row.cells.length > 1 && !row.querySelector('[colspan]'))
                        .map((row) => Array.from(row.cells).map(exportText));
                    let html = `<tr><th colspan="${Math.max(headers.length, 1)}">${escapeHtml(title)}</th></tr>`;
                    html += '<tr>' + headers.map((header) => `<th>${escapeHtml(header)}</th>`).join('') + '</tr>';
                    rows.forEach((row) => {
                        html += '<tr>' + row.map((cell) => `<td>${escapeHtml(cell)}</td>`).join('') + '</tr>';
                    });
                    return html;
                };
                const slug = (value) => normalize(value).toLowerCase().replace(/[^a-z0-9Ã¡Ã©Ã­Ã³ÃºÃ±]+/gi, '-').replace(/^-+|-+$/g, '') || 'reporte';
                const downloadExcel = (filename, tables) => {
                    const html = '<html><head><meta charset="UTF-8"></head><body>' + tables.map(({ table, title }) => `<table border="1">${tableHtml(table, title)}</table><br>`).join('') + '</body></html>';
                    const blob = new Blob(['\ufeff', html], { type: 'application/vnd.ms-excel;charset=utf-8;' });
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(blob);
                    link.download = filename.endsWith('.xls') ? filename : `${filename}.xls`;
                    document.body.append(link);
                    link.click();
                    URL.revokeObjectURL(link.href);
                    link.remove();
                };

                const addSectionExport = (panel, tables) => {
                    if (panel.dataset.sectionExportReady || panel.hasAttribute('data-no-section-export') || !tables.length || panel.classList.contains('service-month-panel')) return;
                    panel.dataset.sectionExportReady = 'true';
                    let header = panel.querySelector(':scope > .panel-header');
                    if (!header) {
                        header = document.createElement('div');
                        header.className = 'panel-header';
                        const titleBlock = document.createElement('div');
                        titleBlock.className = 'panel-header-title';
                        const heading = panel.querySelector(':scope > h2, :scope > h3');
                        if (heading) {
                            titleBlock.append(heading);
                            let sibling = titleBlock.firstElementChild.nextElementSibling;
                            while (sibling && sibling.tagName === 'P') {
                                const next = sibling.nextElementSibling;
                                titleBlock.append(sibling);
                                sibling = next;
                            }
                        }
                        if (titleBlock.children.length) header.append(titleBlock);
                        panel.prepend(header);
                    }
                    let actions = header.querySelector(':scope > .table-export-actions');
                    if (!actions) {
                        actions = document.createElement('div');
                        actions.className = 'table-export-actions';
                        header.append(actions);
                    }
                    const title = normalize(panel.querySelector(':scope h2, :scope h3')?.textContent || document.querySelector('.topbar h1')?.textContent || 'Seccion');
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'button ghost small';
                    button.textContent = 'Descargar Excel';
                    button.addEventListener('click', () => downloadExcel(slug(title), tables.map((table) => ({ table, title }))));
                    actions.append(button);
                };

                const addGeneralExport = () => {
                    if (document.body.dataset.generalExportReady) return;
                    const tables = reportTables();
                    if (!tables.length) return;
                    const topbar = document.querySelector('.topbar');
                    if (!topbar) return;
                    document.body.dataset.generalExportReady = 'true';
                    let right = topbar.querySelector('.topbar-right');
                    if (!right) {
                        right = document.createElement('div');
                        right.className = 'topbar-right';
                        topbar.append(right);
                    }
                    let actions = right.querySelector('.topbar-actions');
                    if (!actions) {
                        actions = document.createElement('div');
                        actions.className = 'topbar-actions';
                        right.prepend(actions);
                    }
                    const title = normalize(document.querySelector('.topbar h1')?.textContent || document.title || 'Reporte general');
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'button ghost';
                    button.textContent = 'Descargar con encabezados';
                    button.addEventListener('click', () => downloadExcel(slug(title + '-general'), reportTables().map((table) => ({
                        table,
                        title: normalize(table.closest('.panel')?.querySelector(':scope h2, :scope h3')?.textContent || title),
                    }))));
                    actions.prepend(button);
                };

                initStatusMenus();
                const tables = reportTables();
                tables.forEach((table) => enhanceFilters(table));
                Array.from(document.querySelectorAll('.panel')).forEach((panel) => {
                    const panelTables = tables.filter((table) => panel.contains(table));
                    addSectionExport(panel, panelTables);
                });
                addGeneralExport();
                document.querySelectorAll('[data-company-selector]').forEach((selector) => {
                    const search = selector.querySelector('.company-selector-search');
                    const options = Array.from(selector.querySelectorAll('[data-company-option]'));
                    const checkboxes = options.map((option) => option.querySelector('.company-checkbox')).filter(Boolean);
                    const allInputs = Array.from(selector.querySelectorAll('input[type="checkbox"]'));
                    const warehouseRows = Array.from(selector.querySelectorAll('[data-warehouse-row]'));
                    const emptyWarehouseRow = selector.querySelector('[data-empty-warehouses]');
                    const counter = selector.querySelector('[data-company-count]');
                    const warehouseCounter = selector.querySelector('[data-warehouse-count]');
                    const selectAll = selector.querySelector('[data-company-select-all]');
                    const clear = selector.querySelector('[data-company-clear]');
                    const companyCheckboxById = new Map(checkboxes.map((checkbox) => [String(checkbox.value), checkbox]));

                    const updateCount = () => {
                        const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
                        if (counter) counter.textContent = `${selected} seleccionadas`;
                    };

                    const syncWarehouses = () => {
                        let visibleRows = 0;
                        let checkedRows = 0;

                        warehouseRows.forEach((row) => {
                            const companyCheckbox = companyCheckboxById.get(String(row.dataset.companyId));
                            const visible = Boolean(companyCheckbox?.checked);
                            row.hidden = !visible;
                            row.querySelectorAll('input[type="checkbox"]').forEach((input) => {
                                input.disabled = !visible;
                                if (visible && input.checked) checkedRows += 1;
                            });
                            if (visible) visibleRows += 1;
                        });

                        if (emptyWarehouseRow) emptyWarehouseRow.hidden = visibleRows > 0;
                        if (warehouseCounter) warehouseCounter.textContent = `${checkedRows} seleccionados`;
                    };

                    const filterOptions = () => {
                        const term = normalize(search?.value || '').toLowerCase();
                        options.forEach((option) => {
                            option.hidden = term && !normalize(option.textContent).toLowerCase().includes(term);
                        });
                    };

                    allInputs.forEach((checkbox) => checkbox.addEventListener('change', () => {
                        updateCount();
                        syncWarehouses();
                    }));
                    search?.addEventListener('input', filterOptions);
                    selectAll?.addEventListener('click', () => {
                        allInputs.forEach((checkbox) => checkbox.checked = true);
                        updateCount();
                        syncWarehouses();
                    });
                    clear?.addEventListener('click', () => {
                        allInputs.forEach((checkbox) => checkbox.checked = false);
                        updateCount();
                        syncWarehouses();
                    });
                    updateCount();
                    syncWarehouses();
                });

                document.addEventListener('click', (event) => {
                    const openButton = event.target.closest('[data-supply-detail-open]');
                    if (openButton) {
                        const dialog = document.getElementById(openButton.dataset.supplyDetailOpen);
                        if (!dialog) return;
                        if (typeof dialog.showModal === 'function') {
                            dialog.showModal();
                        } else {
                            dialog.setAttribute('open', 'open');
                        }
                        return;
                    }

                    if (event.target.closest('[data-supply-detail-close]')) {
                        event.target.closest('[data-supply-detail-dialog]')?.close();
                        return;
                    }

                    if (event.target.matches('[data-supply-detail-dialog]')) {
                        event.target.close();
                    }
                });
            });
        </script>
        <script id="global-column-sorting">
            window.addEventListener('load', () => {
                const normalize = (value) => String(value || '').replace(/\s+/g, ' ').trim();
                const sortableRows = (table) => Array.from(table.tBodies[0]?.rows || [])
                    .filter((row) => row.cells.length > 1 && !row.querySelector('[colspan]'));
                const valueFor = (cell) => {
                    const date = cell?.dataset.filterDate || cell?.dataset.excelFilterDate;
                    if (date) return { type: 'date', value: date };
                    const text = normalize(cell?.dataset.filterValue || cell?.dataset.excelFilterValue || cell?.innerText || cell?.textContent);
                    const numeric = Number(text.replace(/[$,%\s,]/g, ''));
                    return text && Number.isFinite(numeric) && /^[\$\s,\d.%-]+$/.test(text)
                        ? { type: 'number', value: numeric }
                        : { type: 'text', value: text };
                };
                const addSortButtons = () => {
                    document.querySelectorAll('.panel table').forEach((table) => {
                        if (table.matches('[data-no-column-tools]') || table.closest('form') || table.dataset.sortButtonsReady) return;
                        const headers = Array.from(table.tHead?.rows[0]?.cells || []);
                        if (!headers.length) return;
                        table.dataset.sortButtonsReady = 'true';
                        headers.forEach((header, column) => {
                            if (header.hasAttribute('data-no-sort')) return;
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'column-sort-button';
                            button.textContent = '↕';
                            button.title = 'Ordenar por esta columna';
                            button.setAttribute('aria-label', 'Ordenar por esta columna');
                            button.addEventListener('click', (event) => {
                                event.preventDefault();
                                event.stopPropagation();
                                const direction = button.dataset.direction === 'asc' ? 'desc' : 'asc';
                                headers.forEach((cell) => cell.querySelector('.column-sort-button')?.classList.remove('is-active'));
                                headers.forEach((cell) => {
                                    const control = cell.querySelector('.column-sort-button');
                                    if (control && control !== button) {
                                        control.dataset.direction = '';
                                        control.textContent = '↕';
                                    }
                                });
                                button.dataset.direction = direction;
                                button.textContent = direction === 'asc' ? '↑' : '↓';
                                button.classList.add('is-active');
                                sortableRows(table)
                                    .sort((left, right) => {
                                        const first = valueFor(left.cells[column]);
                                        const second = valueFor(right.cells[column]);
                                        let comparison;
                                        if (first.type === 'number' && second.type === 'number') comparison = first.value - second.value;
                                        else comparison = String(first.value).localeCompare(String(second.value), 'es', { numeric: true, sensitivity: 'base' });
                                        return direction === 'asc' ? comparison : -comparison;
                                    })
                                    .forEach((row) => table.tBodies[0].appendChild(row));
                            });
                            const filterHeader = header.querySelector('.th-filter');
                            if (filterHeader) filterHeader.appendChild(button);
                            else header.appendChild(button);
                        });
                    });
                };
                addSortButtons();
            });
        </script>
    </body>
</html>
<?php /**PATH C:\laragon\www\Revision OC Software\resources\views/layouts/app.blade.php ENDPATH**/ ?>