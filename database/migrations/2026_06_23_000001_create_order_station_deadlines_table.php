<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_station_deadlines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->date('target_date');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['production_order_id','station_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_station_deadlines');
    }
};
