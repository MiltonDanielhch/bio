@extends('voyager::master')

@section('page_title', 'Viendo Dispositivos')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-harddrive"></i> Dispositivos
        </h1>
        {{-- @can('add', \App\Models\Dispositivo::class) --}}
            <a href="{{ route('admin.dispositivos.create') }}" class="btn btn-success btn-add-new">
                <i class="voyager-plus"></i> <span>Añadir nuevo</span>
            </a>
        {{-- @endcan --}}
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
                            <div class="col-sm-10">
                                <div class="dataTables_length" id="dataTable_length">
                                    <label>Mostrar <select id="select-paginate" class="form-control input-sm"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select> registros</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <input type="text" id="input-search" class="form-control" placeholder="Buscar">
                            </div>
                        </div>
                        <div id="div-results" style="min-height: 120px"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
    .loading-icon {
        animation: spin 1.5s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@stop

@section('javascript')
    <script>
        let countPage = 10;
        let searchTimeout;

        $(document).ready(function() {
            list();

            $('#input-search').on('keyup', function (e) {
                if (e.keyCode === 13) list(1);
            });

            $('#input-search').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => list(1), 500);
            });

            $('#select-paginate').change(function () {
                countPage = $(this).val();
                list(1);
            });
        });

        function list(page = 1) {
            let url = '{{ route("admin.dispositivos.ajax.list") }}';
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
                    $('#div-results').html('<div class="alert alert-danger text-center">Error al cargar los datos.</div>');
                }
            });
        }
    </script>
@stop
