<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('handover_items', function (Blueprint $table) {
            $table->enum('reject_type', ['rework', 'second', 'scrap'])->nullable()->after('qty_reject');
        });
    }

    public function down(): void
    {
        Schema::table('handover_items', function (Blueprint $table) {
            $table->dropColumn('reject_type');
        });
    }
};
