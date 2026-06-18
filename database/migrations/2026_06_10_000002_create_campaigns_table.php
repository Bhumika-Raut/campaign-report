<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // Campaign ID from sheet (e.g. 1003)
            $table->string('client_name'); // Links to company name or client name
            $table->string('campaign_name');
            $table->date('date');
            $table->integer('potential_reach')->default(0);
            $table->integer('total_shares')->default(0);
            $table->integer('total_clicks')->default(0);
            $table->integer('total_comments')->default(0);
            $table->integer('total_likes')->default(0);
            $table->integer('total_posts')->default(0);
            $table->boolean('ugc_enabled')->default(false);
            $table->integer('form_submissions')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('campaigns');
    }
};
