@props(['name'])
<svg {{ $attributes->class(['marketing-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    @switch($name)
        @case('whatsapp')
            <path d="M20.5 11.5a8.5 8.5 0 0 1-12.4 7.6L3 20.5l1.4-5A8.5 8.5 0 1 1 20.5 11.5Z"/><path d="m8 7 1.5 2.7-1 1c1 2 2 3 4 4l1-1 2.7 1.5c-.2 1.2-1.2 2-2.4 1.8-4.3-.7-7.1-3.5-7.8-7.8C5.8 8.2 6.8 7.2 8 7Z"/>
            @break
        @case('meta')
            <path d="M12 12c-3-5-4-7-6-6-3 1-5 8-3 11 2 3 5-1 9-7 3-4 5-5 7-2 2 3 3 8 0 10-2 1-4-2-7-6Z" stroke-width="2.3"/>
            @break
        @case('instagram')
            <rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".7" fill="currentColor" stroke="none"/>
            @break
        @case('facebook')
            <path d="M14 22V13h3l.5-4H14V7c0-1 .4-1.5 1.6-1.5H18V2h-3c-3.3 0-5 2-5 5v2H7v4h3v9Z" fill="currentColor" stroke="none"/>
            @break
        @case('chatgpt')
            @foreach(range(0, 5) as $turn)
                <path d="M12 5c4-3 9 1 7 5l-7 4-4-2V8l4-3Z" transform="rotate({{ $turn * 60 }} 12 12)" stroke-width="1.1"/>
            @endforeach
            @break
        @case('x')
            <path d="m4 3 12 18h4L8 3H4Z"/><path d="m20 3-7 8m-2 3-7 7"/>
            @break
        @case('google')
            <path d="m12 5 7 13" stroke="#4285f4" stroke-width="7"/><path d="m12 5-7 13" stroke="#fbbc04" stroke-width="7"/><circle cx="5" cy="18" r="3.5" fill="#34a853" stroke="none"/>
            @break
        @case('summary')
            <path d="M4 20V10h4v10m2 0V4h4v16m2 0v-7h4v7M2 20h20"/>
            @break
        @case('campaign')
            <path d="m4 10 14-5v14L4 14v-4Zm3 5 1 5h3l-1-4m10-7 2-1m-2 6 2 1"/>
            @break
        @case('conversation')
            <path d="M21 11.5a9 9 0 0 1-13 8L3 21l1.5-5A9 9 0 1 1 21 11.5Z"/>
            @break
        @case('report')
            <path d="M14 3H5v18h14V8l-5-5Zm0 0v5h5M8 12h8m-8 4h6"/>
            @break
        @case('settings')
            <path d="m9 3-.5 2-2 1-2-.5-2 3 1.5 1.5v3L2.5 15l2 3 2-.5 2 1 .5 2.5h5l.5-2.5 2-1 2 .5 2-3-1.5-2v-3L21 8.5l-2-3-2 .5-2-1-.5-2H9Z"/><circle cx="11.5" cy="12" r="3"/>
            @break
        @case('users')
            <circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6m3 10v-3a6 6 0 0 0-2-4"/>
            @break
        @case('automation')
            <path d="m13 2-9 12h7l-1 8 10-13h-8l1-7Z"/>
            @break
        @case('clock')
            <circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>
            @break
        @case('search')
            <circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>
            @break
        @case('link')
            <path d="m10 8 3-3a4 4 0 0 1 6 6l-3 3m-2 2-3 3a4 4 0 0 1-6-6l3-3m0 6 8-8"/>
            @break
        @case('arrow')
            <path d="m9 5 7 7-7 7"/>
            @break
        @case('company')
            <path d="M4 21V4h11v17M15 10h5v11M2 21h20M7 8h1m3 0h1m-5 4h1m3 0h1m-5 4h1m3 0h1"/>
            @break
        @case('close')
            <path d="m6 6 12 12M6 18 18 6"/>
            @break
        @default
            <path d="M12 4v16M4 12h16"/>
    @endswitch
</svg>
