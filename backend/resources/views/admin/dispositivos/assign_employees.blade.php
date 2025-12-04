@extends('voyager::master')

@section('page_title', 'Asignar Empleados a ' . $dispositivo->nombre_dispositivo)

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        <form action="{{ route('admin.dispositivos.store_employees', $dispositivo->id) }}" method="POST">
            @csrf
            
            <div class="panel panel-bordered panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="voyager-people"></i>
                        Asignar Empleados a: <strong>{{ $dispositivo->nombre_dispositivo }}</strong>
                    </h3>
                </div>

                <div class="panel-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="empleados">Seleccione los Empleados</label>
                        <select name="empleados[]" id="empleados" class="form-control select2" multiple="multiple">
                            @foreach($empleados as $empleado)
                                <option value="{{ $empleado->id }}" 
                                    @if(in_array($empleado->id, $assignedIds)) selected @endif>
                                    {{ $empleado->full_name }} ({{ $empleado->dni ?? $empleado->codigo_empleado }})
                                </option>
                            @endforeach
                        </select>
                        <p class="help-block">Puede buscar por nombre, DNI o código. Los empleados seleccionados serán asignados al dispositivo.</p>
                    </div>
                </div>

                <div class="panel-footer text-right">
                    <a href="{{ route('admin.dispositivos.index') }}" class="btn btn-default">
                        <i class="voyager-angle-left"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="voyager-check"></i> Guardar Asignaciones
                    </button>
                </div>
            </div>
        </form>
    </div>
@stop

@section('javascript')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Seleccione empleados...",
                allowClear: true
            });
        });
    </script>
@stop
