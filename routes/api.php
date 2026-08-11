<?php

use Illuminate\Support\Facades\Route;

Route::prefix('webhooks')->group(function () {
    Route::post('/gowa', [\App\Http\Controllers\Api\WebhookController::class, 'handleGowa'])
        ->name('api.webhooks.gowa');
});
