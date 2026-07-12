<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;

class NotificationHub extends Component
{
    public $filter = 'todos'; // 'todos', 'pagos', 'contratos'
    public $readHashes = [];

    public function mount()
    {
        $this->loadReadNotifications();
    }

    public function loadReadNotifications()
    {
        $stored = Setting::get('read_notifications', '[]');
        $this->readHashes = json_decode($stored, true) ?: [];
    }

    public function markAsRead($hash)
    {
        if (!in_array($hash, $this->readHashes)) {
            $this->readHashes[] = $hash;
            Setting::set('read_notifications', json_encode($this->readHashes));
        }
    }

    public function markAllAsRead()
    {
        $alerts = $this->getAlerts();
        foreach ($alerts as $alert) {
            if (!in_array($alert['hash'], $this->readHashes)) {
                $this->readHashes[] = $alert['hash'];
            }
        }
        Setting::set('read_notifications', json_encode($this->readHashes));
    }

    public function getAlerts()
    {
        $alerts = [];
        $today = Carbon::today();

        // 1. Contratos por vencer (dentro de los próximos 7 días)
        $expiringContracts = Contract::with(['room', 'tenant'])
            ->where('status', 'Activo')
            ->whereNotNull('end_date')
            ->get();

        foreach ($expiringContracts as $contract) {
            $endDate = Carbon::parse($contract->end_date);
            if ($endDate->isAfter($today) && $endDate->diffInDays($today) <= 7) {
                $days = $endDate->diffInDays($today);
                $hash = md5("contract_expiring_" . $contract->id . "_" . $contract->end_date->format('Y-m-d'));
                $alerts[] = [
                    'hash' => $hash,
                    'type' => 'contratos',
                    'sub_type' => 'contract_expiring',
                    'icon' => 'fa-calendar-day',
                    'color' => 'amber', // naranja/amarillo
                    'title' => 'Contrato por Vencer',
                    'message' => "El contrato del Cuarto {$contract->room->room_number} ({$contract->tenant->name}) vence en {$days} días.",
                    'date' => $contract->end_date->format('d/m/Y'),
                    'is_read' => in_array($hash, $this->readHashes),
                ];
            } elseif ($endDate->isBefore($today) || $endDate->isSameDay($today)) {
                // Contratos finalizados pero aún marcados como Activos
                $hash = md5("contract_finished_" . $contract->id . "_" . $contract->end_date->format('Y-m-d'));
                $alerts[] = [
                    'hash' => $hash,
                    'type' => 'contratos',
                    'sub_type' => 'contract_finished',
                    'icon' => 'fa-calendar-xmark',
                    'color' => 'slate', // gris
                    'title' => 'Contrato Concluido',
                    'message' => "El contrato del Cuarto {$contract->room->room_number} ({$contract->tenant->name}) concluyó el {$contract->end_date->format('d/m/Y')}.",
                    'date' => $contract->end_date->format('d/m/Y'),
                    'is_read' => in_array($hash, $this->readHashes),
                ];
            }
        }

        // 2. Pagos atrasados y próximos
        // Obtener todos los contratos activos
        $activeContracts = Contract::with(['room', 'tenant', 'payments'])->where('status', 'Activo')->get();

        foreach ($activeContracts as $contract) {
            // Revisar el mes actual y el anterior
            $monthsToCheck = [
                $today->format('Y-m'),
                $today->copy()->subMonth()->format('Y-m')
            ];

            foreach ($monthsToCheck as $period) {
                $hasPayment = $contract->payments->where('period_covered', $period)->where('status', 'Pagado')->first();
                
                if (!$hasPayment) {
                    // Calcular fecha de vencimiento para este periodo
                    $parts = explode('-', $period);
                    $year = intval($parts[0]);
                    $month = intval($parts[1]);
                    
                    // Día de pago según contrato (ajustado al maximo de dias del mes)
                    $daysInMonth = Carbon::create($year, $month)->daysInMonth;
                    $payDay = min($contract->payment_day, $daysInMonth);
                    $dueDate = Carbon::create($year, $month, $payDay);

                    if ($today->greaterThan($dueDate)) {
                        // Pago atrasado
                        $days = $today->diffInDays($dueDate);
                        $hash = md5("payment_overdue_" . $contract->id . "_" . $period);
                        $alerts[] = [
                            'hash' => $hash,
                            'type' => 'pagos',
                            'sub_type' => 'payment_overdue',
                            'icon' => 'fa-triangle-exclamation',
                            'color' => 'rose', // rojo
                            'title' => 'Pago Atrasado',
                            'message' => "El pago del Cuarto {$contract->room->room_number} de {$period} está atrasado por {$days} días.",
                            'date' => "Venció el " . $dueDate->format('d/m/Y'),
                            'is_read' => in_array($hash, $this->readHashes),
                        ];
                    } elseif ($dueDate->diffInDays($today) <= 3) {
                        // Pago próximo (dentro de 3 días)
                        $days = $dueDate->diffInDays($today);
                        $hash = md5("payment_upcoming_" . $contract->id . "_" . $period);
                        $alerts[] = [
                            'hash' => $hash,
                            'type' => 'pagos',
                            'sub_type' => 'payment_upcoming',
                            'icon' => 'fa-clock',
                            'color' => 'yellow', // amarillo
                            'title' => 'Pago Próximo',
                            'message' => "El pago del Cuarto {$contract->room->room_number} de {$period} vence en {$days} días.",
                            'date' => "Vence el " . $dueDate->format('d/m/Y'),
                            'is_read' => in_array($hash, $this->readHashes),
                        ];
                    }
                }
            }
        }

        // Pagos en estado 'Atrasado' o 'Pendiente' en la base de datos de forma explícita
        $dbUnpaidPayments = Payment::with(['contract.room', 'contract.tenant'])
            ->whereIn('status', ['Atrasado', 'Pendiente'])
            ->get();

        foreach ($dbUnpaidPayments as $payment) {
            if (!$payment->contract) continue;
            
            $hash = md5("payment_db_unpaid_" . $payment->id);
            $days = $payment->overdue_days;
            
            // Evitar duplicar alertas del loop anterior
            $duplicateHash = md5("payment_overdue_" . $payment->contract_id . "_" . $payment->period_covered);
            if (in_array($duplicateHash, array_column($alerts, 'hash'))) {
                continue;
            }

            $alerts[] = [
                'hash' => $hash,
                'type' => 'pagos',
                'sub_type' => 'payment_db_unpaid',
                'icon' => 'fa-circle-exclamation',
                'color' => $payment->status === 'Atrasado' ? 'rose' : 'amber',
                'title' => $payment->status === 'Atrasado' ? 'Pago Moroso' : 'Pago Pendiente',
                'message' => "Pago del Cuarto {$payment->contract->room->room_number} ({$payment->period_covered}) está marcado como {$payment->status} ($" . number_format($payment->amount, 2) . ").",
                'date' => $days > 0 ? "{$days} días de retraso" : 'Pendiente',
                'is_read' => in_array($hash, $this->readHashes),
            ];
        }

        // Filtrar por tipo si no es 'todos'
        if ($this->filter !== 'todos') {
            $alerts = array_filter($alerts, function($a) {
                return $a['type'] === $this->filter;
            });
        }

        // Ordenar: primero no leídos, luego por color (gravedad: rose -> amber -> yellow -> slate)
        usort($alerts, function($a, $b) {
            if ($a['is_read'] !== $b['is_read']) {
                return $a['is_read'] <=> $b['is_read']; // falses (no leídos) primero
            }
            
            $priority = ['rose' => 1, 'amber' => 2, 'yellow' => 3, 'slate' => 4];
            $pA = $priority[$a['color']] ?? 5;
            $pB = $priority[$b['color']] ?? 5;
            return $pA <=> $pB;
        });

        return $alerts;
    }

    public function render()
    {
        $alerts = $this->getAlerts();
        $unreadCount = count(array_filter($alerts, function($a) {
            return !$a['is_read'];
        }));

        return view('livewire.notification-hub', [
            'alerts' => $alerts,
            'unreadCount' => $unreadCount,
        ]);
    }
}
