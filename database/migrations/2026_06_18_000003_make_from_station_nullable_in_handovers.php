<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropForeign(['from_station_id']);
            $table->unsignedBigInteger('from_station_id')->nullable()->change();
            $table->foreign('from_station_id')->references('id')->on('stations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropForeign(['from_station_id']);
            $table->unsignedBigInteger('from_station_id')->nullable(false)->change();
            $table->foreign('from_station_id')->references('id')->on('stations')->cascadeOnDelete();
        });
    }
};
