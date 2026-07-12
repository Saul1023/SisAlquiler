<?php

namespace App\Exports;

use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TenantsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Tenant::all();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nombre Completo',
            'Documento de Identidad',
            'Teléfono',
            'Correo Electrónico',
            'Estado',
            'Fecha de Registro',
        ];
    }

    /**
     * @param mixed $tenant
     * @return array
     */
    public function map($tenant): array
    {
        return [
            $tenant->id,
            $tenant->name,
            $tenant->identity_number,
            $tenant->phone,
            $tenant->email ?: 'N/A',
            $tenant->status,
            $tenant->created_at ? $tenant->created_at->format('d/m/Y') : 'N/A',
        ];
    }
}
