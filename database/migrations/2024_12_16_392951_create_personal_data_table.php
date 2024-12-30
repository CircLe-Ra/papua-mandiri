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
        Schema::create('personal_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('religion_id')->constrained('religions')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('ktp_number', 16)->unique();
            $table->string('full_name');
            $table->enum('gender', ['M', 'F']);
            $table->date('birth_date');
            $table->string('birth_place');
            $table->string('address');
            $table->string('rt');
            $table->string('rw');
            $table->foreignId('sub_district_id')->constrained('sub_districts')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('district_id')->constrained('districts')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('regency_id')->constrained('regencies')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('province_id')->constrained('provinces')->restrictOnDelete()->cascadeOnUpdate();
            $table->string('marital_status');
            $table->string('occupation');
            $table->string('nationality');
            $table->string('blood_type', 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_data');
    }
};
