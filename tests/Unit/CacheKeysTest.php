<?php

namespace Tests\Unit;

use App\Support\CacheKeys;
use Tests\TestCase;

class CacheKeysTest extends TestCase
{
    public function test_facility_list_key_matches_the_legacy_unfiltered_key(): void
    {
        $this->assertSame(
            'facilities.list.7fe4bb8420b1446ec0d2a022e33e0a17',
            CacheKeys::facilityList([
                'search' => null,
                'type' => null,
                'status' => null,
                'location' => null,
            ], 1)
        );
    }

    public function test_facility_list_key_preserves_legacy_filter_and_raw_page_values(): void
    {
        $this->assertSame(
            'facilities.list.b1cd78e7ecd3d9a0bd29671b73be5a24',
            CacheKeys::facilityList([
                'search' => 'โต๊ะ',
                'type' => 'furniture',
                'status' => 'good',
                'location' => 'อาคาร A',
            ], '2')
        );
    }

    public function test_fixed_cache_keys_match_their_legacy_values(): void
    {
        $this->assertSame('facilities.all', CacheKeys::allFacilities());
        $this->assertSame('permissions.all', CacheKeys::allPermissions());
        $this->assertSame('layout_notifications', CacheKeys::layoutNotifications());
    }
}
