@extends('voyager::master')

@section('page_title', 'Reportes de Asistencia')

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
                                <i class="voyager-bar-chart"></i> Reportes de Asistencia
                            </h1>
                        </div>
                        <div class="col-sm-4 text-right">
                            @can('add_reportes_asistencia')
                            <a href="{{ route('admin.reportes-asistencia.create') }}" class="btn btn-success btn-add-new">
                                <i class="voyager-plus"></i> <span>Generar Reporte</span>
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="text" id="search" class="form-control" placeholder="Buscar por nombre del reporte...">
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
                <h4 class="modal-title"><i class="voyager-trash"></i> ¿Estás seguro de que quieres eliminar este reporte y su archivo?</h4>
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

@include('admin.partials.list-browse-script', ['listUrl' => route('admin.reportes-asistencia.ajax.list')])
