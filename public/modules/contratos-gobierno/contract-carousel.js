(() => {
    const sourceGridSelector = '.contract-card-grid[aria-label="Contratos registrados"]';
    const carouselSelector = '.government-contract-carousel';
    let scheduledFrame = 0;

    const normalizeText = (value) => String(value || '')
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .toLowerCase();

    const isCurrentContract = (card) => {
        const status = normalizeText(card.querySelector('.contract-card-badge')?.textContent);

        return status === 'vigente' || status === 'proximo a vencer';
    };

    const contractNumber = (card) => card.querySelector('h3')?.textContent?.trim() || '';

    const findNewContractButton = (viewStack) => Array.from(viewStack.querySelectorAll('.contract-toolbar > button.primary-action'))
        .find((button) => button.textContent.trim() === 'Nuevo contrato');

    const clickSourceContract = (number) => {
        const sourceGrid = document.querySelector(sourceGridSelector);
        const sourceCard = Array.from(sourceGrid?.querySelectorAll(':scope > .contract-card') || [])
            .find((card) => contractNumber(card) === number);

        sourceCard?.click();
    };

    const buildCarousel = () => {
        scheduledFrame = 0;

        const sourceGrid = document.querySelector(sourceGridSelector);
        const viewStack = sourceGrid?.closest('.view-stack');
        const metricGrid = viewStack?.querySelector(':scope > .contract-metrics');

        if (!sourceGrid || !viewStack || !metricGrid) return;

        sourceGrid.classList.add('contract-card-grid-source');

        const sourceNewContractButton = findNewContractButton(viewStack);
        sourceNewContractButton?.classList.add('contract-new-button-source');

        let carousel = viewStack.querySelector(`:scope > ${carouselSelector}`);
        const isNewCarousel = !carousel;

        if (!carousel) {
            carousel = document.createElement('section');
            carousel.className = 'government-contract-carousel';
            carousel.setAttribute('aria-labelledby', 'government-contract-carousel-title');
            carousel.innerHTML = `
                <div class="contract-carousel-heading">
                    <p class="eyebrow">Contratos</p>
                    <h2 id="government-contract-carousel-title">Contratos vigentes</h2>
                </div>
                <div class="contract-carousel-shell">
                    <button class="contract-carousel-arrow contract-carousel-previous" type="button" aria-label="Contrato anterior" title="Contrato anterior">&lt;</button>
                    <div class="contract-carousel-track" role="region" aria-label="Carrusel de contratos vigentes" tabindex="0"></div>
                    <button class="contract-carousel-arrow contract-carousel-next" type="button" aria-label="Contrato siguiente" title="Contrato siguiente">&gt;</button>
                </div>
            `;

            const track = carousel.querySelector('.contract-carousel-track');
            const previousButton = carousel.querySelector('.contract-carousel-previous');
            const nextButton = carousel.querySelector('.contract-carousel-next');

            const updateControls = () => {
                const maxScroll = Math.max(track.scrollWidth - track.clientWidth, 0);
                previousButton.disabled = track.scrollLeft <= 2;
                nextButton.disabled = track.scrollLeft >= maxScroll - 2;
            };

            track.addEventListener('scroll', updateControls, { passive: true });
            carousel.updateControls = updateControls;

            if ('ResizeObserver' in window) {
                const resizeObserver = new ResizeObserver(updateControls);
                resizeObserver.observe(track);
                carousel.carouselResizeObserver = resizeObserver;
            }
        }

        if (carousel.nextElementSibling !== metricGrid) viewStack.insertBefore(carousel, metricGrid);

        if (!isNewCarousel) {
            window.requestAnimationFrame(() => carousel.updateControls?.());
            return;
        }

        const track = carousel.querySelector('.contract-carousel-track');
        const previousScroll = track.scrollLeft;
        const currentCards = Array.from(sourceGrid.querySelectorAll(':scope > .contract-card'))
            .filter(isCurrentContract);
        const carouselCards = currentCards.map((sourceCard) => {
            const number = contractNumber(sourceCard);
            const card = sourceCard.cloneNode(true);

            card.classList.add('contract-carousel-card');
            card.dataset.contractNumber = number;

            return card;
        });

        const newContractCard = document.createElement('button');
        newContractCard.className = 'contract-card contract-carousel-new';
        newContractCard.type = 'button';
        newContractCard.setAttribute('aria-label', 'Nuevo contrato');
        newContractCard.innerHTML = `
            <span class="contract-carousel-new-icon" aria-hidden="true">+</span>
            <strong>Nuevo contrato</strong>
        `;

        track.replaceChildren(...carouselCards, newContractCard);
        track.scrollLeft = Math.min(previousScroll, Math.max(track.scrollWidth - track.clientWidth, 0));
        window.requestAnimationFrame(() => carousel.updateControls?.());
    };

    const handleCarouselClick = (event) => {
        const target = event.target instanceof Element ? event.target : event.target.parentElement;
        const button = target?.closest('button');
        const carousel = button?.closest(carouselSelector);

        if (!button || !carousel) return;

        const track = carousel.querySelector('.contract-carousel-track');

        if (button.matches('.contract-carousel-previous, .contract-carousel-next')) {
            const direction = button.matches('.contract-carousel-previous') ? -1 : 1;
            const distance = Math.max(track.clientWidth * 0.78, 280);
            const maxScroll = Math.max(track.scrollWidth - track.clientWidth, 0);

            const nextScroll = Math.min(Math.max(track.scrollLeft + direction * distance, 0), maxScroll);
            track.scrollLeft = nextScroll;
            window.requestAnimationFrame(() => carousel.updateControls?.());
            return;
        }

        if (button.matches('.contract-carousel-new')) {
            const viewStack = carousel.closest('.view-stack');
            carousel.querySelectorAll('.contract-carousel-card.selected')
                .forEach((card) => card.classList.remove('selected'));
            findNewContractButton(viewStack)?.click();
            return;
        }

        if (button.matches('.contract-carousel-card')) {
            const willSelect = !button.classList.contains('selected');
            carousel.querySelectorAll('.contract-carousel-card.selected')
                .forEach((card) => card.classList.remove('selected'));
            if (willSelect) button.classList.add('selected');
            clickSourceContract(button.dataset.contractNumber);
        }
    };

    const scheduleBuild = (mutations = []) => {
        const containsSourceGrid = (node) => node.nodeType === Node.ELEMENT_NODE
            && (node.matches(sourceGridSelector) || node.querySelector(sourceGridSelector));
        const hasRelevantChange = mutations.length === 0 || mutations.some((mutation) => {
            const target = mutation.target.nodeType === Node.ELEMENT_NODE
                ? mutation.target
                : mutation.target.parentElement;

            if (!target || target.closest(carouselSelector)) return false;

            const sourceGrid = document.querySelector(sourceGridSelector);
            if (sourceGrid && (target === sourceGrid || sourceGrid.contains(target))) return true;

            if (mutation.type !== 'childList') return false;

            return Array.from(mutation.addedNodes).some(containsSourceGrid)
                || Array.from(mutation.removedNodes).some((node) => node === sourceGrid || containsSourceGrid(node));
        });

        if (!hasRelevantChange || scheduledFrame) return;
        scheduledFrame = window.requestAnimationFrame(buildCarousel);
    };

    const root = document.getElementById('root');
    if (!root) return;

    document.addEventListener('click', handleCarouselClick, true);

    const observer = new MutationObserver(scheduleBuild);
    observer.observe(root, {
        attributes: true,
        attributeFilter: ['class'],
        childList: true,
        subtree: true,
    });

    scheduleBuild();
})();
