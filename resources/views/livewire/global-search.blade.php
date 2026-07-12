<div class="relative w-full" x-data="{ open: true }" @click.outside="open = false">
    <div class="relative">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
            <i class="fa-solid fa-magnifying-glass text-sm"></i>
        </div>
        <input 
            type="text" 
            wire:model.live="search" 
            @focus="open = true"
            placeholder="Buscar cuarto o inquilino..." 
            class="w-full pl-10 pr-8 py-2 text-sm bg-slate-100 dark:bg-slate-700/50 border border-transparent focus:bg-white dark:focus:bg-slate-800 focus:border-blue-500 rounded-xl focus:outline-none transition-all duration-200 text-slate-800 dark:text-slate-200"
        >
        @if($search !== '')
            <button wire:click="resetSearch" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        @endif
    </div>

    <!-- Dropdown results -->
    @if(strlen($search) >= 2)
        <div x-show="open" class="absolute left-0 mt-2 w-80 md:w-96 bg-white dark:bg-slate-850 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl z-50 overflow-hidden divide-y divide-slate-100 dark:divide-slate-700/50" style="display: none;">
            
            <!-- Habitaciones Section -->
            <div class="p-2">
                <h6 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1.5">Habitaciones</h6>
                @if(count($rooms) > 0)
                    <div class="space-y-0.5">
                        @foreach($rooms as $room)
                            <a href="{{ route('rooms.index') }}?search={{ $room->room_number }}" @click="open = false" class="flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-door-open text-xs"></i>
                                    </div>
                                    <div>
                                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">Cuarto {{ $room->room_number }}</span>
                                        <span class="block text-[10px] text-slate-400">{{ $room->floor }}</span>
                                    </div>
                                </div>
                                <span class="text-[10px] font-medium px-2 py-0.5 rounded-full {{ $room->status === 'Disponible' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600' : ($room->status === 'Ocupado' ? 'bg-rose-50 dark:bg-rose-950/30 text-rose-600' : 'bg-amber-50 dark:bg-amber-950/30 text-amber-600') }}">
                                    {{ $room->status }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-[11px] text-slate-400 px-3 py-2">No se encontraron habitaciones.</p>
                @endif
            </div>

            <!-- Inquilinos Section -->
            <div class="p-2">
                <h6 class="text-[10px] font-bold uppercase tracking-wider text-slate-400 px-3 py-1.5">Inquilinos</h6>
                @if(count($tenants) > 0)
                    <div class="space-y-0.5">
                        @foreach($tenants as $tenant)
                            <a href="{{ route('tenants.index') }}?search={{ $tenant->identity_number }}" @click="open = false" class="flex items-center justify-between px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $tenant->photo_url }}" alt="{{ $tenant->name }}" class="w-8 h-8 rounded-full object-cover shrink-0">
                                    <div>
                                        <span class="text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $tenant->name }}</span>
                                        <span class="block text-[10px] text-slate-400">CI/DNI: {{ $tenant->identity_number }}</span>
                                    </div>
                                </div>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                            </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-[11px] text-slate-400 px-3 py-2">No se encontraron inquilinos.</p>
                @endif
            </div>
        </div>
    @endif
</div>
