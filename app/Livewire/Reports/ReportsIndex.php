<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use App\Exports\DynamicReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportsIndex extends Component
{
    // Filters
    public $reportType = 'ingresos'; // 'ingresos', 'morosidad', 'ocupacion', 'contratos', 'pagos'
    public $dateFrom = '';
    public $dateTo = '';
    public $tenantId = '';
    public $roomId = '';
    public $paymentStatus = 'Todos';

    public function mount()
    {
        $this->dateFrom = Carbon::today()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::today()->endOfMonth()->format('Y-m-d');
    }

    // Reset filters when report type changes
    public function updatedReportType()
    {
        if ($this->reportType === 'morosidad' || $this->reportType === 'ocupacion') {
            $this->dateFrom = '';
            $this->dateTo = '';
        } else {
            $this->dateFrom = Carbon::today()->startOfMonth()->format('Y-m-d');
            $this->dateTo = Carbon::today()->endOfMonth()->format('Y-m-d');
        }
        $this->tenantId = '';
        $this->roomId = '';
        $this->paymentStatus = 'Todos';
    }

    public function getReportData()
    {
        $data = [];
        $query = null;

        switch ($this->reportType) {
            case 'ingresos':
                // Sum of paid amounts in period, grouped by month
                $query = Payment::where('status', 'Pagado');
                if ($this->dateFrom) $query->where('payment_date', '>=', $this->dateFrom);
                if ($this->dateTo) $query->where('payment_date', '<=', $this->dateTo);
                if ($this->roomId) {
                    $query->whereHas('contract', function($c) {
                        $c->where('room_id', $this->roomId);
                    });
                }
                
                $payments = $query->orderBy('payment_date', 'asc')->get();
                $grouped = $payments->groupBy(function($val) {
                    return Carbon::parse($val->payment_date)->format('Y-m');
                });

                foreach ($grouped as $month => $items) {
                    $data[] = [
                        'periodo' => $month,
                        'transacciones' => $items->count(),
                        'total' => $items->sum('amount')
                    ];
                }
                break;

            case 'morosidad':
                // Unpaid payments (Atrasado/Pendiente)
                $query = Payment::with(['contract.room', 'contract.tenant'])
                    ->whereIn('status', ['Atrasado', 'Pendiente']);
                
                if ($this->tenantId) {
                    $query->whereHas('contract', function($c) {
                        $c->where('tenant_id', $this->tenantId);
                    });
                }
                if ($this->roomId) {
                    $query->whereHas('contract', function($c) {
                        $c->where('room_id', $this->roomId);
                    });
                }

                $payments = $query->orderBy('payment_date', 'asc')->get();
                foreach ($payments as $p) {
                    $data[] = [
                        'inquilino' => $p->contract->tenant->name ?? 'N/A',
                        'cuarto' => $p->contract->room->room_number ?? 'N/A',
                        'periodo' => $p->period_covered,
                        'monto' => $p->amount,
                        'atraso' => $p->overdue_days,
                        'estado' => $p->status
                    ];
                }
                break;

            case 'ocupacion':
                // List of rooms with their occupancy details
                $rooms = Room::with('contracts.tenant')->get();
                foreach ($rooms as $r) {
                    $activeContract = $r->contracts->where('status', 'Activo')->first();
                    $data[] = [
                        'cuarto' => $r->room_number,
                        'piso' => $r->floor,
                        'capacidad' => $r->capacity,
                        'precio' => $r->price,
                        'estado' => $r->status,
                        'inquilino' => $activeContract ? ($activeContract->tenant->name ?? 'N/A') : 'N/A',
                        'fecha_entrada' => $activeContract ? $activeContract->start_date->format('d/m/Y') : '-'
                    ];
                }
                break;

            case 'contratos':
                // List of contracts in period
                $query = Contract::with(['room', 'tenant']);
                if ($this->dateFrom) $query->where('start_date', '>=', $this->dateFrom);
                if ($this->dateTo) $query->where('start_date', '<=', $this->dateTo);
                if ($this->tenantId) $query->where('tenant_id', $this->tenantId);
                if ($this->roomId) $query->where('room_id', $this->roomId);
                
                $contracts = $query->orderBy('start_date', 'desc')->get();
                foreach ($contracts as $c) {
                    $data[] = [
                        'inquilino' => $c->tenant->name ?? 'N/A',
                        'cuarto' => $c->room->room_number ?? 'N/A',
                        'entrada' => $c->start_date->format('d/m/Y'),
                        'salida' => $c->end_date ? $c->end_date->format('d/m/Y') : 'Indefinido',
                        'total' => $c->total_price,
                        'estado' => $c->status
                    ];
                }
                break;

            case 'pagos':
                // Detailed list of payments
                $query = Payment::with(['contract.room', 'contract.tenant']);
                if ($this->dateFrom) $query->where('payment_date', '>=', $this->dateFrom);
                if ($this->dateTo) $query->where('payment_date', '<=', $this->dateTo);
                if ($this->tenantId) {
                    $query->whereHas('contract', function($c) {
                        $c->where('tenant_id', $this->tenantId);
                    });
                }
                if ($this->roomId) {
                    $query->whereHas('contract', function($c) {
                        $c->where('room_id', $this->roomId);
                    });
                }
                if ($this->paymentStatus !== 'Todos') {
                    $query->where('status', $this->paymentStatus);
                }

                $payments = $query->orderBy('payment_date', 'desc')->get();
                foreach ($payments as $p) {
                    $data[] = [
                        'recibo' => $p->receipt_number ?: str_pad($p->id, 6, '0', STR_PAD_LEFT),
                        'inquilino' => $p->contract->tenant->name ?? 'N/A',
                        'cuarto' => $p->contract->room->room_number ?? 'N/A',
                        'periodo' => $p->period_covered,
                        'fecha' => $p->payment_date->format('d/m/Y'),
                        'metodo' => $p->payment_method,
                        'monto' => $p->amount,
                        'estado' => $p->status
                    ];
                }
                break;
        }

        return $data;
    }

    public function exportExcel()
    {
        $data = $this->getReportData();
        $headings = [];
        $exportData = [];

        switch ($this->reportType) {
            case 'ingresos':
                $headings = ['Periodo', 'Cant. Transacciones', 'Total Recibido (Bs.)'];
                foreach ($data as $item) {
                    $exportData[] = [$item['periodo'], $item['transacciones'], $item['total']];
                }
                break;
            case 'morosidad':
                $headings = ['Inquilino', 'Habitación', 'Periodo Cubierto', 'Monto Deuda (Bs.)', 'Días Atraso', 'Estado'];
                foreach ($data as $item) {
                    $exportData[] = [$item['inquilino'], $item['cuarto'], $item['periodo'], $item['monto'], $item['atraso'], $item['estado']];
                }
                break;
            case 'ocupacion':
                $headings = ['Habitación', 'Piso/Ubicación', 'Capacidad', 'Precio Base', 'Estado Actual', 'Inquilino Actual', 'Fecha Entrada'];
                foreach ($data as $item) {
                    $exportData[] = [$item['cuarto'], $item['piso'], $item['capacidad'], $item['precio'], $item['estado'], $item['inquilino'], $item['fecha_entrada']];
                }
                break;
            case 'contratos':
                $headings = ['Inquilino', 'Habitación', 'Fecha Entrada', 'Fecha Salida', 'Monto Mensual (Bs.)', 'Estado'];
                foreach ($data as $item) {
                    $exportData[] = [$item['inquilino'], $item['cuarto'], $item['entrada'], $item['salida'], $item['total'], $item['estado']];
                }
                break;
            case 'pagos':
                $headings = ['Comprobante', 'Inquilino', 'Habitación', 'Periodo', 'Fecha Pago', 'Método Pago', 'Monto Pagado (Bs.)', 'Estado'];
                foreach ($data as $item) {
                    $exportData[] = [$item['recibo'], $item['inquilino'], $item['cuarto'], $item['periodo'], $item['fecha'], $item['metodo'], $item['monto'], $item['estado']];
                }
                break;
        }

        return Excel::download(
            new DynamicReportExport($headings, $exportData), 
            'reporte_' . $this->reportType . '_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('reports.general-pdf', [
            'data' => $data,
            'reportType' => $this->reportType,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'currency' => Setting::get('currency', 'Bs.'),
            'company_name' => Setting::get('company_name', 'Alquileres El Sol'),
            'date' => now()->format('d/m/Y H:i')
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_' . $this->reportType . '_' . now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $previewData = $this->getReportData();

        return view('livewire.reports.reports-index', [
            'previewData' => $previewData,
            'tenants' => Tenant::orderBy('name')->get(),
            'rooms' => Room::orderBy('room_number')->get(),
        ])->layout('layouts.app');
    }
}
