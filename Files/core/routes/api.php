<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['api.key.auth', 'api.request.log', 'api.idempotency'])->group(function () {
    Route::controller('Api\V1\InvoiceController')->prefix('invoices')->group(function () {
        Route::get('/', 'index')->middleware('api.scope:invoices:read');
        Route::post('/', 'store')->middleware('api.scope:invoices:write');
        Route::get('/{id}', 'show')->middleware('api.scope:invoices:read');
    });

    Route::controller('Api\V1\PayoutController')->prefix('payouts')->group(function () {
        Route::post('/', 'store')->middleware(['api.scope:payouts:write', 'payouts.enabled']);
        Route::get('/{id}', 'show')->middleware('api.scope:payouts:read');
    });

    Route::controller('Api\V1\PayoutBatchController')->prefix('payouts')->group(function () {
        Route::post('/batch', 'store')->middleware(['api.scope:payouts:write', 'payouts.enabled']);
        Route::post('/batch/csv', 'storeCsv')->middleware(['api.scope:payouts:write', 'payouts.enabled']);
    });

    Route::controller('Api\V1\PayoutBatchController')->prefix('payout-batches')->group(function () {
        Route::post('/', 'store')->middleware(['api.scope:payouts:write', 'payouts.enabled']);
        Route::get('/{id}', 'show')->middleware('api.scope:payouts:read');
    });

    Route::controller('Api\V1\BalanceController')->group(function () {
        Route::get('/balances', 'show')->middleware('api.scope:balances:read');
    });

    Route::controller('Api\V1\SettlementController')->prefix('settlement-preferences')->group(function () {
        Route::get('/', 'show')->middleware('api.scope:balances:read');
        Route::post('/', 'update')->middleware('api.scope:payouts:write');
    });

    Route::controller('Api\V1\ApiKeyController')->prefix('api-keys')->group(function () {
        Route::get('/', 'index')->middleware('api.scope:keys:read');
        Route::post('/', 'store')->middleware('api.scope:keys:write');
    });

    Route::controller('Api\V1\WebhookController')->prefix('webhooks')->group(function () {
        Route::post('/', 'register')->middleware('api.scope:webhooks:write');
        Route::post('/{id}/rotate-secret', 'rotateSecret')->middleware('api.scope:webhooks:write');
        Route::post('/test', 'test')->middleware('api.scope:webhooks:write');
    });

    Route::controller('Api\V1\PluginController')->prefix('plugins')->group(function () {
        Route::post('/handshake', 'handshake');
    });
});
