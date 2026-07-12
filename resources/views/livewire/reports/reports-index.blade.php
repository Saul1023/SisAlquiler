<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-outfit">Módulo de Reportes</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-outfit">Genera, previsualiza y exporta informes financieros y de ocupación.</p>
        </div>
        
        <div class="flex items-center gap-2">
            <!-- PDF Export -->
            <button wire:click="exportPdf" class="bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 border border-slate-250 dark:border-slate-650 font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-file-pdf text-rose-500"></i> Exportar PDF
            </button>
            <!-- Excel Export -->
            <button wire:click="exportExcel" class="bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-350 border border-slate-250 dark:border-slate-650 font-bold text-xs px-3.5 py-2.5 rounded-xl shadow-sm flex items-center gap-1.5 transition-all">
                <i class="fa-solid fa-file-excel text-emerald-600"></i> Exportar Excel
            </button>
        </div>
    </div>

    <!-- Main Reports Panel Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        <!-- Left: Filters Panel -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm space-y-4 self-start text-xs">
            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-200 uppercase tracking-wider mb-2 font-outfit">Configurar Filtros</h3>
            
            <!-- Report Type selector -->
            <div class="space-y-1">
                <label class="block font-bold text-slate-600 dark:text-slate-350">Tipo de Informe</label>
                <select wire:model.live="reportType" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                    <option value="ingresos">Reporte de Ingresos (Resumen)</option>
                    <option value="morosidad">Reporte de Morosidad (Deudas)</option>
                    <option value="ocupacion">Reporte de Ocupación</option>
                    <option value="contratos">Reporte de Contratos</option>
                    <option value="pagos">Reporte Detallado de Pagos</option>
                </select>
            </div>

            <!-- Conditional Date Range filter -->
            @if($reportType !== 'morosidad' && $reportType !== 'ocupacion')
                <div class="space-y-1">
                    <label class="block font-bold text-slate-600 dark:text-slate-350">Desde</label>
                    <input type="date" wire:model.live="dateFrom" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                </div>

                <div class="space-y-1">
                    <label class="block font-bold text-slate-600 dark:text-slate-350">Hasta</label>
                    <input type="date" wire:model.live="dateTo" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                </div>
            @endif

            <!-- Tenant selector (except for ocupacion and ingresos) -->
            @if($reportType !== 'ocupacion' && $reportType !== 'ingresos')
                <div class="space-y-1">
                    <label class="block font-bold text-slate-600 dark:text-slate-350">Inquilino</label>
                    <select wire:model.live="tenantId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                        <option value="">Todos</option>
                        @foreach($tenants as $tenant)
                            <option value="{{ $tenant->id }}">{{ $tenant->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Room selector (except for ocupacion) -->
            @if($reportType !== 'ocupacion')
                <div class="space-y-1">
                    <label class="block font-bold text-slate-600 dark:text-slate-350">Habitación</label>
                    <select wire:model.live="roomId" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                        <option value="">Todas</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">Cuarto {{ $room->room_number }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <!-- Payment Status selector (only for detailed payments) -->
            @if($reportType === 'pagos')
                <div class="space-y-1">
                    <label class="block font-bold text-slate-600 dark:text-slate-350">Estado de Pago</label>
                    <select wire:model.live="paymentStatus" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none text-slate-800 dark:text-slate-100">
                        <option value="Todos">Todos</option>
                        <option value="Pagado">Pagados</option>
                        <option value="Pendiente">Pendientes</option>
                        <option value="Atrasado">Atrasados</option>
                        <option value="Anulado">Anulados</option>
                    </select>
                </div>
            @endif
        </div>

        <!-- Right: Preview Table Area -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm overflow-hidden lg:col-span-3">
            <div class="p-4 flex items-center justify-between border-b border-slate-100 dark:border-slate-700/30 bg-slate-50/50 dark:bg-slate-800/50">
                <span class="text-xs font-bold text-slate-850 dark:text-slate-200 uppercase tracking-wider font-outfit flex items-center gap-1.5">
                    <i class="fa-solid fa-eye text-blue-500"></i> Vista Previa del Informe
                </span>
                <span class="text-[10px] text-slate-400 font-semibold">{{ count($previewData) }} registros encontrados</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        @if($reportType === 'ingresos')
                            <tr class="bg-slate-50/80 dark:bg-slate-800/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-150 dark:border-slate-700/30">
                                <th class="px-5 py-3.5">Periodo Mensual</th>
                                <th class="px-5 py-3.5 text-center">Transacciones Realizadas</th>
                                <th class="px-5 py-3.5 text-right">Total Recibido</th>
                            </tr>
                        @elseif($reportType === 'morosidad')
                            <tr class="bg-slate-50/80 dark:bg-slate-800/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-150 dark:border-slate-700/30">
                                <th class="px-5 py-3.5">Inquilino</th>
                                <th class="px-5 py-3.5">Habitación</th>
                                <th class="px-5 py-3.5">Periodo Vencido</th>
                                <th class="px-5 py-3.5">Días Atraso</th>
                                <th class="px-5 py-3.5">Estado</th>
                                <th class="px-5 py-3.5 text-right">Monto Deuda</th>
                            </tr>
                        @elseif($reportType === 'ocupacion')
                            <tr class="bg-slate-50/80 dark:bg-slate-800/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-150 dark:border-slate-700/30">
                                <th class="px-5 py-3.5">Habitación</th>
                                <th class="px-5 py-3.5">Piso / Ubicación</th>
                                <th class="px-5 py-3.5">Capacidad</th>
                                <th class="px-5 py-3.5">Precio Base</th>
                                <th class="px-5 py-3.5">Estado</th>
                                <th class="px-5 py-3.5">Inquilino Actual</th>
                                <th class="px-5 py-3.5">F. Entrada</th>
                            </tr>
                        @elseif($reportType === 'contratos')
                            <tr class="bg-slate-50/80 dark:bg-slate-800/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-150 dark:border-slate-700/30">
                                <th class="px-5 py-3.5">Inquilino</th>
                                <th class="px-5 py-3.5">Habitación</th>
                                <th class="px-5 py-3.5">Fecha Entrada</th>
                                <th class="px-5 py-3.5">Fecha Salida</th>
                                <th class="px-5 py-3.5">Estado</th>
                                <th class="px-5 py-3.5 text-right">Precio Mensual</th>
                            </tr>
                        @elseif($reportType === 'pagos')
                            <tr class="bg-slate-50/80 dark:bg-slate-800/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-150 dark:border-slate-700/30">
                                <th class="px-5 py-3.5">Comprobante</th>
                                <th class="px-5 py-3.5">Inquilino</th>
                                <th class="px-5 py-3.5">Cuarto</th>
                                <th class="px-5 py-3.5">Periodo</th>
                                <th class="px-5 py-3.5">F. Pago</th>
                                <th class="px-5 py-3.5">Método</th>
                                <th class="px-5 py-3.5">Estado</th>
                                <th class="px-5 py-3.5 text-right">Monto</th>
                            </tr>
                        @endif
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                        @forelse($previewData as $item)
                            @if($reportType === 'ingresos')
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold font-mono">{{ $item['periodo'] }}</td>
                                    <td class="px-5 py-3 text-center font-medium">{{ $item['transacciones'] }}</td>
                                    <td class="px-5 py-3 text-right font-extrabold text-slate-900 dark:text-white font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($item['total'], 2) }}</td>
                                </tr>
                            @elseif($reportType === 'morosidad')
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold text-slate-800 dark:text-slate-200">{{ $item['inquilino'] }}</td>
                                    <td class="px-5 py-3 font-medium">Cuarto {{ $item['cuarto'] }}</td>
                                    <td class="px-5 py-3 font-mono font-bold">{{ $item['periodo'] }}</td>
                                    <td class="px-5 py-3 font-bold text-rose-600">{{ $item['atraso'] }} días</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-rose-50 dark:bg-rose-950/20 text-rose-600">{{ $item['estado'] }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-right font-extrabold text-rose-600 dark:text-rose-450 font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($item['monto'], 2) }}</td>
                                </tr>
                            @elseif($reportType === 'ocupacion')
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold">Cuarto {{ $item['cuarto'] }}</td>
                                    <td class="px-5 py-3 font-medium">{{ $item['piso'] }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $item['capacidad'] }} Personas</td>
                                    <td class="px-5 py-3 font-bold font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($item['precio'], 2) }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase
                                            @if($item['estado'] === 'Disponible') bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600
                                            @elseif($item['estado'] === 'Ocupado') bg-blue-50 dark:bg-blue-950/20 text-blue-600
                                            @else bg-amber-50 dark:bg-amber-950/20 text-amber-600
                                            @endif">
                                            {{ $item['estado'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 font-bold text-slate-800 dark:text-slate-200">{{ $item['inquilino'] }}</td>
                                    <td class="px-5 py-3 text-slate-400">{{ $item['fecha_entrada'] }}</td>
                                </tr>
                            @elseif($reportType === 'contratos')
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-bold text-slate-850 dark:text-slate-200">{{ $item['inquilino'] }}</td>
                                    <td class="px-5 py-3 font-medium">Cuarto {{ $item['cuarto'] }}</td>
                                    <td class="px-5 py-3 font-medium text-slate-700">{{ $item['entrada'] }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $item['salida'] }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase
                                            @if($item['estado'] === 'Activo') bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600
                                            @elseif($item['estado'] === 'Finalizado') bg-slate-100 dark:bg-slate-750 text-slate-500
                                            @else bg-rose-50 dark:bg-rose-950/20 text-rose-600
                                            @endif">
                                            {{ $item['estado'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right font-extrabold text-slate-900 dark:text-white font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($item['total'], 2) }}</td>
                                </tr>
                            @elseif($reportType === 'pagos')
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                    <td class="px-5 py-3 font-mono font-bold">{{ $item['recibo'] }}</td>
                                    <td class="px-5 py-3 font-bold text-slate-850 dark:text-slate-200">{{ $item['inquilino'] }}</td>
                                    <td class="px-5 py-3 font-medium">Cuarto {{ $item['cuarto'] }}</td>
                                    <td class="px-5 py-3"><span class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-350 px-1.5 py-0.2 rounded font-mono font-bold">{{ $item['periodo'] }}</span></td>
                                    <td class="px-5 py-3 text-slate-500">{{ $item['fecha'] }}</td>
                                    <td class="px-5 py-3 text-slate-500">{{ $item['metodo'] }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-[9px] font-bold uppercase
                                            @if($item['estado'] === 'Pagado') bg-emerald-50 dark:bg-emerald-950/20 text-emerald-600
                                            @elseif($item['estado'] === 'Pendiente') bg-amber-50 dark:bg-amber-950/20 text-amber-600
                                            @elseif($item['estado'] === 'Atrasado') bg-rose-50 dark:bg-rose-950/20 text-rose-600
                                            @else bg-slate-150 dark:bg-slate-700 text-slate-500
                                            @endif">
                                            {{ $item['estado'] }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-right font-extrabold text-slate-900 dark:text-white font-mono">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($item['monto'], 2) }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="10" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-folder-open text-3xl mb-3 opacity-55"></i>
                                    <p class="text-xs font-semibold">No se encontraron registros coincidentes con los filtros actuales.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
