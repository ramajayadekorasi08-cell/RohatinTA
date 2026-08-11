<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $complaints = Complaint::where('parent_id', $user->id);

        $stats = [
            'total' => $complaints->count(),
            'pending' => (clone $complaints)->where('status', 'pending')->count(),
            'on_progress' => (clone $complaints)->whereIn('status', ['approved', 'on_progress'])->count(),
            'resolved' => (clone $complaints)->where('status', 'resolved')->count(),
        ];

        $recentComplaints = Complaint::where('parent_id', $user->id)
            ->with(['category', 'student'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('parent.dashboard', compact('stats', 'recentComplaints'));
    }
}
