@props([
    'message',
    'dialogId' => 'system-message-dialog',
])

<dialog
    class="system-message-dialog"
    id="{{ $dialogId }}"
    aria-labelledby="{{ $dialogId }}-message"
    aria-modal="true"
    data-system-message-dialog
    data-auto-open
>
    <div class="system-message-dialog-content">
        <span class="system-message-dialog-icon" aria-hidden="true">&#10003;</span>
        <p class="system-message-dialog-message" id="{{ $dialogId }}-message">{{ $message }}</p>
        <button
            class="system-message-dialog-close"
            type="button"
            data-system-message-close
            aria-label="Cerrar mensaje"
            title="Cerrar"
        >&times;</button>
    </div>
</dialog>
