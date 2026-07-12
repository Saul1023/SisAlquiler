<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('payment_frequency')->default('Mensual'); // 'Mensual', 'Quincenal', 'Semanal'
            $table->integer('payment_day'); // Día de pago (ej: 5 para el 5 de cada mes)
            $table->decimal('base_price', 10, 2);
            $table->decimal('additional_services_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->json('services')->nullable(); // Guardar JSON de servicios contratados
            $table->string('status')->default('Activo'); // 'Activo', 'Finalizado', 'Cancelado'
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
