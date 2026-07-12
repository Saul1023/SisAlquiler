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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_method'); // 'Efectivo', 'Transferencia', 'Tarjeta', 'Otro'
            $table->string('period_covered'); // Formato YYYY-MM
            $table->string('receipt_number')->nullable();
            $table->string('status')->default('Pagado'); // 'Pagado', 'Pendiente', 'Atrasado', 'Anulado'
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
