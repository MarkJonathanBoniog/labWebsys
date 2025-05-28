<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MakeHasOtrAndHasForm137NullableInRecordsTable extends Migration
{
    public function up()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->boolean('hasOtr')->nullable()->change();
            $table->boolean('hasForm')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('records', function (Blueprint $table) {
            $table->boolean('hasOtr')->nullable(false)->change();
            $table->boolean('hasForm')->nullable(false)->change();
        });
    }
}

