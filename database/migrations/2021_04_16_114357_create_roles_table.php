<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 20);
            $table->string('title', 100);
        });

        Role::insert([
            ['id' => 1, 'slug' => 'superadmin', 'title' => 'Супер админ'],
            ['id' => 2, 'slug' => 'manager',    'title' => 'Менеджер'],
            ['id' => 3, 'slug' => 'client',     'title' => 'Клиент']
        ]);

        Schema::table('users', function(Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function(Blueprint $table) {
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }
}
