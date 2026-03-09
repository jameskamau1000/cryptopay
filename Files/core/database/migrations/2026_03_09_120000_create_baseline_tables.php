<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('general_settings')) {
            Schema::create('general_settings', function (Blueprint $table) {
                $table->id();
                $table->string('site_name')->default('CryptoPay');
                $table->string('cur_text', 20)->default('USD');
                $table->string('cur_sym', 20)->default('$');
                $table->tinyInteger('currency_format')->default(0);
                $table->string('available_version', 20)->nullable();
                $table->tinyInteger('force_ssl')->default(0);
                $table->string('active_template', 40)->default('basic');
                $table->tinyInteger('maintenance_mode')->default(0);
                $table->integer('paginate_number')->default(20);
                $table->json('off_days')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('languages')) {
            Schema::create('languages', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->tinyInteger('is_default')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('pages')) {
            Schema::create('pages', function (Blueprint $table) {
                $table->id();
                $table->string('tempname', 40)->nullable();
                $table->string('slug')->unique();
                $table->string('name')->nullable();
                $table->tinyInteger('is_default')->default(0);
                $table->longText('secs')->nullable();
                $table->longText('seo_content')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('frontends')) {
            Schema::create('frontends', function (Blueprint $table) {
                $table->id();
                $table->string('data_keys')->nullable();
                $table->longText('data_values')->nullable();
                $table->longText('seo_content')->nullable();
                $table->string('tempname', 40)->nullable();
                $table->string('slug')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('firstname', 40)->nullable();
                $table->string('lastname', 40)->nullable();
                $table->string('email', 120)->nullable();
                $table->string('username', 40)->nullable();
                $table->string('password')->nullable();
                $table->decimal('balance', 28, 8)->default(0);
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('ev')->default(1);
                $table->tinyInteger('sv')->default(1);
                $table->tinyInteger('kv')->default(1);
                $table->string('public_api_key', 120)->nullable();
                $table->string('secret_api_key', 120)->nullable();
                $table->string('test_public_api_key', 120)->nullable();
                $table->string('test_secret_api_key', 120)->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name', 40)->nullable();
                $table->string('email', 120)->nullable();
                $table->string('username', 40)->nullable();
                $table->string('password');
                $table->string('image')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gateways')) {
            Schema::create('gateways', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('code')->unique();
                $table->string('name')->nullable();
                $table->string('alias')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('crypto')->default(0);
                $table->longText('gateway_parameters')->nullable();
                $table->longText('extra')->nullable();
                $table->longText('input_form')->nullable();
                $table->longText('supported_currencies')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('gateway_currencies')) {
            Schema::create('gateway_currencies', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('method_code');
                $table->string('name')->nullable();
                $table->string('currency', 20)->nullable();
                $table->string('symbol', 20)->nullable();
                $table->longText('gateway_parameter')->nullable();
                $table->decimal('min_amount', 28, 8)->default(0);
                $table->decimal('max_amount', 28, 8)->default(0);
                $table->decimal('percent_charge', 28, 8)->default(0);
                $table->decimal('fixed_charge', 28, 8)->default(0);
                $table->decimal('rate', 28, 8)->default(1);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
                $table->index('method_code');
            });
        }

        if (!Schema::hasTable('extensions')) {
            Schema::create('extensions', function (Blueprint $table) {
                $table->id();
                $table->string('act')->nullable();
                $table->longText('shortcode')->nullable();
                $table->longText('script')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Baseline migration is intentionally non-destructive.
    }
};
