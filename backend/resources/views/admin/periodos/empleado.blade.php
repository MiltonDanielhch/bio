@extends('voyager::master')

@section('page_title', 'Detalle Empleado - ' . ($resumen->empleado->nombre_completo ?? 'N/A'))

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-person"></i> 
        Detalle de Asistencia - {{ $resumen->empleado->nombre_completo ?? 'N/A' }}
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <!-- Info del Empleado y Período -->
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">Información del Empleado</h3>
                    </div>
                    <div class="panel-body">
                        <p><strong>Código:</strong> {{ $resumen->empleado->codigo_empleado ?? '-' }}</p>
                        <p><strong>Nombre:</strong> {{ $resumen->empleado->nombre_completo ?? '-' }}</p>
                        <p><strong>Departamento:</strong> {{ $resumen->empleado->departamento->nombre_departamento ?? '-' }}</p>
                        <p><strong>DNI/CI:</strong> {{ $resumen->empleado->dni ?? '-' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">Período</h3>
                    </div>
                    <div class="panel-body">
                        <p><strong>Nombre:</strong> {{ $periodo->nombre }}</p>
                        <p><strong>Código:</strong> {{ $periodo->codigo }}</p>
                        <p><strong>Fechas:</strong> 
                            {{ $periodo->fecha_inicio->format('d/m/Y') }} - {{ $periodo->fecha_fin->format('d/m/Y') }}
                        </p>
                        <p><strong>Duración:</strong> {{ $periodo->duracion_dias }} días</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen de Asistencia -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">Resumen del Período</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="well text-center">
                                    <h3 class="text-primary">{{ $resumen->total_dias_laborables }}</h3>
                                    <p>Días Laborables</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="well text-center">
                                    <h3 class="text-success">{{ $resumen->total_dias_trabajados }}</h3>
                                    <p>Días Trabajados</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="well text-center">
                                    <h3 class="text-danger">{{ $resumen->total_dias_falta }}</h3>
                                    <p>Faltas</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="well text-center">
                                    <h3 class="text-info">{{ $resumen->total_dias_incidencia }}</h3>
                                    <p>Incidencias Aprobadas</p>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-2">
                                <strong>Tardanzas:</strong><br>
                                <span class="text-{{ $resumen->total_tardanzas > 0 ? 'warning' : 'success' }}">
                                    {{ $resumen->total_tardanzas }} veces
                                </span>
                            </div>
                            <div class="col-md-2">
                                <strong>Minutos de Atraso:</strong><br>
                                <span class="text-muted">{{ $resumen->total_minutos_tardanza }} min</span>
                            </div>
                            <div class="col-md-2">
                                <strong>Promedio Tardanza:</strong><br>
                                <span class="text-muted">{{ number_format($resumen->promedio_tardanza, 1) }} min</span>
                            </div>
                            <div class="col-md-2">
                                <strong>Horas Trabajadas:</strong><br>
                                <span class="text-success">{{ number_format($resumen->total_horas_trabajadas, 2) }} hrs</span>
                            </div>
                            <div class="col-md-2">
                                <strong>Horas Programadas:</strong><br>
                                <span class="text-muted">{{ number_format($resumen->total_horas_programadas, 2) }} hrs</span>
                            </div>
                            <div class="col-md-2">
                                <strong>% Asistencia:</strong><br>
                                <span class="text-{{ $resumen->porcentaje_asistencia >= 95 ? 'success' : ($resumen->porcentaje_asistencia >= 80 ? 'warning' : 'danger') }}">
                                    <strong>{{ number_format($resumen->porcentaje_asistencia, 1) }}%</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Horas Extra -->
        @if($resumen->total_horas_extra > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Horas Extra</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="well text-center">
                                        <h3 class="text-primary">{{ number_format($resumen->total_horas_extra, 2) }}</h3>
                                        <p>Total Horas Extra</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="well text-center">
                                        <h3>{{ number_format($resumen->total_horas_extra_25, 2) }}</h3>
                                        <p>Horas Extra al 25%</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="well text-center">
                                        <h3>{{ number_format($resumen->total_horas_extra_50, 2) }}</h3>
                                        <p>Horas Extra al 50%</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="well text-center">
                                        <h3>{{ number_format($resumen->total_horas_extra_100, 2) }}</h3>
                                        <p>Horas Extra al 100%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Detalle de Tardanzas -->
        @if($resumen->detalle_tardanzas_por_dia && count($resumen->detalle_tardanzas_por_dia) > 0)
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h3 class="panel-title">Detalle de Tardanzas</h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora Entrada</th>
                                        <th class="text-right">Minutos Tarde</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($resumen->detalle_tardanzas_por_dia as $tardanza)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($tardanza['fecha'])->format('d/m/Y') }}</td>
                                            <td>{{ $tardanza['hora_entrada'] }}</td>
                                            <td class="text-right">
                                                <span class="label label-warning">{{ $tardanza['minutos'] }} min</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Detalle de Faltas -->
                @if($resumen->detalle_faltas && count($resumen->detalle_faltas) > 0)
                    <div class="col-md-6">
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h3 class="panel-title">Detalle de Faltas</h3>
                            </div>
                            <div class="panel-body">
                                <table class="table table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Día</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($resumen->detalle_faltas as $falta)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($falta)->format('d/m/Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($falta)->locale('es')->isoFormat('dddd') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Observaciones -->
        @if($resumen->observaciones)
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Observaciones</h3>
                        </div>
                        <div class="panel-body">
                            {{ $resumen->observaciones }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Botones -->
        <div class="row">
            <div class="col-md-12 text-right">
                <a href="{{ route('admin.periodos.show', $periodo->id) }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Volver al Período
                </a>
            </div>
        </div>
    </div>
@stop
