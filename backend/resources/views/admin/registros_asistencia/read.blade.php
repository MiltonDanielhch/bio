@extends('voyager::master')

@section('page_title', 'Ver Registro de Asistencia')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-eye"></i> Viendo Registro de Asistencia #{{ $registro->id }}
        </h1>
        <a href="{{ route('admin.registros-asistencia.index') }}" class="btn btn-warning">
            <i class="voyager-list"></i> <span>Volver a la lista</span>
        </a>
    </div>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="panel-heading" style="border-bottom:0;">
                                    <h3 class="panel-title">Empleado</h3>
                                </div>
                                <div class="panel-body" style="padding-top:0;">
                                    <p>{{ $registro->empleado->full_name ?? 'N/A' }} ({{ $registro->empleado->codigo_empleado ?? 'N/A' }})</p>
                                </div>
                                <hr style="margin:0;">
                            </div>
                            <div class="col-md-6">
                                <div class="panel-heading" style="border-bottom:0;">
                                    <h3 class="panel-title">Dispositivo</h3>
                                </div>
                                <div class="panel-body" style="padding-top:0;">
                                    <p>{{ $registro->dispositivo->nombre_dispositivo ?? 'N/A' }}</p>
                                </div>
                                <hr style="margin:0;">
                            </div>
                            <div class="col-md-6">
                                <div class="panel-heading" style="border-bottom:0;">
                                    <h3 class="panel-title">Fecha y Hora del Marcaje</h3>
                                </div>
                                <div class="panel-body" style="padding-top:0;">
                                    <p>{{ $registro->fecha_hora->format('d/m/Y H:i:s') }}</p>
                                </div>
                                <hr style="margin:0;">
                            </div>
                            <div class="col-md-3">
                                <div class="panel-heading" style="border-bottom:0;">
                                    <h3 class="panel-title">Tipo de Marcaje</h3>
                                </div>
                                <div class="panel-body" style="padding-top:0;">
                                    <p>{{ ucfirst(str_replace('_', ' ', $registro->tipo_marcaje)) }}</p>
                                </div>
                                <hr style="margin:0;">
                            </div>
                            <div class="col-md-3">
                                <div class="panel-heading" style="border-bottom:0;">
                                    <h3 class="panel-title">Tipo de Verificación</h3>
                                </div>
                                <div class="panel-body" style="padding-top:0;">
                                    <p>{{ ucfirst($registro->tipo_verificacion) }}</p>
                                </div>
                                <hr style="margin:0;">
                            </div>
                            <div class="col-md-4">
                                <div class="panel-heading" style="border-bottom:0;">
                                    <h3 class="panel-title">Estado de Validación</h3>
                                </div>
                                <div class="panel-body" style="padding-top:0;">
                                    <p><span class="badge badge-primary" style="font-size: 14px;">{{ ucfirst($registro->estado_validacion) }}</span></p>
                                </div>
                                <hr style="margin:0;">
                            </div>
                            <div class="col-md-4">
                                <div class="panel-heading" style="border-bottom:0;">
                                    <h3 class="panel-title">Procesado</h3>
                                </div>
                                <div class="panel-body" style="padding-top:0;">
                                    <p>{{ $registro->procesado ? 'Sí' : 'No' }}</p>
                                </div>
                                <hr style="margin:0;">
                            </div>
                            <div class="col-md-12">
                                <div class="panel-heading" style="border-bottom:0;">
                                    <h3 class="panel-title">Observaciones</h3>
                                </div>
                                <div class="panel-body" style="padding-top:0;">
                                    <p>{{ $registro->observaciones ?? 'Sin observaciones.' }}</p>
                                </div>
                                <hr style="margin:0;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
