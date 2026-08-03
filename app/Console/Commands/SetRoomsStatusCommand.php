<?php

namespace App\Console\Commands;

use App\Models\Room;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SetRoomsStatusCommand extends Command
{
    /** {@inheritDoc} */
    // (kept for IDE / php-doc parity)
    /** @var string */
    protected $signature = 'rooms:set-status {--floor= : Set only rooms with floor = N} {--status=available : available|occupied|maintenance} {--all : Alias for updating all rooms}';

    /** @var string */
    protected $description = 'Set rooms.status for rooms (optionally filtered by floor)';

    public function handle(): int
    {
        $status = (string) $this->option('status');
        $floorOption = $this->option('floor');

        if (! in_array($status, ['available', 'occupied', 'maintenance'], true)) {
            $this->error('Invalid --status. Use available|occupied|maintenance');

            return self::FAILURE;
        }

        $query = Room::query()->withTrashed();

        // If --all is provided, ignore --floor.
        if (! $this->option('all') && $floorOption !== null && $floorOption !== '') {
            $query->where('floor', (int) $floorOption);
        }

        $affected = 0;

        DB::transaction(function () use ($query, $status, &$affected): void {
            $affected = (int) $query->update(['status' => $status]);
        });

        $this->info("Updated {$affected} room(s) to status '{$status}'.");

        return self::SUCCESS;
    }
}
