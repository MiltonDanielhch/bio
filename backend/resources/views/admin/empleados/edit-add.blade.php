@extends('voyager::master')

@section('page_title', ($empleado->id ? 'Editar' : 'Crear') . ' Empleado')

@section('page_header')
    <div class="container-fluid">@extends('voyager::master')

@section('page_title', ($empleado->id ? 'Editar' : 'Crear') . ' Empleado')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-people"></i>
            {{ $empleado->id ? 'Editar Empleado' : 'Crear Nuevo Empleado' }}
        </h1>
        <a href="{{ route('admin.empleados.index') }}" class="btn btn-warning btn-add-new">
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
                    <form method="POST" action="{{ $empleado->id ? route('admin.empleados.update', $empleado->id) : route('admin.empleados.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if($empleado->id)
                            @method('PUT')
                        @endif
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="nombres">Nombres</label>
                                    <input type="text" name="nombres" id="nombres" class="form-control" value="{{ old('nombres', $empleado->nombres) }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="apellidos">Apellidos</label>
                                    <input type="text" name="apellidos" id="apellidos" class="form-control" value="{{ old('apellidos', $empleado->apellidos) }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="dni">DNI</label>
                                    <input type="text" name="dni" id="dni" class="form-control" value="{{ old('dni', $empleado->dni) }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="codigo_empleado">Código de Empleado</label>
                                    <input type="text" name="codigo_empleado" id="codigo_empleado" class="form-control" value="{{ old('codigo_empleado', $empleado->codigo_empleado) }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $empleado->email) }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $empleado->telefono) }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="empresa_id">Empresa</label>
                                    <select name="empresa_id" id="empresa_id" class="form-control select2" required>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}" @if(old('empresa_id', $empleado->empresa_id) == $empresa->id) selected @endif>{{ $empresa->nombre_empresa }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="departamento_id">Departamento</label>
                                    <select name="departamento_id" id="departamento_id" class="form-control select2">
                                        <option value="">-- Sin departamento --</option>
                                        @foreach($departamentos as $depto)
                                            <option value="{{ $depto->id }}" @if(old('departamento_id', $empleado->departamento_id) == $depto->id) selected @endif>{{ $depto->nombre_departamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="fecha_contratacion">Fecha de Contratación</label>
                                    <input type="date" name="fecha_contratacion" id="fecha_contratacion" class="form-control" value="{{ old('fecha_contratacion', optional($empleado->fecha_contratacion)->format('Y-m-d')) }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="tipo_contrato">Tipo de Contrato</label>
                                    <select name="tipo_contrato" id="tipo_contrato" class="form-control select2" required>
                                        <option value="indefinido" @if(old('tipo_contrato', $empleado->tipo_contrato) == 'indefinido') selected @endif>Indefinido</option>
                                        <option value="plazo_fijo" @if(old('tipo_contrato', $empleado->tipo_contrato) == 'plazo_fijo') selected @endif>Plazo Fijo</option>
                                        <option value="servicios" @if(old('tipo_contrato', $empleado->tipo_contrato) == 'servicios') selected @endif>Servicios</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="estado">Estado</label>
                                    <select name="estado" id="estado" class="form-control select2" required>
                                        <option value="activo" @if(old('estado', $empleado->estado) == 'activo') selected @endif>Activo</option>
                                        <option value="inactivo" @if(old('estado', $empleado->estado) == 'inactivo') selected @endif>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="foto_perfil">Foto de Perfil</label>
                                @if($empleado->foto_perfil)
                                    <img src="{{ Storage::url($empleado->foto_perfil) }}" style="width: 100px; display: block; margin-bottom: 10px;">
                                @endif
                                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ $empleado->id ? 'Actualizar Empleado' : 'Guardar Empleado' }}
                            </button>
                        </div>
                    </form>
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

        <h1 class="page-title">
            <i class="voyager-people"></i>
            {{ $empleado->id ? 'Editar Empleado' : 'Crear Nuevo Empleado' }}
        </h1>
        <a href="{{ route('admin.empleados.index') }}" class="btn btn-warning btn-add-new">
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
                    <form method="POST" action="{{ $empleado->id ? route('admin.empleados.update', $empleado->id) : route('admin.empleados.store') }}" enctype="multipart/form-data">
                        @csrf
                        @if($empleado->id)
                            @method('PUT')
                        @endif
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="nombres">Nombres</label>
                                    <input type="text" name="nombres" id="nombres" class="form-control" value="{{ old('nombres', $empleado->nombres) }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="apellidos">Apellidos</label>
                                    <input type="text" name="apellidos" id="apellidos" class="form-control" value="{{ old('apellidos', $empleado->apellidos) }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="dni">DNI</label>
                                    <input type="text" name="dni" id="dni" class="form-control" value="{{ old('dni', $empleado->dni) }}" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="codigo_empleado">Código de Empleado</label>
                                    <input type="text" name="codigo_empleado" id="codigo_empleado" class="form-control" value="{{ old('codigo_empleado', $empleado->codigo_empleado) }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $empleado->email) }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="telefono">Teléfono</label>
                                    <input type="text" name="telefono" id="telefono" class="form-control" value="{{ old('telefono', $empleado->telefono) }}">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="empresa_id">Empresa</label>
                                    <select name="empresa_id" id="empresa_id" class="form-control select2" required>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}" @if(old('empresa_id', $empleado->empresa_id) == $empresa->id) selected @endif>{{ $empresa->nombre_empresa }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="departamento_id">Departamento</label>
                                    <select name="departamento_id" id="departamento_id" class="form-control select2">
                                        <option value="">-- Sin departamento --</option>
                                        @foreach($departamentos as $depto)
                                            <option value="{{ $depto->id }}" @if(old('departamento_id', $empleado->departamento_id) == $depto->id) selected @endif>{{ $depto->nombre_departamento }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="fecha_contratacion">Fecha de Contratación</label>
                                    <input type="date" name="fecha_contratacion" id="fecha_contratacion" class="form-control" value="{{ old('fecha_contratacion', optional($empleado->fecha_contratacion)->format('Y-m-d')) }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="tipo_contrato">Tipo de Contrato</label>
                                    <select name="tipo_contrato" id="tipo_contrato" class="form-control select2" required>
                                        <option value="indefinido" @if(old('tipo_contrato', $empleado->tipo_contrato) == 'indefinido') selected @endif>Indefinido</option>
                                        <option value="plazo_fijo" @if(old('tipo_contrato', $empleado->tipo_contrato) == 'plazo_fijo') selected @endif>Plazo Fijo</option>
                                        <option value="servicios" @if(old('tipo_contrato', $empleado->tipo_contrato) == 'servicios') selected @endif>Servicios</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="estado">Estado</label>
                                    <select name="estado" id="estado" class="form-control select2" required>
                                        <option value="activo" @if(old('estado', $empleado->estado) == 'activo') selected @endif>Activo</option>
                                        <option value="inactivo" @if(old('estado', $empleado->estado) == 'inactivo') selected @endif>Inactivo</option>
                                    </select>
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="foto_perfil">Foto de Perfil</label>
                                @if($empleado->foto_perfil)
                                    <img src="{{ Storage::url($empleado->foto_perfil) }}" style="width: 100px; display: block; margin-bottom: 10px;">
                                @endif
                                <input type="file" name="foto_perfil" id="foto_perfil" accept="image/*">
                            </div>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" class="btn btn-primary">
                                {{ $empleado->id ? 'Actualizar Empleado' : 'Guardar Empleado' }}
                            </button>
                        </div>
                    </form>
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
