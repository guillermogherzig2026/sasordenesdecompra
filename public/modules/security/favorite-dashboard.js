document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('.security-favorite-shell');
    const menuButton = shell?.querySelector('[data-security-mobile-menu]');
    const closeMenu = () => {
        shell?.classList.remove('is-menu-open');
        menuButton?.setAttribute('aria-expanded', 'false');
        menuButton?.setAttribute('aria-label', 'Abrir menú');
    };
    menuButton?.addEventListener('click', () => {
        const open = shell.classList.toggle('is-menu-open');
        menuButton.setAttribute('aria-expanded', String(open));
        menuButton.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
    });
    shell?.addEventListener('click', (event) => {
        if (shell.classList.contains('is-menu-open') && !event.target.closest('.sidebar, [data-security-mobile-menu]')) closeMenu();
    });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeMenu(); });

    const openDialog = (dialog) => {
        if (!dialog) return;
        if (typeof dialog.showModal === 'function') dialog.showModal();
        else dialog.setAttribute('open', 'open');
    };
    const closeDialog = (dialog) => {
        if (typeof dialog.close === 'function') dialog.close();
        else dialog.removeAttribute('open');
    };

    const picker = document.querySelector('[data-security-favorite-picker]');
    const search = picker?.querySelector('[data-security-favorite-search]');
    const favoritesDialog = document.querySelector('[data-security-favorites-dialog]');
    const favoritesForm = favoritesDialog?.querySelector('[data-security-favorites-form]');
    const favoritesSearch = favoritesDialog?.querySelector('[data-security-favorites-search]');
    const favoritesOptions = [...(favoritesDialog?.querySelectorAll('[data-security-favorites-option]') ?? [])];
    const favoritesCheckboxes = [...(favoritesDialog?.querySelectorAll('[data-security-favorites-checkbox]') ?? [])];
    const normalize = (value) => value.trim().normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    const updateFavoritesCount = () => {
        const selected = favoritesCheckboxes.filter((checkbox) => checkbox.checked).length;
        const count = favoritesDialog?.querySelector('[data-security-favorites-count]');
        if (count) count.textContent = `${selected} de ${favoritesCheckboxes.length} empresas seleccionadas`;
    };
    const filterFavorites = () => {
        const query = normalize(favoritesSearch?.value ?? '');
        let visible = 0;
        favoritesOptions.forEach((option) => {
            option.hidden = !normalize(option.dataset.search).includes(query);
            if (!option.hidden) visible += 1;
        });
        const empty = favoritesDialog?.querySelector('[data-security-favorites-no-results]');
        if (empty) empty.hidden = visible > 0 || favoritesOptions.length === 0;
    };
    document.querySelector('[data-security-favorites-manage]')?.addEventListener('click', () => {
        if (picker) picker.open = false;
        openDialog(favoritesDialog);
        favoritesSearch?.focus({ preventScroll: true });
    });
    favoritesCheckboxes.forEach((checkbox) => checkbox.addEventListener('change', updateFavoritesCount));
    favoritesSearch?.addEventListener('input', filterFavorites);
    favoritesDialog?.querySelectorAll('[data-security-favorites-close]').forEach((button) => {
        button.addEventListener('click', () => closeDialog(favoritesDialog));
    });
    favoritesDialog?.addEventListener('click', (event) => {
        if (event.target === favoritesDialog) closeDialog(favoritesDialog);
    });
    favoritesDialog?.addEventListener('close', () => {
        favoritesForm?.reset();
        if (favoritesSearch) favoritesSearch.value = '';
        filterFavorites();
        updateFavoritesCount();
    });
    updateFavoritesCount();
    if (favoritesDialog?.hasAttribute('data-auto-open')) openDialog(favoritesDialog);
    search?.addEventListener('input', () => {
        const query = normalize(search.value);
        picker.querySelectorAll('[data-security-favorite-option]').forEach((option) => {
            const name = normalize(option.dataset.search);
            option.hidden = !name.includes(query);
        });
    });
    document.addEventListener('click', (event) => {
        if (picker?.open && !picker.contains(event.target) && !event.target.closest('[data-security-favorites-manage]')) picker.open = false;
    });

    const createDialog = document.querySelector('[data-security-incident-create]');
    document.querySelector('[data-security-incident-create-open]')?.addEventListener('click', () => openDialog(createDialog));
    document.querySelectorAll('[data-security-incident-detail-open]').forEach((button) => {
        button.addEventListener('click', () => openDialog(document.getElementById(`security-incident-detail-${button.dataset.securityIncidentDetailOpen}`)));
    });
    document.querySelectorAll('[data-security-incident-create], [data-security-incident-detail]').forEach((dialog) => {
        dialog.querySelectorAll('[data-security-incident-close]').forEach((button) => {
            button.addEventListener('click', () => closeDialog(dialog));
        });
        dialog.addEventListener('click', (event) => { if (event.target === dialog) closeDialog(dialog); });
    });

    const form = document.querySelector('[data-security-incident-form]');
    const company = form?.querySelector('[data-security-incident-company]');
    const branch = form?.querySelector('[data-security-incident-branch]');
    const camera = form?.querySelector('[data-security-incident-camera]');
    const updateCameras = (reset = false) => {
        if (!camera) return;
        if (reset) camera.value = '';
        [...camera.options].forEach((option) => {
            if (!option.value) return;
            const visible = Boolean(branch.value) && option.dataset.branchId === branch.value;
            option.hidden = !visible;
            option.disabled = !visible;
            if (!visible && option.selected) camera.value = '';
        });
    };
    const updateBranches = (reset = false) => {
        if (!branch) return;
        if (reset) branch.value = '';
        [...branch.options].forEach((option) => {
            if (!option.value) return;
            const visible = Boolean(company.value) && option.dataset.companyId === company.value;
            option.hidden = !visible;
            option.disabled = !visible;
            if (!visible && option.selected) branch.value = '';
        });
        updateCameras(reset);
    };
    company?.addEventListener('change', () => updateBranches(true));
    branch?.addEventListener('change', () => updateCameras(true));
    updateBranches();
    if (createDialog?.hasAttribute('data-auto-open')) openDialog(createDialog);
});
