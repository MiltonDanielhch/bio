@extends('voyager::master')

@section('page_title', 'Ver Departamento')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="voyager-eye"></i> Ver Departamento: {{ $departamento->nombre_departamento }}
            </h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">ID</th>
                                <td>{{ $departamento->id }}</td>
                            </tr>
                            <tr>
                                <th>Sucursal</th>
                                <td>
                                    {{ optional($departamento->sucursal)->nombre_sucursal }}
                                    <small class="text-muted">
                                        ({{ optional($departamento->sucursal->empresa)->nombre_empresa }})
                                    </small>
                                </td>
                            </tr>
                            <tr>
                                <th>Nombre Departamento</th>
                                <td>{{ $departamento->nombre_departamento }}</td>
                            </tr>
                            <tr>
                                <th>Descripción</th>
                                <td>{{ $departamento->descripcion ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Jefe de Departamento</th>
                                <td>
                                    @if($departamento->jefe)
                                        {{ $departamento->jefe->nombres }} {{ $departamento->jefe->apellidos }}
                                        <small class="text-muted">
                                            ({{ $departamento->jefe->codigo_empleado }})
                                        </small>
                                    @else
                                        <span class="text-muted">Sin jefe asignado</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    <span class="label label-{{ $departamento->estado == 'activo' ? 'success' : 'default' }}">
                                        {{ ucfirst($departamento->estado) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Creado</th>
                                <td>{{ optional($departamento->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Actualizado</th>
                                <td>{{ optional($departamento->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel-footer text-right">
            <a href="{{ route('admin.departamentos.index') }}" class="btn btn-default">
                <i class="voyager-angle-left"></i> Volver
            </a>

            @can('update', $departamento)
                <a href="{{ route('admin.departamentos.edit', $departamento) }}" class="btn btn-primary">
                    <i class="voyager-edit"></i> Editar
                </a>
            @endcan
        </div>
    </div>
</div>
@stop   
