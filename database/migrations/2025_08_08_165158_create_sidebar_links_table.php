<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // migration: create_clerk_sidebar_links_table.php
        Schema::create('sidebar_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('clerk_id');
            $table->string('link_key');  // identifier for sidebar link
            $table->string('description')->nullable();  // description of the link
            $table->timestamps();

            $table
                ->foreign('clerk_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sidebar_links');
    }
};
