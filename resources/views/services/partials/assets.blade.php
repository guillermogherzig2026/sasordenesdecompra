        <style>
            .service-form { display: grid; gap: 0; }
            .service-form .panel-header { align-items: center; }
            .service-form .panel-header h2 { margin: 0; }
            .service-form-section { min-width: 0; display: grid; gap: 15px; padding: 18px 0; border-top: 1px solid var(--line); }
            .service-form-section h3 { margin: 0; font-size: 1rem; }
            .service-form-section :is(.grid-4, .checkbox-inline, label) { min-width: 0; }
            .service-form-section input, .service-form-section select, .service-form-section textarea { min-width: 0; }
            .service-form-dates { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .service-form .form-actions { justify-content: flex-end; border-top: 1px solid var(--line); padding-top: 16px; }
            @media (max-width: 700px) {
                .service-form .panel-header { align-items: flex-start; flex-direction: column; }
                .service-form-dates { grid-template-columns: minmax(0, 1fr); }
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const paymentLapse = document.querySelector('select[name="payment_lapse"]');
                const startDay = document.querySelector('select[name="start_day"]');
                const startMonth = document.querySelector('select[name="start_month"]');
                const startYear = document.querySelector('select[name="start_year"]');
                const cutoffDay = document.querySelector('select[name="cutoff_day"]');
                const cutoffMonth = document.querySelector('select[name="cutoff_month"]');
                const cutoffYear = document.querySelector('select[name="cutoff_year"]');
                const dueDateDisplay = document.getElementById('due_date_display');

                function selectedDate(daySelect, monthSelect, yearSelect) {
                    const day = Number(daySelect.value);
                    const month = Number(monthSelect.value);
                    const year = Number(yearSelect.value);
                    const date = new Date(year, month - 1, day);

                    if (
                        date.getFullYear() !== year
                        || date.getMonth() !== month - 1
                        || date.getDate() !== day
                    ) {
                        return null;
                    }

                    return date;
                }

                function selectFirstEnabled(select) {
                    if (!select.selectedOptions[0]?.disabled) {
                        return;
                    }

                    const firstEnabled = Array.from(select.options).find((option) => !option.disabled);
                    if (firstEnabled) {
                        select.value = firstEnabled.value;
                    }
                }

                function syncStartDays() {
                    const month = Number(startMonth.value);
                    const year = Number(startYear.value);
                    const lastDay = new Date(year, month, 0).getDate();

                    Array.from(startDay.options).forEach((option) => {
                        option.disabled = Number(option.value) > lastDay;
                    });

                    if (startDay.selectedOptions[0]?.disabled) {
                        startDay.value = String(lastDay);
                    }
                }

                function syncCutoffDate() {
                    syncStartDays();

                    const start = selectedDate(startDay, startMonth, startYear);
                    if (!start) {
                        recalcDueDate();
                        return;
                    }

                    Array.from(cutoffYear.options).forEach((option) => {
                        option.disabled = Number(option.value) < start.getFullYear();
                    });
                    selectFirstEnabled(cutoffYear);

                    const selectedCutoffYear = Number(cutoffYear.value);
                    Array.from(cutoffMonth.options).forEach((option) => {
                        option.disabled = selectedCutoffYear === start.getFullYear()
                            && Number(option.value) < start.getMonth() + 1;
                    });
                    selectFirstEnabled(cutoffMonth);

                    const selectedCutoffMonth = Number(cutoffMonth.value);
                    const lastCutoffDay = new Date(selectedCutoffYear, selectedCutoffMonth, 0).getDate();
                    const firstCutoffDay = selectedCutoffYear === start.getFullYear()
                        && selectedCutoffMonth === start.getMonth() + 1
                        ? start.getDate()
                        : 1;

                    Array.from(cutoffDay.options).forEach((option) => {
                        const day = Number(option.value);
                        option.disabled = day < firstCutoffDay || day > lastCutoffDay;
                    });
                    selectFirstEnabled(cutoffDay);

                    recalcDueDate();
                }

                function recalcDueDate() {
                    const lapse = parseInt(paymentLapse.value, 10) || 30;
                    const cutoff = selectedDate(cutoffDay, cutoffMonth, cutoffYear);

                    if (!cutoff) {
                        dueDateDisplay.value = '';
                        return;
                    }

                    const due = new Date(cutoff);
                    due.setDate(due.getDate() + lapse);
                    const y = due.getFullYear();
                    const m = String(due.getMonth() + 1).padStart(2, '0');
                    const d = String(due.getDate()).padStart(2, '0');
                    dueDateDisplay.value = `${y}-${m}-${d}`;
                }

                paymentLapse.addEventListener('change', recalcDueDate);
                startDay.addEventListener('change', syncCutoffDate);
                startMonth.addEventListener('change', syncCutoffDate);
                startYear.addEventListener('change', syncCutoffDate);
                cutoffDay.addEventListener('change', recalcDueDate);
                cutoffMonth.addEventListener('change', syncCutoffDate);
                cutoffYear.addEventListener('change', syncCutoffDate);
                syncCutoffDate();
            });
        </script>
