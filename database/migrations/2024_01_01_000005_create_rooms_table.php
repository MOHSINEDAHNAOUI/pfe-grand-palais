<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('number')->unique(); // 101, 102, etc.
            $table->integer('floor');
            $table->decimal('surface', 8, 2); // m²
            $table->string('view')->nullable(); // mer, jardin, ville
            $table->boolean('smoking')->default(false);
            $table->enum('status', ['available', 'occupied', 'maintenance', 'blocked'])->default('available');
            $table->text('notes')->nullable();
            $table->decimal('price_override', 10, 2)->nullable(); // surcharge prix
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'room_type_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('rooms'); }
};