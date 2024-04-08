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
        Schema::table('recipes', function (Blueprint $table) {
            $table->foreignId('user_id')->default(0)->constrained()->onDelete('cascade');
        });

        // Assign the first user to all existing recipes
        $user = DB::table('users')->first();
        DB::table('recipes')->update(['user_id' => $user->id]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recipes', function (Blueprint $table) {
            // $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
