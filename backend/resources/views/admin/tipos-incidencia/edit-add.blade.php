@extends('voyager::master')

@section('page_title', ($tipo->exists ?? false) ? 'Editar Tipo de Incidencia' : 'Agregar Tipo de Incidencia')

@section('content')
<div class="page-content container-fluid">
    <form action="{{ ($tipo->exists ?? false)
            ? route('admin.tipos-incidencia.update', $tipo)
            : route('admin.tipos-incidencia.store') }}"
          method="POST"
          id="form">
        @csrf
        @if($tipo->exists ?? false) @method('PUT') @endif

        <div class="panel panel-bordered panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="voyager-tag"></i>
                    {{ ($tipo->exists ?? false) ? 'Editar' : 'Agregar' }} Tipo de Incidencia
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
                            <label for="nombre">Nombre <span class="required">*</span></label>
                            <input type="text"
                                   name="nombre"
                                   id="nombre"
                                   class="form-control"
                                   placeholder="Ej: Permiso por enfermedad"
                                   maxlength="191"
                                   value="{{ old('nombre', optional($tipo)->nombre) }}"
                                   required
                                   autofocus>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion"
                                      id="descripcion"
                                      class="form-control"
                                      placeholder="Detalles sobre el tipo de incidencia..."
                                      rows="3">{{ old('descripcion', optional($tipo)->descripcion) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-right">
                <a href="{{ route('admin.tipos-incidencia.index') }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="voyager-check"></i> {{ ($tipo->exists ?? false) ? 'Actualizar' : 'Guardar' }}
                </button>
            </div>
        </div>
    </form>
</div>
@stop

@section('javascript')
<script>
    // Puedes añadir JS específico aquí si lo necesitas
</script>
@stop

