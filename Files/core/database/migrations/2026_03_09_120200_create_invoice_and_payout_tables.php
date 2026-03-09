<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('api_key_id')->nullable();
                $table->string('reference', 80)->unique();
                $table->string('external_reference', 120)->nullable();
                $table->string('currency', 20);
                $table->decimal('amount', 28, 8);
                $table->decimal('paid_amount', 28, 8)->default(0);
                $table->string('settlement_currency', 20)->nullable();
                $table->decimal('settlement_amount', 28, 8)->default(0);
                $table->string('status', 30)->default('draft');
                $table->string('type', 20)->default('one_time');
                $table->string('redirect_url')->nullable();
                $table->string('cancel_url')->nullable();
                $table->string('ipn_url')->nullable();
                $table->string('checkout_url')->nullable();
                $table->json('customer')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
            });
        }

        if (!Schema::hasTable('invoice_line_items')) {
            Schema::create('invoice_line_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id');
                $table->string('name');
                $table->decimal('unit_price', 28, 8)->default(0);
                $table->decimal('quantity', 28, 8)->default(1);
                $table->decimal('total', 28, 8)->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->index('invoice_id');
            });
        }

        if (!Schema::hasTable('payouts')) {
            Schema::create('payouts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('reference', 80)->unique();
                $table->decimal('amount', 28, 8);
                $table->string('asset', 20)->default('USDT');
                $table->string('network', 30)->nullable();
                $table->string('destination', 255);
                $table->decimal('fee_amount', 28, 8)->default(0);
                $table->decimal('net_amount', 28, 8)->default(0);
                $table->string('status', 30)->default('pending');
                $table->string('batch_reference', 80)->nullable();
                $table->string('failure_reason')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
            });
        }

        if (!Schema::hasTable('payout_batches')) {
            Schema::create('payout_batches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('reference', 80)->unique();
                $table->string('source', 20)->default('csv');
                $table->string('status', 30)->default('uploaded');
                $table->integer('total_items')->default(0);
                $table->integer('processed_items')->default(0);
                $table->integer('failed_items')->default(0);
                $table->decimal('total_amount', 28, 8)->default(0);
                $table->json('summary')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('payout_batch_items')) {
            Schema::create('payout_batch_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('batch_id');
                $table->unsignedBigInteger('payout_id')->nullable();
                $table->string('destination', 255)->nullable();
                $table->decimal('amount', 28, 8)->default(0);
                $table->string('asset', 20)->default('USDT');
                $table->string('network', 30)->nullable();
                $table->string('status', 30)->default('queued');
                $table->string('error_message')->nullable();
                $table->timestamps();
                $table->index('batch_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payout_batch_items');
        Schema::dropIfExists('payout_batches');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('invoice_line_items');
        Schema::dropIfExists('invoices');
    }
};
