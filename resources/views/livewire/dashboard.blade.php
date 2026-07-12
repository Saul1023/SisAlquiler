<div wire:poll.30s class="space-y-6">
    <!-- Header with Polling status -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white font-outfit">Escritorio</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Resumen y métricas en tiempo real de los alquileres.</p>
        </div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 w-fit shrink-0">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span>Actualizado en tiempo real (30s)</span>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Card 1: Habitaciones -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-door-open text-xl"></i>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-slate-950 dark:text-white font-outfit">{{ $stats['totalRooms'] }}</span>
                    <span class="block text-[11px] font-semibold text-slate-400">Total Cuartos</span>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs border-t border-slate-100 dark:border-slate-700/30 pt-3">
                <span class="text-emerald-600 dark:text-emerald-400 font-medium">
                    <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i>{{ $stats['availableRooms'] }} Libres
                </span>
                <span class="text-slate-400 dark:text-slate-500 font-medium">
                    {{ $stats['occupiedRooms'] }} Ocupados | {{ $stats['maintRooms'] }} Mant.
                </span>
            </div>
        </div>

        <!-- Card 2: Ocupación -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/40 text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-percent text-xl"></i>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-slate-950 dark:text-white font-outfit">{{ $stats['occupancyRate'] }}%</span>
                    <span class="block text-[11px] font-semibold text-slate-400">Tasa de Ocupación</span>
                </div>
            </div>
            <div class="mt-4 border-t border-slate-100 dark:border-slate-700/30 pt-3">
                <!-- Progress Bar -->
                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-600 to-purple-600 h-1.5 rounded-full" style="width: {{ $stats['occupancyRate'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Ingresos -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-money-bill-trend-up text-xl"></i>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-slate-950 dark:text-white font-outfit">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($stats['currentMonthRevenue'], 2) }}</span>
                    <span class="block text-[11px] font-semibold text-slate-400">Ingresos del Mes</span>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs border-t border-slate-100 dark:border-slate-700/30 pt-3">
                @php
                    $diff = $stats['currentMonthRevenue'] - $stats['prevMonthRevenue'];
                    $increased = $diff >= 0;
                    $percent = $stats['prevMonthRevenue'] > 0 ? abs(round(($diff / $stats['prevMonthRevenue']) * 100)) : 0;
                @endphp
                <span class="{{ $increased ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }} font-bold flex items-center gap-1">
                    <i class="fa-solid {{ $increased ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }}"></i>
                    {{ $percent }}%
                </span>
                <span class="text-slate-400 dark:text-slate-500 font-medium">
                    Mes anterior: {{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($stats['prevMonthRevenue'], 0) }}
                </span>
            </div>
        </div>

        <!-- Card 4: Inquilinos Morosos -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-xmark text-xl"></i>
                </div>
                <div class="text-right">
                    <span class="text-2xl font-extrabold text-rose-600 dark:text-rose-500 font-outfit">{{ $stats['delinquentCount'] }}</span>
                    <span class="block text-[11px] font-semibold text-slate-400">Inquilinos Morosos</span>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs border-t border-slate-100 dark:border-slate-700/30 pt-3">
                <span class="text-slate-500 dark:text-slate-400 font-semibold">
                    Contratos Activos: {{ $stats['activeContractsCount'] }}
                </span>
                <a href="{{ route('payments.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline font-bold text-[11px]">Ver deudas &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart 1: Ingresos y Ocupación Mensual -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider font-outfit">Historial de Ingresos y Ocupación</h3>
                <span class="text-[10px] text-slate-400">Últimos 6 meses</span>
            </div>
            <div class="h-64 relative">
                <canvas id="monthlyIncomeChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Métodos de Pago -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider font-outfit">Distribución de Métodos de Pago</h3>
                <span class="text-[10px] text-slate-400">Por montos totales</span>
            </div>
            <div class="h-64 relative flex items-center justify-center">
                <canvas id="paymentMethodsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tables and Activity Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Overdue payments and soon expiring contracts -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Overdue Payments Table -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm overflow-hidden">
                <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-700/30">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider font-outfit flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-500"></i> Pagos Atrasados Recientes
                    </h3>
                    <a href="{{ route('payments.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">Ver todos</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500 font-bold uppercase tracking-wider border-b border-slate-100 dark:border-slate-700/30">
                                <th class="px-4 py-3">Inquilino</th>
                                <th class="px-4 py-3">Cuarto</th>
                                <th class="px-4 py-3">Periodo</th>
                                <th class="px-4 py-3">Deuda</th>
                                <th class="px-4 py-3">Días de Atraso</th>
                                <th class="px-4 py-3 text-right">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700/40">
                            @forelse($stats['overduePayments'] as $payment)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors">
                                    <td class="px-4 py-3.5 font-semibold text-slate-800 dark:text-slate-200">
                                        <div class="flex items-center gap-2">
                                            <img src="{{ $payment->contract->tenant->photo_url ?? '' }}" alt="" class="w-6 h-6 rounded-full object-cover">
                                            <span>{{ $payment->contract->tenant->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 font-medium text-slate-600 dark:text-slate-400">Cuarto {{ $payment->contract->room->room_number ?? 'N/A' }}</td>
                                    <td class="px-4 py-3.5"><span class="bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 px-2 py-0.5 rounded font-mono">{{ $payment->period_covered }}</span></td>
                                    <td class="px-4 py-3.5 font-bold text-rose-600 dark:text-rose-400">{{ \App\Models\Setting::get('currency', 'Bs.') }} {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-3.5">
                                        <span class="bg-rose-50 dark:bg-rose-950/30 text-rose-600 dark:text-rose-400 px-2.5 py-0.5 rounded-full font-bold">
                                            {{ $payment->overdue_days }} días
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 text-right">
                                        <a href="{{ route('payments.index') }}?contract_id={{ $payment->contract_id }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-3 py-1.5 rounded-lg text-[10px] shadow-sm shadow-blue-500/10">Registrar Pago</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">
                                        <i class="fa-solid fa-circle-check text-2xl mb-2 opacity-65 text-emerald-500"></i>
                                        <p class="text-xs font-semibold">¡Felicidades! No hay pagos atrasados.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Expiring Contracts -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm overflow-hidden">
                <div class="p-5 flex items-center justify-between border-b border-slate-100 dark:border-slate-700/30">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider font-outfit flex items-center gap-2">
                        <i class="fa-solid fa-hourglass-half text-amber-500"></i> Contratos por vencer (Próximos 30 días)
                    </h3>
                    <a href="{{ route('contracts.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline">Ver todos</a>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($stats['expiringContracts'] as $contract)
                        <div class="bg-slate-50 dark:bg-slate-800/40 border border-slate-150 dark:border-slate-700/50 rounded-xl p-4 flex gap-3 relative overflow-hidden group">
                            <!-- Left Accent Color -->
                            <div class="absolute inset-y-0 left-0 w-1 bg-amber-500"></div>
                            
                            <img src="{{ $contract->tenant->photo_url ?? '' }}" alt="" class="w-10 h-10 rounded-full object-cover shrink-0">
                            <div class="min-w-0 flex-1">
                                <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $contract->tenant->name ?? 'N/A' }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5">Cuarto {{ $contract->room->room_number ?? 'N/A' }} ({{ $contract->room->floor ?? '' }})</p>
                                <div class="mt-2.5 flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400">
                                    <span>Vence: <b>{{ $contract->end_date->format('d/m/Y') }}</b></span>
                                    <span class="bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 font-bold px-2 py-0.5 rounded-full shrink-0">
                                        {{ Carbon::parse($contract->end_date)->diffInDays(Carbon::today()) }} días
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 text-center py-6 text-slate-400 dark:text-slate-500">
                            <i class="fa-solid fa-file-shield text-2xl mb-2 opacity-65 text-slate-300"></i>
                            <p class="text-xs font-semibold">No hay contratos próximos a vencer.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Recent Activity -->
        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700/50 shadow-sm flex flex-col">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100 uppercase tracking-wider font-outfit mb-5">Actividad Reciente</h3>
            
            <div class="relative pl-6 border-l-2 border-slate-100 dark:border-slate-700/60 space-y-6 flex-1">
                @forelse($stats['recentActivities'] as $activity)
                    <div class="relative">
                        <!-- Left Dot Icon -->
                        <div class="absolute -left-[37px] top-0.5 w-7 h-7 rounded-full border-4 border-white dark:border-slate-800 flex items-center justify-center {{ $activity['icon'] }} shadow-sm">
                            <!-- An icon class is rendered inside -->
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $activity['title'] }}</h4>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 leading-relaxed">{{ $activity['description'] }}</p>
                            <span class="block text-[9px] text-slate-400 mt-1.5 font-medium"><i class="fa-regular fa-clock mr-1"></i>{{ $activity['date_human'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-slate-400 dark:text-slate-500">
                        <i class="fa-solid fa-list-check text-2xl mb-2 opacity-60"></i>
                        <p class="text-xs font-semibold">Sin actividades registradas recientemente.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Scripts to initialize Chart.js -->
    @push('js')
    <script>
        document.addEventListener('livewire:init', () => {
            let incomeChartInstance = null;
            let methodsChartInstance = null;

            function initCharts() {
                // Destroy instances if they exist (for redraws)
                if (incomeChartInstance) incomeChartInstance.destroy();
                if (methodsChartInstance) methodsChartInstance.destroy();

                const isDark = document.documentElement.classList.contains('dark');
                const gridColor = isDark ? '#334155' : '#f1f5f9';
                const textColor = isDark ? '#94a3b8' : '#64748b';

                // Chart 1: Income & Occupancy
                const ctxIncome = document.getElementById('monthlyIncomeChart');
                if (ctxIncome) {
                    incomeChartInstance = new Chart(ctxIncome, {
                        type: 'line',
                        data: {
                            labels: @json($chartData['incomeLabels']),
                            datasets: [
                                {
                                    label: 'Ingresos (Bs.)',
                                    data: @json($chartData['incomeData']),
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    fill: true,
                                    tension: 0.3,
                                    borderWidth: 3,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Ocupación (Cuartos)',
                                    data: @json($chartData['occupancyData']),
                                    borderColor: '#8B5CF6',
                                    backgroundColor: 'transparent',
                                    borderWidth: 2,
                                    borderDash: [5, 5],
                                    pointStyle: 'rect',
                                    tension: 0.1,
                                    yAxisID: 'y1'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                    labels: {
                                        color: textColor,
                                        font: { family: 'Inter', size: 10, weight: '600' }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: { color: 'transparent' },
                                    ticks: { color: textColor, font: { family: 'Inter', size: 10 } }
                                },
                                y: {
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    grid: { color: gridColor },
                                    ticks: { color: textColor, font: { family: 'Inter', size: 10 } }
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: { drawOnChartArea: false },
                                    ticks: { 
                                        color: textColor, 
                                        font: { family: 'Inter', size: 10 },
                                        stepSize: 1,
                                        precision: 0
                                    }
                                }
                            }
                        }
                    });
                }

                // Chart 2: Methods Distribution
                const ctxMethods = document.getElementById('paymentMethodsChart');
                if (ctxMethods) {
                    const methodLabels = @json($chartData['methodLabels']);
                    const methodTotals = @json($chartData['methodTotals']);
                    
                    methodsChartInstance = new Chart(ctxMethods, {
                        type: 'doughnut',
                        data: {
                            labels: methodLabels.length > 0 ? methodLabels : ['Sin Datos'],
                            datasets: [{
                                data: methodTotals.length > 0 ? methodTotals : [1],
                                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#64748B'],
                                borderWithoutAnchor: true,
                                borderWidth: isDark ? 3 : 2,
                                borderColor: isDark ? '#1e293b' : '#ffffff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        color: textColor,
                                        font: { family: 'Inter', size: 10, weight: '600' },
                                        padding: 15
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Init charts on startup
            initCharts();

            // Re-init charts when dark mode toggles
            const observer = new MutationObserver(() => {
                initCharts();
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

            // Re-init charts on Livewire updates
            Livewire.hook('request', ({ respond }) => {
                respond(() => {
                    setTimeout(initCharts, 50);
                });
            });
        });
    </script>
    @endpush
</div>
