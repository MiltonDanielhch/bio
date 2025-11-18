<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th>Nombre del Horario</th>
                    <th>Empresa</th>
                    <th>Detalles</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $h)
                <tr>
                    <td>{{ $h->id }}</td>
                    <td>{{ $h->nombre_horario }}</td>
                    <td>{{ optional($h->empresa)->nombre_empresa }}</td>
                    <td>
                        <small>
                            Entrada: {{ \Carbon\Carbon::parse($h->hora_entrada)->format('h:i A') }} |
                            Salida: {{ \Carbon\Carbon::parse($h->hora_salida)->format('h:i A') }}
                        </small>
                    </td>
                    <td>
                        <span class="label label-{{ $h->estado == 'activo' ? 'success' : 'default' }}">
                            {{ ucfirst($h->estado) }}
                        </span>
                    </td>
                    <td class="text-right" style="width: 30%">
                        @can('view', $h)
                            <a href="{{ route('admin.horarios.show', $h) }}" title="Ver" class="btn btn-sm btn-warning">
                                <i class="voyager-eye"></i> Ver
                            </a>
                        @endcan
                        @can('update', $h)
                            <a href="{{ route('admin.horarios.edit', $h) }}" title="Editar" class="btn btn-sm btn-primary">
                                <i class="voyager-edit"></i> Editar
                            </a>
                        @endcan
                        @can('delete', $h)
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Borrar"
                                    onclick="deleteItem('{{ route('admin.horarios.destroy', $h) }}', '{{ $h->nombre_horario }}')"
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
                            No se encontraron horarios
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
