@extends('voyager::master')

@section('page_title', ($asignacion->exists ?? false) ? 'Editar Asignación' : 'Agregar Asignación')

@section('content')
<div class="page-content container-fluid">
    @if(session('message'))
        <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible auto-dismiss">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <form action="{{ ($asignacion->exists ?? false)
            ? route('admin.asignacion-horarios.update', $asignacion)
            : route('admin.asignacion-horarios.store') }}"
          method="POST"
          id="asignacion-form">
        @csrf
        @if($asignacion->exists ?? false) @method('PUT') @endif

        <div class="panel panel-bordered panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="voyager-calendar"></i>
                    {{ ($asignacion->exists ?? false) ? 'Editar' : 'Agregar' }} Asignación de Horario
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="empleado_id">Empleado <span class="required">*</span></label>
                            <select name="empleado_id" id="empleado_id" class="form-control @error('empleado_id') is-invalid @enderror" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($empleados as $emp)
                                    <option value="{{ $emp->id }}"
                                        {{ old('empleado_id', optional($asignacion)->empleado_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->codigo_empleado }} - {{ $emp->nombres }} {{ $emp->apellidos }}
                                    </option>
                                @endforeach
                            </select>
                            @error('empleado_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="horario_id">Horario <span class="required">*</span></label>
                            <select name="horario_id" id="horario_id" class="form-control @error('horario_id') is-invalid @enderror" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($horarios as $h)
                                    <option value="{{ $h->id }}"
                                        {{ old('horario_id', optional($asignacion)->horario_id) == $h->id ? 'selected' : '' }}>
                                        {{ $h->nombre_horario }} ({{ optional($h->empresa)->nombre_empresa }})
                                    </option>
                                @endforeach
                            </select>
                            @error('horario_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha Inicio <span class="required">*</span></label>
                            <input type="date"
                                   name="fecha_inicio"
                                   id="fecha_inicio"
                                   class="form-control @error('fecha_inicio') is-invalid @enderror"
                                   value="{{ old('fecha_inicio', optional($asignacion)->fecha_inicio?->format('Y-m-d')) }}"
                                   required>
                            @error('fecha_inicio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha Fin (opcional)</label>
                            <input type="date"
                                   name="fecha_fin"
                                   id="fecha_fin"
                                   class="form-control @error('fecha_fin') is-invalid @enderror"
                                   value="{{ old('fecha_fin', optional($asignacion)->fecha_fin?->format('Y-m-d')) }}">
                            <small class="form-text text-muted">Dejar vacío para vigencia indefinida</small>
                            @error('fecha_fin')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="activo">Estado</label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           name="activo"
                                           id="activo"
                                           value="1"
                                           {{ old('activo', optional($asignacion)->activo ?? true) ? 'checked' : '' }}>
                                    Activo
                                </label>
                            </div>
                            <small class="form-text text-muted">Desactivar si el empleado cambia de horario o se suspende</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-right">
                <a href="{{ route('admin.asignacion-horarios.index') }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="voyager-check"></i> {{ ($asignacion->exists ?? false) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </div>
    </form>
</div>
@stop

@section('javascript')
<script>
    $(document).ready(function () {
        // Script para auto-cerrar las alertas de sesión después de 5 segundos
        setTimeout(function() {
            $('.auto-dismiss').fadeOut('slow', function() { $(this).remove(); });
        }, 5000);
    });
</script>
@stop
