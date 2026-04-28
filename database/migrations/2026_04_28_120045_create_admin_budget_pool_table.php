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
        Schema::create('admin_budget_pools', function (Blueprint $table) {
            $table->id();
            $table->decimal('total_allocated', 15, 2)->default(0);
            $table->decimal('total_budget', 15, 2)->default(0);
            $table->decimal('available_funds', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_budget_pools');
    }
};
