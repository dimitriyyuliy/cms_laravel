<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->unsigned()->default('0');
            $table->string('title');
            $table->index('title');
            $table->string('slug')->unique();
            $table->string('status', 32)->default(config('add.page_statuses')[0]);
            $table->smallInteger('sort')->unsigned()->default('500');
            $table->text('description')->nullable();
            $table->text('body')->nullable();
            $table->string('img')->nullable()->default(config('admin.imgProductDefault'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
