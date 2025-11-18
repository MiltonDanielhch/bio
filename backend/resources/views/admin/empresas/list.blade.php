<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>RUC</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $e)
                <tr>
                    <td>{{ $e->id }}</td>
                    <td>{{ $e->nombre_empresa }}</td>
                    <td>{{ $e->ruc }}</td>
                    <td>{{ $e->telefono ?? '-' }}</td>
                    <td>
                        <span class="label label-{{ $e->estado == 'activo' ? 'success' : 'default' }}">
                            {{ ucfirst($e->estado) }}
                        </span>
                    </td>
                    <td class="text-right" style="width: 30%">
                        @can('view', $e)
                            <a href="{{ route('admin.empresas.show', $e) }}" title="Ver" class="btn btn-sm btn-warning">
                                <i class="voyager-eye"></i> Ver
                            </a>
                        @endcan
                        @can('update', $e)
                            <a href="{{ route('admin.empresas.edit', $e) }}" title="Editar" class="btn btn-sm btn-primary">
                                <i class="voyager-edit"></i> Editar
                            </a>
                        @endcan
                        @can('delete', $e)
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Borrar"
                                    onclick="deleteItem('{{ route('admin.empresas.destroy', $e) }}', '{{ $e->nombre_empresa }}')"
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
                            No se encontraron empresas
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
        @if($items->count())
            Mostrando del {{ $items->firstItem() }} al {{ $items->lastItem() }} de {{ $items->total() }} registros.
        @endif
    </div>
    <div class="col-md-8 text-right">
        <nav class="text-right">{{ $items->links() }}</nav>
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
