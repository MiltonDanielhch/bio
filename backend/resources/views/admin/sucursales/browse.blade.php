@extends('voyager::master')

@section('page_title', 'Sucursales')

@section('page_header')
    <div class="container-fluid">
        @include('voyager::alerts')

        @if(session('message'))
            <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible auto-dismiss">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('message') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="margin-bottom: 0;">
                    <div class="panel-body" style="padding: 0;">
                        <div class="col-md-8" style="padding: 0;">
                            <h1 class="page-title">
                                <i class="voyager-shop"></i> Sucursales
                            </h1>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 30px;">
                            @can('create', App\Models\Sucursal::class)
                                <a href="{{ route('admin.sucursales.create') }}" class="btn btn-success">
                                    <i class="voyager-plus"></i> Nueva Sucursal
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
    @include('voyager::alerts')
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-sm-9" style="margin-bottom: 0">
                            <div class="dataTables_length" id="dataTable">
                                <label>Mostrar
                                    <select id="select-paginate" class="form-control input-sm">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select> registros
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-3" style="margin-bottom: 0">
                            <input type="text" id="input-search" class="form-control" placeholder="Buscar...">
                            <br>
                        </div>
                    </div>
                    <div class="row" id="div-results" style="min-height: 120px"></div>
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
                <h4 class="modal-title"><i class="voyager-trash"></i> ¿Desea eliminar esta sucursal?</h4>
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

@section('css')
<style>
    .select2-container{width:100%!important}
    .badge{font-size:100%}
    .badge-success{background-color:#28a745}
    .badge-primary{background-color:#007bff}
    .badge-warning{background-color:#ffc107;color:#212529}
    .badge-danger{background-color:#dc3545}

    .loading-icon {
        animation: spin 1.5s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
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
        $('.modal-title').html('<i class="voyager-trash"></i> ¿Eliminar la sucursal "<strong>' + nombre + '</strong>"?');
    }

    function list(page = 1) {
        let url = '{{ url("admin/sucursales/ajax/list") }}';
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
