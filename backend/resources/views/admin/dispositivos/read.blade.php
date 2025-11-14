@extends('voyager::master')

@section('page_title', 'Ver Dispositivo')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-harddrive"></i> Ver Dispositivo
        </h1>
        <a href="{{ route('admin.dispositivos.index') }}" class="btn btn-warning btn-add-new">
            <i class="voyager-list"></i> <span>Volver a la lista</span>
        </a>
        {{-- @can('edit', $dispositivo) --}}
            <a href="{{ route('admin.dispositivos.edit', $dispositivo->id) }}" class="btn btn-info btn-add-new">
                <i class="voyager-edit"></i> <span>Editar</span>
            </a>
        {{-- @endcan --}}
        {{-- @can('delete', $dispositivo) --}}
            <button type="button" class="btn btn-danger btn-add-new" data-toggle="modal" data-target="#delete_modal">
                <i class="voyager-trash"></i> <span>Eliminar</span>
            </button>
        {{-- @endcan --}}
    </div>
@stop

@section('content')
    <div class="page-content read container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="padding-bottom:5px;">
                    <div class="panel-heading" style="border-bottom:0;">
                        <h3 class="panel-title">Detalles del Dispositivo</h3>
                    </div>
                    <div class="panel-body" style="padding-top:0;">
                        <table class="table table-striped table-bordered">
                            <tbody>
                                <tr>
                                    <td><strong>ID</strong></td>
                                    <td>{{ $dispositivo->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nombre del Dispositivo</strong></td>
                                    <td>{{ $dispositivo->nombre_dispositivo }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sucursal</strong></td>
                                    <td>{{ $dispositivo->sucursal->nombre_sucursal ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo</strong></td>
                                    <td>{{ ucfirst($dispositivo->tipo) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Número de Serie</strong></td>
                                    <td>{{ $dispositivo->numero_serie }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dirección IP</strong></td>
                                    <td>{{ $dispositivo->direccion_ip }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Puerto</strong></td>
                                    <td>{{ $dispositivo->puerto }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Contraseña</strong></td>
                                    <td>{{ $dispositivo->password ? '******' : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Ubicación</strong></td>
                                    <td>{{ $dispositivo->ubicacion ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Estado</strong></td>
                                    <td><span class="badge bg-{{ $dispositivo->estado == 'activo' ? 'success' : 'danger' }}">{{ ucfirst($dispositivo->estado) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Última Conexión</strong></td>
                                    <td>{{ $dispositivo->ultima_conexion ? \Carbon\Carbon::parse($dispositivo->ultima_conexion)->format('d/m/Y H:i:s') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Versión Firmware</strong></td>
                                    <td>{{ $dispositivo->version_firmware ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Creado por</strong></td>
                                    <td>{{ $dispositivo->creador->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Creado el</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($dispositivo->created_at)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Última Actualización</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($dispositivo->updated_at)->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="text-center">
                            <form action="{{ route('admin.dispositivos.test_connection', $dispositivo->id) }}" method="POST" style="display:inline-block; margin-right: 10px;">
                                @csrf
                                <button type="submit" class="btn btn-primary"><i class="voyager-wifi"></i> Probar Conexión</button>
                            </form>
                            <form action="{{ route('admin.dispositivos.sync_now', $dispositivo->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                <button type="submit" class="btn btn-success"><i class="voyager-refresh"></i> Sincronizar Ahora</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Incluir el modal de eliminación si lo tienes en un archivo compartido --}}
    {{-- @include('voyager::partials.delete-modal') --}}
@stop
