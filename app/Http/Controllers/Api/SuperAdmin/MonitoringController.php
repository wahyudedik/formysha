<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\ApiController;
use App\Models\Media;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MonitoringController extends ApiController
{
    /**
     * Return system health and monitoring data.
     */
    public function index(Request $request): JsonResponse
    {
        // Active tenants
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('is_active', true)->count();

        // System health indicators
        $databaseHealthy = $this->checkDatabaseHealth();
        $cacheHealthy = $this->checkCacheHealth();

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

        // Total users
        $totalUsers = User::count();

        // Total media files
        $totalMedia = Media::count();
        $totalMediaSize = Media::sum('file_size') ?? 0;

        $monitoring = [
            'total_tenants' => $totalTenants,
            'active_tenants' => $activeTenants,
            'total_users' => $totalUsers,
            'total_media' => $totalMedia,
            'total_media_size' => $totalMediaSize,
            'total_media_size_formatted' => $this->formatBytes($totalMediaSize),
            'database_health' => $databaseHealthy,
            'cache_health' => $cacheHealthy,
            'tenant_storage' => $tenantStorage,
        ];

        return $this->successResponse($monitoring, 'Data monitoring berhasil diambil');
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
