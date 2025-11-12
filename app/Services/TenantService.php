<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TenantService
{
    public static function currentCustomerId(): ?int
    {
        return optional(optional(Auth::user())->userDetail)->customer_id;
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
