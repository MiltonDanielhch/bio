@extends('voyager::master')

@section('page_title', 'Gestionar Registros de Asistencia')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-list"></i> Registros de Asistencia
        </h1>
        <a href="{{ route('admin.registros-asistencia.create') }}" class="btn btn-success btn-add-new">
            <i class="voyager-plus"></i> <span>Añadir Registro</span>
        </a>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-4 pull-right">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search-input" name="s" placeholder="Buscar por nombre o dispositivo...">
                                        <span class="input-group-btn">
                                            <button class="btn btn-info btn-search" type="button">
                                                <i class="voyager-search"></i>
                                            </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="list-view">
                           <p class="text-center"><i class="voyager-watch"></i> Cargando...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de eliminación --}}
    <div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><i class="voyager-trash"></i> ¿Estás seguro de que quieres eliminar este registro de asistencia?</h4>
                </div>
                <div class="modal-footer">
                    <form action="#" id="delete_form" method="POST">
                        {{ method_field('DELETE') }}
                        {{ csrf_field() }}
                        <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Sí, ¡Bórralo!">
                    </form>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
    $(document).ready(function() {
        // Carga inicial
        loadListView();

        // Paginación y búsqueda
        $('body').on('click', '.pagination a, .btn-search', function(e) {
            e.preventDefault();
            let url = $(this).is('a') ? $(this).attr('href') : '{{ route("admin.registros-asistencia.ajax.list") }}';
            let search = $('#search-input').val();
            loadListView(url, { search: search });
        });

        // Modal de eliminación
        $('body').on('click', '.delete', function (e) {
            var form = $('#delete_form')[0];
            form.action = '{{ route("admin.registros-asistencia.destroy", ["registros_asistencium" => "__id"]) }}'.replace('__id', $(this).data('id'));
        });

        function loadListView(url = '{{ route("admin.registros-asistencia.ajax.list") }}', params = {}) {
            $('#list-view').html('<p class="text-center"><i class="voyager-watch"></i> Cargando...</p>');
            $.get(url, params, function (data) {
                $('#list-view').html(data);
            });
        }
    });
</script>
@stop
