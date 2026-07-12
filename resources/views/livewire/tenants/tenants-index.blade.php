<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-outfit">Gestión de Inquilinos</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Registra y administra la información de contacto e historial de alquileres de los huéspedes.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <!-- Exports -->
            <button wire:click="exportPdf" class="bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 border border-slate-250 dark:border-slate-650 font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-file-pdf text-rose-500"></i> PDF
            </button>
            <button wire:click="exportExcel" class="bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 border border-slate-250 dark:border-slate-650 font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
            </button>
            <!-- Add New -->
            <button wire:click="create" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-2 transition-all ml-1">
                <i class="fa-solid fa-plus text-sm"></i> Nuevo Inquilino
            </button>
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
                    placeholder="Buscar por nombre, identidad o teléfono..." 
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
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
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
                        <th class="px-5 py-3.5">Foto</th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('name')">
                            Nombre Completo <i class="fa-solid {{ $sortField === 'name' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5 cursor-pointer hover:bg-slate-100/50 dark:hover:bg-slate-700/30" wire:click="sortBy('identity_number')">
                            Doc. Identidad <i class="fa-solid {{ $sortField === 'identity_number' ? ($sortAsc ? 'fa-angle-up' : 'fa-angle-down') : 'fa-sort opacity-40' }} ml-1"></i>
                        </th>
                        <th class="px-5 py-3.5">Teléfono</th>
                        <th class="px-5 py-3.5">Email</th>
                        <th class="px-5 py-3.5">Estado</th>
                        <th class="px-5 py-3.5">F. Registro</th>
                        <th class="px-5 py-3.5 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                    @forelse($tenants as $tenant)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors duration-200">
                            <!-- Photo -->
                            <td class="px-5 py-3">
                                <img src="{{ $tenant->photo_url }}" alt="{{ $tenant->name }}" class="w-8 h-8 rounded-full object-cover border border-slate-200 dark:border-slate-700">
                            </td>

                            <!-- Name -->
                            <td class="px-5 py-3.5 font-bold text-slate-900 dark:text-white">
                                {{ $tenant->name }}
                            </td>

                            <!-- Identity -->
                            <td class="px-5 py-3.5 font-medium text-slate-700 dark:text-slate-300 font-mono">{{ $tenant->identity_number }}</td>

                            <!-- Phone -->
                            <td class="px-5 py-3.5 text-slate-600 dark:text-slate-400">
                                <a href="tel:{{ $tenant->phone }}" class="hover:underline flex items-center gap-1">
                                    <i class="fa-solid fa-phone text-[9px] text-slate-400"></i> {{ $tenant->phone }}
                                </a>
                            </td>

                            <!-- Email -->
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400 truncate max-w-[150px]">
                                {{ $tenant->email ?: 'N/A' }}
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase
                                    {{ $tenant->status === 'Activo' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    {{ $tenant->status }}
                                </span>
                            </td>

                            <!-- Registered Date -->
                            <td class="px-5 py-3.5 text-slate-400 dark:text-slate-500">
                                {{ $tenant->created_at ? $tenant->created_at->format('d/m/Y') : 'N/A' }}
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5 text-right space-x-0.5 shrink-0">
                                <button wire:click="showHistory({{ $tenant->id }})" class="p-2 text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Historial de Alquileres">
                                    <i class="fa-solid fa-clock-history text-sm"></i>
                                </button>
                                <button wire:click="edit({{ $tenant->id }})" class="p-2 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Editar Inquilino">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $tenant->id }})" class="p-2 text-rose-600 hover:text-rose-800 dark:text-rose-450 dark:hover:text-rose-450 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Eliminar Inquilino">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-users text-3xl mb-3 opacity-50"></i>
                                <p class="text-xs font-semibold">No se encontraron inquilinos con el criterio especificado.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700/30 flex items-center justify-between">
            <span class="text-[11px] text-slate-400 font-medium">Mostrando {{ $tenants->firstItem() ?? 0 }} a {{ $tenants->lastItem() ?? 0 }} de {{ $tenants->total() }} registros</span>
            <div class="flex">
                {{ $tenants->links('pagination::tailwind') }}
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
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl w-full max-w-lg relative z-10 overflow-hidden"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit">
                        {{ $tenant_id ? 'Editar Inquilino' : 'Registrar Inquilino' }}
                    </h3>
                    <button @click="@this.closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Form Body -->
                <form wire:submit.prevent="save" class="p-6 space-y-4 text-xs">
                    <!-- Photo Upload with Preview and Progress -->
                    <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-800/60 p-4 rounded-xl border border-slate-150 dark:border-slate-700/40" x-data="{ isUploading: false, progress: 0 }" x-on:livewire-upload-start="isUploading = true" x-on:livewire-upload-finish="isUploading = false" x-on:livewire-upload-error="isUploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
                        <!-- Preview Circle -->
                        <div class="shrink-0">
                            @if ($photo)
                                <img src="{{ $photo->temporaryUrl() }}" class="w-16 h-16 rounded-full object-cover border border-slate-250 dark:border-slate-600 shadow-inner">
                            @elseif ($existing_photo)
                                <img src="{{ Storage::url($existing_photo) }}" class="w-16 h-16 rounded-full object-cover border border-slate-250 dark:border-slate-600 shadow-inner">
                            @else
                                <div class="w-16 h-16 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-400 text-2xl">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Input File Selection -->
                        <div class="flex-1 space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Foto de Perfil (Opcional)</label>
                            <input 
                                type="file" 
                                wire:model="photo" 
                                accept="image/*"
                                class="text-[10px] text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-semibold file:bg-blue-50 dark:file:bg-slate-700 file:text-blue-700 dark:file:text-blue-300 hover:file:bg-blue-100 dark:hover:file:bg-slate-650 cursor-pointer"
                            >
                            @error('photo') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                            
                            <!-- Progress Bar -->
                            <div x-show="isUploading" class="w-full bg-slate-200 dark:bg-slate-650 h-1.5 rounded-full overflow-hidden mt-2">
                                <div class="bg-blue-600 h-1.5 rounded-full" :style="'width: ' + progress + '%'"></div>
                            </div>
                            <span x-show="isUploading" class="block text-[9px] text-blue-500 mt-1 font-semibold">Cargando archivo... <span x-text="progress"></span>%</span>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Nombre Completo *</label>
                        <input 
                            type="text" 
                            wire:model="name" 
                            placeholder="Nombre y Apellidos" 
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100
                                {{ $errors->has('name') ? 'border-rose-500 dark:border-rose-600' : 'border-slate-250 dark:border-slate-650' }}"
                        >
                        @error('name') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Identity & Phone -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Documento Identidad (DNI/CI) *</label>
                            <input 
                                type="text" 
                                wire:model="identity_number" 
                                placeholder="Ej: 8541259" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100
                                    {{ $errors->has('identity_number') ? 'border-rose-500 dark:border-rose-600' : 'border-slate-250 dark:border-slate-650' }}"
                            >
                            @error('identity_number') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Teléfono de Contacto *</label>
                            <input 
                                type="text" 
                                wire:model="phone" 
                                placeholder="Ej: 78541258" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100
                                    {{ $errors->has('phone') ? 'border-rose-500 dark:border-rose-600' : 'border-slate-250 dark:border-slate-650' }}"
                            >
                            @error('phone') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Email & Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Correo Electrónico</label>
                            <input 
                                type="email" 
                                wire:model="email" 
                                placeholder="nombre@ejemplo.com" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"
                            >
                            @error('email') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Estado *</label>
                            <select 
                                wire:model="status" 
                                class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"
                            >
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                            @error('status') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="space-y-1">
                        <label class="block font-bold text-slate-600 dark:text-slate-350">Observaciones</label>
                        <textarea 
                            wire:model="notes" 
                            rows="2" 
                            placeholder="Detalles adicionales, referencias, lugar de trabajo, etc..."
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100 border-slate-250 dark:border-slate-650"
                        ></textarea>
                        @error('notes') <span class="block text-[10px] text-rose-500 font-medium">{{ $message }}</span> @enderror
                    </div>

                    <!-- Footer Action Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-slate-700/50 pt-4 mt-6">
                        <button type="button" @click="@this.closeModal()" class="px-4 py-2 border border-slate-250 dark:border-slate-650 hover:bg-slate-50 dark:hover:bg-slate-700/50 font-bold rounded-xl text-slate-600 dark:text-slate-350 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-1.5 transition-all">
                            <span wire:loading wire:target="save" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                            Guardar Inquilino
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- History / Profile Modal -->
    <div 
        x-data="{ show: @entangle('isHistoryOpen') }" 
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
            @click="@this.closeHistory()"
        ></div>

        <!-- Content Container -->
        <div class="flex items-center justify-center min-h-screen p-4">
            <div 
                x-show="show" 
                x-transition:enter="transition ease-out duration-300 animate-slide-up"
                x-transition:leave="transition ease-in duration-200"
                class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl w-full max-w-2xl relative z-10 overflow-hidden"
            >
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/50 flex items-center justify-between bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit">
                        Perfil e Historial de Inquilino
                    </h3>
                    <button @click="@this.closeHistory()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Body -->
                @if($selectedTenant)
                    <div class="p-6 space-y-6 text-xs overflow-y-auto max-h-[500px]">
                        <!-- Profile Card Info -->
                        <div class="flex flex-col sm:flex-row items-center gap-4 bg-slate-50 dark:bg-slate-800/40 border border-slate-150 dark:border-slate-700/40 p-4 rounded-xl">
                            <img src="{{ $selectedTenant->photo_url }}" alt="{{ $selectedTenant->name }}" class="w-16 h-16 rounded-full object-cover border-2 border-white dark:border-slate-700 shadow-md">
                            <div class="text-center sm:text-left min-w-0 flex-1">
                                <h4 class="text-sm font-bold text-slate-950 dark:text-white">{{ $selectedTenant->name }}</h4>
                                <p class="text-[10px] text-slate-400 mt-1 font-mono">DNI/CI: {{ $selectedTenant->identity_number }}</p>
                                <div class="mt-2.5 flex flex-wrap justify-center sm:justify-start gap-4 text-[10px] text-slate-500 dark:text-slate-400 font-medium">
                                    <span><i class="fa-solid fa-phone mr-1"></i>{{ $selectedTenant->phone }}</span>
                                    @if($selectedTenant->email)
                                        <span><i class="fa-solid fa-envelope mr-1"></i>{{ $selectedTenant->email }}</span>
                                    @endif
                                    <span><i class="fa-solid fa-calendar mr-1"></i>Registro: {{ $selectedTenant->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase shrink-0
                                {{ $selectedTenant->status === 'Activo' ? 'bg-emerald-50 dark:bg-emerald-950/30 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
                                {{ $selectedTenant->status }}
                            </span>
                        </div>

                        <!-- History Section -->
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider font-outfit">Contratos y Alquileres Realizados</h4>
                            
                            @forelse($tenantHistory as $contract)
                                <div class="border border-slate-200 dark:border-slate-700/80 rounded-xl overflow-hidden shadow-sm">
                                    <!-- Contract Header Info -->
                                    <div class="bg-slate-50 dark:bg-slate-850 px-4 py-3 flex items-center justify-between border-b border-slate-100 dark:border-slate-700/30">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">Cuarto {{ $contract->room->room_number ?? 'N/A' }}</span>
                                            <span class="text-[10px] text-slate-400">({{ $contract->room->floor ?? '' }})</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold uppercase
                                                @if($contract->status === 'Activo') bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600
                                                @elseif($contract->status === 'Finalizado') bg-slate-100 dark:bg-slate-750 text-slate-500
                                                @else bg-rose-50 dark:bg-rose-950/20 text-rose-600
                                                @endif">
                                                {{ $contract->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Contract details -->
                                    <div class="p-4 grid grid-cols-2 sm:grid-cols-4 gap-3 bg-white dark:bg-slate-800/40">
                                        <div>
                                            <span class="block text-[10px] text-slate-400">F. Inicio</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-350">{{ $contract->start_date->format('d/m/Y') }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] text-slate-400">F. Fin</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-350">{{ $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indefinido' }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] text-slate-400">Monto Mensual</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-350 font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($contract->total_price, 2) }}</span>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] text-slate-400">Servicios Activos</span>
                                            <span class="font-semibold text-slate-700 dark:text-slate-350 truncate block">
                                                @php
                                                    $activeServices = [];
                                                    if ($contract->services) {
                                                        foreach ($contract->services as $name => $active) {
                                                            if ($active) $activeServices[] = ucfirst($name);
                                                        }
                                                    }
                                                @endphp
                                                {{ count($activeServices) > 0 ? implode(', ', $activeServices) : 'Ninguno' }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Payments summary inside contract -->
                                    @if(count($contract->payments) > 0)
                                        <div class="bg-slate-50/40 dark:bg-slate-850/10 border-t border-slate-100 dark:border-slate-700/30 p-3">
                                            <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-wider mb-2">Resumen de Pagos en este Alquiler</span>
                                            <div class="space-y-1.5">
                                                @foreach($contract->payments as $payment)
                                                    <div class="flex items-center justify-between text-[10px] px-2 py-1 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/50 rounded-lg">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-mono bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-1.5 py-0.5 rounded text-[9px]">{{ $payment->period_covered }}</span>
                                                            <span class="text-slate-400">Vence día {{ $contract->payment_day }}</span>
                                                        </div>
                                                        <div class="flex items-center gap-4">
                                                            <span class="font-bold font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($payment->amount, 2) }}</span>
                                                            <span class="px-2 py-0.2 rounded-full text-[9px] font-bold uppercase
                                                                @if($payment->status === 'Pagado') text-emerald-600 bg-emerald-50 dark:bg-emerald-950/20
                                                                @elseif($payment->status === 'Atrasado') text-rose-600 bg-rose-50 dark:bg-rose-950/20
                                                                @else text-amber-600 bg-amber-50 dark:bg-amber-950/20
                                                                @endif">
                                                                {{ $payment->status }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-slate-50/20 border-t border-slate-100 dark:border-slate-700/30 p-3 text-center text-slate-400">
                                            Sin pagos registrados en este contrato.
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <div class="text-center py-8 bg-slate-50 dark:bg-slate-800/40 rounded-xl text-slate-400 border border-dashed border-slate-200 dark:border-slate-700">
                                    <i class="fa-solid fa-file-invoice text-xl opacity-60 mb-1"></i>
                                    <p class="text-xs font-semibold">El inquilino no tiene contratos registrados en el sistema.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- Footer -->
                <div class="px-6 py-3 border-t border-slate-150 dark:border-slate-700/50 flex items-center justify-end bg-slate-50 dark:bg-slate-800/50">
                    <button type="button" @click="@this.closeHistory()" class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white font-bold rounded-xl shadow-sm text-xs transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
