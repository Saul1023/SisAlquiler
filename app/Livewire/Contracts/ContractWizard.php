<?php

namespace App\Livewire\Contracts;

use Livewire\Component;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Setting;
use Carbon\Carbon;

class ContractWizard extends Component
{
    // Wizard step state
    public $step = 1;

    // Step 1: Select Room
    public $selectedRoomId = null;
    public $roomCapacityFilter = '';
    public $roomPriceFilter = '';
    
    // Step 2: Select Tenant
    public $selectedTenantId = null;
    public $tenantSearch = '';
    
    // Quick Tenant Form inside Step 2
    public $showQuickTenantModal = false;
    public $quickName = '';
    public $quickIdentityNumber = '';
    public $quickPhone = '';
    public $quickEmail = '';
    public $quickNotes = '';

    // Step 3: Setup Contract
    public $start_date = '';
    public $end_date = '';
    public $payment_frequency = 'Mensual';
    public $payment_day = 5;
    
    // Services checkboxes
    public $services = [
        'wifi' => false,
        'parking' => false,
        'cleaning' => false,
        'water_light' => false,
    ];
    
    public $wifi_price = 50.00;
    public $parking_price = 50.00;
    public $cleaning_price = 80.00;
    public $water_light_price = 70.00;

    public $base_price = 0;
    public $additional_services_price = 0;
    public $total_price = 0;
    public $notes = '';

    public function mount()
    {
        $this->start_date = Carbon::today()->format('Y-m-d');
        
        // Load service prices from configuration
        $this->wifi_price = (float)Setting::get('wifi_price', 50.00);
        $this->parking_price = (float)Setting::get('parking_price', 50.00);
        $this->cleaning_price = (float)Setting::get('cleaning_price', 80.00);
        $this->water_light_price = (float)Setting::get('water_light_price', 70.00);
    }

