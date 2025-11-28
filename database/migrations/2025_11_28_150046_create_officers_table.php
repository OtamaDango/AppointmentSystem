<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('officers', function (Blueprint $table) {
            $table->id('officer_id');
            $table->string('name');
            $table->unsignedBigInteger('post_id');
            $table->string('status')->default('Inactive');
            $table->timestamps();
            $table->time('WorkStartTime');
            $table->time('WorkEndTime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officers');
    }
};
