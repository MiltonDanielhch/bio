@extends('voyager::master')

@section('page_title', 'Períodos de Planilla')

@section('page_header')
    <div class="container-fluid">
        @include('voyager::alerts')

        @if(session('success'))
            <div class="alert alert-success alert-dismissible auto-dismiss">
                <button type="button" class="close" data-dismiss="alert"  aria-hidden="true">×</button>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible auto-dismiss">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered" style="margin-bottom: 0;">
                    <div class="panel-body" style="padding: 0;">
                        <div class="col-md-8" style="padding: 0;">
                            <h1 class="page-title">
                                <i class="voyager-calendar"></i> Períodos de Planilla
                            </h1>
                        </div>
                        <div class="col-md-4 text-right" style="margin-top: 30px;">
                            <a href="{{ route('admin.periodos.create') }}" class="btn btn-success">
                                <i class="voyager-plus"></i> Nuevo Período
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <!-- Filtro por empresa -->
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-md-4">
                                <label>Filtrar por Empresa:</label>
                                <select class="form-control" id="filter-empresa" onchange="filtrarPorEmpresa()">
                                    <option value="">Todas las empresas</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}" {{ $empresaId == $empresa->id ? 'selected' : '' }}>
                                            {{ $empresa->nombre_empresa }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Tabla de períodos -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Empresa</th>
                                        <th>Período</th>
                                        <th>Días</th>
                                        <th>Estado</th>
                                        <th>Empleados</th>
                                        <th>Cerrado por</th>
                                        <th class="text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($periodos as $periodo)
                                        <tr>
                                            <td><strong>{{ $periodo->codigo }}</strong></td>
                                            <td>{{ $periodo->nombre }}</td>
                                            <td>{{ $periodo->empresa->nombre_empresa ?? '-' }}</td>
                                            <td>
                                                {{ $periodo->fecha_inicio->format('d/m/Y') }} -
                                                {{ $periodo->fecha_fin->format('d/m/Y') }}
                                            </td>
                                            <td>{{ $periodo->duracion_dias }} días</td>
                                            <td>
                                                @if($periodo->estado === 'abierto')
                                                    <span class="label label-primary">Abierto</span>
                                                @elseif($periodo->estado === 'procesando')
                                                    <span class="label label-warning">
                                                        <i class="voyager-refresh"></i> Procesando...
                                                    </span>
                                                @elseif($periodo->estado === 'cerrado')
                                                    <span class="label label-success">Cerrado</span>
                                                @else
                                                    <span class="label label-danger">Error</span>
                                                @endif
                                            </td>
                                            <td>{{ $periodo->total_empleados_procesados }}</td>
                                            <td>
                                                @if($periodo->cerradoPor)
                                                    {{ $periodo->cerradoPor->name }}<br>
                                                    <small class="text-muted">{{ $periodo->fecha_cierre?->format('d/m/Y H:i') }}</small>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('admin.periodos.show', $periodo->id) }}" 
                                                   class="btn btn-sm btn-info" title="Ver Detalle">
                                                    <i class="voyager-eye"></i>
                                                </a>

                                                @if($periodo->puedeCerrarse())
                                                    <form action="{{ route('admin.periodos.cerrar', $periodo->id) }}" 
                                                          method="POST" style="display: inline-block;"
                                                          onsubmit="return confirm('¿Confirma el cierre de este período? Esta acción procesará la planilla.')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Cerrar Período">
                                                            <i class="voyager-lock"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($periodo->estado === 'cerrado')
                                                    <a href="{{ route('admin.periodos.exportar', $periodo->id) }}" 
                                                       class="btn btn-sm btn-success" title="Exportar Excel">
                                                        <i class="voyager-download"></i>
                                                    </a>

                                                    <form action="{{ route('admin.periodos.reabrir', $periodo->id) }}" 
                                                          method="POST" style="display: inline-block;"
                                                          onsubmit="return confirm('¿Confirma reabrir este período? Se eliminarán todos los resúmenes generados.')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-primary" title="Reabrir">
                                                            <i class="voyager-unlock"></i>
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($periodo->puedeEditarse())
                                                    <form action="{{ route('admin.periodos.destroy', $periodo->id) }}" 
                                                          method="POST" style="display: inline-block;"
                                                          onsubmit="return confirm('¿Confirma eliminar este período?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                            <i class="voyager-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <p>No hay períodos registrados.</p>
                                                <a href="{{ route('admin.periodos.create') }}" class="btn btn-primary">
                                                    Crear el primer período
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="text-center">
                            {{ $periodos->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
    function filtrarPorEmpresa() {
        const empresaId = document.getElementById('filter-empresa').value;
        const url = new URL(window.location.href);
        if (empresaId) {
            url.searchParams.set('empresa_id', empresaId);
        } else {
            url.searchParams.delete('empresa_id');
        }
        window.location.href = url.toString();
    }

    // Auto-refrescar si hay períodos procesando
    @if($periodos->where('estado', 'procesando')->count() > 0)
        setTimeout(() => {
            location.reload();
        }, 10000); // Refrescar cada 10 segundos
    @endif
</script>
@endsection
