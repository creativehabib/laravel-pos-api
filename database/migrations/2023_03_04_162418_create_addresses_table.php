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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->morphs('addressable');
            $table->string('address')->nullable();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('district_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->tinyInteger('status');
            $table->string('landmark')->nullable();
            $table->tinyInteger('type')->nullable()->comment('1 = supplier, 2 = customer present, 3 = customer permanent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
