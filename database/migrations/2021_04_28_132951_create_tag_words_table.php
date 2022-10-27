<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_words', function (Blueprint $table) {
            $table->id();
            $table->string('phrase');
            $table->enum('type', ['client_plus_word', 'client_minus_word', 'operator_plus_word', 'operator_minus_word']);
            // $table->foreignId('tag_id')->constrained();
            $table->unsignedBigInteger('tag_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag_words');
    }
}
