<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('client_summaries', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('email');
            $table->integer('campaigns_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('form_submissions_count')->default(0);
            $table->integer('credits_consumed')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('client_summaries');
    }
};
