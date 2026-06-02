<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('project_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        // seed some common statuses
        DB::table('project_statuses')->insert([
            ['name' => 'planning', 'color' => '#6c757d', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'active', 'color' => '#0d6efd', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'completed', 'color' => '#198754', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'on_hold', 'color' => '#ffc107', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_statuses');
    }
};
