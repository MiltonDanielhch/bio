@extends('voyager::master')

@section('page_title', 'Tipos de Incidencia')

@section('content')
<div class="page-content container-fluid">
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-8">
                            <h1 class="page-title">
                                <i class="voyager-tag"></i> Tipos de Incidencia
                            </h1>
                        </div>
                        <div class="col-sm-4 text-right">
                            @can('add_tipos_incidencia')
                            <a href="{{ route('admin.tipos-incidencia.create') }}" class="btn btn-success btn-add-new">
                                <i class="voyager-plus"></i> <span>Agregar Nuevo</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" id="search" class="form-control" placeholder="Buscar por nombre o descripción...">
                            </div>
                        </div>
                    </div>
                    <div id="list-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-danger fade" tabindex="-1" id="delete_modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="voyager-trash"></i> ¿Estás seguro de que quieres eliminar este tipo de incidencia?</h4>
            </div>
            <div class="modal-footer">
                <form id="delete_form" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="submit" class="btn btn-danger pull-right delete-confirm" value="Sí, ¡Bórralo!">
                </form>
                <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
@stop

@push('javascript')
<script>
    let countPage = 10;

    $(document).ready(function () {
        setTimeout(function() {
            $('.auto-dismiss').fadeOut('slow', function() { $(this).remove(); });
        }, 5000);
        $('.auto-dismiss .close').click(function(e) {
            e.preventDefault();
            $(this).closest('.alert').fadeOut('slow', function() { $(this).remove(); });
        });

        list();

        $('#input-search').on('keyup', function (e) {
            if (e.keyCode === 13) list(1);
        });
        let searchTimeout;
        $('#input-search').on('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => list(1), 500);
        });

        $('#select-paginate').change(function () {
            countPage = $(this).val();
            list(1);
        });
    });

    function deleteItem(url, nombre) {
        $('#delete_form').attr('action', url);
        $('.modal-title').html('<i class="voyager-trash"></i> ¿Eliminar la empresa "<strong>' + nombre + '</strong>"?');
    }

    function list(page = 1) {
        let url = '{{ url("admin/empresas/ajax/list") }}';
        let search = $('#input-search').val()?.trim() || '';

        $('#div-results').html(`
            <div class="text-center" style="padding: 40px">
                <i class="voyager-refresh voyager-2x loading-icon"></i><br>Cargando...
            </div>
        `);

        $.ajax({
            url: `${url}?search=${encodeURIComponent(search)}&paginate=${countPage}&page=${page}`,
            type: 'get',
            success: function (response) {
                $('#div-results').html(response);
            },
            error: function (xhr) {
                console.error(xhr);
                $('#div-results').html(`
                    <div class="alert alert-danger text-center">
                        <i class="voyager-warning"></i><br>Error al cargar los datos.<br>
                        <button onclick="list(${page})" class="btn btn-xs btn-default mt-2">Reintentar</button>
                    </div>
                `);
            }
        });
    }
</script>
@endpush

