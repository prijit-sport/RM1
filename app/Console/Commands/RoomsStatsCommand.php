<?php

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Command;

class RoomsStatsCommand extends Command
{
    protected $signature = 'rooms:stats {--floor= : Show stats only for floor N (optional)}';

    protected $description = 'Show room stats (floor count + Zone A/B by room_number prefix)';

    public function handle(): int
    {
        $floor = $this->option('floor');

        $query = Room::query();
        if ($floor !== null && $floor !== '') {
            $query->where('floor', (int) $floor);
        }

        $rooms = $query->get(['room_number', 'floor']);

        $total = $rooms->count();
        $a = $rooms->filter(fn ($r) => str_starts_with((string) $r->room_number, 'A'))->count();
        $b = $rooms->filter(fn ($r) => str_starts_with((string) $r->room_number, 'B'))->count();

        if ($floor !== null && $floor !== '') {
            $this->info("floor={$floor} total={$total} zoneA={$a} zoneB={$b}");
            return self::SUCCESS;
        }

        $this->info('All floors summary');
        foreach ([1, 2, 3, 4, 5] as $f) {
            $floorRooms = $rooms->where('floor', $f);
            $ft = $floorRooms->count();
            $fa = $floorRooms->filter(fn ($r) => str_starts_with((string) $r->room_number, 'A'))->count();
            $fb = $floorRooms->filter(fn ($r) => str_starts_with((string) $r->room_number, 'B'))->count();
            if ($ft === 0) {
                continue;
            }
            $this->line("floor={$f} total={$ft} zoneA={$fa} zoneB={$fb}");
        }

        // Also show distinct floors present
        $distinctFloors = Room::query()->distinct()->orderBy('floor')->pluck('floor')->filter();
        $this->line('distinct floors: ' . $distinctFloors->implode(','));

        return self::SUCCESS;
    }
}


