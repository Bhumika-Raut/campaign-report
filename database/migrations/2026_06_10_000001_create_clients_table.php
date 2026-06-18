<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // Explicit ID from excel sheet (e.g. 1933)
            $table->string('email')->unique();
            $table->string('company')->nullable();
            $table->date('account_created');
            $table->date('last_created_post')->nullable();
            $table->string('plan'); // e.g. Free, Premium
            $table->string('status'); // e.g. Live, Not Live
            $table->integer('consumed')->default(0);
            $table->integer('remaining')->default(0);
            $table->date('last_shared')->nullable();
            $table->integer('clicks')->default(0);
            $table->integer('posts')->default(0);
            $table->integer('comments')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('clients');
    }
};
