<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => ['required', 'string', 'url'],
            'keys.auth' => ['required', 'string'],
            'keys.p256dh' => ['required', 'string'],
        ]);

        $request->user()->updatePushSubscription(
            endpoint: $request->input('endpoint'),
            publicKey: $request->input('keys.p256dh'),
            authToken: $request->input('keys.auth'),
            contentEncoding: $request->input('contentEncoding', 'aesgcm'),
        );

        return response()->json(['status' => 'subscribed']);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'endpoint' => ['required', 'string', 'url'],
        ]);

        $request->user()->deletePushSubscription($request->input('endpoint'));

        return response()->json(['status' => 'unsubscribed']);
    }
}
