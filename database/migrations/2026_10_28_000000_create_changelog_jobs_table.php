<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('changelog_jobs', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('raw_text');
            $table->string('status')->default('pending')->index();
            $table->boolean('is_demo')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('changelog_jobs');
    }
};
