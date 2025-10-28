@extends('voyager::master')

@section('page_title', ($sucursal->exists ?? false) ? 'Editar Sucursal' : 'Agregar Sucursal')

@section('content')
<div class="page-content container-fluid">
    @if(session('message'))
        <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible auto-dismiss">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <form action="{{ ($sucursal->exists ?? false)
            ? route('admin.sucursales.update', $sucursal)
            : route('admin.sucursales.store') }}"
          method="POST"
          id="sucursal-form"
          enctype="multipart/form-data">
        @csrf
        @if($sucursal->exists ?? false) @method('PUT') @endif

        <div class="panel panel-bordered panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="voyager-shop"></i>
                    {{ ($sucursal->exists ?? false) ? 'Editar' : 'Agregar' }} Sucursal
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
                            <label for="empresa_id">Empresa <span class="required">*</span></label>
                            <select name="empresa_id" id="empresa_id" class="form-control @error('empresa_id') is-invalid @enderror" required>
                                <option value="">-- Seleccione --</option>
                                @foreach($empresas as $emp)
                                    <option value="{{ $emp->id }}"
                                        {{ old('empresa_id', optional($sucursal)->empresa_id) == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->nombre_empresa }} ({{ $emp->ruc }})
                                    </option>
                                @endforeach
                            </select>
                            @error('empresa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nombre_sucursal">Nombre Sucursal <span class="required">*</span></label>
                            <input type="text"
                                   name="nombre_sucursal"
                                   id="nombre_sucursal"
                                   class="form-control @error('nombre_sucursal') is-invalid @enderror"
                                   placeholder="Ej: Sucursal Trinidad"
                                   maxlength="100"
                                   value="{{ old('nombre_sucursal', optional($sucursal)->nombre_sucursal) }}"
                                   required>
                            @error('nombre_sucursal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea name="direccion"
                                      id="direccion"
                                      class="form-control @error('direccion') is-invalid @enderror"
                                      placeholder="Av. 6 de Agosto, Edif. Amazonía, Piso 3"
                                      rows="2">{{ old('direccion', optional($sucursal)->direccion) }}</textarea>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="ciudad">Ciudad</label>
                            <input type="text"
                                   name="ciudad"
                                   id="ciudad"
                                   class="form-control @error('ciudad') is-invalid @enderror"
                                   placeholder="Ej: Trinidad"
                                   maxlength="100"
                                   value="{{ old('ciudad', optional($sucursal)->ciudad) }}">
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="pais">País</label>
                            <input type="text"
                                   name="pais"
                                   id="pais"
                                   class="form-control @error('pais') is-invalid @enderror"
                                   placeholder="Ej: Bolivia"
                                   maxlength="100"
                                   value="{{ old('pais', optional($sucursal)->pais ?? 'Bolivia') }}">
                            @error('pais')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="zona_horaria">Zona Horaria</label>
                            <input type="text"
                                   name="zona_horaria"
                                   id="zona_horaria"
                                   class="form-control @error('zona_horaria') is-invalid @enderror"
                                   placeholder="Ej: America/La_Paz"
                                   maxlength="50"
                                   value="{{ old('zona_horaria', optional($sucursal)->zona_horaria ?? 'America/La_Paz') }}">
                            @error('zona_horaria')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="latitud">Latitud</label>
                            <input type="number"
                                   name="latitud"
                                   id="latitud"
                                   class="form-control @error('latitud') is-invalid @enderror"
                                   placeholder="-16.5000"
                                   step="0.00000001"
                                   min="-90"
                                   max="90"
                                   value="{{ old('latitud', optional($sucursal)->latitud) }}">
                            @error('latitud')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="longitud">Longitud</label>
                            <input type="number"
                                   name="longitud"
                                   id="longitud"
                                   class="form-control @error('longitud') is-invalid @enderror"
                                   placeholder="-68.1193"
                                   step="0.00000001"
                                   min="-180"
                                   max="180"
                                   value="{{ old('longitud', optional($sucursal)->longitud) }}">
                            @error('longitud')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="activo"  {{ old('estado', optional($sucursal)->estado) == 'activo'  ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado', optional($sucursal)->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-right">
                <a href="{{ route('admin.sucursales.index') }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="voyager-check"></i> {{ ($sucursal->exists ?? false) ? 'Actualizar' : 'Guardar' }}
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

        // Auto-trim textos
        $('#nombre_sucursal, #direccion, #ciudad, #pais, #zona_horaria').on('blur', function () {
            $(this).val($(this).val().trim());
        });

        // Validación básica antes de enviar
        $('#sucursal-form').on('submit', function () {
            const nombre = $('#nombre_sucursal').val().trim();
            $('#nombre_sucursal').val(nombre);
            if (!nombre) {
                alert('El nombre de la sucursal es obligatorio');
                $('#nombre_sucursal').focus();
                return false;
            }
        });
    });
</script>
@stop
