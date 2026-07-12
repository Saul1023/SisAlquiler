<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-outfit">Gestión de Contratos</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Monitorea los alquileres activos, finaliza estadías y configura depósitos.</p>
        </div>
        <a href="{{ route('contracts.wizard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-2 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-file-signature text-sm"></i> Nuevo Contrato (Asistente)
        </a>
    </div>

    <!-- Filters and Table Container -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm overflow-hidden">
        
        <!-- Table Filters Header -->
        <div class="p-4 flex flex-col md:flex-row md:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-800/50">
            <!-- Search -->
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input 
                    type="text" 
                    wire:model.live="search" 
                    placeholder="Buscar por inquilino o número de cuarto..." 
                    class="w-full pl-9 pr-4 py-2 text-xs bg-white dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-200"
                >
            </div>

            <!-- Select Filters -->
            <div class="flex items-center gap-2 self-end md:self-auto text-xs">
                <!-- Status Filter -->
                <div class="flex items-center gap-1.5">
                    <span class="text-slate-400">Estado:</span>
                    <select wire:model.live="statusFilter" class="bg-white dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl px-2.5 py-1.5 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
                        <option value="Todos">Todos</option>
                        <option value="Activo">Activos</option>
                        <option value="Finalizado">Finalizados</option>
                        <option value="Cancelado">Cancelados</option>
                    </select>
                </div>

                <!-- Per Page selector -->
                <div class="flex items-center gap-1.5 ml-2">
                    <span class="text-slate-400">Mostrar:</span>
                    <select wire:model.live="perPage" class="bg-white dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl px-2 py-1.5 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/30">
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('room_id')">
                            Cuarto <i class="fa-solid {{ $sortField === 'room_id' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5">Inquilino</th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('start_date')">
                            Entrada <i class="fa-solid {{ $sortField === 'start_date' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5">Salida</th>
                        <th class="px-5 py-3.5">Pago</th>
                        <th class="px-5 py-3.5">Servicios</th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('total_price')">
                            Total Mensual <i class="fa-solid {{ $sortField === 'total_price' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5">Estado</th>
                        <th class="px-5 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                    @forelse($contracts as $contract)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors duration-200">
                            <!-- Room -->
                            <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                Cuarto {{ $contract->room->room_number ?? 'N/A' }}
                                <span class="block text-[10px] text-slate-400 font-semibold mt-0.5">{{ $contract->room->floor ?? '' }}</span>
                            </td>

                            <!-- Tenant -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $contract->tenant->photo_url ?? '' }}" alt="" class="w-6 h-6 rounded-full object-cover">
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $contract->tenant->name ?? 'N/A' }}</span>
                                        <span class="text-[10px] text-slate-400">CI/DNI: {{ $contract->tenant->identity_number ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Start Date -->
                            <td class="px-5 py-3.5 font-medium text-slate-700 dark:text-slate-300">
                                {{ $contract->start_date->format('d/m/Y') }}
                            </td>

                            <!-- End Date -->
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-450">
                                {{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indefinido' }}
                            </td>

                            <!-- Payment details -->
                            <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                                <span class="block font-semibold">{{ $contract->payment_frequency }}</span>
                                <span class="text-[10px] text-slate-400">Vence: Día {{ $contract->payment_day }}</span>
                            </td>

                            <!-- Active Services -->
                            <td class="px-5 py-3.5">
                                <div class="flex flex-wrap gap-1 max-w-[150px]">
                                    @php
                                        $srvClass = 'px-1.5 py-0.2 rounded bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-350 text-[9px] font-semibold';
                                        $activeSrvs = 0;
                                    @endphp
                                    @if($contract->services['wifi'] ?? false) <span class="{{ $srvClass }}">WiFi</span> @php $activeSrvs++; @endphp @endif
                                    @if($contract->services['parking'] ?? false) <span class="{{ $srvClass }}">Estac.</span> @php $activeSrvs++; @endphp @endif
                                    @if($contract->services['cleaning'] ?? false) <span class="{{ $srvClass }}">Limp.</span> @php $activeSrvs++; @endphp @endif
                                    @if($contract->services['water_light'] ?? false) <span class="{{ $srvClass }}">Agua/Luz</span> @php $activeSrvs++; @endphp @endif
                                    
                                    @if($activeSrvs === 0)
                                        <span class="text-slate-400 italic text-[10px]">Sin adicionales</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Total Cost -->
                            <td class="px-5 py-3.5 font-extrabold text-slate-900 dark:text-white font-mono">
                                {{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($contract->total_price, 2) }}
                            </td>

                            <!-- Status Badge -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase
                                    @if($contract->status === 'Activo') bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30
                                    @elseif($contract->status === 'Finalizado') bg-slate-100 dark:bg-slate-750 text-slate-500 dark:text-slate-400
                                    @else bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-450 border border-rose-100 dark:border-rose-900/30
                                    @endif">
                                    {{ $contract->status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5 text-right space-x-0.5 shrink-0">
                                @if($contract->status === 'Activo')
                                    <!-- Checkout Button -->
                                    <button wire:click="openFinalizeModal({{ $contract->id }})" class="bg-amber-50 hover:bg-amber-100 dark:bg-amber-950/30 dark:hover:bg-amber-900/40 text-amber-600 dark:text-amber-400 font-bold px-2.5 py-1.5 rounded-lg text-[10px] shadow-sm border border-amber-200 dark:border-amber-900/40 active:scale-95 transition-all" title="Finalizar Alquiler (Check-out)">
                                        <i class="fa-solid fa-sign-out-alt mr-1"></i> Check-out
                                    </button>
                                    
                                    <!-- Cancel Button -->
                                    <button wire:click="confirmCancel({{ $contract->id }})" class="p-2 text-rose-600 hover:text-rose-800 dark:text-rose-400 dark:hover:text-rose-350 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Anular Contrato">
                                        <i class="fa-solid fa-ban text-sm"></i>
                                    </button>
                                @endif
                                
                                <!-- Edit Notes -->
                                <button wire:click="editNotes({{ $contract->id }})" class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Ver/Editar Notas">
                                    <i class="fa-solid fa-file-lines text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-file-signature text-3xl mb-3 opacity-50"></i>
                                <p class="text-xs font-semibold">No se encontraron contratos con el criterio especificado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700/30 flex items-center justify-between">
            <span class="text-[11px] text-slate-400 font-medium">Mostrando {{ $contracts->firstItem() ?? 0 }} a {{ $contracts->lastItem() ?? 0 }} de {{ $contracts->total() }} registros</span>
            <div class="flex">
                {{ $contracts->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- Edit Notes Modal -->
    <div 
        x-data="{ show: @entangle('isEditModalOpen') }" 
        x-show="show" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <!-- Backdrop -->
        <div 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"
            @click="@this.isEditModalOpen = false"
        ></div>

        <!-- Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div 
                x-show="show" 
                x-transition:enter="transition ease-out duration-300 animate-slide-up"
                x-transition:leave="transition ease-in duration-200"
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl w-full max-w-md relative z-10 overflow-hidden text-xs"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit">
                        Notas del Contrato
                    </h3>
                    <button @click="@this.isEditModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="saveNotes" class="p-6 space-y-4">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Observaciones del Contrato</label>
                        <textarea 
                            wire:model="notes" 
                            rows="4" 
                            placeholder="Detalles específicos..." 
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                        ></textarea>
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-6">
                        <button type="button" @click="@this.isEditModalOpen = false" class="px-4 py-2 border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-bold rounded-xl text-slate-600 dark:text-slate-350 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-1.5 transition-all">
                            Guardar Notas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Finalize (Check-out) Modal -->
    <div 
        x-data="{ show: @entangle('isFinalizeOpen') }" 
        x-show="show" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        style="display: none;"
    >
        <!-- Backdrop -->
        <div 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"
            @click="@this.isFinalizeOpen = false"
        ></div>

        <!-- Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div 
                x-show="show" 
                x-transition:enter="transition ease-out duration-300 animate-slide-up"
                x-transition:leave="transition ease-in duration-200"
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl w-full max-w-md relative z-10 overflow-hidden text-xs"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit flex items-center gap-1.5">
                        <i class="fa-solid fa-sign-out-alt text-amber-500"></i> Finalizar Alquiler (Check-out)
                    </h3>
                    <button @click="@this.isFinalizeOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form wire:submit.prevent="finalizeContract" class="p-6 space-y-4">
                    <!-- Check-out Date -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Fecha de Salida (Check-out) *</label>
                        <input 
                            type="date" 
                            wire:model.live="checkout_date" 
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                        >
                        @error('checkout_date') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Prorated Calculation Info Card -->
                    <div class="bg-amber-50/50 dark:bg-amber-950/10 border border-amber-200 dark:border-amber-900/50 p-4 rounded-xl space-y-2">
                        <h4 class="font-bold text-amber-800 dark:text-amber-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-calculator"></i> Cálculo Proporcional (Prorrateo)
                        </h4>
                        
                        <div class="space-y-1.5 text-slate-600 dark:text-slate-400 font-medium leading-relaxed">
                            <div class="flex justify-between">
                                <span>Días ocupados en este ciclo:</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $days_occupied }} {{ $days_occupied == 1 ? 'día' : 'días' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Fórmula aplicada:</span>
                                <span class="italic text-[10px]">(Monto Total / 30) * Días</span>
                            </div>
                            <hr class="border-amber-200 dark:border-amber-900/30 my-1">
                            <div class="flex justify-between text-xs font-bold text-slate-800 dark:text-slate-200 pt-1">
                                <span>MONTO DE CHECK-OUT A PAGAR:</span>
                                <span class="text-amber-700 dark:text-amber-400 font-mono">Bs. {{ number_format($prorated_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <p class="text-[10px] text-slate-400 italic">
                        <i class="fa-solid fa-circle-info mr-1"></i>Al confirmar, el contrato pasará a estado 'Finalizado', la habitación quedará 'Disponible' de inmediato y se registrará un cargo pendiente de pago con el monto calculado de check-out.
                    </p>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-6">
                        <button type="button" @click="@this.isFinalizeOpen = false" class="px-4 py-2 border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-bold rounded-xl text-slate-600 dark:text-slate-350 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow-md shadow-amber-500/10 flex items-center gap-1.5 transition-all">
                            <span wire:loading wire:target="finalizeContract" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                            Confirmar Check-out
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
