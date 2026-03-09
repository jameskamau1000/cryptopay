<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('webhook_endpoints')) {
            Schema::create('webhook_endpoints', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name')->default('Default endpoint');
                $table->string('url');
                $table->string('secret', 120);
                $table->json('events')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamp('last_rotated_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'status']);
            });
        }

        if (!Schema::hasTable('webhook_events')) {
            Schema::create('webhook_events', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('event_id', 80)->unique();
                $table->string('event_type', 80);
                $table->string('resource_type', 40);
                $table->unsignedBigInteger('resource_id')->nullable();
                $table->json('payload');
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->index(['user_id', 'event_type']);
            });
        }

        if (!Schema::hasTable('webhook_deliveries')) {
            Schema::create('webhook_deliveries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('webhook_event_id');
                $table->unsignedBigInteger('webhook_endpoint_id');
                $table->string('status', 20)->default('queued');
                $table->integer('attempts')->default(0);
                $table->smallInteger('response_code')->nullable();
                $table->longText('response_body')->nullable();
                $table->timestamp('next_retry_at')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamps();
                $table->index(['webhook_endpoint_id', 'status']);
            });
        }

        if (!Schema::hasTable('risk_cases')) {
            Schema::create('risk_cases', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('entity_type', 40);
                $table->unsignedBigInteger('entity_id')->nullable();
                $table->string('rule_code', 80);
                $table->string('severity', 20)->default('medium');
                $table->string('status', 20)->default('open');
                $table->string('summary');
                $table->json('evidence')->nullable();
                $table->unsignedBigInteger('assigned_admin_id')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();
                $table->index(['status', 'severity']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_cases');
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('webhook_endpoints');
    }
};
