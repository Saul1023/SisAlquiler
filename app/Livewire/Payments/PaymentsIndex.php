<?php

namespace App\Livewire\Payments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Setting;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentsIndex extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $statusFilter = 'Todos';
    public $methodFilter = 'Todos';
    public $perPage = 10;

    // Sorting
    public $sortField = 'payment_date';
    public $sortAsc = false;

    // Form fields (Modal Create/Edit)
    public $isModalOpen = false;
    public $payment_id = null;
    
    public $contract_id = '';
    public $amount = '';
    public $payment_date = '';
    public $payment_method = 'Efectivo';
    public $period_covered = '';
    public $receipt_number = '';
    public $status = 'Pagado';
    public $notes = '';

    // Suggestions & calculations (reactive)
    public $tenantDebt = 0;
    public $suggestedAmount = 0;
    public $nextPeriodSuggested = '';

    protected $listeners = [
        'deletePayment' => 'deletePayment'
    ];

    public function mount()
    {
        $this->payment_date = Carbon::today()->format('Y-m-d');
        
        if (request()->has('contract_id')) {
            $this->contract_id = request()->get('contract_id');
            $this->openModal();
            $this->updatedContractId();
        }
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

    public function openModal()
    {
        $this->isModalOpen = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->payment_id = null;
        $this->contract_id = '';
        $this->amount = '';
        $this->payment_date = Carbon::today()->format('Y-m-d');
        $this->payment_method = 'Efectivo';
        $this->period_covered = '';
        $this->receipt_number = '';
        $this->status = 'Pagado';
        $this->notes = '';
        
        $this->tenantDebt = 0;
        $this->suggestedAmount = 0;
        $this->nextPeriodSuggested = '';
    }

    // Reactive suggestion when a contract is selected
    public function updatedContractId()
    {
        if (!$this->contract_id) {
            $this->resetSuggestions();
            return;
        }

        $contract = Contract::with(['payments', 'tenant'])->find($this->contract_id);
        if (!$contract) {
            $this->resetSuggestions();
            return;
        }

        // 1. Suggest price
        $this->suggestedAmount = (float)$contract->total_price;
        $this->amount = $this->suggestedAmount;

        // 2. Calculate current unpaid debt for the tenant
        // Sum of all unpaid payments
        $this->tenantDebt = Payment::where('contract_id', $contract->id)
            ->whereIn('status', ['Atrasado', 'Pendiente'])
            ->sum('amount');

        // 3. Suggest next payment period YYYY-MM
        $lastPayment = Payment::where('contract_id', $contract->id)
            ->where('status', 'Pagado')
            ->orderBy('period_covered', 'desc')
            ->first();

        if ($lastPayment) {
            // Find next month covered
            try {
                $parts = explode('-', $lastPayment->period_covered);
                if (count($parts) === 2) {
                    $date = Carbon::create((int)$parts[0], (int)$parts[1], 1)->addMonth();
                    $this->nextPeriodSuggested = $date->format('Y-m');
                } else {
                    $this->nextPeriodSuggested = Carbon::today()->format('Y-m');
                }
            } catch (\Exception $e) {
                $this->nextPeriodSuggested = Carbon::today()->format('Y-m');
            }
        } else {
            // No payment yet, suggest contract start date month
            $this->nextPeriodSuggested = Carbon::parse($contract->start_date)->format('Y-m');
        }

        $this->period_covered = $this->nextPeriodSuggested;
    }

    private function resetSuggestions()
    {
        $this->tenantDebt = 0;
        $this->suggestedAmount = 0;
        $this->nextPeriodSuggested = '';
        $this->amount = '';
        $this->period_covered = '';
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal();
    }

    public function edit($id)
    {
        $this->resetForm();
        $payment = Payment::findOrFail($id);
        $this->payment_id = $payment->id;
        $this->contract_id = $payment->contract_id;
        $this->amount = $payment->amount;
        $this->payment_date = $payment->payment_date->format('Y-m-d');
        $this->payment_method = $payment->payment_method;
        $this->period_covered = $payment->period_covered;
        $this->receipt_number = $payment->receipt_number;
        $this->status = $payment->status;
        $this->notes = $payment->notes;

        $this->updatedContractId();
        $this->openModal();
    }

    public function save()
    {
        $rules = [
            'contract_id' => 'required|exists:contracts,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'period_covered' => 'required|string', // format YYYY-MM
            'receipt_number' => 'nullable|string|max:100',
            'status' => 'required|in:Pagado,Pendiente,Atrasado,Anulado',
            'notes' => 'nullable|string',
        ];

        $validatedData = $this->validate($rules);

        if ($this->payment_id) {
            $payment = Payment::findOrFail($this->payment_id);
            $payment->update($validatedData);
            $this->dispatch('swal:toast', type: 'success', message: 'Pago actualizado correctamente.');
        } else {
            // Check if there is an existing pending/atrasado payment for this period to update it instead of inserting a new one
            $existingUnpaid = Payment::where('contract_id', $this->contract_id)
                ->where('period_covered', $this->period_covered)
                ->whereIn('status', ['Atrasado', 'Pendiente'])
                ->first();

            if ($existingUnpaid && $this->status === 'Pagado') {
                $existingUnpaid->update($validatedData);
            } else {
                Payment::create($validatedData);
            }
            $this->dispatch('swal:toast', type: 'success', message: 'Pago registrado correctamente.');
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $payment = Payment::findOrFail($id);
        $this->dispatch('swal:confirm', 
            id: $id, 
            action: 'deletePayment', 
            title: '¿Eliminar pago?', 
            message: "Esta acción eliminará el registro de pago de $" . number_format($payment->amount, 2) . " para el periodo {$payment->period_covered}.",
            confirmText: 'Sí, eliminar'
        );
    }

    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        $this->dispatch('swal:toast', type: 'success', message: 'Registro de pago eliminado.');
    }

    // Stream download PDF receipt
    public function downloadReceipt($id)
    {
        $payment = Payment::with(['contract.room', 'contract.tenant'])->findOrFail($id);

        $pdf = Pdf::loadView('reports.receipt-pdf', [
            'payment' => $payment,
            'company_name' => Setting::get('company_name', 'Alquileres El Sol'),
            'company_address' => Setting::get('company_address', 'Av. Principal #123'),
            'company_phone' => Setting::get('company_phone', '78945612'),
            'currency' => Setting::get('currency', 'Bs.'),
            'date' => now()->format('d/m/Y H:i')
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'recibo_' . ($payment->receipt_number ?: $payment->id) . '.pdf');
    }

    public function render()
    {
        $query = Payment::with(['contract.room', 'contract.tenant']);

        // Search filter (Tenant name or Room number)
        if (strlen($this->search) > 0) {
            $query->where(function($q) {
                $q->whereHas('contract.tenant', function($t) {
                    $t->where('name', 'like', '%' . $this->search . '%');
                })->orWhereHas('contract.room', function($r) {
                    $r->where('room_number', 'like', '%' . $this->search . '%');
                })->orWhere('receipt_number', 'like', '%' . $this->search . '%');
            });
        }

        // Status Filter
        if ($this->statusFilter !== 'Todos') {
            $query->where('status', $this->statusFilter);
        }

        // Method Filter
        if ($this->methodFilter !== 'Todos') {
            $query->where('payment_method', $this->methodFilter);
        }

        // Pagination & Sorting
        $payments = $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        // Fetch active contracts for form dropdown selection
        $activeContracts = Contract::with(['room', 'tenant'])
            ->where('status', 'Activo')
            ->get();

        return view('livewire.payments.payments-index', [
            'payments' => $payments,
            'activeContracts' => $activeContracts,
        ])->layout('layouts.app');
    }
}
