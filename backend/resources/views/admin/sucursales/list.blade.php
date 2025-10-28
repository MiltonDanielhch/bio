<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Empresa</th>
                    <th>Ciudad</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sucursales as $s)
                <tr>
                    <td>{{ $s->id }}</td>
                    <td>{{ $s->nombre_sucursal }}</td>
                    <td>{{ optional($s->empresa)->nombre_empresa }}</td>
                    <td>{{ $s->ciudad ?? '-' }}</td>
                    <td>
                        <span class="label label-{{ $s->estado == 'activo' ? 'success' : 'default' }}">
                            {{ ucfirst($s->estado) }}
                        </span>
                    </td>
                    <td class="text-right" style="width: 30%">
                        @can('view', $s)
                            <a href="{{ route('admin.sucursales.show', $s) }}" title="Ver" class="btn btn-sm btn-warning">
                                <i class="voyager-eye"></i> Ver
                            </a>
                        @endcan
                        @can('update', $s)
                            <a href="{{ route('admin.sucursales.edit', $s) }}" title="Editar" class="btn btn-sm btn-primary">
                                <i class="voyager-edit"></i> Editar
                            </a>
                        @endcan
                        @can('delete', $s)
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Borrar"
                                    onclick="deleteItem('{{ route('admin.sucursales.destroy', $s) }}', '{{ $s->nombre_sucursal }}')"
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
                            No se encontraron sucursales
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
        @if($sucursales->count())
            Mostrando del {{ $sucursales->firstItem() }} al {{ $sucursales->lastItem() }} de {{ $sucursales->total() }} registros.
        @endif
    </div>
    <div class="col-md-8 text-right">
        <nav class="text-right">{{ $sucursales->links() }}</nav>
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
