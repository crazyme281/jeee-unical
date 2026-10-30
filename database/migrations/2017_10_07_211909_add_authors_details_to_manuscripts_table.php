<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAuthorsDetailsToManuscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->text('authors');
        });

        // Drop Author's table
        Schema::dropIfExists('authors');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manuscripts', function (Blueprint $table) {
            $table->dropColumn('authors');
        });
    }
}
