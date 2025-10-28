<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Empleado</th>
                    <th>Empresa</th>
                    <th>Horario</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Activo</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asignaciones as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>{{ optional($a->empleado)->nombres }} {{ optional($a->empleado)->apellidos }}</td>
                    <td>{{ optional($a->empleado->empresa)->nombre_empresa }}</td>
                    <td>{{ optional($a->horario)->nombre_horario }}</td>
                    <td>{{ $a->fecha_inicio->format('d/m/Y') }}</td>
                    <td>{{ $a->fecha_fin?->format('d/m/Y') ?? 'Indefinido' }}</td>
                    <td>
                        @if($a->activo)
                            <span class="label label-success">Activo</span>
                        @else
                            <span class="label label-default">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-right">
                        @can('view', $a)
                            <a href="{{ route('admin.asignacion-horarios.show', $a) }}" title="Ver" class="btn btn-sm btn-warning">
                                <i class="voyager-eye"></i> Ver
                            </a>
                        @endcan
                        @can('update', $a)
                            <a href="{{ route('admin.asignacion-horarios.edit', $a) }}" title="Editar" class="btn btn-sm btn-primary">
                                <i class="voyager-edit"></i> Editar
                            </a>
                        @endcan
                        @can('delete', $a)
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Borrar"
                                    onclick="deleteItem('{{ route('admin.asignacion-horarios.destroy', $a) }}', '{{ optional($a->empleado)->nombres }} {{ optional($a->empleado)->apellidos }}')"
                                    data-toggle="modal"
                                    data-target="#delete_modal">
                                <i class="voyager-trash"></i> Borrar
                            </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <h5 class="text-center" style="margin-top: 50px">
                            <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                            <br><br>
                            No se encontraron asignaciones
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
        @if($asignaciones->count())
            Mostrando del {{ $asignaciones->firstItem() }} al {{ $asignaciones->lastItem() }} de {{ $asignaciones->total() }} registros.
        @endif
    </div>
    <div class="col-md-8 text-right">
        <nav class="text-right">{{ $asignaciones->links() }}</nav>
    </div>
</div>

@if(request()->ajax())
<script>
    $(document).ready(function(){
        $('.page-link').click(function(e){
            e.preventDefault();
            const url = new URL($(this).attr('href'));
            const page = url.searchParams.get('page') || 1;
            if (typeof list === 'function') list(page);
            else window.location.href = $(this).attr('href');
        });
    });
</script>
@endif
