<?php

namespace App\Http\Controllers\Principal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Complaint;
use App\Models\Rating;

class ReportController extends Controller
{
    public function complaints(Request $request)
    {
        $query = Complaint::with(['parentUser', 'student', 'category'])->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $complaints = $query->paginate(20)->withQueryString();
        $categories = \App\Models\Category::all();

        return view('principal.reports.complaints', compact('complaints', 'categories'));
    }

    public function evaluation(Request $request)
    {
        $query = Rating::with(['complaint', 'user'])->orderBy('created_at', 'desc');
        
        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $evaluations = $query->paginate(20)->withQueryString();

        $avgRating = Rating::avg('rating');
        $ratingCounts = [
            5 => Rating::where('rating', 5)->count(),
            4 => Rating::where('rating', 4)->count(),
            3 => Rating::where('rating', 3)->count(),
            2 => Rating::where('rating', 2)->count(),
            1 => Rating::where('rating', 1)->count(),
        ];

        return view('principal.reports.evaluation', compact('evaluations', 'avgRating', 'ratingCounts'));
    }
}
