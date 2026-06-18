<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('removed_campaigns', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary(); // Campaign ID from sheet
            $table->string('client_name');
            $table->string('company')->nullable();
            $table->string('agency')->nullable();
            $table->string('campaign_title');
            $table->string('paid_or_not')->nullable();
            $table->bigInteger('potential_reach')->default(0);
            $table->integer('total_shares')->default(0);
            $table->double('reach_per_share')->default(0);
            $table->integer('total_clicks')->default(0);
            $table->double('clicks_per_share')->default(0);
            $table->integer('total_comments')->default(0);
            $table->double('comments_per_share')->default(0);
            $table->integer('total_likes')->default(0);
            $table->double('likes_per_share')->default(0);
            $table->integer('total_posts')->default(0);
            $table->integer('reshare')->default(0);
            $table->integer('registrations')->default(0);
            $table->double('emv')->default(0);
            $table->double('direct_savings')->default(0);
            $table->double('total_return')->default(0);
            $table->double('roi')->default(0);
            $table->double('registration_per_share')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('removed_campaigns');
    }
};
