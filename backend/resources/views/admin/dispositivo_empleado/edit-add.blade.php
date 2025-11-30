@extends('voyager::master')

@section('page_title', ($map->exists ?? false) ? 'Editar Mapeo' : 'Agregar Mapeo')

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <form action="{{ ($map->exists ?? false)
                ? route('admin.dispositivo-empleado.update', $map->id)
                : route('admin.dispositivo-empleado.store') }}"
              method="POST">
            @csrf
            @if($map->exists ?? false) @method('PUT') @endif

            <div class="panel panel-bordered panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="voyager-data"></i>
                        {{ ($map->exists ?? false) ? 'Editar' : 'Agregar' }} Mapeo
                    </h3>
                </div>

                <div class="panel-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible auto-dismiss">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <strong>Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="empleado_id">Empleado <span class="required">*</span></label>
                            <select name="empleado_id" id="empleado_id" class="form-control select2" required>
                                <option value="">-- Seleccione un empleado --</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{ $empleado->id }}" @if(old('empleado_id', optional($map)->empleado_id) == $empleado->id) selected @endif>
                                        {{ $empleado->nombres }} {{ $empleado->apellidos }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="dispositivo_id">Dispositivo Biométrico <span class="required">*</span></label>
                            <select name="dispositivo_id" id="dispositivo_id" class="form-control select2" required>
                                <option value="">-- Seleccione un dispositivo --</option>
                                @foreach($dispositivos as $dispositivo)
                                    <option value="{{ $dispositivo->id }}" @if(old('dispositivo_id', optional($map)->dispositivo_id) == $dispositivo->id) selected @endif>
                                        {{ $dispositivo->nombre_dispositivo }} ({{ $dispositivo->direccion_ip }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="zk_user_id">ID de Usuario en el Reloj (zk_user_id) <span class="required">*</span></label>
                            <input type="number" name="zk_user_id" id="zk_user_id" class="form-control" value="{{ old('zk_user_id', optional($map)->zk_user_id) }}" required>
                            <small class="form-text text-muted">Este es el ID numérico que el empleado tiene asignado DENTRO del dispositivo biométrico.</small>
                        </div>
                    </div>
                </div>

                <div class="panel-footer text-right">
                    <a href="{{ route('admin.dispositivo-empleado.index') }}" class="btn btn-default">
                        <i class="voyager-angle-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="voyager-check"></i> {{ ($map->exists ?? false) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {
            // Script para auto-cerrar las alertas de sesión después de 5 segundos
            setTimeout(function() {
                $('.auto-dismiss').fadeOut('slow', function() { $(this).remove(); });
            }, 5000);

            // Inicializar select2
            $('.select2').select2();
        });
    </script>
@stop
