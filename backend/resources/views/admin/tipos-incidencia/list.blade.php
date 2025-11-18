<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $tipo)
                <tr>
                    <td>{{ $tipo->id }}</td>
                    <td>{{ $tipo->nombre }}</td>
                    <td>{{ Str::limit($tipo->descripcion, 80) }}</td>
                    <td class="text-right" style="width: 20%">
                        @can('update', $tipo)
                            <a href="{{ route('admin.tipos-incidencia.edit', $tipo) }}" title="Editar" class="btn btn-sm btn-primary">
                                <i class="voyager-edit"></i> Editar
                            </a>
                        @endcan
                        @can('delete', $tipo)
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Borrar"
                                    onclick="deleteItem('{{ route('admin.tipos-incidencia.destroy', $tipo) }}', '{{ $tipo->nombre }}')"
                                    data-toggle="modal"
                                    data-target="#delete_modal">
                                <i class="voyager-trash"></i> Borrar
                            </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <h5 class="text-center" style="margin-top: 50px">
                            <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                            <br><br>
                            No se encontraron tipos de incidencia.
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

@include('admin.partials.list-pagination-script')
