<?php

namespace App\Http\Controllers\Admin;

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
            'pending' => Complaint::where('status', 'pending')->count(),
            'approved' => Complaint::where('status', 'approved')->count(),
            'on_progress' => Complaint::where('status', 'on_progress')->count(),
            'resolved' => Complaint::where('status', 'resolved')->count(),
            'rejected' => Complaint::where('status', 'rejected')->count(),
        ];

        $recentComplaints = Complaint::with(['parentUser', 'category', 'student'])
            ->where('status', '!=', Complaint::STATUS_RESOLVED)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Chart data - by status
        $statusChart = [
            'labels' => ['Pending', 'Approved', 'On Progress', 'Resolved', 'Rejected'],
            'data' => [
                $stats['pending'], $stats['approved'], $stats['on_progress'],
                $stats['resolved'], $stats['rejected'],
            ],
        ];

        // Chart data - by category
        $categoryData = Category::withCount('complaints')->get();
        $categoryChart = [
            'labels' => $categoryData->pluck('name')->toArray(),
            'data' => $categoryData->pluck('complaints_count')->toArray(),
        ];

        // Chart data - monthly trend (last 6 months)
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

        return view('admin.dashboard', compact('stats', 'recentComplaints', 'statusChart', 'categoryChart', 'trendChart'));
    }
}
