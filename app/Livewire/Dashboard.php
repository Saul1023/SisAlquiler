<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Dashboard extends Component
{
    // Dynamic polling data or statistics
    public function getStats()
    {
        $today = Carbon::today();
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'Ocupado')->count();
        $maintRooms = Room::where('status', 'Mantenimiento')->count();
        $availableRooms = Room::where('status', 'Disponible')->count();
        
        $occupancyRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
        
        $activeContractsCount = Contract::where('status', 'Activo')->count();

        // Revenue of current month
        $currentMonthRevenue = Payment::where('status', 'Pagado')
            ->whereMonth('payment_date', $today->month)
            ->whereYear('payment_date', $today->year)
            ->sum('amount');

        // Revenue of previous month
        $prevMonth = $today->copy()->subMonth();
        $prevMonthRevenue = Payment::where('status', 'Pagado')
            ->whereMonth('payment_date', $prevMonth->month)
            ->whereYear('payment_date', $prevMonth->year)
            ->sum('amount');

        // Delinquent/moroso tenants (unpaid/atrasado payments)
        $delinquentCount = Payment::where('status', 'Atrasado')->distinct('contract_id')->count('contract_id');

        // Overdue payments table data
        $overduePayments = Payment::with(['contract.room', 'contract.tenant'])
            ->where('status', 'Atrasado')
            ->orderBy('payment_date', 'asc')
            ->limit(5)
            ->get();

        // Contracts expiring soon (next 30 days)
        $expiringContracts = Contract::with(['room', 'tenant'])
            ->where('status', 'Activo')
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(30)])
            ->orderBy('end_date', 'asc')
            ->limit(5)
            ->get();

        // Recent activity: last 5 payments and last 5 contracts combined and sorted
        $recentPayments = Payment::with(['contract.room', 'contract.tenant'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function($p) {
                return [
                    'type' => 'payment',
                    'title' => 'Pago Registrado',
                    'description' => "Se registró pago de $" . number_format($p->amount, 2) . " para el Cuarto " . ($p->contract->room->room_number ?? 'N/A') . " ({$p->period_covered})",
                    'date' => $p->created_at,
                    'date_human' => $p->created_at->diffForHumans(),
                    'icon' => 'fa-file-invoice-dollar text-emerald-500 bg-emerald-50 dark:bg-emerald-950/30'
                ];
            });

        $recentContracts = Contract::with(['room', 'tenant'])
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->map(function($c) {
                return [
                    'type' => 'contract',
                    'title' => 'Nuevo Contrato',
                    'description' => "Contrato creado para " . ($c->tenant->name ?? 'N/A') . " en Cuarto " . ($c->room->room_number ?? 'N/A'),
                    'date' => $c->created_at,
                    'date_human' => $c->created_at->diffForHumans(),
                    'icon' => 'fa-file-signature text-blue-500 bg-blue-50 dark:bg-blue-950/30'
                ];
            });

        $recentActivities = $recentPayments->concat($recentContracts)
            ->sortByDesc('date')
            ->take(5)
            ->values()
            ->toArray();

        return [
            'totalRooms' => $totalRooms,
            'occupiedRooms' => $occupiedRooms,
            'availableRooms' => $availableRooms,
            'maintRooms' => $maintRooms,
            'occupancyRate' => $occupancyRate,
            'activeContractsCount' => $activeContractsCount,
            'currentMonthRevenue' => $currentMonthRevenue,
            'prevMonthRevenue' => $prevMonthRevenue,
            'delinquentCount' => $delinquentCount,
            'overduePayments' => $overduePayments,
            'expiringContracts' => $expiringContracts,
            'recentActivities' => $recentActivities,
        ];
    }

    public function getChartData()
    {
        $today = Carbon::today();
        
        // 1. Income by Month (Last 6 months)
        $incomeData = [];
        $incomeLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            $incomeLabels[] = $month->translatedFormat('M Y');
            
            $sum = Payment::where('status', 'Pagado')
                ->whereMonth('payment_date', $month->month)
                ->whereYear('payment_date', $month->year)
                ->sum('amount');
            $incomeData[] = (float)$sum;
        }

        // 2. Occupancy Monthly snapshot (Last 6 months)
        $occupancyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $today->copy()->subMonths($i);
            // Count contracts that were active during this month
            // We can approximate by checking contracts started on or before end of month and end_date null or after end of month
            $endOfMonth = $month->endOfMonth();
            $count = Contract::where('start_date', '<=', $endOfMonth)
                ->where(function($query) use ($endOfMonth) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', $endOfMonth);
                })
                ->where('status', '!=', 'Cancelado')
                ->count();
            $occupancyData[] = $count;
        }

        // 3. Payment Method Distribution
        $methods = Payment::where('status', 'Pagado')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('payment_method')
            ->get();
            
        $methodLabels = $methods->pluck('payment_method')->toArray();
        $methodCounts = $methods->pluck('count')->toArray();
        $methodTotals = $methods->pluck('total')->map(fn($t) => (float)$t)->toArray();

        // 4. Most profitable rooms (based on total paid amount)
        $profitableRooms = DB::table('payments')
            ->join('contracts', 'payments.contract_id', '=', 'contracts.id')
            ->join('rooms', 'contracts.room_id', '=', 'rooms.id')
            ->where('payments.status', 'Pagado')
            ->select('rooms.room_number', DB::raw('sum(payments.amount) as total'))
            ->groupBy('rooms.room_number')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        $roomLabels = $profitableRooms->pluck('room_number')->toArray();
        $roomTotals = $profitableRooms->pluck('total')->map(fn($t) => (float)$t)->toArray();

        return [
            'incomeLabels' => $incomeLabels,
            'incomeData' => $incomeData,
            'occupancyData' => $occupancyData,
            'methodLabels' => $methodLabels,
            'methodTotals' => $methodTotals,
            'roomLabels' => $roomLabels,
            'roomTotals' => $roomTotals,
        ];
    }

    public function render()
    {
        $stats = $this->getStats();
        $chartData = $this->getChartData();

        return view('livewire.dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
        ])->layout('layouts.app');
    }
}
