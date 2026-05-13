<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration {
    public function up(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('confirmation_token')->nullable()->after('qr_code');
            $table->string('payment_method')->default('on_arrival')->after('confirmation_token');
        });
    }
    public function down(): void {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['confirmation_token', 'payment_method']);
        });
    }
};