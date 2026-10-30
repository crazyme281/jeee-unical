<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\DB;

class ModifyUsersTable extends Migration
{
    /**
     * Split Names
     *
     * @return string
     */
    public function splitName($name)
    {
        $splitName = explode(' ', $name, 2); // Restricts it to only 2 values, for names like Billy Bob Jones
        $first_name = $splitName[0];
        $last_name = !empty($splitName[1]) ? $splitName[1] : ''; // If last name doesn't exist, make it empty

        return [
            'first_name' => $first_name,
            'last_name' => $last_name
        ];
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->after('name');
            $table->renameColumn('name', 'first_name');
            $table->string('department')->nullable();
            $table->string('institution')->nullable();
            $table->string('position')->nullable();
        });

//        // Split previous name
//        DB::table('users')->whereNull('last_name')->update([
//            'first_name' => DB::raw(
//                '(SELECT SUBSTRING(first_name, 1, LOCATE(" ", first_name) - 1))'
//            ),
//            'last_name' => DB::raw(
//                '(SELECT SUBSTRING(first_name, LOCATE(" ", first_name) + 1, LENGTH(first_name)))'
//            )
//        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        // Combine names
//        DB::table('users')->whereNotNull('last_name')->update([
//            'first_name' => DB::raw('CONCAT(first_name, " ", last_name)'),
//        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_name');
            $table->renameColumn('first_name', 'name');
            $table->dropColumn('department');
            $table->dropColumn('institution');
            $table->dropColumn('position');
        });
    }
}
