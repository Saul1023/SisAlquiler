<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de {{ ucfirst($reportType) }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #334155;
            margin: 0;
            padding: 10px;
        }
        .header {
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
        }
        .info {
            text-align: right;
            color: #64748b;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table th, .table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 8px 10px;
            text-align: left;
        }
        .table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        .status {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8px;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .status-active, .status-paid, .status-disponible {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-atrasado, .status-ocupado {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-pendiente, .status-mantenimiento {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-inactive, .status-finalizado, .status-cancelado {
            background-color: #f1f5f9;
            color: #334155;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            text-align: center;
            font-size: 8px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">REPORTE DE {{ strtoupper($reportType) }}</div>
                    <div style="font-size: 10px; color: #64748b; margin-top: 5px;">{{ $company_name }}</div>
                </td>
                <td class="info">
                    <div>Generado: {{ $date }}</div>
                    @if($dateFrom || $dateTo)
                        <div style="font-size: 9px; color: #64748b;">Rango: {{ $dateFrom ? Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : 'Inicio' }} al {{ $dateTo ? Carbon\Carbon::parse($dateTo)->format('d/m/Y') : 'Fin' }}</div>
                    @endif
                    <div>Registros: {{ count($data) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            @if($reportType === 'ingresos')
                <tr>
                    <th>Periodo</th>
                    <th style="text-align: center;">Cantidad Transacciones</th>
                    <th style="text-align: right;">Total Cobrado</th>
                </tr>
            @elseif($reportType === 'morosidad')
                <tr>
                    <th>Inquilino</th>
                    <th>Habitación</th>
                    <th>Periodo Cubierto</th>
                    <th>Días Atraso</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Deuda</th>
                </tr>
            @elseif($reportType === 'ocupacion')
                <tr>
                    <th>Habitación</th>
                    <th>Ubicación</th>
                    <th>Capacidad</th>
                    <th>Precio Base</th>
                    <th>Estado</th>
                    <th>Inquilino Actual</th>
                    <th>F. Entrada</th>
                </tr>
            @elseif($reportType === 'contratos')
                <tr>
                    <th>Inquilino</th>
                    <th>Habitación</th>
                    <th>F. Entrada</th>
                    <th>F. Salida</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Precio Mensual</th>
                </tr>
            @elseif($reportType === 'pagos')
                <tr>
                    <th>Comprobante</th>
                    <th>Inquilino</th>
                    <th>Cuarto</th>
                    <th>Periodo</th>
                    <th>F. Pago</th>
                    <th>Método</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Monto</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @forelse($data as $item)
                @if($reportType === 'ingresos')
                    <tr>
                        <td style="font-weight: bold; font-family: monospace;">{{ $item['periodo'] }}</td>
                        <td style="text-align: center;">{{ $item['transacciones'] }}</td>
                        <td style="text-align: right; font-weight: bold; font-family: monospace;">{{ $currency }} {{ number_format($item['total'], 2) }}</td>
                    </tr>
                @elseif($reportType === 'morosidad')
                    <tr>
                        <td style="font-weight: bold; color: #1e293b;">{{ $item['inquilino'] }}</td>
                        <td>Cuarto {{ $item['cuarto'] }}</td>
                        <td style="font-family: monospace;">{{ $item['periodo'] }}</td>
                        <td>
                            <span style="font-weight: bold; color: #b91c1c;">{{ $item['atraso'] }} días</span>
                        </td>
                        <td>
                            <span class="status status-{{ strtolower($item['estado']) }}">{{ $item['estado'] }}</span>
                        </td>
                        <td style="text-align: right; font-weight: bold; font-family: monospace; color: #b91c1c;">{{ $currency }} {{ number_format($item['monto'], 2) }}</td>
                    </tr>
                @elseif($reportType === 'ocupacion')
                    <tr>
                        <td style="font-weight: bold;">Cuarto {{ $item['cuarto'] }}</td>
                        <td>{{ $item['piso'] }}</td>
                        <td>{{ $item['capacidad'] }} Personas</td>
                        <td style="font-family: monospace;">{{ $currency }} {{ number_format($item['precio'], 2) }}</td>
                        <td>
                            <span class="status status-{{ strtolower($item['estado']) }}">{{ $item['estado'] }}</span>
                        </td>
                        <td style="font-weight: bold;">{{ $item['inquilino'] }}</td>
                        <td>{{ $item['fecha_entrada'] }}</td>
                    </tr>
                @elseif($reportType === 'contratos')
                    <tr>
                        <td style="font-weight: bold; color: #1e293b;">{{ $item['inquilino'] }}</td>
                        <td>Cuarto {{ $item['cuarto'] }}</td>
                        <td>{{ $item['entrada'] }}</td>
                        <td>{{ $item['salida'] }}</td>
                        <td>
                            <span class="status status-{{ strtolower($item['estado']) }}">{{ $item['estado'] }}</span>
                        </td>
                        <td style="text-align: right; font-weight: bold; font-family: monospace;">{{ $currency }} {{ number_format($item['total'], 2) }}</td>
                    </tr>
                @elseif($reportType === 'pagos')
                    <tr>
                        <td style="font-family: monospace; font-weight: bold;">{{ $item['recibo'] }}</td>
                        <td style="color: #1e293b; font-weight: bold;">{{ $item['inquilino'] }}</td>
                        <td>Cuarto {{ $item['cuarto'] }}</td>
                        <td style="font-family: monospace;">{{ $item['periodo'] }}</td>
                        <td>{{ $item['fecha'] }}</td>
                        <td>{{ $item['metodo'] }}</td>
                        <td>
                            <span class="status status-{{ strtolower($item['estado']) }}">{{ $item['estado'] }}</span>
                        </td>
                        <td style="text-align: right; font-weight: bold; font-family: monospace;">{{ $currency }} {{ number_format($item['monto'], 2) }}</td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #94a3b8; padding: 20px;">
                        No se encontraron registros bajo los filtros aplicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento oficial generado por el Módulo de Reportes de AlquiRent. Página 1
    </div>
</body>
</html>
