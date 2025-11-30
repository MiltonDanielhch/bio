@extends('voyager::master')

@section('page_title', (isset($departamento) && $departamento->exists) ? 'Editar Departamento' : 'Agregar Departamento')

@section('content')
<div class="page-content container-fluid">
    @if(session('message'))
        <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible auto-dismiss">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <form action="{{ (isset($departamento) && $departamento->exists) ? route('admin.departamentos.update', $departamento) : route('admin.departamentos.store') }}"
          method="POST"
          id="departamento-form">
        @csrf
        @if(isset($departamento) && $departamento->exists) @method('PUT') @endif

        <div class="panel panel-bordered panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="voyager-categories"></i>
                    {{ (isset($departamento) && $departamento->exists) ? 'Editar' : 'Agregar' }} Departamento
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
                            <label for="sucursal_id">Sucursal <span class="required">*</span></label>
                            <select name="sucursal_id" id="sucursal_id" class="form-control @error('sucursal_id') is-invalid @enderror" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($sucursales as $s)
                                    <option value="{{ $s->id }}"
                                        {{ old('sucursal_id', optional($departamento)->sucursal_id) == $s->id ? 'selected' : '' }}>
                                        {{ $s->nombre_sucursal }} ({{ optional($s->empresa)->nombre_empresa }})
                                    </option>
                                @endforeach
                            </select>
                            @error('sucursal_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_departamento">Nombre Departamento <span class="required">*</span></label>
                            <input type="text"
                                   name="nombre_departamento"
                                   id="nombre_departamento"
                                   class="form-control @error('nombre_departamento') is-invalid @enderror"
                                   placeholder="Ej: Recursos Humanos"
                                   maxlength="100"
                                   value="{{ old('nombre_departamento', optional($departamento)->nombre_departamento) }}"
                                   required>
                            @error('nombre_departamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jefe_empleado_id">Jefe de Departamento</label>
                            <select name="jefe_empleado_id" id="jefe_empleado_id" class="form-control @error('jefe_empleado_id') is-invalid @enderror">
                                <option value="">-- Sin jefe --</option>
                                @foreach($empleados as $emp)
                                    <option value="{{ $emp->id }}"
                                        {{ old('jefe_empleado_id', optional($departamento)->jefe_empleado_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->nombres }} {{ $emp->apellidos }} ({{ $emp->codigo_empleado }})
                                    </option>
                                @endforeach
                            </select>
                            @error('jefe_empleado_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="activo"  {{ old('estado', optional($departamento)->estado) == 'activo'  ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado', optional($departamento)->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion"
                                      id="descripcion"
                                      class="form-control @error('descripcion') is-invalid @enderror"
                                      placeholder="Funciones principales del departamento..."
                                      rows="3">{{ old('descripcion', optional($departamento)->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-right">
                <a href="{{ route('admin.departamentos.index') }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="voyager-check"></i> {{ (isset($departamento) && $departamento->exists) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </div>
    </form>
</div>
@stop

@section('javascript')
<script>
    $(document).ready(function () {
        setTimeout(function() {
            $('.auto-dismiss').fadeOut('slow', function() { $(this).remove(); });
        }, 5000);
        $('.auto-dismiss .close').click(function(e) {
            e.preventDefault();
            $(this).closest('.alert').fadeOut('slow', function() { $(this).remove(); });
        });

        // Auto-trim
        $('#nombre_departamento, #descripcion').on('blur', function () {
            $(this).val($(this).val().trim());
        });

        // Validación básica antes de enviar
        $('#departamento-form').on('submit', function () {
            const nombre = $('#nombre_departamento').val().trim();
            $('#nombre_departamento').val(nombre);
            if (!nombre) {
                alert('El nombre del departamento es obligatorio');
                $('#nombre_departamento').focus();
                return false;
            }
        });
    });
</script>
@stop
