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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id('appointment_id');
            $table->unsignedBigInteger('visitor_id');
            $table->unsignedBigInteger('officer_id');
            $table->string('name');
            $table->enum('status', ['Active', 'Cancelled', 'Deactivated', 'Completed'])->default('Active');
            $table->date('date');       
            $table->time('StartTime');   
            $table->time('EndTime');     
            $table->timestamp('AddedOn')->useCurrent();          
            $table->timestamp('LastUpdatedOn')->nullable()->useCurrentOnUpdate();
            $table->timestamps();
            // Foreign keys
            $table->foreign('visitor_id')->references('visitor_id')->on('visitors')->onDelete('cascade');
            $table->foreign('officer_id')->references('officer_id')->on('officers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
