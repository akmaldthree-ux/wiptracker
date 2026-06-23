<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty_needed', 12, 4);
            $table->timestamps();
            $table->unique(['production_order_id','raw_material_id']);
        });

        Schema::table('production_orders', function (Blueprint $table) {
            $table->boolean('materials_approved')->default(false)->after('notes');
            $table->foreignId('materials_approved_by')->nullable()->constrained('users')->nullOnDelete()->after('materials_approved');
            $table->timestamp('materials_approved_at')->nullable()->after('materials_approved_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_requirements');
        Schema::table('production_orders', function (Blueprint $table) {
            $table->dropColumn(['materials_approved','materials_approved_by','materials_approved_at']);
        });
    }
};
