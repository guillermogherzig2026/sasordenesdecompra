@extends('marketing.leadflow.layout', ['title' => 'Usuarios | LeadFlow'])

@section('content')
<div class="page-head">
    <div>
        <h1>Usuarios</h1>
        <p>Administra usuarios, roles y permisos dentro de cada empresa.</p>
    </div>
    <a class="btn primary" href="{{ route('marketing.users.create') }}">+ Nuevo usuario</a>
</div>

<div class="split">
    <div>
        <form class="panel pad selector-row" method="get">
            <div class="field">
                <label>Empresa</label>
                <select class="select" name="company_id">
                    <option value="">Todas las empresas</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected((int) request('company_id') === $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Rol</label>
                <select class="select" name="role">
                    <option value="">Todos los roles</option>
                    @foreach(\App\Models\Marketing\User::ROLES as $value => $label)
                        <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field" style="align-self:end"><button class="btn block" type="submit">Filtros</button></div>
        </form>

        <div class="panel table-wrap" style="margin-top:16px">
            <div class="panel-head">
                <div><span class="muted small">Total de usuarios</span><strong style="display:block;font-size:24px">{{ $users->total() }}</strong></div>
                <div><span class="muted small">Usuarios activos</span><strong style="display:block;font-size:24px">{{ $activeUsersCount }}</strong></div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Empresa</th>
                        <th>Rol</th>
                        <th>Campañas</th>
                        <th>Conversaciones activas</th>
                        <th>Estado</th>
                        <th>Último acceso</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr class="{{ $selected?->id === $user->id ? 'selected' : '' }}">
                            <td><span class="avatar small">{{ $user->initials }}</span> <strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->company?->name ?? 'Todas las empresas' }}</td>
                            <td><span class="badge {{ $user->role === 'asesor' ? 'purple' : 'green' }}">{{ \App\Models\Marketing\User::ROLES[$user->role] }}</span></td>
                            <td>{{ $user->campaigns_count }}</td>
                            <td>{{ $user->assigned_conversations_count }}</td>
                            <td><span class="status-dot {{ $user->active ? 'active' : 'inactive' }}">{{ $user->active ? 'Activo' : 'Inactivo' }}</span></td>
                            <td>{{ $user->last_login_at?->format('d M, H:i') ?? 'Nunca' }}</td>
                            <td>
                                <a class="btn" href="{{ route('marketing.users.edit', $user) }}">Editar</a>
                                <form method="post" action="{{ route('marketing.users.toggle', $user) }}" style="display:inline">
                                    @csrf @method('patch')
                                    <button class="btn" type="submit">{{ $user->active ? 'Desactivar' : 'Activar' }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="pagination">{{ $users->links() }}</div>
        </div>
    </div>

    <aside class="panel pad detail-panel">
        @if($selected)
            <div style="text-align:center">
                <span class="avatar" style="width:72px;height:72px;margin:auto">{{ $selected->initials }}</span>
                <h2>{{ $selected->name }}</h2>
                <span class="status-dot {{ $selected->active ? 'active' : 'inactive' }}">{{ $selected->active ? 'Activo' : 'Inactivo' }}</span>
                <p class="muted small">{{ $selected->email }}</p>
                <span class="badge green">{{ \App\Models\Marketing\User::ROLES[$selected->role] }}</span>
            </div>
            <div class="detail-section">
                <h3>Empresa asignada</h3>
                <div class="detail-row"><span>{{ $selected->company?->name ?? 'Todas las empresas' }}</span><a class="trend" href="{{ route('marketing.companies.index', ['company_id' => $selected->company_id]) }}">Ver empresa</a></div>
            </div>
            <div class="detail-section">
                <h3>Campañas autorizadas ({{ $selected->campaigns->count() }})</h3>
                @forelse($selected->campaigns as $campaign)
                    <span class="badge">{{ $campaign->name }}</span>
                @empty
                    <p class="muted">Acceso general según rol.</p>
                @endforelse
            </div>
            <div class="detail-section">
                <h3>Permisos y acceso</h3>
                @foreach(['Administrar usuarios','Gestionar empresas','Crear y editar campañas','Ver y exportar reportes','Acceso a conversaciones','Configuración de cuenta'] as $permission)
                    <div class="detail-row"><span>{{ $permission }}</span><span class="badge green">✓</span></div>
                @endforeach
            </div>
            <div class="detail-section">
                <h3>Información de contacto</h3>
                <div class="detail-row"><span>Teléfono</span><strong>{{ $selected->phone ?: '-' }}</strong></div>
                <div class="detail-row"><span>Zona horaria</span><strong>{{ $selected->timezone }}</strong></div>
            </div>
        @endif
    </aside>
</div>
@endsection
