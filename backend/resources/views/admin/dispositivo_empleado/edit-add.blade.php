@extends('voyager::master')

@section('page_title', ($map->id ? 'Editar' : 'Crear') . ' Mapeo')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-data"></i>
            {{ $map->id ? 'Editar Mapeo' : 'Crear Nuevo Mapeo' }}
        </h1>
        <a href="{{ route('admin.dispositivo-empleado.index') }}" class="btn btn-warning btn-add-new">
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
                        <form method="POST" action="{{ $map->id ? route('admin.dispositivo-empleado.update', $map->id) : route('admin.dispositivo-empleado.store') }}">
                            @csrf
                            @if($map->id)
                                @method('PUT')
                            @endif

                            <div class="form-group">
                                <label for="empleado_id">Empleado</label>
                                <select name="empleado_id" id="empleado_id" class="form-control select2" required>
                                    <option value="">-- Seleccione un empleado --</option>
                                    @foreach($empleados as $empleado)
                                        <option value="{{ $empleado->id }}" @if(old('empleado_id', $map->empleado_id) == $empleado->id) selected @endif>
                                            {{ $empleado->nombres }} {{ $empleado->apellidos }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('empleado_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="dispositivo_id">Dispositivo Biométrico</label>
                                <select name="dispositivo_id" id="dispositivo_id" class="form-control select2" required>
                                    <option value="">-- Seleccione un dispositivo --</option>
                                    @foreach($dispositivos as $dispositivo)
                                        <option value="{{ $dispositivo->id }}" @if(old('dispositivo_id', $map->dispositivo_id) == $dispositivo->id) selected @endif>
                                            {{ $dispositivo->nombre_dispositivo }} ({{ $dispositivo->direccion_ip }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('dispositivo_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="zk_user_id">ID de Usuario en el Reloj (zk_user_id)</label>
                                <input type="number" name="zk_user_id" id="zk_user_id" class="form-control" value="{{ old('zk_user_id', $map->zk_user_id) }}" required>
                                <small class="form-text text-muted">Este es el ID numérico que el empleado tiene asignado DENTRO del dispositivo biométrico.</small>
                                @error('zk_user_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ $map->id ? 'Actualizar Mapeo' : 'Guardar Mapeo' }}
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
