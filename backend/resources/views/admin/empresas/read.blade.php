@extends('voyager::master')

@section('page_title', 'Ver Empresa')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="voyager-eye"></i> Ver Empresa: {{ $empresa->nombre_empresa }}
            </h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">ID</th>
                                <td>{{ $empresa->id }}</td>
                            </tr>
                            <tr>
                                <th>Nombre</th>
                                <td>{{ $empresa->nombre_empresa }}</td>
                            </tr>
                            <tr>
                                <th>RUC</th>
                                <td>{{ $empresa->ruc }}</td>
                            </tr>
                            <tr>
                                <th>Dirección</th>
                                <td>{{ $empresa->direccion ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Teléfono</th>
                                <td>{{ $empresa->telefono ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $empresa->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Logo</th>
                                <td>
                                    @if($empresa->logo)
                                        <img src="{{ Storage::url($empresa->logo) }}" width="120" alt="Logo">
                                    @else
                                        <span class="text-muted">Sin logo</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Color Primario</th>
                                <td>
                                    <span class="label label-primary"
                                          style="background-color: {{ $empresa->color_primario }}">
                                        {{ $empresa->color_primario }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    <span class="label label-{{ $empresa->estado == 'activo' ? 'success' : 'default' }}">
                                        {{ ucfirst($empresa->estado) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Creado</th>
                                <td>{{ optional($empresa->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Actualizado</th>
                                <td>{{ optional($empresa->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel-footer text-right">
            <a href="{{ route('admin.empresas.index') }}" class="btn btn-default">
                <i class="voyager-angle-left"></i> Volver
            </a>

            @can('update', $empresa)
                <a href="{{ route('admin.empresas.edit', $empresa) }}" class="btn btn-primary">
                    <i class="voyager-edit"></i> Editar
                </a>
            @endcan
        </div>
    </div>
</div>
@stop
