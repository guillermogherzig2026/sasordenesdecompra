(() => {
    const sourceGridSelector = '.contract-card-grid[aria-label="Contratos registrados"]';
    const carouselSelector = '.government-contract-carousel';
    const supplySelectorSection = '.supply-contract-selector-section';
    const supplySections = new Set(['supply-pending', 'supply-billing', 'supply-history']);
    const currentOnlySupplySections = new Set(['supply-pending', 'supply-billing']);
    const selectedSection = document.documentElement.dataset.selectedSection || '';
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

    const removeDashboardCarousel = () => {
        document.querySelectorAll(carouselSelector).forEach((carousel) => {
            carousel.carouselResizeObserver?.disconnect();
            carousel.remove();
        });

        document.querySelectorAll('.contract-card-grid-source')
            .forEach((grid) => grid.classList.remove('contract-card-grid-source'));
        document.querySelectorAll('.contract-new-button-source')
            .forEach((button) => button.classList.remove('contract-new-button-source'));
    };

    const removeSupplyCarousel = () => {
        document.querySelectorAll('.supply-contract-carousel-arrow').forEach((button) => button.remove());
        document.querySelectorAll('.supply-contract-carousel-section').forEach((section) => {
            section.classList.remove('supply-contract-carousel-section');
        });
        document.querySelectorAll('.supply-contract-carousel-track').forEach((track) => {
            track.supplyCarouselResizeObserver?.disconnect();
            track.classList.remove('supply-contract-carousel-track');
            delete track.dataset.supplyCarouselBound;
        });
        document.querySelectorAll('.contract-selector-card-filtered').forEach((card) => {
            card.classList.remove('contract-selector-card-filtered');
            card.removeAttribute('aria-hidden');
        });
    };

    const createSupplyArrow = (direction) => {
        const button = document.createElement('button');
        const isPrevious = direction === 'previous';

        button.className = `supply-contract-carousel-arrow supply-contract-carousel-${direction}`;
        button.type = 'button';
        button.setAttribute('aria-label', isPrevious ? 'Contrato anterior' : 'Contrato siguiente');
        button.title = isPrevious ? 'Contrato anterior' : 'Contrato siguiente';
        button.textContent = isPrevious ? '<' : '>';

        return button;
    };

    const updateSupplyOverview = (cards) => {
        if (!currentOnlySupplySections.has(selectedSection)) return;

        const overviewCard = cards.find((card) => {
            const title = normalizeText(contractNumber(card));

            return title === 'todos' || title === 'todas';
        });
        const currentCount = cards.filter((card) => card !== overviewCard && isCurrentContract(card)).length;
        const countLabel = Array.from(overviewCard?.querySelectorAll('*') || [])
            .find((element) => element.children.length === 0
                && /^\d+\s+contratos\s+vigentes$/.test(normalizeText(element.textContent)));

        const nextLabel = `${currentCount} contratos vigentes`;

        if (countLabel && countLabel.textContent.trim() !== nextLabel) countLabel.textContent = nextLabel;
    };

    const buildSupplyCarousel = () => {
        const section = document.querySelector(supplySelectorSection);
        const track = section?.querySelector(':scope > .supply-contract-card-grid');

        if (!section || !track) return;

        section.classList.add('supply-contract-carousel-section');
        track.classList.add('supply-contract-carousel-track');

        const currentOnly = currentOnlySupplySections.has(selectedSection);
        const cards = Array.from(track.querySelectorAll(':scope > .contract-card'));

        cards.forEach((card) => {
            const title = normalizeText(contractNumber(card));
            const isOverview = title === 'todos' || title === 'todas';
            const shouldHide = currentOnly && !isOverview && !isCurrentContract(card);

            card.classList.toggle('contract-selector-card-filtered', shouldHide);
            if (shouldHide) card.setAttribute('aria-hidden', 'true');
            else card.removeAttribute('aria-hidden');
        });

        updateSupplyOverview(cards);

        let previousButton = section.querySelector(':scope > .supply-contract-carousel-previous');
        let nextButton = section.querySelector(':scope > .supply-contract-carousel-next');

        if (!previousButton) {
            previousButton = createSupplyArrow('previous');
            section.insertBefore(previousButton, track);
        }

        if (!nextButton) {
            nextButton = createSupplyArrow('next');
            section.insertBefore(nextButton, track.nextSibling);
        }

        const updateControls = () => {
            const maxScroll = Math.max(track.scrollWidth - track.clientWidth, 0);
            previousButton.disabled = track.scrollLeft <= 6;
            nextButton.disabled = track.scrollLeft >= maxScroll - 6;
        };

        track.updateSupplyControls = updateControls;

        if (!track.dataset.supplyCarouselBound) {
            track.dataset.supplyCarouselBound = 'true';
            track.addEventListener('scroll', updateControls, { passive: true });

            if ('ResizeObserver' in window) {
                const resizeObserver = new ResizeObserver(updateControls);
                resizeObserver.observe(track);
                track.supplyCarouselResizeObserver = resizeObserver;
            }
        }

        window.requestAnimationFrame(updateControls);
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

        if (button?.matches('.supply-contract-carousel-arrow')) {
            const section = button.closest(supplySelectorSection);
            const track = section?.querySelector('.supply-contract-carousel-track');

            if (!track) return;

            const direction = button.matches('.supply-contract-carousel-previous') ? -1 : 1;
            const distance = Math.max(track.clientWidth * 0.82, 300);
            const maxScroll = Math.max(track.scrollWidth - track.clientWidth, 0);
            const nextScroll = Math.min(Math.max(track.scrollLeft + direction * distance, 0), maxScroll);

            track.scrollTo({ left: nextScroll, behavior: 'smooth' });
            window.requestAnimationFrame(() => track.updateSupplyControls?.());
            return;
        }

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

    const synchronizeCarousels = () => {
        scheduledFrame = 0;

        if (supplySections.has(selectedSection)) {
            removeDashboardCarousel();
            buildSupplyCarousel();
            return;
        }

        removeSupplyCarousel();
        buildCarousel();
    };

    const scheduleBuild = () => {
        if (scheduledFrame) return;
        scheduledFrame = window.requestAnimationFrame(synchronizeCarousels);
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
