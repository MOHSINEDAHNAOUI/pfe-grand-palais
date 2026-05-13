<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('dynamic_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('price', 10, 2);
            $table->string('reason')->nullable(); // holiday, event, weekend
            $table->timestamps();

            $table->unique(['room_type_id', 'date']);
        });
    }
    public function down(): void { Schema::dropIfExists('dynamic_prices'); }
};