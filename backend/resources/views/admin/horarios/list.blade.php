<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Empleado</th>
                    <th>Horario</th>
                    <th>Vigencia</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($asignaciones as $a)
                <tr>
                    <td>{{ $a->id }}</td>
                    <td>
                        <strong>{{ $a->empleado->codigo_empleado }}</strong><br>
                        {{ $a->empleado->nombres }} {{ $a->empleado->apellidos }}
                    </td>
                    <td>
                        {{ $a->horario->nombre_horario }}<br>
                        <small class="text-muted">{{ $a->horario->empresa->nombre_empresa }}</small>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($a->fecha_inicio)->format('d/m/Y') }}
                        @if($a->fecha_fin)
                            <br>al<br>{{ \Carbon\Carbon::parse($a->fecha_fin)->format('d/m/Y') }}
                        @else
                            <br><span class="text-muted">Indefinido</span>
                        @endif
                    </td>
                    <td>
                        <span class="label label-{{ $a->activo ? 'success' : 'default' }}">
                            {{ $a->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-right" style="width: 30%">
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
                                    onclick="deleteItem('{{ route('admin.asignacion-horarios.destroy', $a) }}', '{{ $a->empleado->nombres }} {{ $a->empleado->apellidos }}')"
                                    data-toggle="modal"
                                    data-target="#delete_modal">
                                <i class="voyager-trash"></i> Borrar
                            </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
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
