<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoogleSheet extends Migration
{
    public function up()
    {
        Schema::create('google_sheet_lists', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid');
            $table->string('list_name');
            $table->string('connection_name');
            $table->string('connection_type');
            $table->string('sheet_name');
            $table->string('google_sheet_id');
            $table->timestamp('last_sync')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('google_sheet_lists');
    }
}
