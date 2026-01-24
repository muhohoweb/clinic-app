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
        $totalPatients = Patient::query()->count();
        $totalVisits = Visit::query()->count();
        $totalPayments = Payment::query()->count();

        // Payment statistics
        $totalCharged = Payment::query()->sum('amount_charged');
        $totalPaid = Payment::query()->sum('amount_paid');
        $totalBalance = Payment::query()->sum('balance');

        // Today's statistics
        $todayVisits = Visit::query()->whereDate('created_at', today())->count();
        $todayPayments = Payment::query()->whereDate('created_at', today())->sum('amount_paid');

        // This month's statistics
        $monthVisits = Visit::query()->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $monthRevenue = Payment::query()->whereMonth('created_at', now()->month)
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
        $malePatients = Patient::query()->where('gender', 'male')->count();
        $femalePatients = Patient::query()->where('gender', 'female')->count();

        // Payment methods breakdown
        $paymentsByMethod = Payment::query()->select('mode_of_payment', DB::raw('count(*) as count'))
            ->groupBy('mode_of_payment')
            ->get();

        // Top diagnoses (last 30 days)
        $topDiagnoses = Visit::query()->select('diagnosis', DB::raw('count(*) as count'))
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