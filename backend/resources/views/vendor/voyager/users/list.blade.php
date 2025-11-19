<div class="col-md-12">
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email.</th>
                    <th>Role</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>
                        @if($item->person_id)
                            <table>
                                @php
                                    $image = asset('images/default.jpg');
                                    if($item->person->image){
                                        $image = asset('storage/'.str_replace('.', '-cropped.', $item->person->image));
                                    }
                                @endphp
                                <tr>
                                    <td ><img src="{{ $image }}" alt="{{ $item->person->first_name }} " style="width: 60px; height: 60px; border-radius: 30px; margin-right: 10px"></td>
                                    <td>
                                        <small>CI:</small> {{$item->person->ci}} <br>
                                        {{ strtoupper($item->person->first_name) }} {{ strtoupper($item->person->last_name) }}
                                    </td>
                                </tr>
                            </table>
                        @else
                            {{$item->name}}
                        @endif
                    </td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->role->name ?? 'Sin Permiso' }}</td>
                    <td style="text-align: center">
                        @if ($item->status==1)
                            <label class="label label-success">Activo</label>
                        @else
                            <label class="label label-warning">Inactivo</label>
                        @endif
                    </td>
                    <td class="no-sort no-click bread-actions text-right">
                        {{-- La ruta 'show' fue excluida, por lo que el botón "Ver" se comenta --}}
                        {{-- @can('view', $item)
                            <a href="{{ route('admin.users.show', $item) }}" title="Ver" class="btn btn-sm btn-warning view">
                                <i class="voyager-eye"></i> <span class="hidden-xs hidden-sm">Ver</span>
                            </a>
                        @endcan --}}
                        @can('update', $item)
                            <a href="{{ route('admin.users.edit', $item) }}" title="Editar" class="btn btn-sm btn-primary edit">
                                <i class="voyager-edit"></i> <span class="hidden-xs hidden-sm">Editar</span>
                            </a>
                        @endcan
                        @can('delete', $item)
                            <a href="#" onclick="deleteItem('{{ route('admin.users.destroy', $item) }}')" title="Eliminar" data-toggle="modal" data-target="#modal-delete" class="btn btn-sm btn-danger delete">
                                <i class="voyager-trash"></i> <span class="hidden-xs hidden-sm">Eliminar</span>
                            </a>
                        @endcan
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <h5 class="text-center" style="margin-top: 50px">
                                <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                                <br><br>
                                No hay resultados
                            </h5>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4" style="overflow-x:auto">
        @if(count($items)>0)
            <p class="text-muted">Mostrando del {{$items->firstItem()}} al {{$items->lastItem()}} de {{$items->total()}} registros.</p>
        @endif
    </div>
    <div class="col-md-8" style="overflow-x:auto">
        <nav class="text-right">
            {{ $items->links() }}
        </nav>
    </div>
</div>

<script>
   var page = "{{ request('page') }}";
    $(document).ready(function(){
        $('.page-link').click(function(e){
            e.preventDefault();
            let link = $(this).attr('href');
            if(link){
                // Se usa URL para obtener el parámetro de forma segura
                let url = new URL(link);
                let page = url.searchParams.get('page') || 1;
                list(page);
            }
        });
    });
</script>
