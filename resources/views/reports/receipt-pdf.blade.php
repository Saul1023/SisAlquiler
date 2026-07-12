<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo de Pago {{ $payment->receipt_number ?: $payment->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #334155;
            margin: 0;
            padding: 20px;
        }
        .receipt-box {
            border: 2px solid #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            border-bottom: 1px dashed #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .header table {
            width: 100%;
        }
        .company-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e293b;
        }
        .company-subtitle {
            font-size: 9px;
            color: #64748b;
            margin-top: 3px;
        }
        .receipt-title {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            color: #3b82f6;
        }
        .receipt-num {
            text-align: right;
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
        }
        .details-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .details-table td {
            padding: 4px 0;
        }
        .details-label {
            color: #64748b;
            width: 90px;
        }
        .details-value {
            font-weight: bold;
            color: #334155;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .items-table th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }
        .items-table td {
            border-bottom: 1px solid #f1f5f9;
            padding: 8px;
            text-align: left;
        }
        .totals-table {
            width: 100%;
            margin-top: 15px;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
        }
        .totals-table td {
            padding: 4px 0;
        }
        .total-amount {
            font-size: 14px;
            font-weight: bold;
            color: #3b82f6;
            text-align: right;
        }
        .signature-section {
            margin-top: 40px;
            text-align: center;
        }
        .signature-line {
            width: 150px;
            border-bottom: 1px solid #94a3b8;
            margin: 0 auto 5px auto;
        }
        .signature-text {
            font-size: 9px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="receipt-box">
        <!-- Receipt Header -->
        <div class="header">
            <table>
                <tr>
                    <td>
                        <div class="company-title">{{ $company_name }}</div>
                        <div class="company-subtitle">{{ $company_address }}</div>
                        <div class="company-subtitle">Teléfono: {{ $company_phone }}</div>
                    </td>
                    <td>
                        <div class="receipt-title">RECIBO DE PAGO</div>
                        <div class="receipt-num">Nro: <b>{{ $payment->receipt_number ?: str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</b></div>
                        <div class="receipt-num">Fecha Emisión: {{ $payment->payment_date->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Tenant and Contract Details -->
        <table class="details-table">
            <tr>
                <td class="details-label">Inquilino:</td>
                <td class="details-value">{{ $payment->contract->tenant->name ?? 'N/A' }}</td>
                <td class="details-label">CI/DNI:</td>
                <td class="details-value">{{ $payment->contract->tenant->identity_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="details-label">Habitación:</td>
                <td class="details-value">Cuarto {{ $payment->contract->room->room_number ?? 'N/A' }}</td>
                <td class="details-label">Método Pago:</td>
                <td class="details-value">{{ $payment->payment_method }}</td>
            </tr>
            <tr>
                <td class="details-label">Periodo Cubierto:</td>
                <td class="details-value" style="font-family: monospace;">{{ $payment->period_covered }}</td>
                <td class="details-label">Estado Pago:</td>
                <td class="details-value" style="text-transform: uppercase; color: #10b981;">{{ $payment->status }}</td>
            </tr>
        </table>

        <!-- Receipt breakdown Items -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Concepto / Descripción</th>
                    <th style="text-align: right; width: 100px;">Importe</th>
                </tr>
            </thead>
            <tbody>
                <!-- Room base price -->
                <tr>
                    <td>Alquiler base correspondiente al Cuarto {{ $payment->contract->room->room_number ?? 'N/A' }}</td>
                    <td style="text-align: right; font-family: monospace;">{{ $currency }} {{ number_format($payment->contract->base_price ?? $payment->amount, 2) }}</td>
                </tr>
                
                <!-- Additional services if payment is standard contract amount -->
                @if($payment->contract && abs($payment->amount - $payment->contract->total_price) < 0.05)
                    @if($payment->contract->services['wifi'] ?? false)
                        <tr>
                            <td>Servicios adicionales: Conexión WiFi</td>
                            <td style="text-align: right; font-family: monospace;">{{ $currency }} {{ number_format(Setting::get('wifi_price', 50.00), 2) }}</td>
                        </tr>
                    @endif
                    @if($payment->contract->services['parking'] ?? false)
                        <tr>
                            <td>Servicios adicionales: Espacio de Estacionamiento</td>
                            <td style="text-align: right; font-family: monospace;">{{ $currency }} {{ number_format(Setting::get('parking_price', 100.00), 2) }}</td>
                        </tr>
                    @endif
                    @if($payment->contract->services['cleaning'] ?? false)
                        <tr>
                            <td>Servicios adicionales: Servicio de Limpieza semanal</td>
                            <td style="text-align: right; font-family: monospace;">{{ $currency }} {{ number_format(Setting::get('cleaning_price', 80.00), 2) }}</td>
                        </tr>
                    @endif
                    @if($payment->contract->services['water_light'] ?? false)
                        <tr>
                            <td>Servicios adicionales: Consumo de Agua y Luz</td>
                            <td style="text-align: right; font-family: monospace;">{{ $currency }} {{ number_format(Setting::get('water_light_price', 70.00), 2) }}</td>
                        </tr>
                    @endif
                @elseif($payment->notes)
                    <!-- Display notes if it is a special/prorated amount -->
                    <tr>
                        <td style="color: #64748b; font-style: italic;">Obs: {{ $payment->notes }}</td>
                        <td style="text-align: right; font-family: monospace;">-</td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Totals -->
        <table class="totals-table">
            <tr>
                <td style="color: #64748b; font-weight: bold;">TOTAL PAGADO:</td>
                <td class="total-amount">{{ $currency }} {{ number_format($payment->amount, 2) }}</td>
            </tr>
        </table>

        <!-- Signature section -->
        <div class="signature-section">
            <div class="signature-line"></div>
            <div class="signature-text">Recibido Conforme (Administrador)</div>
            <div class="signature-text" style="font-size: 8px; margin-top: 10px;">Comprobante de uso interno y control de alquileres. Generado el {{ $date }}</div>
        </div>
    </div>
</body>
</html>
