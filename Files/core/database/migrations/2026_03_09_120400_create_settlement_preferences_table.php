<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('settlement_preferences')) {
            Schema::create('settlement_preferences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('preferred_asset', 20)->default('USDT');
                $table->string('network', 30)->nullable();
                $table->string('destination', 255)->nullable();
                $table->tinyInteger('auto_settle')->default(1);
                $table->decimal('min_settlement_amount', 28, 8)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('settlement_preferences');
    }
};
