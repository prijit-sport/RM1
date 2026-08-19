<?php

namespace App\Support;

final class CacheKeys
{
    /**
     * Keep the legacy key format unchanged so live file-cache entries remain usable after deploy.
     *
     * @param  array<string, mixed>  $filters
     */
    public static function facilityList(array $filters, int|string $page): string
    {
        return 'facilities.list.'.md5(json_encode([
            'search' => $filters['search'] ?? null,
            'type' => $filters['type'] ?? null,
            'status' => $filters['status'] ?? null,
            'location' => $filters['location'] ?? null,
            'page' => $page,
        ]));
    }

    public static function allFacilities(): string
    {
        return 'facilities.all';
    }

    public static function allPermissions(): string
    {
        return 'permissions.all';
    }

    public static function layoutNotifications(): string
    {
        return 'layout_notifications';
    }

    /*
     * If the cache store changes to Redis or Memcached, use cache tags instead of
     * extending string keys. Tags allow precise invalidation, for example:
     *
     * Cache::tags(['facilities'])->remember(self::facilityList($filters, $page), $ttl, $callback);
     * Cache::tags(['facilities'])->flush();
     *
     * File cache does not support tags, so this remains a key-only class for now.
     */
}
