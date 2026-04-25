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
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->nullable()->default(null);
        });
        
        // Update existing accounts to belong to the first user
        $firstUser = \App\Models\User::first();
        if ($firstUser) {
            \DB::table('accounts')->update(['user_id' => $firstUser->id]);
        }
        
        // Now add the foreign key constraint
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->default(null)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
