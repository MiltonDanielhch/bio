<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Empleado</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidencias as $incidencia)
                <tr>
                    <td>{{ $incidencia->id }}</td>
                    <td>
                        {{ optional($incidencia->empleado)->apellidos }} {{ optional($incidencia->empleado)->nombres }}<br>
                        <small>{{ optional($incidencia->empleado)->codigo_empleado }}</small>
                    </td>
                    <td>{{ optional($incidencia->tipoIncidencia)->nombre }}</td>
                    <td>{{ \Carbon\Carbon::parse($incidencia->fecha_incidencia)->format('d/m/Y') }}</td>
                    <td>{{ Str::limit($incidencia->motivo, 50) }}</td>
                    <td>
                        @php
                            $labelClass = 'default';
                            if ($incidencia->estado == 'aprobado') $labelClass = 'success';
                            if ($incidencia->estado == 'rechazado') $labelClass = 'danger';
                        @endphp
                        <span class="label label-{{ $labelClass }}">{{ ucfirst($incidencia->estado) }}</span>
                    </td>
                    <td class="text-right" style="width: 20%">
                        @can('edit_incidencias')
                            <a href="{{ route('admin.incidencias.edit', $incidencia) }}" title="Editar" class="btn btn-sm btn-primary">
                                <i class="voyager-edit"></i> Editar
                            </a>
                        @endcan
                        @can('delete_incidencias')
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Borrar"
                                    onclick="deleteItem('{{ route('admin.incidencias.destroy', $incidencia) }}')"
                                    data-toggle="modal"
                                    data-target="#delete_modal">
                                <i class="voyager-trash"></i> Borrar
                            </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <h5 class="text-center" style="margin-top: 50px">
                            <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                            <br><br>
                            No se encontraron incidencias.
                        </h5>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4 text-muted">
        @if($incidencias->count())
            Mostrando del {{ $incidencias->firstItem() }} al {{ $incidencias->lastItem() }} de {{ $incidencias->total() }} registros.
        @endif
    </div>
    <div class="col-md-8 text-right">
        <nav class="text-right">{{ $incidencias->links() }}</nav>
    </div>
</div>

@include('admin.partials.list-pagination-script')

