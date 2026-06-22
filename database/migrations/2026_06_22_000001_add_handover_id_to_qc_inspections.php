<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('qc_inspections', function (Blueprint $table) {
            $table->foreignId('handover_id')->nullable()->after('production_order_id')
                  ->constrained('handovers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('qc_inspections', function (Blueprint $table) {
            $table->dropForeign(['handover_id']);
            $table->dropColumn('handover_id');
        });
    }
};
