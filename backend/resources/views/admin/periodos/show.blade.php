@extends('voyager::master')

@section('page_title', 'Detalle del Período')

@section('page_header')
    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible auto-dismiss">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible auto-dismiss">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('error') }}
            </div>
        @endif

        <h1 class="page-title">
            <i class="voyager-calendar"></i> {{ $periodo->nombre }}
        </h1>
    </div>
@stop

@section('content')
    <div class="page-content container-fluid">
        <!-- Información General -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Información General
                            <div class="pull-right">
                                @if($periodo->estado === 'abierto')
                                    <span class="label label-primary">Abierto</span>
                                @elseif($periodo->estado === 'procesando')
                                    <span class="label label-warning">
                                        <i class="voyager-refresh"></i> Procesando...
                                    </span>
                                @elseif($periodo->estado === 'cerrado')
                                    <span class="label label-success">Cerrado</span>
                                @else
                                    <span class="label label-danger">Error</span>
                                @endif
                            </div>
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Código:</strong><br>
                                <span class="text-muted">{{ $periodo->codigo }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Empresa:</strong><br>
                                <span class="text-muted">{{ $periodo->empresa->nombre_empresa ?? '-' }}</span>
                            </div>
                            <div class="col-md-3">
                                <strong>Período:</strong><br>
                                <span class="text-muted">
                                    {{ $periodo->fecha_inicio->format('d/m/Y') }} - {{ $periodo->fecha_fin->format('d/m/Y') }}
                                    ({{ $periodo->duracion_dias }} días)
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>Cerrado por:</strong><br>
                                <span class="text-muted">
                                    @if($periodo->cerradoPor)
                                        {{ $periodo->cerradoPor->name }}<br>
                                        <small>{{ $periodo->fecha_cierre?->format('d/m/Y H:i') }}</small>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                        
                        @if($periodo->descripcion)
                            <hr>
                            <strong>Descripción:</strong><br>
                            <p class="text-muted">{{ $periodo->descripcion }}</p>
                        @endif

                        @if($periodo->observaciones)
                            <hr>
                            <div class="alert alert-warning">
                                <strong>Observaciones:</strong><br>
                                {{ $periodo->observaciones }}
                            </div>
                        @endif
                    </div>
                    <div class="panel-footer text-right">
                        <a href="{{ route('admin.periodos.index') }}" class="btn btn-default">
                            <i class="voyager-list"></i> Volver al Listado
                        </a>

                        @if($periodo->puedeCerrarse())
                            <form action="{{ route('admin.periodos.cerrar', $periodo->id) }}" 
                                  method="POST" style="display: inline-block;"
                                  onsubmit="return confirm('¿Confirma el cierre de este período?\n\nEsto procesará la asistencia de todos los empleados en el rango de fechas especificado.')">
                                @csrf
                                <button type="submit" class="btn btn-warning">
                                    <i class="voyager-lock"></i> Cerrar Período
                                </button>
                            </form>
                        @endif

                        @if($periodo->estado === 'cerrado')
                            <a href="{{ route('admin.periodos.exportar', $periodo->id) }}" class="btn btn-success">
                                <i class="voyager-download"></i> Exportar a Excel
                            </a>

                            <form action="{{ route('admin.periodos.reabrir', $periodo->id) }}" 
                                  method="POST" style="display: inline-block;"
                                  onsubmit="return confirm('¿Confirma reabrir este período?\n\nSe eliminarán todos los resúmenes generados.')">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="voyager-unlock"></i> Reabrir
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($periodo->estado === 'cerrado')
            <!-- Estadísticas Generales -->
            <div class="row">
                <div class="col-md-2">
                    <div class="panel panel-bordered">
                        <div class="panel-body text-center">
                            <h2 class="text-primary">{{ $stats['total_empleados'] }}</h2>
                            <p class="text-muted">Empleados</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-bordered">
                        <div class="panel-body text-center">
                            <h2 class="text-success">{{ $stats['empleados_perfectos'] }}</h2>
                            <p class="text-muted">Sin Problemas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-bordered">
                        <div class="panel-body text-center">
                            <h2 class="text-danger">{{ $stats['total_dias_falta'] }}</h2>
                            <p class="text-muted">Total Faltas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-bordered">
                        <div class="panel-body text-center">
                            <h2 class="text-warning">{{ $stats['total_tardanzas'] }}</h2>
                            <p class="text-muted">Total Tardanzas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-bordered">
                        <div class="panel-body text-center">
                            <h2 class="text-info">{{ number_format($stats['total_horas_trabajadas'], 2) }}</h2>
                            <p class="text-muted">Horas Trabajadas</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="panel panel-bordered">
                        <div class="panel-body text-center">
                            <h2 class="text-primary">{{ number_format($stats['total_horas_extra'], 2) }}</h2>
                            <p class="text-muted">Horas Extra</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen por Empleado -->
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h3 class="panel-title">Resumen por Empleado</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Empleado</th>
                                            <th>Departamento</th>
                                            <th class="text-center">Días Lab.</th>
                                            <th class="text-center">Días Trab.</th>
                                            <th class="text-center">Faltas</th>
                                            <th class="text-center">Tardanzas</th>
                                            <th class="text-center">Min. Atraso</th>
                                            <th class="text-center">Hrs. Trab.</th>
                                            <th class="text-center">Hrs. Extra</th>
                                            <th class="text-center">% Asist.</th>
                                            <th class="text-right">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($periodo->resumenes as $resumen)
                                            <tr class="{{ $resumen->tieneProblemas() ? 'warning' : '' }}">
                                                <td>{{ $resumen->empleado->codigo_empleado ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('admin.periodos.empleado', [$periodo->id, $resumen->empleado_id]) }}">
                                                        {{ $resumen->empleado->nombre_completo ?? 'N/A' }}
                                                    </a>
                                                </td>
                                                <td>{{ $resumen->empleado->departamento->nombre_departamento ?? '-' }}</td>
                                                <td class="text-center">{{ $resumen->total_dias_laborables }}</td>
                                                <td class="text-center">{{ $resumen->total_dias_trabajados }}</td>
                                                <td class="text-center">
                                                    @if($resumen->total_dias_falta > 0)
                                                        <span class="label label-danger">{{ $resumen->total_dias_falta }}</span>
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if($resumen->total_tardanzas > 0)
                                                        <span class="label label-warning">{{ $resumen->total_tardanzas }}</span>
                                                    @else
                                                        0
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $resumen->total_minutos_tardanza }}</td>
                                                <td class="text-center">{{ number_format($resumen->total_horas_trabajadas, 2) }}</td>
                                                <td class="text-center">
                                                    {{ number_format($resumen->total_horas_extra, 2) }}
                                                    @if($resumen->total_horas_extra > 0)
                                                        <br>
                                                        <small class="text-muted">
                                                            25%: {{ $resumen->total_horas_extra_25 }} |
                                                            50%: {{ $resumen->total_horas_extra_50 }} |
                                                            100%: {{ $resumen->total_horas_extra_100 }}
                                                        </small>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <strong class="{{ $resumen->porcentaje_asistencia >= 95 ? 'text-success' : ($resumen->porcentaje_asistencia >= 80 ? 'text-warning' : 'text-danger') }}">
                                                        {{ number_format($resumen->porcentaje_asistencia, 1) }}%
                                                    </strong>
                                                </td>
                                                <td class="text-right">
                                                    @if($resumen->esPerfecto())
                                                        <span class="label label-success">Perfecto</span>
                                                    @elseif($resumen->tieneProblemas())
                                                        <span class="label label-warning">Con Observaciones</span>
                                                    @else
                                                        <span class="label label-info">Normal</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif($periodo->estado === 'procesando')
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info text-center">
                        <i class="voyager-refresh loading-icon"></i>
                        <h3>Procesando planilla...</h3>
                        <p>El sistema está calculando la asistencia para {{ $periodo->total_empleados_procesados }} empleados.</p>
                        <p><small>Esta página se actualizará automáticamente cuando termine el proceso.</small></p>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-warning text-center">
                        <h3>Este período aún no ha sido cerrado</h3>
                        <p>Cierre el período para generar el resumen consolidado de asistencia.</p>
                        <form action="{{ route('admin.periodos.cerrar', $periodo->id) }}" 
                              method="POST" style="display: inline-block;"
                              onsubmit="return confirm('¿Confirma el cierre de este período?')">
                            @csrf
                            <button type="submit" class="btn btn-lg btn-warning">
                                <i class="voyager-lock"></i> Cerrar Período Ahora
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
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
@endsection

@section('javascript')
<script>
    // Auto-refrescar si está procesando
    @if($periodo->estado === 'procesando')
        setTimeout(() => {
            location.reload();
        }, 5000); // Refrescar cada 5 segundos
    @endif
</script>
@endsection
