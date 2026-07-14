<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsIndex extends Component
{
    public $activeTab = 'empresa'; // 'empresa', 'email', 'seguridad'

    // Tab 1: Empresa & Precios
    public $company_name = '';
    public $company_address = '';
    public $company_phone = '';
    public $currency = '';
    public $grace_days = 3;
    public $wifi_price = '';
    public $parking_price = '';
    public $cleaning_price = '';
    public $water_light_price = '';

    // Tab 2: SMTP Config
    public $mail_host = '';
    public $mail_port = '';
    public $mail_username = '';
    public $mail_password = '';
    public $mail_encryption = '';
    public $mail_from_address = '';

    // Tab 3: Security Change Password
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';

    // Tab 4: AI config
    public $gemini_api_key = '';

    public function mount()
    {
        // Load Company Info & Pricing
        $this->company_name = Setting::get('company_name', 'Alquileres El Sol');
        $this->company_address = Setting::get('company_address', 'Av. Principal #123');
        $this->company_phone = Setting::get('company_phone', '78945612');
        $this->currency = Setting::get('currency', 'Bs.');
        $this->grace_days = (int)Setting::get('grace_days', 3);
        $this->wifi_price = Setting::get('wifi_price', '50.00');
        $this->parking_price = Setting::get('parking_price', '100.00');
        $this->cleaning_price = Setting::get('cleaning_price', '80.00');
        $this->water_light_price = Setting::get('water_light_price', '70.00');

        // Load SMTP config
        $this->mail_host = Setting::get('mail_host', '127.0.0.1');
        $this->mail_port = Setting::get('mail_port', '2525');
        $this->mail_username = Setting::get('mail_username', '');
        $this->mail_password = Setting::get('mail_password', '');
        $this->mail_encryption = Setting::get('mail_encryption', 'tls');
        $this->mail_from_address = Setting::get('mail_from_address', 'hello@example.com');

        // Load AI config
        $this->gemini_api_key = Setting::get('gemini_api_key', '');
    }

    public function saveCompany()
    {
        $this->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'company_phone' => 'required|string|max:20',
            'currency' => 'required|string|max:10',
            'grace_days' => 'required|integer|min:0',
            'wifi_price' => 'required|numeric|min:0',
            'parking_price' => 'required|numeric|min:0',
            'cleaning_price' => 'required|numeric|min:0',
            'water_light_price' => 'required|numeric|min:0',
        ]);

        Setting::set('company_name', $this->company_name);
        Setting::set('company_address', $this->company_address);
        Setting::set('company_phone', $this->company_phone);
        Setting::set('currency', $this->currency);
        Setting::set('grace_days', (string)$this->grace_days);
        Setting::set('wifi_price', (string)$this->wifi_price);
        Setting::set('parking_price', (string)$this->parking_price);
        Setting::set('cleaning_price', (string)$this->cleaning_price);
        Setting::set('water_light_price', (string)$this->water_light_price);

        $this->dispatch('swal:toast', type: 'success', message: 'Configuraciones de empresa guardadas.');
    }

    public function saveMail()
    {
        $this->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'required|string',
            'mail_from_address' => 'required|email',
        ]);

        Setting::set('mail_host', $this->mail_host);
        Setting::set('mail_port', (string)$this->mail_port);
        Setting::set('mail_username', $this->mail_username);
        Setting::set('mail_password', $this->mail_password);
        Setting::set('mail_encryption', $this->mail_encryption);
        Setting::set('mail_from_address', $this->mail_from_address);

        $this->dispatch('swal:toast', type: 'success', message: 'Configuraciones de correo guardadas.');
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        // Get authenticated user and change password
        $user = User::find(auth()->id());
        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';

        $this->dispatch('swal:toast', type: 'success', message: 'Contraseña actualizada con éxito.');
    }

    public function saveAI()
    {
        $this->validate([
            'gemini_api_key' => 'nullable|string|max:100',
        ]);

        Setting::set('gemini_api_key', $this->gemini_api_key ?? '');

        $this->dispatch('swal:toast', type: 'success', message: 'Configuraciones de IA guardadas con éxito.');
    }

    public function render()
    {
        return view('livewire.settings.settings-index')->layout('layouts.app');
    }
}
