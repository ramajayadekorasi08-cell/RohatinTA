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
        Schema::table('students', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->after('name')->nullable();
            $table->string('birth_place')->after('gender')->nullable();
            $table->date('birth_date')->after('birth_place')->nullable();
            $table->text('address')->after('class')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['gender', 'birth_place', 'birth_date', 'address']);
        });
    }
};
