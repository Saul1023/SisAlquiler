<div x-data="{ open: @entangle('isOpen') }" class="relative">
    
    <!-- Floating AI Bubble Button (visible when closed) -->
    <button 
        x-show="!open" 
        @click="open = true; @this.toggleChat()" 
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-10 opacity-0 scale-90"
        x-transition:enter-end="translate-y-0 opacity-100 scale-100"
        class="fixed bottom-6 right-6 z-45 w-14 h-14 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white shadow-xl shadow-blue-500/25 flex items-center justify-center hover:scale-105 active:scale-95 transition-all group"
        title="Preguntar al Copiloto IA"
    >
        <!-- Pulse effect -->
        <span class="absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-20 animate-ping group-hover:animate-none"></span>
        <i class="fa-solid fa-robot text-xl relative z-10 transition-transform group-hover:rotate-12"></i>
    </button>

    <!-- Chat Slide-over Drawer Panel -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-hidden" 
        style="display: none;"
    >
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-xs" @click="open = false; @this.toggleChat()"></div>

        <!-- Chat Side Drawer -->
        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div 
                x-show="open"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-screen max-w-md bg-white dark:bg-slate-800 border-l border-slate-200 dark:border-slate-700/60 shadow-2xl flex flex-col h-full overflow-hidden"
            >
                <!-- Drawer Header -->
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/40 flex items-center justify-between bg-slate-50 dark:bg-slate-800/80">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-950/30 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-900/20">
                            <i class="fa-solid fa-robot text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-850 dark:text-white uppercase tracking-wider font-outfit">Copiloto IA</h3>
                            <span class="text-[9px] text-emerald-500 font-bold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Conectado a Gemini
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5">
                        <!-- Trash/Clear Button -->
                        @if(count($chatHistory) > 0)
                            <button wire:click="clearChat" class="p-1.5 text-slate-450 hover:text-rose-500 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors" title="Limpiar historial">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </button>
                        @endif
                        <!-- Close Drawer Button -->
                        <button @click="open = false; @this.toggleChat()" class="p-1.5 text-slate-450 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Chat Body (Messages History) -->
                <div 
                    id="copilot-chat-history"
                    class="flex-1 p-5 overflow-y-auto space-y-4 bg-slate-50/50 dark:bg-slate-900/10 scroll-smooth"
                >
                    <!-- Initial Welcome Message -->
                    <div class="flex gap-2.5 items-start">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-50 dark:border-blue-900/25">
                            <i class="fa-solid fa-robot text-xs"></i>
                        </div>
                        <div class="max-w-[85%] bg-white dark:bg-slate-700 border border-slate-200/65 dark:border-slate-650 p-3 rounded-2xl rounded-tl-none shadow-xs text-xs text-slate-800 dark:text-slate-200">
                            <p class="font-medium">¡Hola! Soy tu Copiloto inteligente en **AlquiRent**.</p>
                            <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed">Puedes hacerme cualquier tipo de pregunta analítica sobre el sistema. Por ejemplo: consultas sobre deudas, contratos, disponibilidad o proyecciones financieras.</p>
                        </div>
                    </div>

                    <!-- Iterated Message Bubbles -->
                    @foreach($chatHistory as $msg)
                        @if($msg['role'] === 'user')
                            <!-- User Message -->
                            <div class="flex gap-2.5 items-start justify-end">
                                <div class="max-w-[85%] bg-blue-600 text-white p-3 rounded-2xl rounded-tr-none shadow-sm text-xs font-medium">
                                    {{ $msg['text'] }}
                                </div>
                            </div>
                        @else
                            <!-- Assistant/AI Message -->
                            <div class="flex gap-2.5 items-start">
                                <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 border border-blue-50 dark:border-blue-900/25">
                                    <i class="fa-solid fa-robot text-xs"></i>
                                </div>
                                <div class="max-w-[85%] bg-white dark:bg-slate-700 border border-slate-200/65 dark:border-slate-650 p-3 rounded-2xl rounded-tl-none shadow-xs text-xs text-slate-800 dark:text-slate-200 space-y-1.5 leading-relaxed break-words markdown-content">
                                    <!-- Render text (simple markdown line break parsing or text format) -->
                                    {!! \Illuminate\Support\Str::markdown($msg['text']) !!}
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Writing Loading Indicator -->
                    <div wire:loading wire:target="sendMessage, askQuickQuestion" class="flex gap-2.5 items-start animate-pulse">
                        <div class="w-7 h-7 rounded-lg bg-blue-100 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-robot text-xs animate-bounce"></i>
                        </div>
                        <div class="bg-white dark:bg-slate-700 border border-slate-200/65 dark:border-slate-650 p-3 rounded-2xl rounded-tl-none shadow-xs text-[11px] text-slate-400 dark:text-slate-400 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.1s"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.2s"></span>
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0.3s"></span>
                            <span>Analizando el sistema...</span>
                        </div>
                    </div>
                </div>

                <!-- Suggested Quick Questions Panel -->
                <div class="px-5 py-2.5 border-t border-slate-100 dark:border-slate-700/40 bg-slate-50/50 dark:bg-slate-900/20 flex flex-wrap gap-1.5">
                    <button wire:click="askQuickQuestion('¿Quiénes tienen deudas pendientes hoy?')" class="px-2.5 py-1.5 bg-white dark:bg-slate-850 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] text-slate-600 dark:text-slate-350 font-bold transition-all">
                        🔍 ¿Quiénes deben renta?
                    </button>
                    <button wire:click="askQuickQuestion('Dame un resumen del estado de ocupación actual de los cuartos')" class="px-2.5 py-1.5 bg-white dark:bg-slate-850 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] text-slate-600 dark:text-slate-350 font-bold transition-all">
                        🏢 Estado de ocupación
                    </button>
                    <button wire:click="askQuickQuestion('¿Cuáles son los ingresos totales y cómo se distribuyen por mes este año?')" class="px-2.5 py-1.5 bg-white dark:bg-slate-850 hover:bg-slate-50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700 rounded-lg text-[10px] text-slate-600 dark:text-slate-350 font-bold transition-all">
                        💰 Ingresos este año
                    </button>
                </div>

                <!-- Input Footer Box -->
                <div class="p-4 border-t border-slate-150 dark:border-slate-700/60 bg-white dark:bg-slate-800">
                    @if($hasApiKey)
                        <form wire:submit.prevent="sendMessage" class="flex gap-2">
                            <input 
                                type="text" 
                                wire:model="message" 
                                placeholder="Escribe tu consulta semántica aquí..." 
                                class="flex-1 px-3.5 py-2.5 bg-slate-50 dark:bg-slate-700 border border-slate-250 dark:border-slate-650 rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-slate-800 dark:text-slate-100 text-xs"
                                autocomplete="off"
                                wire:loading.attr="disabled"
                                wire:target="sendMessage, askQuickQuestion"
                            >
                            <button 
                                type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-md shadow-blue-500/10 shrink-0 transition-colors"
                                wire:loading.attr="disabled"
                                wire:target="sendMessage, askQuickQuestion"
                            >
                                <i class="fa-solid fa-paper-plane text-sm"></i>
                            </button>
                        </form>
                    @else
                        <!-- Warnings if no API key is saved -->
                        <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/30 p-3 rounded-xl flex items-start gap-2 text-amber-700 dark:text-amber-400">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5 shrink-0 text-sm"></i>
                            <div>
                                <h5 class="font-bold text-[11px]">Asistente IA Desactivado</h5>
                                <p class="text-[9px] mt-0.5 leading-relaxed font-medium">Por favor, registra tu clave de API de Google Gemini en la pantalla de <a href="{{ route('settings.index') }}" class="underline font-bold">Configuración</a> para poder realizar consultas analíticas por chat.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Livewire Helper Scripts for Auto-scroll -->
    <script>
        document.addEventListener('livewire:init', () => {
            const scrollChat = () => {
                setTimeout(() => {
                    const chatHistoryDiv = document.getElementById('copilot-chat-history');
                    if (chatHistoryDiv) {
                        chatHistoryDiv.scrollTop = chatHistoryDiv.scrollHeight;
                    }
                }, 50);
            };

            // Scroll on chat open or new messages
            Livewire.on('chat-opened', scrollChat);
            Livewire.on('scroll-chat-bottom', scrollChat);
        });
    </script>
</div>
