<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('custody_wallets')) {
            Schema::create('custody_wallets', function (Blueprint $table) {
                $table->id();
                $table->string('chain', 20);
                $table->string('asset', 20)->default('USDT');
                $table->string('label')->nullable();
                $table->string('address', 255)->unique();
                $table->text('public_key')->nullable();
                $table->text('encrypted_private_key')->nullable();
                $table->tinyInteger('is_active')->default(1);
                $table->tinyInteger('is_treasury')->default(0);
                $table->timestamps();
                $table->index(['chain', 'asset', 'is_active']);
            });
        }

        if (!Schema::hasTable('merchant_deposit_addresses')) {
            Schema::create('merchant_deposit_addresses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->string('chain', 20);
                $table->string('asset', 20)->default('USDT');
                $table->string('address', 255)->unique();
                $table->string('memo', 120)->nullable();
                $table->string('status', 30)->default('assigned');
                $table->unsignedBigInteger('last_checked_block')->nullable();
                $table->timestamp('assigned_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
                $table->index(['invoice_id', 'status']);
            });
        }

        if (!Schema::hasTable('onchain_deposits')) {
            Schema::create('onchain_deposits', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('chain', 20);
                $table->string('asset', 20)->default('USDT');
                $table->string('address', 255);
                $table->string('tx_hash', 191)->unique();
                $table->unsignedBigInteger('block_number')->nullable();
                $table->integer('confirmations')->default(0);
                $table->decimal('amount', 28, 8)->default(0);
                $table->string('status', 30)->default('detected');
                $table->json('payload')->nullable();
                $table->timestamp('detected_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamps();
                $table->index(['invoice_id', 'status']);
                $table->index(['chain', 'asset', 'status']);
            });
        }

        if (!Schema::hasTable('onchain_payouts')) {
            Schema::create('onchain_payouts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('payout_id')->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('from_wallet_id')->nullable();
                $table->string('chain', 20);
                $table->string('asset', 20)->default('USDT');
                $table->string('to_address', 255);
                $table->string('tx_hash', 191)->nullable()->unique();
                $table->decimal('amount', 28, 8)->default(0);
                $table->decimal('fee', 28, 8)->default(0);
                $table->integer('confirmations')->default(0);
                $table->string('status', 30)->default('queued');
                $table->string('error_message')->nullable();
                $table->json('payload')->nullable();
                $table->timestamp('broadcasted_at')->nullable();
                $table->timestamp('confirmed_at')->nullable();
                $table->timestamps();
                $table->index(['chain', 'asset', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('onchain_payouts');
        Schema::dropIfExists('onchain_deposits');
        Schema::dropIfExists('merchant_deposit_addresses');
        Schema::dropIfExists('custody_wallets');
    }
};
