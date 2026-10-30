<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropPathColumnFromFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $files = DB::table('files')->get();
        foreach ($files as $file){
            DB::table('files')->where('id', $file->id)
                ->update([
                    'link' => parse_url($file->path)['path'] . '/' . $file->link
                ]);
        }

        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('path');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('files', function (Blueprint $table) {
            $table->string('path')->nullable();
        });
    }
}
