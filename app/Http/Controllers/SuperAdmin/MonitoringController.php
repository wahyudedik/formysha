<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\AuditLog;
use App\Models\ClinicalNote;
use App\Models\Media;
use App\Models\Referral;
use App\Models\Staff;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Tampilkan halaman monitoring Super Admin.
     */
    public function index(Request $request): View
    {
        // Active tenants
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();

        // System health indicators
        $databaseHealthy = $this->checkDatabaseHealth();
        $cacheHealthy = $this->checkCacheHealth();

        // Recent login activity (audit logs with login event)
        $recentLogins = AuditLog::where('event', 'login')
            ->with('user', 'tenant')
            ->latest()
            ->take(10)
            ->get();

        // Storage usage per tenant (top 10)
        $tenantStorage = Tenant::withCount('children')
            ->where('is_active', true)
            ->get()
            ->map(function (Tenant $tenant) {
                $storageUsed = $tenant->getStorageUsed();
                $storageLimit = $tenant->getStorageLimit();
                $plan = $tenant->activeSubscription?->plan;

                return [
                    'id' => $tenant->id,
                    'name' => $tenant->name,
                    'children_count' => $tenant->children_count,
                    'storage_used' => $storageUsed,
                    'storage_limit' => $storageLimit,
                    'storage_formatted' => $this->formatBytes($storageUsed),
                    'storage_limit_formatted' => $this->formatBytes($storageLimit),
                    'storage_percentage' => $storageLimit > 0 && $storageLimit !== PHP_INT_MAX
                        ? round(($storageUsed / $storageLimit) * 100, 1)
                        : 0,
                    'plan_name' => $plan?->name ?? '-',
                ];
            })
            ->sortByDesc('storage_used')
            ->take(10)
            ->values();

        // Error log summary (recent audit logs with error-like events)
        $recentErrors = AuditLog::whereIn('event', ['error', 'failed_login', 'unauthorized'])
            ->with('user', 'tenant')
            ->latest()
            ->take(10)
            ->get();

        // Total users
        $totalUsers = User::count();

        // Total media files
        $totalMedia = Media::count();
        $totalMediaSize = Media::sum('file_size') ?? 0;

        // ─── B2B Monitoring ──────────────────────────────────────

        // Fasilitas dengan staf paling aktif
        $b2bFacilities = Tenant::where('type', '!=', 'family')
            ->where('is_active', true)
            ->withCount(['staff' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderByDesc('staff_count')
            ->take(5)
            ->get();

        // Fasilitas dengan clinical notes paling banyak
        $topFacilitiesByNotes = Tenant::where('type', '!=', 'family')
            ->where('is_active', true)
            ->withCount('clinicalNotes')
            ->orderByDesc('clinical_notes_count')
            ->take(5)
            ->get();

        // Fasilitas dengan referral pending terbanyak
        $facilitiesWithPendingReferrals = Tenant::where('type', '!=', 'family')
            ->where('is_active', true)
            ->withCount(['referralsFrom as pending_referrals_count' => function ($query) {
                $query->where('status', 'pending');
            }])
            ->orderByDesc('pending_referrals_count')
            ->take(5)
            ->get();

        // Total B2B stats
        $b2bTenantCount = Tenant::where('type', '!=', 'family')->count();
        $totalStaff = Staff::where('is_active', true)->count();
        $totalClinicalNotes = ClinicalNote::count();
        $totalReferrals = Referral::count();
        $pendingReferrals = Referral::where('status', 'pending')->count();

        return view('super-admin.monitoring.index', compact(
            'totalTenants',
            'activeTenants',
            'databaseHealthy',
            'cacheHealthy',
            'recentLogins',
            'tenantStorage',
            'recentErrors',
            'totalUsers',
            'totalMedia',
            'totalMediaSize',
            'b2bFacilities',
            'topFacilitiesByNotes',
            'facilitiesWithPendingReferrals',
            'b2bTenantCount',
            'totalStaff',
            'totalClinicalNotes',
            'totalReferrals',
            'pendingReferrals',
        ));
    }

    /**
     * Check database health.
     */
    private function checkDatabaseHealth(): array
    {
        try {
            DB::connection()->getPdo();
            $latency = microtime(true);
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $latency) * 1000, 2);

            return [
                'status' => 'healthy',
                'message' => 'Database terhubung',
                'latency' => $latency.'ms',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database tidak terhubung: '.$e->getMessage(),
                'latency' => '-',
            ];
        }
    }

    /**
     * Check cache health.
     */
    private function checkCacheHealth(): array
    {
        try {
            $key = 'health_check_'.time();
            Cache::put($key, true, 10);
            $value = Cache::get($key);
            Cache::forget($key);

            if ($value) {
                return [
                    'status' => 'healthy',
                    'message' => 'Cache berfungsi',
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Cache tidak merespons',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Format bytes to human readable.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1_073_741_824) {
            return round($bytes / 1_073_741_824, 2).' GB';
        }

        if ($bytes >= 1_048_576) {
            return round($bytes / 1_048_576, 2).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2).' KB';
        }

        return $bytes.' B';
    }
}
