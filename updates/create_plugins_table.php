<?php namespace Awebsome\PrivatePlugins\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreatePluginsTable extends Migration
{
    public function up()
    {
        Schema::create('awebsome_privateplugins_plugins', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('code')->unique();
            $table->string('repository');               # Repo Provider
            $table->string('repo_name');                # Repo Name
            $table->boolean('private')->default(0);     # Repo Private
            $table->string('user')->nullable();         # Repo User
            $table->string('password')->nullable();     # Repo Password

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('awebsome_privateplugins_plugins');
    }
}
