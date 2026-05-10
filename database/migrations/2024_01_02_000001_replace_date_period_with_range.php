<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('research_records', function (Blueprint $table) {
            $table->date('date_from')->nullable()->after('theme');
            $table->date('date_to')->nullable()->after('date_from');
        });

        // Migrate existing date_period data into date_from
        \DB::statement('UPDATE research_records SET date_from = date_period WHERE date_period IS NOT NULL');

        Schema::table('research_records', function (Blueprint $table) {
            $table->dropColumn('date_period');
        });
    }

    public function down(): void
    {
        Schema::table('research_records', function (Blueprint $table) {
            $table->date('date_period')->nullable()->after('theme');
        });
        \DB::statement('UPDATE research_records SET date_period = date_from WHERE date_from IS NOT NULL');
        Schema::table('research_records', function (Blueprint $table) {
            $table->dropColumn(['date_from','date_to']);
        });
    }
};
