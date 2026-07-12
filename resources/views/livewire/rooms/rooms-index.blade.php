<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-outfit">Gestión de Habitaciones</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Administra y monitorea el estado físico de los cuartos de alquiler.</p>
        </div>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-2 transition-all">
            <i class="fa-solid fa-plus text-sm"></i> Nueva Habitación
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $counts = \App\Models\Room::select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status');
            $disponibles = $counts['Disponible'] ?? 0;
            $ocupados = $counts['Ocupado'] ?? 0;
            $mantenimiento = $counts['Mantenimiento'] ?? 0;
            $total = $disponibles + $ocupados + $mantenimiento;
        @endphp
        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-hotel text-sm"></i>
            </div>
            <div>
                <span class="block text-lg font-bold text-slate-950 dark:text-white font-outfit">{{ $total }}</span>
                <span class="block text-[10px] text-slate-400 font-semibold uppercase">Total Cuartos</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-circle-check text-sm"></i>
            </div>
            <div>
                <span class="block text-lg font-bold text-slate-950 dark:text-white font-outfit">{{ $disponibles }}</span>
                <span class="block text-[10px] text-slate-400 font-semibold uppercase">Disponibles</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-door-closed text-sm"></i>
            </div>
            <div>
                <span class="block text-lg font-bold text-slate-950 dark:text-white font-outfit">{{ $ocupados }}</span>
                <span class="block text-[10px] text-slate-400 font-semibold uppercase">Ocupados</span>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                <i class="fa-solid fa-screwdriver-wrench text-sm"></i>
            </div>
            <div>
                <span class="block text-lg font-bold text-slate-950 dark:text-white font-outfit">{{ $mantenimiento }}</span>
                <span class="block text-[10px] text-slate-400 font-semibold uppercase">Mantenimiento</span>
            </div>
        </div>
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
                    placeholder="Buscar por número o piso..." 
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
                        <option value="Disponible">Disponible</option>
                        <option value="Ocupado">Ocupado</option>
                        <option value="Mantenimiento">Mantenimiento</option>
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
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('room_number')">
                            Número <i class="fa-solid {{ $sortField === 'room_number' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('floor')">
                            Ubicación <i class="fa-solid {{ $sortField === 'floor' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('capacity')">
                            Capacidad <i class="fa-solid {{ $sortField === 'capacity' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('price')">
                            Precio Base <i class="fa-solid {{ $sortField === 'price' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5">Estado (Un click para cambiar)</th>
                        <th class="px-5 py-3.5">Descripción</th>
                        <th class="px-5 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                    @forelse($rooms as $room)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors duration-200">
                            <!-- Room Number -->
                            <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs
                                        {{ $room->status === 'Disponible' ? 'bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-400' : ($room->status === 'Ocupado' ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400' : 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400') }}">
                                        {{ $room->room_number }}
                                    </div>
                                </div>
                            </td>

                            <!-- Location -->
                            <td class="px-5 py-3.5 font-medium text-slate-700 dark:text-slate-300">{{ $room->floor }}</td>

                            <!-- Capacity -->
                            <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                                <span class="flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-group text-[10px]"></i> {{ $room->capacity }} {{ $room->capacity == 1 ? 'Persona' : 'Personas' }}
                                </span>
                            </td>

                            <!-- Price -->
                            <td class="px-5 py-3.5 font-bold text-slate-800 dark:text-slate-200">
                                {{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($room->price, 2) }}
                            </td>

                            <!-- Status Dropdown Toggle (Alpine) -->
                            <td class="px-5 py-3.5" x-data="{ dropdownOpen: false }">
                                <div class="relative">
                                    <button 
                                        @click="dropdownOpen = !dropdownOpen" 
                                        class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold uppercase transition-transform active:scale-95 focus:outline-none shadow-sm
                                            @if($room->status === 'Disponible') bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/50 hover:bg-emerald-100/50
                                            @elseif($room->status === 'Ocupado') bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-900/50 cursor-not-allowed
                                            @else bg-amber-50 dark:bg-amber-950/30 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-900/50 hover:bg-amber-100/50
                                            @endif"
                                        :disabled="'{{ $room->status }}' === 'Ocupado'"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full 
                                            @if($room->status === 'Disponible') bg-emerald-500
                                            @elseif($room->status === 'Ocupado') bg-blue-500
                                            @else bg-amber-500
                                            @endif">
                                        </span>
                                        {{ $room->status }}
                                        @if($room->status !== 'Ocupado')
                                            <i class="fa-solid fa-angle-down text-[8px] opacity-60"></i>
                                        @endif
                                    </button>

                                    <!-- Dropdown options -->
                                    <div 
                                        x-show="dropdownOpen" 
                                        @click.outside="dropdownOpen = false" 
                                        class="absolute left-0 mt-1.5 w-36 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl shadow-lg z-20 py-1"
                                        style="display: none;"
                                    >
                                        @if($room->status !== 'Disponible')
                                            <button 
                                                wire:click="toggleStatus({{ $room->id }}, 'Disponible')" 
                                                @click="dropdownOpen = false"
                                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-650 flex items-center gap-1.5"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> DISPONIBLE
                                            </button>
                                        @endif
                                        @if($room->status !== 'Mantenimiento')
                                            <button 
                                                wire:click="toggleStatus({{ $room->id }}, 'Mantenimiento')" 
                                                @click="dropdownOpen = false"
                                                class="w-full text-left px-3 py-1.5 text-[10px] font-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-slate-650 flex items-center gap-1.5"
                                            >
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> MANTENIMIENTO
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Description -->
                            <td class="px-5 py-3.5 text-slate-400 dark:text-slate-500 italic max-w-xs truncate" title="{{ $room->description }}">
                                {{ $room->description ?: 'Sin observaciones' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5 text-right space-x-1 shrink-0">
                                <button wire:click="edit({{ $room->id }})" class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Editar Cuarto">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $room->id }})" class="p-2 text-rose-600 hover:text-rose-800 dark:text-rose-450 dark:hover:text-rose-450 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Eliminar Cuarto">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-hotel text-3xl mb-3 opacity-50"></i>
                                <p class="text-xs font-semibold">No se encontraron habitaciones con el criterio especificado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700/30 flex items-center justify-between">
            <span class="text-[11px] text-slate-400 font-medium">Mostrando {{ $rooms->firstItem() ?? 0 }} a {{ $rooms->lastItem() ?? 0 }} de {{ $rooms->total() }} registros</span>
            <div class="flex">
                {{ $rooms->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div 
        x-data="{ show: @entangle('isModalOpen') }" 
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
            @click="@this.closeModal()"
        ></div>

        <!-- Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div 
                x-show="show" 
                x-transition:enter="transition ease-out duration-300 animate-slide-up"
                x-transition:leave="transition ease-in duration-200"
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl w-full max-w-md relative z-10 overflow-hidden"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit">
                        {{ $room_id ? 'Editar Habitación' : 'Nueva Habitación' }}
                    </h3>
                    <button @click="@this.closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form wire:submit.prevent="save" class="p-6 space-y-4 text-xs">
                    <!-- Room Number & Floor -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Número de Cuarto *</label>
                            <input 
                                type="text" 
                                wire:model="room_number" 
                                placeholder="Ej: 101" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100
                                    {{ $errors->has('room_number') ? 'border-rose-500 dark:border-rose-600' : 'border-slate-250 dark:border-slate-650' }}"
                            >
                            @error('room_number') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Piso / Ubicación *</label>
                            <input 
                                type="text" 
                                wire:model="floor" 
                                placeholder="Ej: Piso 1" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100
                                    {{ $errors->has('floor') ? 'border-rose-500 dark:border-rose-600' : 'border-slate-250 dark:border-slate-650' }}"
                            >
                            @error('floor') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Capacity & Base Price -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Capacidad (Personas) *</label>
                            <input 
                                type="number" 
                                wire:model="capacity" 
                                min="1" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100
                                    {{ $errors->has('capacity') ? 'border-rose-500 dark:border-rose-600' : 'border-slate-250 dark:border-slate-650' }}"
                            >
                            @error('capacity') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Precio Base Mensual ({{ \App\Models\Setting::get('currency', 'Bs.') }}) *</label>
                            <input 
                                type="number" 
                                step="0.01" 
                                wire:model="price" 
                                placeholder="0.00" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100
                                    {{ $errors->has('price') ? 'border-rose-500 dark:border-rose-600' : 'border-slate-250 dark:border-slate-650' }}"
                            >
                            @error('price') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Estado *</label>
                        <select 
                            wire:model="status" 
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100
                                {{ $errors->has('status') ? 'border-rose-500 dark:border-rose-600' : 'border-slate-250 dark:border-slate-650' }}"
                            :disabled="room_id && '{{ $status }}' === 'Ocupado'"
                        >
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupado">Ocupado</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                        @error('status') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        @if($room_id && $status === 'Ocupado')
                            <p class="text-[9px] text-amber-500 mt-1 font-semibold"><i class="fa-solid fa-info-circle mr-1"></i>Los cuartos ocupados no pueden cambiar de estado desde aquí. Se deben administrar desde contratos.</p>
                        @endif
                    </div>

                    <!-- Description -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Descripción / Observaciones</label>
                        <textarea 
                            wire:model="description" 
                            rows="3" 
                            placeholder="Detalles sobre equipamiento o condiciones del cuarto..."
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"
                        ></textarea>
                        @error('description') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-6">
                        <button type="button" @click="@this.closeModal()" class="px-4 py-2 border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-bold rounded-xl text-slate-600 dark:text-slate-350 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-1.5 transition-all">
                            <span wire:loading wire:target="save" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                            Guardar Habitación
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
