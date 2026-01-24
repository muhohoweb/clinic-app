<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Visit;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $totalPatients = Patient::count();
        $totalVisits = Visit::count();
        $totalPayments = Payment::count();

        // Payment statistics
        $totalCharged = Payment::sum('amount_charged');
        $totalPaid = Payment::sum('amount_paid');
        $totalBalance = Payment::sum('balance');

        // Today's statistics
        $todayVisits = Visit::whereDate('created_at', today())->count();
        $todayPayments = Payment::whereDate('created_at', today())->sum('amount_paid');

        // This month's statistics
        $monthVisits = Visit::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $monthRevenue = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount_paid');

        // Recent visits (last 5)
        $recentVisits = Visit::with('patient')
            ->latest()
            ->take(5)
            ->get();

        // Recent payments (last 5)
        $recentPayments = Payment::with(['visit.patient'])
            ->latest()
            ->take(5)
            ->get();

        // Gender distribution
        $malePatients = Patient::where('gender', 'male')->count();
        $femalePatients = Patient::where('gender', 'female')->count();

        // Payment methods breakdown
        $paymentsByMethod = Payment::select('mode_of_payment', DB::raw('count(*) as count'))
            ->groupBy('mode_of_payment')
            ->get();

        // Top diagnoses (last 30 days)
        $topDiagnoses = Visit::select('diagnosis', DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalPatients' => $totalPatients,
                'totalVisits' => $totalVisits,
                'totalPayments' => $totalPayments,
                'totalCharged' => $totalCharged,
                'totalPaid' => $totalPaid,
                'totalBalance' => $totalBalance,
                'todayVisits' => $todayVisits,
                'todayPayments' => $todayPayments,
                'monthVisits' => $monthVisits,
                'monthRevenue' => $monthRevenue,
                'malePatients' => $malePatients,
                'femalePatients' => $femalePatients,
            ],
            'recentVisits' => $recentVisits,
            'recentPayments' => $recentPayments,
            'paymentsByMethod' => $paymentsByMethod,
            'topDiagnoses' => $topDiagnoses,
        ]);
    }
}