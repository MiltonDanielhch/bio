@extends('voyager::master')

@section('page_title', ($horario->exists ?? false) ? 'Editar Horario' : 'Agregar Horario')

@section('content')
<div class="page-content container-fluid">
    @if(session('message'))
        <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible auto-dismiss">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            {{ session('message') }}
        </div>
    @endif

    <form action="{{ ($horario->exists ?? false)
            ? route('admin.horarios.update', $horario)
            : route('admin.horarios.store') }}"
          method="POST"
          id="horario-form">
        @csrf
        @if($horario->exists ?? false) @method('PUT') @endif

        <div class="panel panel-bordered panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <i class="voyager-clock"></i>
                    {{ ($horario->exists ?? false) ? 'Editar' : 'Agregar' }} Horario
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
                                        {{ old('empresa_id', optional($horario)->empresa_id) == $emp->id ? 'selected' : '' }}>
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
                            <label for="nombre_horario">Nombre Horario <span class="required">*</span></label>
                            <input type="text"
                                   name="nombre_horario"
                                   id="nombre_horario"
                                   class="form-control @error('nombre_horario') is-invalid @enderror"
                                   placeholder="Ej: Turno Mañana"
                                   maxlength="100"
                                   value="{{ old('nombre_horario', optional($horario)->nombre_horario) }}"
                                   required>
                            @error('nombre_horario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="hora_entrada">Hora Entrada <span class="required">*</span></label>
                            <input type="time"
                                   name="hora_entrada"
                                   id="hora_entrada"
                                   class="form-control @error('hora_entrada') is-invalid @enderror"
                                   value="{{ old('hora_entrada', optional($horario)->hora_entrada) }}"
                                   required>
                            @error('hora_entrada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="hora_salida">Hora Salida <span class="required">*</span></label>
                            <input type="time"
                                   name="hora_salida"
                                   id="hora_salida"
                                   class="form-control @error('hora_salida') is-invalid @enderror"
                                   value="{{ old('hora_salida', optional($horario)->hora_salida) }}"
                                   required>
                            @error('hora_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tolerancia_entrada">Tolerancia Entrada (min)</label>
                            <input type="number"
                                   name="tolerancia_entrada"
                                   id="tolerancia_entrada"
                                   class="form-control @error('tolerancia_entrada') is-invalid @enderror"
                                   placeholder="5"
                                   min="0"
                                   max="60"
                                   value="{{ old('tolerancia_entrada', optional($horario)->tolerancia_entrada ?? 5) }}">
                            @error('tolerancia_entrada')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tolerancia_salida">Tolerancia Salida (min)</label>
                            <input type="number"
                                   name="tolerancia_salida"
                                   id="tolerancia_salida"
                                   class="form-control @error('tolerancia_salida') is-invalid @enderror"
                                   placeholder="5"
                                   min="0"
                                   max="60"
                                   value="{{ old('tolerancia_salida', optional($horario)->tolerancia_salida ?? 5) }}">
                            @error('tolerancia_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="hora_entrada_almuerzo">Entrada Almuerzo</label>
                            <input type="time"
                                   name="hora_entrada_almuerzo"
                                   id="hora_entrada_almuerzo"
                                   class="form-control @error('hora_entrada_almuerzo') is-invalid @enderror"
                                   value="{{ old('hora_entrada_almuerzo', optional($horario)->hora_entrada_almuerzo) }}">
                            @error('hora_entrada_almuerzo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="hora_salida_almuerzo">Salida Almuerzo</label>
                            <input type="time"
                                   name="hora_salida_almuerzo"
                                   id="hora_salida_almuerzo"
                                   class="form-control @error('hora_salida_almuerzo') is-invalid @enderror"
                                   value="{{ old('hora_salida_almuerzo', optional($horario)->hora_salida_almuerzo) }}">
                            @error('hora_salida_almuerzo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="flexible">Jornada Flexible</label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           name="flexible"
                                           id="flexible"
                                           value="1"
                                           {{ old('flexible', optional($horario)->flexible) ? 'checked' : '' }}>
                                    Si
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="nocturno">Jornada Nocturna</label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"
                                           name="nocturno"
                                           id="nocturno"
                                           value="1"
                                           {{ old('nocturno', optional($horario)->nocturno) ? 'checked' : '' }}>
                                    Si
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Días Laborales <span class="required">*</span></label>
                            <div class="row">
                                @php
                                    $dias = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
                                    $oldDias = old('dias_laborales', optional($horario)->dias_laborales ?? ['lunes','martes','miercoles','jueves','viernes']);
                                @endphp
                                @foreach($dias as $dia)
                                    <div class="col-md-2">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox"
                                                       name="dias_laborales[]"
                                                       value="{{ $dia }}"
                                                       {{ in_array($dia, $oldDias) ? 'checked' : '' }}>
                                                {{ ucfirst($dia) }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('dias_laborales')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel-footer text-right">
                <a href="{{ route('admin.horarios.index') }}" class="btn btn-default">
                    <i class="voyager-angle-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="voyager-check"></i> {{ ($horario->exists ?? false) ? 'Actualizar' : 'Guardar' }}
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
        $('#nombre_horario').on('blur', function () {
            $(this).val($(this).val().trim());
        });

        // Validación básica antes de enviar
        $('#horario-form').on('submit', function () {
            const nombre = $('#nombre_horario').val().trim();
            $('#nombre_horario').val(nombre);
            if (!nombre) {
                alert('El nombre del horario es obligatorio');
                $('#nombre_horario').focus();
                return false;
            }

            // Al menos un día marcado
            if ($('input[name="dias_laborales[]"]:checked').length === 0) {
                alert('Selecciona al menos un día laboral');
                return false;
            }
        });
    });
</script>
@stop
