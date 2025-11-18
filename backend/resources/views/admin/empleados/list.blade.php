<div class="table-responsive">
    <table id="dataTable" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empleado</th>
                <th>DNI / Código</th>
                <th>Empresa</th>
                <th>Departamento</th>
                <th>Estado</th>
                <th class="actions text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $empleado)
                <tr>
                    <td>{{ $empleado->id }}</td>
                    <td>
                        <div style="display: flex; align-items: center;">
                            <img src="{{ $empleado->foto_perfil ? Storage::url($empleado->foto_perfil) : asset('img/default-avatar.png') }}" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                            <div>
                                <strong>{{ $empleado->full_name }}</strong><br>
                                <small>{{ $empleado->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        DNI: {{ $empleado->dni }}<br>
                        <small>Código: {{ $empleado->codigo_empleado }}</small>
                    </td>
                    <td>{{ $empleado->empresa->nombre_empresa ?? 'N/A' }}</td>
                    <td>{{ $empleado->departamento->nombre_departamento ?? 'N/A' }}</td>
                    <td>
                        <span class="badge bg-{{ $empleado->estado == 'activo' ? 'success' : 'danger' }}">{{ ucfirst($empleado->estado) }}</span>
                    </td>
                    <td class="no-sort no-click bread-actions text-right">
                        <a href="{{ route('admin.empleados.edit', $empleado->id) }}" title="Editar" class="btn btn-sm btn-primary edit">
                            <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Editar</span>
                        </a>
                        <button title="Borrar" class="btn btn-sm btn-danger delete" data-id="{{ $empleado->id }}" data-toggle="modal" data-target="#delete_modal">
                            <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Borrar</span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No se encontraron empleados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="col-md-12">
    <div class="col-md-6" style="overflow-x:auto">
        @if($items->count() > 0)
            <p class="text-muted">Mostrando del {{ $items->firstItem() }} al {{ $items->lastItem() }} de {{ $items->total() }} registros.</p>
        @endif
    </div>
    <div class="col-md-6">
        <nav class="pull-right">
            {{ $items->links() }}
        </nav>
    </div>
</div>
