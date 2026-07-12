<div class="relative" x-data="{ open: false }">
    <!-- Bell Button -->
    <button @click="open = !open" class="relative p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 focus:outline-none transition-colors duration-200">
        <i class="fa-solid fa-bell text-lg"></i>
        @if($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-slate-800 animate-pulse">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Notification Dropdown Panel -->
    <div 
        x-show="open" 
        @click.outside="open = false" 
        class="absolute right-0 mt-2 w-80 md:w-96 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl z-50 overflow-hidden divide-y divide-slate-100 dark:divide-slate-700/50"
        style="display: none;"
    >
        <!-- Panel Header -->
        <div class="flex items-center justify-between px-4 py-3 bg-slate-50 dark:bg-slate-800/80">
            <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Notificaciones</span>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    Marcar todo como leído
                </button>
            @endif
        </div>

        <!-- Filter Tabs -->
        <div class="flex gap-2 px-4 py-2 bg-slate-50/50 dark:bg-slate-800/40 text-[11px] font-medium border-b border-slate-100 dark:border-slate-700/30">
            <button wire:click="$set('filter', 'todos')" class="px-2.5 py-1 rounded-full transition-colors {{ $filter === 'todos' ? 'bg-blue-600 text-white' : 'bg-slate-200/50 dark:bg-slate-700/80 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                Todos
            </button>
            <button wire:click="$set('filter', 'pagos')" class="px-2.5 py-1 rounded-full transition-colors {{ $filter === 'pagos' ? 'bg-blue-600 text-white' : 'bg-slate-200/50 dark:bg-slate-700/80 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                Pagos
            </button>
            <button wire:click="$set('filter', 'contratos')" class="px-2.5 py-1 rounded-full transition-colors {{ $filter === 'contratos' ? 'bg-blue-600 text-white' : 'bg-slate-200/50 dark:bg-slate-700/80 text-slate-500 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' }}">
                Contratos
            </button>
        </div>

        <!-- Alerts List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700/40">
            @forelse($alerts as $alert)
                <div class="p-3.5 flex items-start gap-3 transition-colors {{ $alert['is_read'] ? 'opacity-60 bg-transparent' : 'bg-blue-50/10 dark:bg-blue-950/5' }}">
                    <!-- Status Icon -->
                    <div class="mt-0.5 shrink-0 flex items-center justify-center w-7 h-7 rounded-lg 
                        @if($alert['color'] === 'rose') bg-rose-50 dark:bg-rose-950/40 text-rose-500
                        @elseif($alert['color'] === 'amber') bg-amber-50 dark:bg-amber-950/40 text-amber-500
                        @elseif($alert['color'] === 'yellow') bg-yellow-50 dark:bg-yellow-950/40 text-yellow-500
                        @else bg-slate-100 dark:bg-slate-750 text-slate-500
                        @endif">
                        <i class="fa-solid {{ $alert['icon'] }} text-sm"></i>
                    </div>

                    <!-- Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $alert['title'] }}</span>
                            <span class="text-[9px] font-medium text-slate-400 shrink-0">{{ $alert['date'] }}</span>
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                            {{ $alert['message'] }}
                        </p>
                        
                        <!-- Actions -->
                        @if(!$alert['is_read'])
                            <div class="flex items-center gap-2 mt-2">
                                <button wire:click="markAsRead('{{ $alert['hash'] }}')" class="text-[9px] font-semibold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    Marcar como leído
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400 dark:text-slate-500">
                    <i class="fa-solid fa-bell-slash text-2xl mb-2.5 opacity-60"></i>
                    <p class="text-xs font-semibold">Sin alertas por ahora</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
