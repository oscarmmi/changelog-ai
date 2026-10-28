<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('changelog_summaries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('changelog_job_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('breaking');
            $table->json('features');
            $table->json('fixes');
            $table->longText('rewritten_markdown')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('changelog_summaries');
    }
};
