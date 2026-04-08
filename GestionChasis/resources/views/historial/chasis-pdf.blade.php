<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Historial de Movimientos de Chasis</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { margin-bottom: 14px; color: #555; }
        .bloque { margin-bottom: 14px; padding: 10px; border: 1px solid #ddd; border-radius: 6px; }
        .titulo { font-weight: bold; margin-bottom: 6px; }
        .small { color: #666; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    @if(!empty($placa))
        <h1>Historial de movimientos de la placa {{ $placa }}</h1>
    @else
        <h1>Historial general de movimientos de chasis</h1>
    @endif
    <div class="meta">Generado en: {{ $generadoEn }}</div>

    @forelse($registros as $registro)
        <div class="bloque">
            <div class="titulo">{{ strtoupper($registro->accion) }} - {{ $registro->descripcion }}</div>
            @php
                $fechaRegistro = $registro->created_at
                    ? $registro->created_at->copy()->timezone($timezone ?? config('app.timezone', 'UTC'))->format('Y-m-d H:i:s')
                    : 'N/D';
            @endphp
            <div class="small">Fecha: {{ $fechaRegistro }}</div>
            <div class="small">Placa: {{ $registro->detalle['placa'] ?? ($registro->chasis->placa ?? 'N/D') }}</div>

            @if(!empty($registro->detalle['cambios']))
                <table>
                    <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Antes</th>
                            <th>Despues</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registro->detalle['cambios'] as $cambio)
                            <tr>
                                <td>{{ $cambio['campo'] ?? '' }}</td>
                                <td>{{ is_bool($cambio['antes'] ?? null) ? (($cambio['antes'] ?? false) ? 'Si' : 'No') : ($cambio['antes'] ?? '-') }}</td>
                                <td>{{ is_bool($cambio['despues'] ?? null) ? (($cambio['despues'] ?? false) ? 'Si' : 'No') : ($cambio['despues'] ?? '-') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif(!empty($registro->detalle['nuevo']) || !empty($registro->detalle['anterior']))
                <table>
                    <thead>
                        <tr>
                            <th>Dato</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $fuente = $registro->detalle['nuevo'] ?? $registro->detalle['anterior'] ?? [];
                        @endphp
                        @foreach($fuente as $clave => $valor)
                            <tr>
                                <td>{{ $clave }}</td>
                                <td>{{ is_array($valor) ? json_encode($valor) : (is_bool($valor) ? ($valor ? 'Si' : 'No') : $valor) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @empty
        @if(!empty($placa))
            <p>No hay movimientos registrados para la placa indicada.</p>
        @else
            <p>No hay movimientos registrados.</p>
        @endif
    @endforelse
</body>
</html>
