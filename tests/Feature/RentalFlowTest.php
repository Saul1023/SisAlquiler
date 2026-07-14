<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Contract;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Livewire\Livewire;
use App\Livewire\Rooms\RoomsIndex;
use App\Livewire\Tenants\TenantsIndex;
use App\Livewire\Contracts\ContractWizard;
use App\Livewire\Payments\PaymentsIndex;

class RentalFlowTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create authenticated user
        $this->user = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_an_authenticated_user_can_create_a_room()
    {
        $this->actingAs($this->user);

        Livewire::test(RoomsIndex::class)
            ->set('room_number', '301')
            ->set('floor', 'Piso 3')
            ->set('capacity', 2)
            ->set('price', 650.00)
            ->set('status', 'Disponible')
            ->set('description', 'Habitación de prueba')
            ->call('save');

        $this->assertDatabaseHas('rooms', [
            'room_number' => '301',
            'floor' => 'Piso 3',
            'price' => 650.00,
        ]);
    }

    public function test_an_authenticated_user_can_register_a_tenant()
    {
        $this->actingAs($this->user);

        Livewire::test(TenantsIndex::class)
            ->set('name', 'Roberto Gomez')
            ->set('identity_number', '12345678')
            ->set('phone', '78945612')
            ->set('email', 'roberto@test.com')
            ->set('status', 'Activo')
            ->call('save');

        $this->assertDatabaseHas('tenants', [
            'name' => 'Roberto Gomez',
            'identity_number' => '12345678',
        ]);
    }

    public function test_a_user_can_create_a_contract_using_the_wizard()
    {
        $this->actingAs($this->user);

        // Create initial room and tenant
        $room = Room::create([
            'room_number' => '401',
            'floor' => 'Piso 4',
            'capacity' => 2,
            'price' => 500.00,
            'status' => 'Disponible'
        ]);

        $tenant = Tenant::create([
            'name' => 'Lucia Paz',
            'identity_number' => '87654321',
            'phone' => '71122334',
            'status' => 'Activo'
        ]);

        // Run Wizard Flow
        Livewire::test(ContractWizard::class)
            ->set('selectedRoomId', $room->id)
            ->call('nextStep')
            ->set('selectedTenantId', $tenant->id)
            ->call('nextStep')
            ->set('start_date', now()->format('Y-m-d'))
            ->set('payment_frequency', 'Mensual')
            ->set('payment_day', 5)
            ->set('services.wifi', true) // Wifi service
            ->call('nextStep')
            ->call('saveContract');

        // Assert contract created
        $this->assertDatabaseHas('contracts', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => 'Activo',
        ]);

        // Room status updated to occupied by ContractObserver
        $this->assertEquals('Ocupado', $room->fresh()->status);
    }

    public function test_a_user_can_record_a_payment_for_a_contract()
    {
        $this->actingAs($this->user);

        // Create Room, Tenant & Contract
        $room = Room::create([
            'room_number' => '501',
            'floor' => 'Piso 5',
            'capacity' => 2,
            'price' => 500.00,
            'status' => 'Ocupado'
        ]);

        $tenant = Tenant::create([
            'name' => 'Pedro Paz',
            'identity_number' => '8520369',
            'phone' => '71122334',
            'status' => 'Activo'
        ]);

        $contract = Contract::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'start_date' => now()->subMonth()->format('Y-m-d'),
            'payment_frequency' => 'Mensual',
            'payment_day' => 5,
            'base_price' => 500.00,
            'total_price' => 500.00,
            'status' => 'Activo',
        ]);

        // Record payment
        Livewire::test(PaymentsIndex::class)
            ->set('contract_id', $contract->id)
            ->set('amount', 500.00)
            ->set('period_covered', '2026-06')
            ->set('payment_method', 'Efectivo')
            ->set('receipt_number', 'REC-TEST-01')
            ->set('status', 'Pagado')
            ->call('save');

        $this->assertDatabaseHas('payments', [
            'contract_id' => $contract->id,
            'amount' => 500.00,
            'period_covered' => '2026-06',
            'status' => 'Pagado',
        ]);
    }

    public function test_it_can_generate_structured_system_context_for_gemini()
    {
        // Seed a room
        Room::create([
            'room_number' => '601',
            'floor' => 'Piso 6',
            'capacity' => 1,
            'price' => 400.00,
            'status' => 'Disponible'
        ]);

        $context = \App\Services\GeminiService::generateDatabaseContext();

        $this->assertIsArray($context);
        $this->assertArrayHasKey('inventario_cuartos', $context);
        $this->assertArrayHasKey('inquilinos', $context);
        $this->assertArrayHasKey('contratos_activos', $context);
        $this->assertArrayHasKey('deudas_pendientes', $context);
        
        $rooms = $context['inventario_cuartos'];
        $this->assertNotEmpty($rooms);
        $this->assertEquals('601', $rooms[0]['cuarto']);
    }
}
