@props([
    'catalog',
    'selected' => [],
    'idPrefix' => 'navigation',
    'roleLabels' => [],
    'selectedRole' => 'buyer',
    'buyerSubroleLabels' => [],
    'selectedBuyerSubroles' => [],
])

@php
    $selectedPermissions = array_flip(\App\Support\NavigationPermissionCatalog::normalize($selected));
    $roleCategory = \App\Support\NavigationPermissionCatalog::categoryForRole($selectedRole);
    $firstSelectedCategory = collect($catalog)->search(fn (array $category) => collect(array_keys($category['items']))
        ->contains(fn (string $permission) => isset($selectedPermissions[$permission])));
    $firstCategory = $roleCategory && isset($catalog[$roleCategory])
        ? $roleCategory
        : ($firstSelectedCategory !== false ? $firstSelectedCategory : array_key_first($catalog));
    $modulePresentation = [
        'home' => ['abbr' => 'IN', 'accent' => '#2563eb', 'tint' => '#eaf1ff'],
        'finance' => ['abbr' => 'FI', 'accent' => '#087f6b', 'tint' => '#e7f7f1'],
        'procurement' => ['abbr' => 'CS', 'accent' => '#c56a12', 'tint' => '#fff3e5'],
        'inventory' => ['abbr' => 'AI', 'accent' => '#14718b', 'tint' => '#e8f6fa'],
        'services' => ['abbr' => 'SE', 'accent' => '#a23a67', 'tint' => '#fbeaf2'],
        'human_resources' => ['abbr' => 'RH', 'accent' => '#b53f45', 'tint' => '#fdecee'],
        'construction' => ['abbr' => 'AO', 'accent' => '#9b6b0b', 'tint' => '#fff6dc'],
        'plazas' => ['abbr' => 'AP', 'accent' => '#0d766e', 'tint' => '#e7f7f5'],
        'government_contracts' => ['abbr' => 'CG', 'accent' => '#355da8', 'tint' => '#edf2fc'],
        'security' => ['abbr' => 'SV', 'accent' => '#8d4858', 'tint' => '#f9edf0'],
    ];
@endphp

