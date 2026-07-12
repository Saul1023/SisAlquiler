<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="space-y-4">
    <!-- Session Status -->
    @if(session('status'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl text-emerald-400 text-xs font-semibold">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="login" class="space-y-4 text-slate-300">
        <!-- Email Address -->
        <div class="space-y-1.5">
            <label for="email" class="block font-bold text-slate-400">Correo Electrónico</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                    <i class="fa-solid fa-envelope text-sm"></i>
                </span>
                <input 
                    wire:model="form.email" 
                    id="email" 
                    type="email" 
                    name="email" 
                    required 
                    autofocus 
                    autocomplete="username" 
                    placeholder="admin@admin.com"
                    class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-900/60 border border-slate-700 rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-white"
                >
            </div>
            @error('form.email') <span class="block text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
        </div>

        <!-- Password -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label for="password" class="block font-bold text-slate-400">Contraseña</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-[10px] text-blue-400 hover:underline font-bold" wire:navigate>
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-500">
                    <i class="fa-solid fa-lock text-sm"></i>
                </span>
                <input 
                    wire:model="form.password" 
                    id="password" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password" 
                    placeholder="••••••••"
                    class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-900/60 border border-slate-700 rounded-xl focus:border-blue-500 focus:outline-none transition-colors text-white"
                >
            </div>
            @error('form.password') <span class="block text-[10px] text-rose-500 font-semibold">{{ $message }}</span> @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label class="inline-flex items-center cursor-pointer select-none">
                <input 
                    wire:model="form.remember" 
                    id="remember" 
                    type="checkbox" 
                    name="remember"
                    class="rounded bg-slate-950 border-slate-700 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-800"
                >
                <span class="ms-2 text-xs font-semibold text-slate-450">Recordarme en este dispositivo</span>
            </label>
        </div>

        <!-- Submit Button -->
        <button 
            type="submit" 
            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-xs py-3 rounded-xl shadow-lg shadow-blue-500/10 flex items-center justify-center gap-2 transition-all font-outfit mt-6 uppercase tracking-wider"
        >
            <span wire:loading wire:target="login" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            Iniciar Sesión
        </button>
    </form>
</div>
