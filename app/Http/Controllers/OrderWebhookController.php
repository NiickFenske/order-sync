<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class OrderWebhookController extends Controller
{
    public function create(Request $request): Response
    {
        Log::info('orders/create webhook received', [
            'body' => $request->getContent(),
        ]);

        return response()->noContent(200);
    }
}
