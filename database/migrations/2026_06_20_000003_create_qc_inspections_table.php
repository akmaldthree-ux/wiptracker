<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('qc_inspections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->cascadeOnDelete();
            $table->foreignId('inspector_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('inspected_at');
            $table->enum('status', ['pass', 'fail', 'conditional']);
            $table->integer('total_checked')->default(0);
            $table->integer('total_defect')->default(0);
            $table->decimal('defect_rate', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('photo_evidence')->nullable();
            $table->timestamps();
        });

        Schema::create('qc_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qc_inspection_id')->constrained('qc_inspections')->cascadeOnDelete();
            $table->string('checklist_item');
            $table->enum('result', ['ok', 'fail', 'na'])->default('na');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('qc_checklist_items');
        Schema::dropIfExists('qc_inspections');
    }
};
