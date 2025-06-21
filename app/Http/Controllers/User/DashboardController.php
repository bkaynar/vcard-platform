<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\VcardVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Kullanıcının VCard ziyaret istatistikleri
        $totalVisits = VcardVisit::where('user_id', $user->id)->count();
        $todayVisits = VcardVisit::where('user_id', $user->id)
            ->whereDate('visited_at', today())
            ->count();

        $weeklyVisits = VcardVisit::where('user_id', $user->id)
            ->whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $monthlyVisits = VcardVisit::where('user_id', $user->id)
            ->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year)
            ->count();

        // Son 7 günlük ziyaret grafiği
        $last7DaysVisits = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = VcardVisit::where('user_id', $user->id)
                ->whereDate('visited_at', $date)
                ->count();

            $last7DaysVisits[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count
            ];
        }

        // Son ziyaretçiler
        $recentVisits = VcardVisit::where('user_id', $user->id)
            ->orderBy('visited_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($visit) {
                return [
                    'ip_address' => $visit->ip_address,
                    'visited_at' => $visit->visited_at->format('d.m.Y H:i'),
                    'user_agent' => $visit->user_agent ? substr($visit->user_agent, 0, 50) . '...' : 'Bilinmiyor'
                ];
            });

        return Inertia::render('User/Dashboard', [
            'stats' => [
                'total_visits' => $totalVisits,
                'today_visits' => $todayVisits,
                'weekly_visits' => $weeklyVisits,
                'monthly_visits' => $monthlyVisits,
            ],
            'charts' => [
                'last_7_days_visits' => $last7DaysVisits,
            ],
            'recent_visits' => $recentVisits,
            'profile_url' => route('vcard.show', ['username' => $user->username]),
        ]);
    }

    public function statistics()
    {
        $user = Auth::user();

        // Detaylı VCard ziyaret istatistikleri
        $totalVisits = VcardVisit::where('user_id', $user->id)->count();
        $todayVisits = VcardVisit::where('user_id', $user->id)
            ->whereDate('visited_at', today())
            ->count();

        $weeklyVisits = VcardVisit::where('user_id', $user->id)
            ->whereBetween('visited_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        $monthlyVisits = VcardVisit::where('user_id', $user->id)
            ->whereMonth('visited_at', now()->month)
            ->whereYear('visited_at', now()->year)
            ->count();

        // Son 30 günlük ziyaret grafiği
        $last30DaysVisits = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = VcardVisit::where('user_id', $user->id)
                ->whereDate('visited_at', $date)
                ->count();

            $last30DaysVisits[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $date->format('d M'),
                'count' => $count
            ];
        }

        // Saatlik ziyaret dağılımı (bugün) - SQLite uyumlu
        $hourlyVisits = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $count = VcardVisit::where('user_id', $user->id)
                ->whereDate('visited_at', today())
                ->whereRaw("strftime('%H', visited_at) = ?", [sprintf('%02d', $hour)])
                ->count();

            $hourlyVisits[] = [
                'hour' => $hour,
                'label' => sprintf('%02d:00', $hour),
                'count' => $count
            ];
        }

        // En popüler saatler (genel) - SQLite uyumlu
        $popularHours = VcardVisit::where('user_id', $user->id)
            ->selectRaw("strftime('%H', visited_at) as hour, COUNT(*) as count")
            ->groupByRaw("strftime('%H', visited_at)")
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'hour' => sprintf('%02d:00', (int)$item->hour),
                    'count' => $item->count
                ];
            });

        // Tüm ziyaretçiler (sayfalama ile)
        $allVisits = VcardVisit::where('user_id', $user->id)
            ->orderBy('visited_at', 'desc')
            ->paginate(20)
            ->through(function ($visit) {
                return [
                    'id' => $visit->id,
                    'ip_address' => $visit->ip_address,
                    'visited_at' => $visit->visited_at->format('d.m.Y H:i:s'),
                    'user_agent' => $visit->user_agent ?: 'Bilinmiyor',
                    'location' => $visit->location ?: 'Bilinmiyor'
                ];
            });

        return Inertia::render('User/Statistics', [
            'stats' => [
                'total_visits' => $totalVisits,
                'today_visits' => $todayVisits,
                'weekly_visits' => $weeklyVisits,
                'monthly_visits' => $monthlyVisits,
            ],
            'charts' => [
                'last_30_days_visits' => $last30DaysVisits,
                'hourly_visits' => $hourlyVisits,
                'popular_hours' => $popularHours,
            ],
            'all_visits' => $allVisits,
            'profile_url' => route('vcard.show', ['username' => $user->username]),
        ]);
    }
}
