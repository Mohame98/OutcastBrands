<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->morphs('reportable');

            $table->enum('reason', [
                'Sexual content',
                'Violent or repulsive content',
                'Hateful or abusive content',
                'Harassment or bullying',
                'Misinformation',
                'Child abuse',
                'Promotes terrorism',
                'Spam or misleading',
                'Legal issue',
                'Captions issue'
            ]);

            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
