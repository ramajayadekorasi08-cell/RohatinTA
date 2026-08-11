<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ahp_criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->decimal('weight', 8, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('ahp_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_row_id')->constrained('ahp_criteria')->onDelete('cascade');
            $table->foreignId('criteria_col_id')->constrained('ahp_criteria')->onDelete('cascade');
            $table->decimal('value', 8, 4);
            $table->timestamps();
        });

        Schema::create('ahp_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->onDelete('cascade');
            $table->foreignId('criteria_id')->constrained('ahp_criteria')->onDelete('cascade');
            $table->decimal('score', 8, 4)->default(0);
            $table->decimal('weighted_score', 8, 4)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ahp_results');
        Schema::dropIfExists('ahp_comparisons');
        Schema::dropIfExists('ahp_criteria');
    }
};
