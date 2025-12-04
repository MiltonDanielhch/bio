<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>IP</th>
                <th>Sucursal</th>
                <th>Estado</th>
                <th>Creado por</th>
                <th>Creado el</th>
                <th class="actions text-right">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->nombre_dispositivo }}</td>
                    <td>{{ $item->direccion_ip }}</td>
                    <td>{{ $item->sucursal->nombre_sucursal ?? 'N/A' }}</td>
                    <td><span class="badge bg-{{ $item->estado == 'activo' ? 'success' : 'danger' }}">{{ ucfirst($item->estado) }}</span></td>
                    <td>{{ $item->creador->name ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</td>
                    <td class="no-sort no-click bread-actions text-right">
                        @can('view', $item)
                            <a href="{{ route('admin.dispositivos.show', $item->id) }}" title="Ver" class="btn btn-sm btn-warning view">
                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                            </a>
                        @endcan
                        @can('update', $item)
                            <a href="{{ route('admin.dispositivos.edit', $item->id) }}" title="Editar" class="btn btn-sm btn-primary edit">
                                <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Editar</span>
                            </a>
                        @endcan
                        
                        {{-- Botón: Asignar Empleados --}}
                        @can('update', $item)
                            <a href="{{ route('admin.dispositivos.assign_employees', $item->id) }}" title="Asignar Empleados" class="btn btn-sm btn-dark" style="background-color: #333; color: #fff;">
                                <i class="voyager-people"></i> <span class="hidden-xs hidden-sm">Empleados</span>
                            </a>
                        @endcan
                        {{-- Botón: Sincronizar Asistencias (Dispositivo → Sistema) --}}
                        @can('update', $item)
                            <form action="{{ route('admin.dispositivos.sync_now', $item->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" title="Sincronizar Asistencias" class="btn btn-sm btn-info">
                                    <i class="voyager-download"></i> <span class="hidden-xs hidden-sm">Asistencias</span>
                                </button>
                            </form>
                        @endcan
                        
                        {{-- Botón: Sincronizar Usuarios (Sistema → Dispositivo) --}}
                        @can('update', $item)
                            <form action="{{ route('admin.dispositivos.sync_users', $item->id) }}" method="POST" style="display: inline-block;">
                                @csrf
                                <button type="submit" title="Sincronizar Usuarios" class="btn btn-sm btn-success">
                                    <i class="voyager-upload"></i> <span class="hidden-xs hidden-sm">Usuarios</span>
                                </button>
                            </form>
                        @endcan
                        
                        @can('delete', $item)
                            <button title="Borrar"
                                    class="btn btn-sm btn-danger delete"
                                    data-toggle="modal"
                                    data-target="#delete_modal"
                                    onclick="deleteItem('{{ route('admin.dispositivos.destroy', $item->id) }}', '{{ $item->nombre_dispositivo }}')">
                                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Borrar</span>
                            </button>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">No se encontraron registros.</td>
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
