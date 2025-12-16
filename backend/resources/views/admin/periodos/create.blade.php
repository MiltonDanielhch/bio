@extends('voyager::master')

@section('page_title', 'Crear Período de Planilla')

@section('page_header')
    <h1 class="page-title">
        <i class="voyager-calendar"></i> Crear Nuevo Período de Planilla
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">Información del Período</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route('admin.periodos.store') }}" method="POST">
                            @csrf

                            <div class="form-group @error('empresa_id') has-error @enderror">
                                <label for="empresa_id">Empresa <span class="text-danger">*</span></label>
                                <select name="empresa_id" id="empresa_id" class="form-control" required>
                                    <option value="">Seleccione una empresa</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                            {{ $empresa->nombre_empresa }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('empresa_id')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group @error('nombre') has-error @enderror">
                                <label for="nombre">Nombre del Período <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="nombre" 
                                       id="nombre" 
                                       class="form-control" 
                                       placeholder="Ej: Planilla Enero 2025" 
                                       value="{{ old('nombre') }}"
                                       required>
                                @error('nombre')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror
                                <small class="help-block">Un nombre descriptivo para identificar este período</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group @error('fecha_inicio') has-error @enderror">
                                        <label for="fecha_inicio">Fecha Inicio <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               name="fecha_inicio" 
                                               id="fecha_inicio" 
                                               class="form-control" 
                                               value="{{ old('fecha_inicio') }}"
                                               required>
                                        @error('fecha_inicio')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group @error('fecha_fin') has-error @enderror">
                                        <label for="fecha_fin">Fecha Fin <span class="text-danger">*</span></label>
                                        <input type="date" 
                                               name="fecha_fin" 
                                               id="fecha_fin" 
                                               class="form-control" 
                                               value="{{ old('fecha_fin') }}"
                                               required>
                                        @error('fecha_fin')
                                            <span class="help-block">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group @error('descripcion') has-error @enderror">
                                <label for="descripcion">Descripción (Opcional)</label>
                                <textarea name="descripcion" 
                                          id="descripcion" 
                                          class="form-control" 
                                          rows="3"
                                          placeholder="Agregue cualquier detalle adicional sobre este período...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <span class="help-block">{{ $message }}</span>
                                @enderror>
                            </div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="voyager-info-circled"></i> Información Importante
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <ul>
                                        <li>El código del período se generará automáticamente basado en la fecha de inicio (formato: YYYY-MM)</li>
                                        <li>Una vez creado, puede <strong>cerrar el período</strong> para procesar toda la asistencia del rango de fechas</li>
                                        <li>El sistema calculará automáticamente:
                                            <ul>
                                                <li>Tardanzas y minutos de retraso</li>
                                                <li>Faltas injustificadas</li>
                                                <li>Horas trabajadas y horas programadas</li>
                                                <li>Horas extra clasificadas (25%, 50%, 100%)</li>
                                            </ul>
                                        </li>
                                        <li>Una vez cerrado, podrá exportar el resumen a Excel para planilla</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="form-group text-right">
                                <a href="{{ route('admin.periodos.index') }}" class="btn btn-default">
                                    <i class="voyager-x"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="voyager-check"></i> Crear Período
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
    // Auto-llenar nombre basado en fechas
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        const fecha = new Date(this.value + 'T00:00:00');
        const meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        const nombreSugerido = `Planilla ${meses[fecha.getMonth()]} ${fecha.getFullYear()}`;
        
        const campoNombre = document.getElementById('nombre');
        if (!campoNombre.value) {
            campoNombre.value = nombreSugerido;
        }
    });

    // Auto-llenar fecha fin (último día del mes)
    document.getElementById('fecha_inicio').addEventListener('change', function() {
        const fechaInicio = new Date(this.value + 'T00:00:00');
        const ultimoDia = new Date(fechaInicio.getFullYear(), fechaInicio.getMonth() + 1, 0);
        const fechaFinFormato = ultimoDia.toISOString().split('T')[0];
        
        const campoFechaFin = document.getElementById('fecha_fin');
        if (!campoFechaFin.value) {
            campoFechaFin.value = fechaFinFormato;
        }
    });
</script>
@endsection
