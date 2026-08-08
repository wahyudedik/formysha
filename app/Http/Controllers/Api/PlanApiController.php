<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\PlanResource;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;

class PlanApiController extends ApiController
{
    /**
     * List all active plans (public, no auth required).
     */
    public function index(): JsonResponse
    {
        $plans = Plan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->successResponse(
            PlanResource::collection($plans),
            'Daftar paket berhasil diambil'
        );
    }

    /**
     * Show a specific plan.
     */
    public function show(Plan $plan): JsonResponse
    {
        return $this->successResponse(
            new PlanResource($plan),
            'Detail paket berhasil diambil'
        );
    }
}
