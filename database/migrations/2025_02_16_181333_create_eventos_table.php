<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->enum('tipo', ['conferencia', 'taller']);
            $table->date('fecha');
            $table->Time('hora');
            $table->unsignedBigInteger('ponente_id');
            $table->integer('cupo_maximo');
            $table->timestamps();

            $table->foreign('ponente_id')->references('id')->on('ponente')->onDelete('cascade');
           });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eventos');
    }
}