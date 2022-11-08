<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('sources', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('claims', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
}
