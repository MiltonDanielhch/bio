@extends('voyager::master')

@section('page_header')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom: 5px;">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Hola, {{ Auth::user()->name }}</h2>
                                <p class="text-muted">Resumen de rendimiento - {{ now()->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    @php
        $meses = array('', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
    @endphp

    <div class="page-content container-fluid">
        @include('voyager::alerts')
        @include('voyager::dimmers')

        <!-- KPI Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-bordered dashboard-kpi">
                    <div class="panel-body text-center">
                        <div class="kpi-icon">
                            <i class="voyager-group"></i>
                        </div>
                        <h3 class="kpi-value">{{ $stats['total_empleados'] }}</h3>
                        <p class="kpi-label">Empleados Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-bordered dashboard-kpi">
                    <div class="panel-body text-center">
                        <div class="kpi-icon">
                            <i class="voyager-watch"></i>
                        </div>
                        <h3 class="kpi-value">{{ $stats['total_dispositivos'] }}</h3>
                        <p class="kpi-label">Dispositivos Totales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-bordered dashboard-kpi">
                    <div class="panel-body text-center">
                        <div class="kpi-icon">
                            <i class="voyager-logbook" style="color: #28a745;"></i>
                        </div>
                        <h3 class="kpi-value">{{ $stats['asistencias_hoy'] }}</h3>
                        <p class="kpi-label">Asistencias del Día</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-bordered dashboard-kpi">
                    <div class="panel-body text-center">
                        <div class="kpi-icon">
                            <i class="voyager-check" style="color: #17a2b8;"></i>
                        </div>
                        <h3 class="kpi-value">{{ $stats['empleados_presentes_hoy'] }}</h3>
                        <p class="kpi-label">Empleados Presentes Hoy</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Estado de Dispositivos -->
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-wifi"></i> Estado de Dispositivos</h3>
                    </div>
                    <div class="panel-body">
                        <div style="padding: 20px;">
                            <p><strong>Online:</strong> <span class="badge" style="background-color: #28a745; font-size: 14px;">{{ $stats['dispositivos_online'] }}</span></p>
                            <p><strong>Offline:</strong> <span class="badge" style="background-color: #dc3545; font-size: 14px;">{{ $stats['dispositivos_offline'] }}</span></p>
                            <small class="text-muted">*Esta es una simulación. Se requiere un job en segundo plano para datos en tiempo real.</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de últimos pedidos -->
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="voyager-list"></i> Últimas 5 Asistencias</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th># Pedido</th>
                                        <th>Dispositivo</th>
                                        <th>Fecha y Hora</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($ultimas_asistencias as $asistencia)
                                        <tr>
                                            <td>{{ $asistencia->empleado->nombre_completo ?? 'N/A' }}</td>
                                            <td>{{ $asistencia->dispositivo->nombre_dispositivo ?? 'N/A' }}</td>
                                            <td>{{ $asistencia->fecha_hora->format('d/m/Y H:i:s') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No hay registros de asistencia hoy.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <style>
        .dashboard-kpi {
            transition: all 0.2s ease;
        }
        .dashboard-kpi:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }
        .kpi-icon {
            font-size: 24px;
            color: #22A7F0;
            margin-bottom: 10px;
        }
        .kpi-value {
            font-size: 26px;
            font-weight: bold;
            margin: 10px 0;
        }
        .kpi-label {
            color: #6c757d;
            margin-bottom: 5px;
        }
    </style>
@stop

@section('javascript')
    <script>
        $(document).ready(function(){
            // No se necesita JavaScript adicional por ahora,
            // ya que hemos eliminado los gráficos y los filtros de fecha.
        });
    </script>
@stop
