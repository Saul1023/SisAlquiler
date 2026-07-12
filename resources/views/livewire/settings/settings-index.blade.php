<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-outfit">Configuración del Sistema</h1>
        <p class="text-xs text-slate-500 dark:text-slate-400 font-outfit font-medium">Gestiona los precios base de los servicios, datos de facturación de la empresa y seguridad del administrador.</p>
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-xs">
        
        <!-- Sidebar Navigation Tabs -->
        <div class="md:col-span-1 bg-white dark:bg-slate-800 p-4 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm space-y-1.5 self-start">
            <button 
                wire:click="$set('activeTab', 'empresa')" 
                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-bold transition-all
                    {{ $activeTab === 'empresa' ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-slate-800' }}"
            >
                <i class="fa-solid fa-building text-sm"></i> Empresa y Precios
            </button>
            <button 
                wire:click="$set('activeTab', 'email')" 
                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-bold transition-all
                    {{ $activeTab === 'email' ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-slate-800' }}"
            >
                <i class="fa-solid fa-envelope text-sm"></i> Servidor SMTP
            </button>
            <button 
                wire:click="$set('activeTab', 'seguridad')" 
                class="w-full flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-left font-bold transition-all
                    {{ $activeTab === 'seguridad' ? 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/50 hover:text-slate-800' }}"
            >
                <i class="fa-solid fa-shield-halved text-sm"></i> Seguridad
            </button>
        </div>

        <!-- Tab Content Area -->
        <div class="md:col-span-3 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm overflow-hidden">
            
            <!-- Tab 1: Empresa & Precios -->
            @if($activeTab === 'empresa')
                <form wire:submit.prevent="saveCompany" class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    <div class="p-6 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit mb-4">Datos de la Empresa</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Nombre de la Empresa *</label>
                                <input type="text" wire:model="company_name" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('company_name') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Teléfono *</label>
                                <input type="text" wire:model="company_phone" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('company_phone') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="block font-bold text-slate-600 dark:text-slate-350">Dirección Física *</label>
                            <input type="text" wire:model="company_address" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                            @error('company_address') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 dark:border-slate-700/30 pt-4 mt-6">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Moneda del Sistema *</label>
                                <input type="text" wire:model="currency" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('currency') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Días de Gracia para Pagos *</label>
                                <input type="number" wire:model="grace_days" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('grace_days') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Services pricing -->
                        <div class="border-t border-slate-100 dark:border-slate-700/30 pt-4 mt-6">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit mb-4">Precios de Servicios Adicionales</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-600 dark:text-slate-350">Precio WiFi mensual (Bs.) *</label>
                                    <input type="number" step="0.01" wire:model="wifi_price" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 font-mono">
                                    @error('wifi_price') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-600 dark:text-slate-350">Precio Estacionamiento mensual (Bs.) *</label>
                                    <input type="number" step="0.01" wire:model="parking_price" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 font-mono">
                                    @error('parking_price') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-600 dark:text-slate-350">Precio Limpieza semanal mensual (Bs.) *</label>
                                    <input type="number" step="0.01" wire:model="cleaning_price" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 font-mono">
                                    @error('cleaning_price') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="block font-bold text-slate-600 dark:text-slate-350">Precio Agua / Luz mensual (Bs.) *</label>
                                    <input type="number" step="0.01" wire:model="water_light_price" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 font-mono">
                                    @error('water_light_price') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-xl shadow-md flex items-center gap-1.5 transition-all">
                            <span wire:loading wire:target="saveCompany" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            @endif

            <!-- Tab 2: SMTP Config -->
            @if($activeTab === 'email')
                <form wire:submit.prevent="saveMail" class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    <div class="p-6 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit mb-4">Configuración del Servidor SMTP</h3>
                        <p class="text-[10px] text-slate-400 -mt-2">Define las credenciales para la salida de correos de recordatorios automatizados de pago.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Host SMTP *</label>
                                <input type="text" wire:model="mail_host" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 font-mono">
                                @error('mail_host') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Puerto SMTP *</label>
                                <input type="number" wire:model="mail_port" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100 font-mono">
                                @error('mail_port') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Usuario SMTP</label>
                                <input type="text" wire:model="mail_username" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('mail_username') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Contraseña SMTP</label>
                                <input type="password" wire:model="mail_password" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('mail_password') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Encriptación *</label>
                                <select wire:model="mail_encryption" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                    <option value="none">Ninguno</option>
                                    <option value="ssl">SSL</option>
                                    <option value="tls">TLS</option>
                                </select>
                                @error('mail_encryption') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Email Remitente *</label>
                                <input type="email" wire:model="mail_from_address" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('mail_from_address') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-xl shadow-md flex items-center gap-1.5 transition-all">
                            <span wire:loading wire:target="saveMail" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                            Guardar Conexión
                        </button>
                    </div>
                </form>
            @endif

            <!-- Tab 3: Security -->
            @if($activeTab === 'seguridad')
                <form wire:submit.prevent="changePassword" class="divide-y divide-slate-100 dark:divide-slate-700/30">
                    <div class="p-6 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider font-outfit mb-4">Actualizar Contraseña Administrador</h3>
                        
                        <div class="space-y-3 max-w-sm">
                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Contraseña Actual *</label>
                                <input type="password" wire:model="current_password" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('current_password') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Nueva Contraseña *</label>
                                <input type="password" wire:model="new_password" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('new_password') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1">
                                <label class="block font-bold text-slate-600 dark:text-slate-350">Confirmar Nueva Contraseña *</label>
                                <input type="password" wire:model="new_password_confirmation" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                                @error('new_password_confirmation') <span class="block text-[10px] text-rose-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-xl shadow-md flex items-center gap-1.5 transition-all">
                            <span wire:loading wire:target="changePassword" class="w-3 h-3 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                            Cambiar Contraseña
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
