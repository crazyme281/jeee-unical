<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('doi')->nullable();
            $table->string('title');
            $table->longText('abstract');
            $table->text('keywords');
            $table->text('authors');    // Comma separated authors' names
            $table->string('pages')->nullable();
            $table->string('file');
            $table->bigInteger('views')->default(0);
            $table->bigInteger('downloads')->default(0);
            $table->boolean('featured')->default('1');;
            $table->unsignedInteger('issue_id');
            $table->foreign('issue_id')
                ->references('id')->on('issues')
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
        Schema::dropIfExists('articles');
    }
}
