<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Per-item rework destination station
        Schema::table('handover_items', function (Blueprint $table) {
            $table->foreignId('rework_to_station_id')->nullable()->constrained('stations')->nullOnDelete()->after('reject_notes');
        });

        // Track rework re-purposing to different order/series
        Schema::create('rework_repurposes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rework_handover_id')->constrained('handovers')->cascadeOnDelete();
            $table->foreignId('original_order_id')->constrained('production_orders')->cascadeOnDelete();
            $table->foreignId('new_order_id')->constrained('production_orders')->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained()->cascadeOnDelete();
            $table->integer('qty');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rework_repurposes');
        Schema::table('handover_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('rework_to_station_id');
        });
    }
};
