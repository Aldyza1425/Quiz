<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('duration_seconds')->default(0)->after('duration_minutes');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('media_type')->default('none')->after('type'); // none, image, video, pdf, youtube
            $table->string('media_url')->nullable()->after('media_type');
        });

        // Migrate existing data if any
        \DB::table('quizzes')->update([
            'duration_seconds' => \DB::raw('duration_minutes * 60')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn('duration_seconds');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['media_type', 'media_url']);
        });
    }
};
