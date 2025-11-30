@extends('voyager::master')

@section('page_title', ($dispositivo->exists ?? false) ? 'Editar Dispositivo' : 'Agregar Dispositivo')

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <form action="{{ ($dispositivo->exists ?? false)
                ? route('admin.dispositivos.update', $dispositivo->id)
                : route('admin.dispositivos.store') }}"
              method="POST">
            @csrf
            @if($dispositivo->exists ?? false) @method('PUT') @endif

            <div class="panel panel-bordered panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="voyager-harddrive"></i>
                        {{ ($dispositivo->exists ?? false) ? 'Editar' : 'Agregar' }} Dispositivo
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
                            <label for="sucursal_id">Sucursal <span class="required">*</span></label>
                            <select name="sucursal_id" id="sucursal_id" class="form-control select2" required>
                                <option value="">-- Seleccione una sucursal --</option>
                                @foreach($sucursales as $sucursal)
                                    <option value="{{ $sucursal->id }}" @if(old('sucursal_id', optional($dispositivo)->sucursal_id) == $sucursal->id) selected @endif>
                                        {{ $sucursal->nombre_sucursal }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="nombre_dispositivo">Nombre del Dispositivo <span class="required">*</span></label>
                            <input type="text" name="nombre_dispositivo" id="nombre_dispositivo" class="form-control" value="{{ old('nombre_dispositivo', optional($dispositivo)->nombre_dispositivo) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="tipo">Tipo <span class="required">*</span></label>
                            <select name="tipo" id="tipo" class="form-control" required>
                                <option value="huella" @if(old('tipo', optional($dispositivo)->tipo) == 'huella') selected @endif>Huella</option>
                                <option value="facial" @if(old('tipo', optional($dispositivo)->tipo) == 'facial') selected @endif>Facial</option>
                                <option value="huella_facial" @if(old('tipo', optional($dispositivo)->tipo) == 'huella_facial') selected @endif>Huella y Facial</option>
                                <option value="tarjeta" @if(old('tipo', optional($dispositivo)->tipo) == 'tarjeta') selected @endif>Tarjeta</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="numero_serie">Número de Serie <span class="required">*</span></label>
                            <input type="text" name="numero_serie" id="numero_serie" class="form-control" value="{{ old('numero_serie', optional($dispositivo)->numero_serie) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="direccion_ip">Dirección IP <span class="required">*</span></label>
                            <input type="text" name="direccion_ip" id="direccion_ip" class="form-control" value="{{ old('direccion_ip', optional($dispositivo)->direccion_ip) }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="puerto">Puerto <span class="required">*</span></label>
                            <input type="number" name="puerto" id="puerto" class="form-control" value="{{ old('puerto', optional($dispositivo)->puerto ?? 4370) }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="password">Contraseña (si aplica)</label>
                            <input type="number" name="password" id="password" class="form-control" value="{{ old('password', optional($dispositivo)->password) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="ubicacion">Ubicación</label>
                            <input type="text" name="ubicacion" id="ubicacion" class="form-control" value="{{ old('ubicacion', optional($dispositivo)->ubicacion) }}">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="estado">Estado <span class="required">*</span></label>
                            <select name="estado" id="estado" class="form-control" required>
                                <option value="activo" @if(old('estado', optional($dispositivo)->estado) == 'activo') selected @endif>Activo</option>
                                <option value="inactivo" @if(old('estado', optional($dispositivo)->estado) == 'inactivo') selected @endif>Inactivo</option>
                                <option value="mantenimiento" @if(old('estado', optional($dispositivo)->estado) == 'mantenimiento') selected @endif>Mantenimiento</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="panel-footer text-right">
                    <a href="{{ route('admin.dispositivos.index') }}" class="btn btn-default">
                        <i class="voyager-angle-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="voyager-check"></i> {{ ($dispositivo->exists ?? false) ? 'Actualizar' : 'Guardar' }}
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

            // Inicializar select2 para el selector de sucursales
            $('.select2').select2();
        });
    </script>
@stop
