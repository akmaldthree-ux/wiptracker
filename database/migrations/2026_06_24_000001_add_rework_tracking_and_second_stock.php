<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->boolean('is_rework')->default(false)->after('notes');
            $table->foreignId('parent_handover_id')->nullable()->constrained('handovers')->nullOnDelete()->after('is_rework');
            $table->enum('rework_result', ['pending','completed','failed'])->default('pending')->after('parent_handover_id');
        });

        Schema::create('second_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_station_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignId('handover_item_id')->constrained()->cascadeOnDelete();
            $table->integer('qty');
            $table->enum('status', ['available','sold','scrapped'])->default('available');
            $table->decimal('discount_price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('second_stock');
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropColumn(['is_rework','parent_handover_id','rework_result']);
        });
    }
};
