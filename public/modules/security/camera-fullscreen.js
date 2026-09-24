document.addEventListener('DOMContentLoaded', () => {
    const dialog = document.querySelector('[data-security-camera-fullscreen]');
    const openButton = document.querySelector('[data-security-camera-fullscreen-open]');
    if (!dialog || !openButton) return;

    const stage = dialog.querySelector('[data-security-wall-stage]');
    const shell = dialog.querySelector('.security-camera-fullscreen-shell');
    const selector = dialog.querySelector('[data-security-wall-selector]');
    const cameraInputs = [...dialog.querySelectorAll('[data-security-wall-camera]')];
    const cameras = cameraInputs.map((input) => ({
        id: input.value,
        code: input.dataset.code,
        name: input.dataset.name,
        location: input.dataset.location,
        playbackUrl: input.dataset.playbackUrl,
    }));
    if (!cameras.length) return;

    const cameraById = new Map(cameras.map((camera) => [camera.id, camera]));
    const countButtons = [...dialog.querySelectorAll('[data-security-wall-count]')];
    const customButton = dialog.querySelector('[data-security-wall-custom-trigger]');
    const customField = dialog.querySelector('[data-security-wall-custom-field]');
    const customInput = dialog.querySelector('[data-security-wall-custom-input]');
    const selectionCount = dialog.querySelector('[data-security-wall-selection-count]');
    const visibleCount = dialog.querySelector('[data-security-wall-visible-count]');
    const featuredField = dialog.querySelector('[data-security-wall-featured-field]');
    const featuredSelect = dialog.querySelector('[data-security-wall-featured]');
    const applyButton = dialog.querySelector('[data-security-wall-apply]');
    const searchInput = dialog.querySelector('[data-security-wall-search]');
    const storageKey = `security-camera-wall:v1:${dialog.dataset.branchId}`;
    const presetCounts = new Set([1, 2, 3, 4, 6, 9, 16]);
    const defaultCount = Math.min(4, cameras.length);
    let targetCount = defaultCount;
    let appliedIds = cameras.slice(0, defaultCount).map((camera) => camera.id);
    let featuredId = appliedIds[0];
    let zoomedId = null;
    let customMode = !presetCounts.has(defaultCount);
    let hideTimer;

    try {
        const saved = JSON.parse(localStorage.getItem(storageKey) || 'null');
        if (saved && Array.isArray(saved.ids)) {
            targetCount = Math.min(cameras.length, Math.max(1, Number(saved.count) || saved.ids.length));
            appliedIds = [...new Set(saved.ids.map(String))].filter((id) => cameraById.has(id)).slice(0, targetCount);
            for (const camera of cameras) {
                if (appliedIds.length >= targetCount) break;
                if (!appliedIds.includes(camera.id)) appliedIds.push(camera.id);
            }
            featuredId = appliedIds.includes(String(saved.featuredId)) ? String(saved.featuredId) : appliedIds[0];
            customMode = !presetCounts.has(targetCount);
        }
    } catch {
        // The viewer remains usable when browser storage is unavailable.
    }

    const selectedInputs = () => cameraInputs.filter((input) => input.checked);
    const normalize = (value) => String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();

    const updateFeatured = () => {
        const selected = selectedInputs().map((input) => cameraById.get(input.value));
        const previous = featuredSelect.value || featuredId;
        featuredSelect.replaceChildren(...selected.map((camera) => {
            const option = document.createElement('option');
            option.value = camera.id;
            option.textContent = `${camera.code} · ${camera.name}`;
            return option;
        }));
        if (selected.some((camera) => camera.id === previous)) featuredSelect.value = previous;
        featuredField.hidden = targetCount !== 3;
    };

    const updateSelection = () => {
        const count = selectedInputs().length;
        selectionCount.textContent = `${count} de ${targetCount} seleccionadas`;
        applyButton.disabled = count !== targetCount;
        cameraInputs.forEach((input) => { input.disabled = !input.checked && count >= targetCount; });
        countButtons.forEach((button) => {
            button.setAttribute('aria-pressed', String(!customMode && Number(button.dataset.securityWallCount) === targetCount));
        });
        customButton.setAttribute('aria-pressed', String(customMode));
        customField.hidden = !customMode;
        customInput.value = targetCount;
        updateFeatured();
    };

    const setTargetCount = (count, custom = false) => {
        if (!Number.isInteger(count) || count < 1 || count > cameras.length) return;
        targetCount = count;
        customMode = custom || !presetCounts.has(count);
        selectedInputs().slice(count).forEach((input) => { input.checked = false; });
        updateSelection();
    };

    const setCheckedIds = (ids) => {
        const selected = new Set(ids);
        cameraInputs.forEach((input) => { input.checked = selected.has(input.value); });
        updateSelection();
    };

    const layoutFor = (count) => {
        const width = Math.max(1, stage.clientWidth - 8);
        const height = Math.max(1, stage.clientHeight - 8);
        let best = { columns: 1, rows: count, area: 0, empty: count };
        for (let columns = 1; columns <= count; columns++) {
            const rows = Math.ceil(count / columns);
            const cellWidth = (width - (columns - 1) * 4) / columns;
            const cellHeight = (height - (rows - 1) * 4) / rows;
            const videoWidth = Math.min(cellWidth, cellHeight * 16 / 9);
            const videoHeight = Math.min(cellHeight, cellWidth * 9 / 16);
            const area = count * videoWidth * videoHeight;
            const empty = columns * rows - count;
            if (area > best.area || (area === best.area && empty < best.empty)) {
                best = { columns, rows, area, empty };
            }
        }
        return best;
    };

    const updateLayout = () => {
        if (zoomedId) {
            stage.style.setProperty('--wall-columns', '1');
            stage.style.setProperty('--wall-rows', '1');
            return;
        }
        if (appliedIds.length === 3) {
            stage.dataset.threeOrientation = stage.clientWidth < stage.clientHeight ? 'vertical' : 'horizontal';
            stage.style.setProperty('--wall-columns', '2');
            stage.style.setProperty('--wall-rows', '2');
            return;
        }
        const layout = layoutFor(appliedIds.length);
        stage.style.setProperty('--wall-columns', String(layout.columns));
        stage.style.setProperty('--wall-rows', String(layout.rows));
    };

    const toggleZoom = (cameraId) => {
        zoomedId = zoomedId === cameraId ? null : cameraId;
        stage.classList.toggle('is-zoomed', Boolean(zoomedId));
        stage.classList.toggle('is-three', appliedIds.length === 3 && !zoomedId);
        stage.querySelectorAll('[data-wall-tile]').forEach((tile) => {
            tile.classList.toggle('is-zoomed', tile.dataset.cameraId === zoomedId);
            tile.setAttribute('aria-label', `${tile.dataset.cameraName}. ${zoomedId === tile.dataset.cameraId ? 'Volver al mosaico' : 'Ampliar cámara'}`);
        });
        updateLayout();
    };

    const render = () => {
        zoomedId = null;
        stage.classList.remove('is-zoomed');
        stage.classList.toggle('is-three', appliedIds.length === 3);
        const orderedIds = appliedIds.length === 3
            ? [featuredId, ...appliedIds.filter((id) => id !== featuredId)]
            : appliedIds;
        const tiles = orderedIds.map((id) => {
            const camera = cameraById.get(id);
            const tile = document.createElement('div');
            tile.className = `security-camera-fullscreen-tile ${camera.playbackUrl ? 'is-online' : 'is-offline'}`;
            if (appliedIds.length === 3 && id === featuredId) tile.classList.add('is-featured');
            tile.dataset.wallTile = '';
            tile.dataset.cameraId = id;
            tile.dataset.cameraName = camera.name;
            tile.tabIndex = 0;
            tile.role = 'button';
            tile.setAttribute('aria-label', `${camera.name}. Ampliar cámara`);

            if (camera.playbackUrl) {
                const frame = document.createElement('iframe');
                frame.className = 'security-camera-fullscreen-video';
                frame.src = camera.playbackUrl;
                frame.title = `Video en vivo de ${camera.name}`;
                frame.allow = 'autoplay; fullscreen';
                tile.append(frame);
            } else {
                const offline = document.createElement('div');
                offline.className = 'security-camera-fullscreen-offline';
                const icon = document.createElement('span');
                icon.textContent = '!';
                icon.setAttribute('aria-hidden', 'true');
                const message = document.createElement('strong');
                message.textContent = 'Sin conexión';
                offline.append(icon, message);
                tile.append(offline);
            }

            const label = document.createElement('div');
            label.className = 'security-camera-fullscreen-tile-label';
            const code = document.createElement('strong');
            code.textContent = camera.code;
            const name = document.createElement('span');
            name.textContent = camera.name;
            label.append(code, name);
            tile.append(label);
            tile.addEventListener('dblclick', () => toggleZoom(camera.id));
            tile.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') toggleZoom(camera.id);
            });
            return tile;
        });
        stage.replaceChildren(...tiles);
        visibleCount.textContent = `${appliedIds.length} ${appliedIds.length === 1 ? 'cámara' : 'cámaras'}`;
        updateLayout();
    };

    const showControls = () => {
        dialog.dataset.controlsHidden = 'false';
        clearTimeout(hideTimer);
        if (selector.hidden) {
            hideTimer = window.setTimeout(() => { dialog.dataset.controlsHidden = 'true'; }, 3500);
        }
    };

    const openSelector = () => {
        selector.hidden = false;
        showControls();
    };

    const closeSelector = () => {
        selector.hidden = true;
        showControls();
    };

    const closeViewer = () => {
        if (document.fullscreenElement === shell) document.exitFullscreen().catch(() => {});
        if (dialog.open) dialog.close();
        else dialog.removeAttribute('open');
    };

    const openViewer = () => {
        targetCount = appliedIds.length;
        customMode = !presetCounts.has(targetCount);
        setCheckedIds(appliedIds);
        if (typeof dialog.showModal === 'function') dialog.showModal();
        else dialog.setAttribute('open', 'open');
        render();
        openSelector();
        shell.requestFullscreen?.().catch(() => {});
    };

    openButton.addEventListener('click', openViewer);
    dialog.querySelector('[data-security-wall-exit]').addEventListener('click', closeViewer);
    dialog.querySelector('[data-security-wall-select-open]').addEventListener('click', openSelector);
    dialog.querySelector('[data-security-wall-select-close]').addEventListener('click', closeSelector);
    dialog.querySelector('[data-security-wall-controls-toggle]').addEventListener('click', () => {
        clearTimeout(hideTimer);
        dialog.dataset.controlsHidden = 'true';
    });
    dialog.addEventListener('pointermove', () => {
        if (dialog.dataset.controlsHidden === 'true' || selector.hidden) showControls();
    });
    dialog.addEventListener('cancel', (event) => {
        if (zoomedId) {
            event.preventDefault();
            toggleZoom(zoomedId);
        }
    });
    dialog.addEventListener('close', () => {
        clearTimeout(hideTimer);
        stage.replaceChildren();
        if (document.fullscreenElement === shell) document.exitFullscreen().catch(() => {});
    });
    countButtons.forEach((button) => button.addEventListener('click', () => setTargetCount(Number(button.dataset.securityWallCount))));
    customButton.addEventListener('click', () => {
        setTargetCount(targetCount, true);
        customInput.focus();
    });
    customInput.addEventListener('input', () => {
        const count = Number(customInput.value);
        if (Number.isInteger(count) && count >= 1 && count <= cameras.length) setTargetCount(count, true);
        else applyButton.disabled = true;
    });
    cameraInputs.forEach((input) => input.addEventListener('change', updateSelection));
    searchInput.addEventListener('input', () => {
        const query = normalize(searchInput.value.trim());
        dialog.querySelectorAll('[data-security-wall-option]').forEach((option) => {
            option.hidden = !normalize(option.dataset.search).includes(query);
        });
    });
    applyButton.addEventListener('click', () => {
        const selected = selectedInputs().map((input) => input.value);
        if (selected.length !== targetCount) return;
        appliedIds = selected;
        featuredId = selected.includes(featuredSelect.value) ? featuredSelect.value : selected[0];
        try {
            localStorage.setItem(storageKey, JSON.stringify({ ids: appliedIds, count: targetCount, featuredId }));
        } catch {
            // A private browsing restriction does not block camera monitoring.
        }
        render();
        closeSelector();
    });
    window.addEventListener('resize', () => { if (dialog.open) updateLayout(); });
    updateSelection();
});
