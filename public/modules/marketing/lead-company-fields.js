document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('[data-marketing-lead-form]');
    if (!form) return;

    const company = form.elements.namedItem('company_id');
    const dependentFields = ['campaign_id', 'advisor_id'].map((name) => form.elements.namedItem(name));
    const sync = () => {
        dependentFields.forEach((field) => {
            const selected = field.selectedOptions[0];
            Array.from(field.options).forEach((option) => {
                if (!option.dataset.companyId) return;
                const available = option.dataset.companyId === company.value;
                option.hidden = !available;
                option.disabled = !available;
            });
            if (selected?.disabled) field.value = '';
        });
    };

    company.addEventListener('change', sync);
    sync();
});
