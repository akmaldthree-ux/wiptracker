<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->string('photo_sent')->nullable()->after('condition_notes');
            $table->string('photo_received')->nullable()->after('photo_sent');
        });
        Schema::table('handover_items', function (Blueprint $table) {
            $table->string('photo_reject')->nullable()->after('reject_notes');
        });
    }

    public function down(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropColumn(['photo_sent', 'photo_received']);
        });
        Schema::table('handover_items', function (Blueprint $table) {
            $table->dropColumn('photo_reject');
        });
    }
};
