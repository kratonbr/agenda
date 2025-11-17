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
    Schema::create('appointments', function (Blueprint $table) {
        $table->id(); // O ID único do agendamento
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('customer_name'); // Nome do cliente
        $table->string('phone')->nullable(); // Telefone (pode ser vazio/nulo)
        $table->dateTime('scheduled_at'); // Data e Hora do agendamento
        $table->string('status')->default('agendado'); // Status inicial
        $table->text('notes')->nullable(); // Observações extras
        
        // Na próxima etapa adicionaremos o user_id (dono do agendamento)
        
        $table->timestamps(); // Cria a data de criação e atualização
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
