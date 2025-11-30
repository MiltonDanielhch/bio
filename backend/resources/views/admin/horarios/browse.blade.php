@extends('voyager::master')

@section('page_title', 'Asignaciones de Horario')

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
                                <i class="voyager-calendar"></i> Asignaciones de Horario
                            </h1>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 30px;">
                            @can('create', App\Models\AsignacionHorario::class)
                                <a href="{{ route('admin.asignacion-horarios.create') }}" class="btn btn-success">
                                    <i class="voyager-plus"></i> Nueva Asignación
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

@section('css')
<style>
    .select2-container{width:100%!important}
    .badge{font-size:100%}
</style>
@stop

@push('javascript')
<script>
    $(document).ready(function () {
        // Script para auto-cerrar alertas
        setTimeout(() => $('.auto-dismiss').fadeOut('slow', (el) => $(el).remove()), 5000);
    });
</script>
@include('admin.partials.list-browse-script', ['listUrl' => route('admin.asignacion-horarios.ajax.list')])
@endpush
