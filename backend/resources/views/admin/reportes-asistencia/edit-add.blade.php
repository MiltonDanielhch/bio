@extends('voyager::master')

@section('page_title', 'Generar Nuevo Reporte')

@section('content')
<div class="page-content container-fluid">
    <form action="{{ route('admin.reportes-asistencia.store') }}" method="POST" id="form">
        @csrf

        <div class="panel panel-bordered panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="voyager-bar-chart"></i>
                    Generar Nuevo Reporte de Asistencia
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
                            <label for="empresa_id">Empresa <span class="required">*</span></label>
                            <select name="empresa_id" id="empresa_id" class="form-control select2" required>
                                @foreach($empresas as $empresa)
                                    <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                        {{ $empresa->nombre_empresa }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tipo">Tipo de Reporte <span class="required">*</span></label>
                            <select name="tipo" id="tipo" class="form-control select2" required>
                                <option value="diario" {{ old('tipo') == 'diario' ? 'selected' : '' }}>Diario</option>
                                <option value="semanal" {{ old('tipo') == 'semanal' ? 'selected' : '' }}>Semanal</option>
                                <option value="mensual" {{ old('tipo') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                                <option value="custom" {{ old('tipo') == 'custom' ? 'selected' : '' }}>Rango Personalizado</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_inicio">Fecha de Inicio <span class="required">*</span></label>
                            <input type="date" name="fecha_inicio" id="fecha_inicio" class="form-control"
                                   value="{{ old('fecha_inicio', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fecha_fin">Fecha de Fin <span class="required">*</span></label>
                            <input type="date" name="fecha_fin" id="fecha_fin" class="form-control"
                                   value="{{ old('fecha_fin', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="voyager-info-circled"></i> La generación del reporte puede tardar unos minutos. Una vez completado, aparecerá en el listado y podrás descargarlo.
                </div>
            </div>

            <div class="panel-footer text-right">
                <a href="{{ route('admin.reportes-asistencia.index') }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="voyager-paper-plane"></i> Enviar a Generar
                </button>
            </div>
        </div>
    </form>
</div>
@stop

