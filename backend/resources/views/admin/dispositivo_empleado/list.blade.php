<div class="table-responsive">
    <table id="dataTable" class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empleado</th>
                <th>Dispositivo</th>
                <th>ID en Reloj (zk_user_id)</th>
                <th>Creado el</th>
                <th class="actions text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mapeos as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->empleado->full_name ?? 'N/A' }}</td>
                    <td>{{ $item->dispositivo->nombre_dispositivo ?? 'N/A' }}</td>
                    <td><span class="badge bg-primary">{{ $item->zk_user_id }}</span></td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</td>
                    <td class="no-sort no-click bread-actions text-right">
                        <a href="{{ route('admin.dispositivo-empleado.edit', $item->id) }}" title="Editar" class="btn btn-sm btn-primary edit">
                            <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Editar</span>
                        </a>
                        <button title="Borrar" class="btn btn-sm btn-danger delete" data-id="{{ $item->id }}" data-toggle="modal" data-target="#delete_modal">
                            <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Borrar</span>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No se encontraron registros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="col-md-12">
    <div class="col-md-6" style="overflow-x:auto">
        @if(count($mapeos) > 0)
            <p class="text-muted">Mostrando del {{ $mapeos->firstItem() }} al {{ $mapeos->lastItem() }} de {{ $mapeos->total() }} registros.</p>
        @endif
    </div>
    <div class="col-md-6">
        <nav class="pull-right">
            {{ $mapeos->links() }}
        </nav>
    </div>
</div>
