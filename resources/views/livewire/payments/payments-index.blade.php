<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-outfit">Registro de Pagos</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Controla la facturación mensual, cobros de servicios y emite comprobantes de pago.</p>
        </div>
        <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-2 transition-all self-start sm:self-auto">
            <i class="fa-solid fa-plus text-sm"></i> Registrar Pago (Cobro)
        </button>
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
                    placeholder="Buscar por inquilino, cuarto o recibo..." 
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
                        <option value="Pagado">Pagados</option>
                        <option value="Pendiente">Pendientes</option>
                        <option value="Atrasado">Atrasados</option>
                        <option value="Anulado">Anulados</option>
                    </select>
                </div>

                <!-- Method Filter -->
                <div class="flex items-center gap-1.5 ml-2">
                    <span class="text-slate-400">Método:</span>
                    <select wire:model.live="methodFilter" class="bg-white dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl px-2 py-1.5 text-slate-800 dark:text-slate-200 focus:outline-none focus:border-blue-500">
                        <option value="Todos">Todos</option>
                        <option value="Efectivo">Efectivo</option>
                        <option value="Transferencia">Transferencia</option>
                        <option value="Tarjeta">Tarjeta</option>
                        <option value="Otro">Otro</option>
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
            <table class="w-full text-left text-xs border-collapse responsive-table">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/30">
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('receipt_number')">
                            Recibo <i class="fa-solid {{ $sortField === 'receipt_number' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5">Inquilino</th>
                        <th class="px-5 py-3.5">Cuarto</th>
                        <th class="px-5 py-3.5">Periodo</th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('payment_date')">
                            F. Pago <i class="fa-solid {{ $sortField === 'payment_date' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5">Método</th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('amount')">
                            Monto Cobrado <i class="fa-solid {{ $sortField === 'amount' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5">Estado</th>
                        <th class="px-5 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors duration-200">
                            <!-- Receipt Number -->
                            <td data-label="Recibo" class="px-5 py-3.5 font-bold text-slate-800 dark:text-slate-200">
                                {{ $payment->receipt_number ?: '-' }}
                            </td>

                            <!-- Tenant -->
                            <td data-label="Inquilino" class="px-5 py-3.5">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $payment->contract->tenant->photo_url ?? '' }}" alt="" class="w-6 h-6 rounded-full object-cover">
                                    <div>
                                        <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $payment->contract->tenant->name ?? 'N/A' }}</span>
                                        <span class="text-[10px] text-slate-400">CI/DNI: {{ $payment->contract->tenant->identity_number ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Room -->
                            <td data-label="Cuarto" class="px-5 py-3.5 font-medium text-slate-600 dark:text-slate-400">
                                Cuarto {{ $payment->contract->room->room_number ?? 'N/A' }}
                            </td>

                            <!-- Covered Period -->
                            <td data-label="Periodo" class="px-5 py-3.5">
                                <span class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-350 px-2 py-0.5 rounded font-mono font-bold">{{ $payment->period_covered }}</span>
                            </td>

                            <!-- Payment Date -->
                            <td data-label="F. Pago" class="px-5 py-3.5 font-medium text-slate-750 dark:text-slate-350">
                                {{ $payment->payment_date->format('d/m/Y') }}
                            </td>

                            <!-- Payment Method -->
                            <td data-label="Método" class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                                {{ $payment->payment_method }}
                            </td>

                            <!-- Amount -->
                            <td data-label="Monto Cobrado" class="px-5 py-3.5 font-extrabold text-slate-900 dark:text-white font-mono">
                                {{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($payment->amount, 2) }}
                            </td>

                            <!-- Status -->
                            <td data-label="Estado" class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase
                                    @if($payment->status === 'Pagado') bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600 dark:text-emerald-450 border border-emerald-100 dark:border-emerald-900/20
                                    @elseif($payment->status === 'Pendiente') bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-450 border border-amber-100 dark:border-amber-900/20
                                    @elseif($payment->status === 'Atrasado') bg-rose-50 dark:bg-rose-950/20 text-rose-600 dark:text-rose-450 border border-rose-100 dark:border-rose-900/20
                                    @else bg-slate-150 dark:bg-slate-700 text-slate-500 dark:text-slate-400
                                    @endif">
                                    {{ $payment->status }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td data-label="Acciones" class="px-5 py-3.5 text-right space-x-0.5 shrink-0">
                                @if($payment->status === 'Pagado')
                                    <!-- Print PDF Receipt -->
                                    <button wire:click="downloadReceipt({{ $payment->id }})" class="p-2 text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-350 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Descargar Recibo PDF">
                                        <i class="fa-solid fa-file-pdf text-sm"></i>
                                    </button>
                                @endif
                                
                                <button wire:click="edit({{ $payment->id }})" class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Editar Pago">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $payment->id }})" class="p-2 text-rose-600 hover:text-rose-800 dark:text-rose-450 dark:hover:text-rose-450 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Eliminar Pago">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-file-invoice-dollar text-3xl mb-3 opacity-50"></i>
                                <p class="text-xs font-semibold">No se encontraron cobros registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700/30 flex items-center justify-between">
            <span class="text-[11px] text-slate-400 font-medium">Mostrando {{ $payments->firstItem() ?? 0 }} a {{ $payments->lastItem() ?? 0 }} de {{ $payments->total() }} registros</span>
            <div class="flex">
                {{ $payments->links('pagination::tailwind') }}
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
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl w-full max-w-md relative z-10 overflow-hidden text-xs"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit">
                        {{ $payment_id ? 'Modificar Pago' : 'Registrar Cobro' }}
                    </h3>
                    <button @click="@this.closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form wire:submit.prevent="save" class="p-6 space-y-4">
                    <!-- Contract Selection (reactive) -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Contrato Activo *</label>
                        <select 
                            wire:model.live="contract_id" 
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                        >
                            <option value="">Selecciona un contrato...</option>
                            @foreach($activeContracts as $c)
                                <option value="{{ $c->id }}">Cuarto {{ $c->room->room_number }} - {{ $c->tenant->name }}</option>
                            @endforeach
                        </select>
                        @error('contract_id') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Reactive Tenant Debt Warning Alert -->
                    @if($tenantDebt > 0 && !$payment_id)
                        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 p-3.5 rounded-xl flex items-start gap-2.5 text-amber-700 dark:text-amber-400">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-sm shrink-0"></i>
                            <div>
                                <h5 class="font-bold">Deuda Pendiente Detectada</h5>
                                <p class="text-[10px] font-medium leading-relaxed mt-0.5">El inquilino registra cobros vencidos o pendientes en este contrato por un total de <b>Bs. {{ number_format($tenantDebt, 2) }}</b>.</p>
                            </div>
                        </div>
                    @endif

                    <!-- Amount & Period -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Monto Cobrado *</label>
                                @if($suggestedAmount > 0)
                                    <button type="button" wire:click="$set('amount', {{ $suggestedAmount }})" class="text-[9px] text-blue-600 dark:text-blue-400 hover:underline">Sugerido: Bs. {{ $suggestedAmount }}</button>
                                @endif
                            </div>
                            <input 
                                type="number" 
                                step="0.01" 
                                wire:model="amount" 
                                placeholder="0.00" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                            >
                            @error('amount') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Periodo Cubierto *</label>
                            <input 
                                type="text" 
                                wire:model="period_covered" 
                                placeholder="YYYY-MM (Ej: 2026-07)" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 font-mono"
                            >
                            @error('period_covered') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Payment Date & Method -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Fecha de Pago *</label>
                            <input 
                                type="date" 
                                wire:model="payment_date" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                            >
                            @error('payment_date') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Método de Pago *</label>
                            <select 
                                wire:model="payment_method" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                            >
                                <option value="Efectivo">Efectivo</option>
                                <option value="Transferencia">Transferencia</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Otro">Otro</option>
                            </select>
                            @error('payment_method') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Receipt Number & Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Nro. de Recibo</label>
                            <input 
                                type="text" 
                                wire:model="receipt_number" 
                                placeholder="Ej: REC-00125" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                            >
                            @error('receipt_number') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Estado del Cobro *</label>
                            <select 
                                wire:model="status" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                            >
                                <option value="Pagado">Pagado</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Atrasado">Atrasado (Moroso)</option>
                                <option value="Anulado">Anulado</option>
                            </select>
                            @error('status') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Observaciones</label>
                        <textarea 
                            wire:model="notes" 
                            rows="2" 
                            placeholder="Detalles sobre recargos, pagos parciales..." 
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100"
                        ></textarea>
                        @error('notes') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-6">
                        <button type="button" @click="@this.closeModal()" class="px-4 py-2 border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-bold rounded-xl text-slate-600 dark:text-slate-350 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-1.5 transition-all font-outfit">
                            <span wire:loading wire:target="save" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                            {{ $payment_id ? 'Actualizar Pago' : 'Registrar Pago' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
