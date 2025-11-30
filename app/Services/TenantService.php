<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TenantService
{
    public static function currentCustomerId(): ?int
    {
        if (session()->has('current_tenant_id')) {
            return session('current_tenant_id');
        }

        return optional(optional(Auth::user())->userDetail)->customer_id;
    }

    public static function currentUserDetail()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        $customerId = self::currentCustomerId();
        if (!$customerId) {
            // If internal or no customer selected, return primary detail or null?
            // For internal users, they might not have a customer_id in user_details?
            // Or they have one but we ignore it?
            // Let's fallback to default behavior if no customer context
            return $user->userDetail;
        }

        // Find detail for this customer
        $detail = $user->userDetails()->where('customer_id', $customerId)->first();

        // Fallback to default if not found (shouldn't happen if logic is correct)
        return $detail ?? $user->userDetail;
    }

    public static function isInternal(): bool
    {
        return is_null(self::currentCustomerId());
    }

    public static function resolveCustomerId(?int $customerId = null): ?int
    {
        if (self::isInternal()) {
            return $customerId;
        }

        return self::currentCustomerId();
    }

    public static function canAccessCustomer(?int $modelCustomerId): bool
    {
        if (self::isInternal()) {
            return true;
        }

        return $modelCustomerId === self::currentCustomerId();
    }

    public static function assertAccess(?int $modelCustomerId): void
    {
        if (!self::canAccessCustomer($modelCustomerId)) {
            abort(403, 'Unauthorized customer scope.');
        }
    }

    public static function scopeQueryByCustomer(Builder $query, ?string $column = 'customer_id'): Builder
    {
        if ($customerId = self::currentCustomerId()) {
            return $query->where($column, $customerId);
        }

        return $query;
    }
}
