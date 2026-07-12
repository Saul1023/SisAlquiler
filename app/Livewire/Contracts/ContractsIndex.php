<?php

namespace App\Livewire\Contracts;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contract;
use App\Models\Room;
use App\Models\Payment;
use Carbon\Carbon;

class ContractsIndex extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $statusFilter = 'Activo';
    public $perPage = 10;

    // Sorting
    public $sortField = 'start_date';
    public $sortAsc = false;

    // Edit modal state
    public $isEditModalOpen = false;
    public $contract_id = null;
    public $notes = '';

    // Finalize modal state
    public $isFinalizeOpen = false;
    public $checkout_date = '';
    public $prorated_amount = 0;
    public $days_occupied = 0;
    
    // Listeners for delete confirmations
    protected $listeners = [
        'cancelContract' => 'cancelContract'
    ];

    public function mount()
    {
        $this->checkout_date = Carbon::today()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }
        $this->sortField = $field;
    }

    // Edit Notes Modal
    public function editNotes($id)
    {
        $contract = Contract::findOrFail($id);
        $this->contract_id = $contract->id;
        $this->notes = $contract->notes;
        $this->isEditModalOpen = true;
    }

    public function saveNotes()
    {
        $contract = Contract::findOrFail($this->contract_id);
        $contract->notes = $this->notes;
        $contract->save();
        
        $this->isEditModalOpen = false;
        $this->dispatch('swal:toast', type: 'success', message: 'Notas del contrato actualizadas.');
    }

    // Finalize Contract Modal
    public function openFinalizeModal($id)
    {
        $this->contract_id = $id;
        $this->checkout_date = Carbon::today()->format('Y-m-d');
        $this->calculateProration();
        $this->isFinalizeOpen = true;
    }

    public function updatedCheckoutDate()
    {
        $this->calculateProration();
    }

    public function calculateProration()
    {
        if (!$this->contract_id || !$this->checkout_date) return;
        
        $contract = Contract::findOrFail($this->contract_id);
        $checkoutDate = Carbon::parse($this->checkout_date);
        $startDate = Carbon::parse($contract->start_date);

        if ($checkoutDate->isBefore($startDate)) {
            $this->prorated_amount = 0;
            $this->days_occupied = 0;
            return;
        }

        // Calculate days since the last monthly billing cycle date
        $cycleDay = min($contract->payment_day, 28);
        $cycleDate = Carbon::create($checkoutDate->year, $checkoutDate->month, $cycleDay);
        
        if ($cycleDate->isAfter($checkoutDate)) {
            // The billing period started in the previous month
            $cycleDate = $cycleDate->subMonth();
        }

        // If checkout is on the same day as the start of the cycle, days = 0, but they occupied that day.
        $this->days_occupied = $checkoutDate->diffInDays($cycleDate);
        if ($this->days_occupied === 0) {
            $this->days_occupied = 1; // Minimum 1 day
        }

        // Calculate prorated price based on monthly rate
        // (Monthly rate / 30) * Days
        $dailyRate = $contract->total_price / 30;
        $this->prorated_amount = round($dailyRate * $this->days_occupied, 2);
    }

    public function finalizeContract()
    {
        $this->validate([
            'checkout_date' => 'required|date',
        ]);

        $contract = Contract::findOrFail($this->contract_id);
        $checkoutDate = Carbon::parse($this->checkout_date);
        $startDate = Carbon::parse($contract->start_date);

        if ($checkoutDate->isBefore($startDate)) {
            $this->addError('checkout_date', 'La fecha de check-out no puede ser anterior a la fecha de entrada.');
            return;
        }

        // 1. Update contract
        $contract->end_date = $checkoutDate;
        $contract->status = 'Finalizado';
        $contract->save();

        // 2. Generate prorated final checkout payment if amount > 0
        if ($this->prorated_amount > 0) {
            Payment::create([
                'contract_id' => $contract->id,
                'amount' => $this->prorated_amount,
                'payment_date' => $checkoutDate,
                'payment_method' => 'Efectivo',
                'period_covered' => $checkoutDate->format('Y-m') . '-Prorrateo',
                'status' => 'Pendiente', // Registrar como pendiente para cobro en check-out
                'notes' => 'Monto proporcional de check-out por ' . $this->days_occupied . ' días de ocupación en este ciclo.'
            ]);
        }

        $this->isFinalizeOpen = false;
        $this->dispatch('swal:toast', type: 'success', message: 'Contrato finalizado y habitación liberada.');
    }

    // Cancel Contract
    public function confirmCancel($id)
    {
        $contract = Contract::findOrFail($id);
        $this->dispatch('swal:confirm', 
            id: $id, 
            action: 'cancelContract', 
            title: '¿Anular contrato?', 
            message: "Esta acción cancelará inmediatamente el contrato del Cuarto {$contract->room->room_number} sin generar cargos proporcionales. La habitación quedará disponible.",
            confirmText: 'Sí, anular'
        );
    }

    public function cancelContract($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->status = 'Cancelado';
        $contract->save();
        $this->dispatch('swal:toast', type: 'success', message: 'Contrato anulado correctamente.');
    }

    public function render()
    {
        $query = Contract::with(['room', 'tenant']);

        // Search filter (Tenant name or Room number)
        if (strlen($this->search) > 0) {
            $query->where(function($q) {
                $q->whereHas('tenant', function($tQuery) {
                    $tQuery->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('room', function($rQuery) {
                    $rQuery->where('room_number', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Status filter
        if ($this->statusFilter !== 'Todos') {
            $query->where('status', $this->statusFilter);
        }

        // Sorting & Pagination
        $contracts = $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.contracts.contracts-index', [
            'contracts' => $contracts
        ])->layout('layouts.app');
    }
}
