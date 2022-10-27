<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claims', function (Blueprint $table) {

            $table->id();

            $table->string('code')->unique();

            $table->datetime('datetime'); // дата и время заявки
            $table->integer('duration')->nullable(); // продолжительность в секундах (если не звонок, то ячейки пустая)

            $table->foreignId('source_id')->constrained(); // источник

            $table->string('phone'); // пользователь (номер телефона)
            /** @todo возможно стоить переименовать это поле как user или что то в этом плане */

            $table->string('redirected_to')->nullable(); // перенапрвлен на (номер телефона)

            $table->enum('manager_check', ['targeted', 'untargeted', 'unidentified'])->default('unidentified'); // целевой по мнению менеджера
            $table->enum('client_check', ['targeted', 'untargeted', 'unchecked'])->default('unchecked'); // целевой по мнению клиента

            $table->text('manager_comment')->nullable();
            $table->text('client_comment')->nullable();

            $table->json('dialog')->nullable(); // диалог в виде json

            $table->foreignId('project_id')->constrained(); // проект

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claims');
    }
}
