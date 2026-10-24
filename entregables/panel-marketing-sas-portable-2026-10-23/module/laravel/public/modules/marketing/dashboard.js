document.addEventListener('DOMContentLoaded', () => {
    const dashboard = document.querySelector('[data-marketing-dashboard]');
    if (!dashboard) return;

    const normalize = (value) => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es').trim();
    const search = dashboard.querySelector('[data-marketing-channel-search]');
    const cards = [...dashboard.querySelectorAll('[data-marketing-channel-card]')];
    search?.addEventListener('input', () => {
        const query = normalize(search.value);
        let visible = 0;
        cards.forEach((card) => {
            card.hidden = !normalize(card.dataset.channelName).includes(query);
            if (!card.hidden) visible += 1;
        });
        dashboard.querySelector('[data-marketing-channel-empty]').hidden = visible > 0;
    });

    let opener;
    dashboard.querySelectorAll('[data-channel-dialog-open]').forEach((button) => {
        const dialog = document.getElementById(`marketing-channel-${button.dataset.channelDialogOpen}`);
        if (!dialog) return;
        button.addEventListener('click', () => { opener = button; dialog.showModal(); });
    });
    dashboard.querySelectorAll('[data-channel-dialog]').forEach((dialog) => {
        dialog.addEventListener('close', () => opener?.focus());
        dialog.querySelectorAll('[data-channel-dialog-close]').forEach((button) => button.addEventListener('click', () => dialog.close()));
        dialog.addEventListener('click', (event) => {
            const bounds = dialog.getBoundingClientRect();
            if (event.target === dialog && (event.clientX < bounds.left || event.clientX > bounds.right || event.clientY < bounds.top || event.clientY > bounds.bottom)) dialog.close();
        });
        if (dialog.hasAttribute('data-reopen-dialog')) dialog.showModal();
    });

    const campaign = dashboard.querySelector('[data-marketing-campaign-select]');
    const networks = dashboard.querySelector('[data-marketing-campaign-networks]');
    const campaignForm = dashboard.querySelector('[data-marketing-campaign-form]');
    const channelChoices = [...(campaignForm?.querySelectorAll('input[name="channels[]"]:not(:disabled)') || [])];
    const linkButton = campaignForm?.querySelector('button[type="submit"]');
    const syncLinkButton = () => {
        if (linkButton) linkButton.disabled = !campaign?.value || !channelChoices.some((choice) => choice.checked);
    };
    const syncNetworks = (updateChoices = false) => {
        const selected = campaign.selectedOptions[0];
        const values = JSON.parse(selected?.dataset.networks || '[]');
        networks.textContent = selected?.value ? (values.join(', ') || 'Sin redes registradas') : 'Selecciona una campaña';
        if (updateChoices) channelChoices.forEach((choice) => { choice.checked = values.includes(choice.dataset.network); });
        syncLinkButton();
    };
    campaign?.addEventListener('change', () => syncNetworks(true));
    channelChoices.forEach((choice) => choice.addEventListener('change', syncLinkButton));
    if (campaign) syncNetworks();

    const company = dashboard.querySelector('[data-marketing-link-company]');
    company?.addEventListener('change', () => {
        const destination = new URL(company.dataset.panelUrl, window.location.origin);
        destination.searchParams.set('company_id', company.value);
        destination.searchParams.set('campaign_id', 'all');
        destination.searchParams.set('view', 'channels');
        destination.hash = 'marketing-link-campaign';
        window.location.assign(destination.href);
    });

    dashboard.querySelectorAll('.marketing-settings-nav a[href^="#"]').forEach((link) => {
        link.addEventListener('click', () => {
            dashboard.querySelectorAll('.marketing-settings-nav [aria-current]').forEach((active) => active.removeAttribute('aria-current'));
            link.setAttribute('aria-current', 'location');
        });
    });
});
