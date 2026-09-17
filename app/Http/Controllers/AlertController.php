<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Device;
use Illuminate\Http\Request;

class AlertController extends Controller
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

        $alerts = $query->latest('triggered_at')->paginate(20)->withQueryString();

        return view('alerts.index', compact('alerts'));
    }

    public function acknowledge(Alert $alert)
    {
        $alert->acknowledge(auth()->user());

        return redirect()->back()->with('success', 'Alert acknowledged.');
    }

    public function resolve(Alert $alert)
    {
        $alert->resolve();

        return redirect()->back()->with('success', 'Alert resolved.');
    }
}
