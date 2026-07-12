<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Rooms\RoomsIndex;
use App\Livewire\Tenants\TenantsIndex;
use App\Livewire\Contracts\ContractsIndex;
use App\Livewire\Contracts\ContractWizard;
use App\Livewire\Payments\PaymentsIndex;
use App\Livewire\Reports\ReportsIndex;
use App\Livewire\Settings\SettingsIndex;
use App\Livewire\Actions\Logout;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    
    // Al Rent Modules
    Route::get('rooms', RoomsIndex::class)->name('rooms.index');
    Route::get('tenants', TenantsIndex::class)->name('tenants.index');
    Route::get('contracts', ContractsIndex::class)->name('contracts.index');
    Route::get('contracts/wizard', ContractWizard::class)->name('contracts.wizard');
    Route::get('payments', PaymentsIndex::class)->name('payments.index');
    Route::get('reports', ReportsIndex::class)->name('reports.index');
    Route::get('settings', SettingsIndex::class)->name('settings.index');

    // Logout POST Route
    Route::post('logout', function (Logout $logout) {
        $logout();
        return redirect('/');
    })->name('logout');
});

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
