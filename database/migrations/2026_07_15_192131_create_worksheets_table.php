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
        Schema::create('worksheets', function (Blueprint $table) {
            $table->id();
            $table->enum('priority', ['normal', 'urgent', 'downtime'])->default('normal');

            $table->text('description');
            $table->date('due_date')->nullable();
            $table->date('finish_date')->nullable();

            $table->foreignId('device_id')->constrained();

            $table->unsignedBigInteger('creator_id');
            $table->foreign('creator_id')->references('id')->on('users');

            $table->unsignedBigInteger('repairer_id')->nullable();
            $table->foreign('repairer_id')->references('id')->on('users');

            $table->text('attachments');

            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worksheets');
    }
};
