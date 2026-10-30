<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdjustSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('paystack_plan');
            $table->dropColumn('ends_at');
            $table->dropColumn('trial_ends_at');
            $table->double('amount', 8, 2);
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
            $table->string('paystack_plan');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->dropColumn('amount');
        });
    }
}
