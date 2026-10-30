<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEditorialTracking extends Migration
{
    private $statuses = ['Draft', 'Submitted', 'Review', 'Revision', 'Approved', 'Rejected'];

    /**
     * Run the migrations.
     *
     * Adds the few fields the admin dashboard needs to report on editors,
     * reviewers, review reports and deadlines, plus a "Revision" status
     * (manuscript sent back to the author).
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->unsignedInteger('editor_id')->nullable();
            $table->unsignedInteger('reviewer_id')->nullable();
            $table->date('review_deadline')->nullable();
            $table->timestamp('report_received_at')->nullable();

            $table->foreign('editor_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('reviewer_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('reviewer')->default(false);
        });

        $this->setStatuses($this->statuses);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('manuscripts')->where('status', 'Revision')->update(['status' => 'Review']);
        $this->setStatuses(array_values(array_diff($this->statuses, ['Revision'])));

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('reviewer');
        });

        Schema::table('manuscripts', function (Blueprint $table) {
            $table->dropForeign(['editor_id']);
            $table->dropForeign(['reviewer_id']);
            $table->dropColumn(['editor_id', 'reviewer_id', 'review_deadline', 'report_received_at']);
        });
    }

    /**
     * Laravel's enum() is a varchar + CHECK constraint on Postgres and a real
     * ENUM on MySQL, so widening it needs driver-specific SQL.
     */
    private function setStatuses(array $values)
    {
        $list = "'".implode("','", $values)."'";

        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE manuscripts DROP CONSTRAINT IF EXISTS manuscripts_status_check');
            DB::statement("ALTER TABLE manuscripts ADD CONSTRAINT manuscripts_status_check CHECK (status::text IN ($list))");
        } else {
            DB::statement("ALTER TABLE manuscripts MODIFY status ENUM($list) NOT NULL");
        }
    }
}