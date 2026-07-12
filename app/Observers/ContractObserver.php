<?php

namespace App\Observers;

use App\Models\Contract;

class ContractObserver
{
    /**
     * Handle the Contract "created" event.
     */
    public function created(Contract $contract): void
    {
        if ($contract->status === 'Activo') {
            $room = $contract->room;
            if ($room && $room->status !== 'Ocupado') {
                $room->status = 'Ocupado';
                $room->save();
            }
        }
    }

    /**
     * Handle the Contract "updated" event.
     */
    public function updated(Contract $contract): void
    {
        // If contract was set to finished or cancelled, free up the room
        if ($contract->isDirty('status')) {
            $room = $contract->room;
            if ($room) {
                if ($contract->status === 'Activo') {
                    $room->status = 'Ocupado';
                    $room->save();
                } elseif (in_array($contract->status, ['Finalizado', 'Cancelado'])) {
                    // Check if there are other active contracts for this room (unlikely but safe)
                    $hasActive = Contract::where('room_id', $room->id)
                        ->where('status', 'Activo')
                        ->where('id', '!=', $contract->id)
                        ->exists();
                    
                    if (!$hasActive) {
                        $room->status = 'Disponible';
                        $room->save();
                    }
                }
            }
        }
    }
}
