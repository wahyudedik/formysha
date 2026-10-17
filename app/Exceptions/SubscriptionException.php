<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Exception untuk menandai alasan bisnis subscription ditolak.
 *
 * Dilempar oleh SubscriptionService (guard plan inactive, audience mismatch,
 * custom pricing, validasi payment proof) dan ditangani oleh controller web
 * (redirect + flash error) maupun API (error response JSON).
 */
class SubscriptionException extends RuntimeException {}
