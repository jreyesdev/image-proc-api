<?php

use App\Models\Album;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageManipulationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('image_manipulations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('path');
            $table->string('type', 25);
            $table->text('data');
            $table->text('output_path')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->foreignIdFor(User::class, 'user_id')->nullable();
            $table->foreignIdFor(Album::class, 'album_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('image_manipulations');
    }
}
