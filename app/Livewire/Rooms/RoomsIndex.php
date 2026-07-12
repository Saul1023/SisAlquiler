<?php

namespace App\Livewire\Rooms;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Room;
use App\Models\Contract;

class RoomsIndex extends Component
{
    use WithPagination;

    // Search and filters
    public $search = '';
    public $statusFilter = 'Todos';
    public $perPage = 10;

    // Sorting
    public $sortField = 'room_number';
    public $sortAsc = true;

    // Form fields
    public $room_id = null;
    public $room_number = '';
    public $floor = '';
    public $capacity = 1;
    public $price = '';
    public $status = 'Disponible';
    public $description = '';

    // Modal state
    public $isModalOpen = false;

    // Listeners for delete confirmations
    protected $listeners = [
        'deleteRoom' => 'deleteRoom'
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
        $this->room_id = null;
        $this->room_number = '';
        $this->floor = '';
        $this->capacity = 1;
        $this->price = '';
        $this->status = 'Disponible';
        $this->description = '';
    }

    public function create()
    {
        $this->resetForm();
        $this->openModal();
    }

    public function edit($id)
    {
        $this->resetForm();
        $room = Room::findOrFail($id);
        $this->room_id = $room->id;
        $this->room_number = $room->room_number;
        $this->floor = $room->floor;
        $this->capacity = $room->capacity;
        $this->price = $room->price;
        $this->status = $room->status;
        $this->description = $room->description;

        $this->openModal();
    }

    public function save()
    {
        $rules = [
            'room_number' => 'required|string|unique:rooms,room_number,' . ($this->room_id ?? 'NULL') . ',id,deleted_at,NULL',
            'floor' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:Disponible,Ocupado,Mantenimiento',
            'description' => 'nullable|string',
        ];

        $validatedData = $this->validate($rules);

        if ($this->room_id) {
            // Prevent changing status to 'Disponible' if it has an active contract
            $room = Room::findOrFail($this->room_id);
            if ($room->status === 'Ocupado' && $this->status !== 'Ocupado') {
                $hasActiveContract = Contract::where('room_id', $this->room_id)
                    ->where('status', 'Activo')
                    ->exists();
                if ($hasActiveContract) {
                    $this->addError('status', 'No se puede cambiar el estado de un cuarto ocupado con contrato activo.');
                    return;
                }
            }
            $room->update($validatedData);
            $this->dispatch('swal:toast', type: 'success', message: 'Cuarto actualizado correctamente.');
        } else {
            Room::create($validatedData);
            $this->dispatch('swal:toast', type: 'success', message: 'Cuarto creado correctamente.');
        }

        $this->closeModal();
    }

    public function toggleStatus($id, $newStatus)
    {
        $room = Room::findOrFail($id);

        if ($room->status === 'Ocupado' && $newStatus !== 'Ocupado') {
            // Check active contract
            $hasActiveContract = Contract::where('room_id', $id)
                ->where('status', 'Activo')
                ->exists();
            if ($hasActiveContract) {
                $this->dispatch('swal:alert', type: 'error', title: 'Error', message: 'Este cuarto está ocupado con un contrato activo. Finalice el contrato primero.');
                return;
            }
        }

        $room->status = $newStatus;
        $room->save();
        $this->dispatch('swal:toast', type: 'success', message: "Estado de la habitación {$room->room_number} cambiado a {$newStatus}.");
    }

    public function confirmDelete($id)
    {
        $room = Room::findOrFail($id);

        // Check if there are active contracts
        $hasActiveContract = Contract::where('room_id', $id)
            ->where('status', 'Activo')
            ->exists();

        if ($hasActiveContract) {
            $this->dispatch('swal:alert', type: 'error', title: 'Acción bloqueada', message: 'No se puede eliminar la habitación porque tiene un contrato activo.');
            return;
        }

        $this->dispatch('swal:confirm', 
            id: $id, 
            action: 'deleteRoom', 
            title: '¿Eliminar habitación?', 
            message: "Esta acción enviará la habitación {$room->room_number} a la papelera. Podrás recuperarla más tarde.",
            confirmText: 'Sí, eliminar'
        );
    }

    public function deleteRoom($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        $this->dispatch('swal:toast', type: 'success', message: 'Habitación eliminada correctamente.');
    }

    public function render()
    {
        $query = Room::query();

        // Search filter
        if (strlen($this->search) > 0) {
            $query->where(function($q) {
                $q->where('room_number', 'like', '%' . $this->search . '%')
                  ->orWhere('floor', 'like', '%' . $this->search . '%');
            });
        }

        // Status filter
        if ($this->statusFilter !== 'Todos') {
            $query->where('status', $this->statusFilter);
        }

        // Sorting and Pagination
        $rooms = $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);

        return view('livewire.rooms.rooms-index', [
            'rooms' => $rooms
        ])->layout('layouts.app');
    }
}
