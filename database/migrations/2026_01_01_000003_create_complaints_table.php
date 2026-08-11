<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->foreignId('parent_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('evidence_path')->nullable();
            $table->enum('status', ['pending', 'approved', 'on_progress', 'resolved', 'rejected'])->default('pending');
            $table->decimal('priority_score', 8, 4)->nullable();
            $table->enum('priority_level', ['high', 'medium', 'low'])->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
