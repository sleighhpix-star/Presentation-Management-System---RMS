<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_records', function (Blueprint $table) {
            $table->id();

            // ── Core identifiers ──────────────────────────
            $table->string('year', 10);
            $table->string('quarter', 10);
            $table->string('constituent', 100);

            // ── Research info ──────────────────────────────
            $table->string('research_title');
            $table->string('reporter', 250);
            $table->string('college_campus', 250);
            $table->string('title_of_project')->nullable();
            $table->text('authors');
            $table->string('source_of_fund', 200)->nullable();
            $table->string('status', 50)->default('Proposed');

            // ── SDG & support ──────────────────────────────
            $table->json('sdg');                               // ['SDG 1', 'SDG 3', …]
            $table->string('presentation_support', 10)->default('N/A'); // Yes | No | N/A

            // ── Event details ──────────────────────────────
            $table->string('title_of_event')->nullable();
            $table->string('theme')->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('venue')->nullable();
            $table->string('classification', 50)->nullable();  // International | National | Regional | Local

            // ── Organizer ──────────────────────────────────
            $table->string('organizer_name', 250)->nullable();
            $table->string('organizer_email', 250)->nullable();
            $table->string('website')->nullable();

            // ── Photo ──────────────────────────────────────
            $table->string('photo')->nullable();               // stored path relative to storage/app/public

            $table->timestamps();
            $table->softDeletes();

            // Indexes for common filter columns
            $table->index(['year', 'quarter']);
            $table->index('status');
            $table->index('constituent');
            $table->index('college_campus');
            $table->index('classification');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_records');
    }
};
