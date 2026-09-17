<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alert;
use Illuminate\Http\Request;

class AlertApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Alert::with(['device', 'alertRule']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            $query->active();
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        $alerts = $query->latest('triggered_at')->paginate(20);

        return response()->json($alerts);
    }

    public function acknowledge(Alert $alert)
    {
        $alert->acknowledge(auth()->user());

        return response()->json(['message' => 'Alert acknowledged.']);
    }

    public function resolve(Alert $alert)
    {
        $alert->resolve();

        return response()->json(['message' => 'Alert resolved.']);
    }
}
