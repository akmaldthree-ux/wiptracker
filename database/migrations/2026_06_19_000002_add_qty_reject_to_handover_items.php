<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('handover_items', function (Blueprint $table) {
            $table->integer('qty_reject')->default(0)->after('qty_received');
            $table->text('reject_notes')->nullable()->after('qty_reject');
        });
    }

    public function down(): void
    {
        Schema::table('handover_items', function (Blueprint $table) {
            $table->dropColumn(['qty_reject', 'reject_notes']);
        });
    }
};
