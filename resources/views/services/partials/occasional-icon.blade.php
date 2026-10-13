<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($icon)
        @case('tag')
            <path d="M12.6 2.6A2 2 0 0 0 11.2 2H4a2 2 0 0 0-2 2v7.2a2 2 0 0 0 .6 1.4l8.8 8.8a2 2 0 0 0 2.8 0l7.2-7.2a2 2 0 0 0 0-2.8Z"/><circle cx="7.5" cy="7.5" r=".5"/>
            @break
        @case('phone')
            <rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/>
            @break
        @case('fuel')
            <path d="M3 22V6a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v16M2 22h13M3 10h11m0-1 4 4v5a2 2 0 0 0 4 0V9l-3-3"/>
            @break
        @case('basket')
            <path d="m5 11 4-7m10 7-4-7M2 11h20m-19 0 2 9h14l2-9m-11 0 1 9m4-9-1 9"/>
            @break
        @case('parking')
            <rect x="3" y="3" width="18" height="18" rx="2"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
            @break
        @case('car')
            <path d="M5 17H4a2 2 0 0 1-2-2v-4l3-6a2 2 0 0 1 2-1h10a2 2 0 0 1 2 1l3 6v4a2 2 0 0 1-2 2H5Zm0 0-1 4m15-4 1 4M2 11h20M7 14h.01M17 14h.01"/>
            @break
        @case('ticket')
            <path d="M2 9a3 3 0 0 1 0 6v3a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-3a3 3 0 0 1 0-6V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2m0 4v2m0 4v2"/>
            @break
        @case('briefcase')
            <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M2 12h20"/>
            @break
        @case('wrench')
            <path d="M14.7 6.3a1 1 0 0 0 1.1.2L18.2 4a6 6 0 0 1-7.9 7.9l-6.8 6.8a2 2 0 0 0 2.8 2.8l6.8-6.8A6 6 0 0 0 21 6.8l-2.4 2.4a1 1 0 0 0 .2 1.1"/>
            @break
        @default
            <path d="m12 2 3.1 6.3 7 1-5 4.9 1.2 6.9-6.3-3.3-6.3 3.3 1.2-6.9-5-4.9 7-1Z"/>
    @endswitch
</svg>
