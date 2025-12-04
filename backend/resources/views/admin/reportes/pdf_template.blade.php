<!DOCTYPE html>
<html>
<head>
    <title>{{ $reporte->nombre_reporte }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; margin-bottom: 30px; }
        .empleado-section { margin-top: 20px; page-break-inside: avoid; }
        .atraso { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $reporte->nombre_reporte }}</h2>
        <p>Generado el: {{ $reporte->created_at->format('d/m/Y H:i') }}</p>
    </div>

    @foreach($data as $item)
        <div class="empleado-section">
            <h3>{{ $item['empleado']->full_name }} ({{ $item['empleado']->codigo_empleado }})</h3>
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Entrada</th>
                        <th>Salida</th>
                        <th>Horas</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($item['dias'] as $fecha => $dia)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</td>
                            <td>{{ $dia['entrada'] }}</td>
                            <td>{{ $dia['salida'] }}</td>
                            <td>{{ $dia['horas'] }}</td>
                            <td class="{{ $dia['estado'] == 'Atraso' ? 'atraso' : '' }}">
                                {{ $dia['estado'] }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
