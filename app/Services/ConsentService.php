<?php

namespace App\Services;

use App\Enums\ConsentType;
use App\Models\Child;
use App\Models\Consent;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Service untuk mengelola consent orang tua terhadap data anak.
 */
class ConsentService
{
    /**
     * Berikan atau update consent untuk child.
     */
    public function grant(
        User $user,
        Child $child,
        ConsentType $type,
        ?string $notes = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): Consent {
        $consent = Consent::updateOrCreate(
            [
                'user_id' => $user->id,
                'child_id' => $child->id,
                'consent_type' => $type,
            ],
            [
                'granted' => true,
                'notes' => $notes,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'revoked_at' => null,
            ]
        );

        return $consent;
    }

    /**
     * Revoke consent untuk child.
     */
    public function revoke(User $user, Child $child, ConsentType $type): ?Consent
    {
        $consent = Consent::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->where('consent_type', $type)
            ->first();

        if ($consent) {
            $consent->revoke();
        }

        return $consent;
    }

    /**
     * Cek apakah consent aktif untuk child.
     */
    public function hasConsent(User $user, Child $child, ConsentType $type): bool
    {
        return Consent::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->where('consent_type', $type)
            ->where('granted', true)
            ->whereNull('revoked_at')
            ->exists();
    }

    /**
     * Dapatkan semua consent untuk child dari user tertentu.
     */
    public function getConsents(User $user, Child $child): Collection
    {
        return Consent::where('user_id', $user->id)
            ->where('child_id', $child->id)
            ->get();
    }

    /**
     * Dapatkan status semua tipe consent untuk child.
     *
     * @return array<string, array{type: ConsentType, granted: bool, consent: ?Consent}>
     */
    public function getConsentStatuses(User $user, Child $child): array
    {
        $existingConsents = $this->getConsents($user, $child)
            ->keyBy(fn (Consent $c) => $c->consent_type->value);

        $statuses = [];

        foreach (ConsentType::options() as $type) {
            $consent = $existingConsents->get($type->value);
            $statuses[$type->value] = [
                'type' => $type,
                'granted' => $consent?->isActive() ?? false,
                'consent' => $consent,
            ];
        }

        return $statuses;
    }

    /**
     * Bulk grant semua consent untuk child (saat pertama kali daftar).
     */
    public function grantAll(User $user, Child $child, ?string $ipAddress = null): void
    {
        foreach (ConsentType::options() as $type) {
            $this->grant($user, $child, $type, null, $ipAddress);
        }
    }
}
