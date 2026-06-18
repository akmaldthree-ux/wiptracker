<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sewing_locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->integer('capacity')->nullable()->comment('Kapasitas produksi per hari');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('handovers', function (Blueprint $table) {
            $table->foreignId('sewing_location_id')->nullable()->constrained('sewing_locations')->nullOnDelete()->after('to_station_id');
        });
    }

    public function down(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\SewingLocation::class);
            $table->dropColumn('sewing_location_id');
        });
        Schema::dropIfExists('sewing_locations');
    }
};
