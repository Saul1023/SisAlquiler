<?php

namespace App\Livewire\Tenants;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Tenant;
use App\Models\Contract;
use App\Exports\TenantsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TenantsIndex extends Component
{
    use WithPagination, WithFileUploads;

    // Search and filters
    public $search = '';
    public $statusFilter = 'Todos';
    public $perPage = 10;

    // Sorting
    public $sortField = 'name';
    public $sortAsc = true;

    // Form fields
    public $tenant_id = null;
    public $name = '';
    public $identity_number = '';
    public $phone = '';
    public $email = '';
    public $photo = null; // Uploaded photo
    public $existing_photo = null; // Display existing
    public $notes = '';
    public $status = 'Activo';

    // Modals state
    public $isModalOpen = false;
    
    // History Modal
    public $isHistoryOpen = false;
    public $selectedTenant = null;
    public $tenantHistory = [];

    // Listeners for delete confirmations
    protected $listeners = [
        'deleteTenant' => 'deleteTenant'
    ];

    public function mount()
    {
        // Capture global search query if present
        if (request()->has('search')) {
            $this->search = request()->get('search');
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
        $this->tenant_id = null;
        $this->name = '';
        $this->identity_number = '';
        $this->phone = '';
        $this->email = '';
        $this->photo = null;
        $this->existing_photo = null;
        $this->notes = '';
        $this->status = 'Activo';
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal();
    }

    public function edit($id)
    {
        $this->resetForm();
        $tenant = Tenant::findOrFail($id);
        $this->tenant_id = $tenant->id;
        $this->name = $tenant->name;
        $this->identity_number = $tenant->identity_number;
        $this->phone = $tenant->phone;
        $this->email = $tenant->email;
        $this->existing_photo = $tenant->photo_path;
        $this->notes = $tenant->notes;
        $this->status = $tenant->status;

        $this->openModal();
    }

    public function save()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'identity_number' => 'required|string|unique:tenants,identity_number,' . ($this->tenant_id ?? 'NULL') . ',id,deleted_at,NULL',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'photo' => 'nullable|image|max:2048', // 2MB max
            'notes' => 'nullable|string',
            'status' => 'required|in:Activo,Inactivo',
        ];

        $validatedData = $this->validate($rules);

        // Remove photo from validated data initially
        unset($validatedData['photo']);

        if ($this->photo) {
            // Store photo
            $path = $this->photo->store('tenants', 'public');
            $validatedData['photo_path'] = $path;

            // Delete old photo if it exists
            if ($this->existing_photo) {
                Storage::disk('public')->delete($this->existing_photo);
            }
        }

        if ($this->tenant_id) {
            $tenant = Tenant::findOrFail($this->tenant_id);
            $tenant->update($validatedData);
            $this->dispatch('swal:toast', type: 'success', message: 'Inquilino actualizado correctamente.');
        } else {
            Tenant::create($validatedData);
            $this->dispatch('swal:toast', type: 'success', message: 'Inquilino registrado correctamente.');
        }

        $this->closeModal();
    }

    public function showHistory($id)
    {
        $this->selectedTenant = Tenant::findOrFail($id);
        $this->tenantHistory = Contract::with(['room', 'payments'])
            ->where('tenant_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        $this->isHistoryOpen = true;
    }

    public function closeHistory()
    {
        $this->isHistoryOpen = false;
        $this->selectedTenant = null;
        $this->tenantHistory = [];
    }

    public function confirmDelete($id)
    {
        $tenant = Tenant::findOrFail($id);

        // Check if there is an active contract
        $hasActiveContract = Contract::where('tenant_id', $id)
            ->where('status', 'Activo')
            ->exists();

        if ($hasActiveContract) {
            $this->dispatch('swal:alert', type: 'error', title: 'Acción bloqueada', message: 'No se puede eliminar el inquilino porque tiene un contrato activo.');
            return;
        }

        $this->dispatch('swal:confirm', 
            id: $id, 
            action: 'deleteTenant', 
            title: '¿Eliminar inquilino?', 
            message: "Esta acción enviará a {$tenant->name} a la papelera. Se conservará su historial de pagos y contratos anteriores.",
            confirmText: 'Sí, eliminar'
        );
    }

    public function deleteTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $tenant->delete();
        $this->dispatch('swal:toast', type: 'success', message: 'Inquilino eliminado correctamente.');
    }

    // Export to Excel
    public function exportExcel()
    {
        return Excel::download(new TenantsExport, 'inquilinos_' . now()->format('Y-m-d') . '.xlsx');
    }

    // Export to PDF
    public function exportPdf()
    {
        $tenants = Tenant::where('status', $this->statusFilter !== 'Todos' ? $this->statusFilter : 'like', '%%')
            ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.tenants-pdf', [
            'tenants' => $tenants,
            'title' => 'Listado de Inquilinos',
            'date' => now()->format('d/m/Y H:i')
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'reporte_inquilinos_' . now()->format('Y-m-d') . '.pdf');
    }

    public function render()
    {
        $query = Tenant::query();

        // Search filter
        if (strlen($this->search) > 0) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('identity_number', 'like', '%' . $this->search . '%')
                  ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter !== 'Todos') {
            $query->where('status', $this->statusFilter);
        }

        // Sorting & Pagination
        $tenants = $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.tenants.tenants-index', [
            'tenants' => $tenants
        ])->layout('layouts.app');
    }
}
