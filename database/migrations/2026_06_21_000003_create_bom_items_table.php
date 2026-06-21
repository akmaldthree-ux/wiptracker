<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bom_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty_per_unit', 10, 4)->comment('Jumlah bahan per 1 pcs produk');
            $table->decimal('waste_percentage', 5, 2)->default(5.00)->comment('% waste/susut');
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->unique(['product_id','raw_material_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('bom_items'); }
};
