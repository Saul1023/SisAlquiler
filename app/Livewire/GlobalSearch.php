<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Room;
use App\Models\Tenant;

class GlobalSearch extends Component
{
    public $search = '';

    public function resetSearch()
    {
        $this->search = '';
    }

    public function render()
    {
        $rooms = [];
        $tenants = [];

        if (strlen($this->search) >= 2) {
            $rooms = Room::where('room_number', 'like', '%' . $this->search . '%')
                ->limit(5)
                ->get();

            $tenants = Tenant::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('identity_number', 'like', '%' . $this->search . '%')
                ->limit(5)
                ->get();
        }

        return view('livewire.global-search', [
            'rooms' => $rooms,
            'tenants' => $tenants,
        ]);
    }
}
