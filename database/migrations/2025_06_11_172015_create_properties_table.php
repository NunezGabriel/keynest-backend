<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id('property_id');
            $table->uuid('user_id'); // FK a users
            $table->string('title', 150);
            $table->text('description');
            $table->enum('property_type', ['casa', 'departamento']);
            $table->decimal('price', 12, 2);
            $table->decimal('maintenance_cost', 12, 2)->nullable();
            $table->boolean('is_rent');
            $table->integer('square_meters');
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->boolean('pets_allowed')->default(false);
            $table->string('location', 255);
            $table->enum('status', ['disponible', 'vendido', 'alquilado', 'inactivo'])->default('disponible');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
