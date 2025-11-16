@extends('voyager::master')

@section('page_title', ($registro->id ? 'Editar' : 'Crear') . ' Registro de Asistencia')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-list"></i>
            {{ $registro->id ? 'Editar Registro de Asistencia' : 'Crear Nuevo Registro de Asistencia' }}
        </h1>
        <a href="{{ route('admin.registros-asistencia.index') }}" class="btn btn-warning btn-add-new">
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
                        <form method="POST" action="{{ $registro->id ? route('admin.registros-asistencia.update', $registro->id) : route('admin.registros-asistencia.store') }}">
                            @csrf
                            @if($registro->id)
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="empleado_id">Empleado</label>
                                <select name="empleado_id" id="empleado_id" class="form-control select2" required>
                                    <option value="">-- Seleccione un empleado --</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}" @if(old('empleado_id', $registro->empleado_id) == $empleado->id) selected @endif>
                                            {{ $empleado->full_name }} ({{ $empleado->codigo_empleado }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="dispositivo_id">Dispositivo</label>
                                <select name="dispositivo_id" id="dispositivo_id" class="form-control select2" required>
                                    <option value="">-- Seleccione un dispositivo --</option>
                                    @foreach($dispositivos as $dispositivo)
                                        <option value="{{ $dispositivo->id }}" @if(old('dispositivo_id', $registro->dispositivo_id) == $dispositivo->id) selected @endif>
                                            {{ $dispositivo->nombre_dispositivo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="fecha_local">Fecha del Marcaje</label>
                                    <input type="date" name="fecha_local" id="fecha_local" class="form-control" value="{{ old('fecha_local', optional($registro->fecha_hora)->format('Y-m-d')) }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="hora_local">Hora del Marcaje</label>
                                    <input type="time" name="hora_local" id="hora_local" class="form-control" step="1" value="{{ old('hora_local', optional($registro->fecha_hora)->format('H:i:s')) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="tipo_marcaje">Tipo de Marcaje</label>
                                    <select name="tipo_marcaje" id="tipo_marcaje" class="form-control select2" required>
                                        <option value="entrada" @if(old('tipo_marcaje', $registro->tipo_marcaje) == 'entrada') selected @endif>Entrada</option>
                                        <option value="salida" @if(old('tipo_marcaje', $registro->tipo_marcaje) == 'salida') selected @endif>Salida</option>
                                        <option value="entrada_almuerzo" @if(old('tipo_marcaje', $registro->tipo_marcaje) == 'entrada_almuerzo') selected @endif>Entrada Almuerzo</option>
                                        <option value="salida_almuerzo" @if(old('tipo_marcaje', $registro->tipo_marcaje) == 'salida_almuerzo') selected @endif>Salida Almuerzo</option>
                                        <option value="general" @if(old('tipo_marcaje', $registro->tipo_marcaje) == 'general') selected @endif>General</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="tipo_verificacion">Tipo de Verificación</label>
                                    <select name="tipo_verificacion" id="tipo_verificacion" class="form-control select2" required>
                                        <option value="manual" @if(old('tipo_verificacion', $registro->tipo_verificacion) == 'manual') selected @endif>Manual</option>
                                        <option value="huella" @if(old('tipo_verificacion', $registro->tipo_verificacion) == 'huella') selected @endif>Huella</option>
                                        <option value="rostro" @if(old('tipo_verificacion', $registro->tipo_verificacion) == 'rostro') selected @endif>Rostro</option>
                                        <option value="tarjeta" @if(old('tipo_verificacion', $registro->tipo_verificacion) == 'tarjeta') selected @endif>Tarjeta</option>
                                        <option value="clave" @if(old('tipo_verificacion', $registro->tipo_verificacion) == 'clave') selected @endif>Clave</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="observaciones">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" class="form-control" rows="3">{{ old('observaciones', $registro->observaciones) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ $registro->id ? 'Actualizar Registro' : 'Guardar Registro' }}
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
            $('.select2').select2();
        });
    </script>
@stop
