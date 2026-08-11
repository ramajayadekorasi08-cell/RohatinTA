<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Category;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => Complaint::count(),
            'active' => Complaint::whereIn('status', ['pending', 'approved', 'on_progress'])->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'avg_rating' => Rating::avg('rating') ?? 0,
        ];

        $statusChart = [
            'labels' => ['Pending', 'Approved', 'On Progress', 'Resolved', 'Rejected'],
            'data' => [
                Complaint::where('status', 'pending')->count(),
                Complaint::where('status', 'approved')->count(),
                Complaint::where('status', 'on_progress')->count(),
                Complaint::where('status', 'resolved')->count(),
                Complaint::where('status', 'rejected')->count(),
            ],
        ];

        $categoryData = Category::withCount('complaints')->get();
        $categoryChart = [
            'labels' => $categoryData->pluck('name')->toArray(),
            'data' => $categoryData->pluck('complaints_count')->toArray(),
        ];

        $monthlyData = Complaint::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $trendChart = [
            'labels' => $monthlyData->pluck('month')->toArray(),
            'data' => $monthlyData->pluck('count')->toArray(),
        ];

        $highPriorityComplaints = Complaint::where('priority_level', 'high')
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->with(['parentUser', 'category'])
            ->orderBy('priority_score', 'desc')
            ->take(5)
            ->get();

        return view('principal.dashboard', compact('stats', 'statusChart', 'categoryChart', 'trendChart', 'highPriorityComplaints'));
    }
}
