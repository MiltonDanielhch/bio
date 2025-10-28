@extends('voyager::master')

@section('page_title', 'Ver Sucursal')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="voyager-eye"></i> Ver Sucursal: {{ $sucursal->nombre_sucursal }}
            </h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">ID</th>
                                <td>{{ $sucursal->id }}</td>
                            </tr>
                            <tr>
                                <th>Empresa</th>
                                <td>{{ optional($sucursal->empresa)->nombre_empresa ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nombre Sucursal</th>
                                <td>{{ $sucursal->nombre_sucursal }}</td>
                            </tr>
                            <tr>
                                <th>Dirección</th>
                                <td>{{ $sucursal->direccion ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Ciudad</th>
                                <td>{{ $sucursal->ciudad ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>País</th>
                                <td>{{ $sucursal->pais ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Zona Horaria</th>
                                <td>{{ $sucursal->zona_horaria }}</td>
                            </tr>
                            <tr>
                                <th>Latitud / Longitud</th>
                                <td>
                                    @if($sucursal->latitud && $sucursal->longitud)
                                        {{ $sucursal->latitud }}, {{ $sucursal->longitud }}
                                        <br>
                                        <iframe
                                            width="100%"
                                            height="200"
                                            frameborder="0"
                                            scrolling="no"
                                            marginheight="0"
                                            marginwidth="0"
                                            src="https://maps.google.com/maps?q={{ $sucursal->latitud }},{{ $sucursal->longitud }}&hl=es&z=14&amp;output=embed">
                                        </iframe>
                                    @else
                                        <span class="text-muted">Sin coordenadas</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    <span class="label label-{{ $sucursal->estado == 'activo' ? 'success' : 'default' }}">
                                        {{ ucfirst($sucursal->estado) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Creado</th>
                                <td>{{ optional($sucursal->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Actualizado</th>
                                <td>{{ optional($sucursal->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel-footer text-right">
            <a href="{{ route('admin.sucursales.index') }}" class="btn btn-default">
                <i class="voyager-angle-left"></i> Volver
            </a>

            @can('update', $sucursal)
                <a href="{{ route('admin.sucursales.edit', $sucursal) }}" class="btn btn-primary">
                    <i class="voyager-edit"></i> Editar
                </a>
            @endcan
        </div>
    </div>
</div>
@stop
