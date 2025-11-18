<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Reporte</th>
                    <th>Periodo</th>
                    <th>Generado Por</th>
                    <th>Estado</th>
                    <th class="text-right">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $reporte)
                <tr>
                    <td>{{ $reporte->id }}</td>
                    <td>
                        {{ $reporte->nombre_reporte }}<br>
                        <small>Generado el: {{ $reporte->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        {{ \Carbon\Carbon::parse($reporte->fecha_inicio)->format('d/m/Y') }} -
                        {{ \Carbon\Carbon::parse($reporte->fecha_fin)->format('d/m/Y') }}
                    </td>
                    <td>{{ optional($reporte->generador)->name }}</td>
                    <td>
                        @php
                            $labelClass = 'default';
                            if ($reporte->estado == 'completado') $labelClass = 'success';
                            if ($reporte->estado == 'procesando') $labelClass = 'warning';
                            if ($reporte->estado == 'error') $labelClass = 'danger';
                        @endphp
                        <span class="label label-{{ $labelClass }}">{{ ucfirst($reporte->estado) }}</span>
                    </td>
                    <td class="text-right" style="width: 25%">
                        @can('view', $reporte)
                            <a href="{{ route('admin.reportes-asistencia.show', $reporte) }}" title="Ver" class="btn btn-sm btn-info">
                                <i class="voyager-eye"></i> Ver
                            </a>
                        @endcan

                        @if($reporte->estado == 'completado')
                            @can('view', $reporte)
                                <a href="{{ route('admin.reportes-asistencia.download', $reporte) }}" title="Descargar" class="btn btn-sm btn-success">
                                    <i class="voyager-download"></i> Descargar
                                </a>
                            @endcan
                        @endif

                        @can('delete', $reporte)
                            <button type="button"
                                    class="btn btn-sm btn-danger"
                                    title="Borrar"
                                    onclick="deleteItem('{{ route('admin.reportes-asistencia.destroy', $reporte) }}', '{{ $reporte->nombre_reporte }}')"
                                    data-toggle="modal"
                                    data-target="#delete_modal">
                                <i class="voyager-trash"></i> Borrar
                            </button>
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <h5 class="text-center" style="margin-top: 50px">
                            <img src="{{ asset('images/empty.png') }}" width="120px" alt="" style="opacity: 0.8">
                            <br><br>
                            No se han generado reportes todavía.
                        </h5>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="col-md-12">
    <div class="col-md-4 text-muted">
        @if($items->count())
            Mostrando del {{ $items->firstItem() }} al {{ $items->lastItem() }} de {{ $items->total() }} registros.
        @endif
    </div>
    <div class="col-md-8 text-right">
        <nav class="text-right">{{ $items->links() }}</nav>
    </div>
</div>

@include('admin.partials.list-pagination-script')
