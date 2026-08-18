document.addEventListener('DOMContentLoaded', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }

    document.querySelectorAll('[data-back-button]').forEach((button) => {
        button.addEventListener('click', () => {
            const fallbackUrl = button.dataset.fallbackUrl || '/';

            if (window.history.length > 1) {
                window.history.back();
                return;
            }

            window.location.href = fallbackUrl;
        });
    });

    document.querySelectorAll('[data-project-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-project-track]');
        const previousButton = carousel.querySelector('[data-carousel-prev]');
        const nextButton = carousel.querySelector('[data-carousel-next]');

        if (!track || !previousButton || !nextButton) {
            return;
        }

        previousButton.addEventListener('click', () => {
            track.scrollBy({
                left: -Math.max(180, track.clientWidth * 0.7),
                behavior: 'smooth',
            });
        });

        nextButton.addEventListener('click', () => {
            track.scrollBy({
                left: Math.max(180, track.clientWidth * 0.7),
                behavior: 'smooth',
            });
        });
    });

    document.querySelectorAll('[data-audit-criteria-modal]').forEach((modal) => {
        const openButtons = document.querySelectorAll('[data-audit-criteria-open]');
        const closeButtons = modal.querySelectorAll('[data-audit-criteria-close]');
        const editButton = modal.querySelector('[data-audit-criteria-edit]');
        const inputs = modal.querySelectorAll('.audit-criteria-input');

        openButtons.forEach((button) => {
            button.addEventListener('click', () => {
                modal.hidden = false;
            });
        });

        closeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                modal.hidden = true;
            });
        });

        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.hidden = true;
            }
        });

        editButton?.addEventListener('click', () => {
            const isEditing = editButton.dataset.editing === 'true';
            inputs.forEach((input) => {
                input.disabled = isEditing;
            });
            editButton.dataset.editing = isEditing ? 'false' : 'true';
            editButton.textContent = isEditing ? 'Editar criterios' : 'Guardar criterios';
        });
    });

    document.querySelectorAll('table.excel-table').forEach((table) => {
        table.addEventListener('click', (event) => {
            const row = event.target.closest('tbody tr');
            if (row) {
                row.classList.toggle('selected-row');
            }
        });

        if (!window.jQuery || !jQuery.fn.DataTable) {
            return;
        }

        if (table.tHead?.rows.length === 1) {
            const row = table.tHead.insertRow();
            row.classList.add('filter-row');
            Array.from(table.tHead.rows[0].cells).forEach((cell) => {
                const th = document.createElement('th');
                if (!cell.classList.contains('no-filter')) {
                    th.innerHTML = '<input type="text" aria-label="Filtrar columna" placeholder="Filtrar">';
                }
                row.appendChild(th);
            });
        }

        const dataTable = jQuery(table).DataTable({
            responsive: false,
            fixedHeader: true,
            stateSave: true,
            pageLength: 10,
            orderCellsTop: true,
            dom: '<"row align-items-center mb-2"<"col-md-6"B><"col-md-6"f>>rt<"row align-items-center mt-2"<"col-md-6"i><"col-md-6"p>>',
            buttons: [
                { extend: 'excelHtml5', text: 'Excel', footer: true },
                { extend: 'pdfHtml5', text: 'PDF', footer: true, orientation: 'landscape', pageSize: 'LEGAL' },
                { extend: 'print', text: 'Imprimir', footer: true },
                { extend: 'colvis', text: 'Columnas' },
            ],
            language: {
                search: 'Buscar:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
                infoEmpty: 'Sin registros',
                zeroRecords: 'No se encontraron registros',
                paginate: {
                    first: 'Primero',
                    last: 'Ultimo',
                    next: 'Siguiente',
                    previous: 'Anterior',
                },
                buttons: {
                    colvis: 'Columnas',
                },
            },
        });

        dataTable.columns().every(function () {
            const input = table.tHead.rows[1]?.cells[this.index()]?.querySelector('input');
            if (!input) {
                return;
            }

            input.addEventListener('keyup', () => {
                if (this.search() !== input.value) {
                    this.search(input.value).draw();
                }
            });
        });
    });
});
