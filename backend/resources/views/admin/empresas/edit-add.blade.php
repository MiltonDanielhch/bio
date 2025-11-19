@extends('voyager::master')

@section('page_title', ($empresa->exists ?? false) ? 'Editar Empresa' : 'Agregar Empresa')

@section('content') {{-- Se agrega para que funcione el breadcrumb de Voyager --}}
<div class="page-content container-fluid">
    @if(session('message'))
        <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible auto-dismiss">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <form action="{{ $empresa->exists ? route('admin.empresas.update', $empresa) : route('admin.empresas.store') }}"
          method="POST"
          id="empresa-form"
          enctype="multipart/form-data">
        @csrf
        @if($empresa->exists ?? false) @method('PUT') @endif

        <div class="panel panel-bordered">
            <h3 class="panel-title" style="padding: 10px;">
                <i class="voyager-briefcase"></i> {{ $empresa->exists ? 'Editar' : 'Agregar' }} Empresa
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
                            <label for="nombre_empresa">Nombre Empresa <span class="required">*</span></label>
                            <input type="text"
                                   name="nombre_empresa"
                                   id="nombre_empresa"
                                   class="form-control @error('nombre_empresa') is-invalid @enderror"
                                   placeholder="Ej: Transportes Amazonía SRL"
                                   maxlength="100"
                                   value="{{ old('nombre_empresa', optional($empresa)->nombre_empresa) }}"
                                   required
                                   autofocus>
                            @error('nombre_empresa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="ruc">RUC <span class="required">*</span></label>
                            <input type="text"
                                   name="ruc"
                                   id="ruc"
                                   class="form-control @error('ruc') is-invalid @enderror"
                                   placeholder="Ej: 1234567890123"
                                   maxlength="25"
                                   value="{{ old('ruc', optional($empresa)->ruc) }}"
                                   required>
                            @error('ruc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="telefono">Teléfono</label>
                            <input type="text"
                                   name="telefono"
                                   id="telefono"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   placeholder="Ej: 33442211"
                                   maxlength="20"
                                   value="{{ old('telefono', optional($empresa)->telefono) }}">
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Ej: info@empresa.com"
                                   maxlength="100"
                                   value="{{ old('email', optional($empresa)->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="logo">Logo</label>
                            <input type="file"
                                   name="logo"
                                   id="logo"
                                   class="form-control @error('logo') is-invalid @enderror"
                                   accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($empresa->logo)
                                <div class="mt-2">
                                    <img src="{{ Storage::url($empresa->logo) }}" width="120" alt="Logo actual">
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="color_primario">Color Primario</label>
                            <input type="color"
                                   name="color_primario"
                                   id="color_primario"
                                   class="form-control"
                                   value="{{ old('color_primario', optional($empresa)->color_primario ?? '#3490dc') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="estado">Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="activo"  {{ old('estado', optional($empresa)->estado) == 'activo'  ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('estado', optional($empresa)->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <textarea name="direccion"
                                      id="direccion"
                                      class="form-control @error('direccion') is-invalid @enderror"
                                      placeholder="Av. 6 de Agosto, Edif. Amazonía, Piso 3"
                                      rows="2">{{ old('direccion', optional($empresa)->direccion) }}</textarea>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-right">
                <a href="{{ route('admin.empresas.index') }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="voyager-check"></i> {{ ($empresa->exists ?? false) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </div>
    </form>
</div>
@stop

@section('javascript')

    $(document).ready(function () {
        setTimeout(function() {
            $('.auto-dismiss').fadeOut('slow', function() { $(this).remove(); });
        }, 5000);
        $('.auto-dismiss .close').click(function(e) {
            e.preventDefault();
            $(this).closest('.alert').fadeOut('slow', function() { $(this).remove(); });
        });

        // Auto-trim textos
        $('#nombre_empresa, #ruc, #telefono, #email').on('blur', function () {
            $(this).val($(this).val().trim());
        });

        // Prevenir submit vacío
        $('#empresa-form').on('submit', function () {
            const nombre = $('#nombre_empresa').val().trim();
            $('#nombre_empresa').val(nombre);
            if (!nombre) {
                alert('El nombre de la empresa es obligatorio');
                $('#nombre_empresa').focus();
                return false;
            }
        });
    });

@stop
