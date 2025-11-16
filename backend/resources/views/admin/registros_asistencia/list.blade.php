<div class="table-responsive">
    <table id="dataTable" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empleado</th>
                <th>Dispositivo</th>
                <th>Tipo Marcaje</th>
                <th>Fecha y Hora</th>
                <th>Tipo Verificación</th>
                <th class="actions text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $registro)
                <tr>
                    <td>{{ $registro->id }}</td>
                    <td>{{ $registro->empleado->full_name ?? 'N/A' }}</td>
                    <td>{{ $registro->dispositivo->nombre_dispositivo ?? 'N/A' }}</td>
                    <td>{{ ucfirst($registro->tipo_marcaje) }}</td>
                    <td>{{ $registro->fecha_hora }}</td>
                    <td>{{ ucfirst($registro->tipo_verificacion) }}</td>
                    <td class="no-sort no-click bread-actions text-right">
                        <a href="{{ route('admin.registros-asistencia.edit', $registro->id) }}" title="Editar" class="btn btn-sm btn-primary edit">
                            <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Editar</span>
                        </a>
                        <button title="Borrar" class="btn btn-sm btn-danger delete" data-id="{{ $registro->id }}" data-toggle="modal" data-target="#delete_modal">
                            <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Borrar</span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No se encontraron registros de asistencia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="col-md-12">
    <div class="col-md-6" style="overflow-x:auto">
        @if(count($registros) > 0)
            <p class="text-muted">Mostrando del {{ $registros->firstItem() }} al {{ $registros->lastItem() }} de {{ $registros->total() }} registros.</p>
        @endif
    </div>
    <div class="col-md-6">
        <nav class="pull-right">
            {{ $registros->links() }}
        </nav>
    </div>
</div>
