(() => {
    const storageKey = 'suministro-control:supply-orders:v1';
    const approvedOrderIds = new Set();
    const approvedFolios = new Set();
    let activeOrderId = null;
    let scheduledFrame = 0;

    const orderIdFromButton = (button) => {
        const selector = button
            ?.closest('tr')
            ?.querySelector('input[type="checkbox"][aria-label^="Seleccionar "]');
        const label = selector?.getAttribute('aria-label') || '';

        return label.startsWith('Seleccionar ') ? label.slice('Seleccionar '.length).trim() : null;
    };

    const rememberPersistedApprovals = () => {
        try {
            const storedState = JSON.parse(window.localStorage.getItem(storageKey) || '{}');
            const storedOrderIds = storedState.approvedSupplyRemissionOrderIds;

            if (Array.isArray(storedOrderIds)) {
                storedOrderIds.forEach((orderId) => approvedOrderIds.add(orderId));
            }
        } catch {
            // The application owns recovery for malformed persisted state.
        }
    };

    const folioFromPanel = (panel) => panel
        ?.querySelector('.remission-print-header p')
        ?.textContent
        ?.trim();

    const rememberApprovedPanel = () => {
        const panel = document.querySelector('.remission-format-panel');
        const approvalButton = panel?.querySelector('.generate-format-remission-button');
        const folio = folioFromPanel(panel);
        const isApproved = approvalButton?.classList.contains('approved-format-remission-button')
            || approvalButton?.textContent.trim() === 'Aprobada';

        if (folio && isApproved) {
            approvedFolios.add(folio);
            if (activeOrderId) approvedOrderIds.add(activeOrderId);
        }
    };

    const synchronizeRemissionButtons = () => {
        scheduledFrame = 0;
        rememberPersistedApprovals();
        rememberApprovedPanel();

        document.querySelectorAll('.remission-folio-button.generated-remission-button')
            .forEach((button) => {
                const folio = button.textContent.trim();
                const orderId = orderIdFromButton(button);
                const isApproved = approvedFolios.has(folio)
                    || (orderId && approvedOrderIds.has(orderId));
                const status = isApproved ? 'aprobada' : 'pendiente de aprobacion';

                button.classList.toggle('approved-remission-button', isApproved);
                button.classList.toggle('pending-remission-button', !isApproved);
                button.dataset.approvalStatus = isApproved ? 'approved' : 'pending';
                button.setAttribute('aria-label', `${folio}, ${status}`);
                button.title = isApproved
                    ? 'Remision aprobada'
                    : 'Remision pendiente de aprobacion';
            });
    };

    const scheduleSynchronization = () => {
        if (scheduledFrame) return;
        scheduledFrame = window.requestAnimationFrame(synchronizeRemissionButtons);
    };

    const rememberBulkApproval = (dialog) => {
        dialog.querySelectorAll('.remission-selector-options label')
            .forEach((option) => {
                const checkbox = option.querySelector('input[type="checkbox"]');
                const folio = option.querySelector('span')?.textContent?.trim();

                if (checkbox?.checked && folio) {
                    approvedFolios.add(folio);

                    const remissionButton = Array.from(document.querySelectorAll('.remission-folio-button'))
                        .find((candidate) => candidate.textContent.trim() === folio);
                    const orderId = orderIdFromButton(remissionButton);

                    if (orderId) approvedOrderIds.add(orderId);
                }
            });
    };

    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target : event.target.parentElement;
        const button = target?.closest('button');

        if (!button) return;

        if (button.matches('.remission-folio-button.generated-remission-button')) {
            activeOrderId = orderIdFromButton(button);
            return;
        }

        const formatPanel = button.closest('.remission-format-panel');
        if (formatPanel && button.matches('.generate-format-remission-button') && !button.disabled) {
            const folio = folioFromPanel(formatPanel);
            if (folio) approvedFolios.add(folio);
            if (activeOrderId) approvedOrderIds.add(activeOrderId);
            scheduleSynchronization();
            return;
        }

        const approvalDialog = button.closest('.remission-approval-selector');
        if (approvalDialog && button.textContent.trim() === 'ACEPTAR') {
            rememberBulkApproval(approvalDialog);
            scheduleSynchronization();
        }
    });

    const root = document.getElementById('root');
    if (!root) return;

    const observer = new MutationObserver(scheduleSynchronization);
    observer.observe(root, {
        attributes: true,
        attributeFilter: ['class', 'disabled'],
        childList: true,
        subtree: true,
    });

    scheduleSynchronization();
})();
