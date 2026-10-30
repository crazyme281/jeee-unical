<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVolumeAndIssueToSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->integer('volume')->after('quantity');
            $table->integer('issue')->default(0)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('volume');
            $table->dropColumn('issue');
        });
    }
}
