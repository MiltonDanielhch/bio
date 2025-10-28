<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Sucursal</th>
                    <th>Jefe</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departamentos as $d)
                <tr>
                    <td>{{ $d->id }}</td>
                    <td>{{ $d->nombre_departamento }}</td>
                    <td>
                        {{ optional($d->sucursal)->nombre_sucursal }}
                        <small class="text-muted">
                            ({{ optional($d->sucursal->empresa)->nombre_empresa }})
                        </small>
                    </td>
                    <td>
                        @if($d->jefe)
                            {{ $d->jefe->nombres }} {{ $d->jefe->apellidos }}
                        @else
                            <span class="text-muted">Sin jefe</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $label = match($d->estado) {
                                'activo'     => 'success',
                                'inactivo'   => 'danger',
                                'vacaciones' => 'info',
                                'licencia'   => 'warning',
                                default      => 'default'
                            };
                        @endphp
                        <span class="label label-{{ $label }}">
                            {{ ucfirst($d->estado) }}
                        </span>
                    </td>
                    <td class="text-right" style="width: 30%">
                        @can('view', $d)
                            <a href="{{ route('admin.departamentos.show', $d) }}" title="Ver" class="btn btn-sm btn-warning">
                                <i class="voyager-eye"></i> Ver
                            </a>
                        @endcan
                        @can('update', $d)
                            <a href="{{ route('admin.departamentos.edit', $d) }}" title="Editar" class="btn btn-sm btn-primary">
                                <i class="voyager-edit"></i> Editar
                            </a>
                        @endcan
                        @can('delete', $d)
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Borrar"
                                    onclick="deleteItem('{{ route('admin.departamentos.destroy', $d) }}', '{{ $d->nombre_departamento }}')"
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
                            No se encontraron departamentos
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
        @if($departamentos->count())
            Mostrando del {{ $departamentos->firstItem() }} al {{ $departamentos->lastItem() }} de {{ $departamentos->total() }} registros.
        @endif
    </div>
    <div class="col-md-8 text-right">
        <nav class="text-right">{{ $departamentos->links() }}</nav>
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
