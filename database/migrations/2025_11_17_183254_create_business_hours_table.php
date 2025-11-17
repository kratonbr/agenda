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
    Schema::create('business_hours', function (Blueprint $table) {
        $table->id();
        
        // ESTA É A LINHA QUE ESTAVA FALTANDO OU NÃO FOI SALVA:
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
        
        $table->tinyInteger('day'); // 0=Domingo a 6=Sábado
        $table->time('open_at')->nullable();
        $table->time('close_at')->nullable();
        $table->boolean('is_open')->default(true);
        $table->timestamps();
        
        $table->unique(['user_id', 'day']);
    });
}
    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};
