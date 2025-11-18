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
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('professional_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('duration')->nullable();
            $table->string('payment_status')->default('pendente');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['business_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['professional_id']);
            $table->dropColumn(['business_id', 'customer_id', 'professional_id', 'duration', 'payment_status']);
        });
    }
};
