(() => {
    const ready = (callback) => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback, { once: true });
            return;
        }

        callback();
    };

    ready(() => {
        document.querySelectorAll('[data-agent-center]').forEach((center) => {
            if (center.dataset.agentCenterReady === 'true') return;
            center.dataset.agentCenterReady = 'true';

            const track = center.querySelector('[data-agent-track]');
            const previous = center.querySelector('[data-agent-carousel="previous"]');
            const next = center.querySelector('[data-agent-carousel="next"]');
            const choices = Array.from(center.querySelectorAll('[data-agent-selection]'));
            const details = Array.from(center.querySelectorAll('[data-agent-detail]'));
            const empty = center.querySelector('[data-agent-empty]');
            let selection = center.dataset.selected || '';

            const updateNavigation = () => {
                if (!track || !previous || !next) return;
                previous.disabled = track.scrollLeft <= 2;
                next.disabled = track.scrollLeft >= track.scrollWidth - track.clientWidth - 2;
            };

            const renderSelection = () => {
                center.dataset.selected = selection;
                choices.forEach((choice) => {
                    choice.setAttribute('aria-pressed', choice.dataset.agentSelection === selection ? 'true' : 'false');
                });

                let visibleDetails = 0;
                details.forEach((detail) => {
                    const visible = selection === 'all' || detail.dataset.agentDetail === selection;
                    detail.hidden = !visible;
                    if (visible) visibleDetails += 1;

                    const selectedInput = detail.querySelector('input[name="selected"]');
                    if (selectedInput) selectedInput.value = selection || detail.dataset.agentDetail;
                });

                if (empty) empty.hidden = selection === '' || visibleDetails > 0;
            };

            const updateSelection = (value) => {
                selection = selection === value ? '' : value;
                renderSelection();
            };

            choices.forEach((choice) => {
                choice.addEventListener('click', () => updateSelection(choice.dataset.agentSelection));
            });

            previous?.addEventListener('click', () => {
                track.scrollBy({ left: -Math.max(260, track.clientWidth * .65), behavior: 'smooth' });
            });
            next?.addEventListener('click', () => {
                track.scrollBy({ left: Math.max(260, track.clientWidth * .65), behavior: 'smooth' });
            });
            track?.addEventListener('scroll', updateNavigation, { passive: true });
            window.addEventListener('resize', updateNavigation, { passive: true });

            renderSelection();
            requestAnimationFrame(updateNavigation);

            const modal = document.querySelector('[data-agent-modal]');
            const form = modal?.querySelector('[data-agent-form]');
            if (!modal || !form) return;

            let agents = {};
            const dataElement = document.getElementById(center.dataset.agentDataId);
            try {
                agents = JSON.parse(dataElement?.textContent || '{}');
            } catch (error) {
                agents = {};
            }

            const method = form.querySelector('[data-agent-method]');
            const editingId = form.querySelector('[data-agent-editing-id]');
            const name = form.querySelector('[data-agent-name]');
            const description = form.querySelector('[data-agent-description]');
            const instructions = form.querySelector('[data-agent-instructions]');
            const title = form.querySelector('[data-agent-form-title]');

            const showModal = () => {
                if (typeof modal.showModal === 'function') modal.showModal();
                else modal.setAttribute('open', 'open');
                requestAnimationFrame(() => name?.focus());
            };

            const configureForm = (agentId = '') => {
                const agent = agentId ? agents[String(agentId)] : null;
                form.action = agent
                    ? modal.dataset.updateUrl.replace('__AGENT__', String(agent.id))
                    : modal.dataset.storeUrl;
                method.disabled = !agent;
                editingId.value = agent ? String(agent.id) : '';
                title.textContent = agent ? 'Editar agente' : 'Crear nuevo agente';
                name.value = agent?.name || '';
                description.value = agent?.description || '';
                instructions.value = agent?.instructions || '';
                form.querySelectorAll('.agent-error').forEach((error) => error.remove());
            };

            center.querySelector('[data-agent-create]')?.addEventListener('click', () => {
                configureForm();
                showModal();
            });

            center.querySelectorAll('[data-agent-edit]').forEach((button) => {
                button.addEventListener('click', () => {
                    configureForm(button.dataset.agentEdit);
                    showModal();
                });
            });

            modal.querySelectorAll('[data-agent-modal-close]').forEach((button) => {
                button.addEventListener('click', () => modal.close());
            });
            modal.addEventListener('click', (event) => {
                if (event.target === modal) modal.close();
            });

            if (modal.dataset.openOnLoad === 'true') {
                const oldEditingId = modal.dataset.editingId;
                const oldAgent = oldEditingId ? agents[String(oldEditingId)] : null;
                form.action = oldAgent
                    ? modal.dataset.updateUrl.replace('__AGENT__', String(oldAgent.id))
                    : modal.dataset.storeUrl;
                method.disabled = !oldAgent;
                title.textContent = oldAgent ? 'Editar agente' : 'Crear nuevo agente';
                showModal();
            }
        });
    });
})();
