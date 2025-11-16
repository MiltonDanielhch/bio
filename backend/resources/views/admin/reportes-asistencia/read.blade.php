@extends('voyager::master')

@section('page_title', 'Detalle del Reporte')

@section('content')
<div class="page-content container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="voyager-eye"></i> Detalle del Reporte #{{ $reporte->id }}
                    </h3>
                    <div class="panel-actions">
                        <a href="{{ route('admin.reportes-asistencia.index') }}" class="btn btn-primary">
                            <i class="voyager-angle-left"></i> Volver al listado
                        </a>
                    </div>
                </div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h4><strong>{{ $reporte->nombre_reporte }}</strong></h4>
                            <p><strong>Empresa:</strong> {{ optional($reporte->empresa)->nombre_empresa }}</p>
                            <p><strong>Periodo:</strong> {{ \Carbon\Carbon::parse($reporte->fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($reporte->fecha_fin)->format('d/m/Y') }}</p>
                            <p><strong>Generado por:</strong> {{ optional($reporte->generador)->name }}</p>
                            <p><strong>Fecha de generación:</strong> {{ $reporte->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div class="col-md-4 text-right">
                            @php
                                $labelClass = 'default';
                                if ($reporte->estado == 'completado') $labelClass = 'success';
                                if ($reporte->estado == 'procesando') $labelClass = 'warning';
                                if ($reporte->estado == 'error') $labelClass = 'danger';
                            @endphp
                            <h4>Estado: <span class="label label-{{ $labelClass }}" style="font-size: 1.2em;">{{ ucfirst($reporte->estado) }}</span></h4>

                            @if($reporte->estado == 'completado')
                                <a href="{{ route('admin.reportes-asistencia.download', $reporte) }}" class="btn btn-lg btn-success" style="margin-top: 20px;">
                                    <i class="voyager-download"></i> Descargar Archivo
                                </a>
                            @elseif($reporte->estado == 'procesando')
                                <div class="alert alert-warning" style="margin-top: 20px;">
                                    <i class="voyager-watch"></i> El reporte se está procesando. Vuelve a intentarlo en unos minutos.
                                </div>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Filtros Aplicados</h4>
                            @if($reporte->filtros)
                                <pre>{{ json_encode($reporte->filtros, JSON_PRETTY_PRINT) }}</pre>
                            @else
                                <p>No se aplicaron filtros adicionales.</p>
                            @endif
                        </div>
                    </div>

                    @if($reporte->estado == 'error')
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Detalle del Error</h4>
                            <div class="alert alert-danger">
                                <p>{{ $reporte->error }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>

                <div class="panel-footer">
                    @can('delete_reportes_asistencia')
                    <form action="{{ route('admin.reportes-asistencia.destroy', $reporte) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este reporte?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="voyager-trash"></i> Eliminar Reporte
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@stop

