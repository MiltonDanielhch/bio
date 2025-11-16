@extends('voyager::master')

@section('page_title', ($incidencia->exists ?? false) ? 'Editar Incidencia' : 'Registrar Incidencia')

@section('content')
<div class="page-content container-fluid">
    <form action="{{ ($incidencia->exists ?? false)
            ? route('admin.incidencias.update', $incidencia)
            : route('admin.incidencias.store') }}"
          method="POST"
          id="form">
        @csrf
        @if($incidencia->exists ?? false) @method('PUT') @endif

        <div class="panel panel-bordered panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="voyager-warning"></i>
                    {{ ($incidencia->exists ?? false) ? 'Editar' : 'Registrar' }} Incidencia
                </h3>
            </div>

            <div class="panel-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Por favor corrige los siguientes errores:</strong>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="empleado_id">Empleado <span class="required">*</span></label>
                            <select name="empleado_id" id="empleado_id" class="form-control select2" required>
                                <option value="">-- Selecciona un empleado --</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{ $empleado->id }}" {{ old('empleado_id', optional($incidencia)->empleado_id) == $empleado->id ? 'selected' : '' }}>
                                        {{ $empleado->apellidos }}, {{ $empleado->nombres }} ({{ $empleado->codigo_empleado }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo_incidencia_id">Tipo de Incidencia <span class="required">*</span></label>
                            <select name="tipo_incidencia_id" id="tipo_incidencia_id" class="form-control select2" required>
                                <option value="">-- Selecciona un tipo --</option>
                                @foreach($tipos as $tipo)
                                    <option value="{{ $tipo->id }}" {{ old('tipo_incidencia_id', optional($incidencia)->tipo_incidencia_id) == $tipo->id ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="fecha_incidencia">Fecha <span class="required">*</span></label>
                            <input type="date" name="fecha_incidencia" id="fecha_incidencia" class="form-control"
                                   value="{{ old('fecha_incidencia', optional($incidencia)->fecha_incidencia ? \Carbon\Carbon::parse(optional($incidencia)->fecha_incidencia)->format('Y-m-d') : '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="hora_incidencia">Hora (Opcional)</label>
                            <input type="time" name="hora_incidencia" id="hora_incidencia" class="form-control"
                                   value="{{ old('hora_incidencia', optional($incidencia)->hora_incidencia) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="estado">Estado <span class="required">*</span></label>
                            <select name="estado" id="estado" class="form-control select2" required>
                                <option value="pendiente" {{ old('estado', optional($incidencia)->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="aprobado" {{ old('estado', optional($incidencia)->estado) == 'aprobado' ? 'selected' : '' }}>Aprobado</option>
                                <option value="rechazado" {{ old('estado', optional($incidencia)->estado) == 'rechazado' ? 'selected' : '' }}>Rechazado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="motivo">Motivo / Justificación <span class="required">*</span></label>
                            <textarea name="motivo" id="motivo" class="form-control" rows="3" required>{{ old('motivo', optional($incidencia)->motivo) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="observaciones">Observaciones (Uso interno)</label>
                            <textarea name="observaciones" id="observaciones" class="form-control" rows="2">{{ old('observaciones', optional($incidencia)->observaciones) }}</textarea>
                        </div>
                    </div>
                </div>

                @if($incidencia->exists && $incidencia->aprobado_por)
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            Incidencia {{ $incidencia->estado == 'aprobado' ? 'aprobada' : 'rechazada' }} por:
                            <strong>{{ optional($incidencia->aprobador)->name }}</strong> el
                            <strong>{{ \Carbon\Carbon::parse($incidencia->aprobado_en)->format('d/m/Y H:i') }}</strong>.
                        </div>
                    </div>
                </div>
                @endif

            </div>

            <div class="panel-footer text-right">
                <a href="{{ route('admin.incidencias.index') }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="voyager-check"></i> {{ ($incidencia->exists ?? false) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </div>
    </form>
</div>
@stop

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
@stop

@section('javascript')
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>
@stop

