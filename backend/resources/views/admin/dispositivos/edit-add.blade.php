@extends('voyager::master')

@section('page_title', ($dispositivo->id ? 'Editar' : 'Crear') . ' Dispositivo')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-harddrive"></i>
            {{ $dispositivo->id ? 'Editar Dispositivo' : 'Crear Nuevo Dispositivo' }}
        </h1>
        <a href="{{ route('admin.dispositivos.index') }}" class="btn btn-warning btn-add-new">
            <i class="voyager-list"></i> <span>Volver a la lista</span>
        </a>
    </div>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        @include('voyager::alerts')
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <form method="POST" action="{{ $dispositivo->id ? route('admin.dispositivos.update', $dispositivo->id) : route('admin.dispositivos.store') }}">
                            @csrf
                            @if($dispositivo->id)
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="sucursal_id">Sucursal</label>
                                <select name="sucursal_id" id="sucursal_id" class="form-control select2">
                                    <option value="">-- Seleccione una sucursal --</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}" @if(old('sucursal_id', $dispositivo->sucursal_id) == $sucursal->id) selected @endif>
                                            {{ $sucursal->nombre_sucursal }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('sucursal_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="nombre_dispositivo">Nombre del Dispositivo</label>
                                <input type="text" name="nombre_dispositivo" id="nombre_dispositivo" class="form-control" value="{{ old('nombre_dispositivo', $dispositivo->nombre_dispositivo) }}" required>
                                @error('nombre_dispositivo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tipo">Tipo</label>
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="huella" @if(old('tipo', $dispositivo->tipo) == 'huella') selected @endif>Huella</option>
                                    <option value="facial" @if(old('tipo', $dispositivo->tipo) == 'facial') selected @endif>Facial</option>
                                    <option value="huella_facial" @if(old('tipo', $dispositivo->tipo) == 'huella_facial') selected @endif>Huella y Facial</option>
                                    <option value="tarjeta" @if(old('tipo', $dispositivo->tipo) == 'tarjeta') selected @endif>Tarjeta</option>
                                </select>
                                @error('tipo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="numero_serie">Número de Serie</label>
                                <input type="text" name="numero_serie" id="numero_serie" class="form-control" value="{{ old('numero_serie', $dispositivo->numero_serie) }}" required>
                                @error('numero_serie')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="direccion_ip">Dirección IP</label>
                                <input type="text" name="direccion_ip" id="direccion_ip" class="form-control" value="{{ old('direccion_ip', $dispositivo->direccion_ip) }}">
                                @error('direccion_ip')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="puerto">Puerto</label>
                                <input type="number" name="puerto" id="puerto" class="form-control" value="{{ old('puerto', $dispositivo->puerto) }}" required>
                                @error('puerto')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">Contraseña (si aplica)</label>
                                <input type="number" name="password" id="password" class="form-control" value="{{ old('password', $dispositivo->password) }}">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="ubicacion">Ubicación</label>
                                <input type="text" name="ubicacion" id="ubicacion" class="form-control" value="{{ old('ubicacion', $dispositivo->ubicacion) }}">
                                @error('ubicacion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="estado">Estado</label>
                                <select name="estado" id="estado" class="form-control">
                                    <option value="activo" @if(old('estado', $dispositivo->estado) == 'activo') selected @endif>Activo</option>
                                    <option value="inactivo" @if(old('estado', $dispositivo->estado) == 'inactivo') selected @endif>Inactivo</option>
                                    <option value="mantenimiento" @if(old('estado', $dispositivo->estado) == 'mantenimiento') selected @endif>Mantenimiento</option>
                                </select>
                                @error('estado')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ $dispositivo->id ? 'Actualizar Dispositivo' : 'Guardar Dispositivo' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {
            // Inicializar select2 si lo usas para el selector de sucursales
            $('.select2').select2();
        });
    </script>
@stop
