@extends('voyager::master')

@section('page_title', 'Ver Horario')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="voyager-clock"></i> Ver Horario: {{ $horario->nombre_horario }}
            </h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">ID</th>
                                <td>{{ $horario->id }}</td>
                            </tr>
                            <tr>
                                <th>Empresa</th>
                                <td>{{ optional($horario->empresa)->nombre_empresa }} ({{ optional($horario->empresa)->ruc }})</td>
                            </tr>
                            <tr>
                                <th>Nombre Horario</th>
                                <td>{{ $horario->nombre_horario }}</td>
                            </tr>
                            <tr>
                                <th>Hora Entrada</th>
                                <td>{{ \Carbon\Carbon::parse($horario->hora_entrada)->format('H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Hora Salida</th>
                                <td>{{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Almuerzo</th>
                                <td>
                                    @if($horario->hora_entrada_almuerzo && $horario->hora_salida_almuerzo)
                                        {{ \Carbon\Carbon::parse($horario->hora_entrada_almuerzo)->format('H:i') }}
                                        -
                                        {{ \Carbon\Carbon::parse($horario->hora_salida_almuerzo)->format('H:i') }}
                                    @else
                                        <span class="text-muted">Sin almuerzo definido</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Tolerancia</th>
                                <td>
                                    Entrada: {{ $horario->tolerancia_entrada ?? 0 }} min<br>
                                    Salida: {{ $horario->tolerancia_salida ?? 0 }} min
                                </td>
                            </tr>
                            <tr>
                                <th>Días Laborales</th>
                                <td>
                                    @if(!empty($horario->dias_laborales))
                                        @foreach($horario->dias_laborales as $dia)
                                            <span class="label label-info">{{ ucfirst($dia) }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Sin días asignados</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Jornada Flexible</th>
                                <td>
                                    <span class="label label-{{ $horario->flexible ? 'success' : 'default' }}">
                                        {{ $horario->flexible ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Jornada Nocturna</th>
                                <td>
                                    <span class="label label-{{ $horario->nocturno ? 'warning' : 'default' }}">
                                        {{ $horario->nocturno ? 'Sí' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Creado</th>
                                <td>{{ optional($horario->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Actualizado</th>
                                <td>{{ optional($horario->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel-footer text-right">
            <a href="{{ route('admin.horarios.index') }}" class="btn btn-default">
                <i class="voyager-angle-left"></i> Volver
            </a>

            @can('update', $horario)
                <a href="{{ route('admin.horarios.edit', $horario) }}" class="btn btn-primary">
                    <i class="voyager-edit"></i> Editar
                </a>
            @endcan
        </div>
    </div>
</div>
@stop
