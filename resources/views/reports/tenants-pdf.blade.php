<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
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
            font-size: 18px;
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
            font-size: 10px;
            text-transform: uppercase;
        }
        .status {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-inactive {
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
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="title">{{ $title }}</div>
                    <div style="font-size: 10px; color: #64748b; margin-top: 5px;">{{ \App\Models\Setting::get('company_name', 'Alquileres El Sol') }}</div>
                </td>
                <td class="info">
                    <div>Fecha: {{ $date }}</div>
                    <div>Registros: {{ count($tenants) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Doc. Identidad</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Estado</th>
                <th>F. Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tenants as $tenant)
                <tr>
                    <td style="font-weight: bold;">{{ $tenant->id }}</td>
                    <td style="font-weight: bold; color: #1e293b;">{{ $tenant->name }}</td>
                    <td>{{ $tenant->identity_number }}</td>
                    <td>{{ $tenant->phone }}</td>
                    <td>{{ $tenant->email ?: 'N/A' }}</td>
                    <td>
                        <span class="status {{ $tenant->status === 'Activo' ? 'status-active' : 'status-inactive' }}">
                            {{ $tenant->status }}
                        </span>
                    </td>
                    <td>{{ $tenant->created_at ? $tenant->created_at->format('d/m/Y') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por Sistema de Gestión de Alquileres. Página 1
    </div>
</body>
</html>
