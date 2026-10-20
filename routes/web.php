<?php

use App\Http\Controllers\OrderWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/webhooks/orders-create', [OrderWebhookController::class, 'create']);
