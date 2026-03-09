<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('merchant_api_keys')) {
            Schema::create('merchant_api_keys', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name')->default('Default API Key');
                $table->string('public_key', 120)->unique();
                $table->string('secret_key', 120);
                $table->json('scopes')->nullable();
                $table->tinyInteger('is_test')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamp('last_used_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
            });
        }

        if (!Schema::hasTable('api_request_logs')) {
            Schema::create('api_request_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('api_key_id')->nullable();
                $table->string('request_id', 80)->nullable();
                $table->string('method', 12);
                $table->string('endpoint');
                $table->smallInteger('status_code')->default(200);
                $table->string('ip_address', 64)->nullable();
                $table->integer('duration_ms')->default(0);
                $table->longText('request_headers')->nullable();
                $table->longText('request_body')->nullable();
                $table->longText('response_body')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'created_at']);
                $table->index('request_id');
            });
        }

        if (!Schema::hasTable('api_idempotency_keys')) {
            Schema::create('api_idempotency_keys', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('idempotency_key', 120);
                $table->string('endpoint');
                $table->string('method', 12);
                $table->string('request_hash', 64);
                $table->smallInteger('status_code')->nullable();
                $table->longText('response_body')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->unique(['idempotency_key', 'endpoint', 'method'], 'idem_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('api_idempotency_keys');
        Schema::dropIfExists('api_request_logs');
        Schema::dropIfExists('merchant_api_keys');
    }
};
