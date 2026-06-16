<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 10)->unique();
            $table->integer('order_sequence')->default(0);
            $table->text('description')->nullable();
            $table->integer('bottleneck_threshold')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('pic_stasiun');
            $table->foreignId('station_id')->nullable()->constrained()->nullOnDelete();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->string('hex_code', 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->string('type', 10)->default('letter');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('series', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 20)->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('skus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('series_id')->constrained()->cascadeOnDelete();
            $table->foreignId('color_id')->constrained()->cascadeOnDelete();
            $table->foreignId('size_id')->constrained()->cascadeOnDelete();
            $table->string('sku_code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('series_id')->nullable()->constrained()->nullOnDelete();
            $table->date('target_date');
            $table->string('status', 20)->default('draft');
            $table->decimal('selling_price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('production_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained()->cascadeOnDelete();
            $table->integer('target_qty')->default(0);
            $table->timestamps();
        });

        Schema::create('wip_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained()->cascadeOnDelete();
            $table->foreignId('station_id')->constrained()->cascadeOnDelete();
            $table->integer('qty_in')->default(0);
            $table->integer('qty_out')->default(0);
            $table->integer('qty_reject')->default(0);
            $table->date('input_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('handovers', function (Blueprint $table) {
            $table->id();
            $table->string('handover_no')->unique();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('from_station_id')->constrained('stations')->cascadeOnDelete();
            $table->foreignId('to_station_id')->constrained('stations')->cascadeOnDelete();
            $table->string('status', 20)->default('pending');
            $table->foreignId('initiated_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->string('condition_notes')->nullable();
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('handover_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('handover_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sku_id')->constrained()->cascadeOnDelete();
            $table->integer('qty_sent');
            $table->integer('qty_received')->nullable();
            $table->integer('discrepancy')->nullable();
            $table->text('discrepancy_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('unit', 20);
            $table->string('category', 30)->default('lainnya');
            $table->string('color')->nullable();
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('material_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty', 10, 2);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_price', 15, 2);
            $table->string('supplier')->nullable();
            $table->string('po_no')->nullable();
            $table->date('receipt_date');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('material_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('raw_material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->decimal('qty_planned', 10, 2)->default(0);
            $table->decimal('qty_actual', 10, 2)->default(0);
            $table->date('allocation_date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('material_cost_plan', 15, 2)->default(0);
            $table->decimal('process_cost_plan', 15, 2)->default(0);
            $table->decimal('overhead_cost_plan', 15, 2)->default(0);
            $table->decimal('total_plan', 15, 2)->default(0);
            $table->decimal('material_cost_actual', 15, 2)->default(0);
            $table->decimal('process_cost_actual', 15, 2)->default(0);
            $table->decimal('overhead_cost_actual', 15, 2)->default(0);
            $table->decimal('total_actual', 15, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('cost_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('budget_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20);
            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->foreignId('station_id')->nullable()->constrained()->nullOnDelete();
            $table->date('entry_date');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type', 20)->default('info');
            $table->boolean('is_read')->default(false);
            $table->string('link')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('model_type')->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('cost_entries');
        Schema::dropIfExists('budgets');
        Schema::dropIfExists('material_allocations');
        Schema::dropIfExists('material_receipts');
        Schema::dropIfExists('raw_materials');
        Schema::dropIfExists('handover_items');
        Schema::dropIfExists('handovers');
        Schema::dropIfExists('wip_entries');
        Schema::dropIfExists('production_order_items');
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('skus');
        Schema::dropIfExists('series');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('colors');
        Schema::dropIfExists('products');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('stations');
    }
};
