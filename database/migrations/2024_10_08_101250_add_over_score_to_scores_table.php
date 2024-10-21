<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            // Add the over_score field after the score field
            $table->float('over_score', 8, 2)->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('scores', function (Blueprint $table) {
            // Drop the over_score field if the migration is rolled back
            $table->dropColumn('over_score');
        });
    }
};