    // Step navigation
    public function nextStep()
    {
        if ($this->step === 1) {
            if (!$this->selectedRoomId) {
                $this->addError('selectedRoomId', 'Debe seleccionar una habitación para continuar.');
                return;
            }
            $room = Room::findOrFail($this->selectedRoomId);
            $this->base_price = (float)$room->price;
            $this->calculatePrices();
            $this->step = 2;
        } elseif ($this->step === 2) {
            if (!$this->selectedTenantId) {
                $this->addError('selectedTenantId', 'Debe seleccionar un inquilino para continuar.');
                return;
            }
            $this->step = 3;
        } elseif ($this->step === 3) {
            $this->validate([
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after:start_date',
                'payment_frequency' => 'required|in:Mensual,Quincenal,Semanal',
                'payment_day' => 'required|integer|min:1|max:31',
                'notes' => 'nullable|string',
            ]);
            $this->step = 4;
        }
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    // Step 1: Room Selection Methods
    public function selectRoom($id)
    {
        $this->selectedRoomId = $id;
        $this->resetErrorBag('selectedRoomId');
    }

    // Step 2: Tenant Autocomplete
    public function selectTenant($id)
    {
        $this->selectedTenantId = $id;
        $this->tenantSearch = '';
        $this->resetErrorBag('selectedTenantId');
    }

    public function deselectTenant()
    {
        $this->selectedTenantId = null;
    }

    // Step 2: Quick Tenant Addition
    public function openQuickTenantModal()
    {
        $this->showQuickTenantModal = true;
        $this->resetQuickTenantForm();
    }

    public function closeQuickTenantModal()
    {
        $this->showQuickTenantModal = false;
        $this->resetQuickTenantForm();
    }

    private function resetQuickTenantForm()
    {
        $this->quickName = '';
        $this->quickIdentityNumber = '';
        $this->quickPhone = '';
        $this->quickEmail = '';
        $this->quickNotes = '';
        $this->resetErrorBag();
    }

    public function saveQuickTenant()
    {
        $this->validate([
            'quickName' => 'required|string|max:255',
            'quickIdentityNumber' => 'required|string|unique:tenants,identity_number,NULL,id,deleted_at,NULL',
            'quickPhone' => 'required|string|max:20',
            'quickEmail' => 'nullable|email|max:255',
            'quickNotes' => 'nullable|string',
        ]);

        $tenant = Tenant::create([
            'name' => $this->quickName,
            'identity_number' => $this->quickIdentityNumber,
            'phone' => $this->quickPhone,
            'email' => $this->quickEmail,
            'notes' => $this->quickNotes,
            'status' => 'Activo'
        ]);

        $this->selectedTenantId = $tenant->id;
        $this->closeQuickTenantModal();
        $this->dispatch('swal:toast', type: 'success', message: 'Inquilino creado y seleccionado.');
    }

    // Step 3: Reactive Calculations
    public function updatedServices()
    {
        $this->calculatePrices();
    }

    public function calculatePrices()
    {
        $this->additional_services_price = 0;
        
        if ($this->services['wifi']) {
            $this->additional_services_price += $this->wifi_price;
        }
        if ($this->services['parking']) {
            $this->additional_services_price += $this->parking_price;
        }
        if ($this->services['cleaning']) {
            $this->additional_services_price += $this->cleaning_price;
        }
        if ($this->services['water_light']) {
            $this->additional_services_price += $this->water_light_price;
        }

        $this->total_price = $this->base_price + $this->additional_services_price;
    }

    // Step 4: Save Contract
    public function saveContract()
    {
        // Final sanity checks
        if (!$this->selectedRoomId || !$this->selectedTenantId) {
            $this->dispatch('swal:alert', type: 'error', title: 'Error', message: 'Datos incompletos para guardar el contrato.');
            return;
        }

        // Check if room is still free
        $room = Room::findOrFail($this->selectedRoomId);
        if ($room->status !== 'Disponible') {
            $this->dispatch('swal:alert', type: 'error', title: 'Error', message: 'La habitación ya no se encuentra disponible.');
            return;
        }

        // Create Contract
        $contract = Contract::create([
            'tenant_id' => $this->selectedTenantId,
            'room_id' => $this->selectedRoomId,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'payment_frequency' => $this->payment_frequency,
            'payment_day' => $this->payment_day,
            'base_price' => $this->base_price,
            'additional_services_price' => $this->additional_services_price,
            'total_price' => $this->total_price,
            'services' => $this->services,
            'status' => 'Activo',
            'notes' => $this->notes,
        ]);

        $this->dispatch('swal:toast', type: 'success', message: 'Contrato registrado con éxito. Habitación ocupada.');
        
        // Redirect to contracts list
        return redirect()->route('contracts.index');
    }

    public function render()
    {
        // 1. Available rooms query for step 1
        $roomsQuery = Room::where('status', 'Disponible');
        
        if ($this->roomCapacityFilter !== '') {
            $roomsQuery->where('capacity', $this->roomCapacityFilter);
        }
        
        if ($this->roomPriceFilter !== '') {
            if ($this->roomPriceFilter === 'low') {
                $roomsQuery->where('price', '<', 500);
            } elseif ($this->roomPriceFilter === 'medium') {
                $roomsQuery->whereBetween('price', [500, 750]);
            } elseif ($this->roomPriceFilter === 'high') {
                $roomsQuery->where('price', '>', 750);
            }
        }
        
        $availableRooms = $roomsQuery->get();

        // 2. Tenants query for step 2
        $tenants = [];
        if (strlen($this->tenantSearch) >= 2) {
            $tenants = Tenant::where('status', 'Activo')
                ->where(function($q) {
                    $q->where('name', 'like', '%' . $this->tenantSearch . '%')
                      ->orWhere('identity_number', 'like', '%' . $this->tenantSearch . '%');
                })
                ->limit(5)
                ->get();
        }

        // Detailed object resolution for summary page (step 4)
        $resolvedRoom = $this->selectedRoomId ? Room::find($this->selectedRoomId) : null;
        $resolvedTenant = $this->selectedTenantId ? Tenant::find($this->selectedTenantId) : null;

        return view('livewire.contracts.contract-wizard', [
            'availableRooms' => $availableRooms,
            'tenants' => $tenants,
            'resolvedRoom' => $resolvedRoom,
            'resolvedTenant' => $resolvedTenant,
        ])->layout('layouts.app');
    }
}
