<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\ModerationLog;

class AddSkippedField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('moderation_logs', function ($table) {
            $table->tinyInteger('is_skipped')->after('type')->default(ModerationLog::IS_NO_SKIPPED);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('moderation_logs', function (Blueprint $table) {
            $table->dropColumn('is_skipped');
        });
    }
}