<section class="navigation-permission-manager" data-navigation-permission-manager data-role-navigation-manager>
    <input type="hidden" name="menu_permissions_configured" value="1">
    <input id="{{ $idPrefix }}-category" type="hidden" value="{{ $firstCategory }}" data-navigation-category>

    <section class="authorization-step authorization-menu-step">
        <div class="authorization-step-header">
            <div class="authorization-step-title">
                <span class="authorization-step-number">1</span>
                <div>
                    <h3>Selecciona un menú</h3>
                    <p>Elige un módulo para configurar los accesos del usuario.</p>
                </div>
            </div>
            <strong class="authorization-total"><span data-navigation-permission-total>0</span> accesos seleccionados</strong>
        </div>

        <div class="navigation-module-carousel" data-navigation-carousel>
            <button class="navigation-carousel-button" type="button" aria-label="Ver menús anteriores" data-navigation-carousel-prev>&lsaquo;</button>
            <div class="navigation-module-track" role="group" aria-label="Menús disponibles" data-navigation-carousel-track>
                @foreach ($catalog as $categoryKey => $category)
                    @php
                        $presentation = $modulePresentation[$categoryKey] ?? ['abbr' => strtoupper(substr($category['label'], 0, 2)), 'accent' => '#14718b', 'tint' => '#e8f6fa'];
                        $selectedInCategory = collect(array_keys($category['items']))
                            ->filter(fn (string $permission) => isset($selectedPermissions[$permission]))
                            ->count();
                    @endphp
                    <button
                        class="navigation-module-card {{ $categoryKey === $firstCategory ? 'is-active' : '' }}"
                        type="button"
                        style="--module-accent: {{ $presentation['accent'] }}; --module-tint: {{ $presentation['tint'] }}"
                        aria-pressed="{{ $categoryKey === $firstCategory ? 'true' : 'false' }}"
                        aria-controls="{{ $idPrefix }}-panel-{{ $categoryKey }}"
                        data-navigation-category-button="{{ $categoryKey }}"
                    >
                        <span class="navigation-module-icon" aria-hidden="true">{{ $presentation['abbr'] }}</span>
                        <span class="navigation-module-name">{{ $category['label'] }}</span>
                        <span class="navigation-module-meta">{{ count($category['items']) }} submenús</span>
                        <span class="navigation-module-selected"><strong data-navigation-card-count>{{ $selectedInCategory }}</strong> seleccionados</span>
                    </button>
                @endforeach
            </div>
            <button class="navigation-carousel-button" type="button" aria-label="Ver menús siguientes" data-navigation-carousel-next>&rsaquo;</button>
        </div>

    </section>

    <section class="authorization-step authorization-submenu-step">
        <div class="authorization-step-header">
            <div class="authorization-step-title">
                <span class="authorization-step-number">2</span>
                <div>
                    <h3>Autoriza los submenús de <span data-navigation-active-category-label>{{ $catalog[$firstCategory]['label'] }}</span></h3>
                    <p>Marca únicamente las opciones que podrá consultar y operar.</p>
                </div>
            </div>
        </div>

        <div class="navigation-permission-window">
            @foreach ($catalog as $categoryKey => $category)
                <div
                    class="navigation-permission-panel"
                    id="{{ $idPrefix }}-panel-{{ $categoryKey }}"
                    data-navigation-panel="{{ $categoryKey }}"
                    data-navigation-label="{{ $category['label'] }}"
                    @if ($categoryKey !== $firstCategory) hidden @endif
                >
                    <div class="navigation-permission-panel-header">
                        <div>
                            <h4>{{ $category['label'] }}</h4>
                            <p class="fine-print">{{ $category['description'] }}</p>
                        </div>
                        <div class="navigation-permission-actions">
                            <span><strong data-navigation-category-count>0</strong> de {{ count($category['items']) }}</span>
                            <button class="button ghost small" type="button" data-navigation-select-all>Seleccionar todos</button>
                            <button class="button ghost small" type="button" data-navigation-clear>Limpiar</button>
                        </div>
                    </div>

                    @php
                        $permissionGroups = $category['groups'] ?? [];
                        $groupedPermissions = collect($permissionGroups)->flatMap(fn (array $group) => $group['items'])->all();
                        $ungroupedPermissions = array_values(array_diff(array_keys($category['items']), $groupedPermissions));

                        if ($permissionGroups === []) {
                            $permissionGroups = [
                                'all' => ['label' => null, 'items' => array_keys($category['items'])],
                            ];
                        } elseif ($ungroupedPermissions !== []) {
                            $permissionGroups['other'] = ['label' => 'Otros', 'items' => $ungroupedPermissions];
                        }
                    @endphp

                    <div class="navigation-permission-subcategories">
                        @foreach ($permissionGroups as $groupKey => $permissionGroup)
                            <fieldset
                                class="navigation-permission-subcategory {{ filled($permissionGroup['label']) ? 'has-heading' : '' }}"
                                data-navigation-subcategory="{{ $categoryKey }}.{{ $groupKey }}"
                            >
                                @if (filled($permissionGroup['label']))
                                    <legend>{{ $permissionGroup['label'] }}</legend>
                                @endif
                                <div class="navigation-permission-grid">
                                    @foreach ($permissionGroup['items'] as $permission)
                                        @continue(! isset($category['items'][$permission]))
                                        <label class="navigation-permission-option">
                                            <input
                                                name="menu_permissions[]"
                                                type="checkbox"
                                                value="{{ $permission }}"
                                                @checked(isset($selectedPermissions[$permission]))
                                            >
                                            <span>{{ $category['items'][$permission] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</section>
