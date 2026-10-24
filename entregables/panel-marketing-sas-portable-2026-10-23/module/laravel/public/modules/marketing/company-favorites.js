document.addEventListener('DOMContentLoaded', () => {
    const shell = document.querySelector('.marketing-workspace-shell');
    const menuButton = shell?.querySelector('[data-marketing-mobile-menu]');
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
        if (shell.classList.contains('is-menu-open') && !event.target.closest('.sidebar, [data-marketing-mobile-menu]')) closeMenu();
    });
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeMenu(); });

    const carousel = document.querySelector('[data-marketing-company-carousel]');
    if (!carousel) return;

    document.querySelector('[data-marketing-company-picker] select')?.addEventListener('change', (event) => {
        event.target.form.requestSubmit();
    });

    const track = carousel.querySelector('[data-marketing-carousel-track]');
    const previous = carousel.querySelector('[data-marketing-carousel-prev]');
    const next = carousel.querySelector('[data-marketing-carousel-next]');
    const updateArrows = () => {
        previous.disabled = track.scrollLeft <= 1;
        next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
    };
    const step = () => Math.max(158, Math.floor(track.clientWidth * .75));

    previous.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
    next.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));
    track.addEventListener('scroll', updateArrows, { passive: true });
    window.addEventListener('resize', updateArrows);

    const storageKey = 'oc.marketing.favoriteCarousel';
    const selectedId = carousel.dataset.marketingSelectedCompany;
    const selected = track.querySelector('.marketing-favorite-card-wrap.active, .marketing-favorite-command.active');
    try {
        const saved = JSON.parse(sessionStorage.getItem(storageKey) || 'null');
        if (saved?.companyId === selectedId) track.scrollLeft = saved.left;
        else if (selected) track.scrollLeft = Math.max(0, selected.offsetLeft - track.offsetLeft - 8);
    } catch (_) {
        if (selected) track.scrollLeft = Math.max(0, selected.offsetLeft - track.offsetLeft - 8);
    }
    track.addEventListener('scroll', () => {
        try { sessionStorage.setItem(storageKey, JSON.stringify({ companyId: selectedId, left: track.scrollLeft })); } catch (_) {}
    }, { passive: true });
    updateArrows();
});
