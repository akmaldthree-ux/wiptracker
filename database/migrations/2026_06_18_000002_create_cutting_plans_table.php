<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cutting_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_no', 30)->unique();
            $table->foreignId('production_order_id')->constrained('production_orders')->cascadeOnDelete();
            $table->date('planned_date');
            $table->decimal('marker_length', 8, 2)->nullable()->comment('Panjang marker dalam meter');
            $table->decimal('fabric_width', 6, 2)->nullable()->comment('Lebar kain dalam cm');
            $table->integer('total_layers')->nullable()->comment('Jumlah lapisan kain');
            $table->integer('planned_qty')->default(0);
            $table->integer('actual_qty')->nullable();
            $table->decimal('efficiency', 5, 2)->nullable()->comment('Efisiensi marker dalam %');
            $table->string('shift', 20)->nullable()->comment('Shift pagi/siang/malam');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('draft')->comment('draft|scheduled|in_progress|completed|cancelled');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('cutting_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('bundle_no', 30)->unique();
            $table->foreignId('cutting_plan_id')->constrained('cutting_plans')->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained('skus');
            $table->integer('qty');
            $table->string('status', 20)->default('cut')->comment('cut|bundled|sent_to_sewing');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cutting_bundles');
        Schema::dropIfExists('cutting_plans');
    }
};
