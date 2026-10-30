<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateManuscriptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manuscripts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->longText('abstract');
            $table->text('keywords');
            $table->string('primary_file');
            $table->text('supporting_files');
            $table->enum('status', ['Draft', 'Submitted', 'Review', 'Approved', 'Rejected']);
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onUpdate('cascade')
                ->onDelete('restrict');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manuscripts');
    }
}
