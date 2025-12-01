@extends('voyager::master')

@section('page_title', 'Mapeo Dispositivo-Empleado')

@section('page_header')
    <div class="container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="margin-bottom: 0;">
                    <div class="panel-body" style="padding: 0;">
                        <div class="col-md-8" style="padding: 0;">
                            <h1 class="page-title">
                                <i class="voyager-data"></i> Mapeo Dispositivo-Empleado
                            </h1>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 30px;">
                            @can('create', App\Models\DispositivoEmpleado::class)
                                <a href="{{ route('admin.dispositivo-empleado.create') }}" class="btn btn-success btn-add-new">
                                    <i class="voyager-plus"></i> <span>Crear Mapeo</span>
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-4" style="margin-bottom: 0">
                                <div class="dataTables_length" id="dataTable">
                                    <label>Mostrar
                                        <select id="select-paginate" class="form-control input-sm">
                                            <option value="10">10</option>
                                            <option value="25">25</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-5">
                                <div id="dataTable_filter" class="dataTables_filter">
                                    <label>Filtrar por Dispositivo:
                                        <select id="filter-dispositivo" class="form-control input-sm">
                                            <option value="">-- Todos los Dispositivos --</option>
                                            @foreach($dispositivos as $dispositivo)
                                                <option value="{{ $dispositivo->id }}">{{ $dispositivo->nombre_dispositivo }}</option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3" style="margin-bottom: 0">
                                <input type="text" id="search" class="form-control" placeholder="Buscar...">
                                <br>
                            </div>
                        </div>
                        <div class="row" id="list-container" style="min-height: 120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal eliminar --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> ¿Estás seguro de que quieres eliminar?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        @method('DELETE') @csrf
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Sí, eliminar">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@push('javascript')
    <script>
        $(document).ready(function () {
            // Script para auto-cerrar alertas
            setTimeout(() => $('.auto-dismiss').fadeOut('slow', (el) => $(el).remove()), 5000);

            // Cuando el filtro de dispositivo cambie, recargamos la lista.
            // Este ID debe coincidir con el ID del script parcial.
            $('#filter-dispositivo').on('change', function() {
                list(1); // Llama a la función list del script parcial, reseteando a la página 1
            });
        });
    </script>
    @include('admin.partials.list-browse-script', ['listUrl' => route('admin.dispositivo-empleado.ajax.list')])
@endpush
