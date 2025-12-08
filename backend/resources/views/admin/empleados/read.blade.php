@extends('voyager::master')

@section('page_title', 'Ver Empleado')

@section('content')
<div class="page-content container-fluid">
    <div class="panel panel-bordered panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="voyager-eye"></i> Ver Empleado: {{ $empleado->full_name }}
            </h3>
        </div>

        <div class="panel-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <img src="{{ $empleado->foto_perfil ? Storage::url($empleado->foto_perfil) : asset('img/default-avatar.png') }}"
                         style="width: 150px; height: 150px; border-radius: 50%; border: 3px solid #ddd; margin-bottom: 20px;"
                         alt="Foto de Perfil">
                    <h4>{{ $empleado->full_name }}</h4>
                    <p>{{ $empleado->email ?? 'Sin email' }}</p>
                    <span class="label label-{{ $empleado->estado == 'activo' ? 'success' : 'danger' }}">
                        {{ ucfirst($empleado->estado) }}
                    </span>
                </div>
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th style="width: 200px;">ID</th>
                                <td>{{ $empleado->id }}</td>
                            </tr>
                            <tr>
                                <th>DNI</th>
                                <td>{{ $empleado->dni }}</td>
                            </tr>
                            <tr>
                                <th>Código de Empleado</th>
                                <td>{{ $empleado->codigo_empleado }}</td>
                            </tr>
                             <tr>
                                <th>Teléfono</th>
                                <td>{{ $empleado->telefono ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Dirección</th>
                                <td>{{ $empleado->direccion ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Fecha de Nacimiento</th>
                                <td>{{ $empleado->fecha_nacimiento ? $empleado->fecha_nacimiento->format('d/m/Y') : '-' }}</td>
                            </tr>
                            <tr>
                                <th>Género</th>
                                <td>
                                    @if($empleado->genero == 'M')
                                        Masculino
                                    @elseif($empleado->genero == 'F')
                                        Femenino
                                    @else
                                        {{ $empleado->genero ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <h5 style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 20px;">Información Laboral</h5>
                    <table class="table table-bordered">
                         <tbody>
                            <tr>
                                <th style="width: 200px;">Empresa</th>
                                <td>{{ optional($empleado->empresa)->nombre_empresa ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Departamento</th>
                                <td>{{ optional($empleado->departamento)->nombre_departamento ?? 'Sin departamento' }}</td>
                            </tr>
                            <tr>
                                <th>Fecha de Contratación</th>
                                <td>{{ $empleado->fecha_contratacion->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tipo de Contrato</th>
                                <td>{{ ucfirst(str_replace('_', ' ', $empleado->tipo_contrato)) }}</td>
                            </tr>
                        </tbody>
                    </table>

                     <h5 style="border-bottom: 1px solid #eee; padding-bottom: 5px; margin-top: 20px;">Metadatos</h5>
                     <table class="table table-bordered">
                         <tbody>
                            <tr>
                                <th style="width: 200px;">Creado por</th>
                                <td>{{ optional($empleado->creador)->name ?? 'Sistema' }}</td>
                            </tr>
                            <tr>
                                <th>Creado</th>
                                <td>{{ optional($empleado->created_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Última Actualización</th>
                                <td>{{ optional($empleado->updated_at)->format('d/m/Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="panel-footer text-right">
            <a href="{{ route('admin.empleados.index') }}" class="btn btn-default">
                <i class="voyager-angle-left"></i> Volver
            </a>

            @can('update', $empleado)
                <a href="{{ route('admin.empleados.edit', $empleado) }}" class="btn btn-primary">
                    <i class="voyager-edit"></i> Editar
                </a>
            @endcan
        </div>
    </div>
</div>
@stop

