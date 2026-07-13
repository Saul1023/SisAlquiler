<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-outfit">Nuevo Contrato</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-outfit">Flujo asistido para dar de alta un alquiler de habitación.</p>
        </div>
        <a href="{{ route('contracts.index') }}" class="text-xs font-semibold text-slate-500 dark:text-slate-450 hover:text-slate-700 dark:hover:text-slate-300">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver al Listado
        </a>
    </div>

    <!-- Multi-Step Progress Tracker -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 rounded-2xl p-5 shadow-sm">
        <div class="flex items-center justify-between max-w-xl mx-auto text-xs font-bold text-slate-500 dark:text-slate-400">
            <!-- Step 1 -->
            <div class="flex flex-col items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors
                    {{ $step === 1 ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400' : ($step > 1 ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-200 dark:border-slate-700') }}">
                    @if($step > 1) <i class="fa-solid fa-check text-xs"></i> @else 1 @endif
                </div>
                <span class="{{ $step === 1 ? 'text-blue-600 dark:text-blue-400 font-bold' : ($step > 1 ? 'text-emerald-500' : 'text-slate-400') }}">Habitación</span>
            </div>
            
            <div class="flex-1 h-0.5 bg-slate-200 dark:bg-slate-700 mx-2 -mt-4"></div>

            <!-- Step 2 -->
            <div class="flex flex-col items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors
                    {{ $step === 2 ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400' : ($step > 2 ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-200 dark:border-slate-700') }}">
                    @if($step > 2) <i class="fa-solid fa-check text-xs"></i> @else 2 @endif
                </div>
                <span class="{{ $step === 2 ? 'text-blue-600 dark:text-blue-400 font-bold' : ($step > 2 ? 'text-emerald-500' : 'text-slate-400') }}">Inquilino</span>
            </div>

            <div class="flex-1 h-0.5 bg-slate-200 dark:bg-slate-700 mx-2 -mt-4"></div>

            <!-- Step 3 -->
            <div class="flex flex-col items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors
                    {{ $step === 3 ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400' : ($step > 3 ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-200 dark:border-slate-700') }}">
                    @if($step > 3) <i class="fa-solid fa-check text-xs"></i> @else 3 @endif
                </div>
                <span class="{{ $step === 3 ? 'text-blue-600 dark:text-blue-400 font-bold' : ($step > 3 ? 'text-emerald-500' : 'text-slate-400') }}">Configuración</span>
            </div>

            <div class="flex-1 h-0.5 bg-slate-200 dark:bg-slate-700 mx-2 -mt-4"></div>

            <!-- Step 4 -->
            <div class="flex flex-col items-center gap-2">
                <div class="w-8 h-8 rounded-full flex items-center justify-center border-2 transition-colors
                    {{ $step === 4 ? 'border-blue-600 bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400' : 'border-slate-200 dark:border-slate-700' }}">
                    4
                </div>
                <span class="{{ $step === 4 ? 'text-blue-600 dark:text-blue-400 font-bold' : 'text-slate-400' }}">Confirmación</span>
            </div>
        </div>
    </div>

    <!-- Wizard Steps Body -->
    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700/50 rounded-2xl shadow-sm p-6">
        
        <!-- STEP 1: SELECT ROOM -->
        @if($step === 1)
            <div class="space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-700/30 pb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider font-outfit">Paso 1: Seleccionar Habitación Disponible</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Elige un cuarto libre de la lista.</p>
                    </div>
                    
                    <!-- Filters -->
                    <div class="flex gap-2 text-xs">
                        <select wire:model.live="roomCapacityFilter" class="bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-650 rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-slate-200 focus:outline-none">
                            <option value="">Capacidad (Todas)</option>
                            <option value="1">1 Persona</option>
                            <option value="2">2 Personas</option>
                            <option value="3">3 Personas</option>
                            <option value="4">4 Personas</option>
                        </select>
                        <select wire:model.live="roomPriceFilter" class="bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-650 rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-slate-200 focus:outline-none">
                            <option value="">Precio (Todos)</option>
                            <option value="low">Económicos (< $500)</option>
                            <option value="medium">Estándar ($500 - $750)</option>
                            <option value="high">Premium (> $750)</option>
                        </select>
                    </div>
                </div>

                <!-- Grid of rooms -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
                    @forelse($availableRooms as $room)
                        <div 
                            wire:click="selectRoom({{ $room->id }})"
                            class="border rounded-2xl p-4 cursor-pointer relative overflow-hidden transition-all duration-200 text-xs flex flex-col justify-between
                                {{ $selectedRoomId === $room->id ? 'border-blue-600 ring-2 ring-blue-500/10 bg-blue-50/10 dark:bg-blue-950/5 scale-[1.02] shadow-md' : 'border-slate-200 dark:border-slate-700 hover:border-slate-350 dark:hover:border-slate-600 hover:scale-[1.01] hover:shadow' }}"
                        >
                            <!-- Highlight Selected Dot -->
                            @if($selectedRoomId === $room->id)
                                <div class="absolute top-2 right-2 w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold">
                                    <i class="fa-solid fa-check"></i>
                                </div>
                            @endif

                            <div>
                                <span class="font-extrabold text-slate-900 dark:text-white text-sm font-outfit block">Cuarto {{ $room->room_number }}</span>
                                <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">{{ $room->floor }}</span>
                                
                                <div class="flex items-center gap-3 mt-3.5 text-slate-500 dark:text-slate-400 font-medium">
                                    <span><i class="fa-solid fa-user-group mr-1.5 text-[10px]"></i>Capacidad: {{ $room->capacity }}</span>
                                </div>
                                
                                @if($room->description)
                                    <p class="text-[10px] text-slate-450 mt-2.5 italic line-clamp-2">{{ $room->description }}</p>
                                @endif
                            </div>

                            <div class="mt-4 border-t border-slate-100 dark:border-slate-700/30 pt-3 flex items-center justify-between">
                                <span class="text-slate-450 font-bold">Monto Base:</span>
                                <span class="text-xs font-extrabold text-slate-900 dark:text-white font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($room->price, 2) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 text-slate-400 dark:text-slate-500">
                            <i class="fa-solid fa-hotel text-3xl mb-3 opacity-55"></i>
                            <p class="text-xs font-semibold">No hay habitaciones disponibles que cumplan los filtros.</p>
                        </div>
                    @endforelse
                </div>

                @error('selectedRoomId') <span class="block text-[11px] text-rose-500 font-medium text-center bg-rose-50 dark:bg-rose-950/20 py-2 rounded-xl border border-rose-200/50 mt-2">{{ $message }}</span> @enderror

                <!-- Wizard Actions -->
                <div class="flex justify-end pt-5 border-t border-slate-100 dark:border-slate-700/30">
                    <button wire:click="nextStep" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-5 py-2.5 rounded-xl shadow-md flex items-center gap-1.5 transition-all">
                        Siguiente: Inquilino <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 2: SELECT TENANT -->
        @if($step === 2)
            <div class="space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-700/30 pb-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider font-outfit">Paso 2: Seleccionar Inquilino</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Busca a un inquilino registrado o crea uno nuevo en el momento.</p>
                    </div>
                </div>

                <!-- Selection Container -->
                <div class="max-w-xl mx-auto space-y-4">
                    
                    @if(!$selectedTenantId)
                        <!-- Search input -->
                        <div class="space-y-1 text-xs">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Buscar por Nombre o Identidad (Escribe al menos 2 letras) *</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400 pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                                <input 
                                    type="text" 
                                    wire:model.live="tenantSearch" 
                                    placeholder="Nombre completo o CI/DNI..." 
                                    class="w-full pl-9 pr-4 py-2 text-xs bg-slate-50 dark:bg-slate-750 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100"
                                >
                            </div>
                        </div>

                        <!-- Autocomplete dropdown -->
                        @if(strlen($tenantSearch) >= 2)
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg overflow-hidden divide-y divide-slate-100 dark:divide-slate-750 text-xs">
                                @forelse($tenants as $tenant)
                                    <button 
                                        wire:click="selectTenant({{ $tenant->id }})"
                                        class="w-full text-left px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center justify-between transition-colors"
                                    >
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $tenant->photo_url }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                            <div>
                                                <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ $tenant->name }}</span>
                                                <span class="text-[10px] text-slate-400">CI/DNI: {{ $tenant->identity_number }} | Tel: {{ $tenant->phone }}</span>
                                            </div>
                                        </div>
                                        <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold">Seleccionar &rarr;</span>
                                    </button>
                                @empty
                                    <div class="p-4 text-center text-slate-400">
                                        <p class="font-medium">No se encontraron inquilinos activos.</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif

                        <!-- Or register a new one quick -->
                        <div class="text-center py-5">
                            <span class="text-[11px] text-slate-400 block mb-2">¿El inquilino no está registrado?</span>
                            <button type="button" wire:click="openQuickTenantModal" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-700 dark:text-slate-300 font-semibold px-4 py-2 rounded-xl text-[11px] shadow-sm flex items-center gap-1.5 mx-auto transition-colors">
                                <i class="fa-solid fa-user-plus"></i> Crear Inquilino Rápido
                            </button>
                        </div>
                    @else
                        <!-- Tenant Selected Card -->
                        @php
                            $selectedTenant = \App\Models\Tenant::find($selectedTenantId);
                        @endphp
                        @if($selectedTenant)
                            <div class="bg-blue-50/20 dark:bg-blue-950/5 border border-blue-200 dark:border-blue-900/50 p-4 rounded-xl flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $selectedTenant->photo_url }}" alt="" class="w-12 h-12 rounded-full object-cover border-2 border-white dark:border-slate-800 shadow-md">
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $selectedTenant->name }}</h4>
                                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">DNI/CI: {{ $selectedTenant->identity_number }}</p>
                                        <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1"><i class="fa-solid fa-phone mr-1"></i>{{ $selectedTenant->phone }}</p>
                                    </div>
                                </div>
                                <button type="button" wire:click="deselectTenant" class="text-rose-600 hover:text-rose-800 font-bold text-xs bg-rose-50 dark:bg-rose-950/20 px-3 py-1.5 rounded-xl hover:scale-95 transition-all">
                                    Quitar
                                </button>
                            </div>
                        @endif
                    @endif
                </div>

                @error('selectedTenantId') <span class="block text-[11px] text-rose-500 font-medium text-center bg-rose-50 dark:bg-rose-950/20 py-2 rounded-xl border border-rose-200/50 mt-2">{{ $message }}</span> @enderror

                <!-- Wizard Actions -->
                <div class="flex justify-between pt-5 border-t border-slate-100 dark:border-slate-700/30">
                    <button wire:click="prevStep" class="border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-600 dark:text-slate-350 font-bold text-xs px-4 py-2.5 rounded-xl transition-all">
                        <i class="fa-solid fa-arrow-left"></i> Anterior
                    </button>
                    <button wire:click="nextStep" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-5 py-2.5 rounded-xl shadow-md flex items-center gap-1.5 transition-all">
                        Siguiente: Configurar <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 3: CONFIGURE CONTRACT -->
        @if($step === 3)
            <div class="space-y-5">
                <div class="border-b border-slate-100 dark:border-slate-700/30 pb-4">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider font-outfit">Paso 3: Configurar Contrato y Servicios</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Establece la vigencia, día de pago y servicios adicionales contratados.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                    <!-- Config Form -->
                    <div class="space-y-4">
                        <!-- Dates -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Fecha de Entrada *</label>
                                <input 
                                    type="date" 
                                    wire:model="start_date" 
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"
                                >
                                @error('start_date') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Fecha de Salida (Opcional)</label>
                                <input 
                                    type="date" 
                                    wire:model="end_date" 
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"
                                >
                                @error('end_date') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Frecuencia & Pago -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Frecuencia de Pago *</label>
                                <select 
                                    wire:model.live="payment_frequency" 
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"
                                >
                                    <option value="Mensual">Mensual</option>
                                    <option value="Quincenal">Quincenal</option>
                                    <option value="Semanal">Semanal</option>
                                </select>
                                @error('payment_frequency') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Día de Pago (Número) *</label>
                                <input 
                                    type="number" 
                                    wire:model="payment_day" 
                                    min="1" 
                                    max="31" 
                                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"
                                >
                                @error('payment_day') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Servicios adicionales -->
                        <div class="space-y-2 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-150 dark:border-slate-700/40">
                            <label class="block font-bold text-slate-600 dark:text-slate-350 mb-1">Servicios Adicionales Contratados</label>
                            
                            <div class="grid grid-cols-2 gap-3.5">
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" wire:model.live="services.wifi" class="rounded text-blue-600 w-4 h-4 border-slate-300 focus:ring-blue-500">
                                    <span>WiFi (+ Bs. {{ $wifi_price }})</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" wire:model.live="services.parking" class="rounded text-blue-600 w-4 h-4 border-slate-300 focus:ring-blue-500">
                                    <span>Estacionamiento (+ Bs. {{ $parking_price }})</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" wire:model.live="services.cleaning" class="rounded text-blue-600 w-4 h-4 border-slate-300 focus:ring-blue-500">
                                    <span>Limpieza (+ Bs. {{ $cleaning_price }})</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer font-medium text-slate-700 dark:text-slate-300">
                                    <input type="checkbox" wire:model.live="services.water_light" class="rounded text-blue-600 w-4 h-4 border-slate-300 focus:ring-blue-500">
                                    <span>Agua / Luz (+ Bs. {{ $water_light_price }})</span>
                                </label>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Notas / Cláusulas del Contrato</label>
                            <textarea wire:model="notes" rows="2" placeholder="Detalles de depósito en garantía, acuerdos especiales..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"></textarea>
                        </div>
                    </div>

                    <!-- Reactive Price Details Sidebar -->
                    <div class="bg-slate-50 dark:bg-slate-800/80 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 flex flex-col justify-between">
                        <div>
                            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-4 border-b border-slate-200 dark:border-slate-700/50 pb-2">Desglose de Montos</h3>
                            
                            <div class="space-y-3 font-medium">
                                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                    <span>Precio base del cuarto:</span>
                                    <span class="font-bold font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($base_price, 2) }}</span>
                                </div>

                                <div class="flex items-center justify-between text-slate-500 dark:text-slate-400">
                                    <span>Servicios Adicionales:</span>
                                    <span class="font-bold font-mono">+ {{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($additional_services_price, 2) }}</span>
                                </div>
                                
                                <div class="pl-4 space-y-1.5 text-[10px] text-slate-400 italic">
                                    @if($services['wifi']) <div class="flex justify-between"><span>- WiFi</span><span>Bs. {{ $wifi_price }}</span></div> @endif
                                    @if($services['parking']) <div class="flex justify-between"><span>- Estacionamiento</span><span>Bs. {{ $parking_price }}</span></div> @endif
                                    @if($services['cleaning']) <div class="flex justify-between"><span>- Limpieza</span><span>Bs. {{ $cleaning_price }}</span></div> @endif
                                    @if($services['water_light']) <div class="flex justify-between"><span>- Agua / Luz</span><span>Bs. {{ $water_light_price }}</span></div> @endif
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-700/60 pt-4 mt-6 flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">PRECIO TOTAL:</span>
                            <span class="text-lg font-extrabold text-blue-600 dark:text-blue-400 font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($total_price, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Wizard Actions -->
                <div class="flex justify-between pt-5 border-t border-slate-100 dark:border-slate-700/30">
                    <button wire:click="prevStep" class="border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-600 dark:text-slate-350 font-bold text-xs px-4 py-2.5 rounded-xl transition-all">
                        <i class="fa-solid fa-arrow-left"></i> Anterior
                    </button>
                    <button wire:click="nextStep" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-5 py-2.5 rounded-xl shadow-md flex items-center gap-1.5 transition-all">
                        Siguiente: Confirmación <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- STEP 4: CONFIRM AND SAVE -->
        @if($step === 4)
            <div class="space-y-6">
                <div class="border-b border-slate-100 dark:border-slate-700/30 pb-4">
                    <h2 class="text-sm font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider font-outfit">Paso 4: Resumen y Confirmación</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Verifica que los datos del alquiler sean correctos antes de guardar el contrato.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
                    
                    <!-- Left column: Room and Tenant details -->
                    <div class="md:col-span-2 space-y-5">
                        
                        <!-- Room Details -->
                        @if($resolvedRoom)
                            <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm bg-slate-50/30 dark:bg-slate-850/10">
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-3 font-outfit flex items-center gap-2">
                                    <i class="fa-solid fa-door-open text-blue-500"></i> Habitación
                                </h3>
                                <div class="grid grid-cols-2 gap-3.5">
                                    <div>
                                        <span class="block text-[10px] text-slate-400">Número de Cuarto</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-350">Cuarto {{ $resolvedRoom->room_number }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] text-slate-400">Piso / Ubicación</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-350">{{ $resolvedRoom->floor }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] text-slate-400">Capacidad Máxima</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-350">{{ $resolvedRoom->capacity }} {{ $resolvedRoom->capacity == 1 ? 'Persona' : 'Personas' }}</span>
                                    </div>
                                    <div>
                                        <span class="block text-[10px] text-slate-400">Precio Base Mensual</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-350 font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($resolvedRoom->price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tenant Details -->
                        @if($resolvedTenant)
                            <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm bg-slate-50/30 dark:bg-slate-850/10">
                                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-3 font-outfit flex items-center gap-2">
                                    <i class="fa-solid fa-user text-blue-500"></i> Inquilino
                                </h3>
                                <div class="flex items-center gap-4">
                                    <img src="{{ $resolvedTenant->photo_url }}" alt="" class="w-12 h-12 rounded-full object-cover shadow border border-slate-200 dark:border-slate-700">
                                    <div class="grid grid-cols-2 gap-x-6 gap-y-2 flex-1">
                                        <div>
                                            <span class="block text-[10px] text-slate-400 font-medium">Nombre Completo</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $resolvedTenant->name }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] text-slate-400 font-medium">Doc. Identidad</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300 font-mono">{{ $resolvedTenant->identity_number }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] text-slate-400 font-medium">Teléfono</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $resolvedTenant->phone }}</span>
                                        </div>
                                        @if($resolvedTenant->email)
                                            <div>
                                                <span class="block text-[10px] text-slate-400 font-medium">Email</span>
                                                <span class="font-bold text-slate-700 dark:text-slate-300 truncate block">{{ $resolvedTenant->email }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Right Column: Contract breakdown and terms -->
                    <div class="space-y-4">
                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm bg-slate-50/30 dark:bg-slate-850/10 space-y-3">
                            <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-2 font-outfit flex items-center gap-2">
                                <i class="fa-solid fa-file-contract text-blue-500"></i> Términos del Contrato
                            </h3>
                            <div>
                                <span class="block text-[10px] text-slate-400">Fecha de Entrada</span>
                                <span class="font-bold text-slate-700 dark:text-slate-350">{{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-400">Fecha de Salida</span>
                                <span class="font-bold text-slate-700 dark:text-slate-350">{{ $end_date ? \Carbon\Carbon::parse($end_date)->format('d/m/Y') : 'Indefinido (Indeterminado)' }}</span>
                            </div>
                            <div>
                                <span class="block text-[10px] text-slate-400">Frecuencia y Día de Pago</span>
                                <span class="font-bold text-slate-700 dark:text-slate-350">{{ $payment_frequency }} (Día {{ $payment_day }} de vencimiento)</span>
                            </div>
                            @if($notes)
                                <div>
                                    <span class="block text-[10px] text-slate-400">Observaciones</span>
                                    <p class="font-medium text-slate-700 dark:text-slate-350 italic mt-0.5">{{ $notes }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Price summary -->
                        <div class="bg-blue-600 text-white rounded-xl p-5 shadow-md shadow-blue-500/10 flex flex-col justify-between h-40">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-100">Monto Mensual Comprometido</span>
                                <span class="text-2xl font-extrabold font-mono block mt-1.5">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($total_price, 2) }}</span>
                            </div>
                            <div class="text-[10px] text-blue-100 flex justify-between border-t border-blue-500 pt-2.5">
                                <span>Base: Bs. {{ number_format($base_price, 0) }}</span>
                                <span>Servicios: Bs. {{ number_format($additional_services_price, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Wizard Actions -->
                <div class="flex justify-between pt-5 border-t border-slate-100 dark:border-slate-700/30">
                    <button wire:click="prevStep" class="border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-600 dark:text-slate-350 font-bold text-xs px-4 py-2.5 rounded-xl transition-all">
                        <i class="fa-solid fa-arrow-left"></i> Anterior
                    </button>
                    
                    <button wire:click="saveContract" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-6 py-2.5 rounded-xl shadow-md flex items-center gap-1.5 transition-all">
                        <span wire:loading wire:target="saveContract" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                        <i class="fa-solid fa-floppy-disk"></i> Confirmar y Guardar Contrato
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- QUICK TENANT MODAL (Step 2 Helper) -->
    <div 
        x-data="{ show: @entangle('showQuickTenantModal') }" 
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
            @click="@this.closeQuickTenantModal()"
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
                        Crear Inquilino Rápido
                    </h3>
                    <button @click="@this.closeQuickTenantModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="saveQuickTenant" class="p-6 space-y-4">
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Nombre Completo *</label>
                        <input type="text" wire:model="quickName" placeholder="Juan Pérez" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                        @error('quickName') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Identidad (DNI/CI) *</label>
                            <input type="text" wire:model="quickIdentityNumber" placeholder="1234567" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                            @error('quickIdentityNumber') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Teléfono *</label>
                            <input type="text" wire:model="quickPhone" placeholder="78945612" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                            @error('quickPhone') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Email (Opcional)</label>
                        <input type="email" wire:model="quickEmail" placeholder="juan@gmail.com" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                        @error('quickEmail') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-6">
                        <button type="button" @click="@this.closeQuickTenantModal()" class="px-4 py-2 border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-bold rounded-xl text-slate-600 dark:text-slate-350 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-1.5 transition-all">
                            Guardar y Seleccionar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>