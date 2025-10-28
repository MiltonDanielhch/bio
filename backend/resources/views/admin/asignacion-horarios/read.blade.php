@extends('voyager::master')

@section('page_title', 'Ver Asignación de Horario')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="voyager-eye"></i> Ver Asignación de Horario
            </h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">ID</th>
                                <td>{{ $asignacionHorario->id }}</td>
                            </tr>
                            <tr>
                                <th>Empleado</th>
                                <td>
                                    <strong>{{ $asignacionHorario->empleado->codigo_empleado }}</strong><br>
                                    {{ $asignacionHorario->empleado->nombres }} {{ $asignacionHorario->empleado->apellidos }}
                                </td>
                            </tr>
                            <tr>
                                <th>Horario</th>
                                <td>
                                    {{ $asignacionHorario->horario->nombre_horario }}<br>
                                    <small class="text-muted">{{ optional($asignacionHorario->horario->empresa)->nombre_empresa }}</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Rango de Fechas</th>
                                <td>
                                    <strong>Desde:</strong> {{ \Carbon\Carbon::parse($asignacionHorario->fecha_inicio)->format('d/m/Y') }}<br>
                                    @if($asignacionHorario->fecha_fin)
                                        <strong>Hasta:</strong> {{ \Carbon\Carbon::parse($asignacionHorario->fecha_fin)->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted"><strong>Hasta:</strong> Indefinido</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Días Laborales (Horario)</th>
                                <td>
                                    @if(!empty($asignacionHorario->horario->dias_laborales))
                                        @foreach($asignacionHorario->horario->dias_laborales as $dia)
                                            <span class="label label-info">{{ ucfirst($dia) }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Sin días asignados</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    <span class="label label-{{ $asignacionHorario->activo ? 'success' : 'default' }}">
                                        {{ $asignacionHorario->activo ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Creado</th>
                                <td>{{ optional($asignacionHorario->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Actualizado</th>
                                <td>{{ optional($asignacionHorario->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel-footer text-right">
            <a href="{{ route('admin.asignacion-horarios.index') }}" class="btn btn-default">
                <i class="voyager-angle-left"></i> Volver
            </a>

            @can('update', $asignacionHorario)
                <a href="{{ route('admin.asignacion-horarios.edit', $asignacionHorario) }}" class="btn btn-primary">
                    <i class="voyager-edit"></i> Editar
                </a>
            @endcan
        </div>
    </div>
</div>
@stop
