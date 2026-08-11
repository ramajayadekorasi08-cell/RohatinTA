<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleGowa(Request $request)
    {
        $secret = config('services.gowa.webhook_secret');

        if ($secret && $request->header('X-Webhook-Secret') !== $secret) {
            Log::warning('GOWA Webhook: Invalid secret');
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        Log::info('GOWA Webhook received', $request->all());

        return response()->json(['status' => 'ok']);
    }
}
