<?php

namespace App\Console\Commands;

use App\Models\Guest;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateGuestPiiEncryption extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:migrate-guest-pii-encryption';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encrypt existing Guest PII (email, id_number) into dedicated ciphertext + hash lookup columns';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $guests = Guest::withoutTrashed()->get();
        $total = $guests->count();

        $this->info("Processing {$total} guest records...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $success = 0;
        $failed = 0;
        $processed = 0;

        foreach ($guests as $guest) {
            $processed++;

            try {
                DB::transaction(function () use ($guest): void {
                    $guest->forceFill([
                        'email_ciphertext'      => encrypt($guest->email),
                        'email_hash'            => hash_hmac('sha256', $guest->email, (string) config('app.key')),
                        'id_number_ciphertext'  => encrypt($guest->id_number),
                        'id_number_hash'        => hash_hmac('sha256', $guest->id_number, (string) config('app.key')),
                    ])->save();
                });

                $success++;
            } catch (\Throwable $e) {
                $failed++;
                $this->newLine();
                $this->error("Failed guest ID {$guest->id}: {$e->getMessage()}");
            }

            if ($processed % 20 === 0) {
                $this->newLine();
                $this->line("Processed {$processed}/{$total} records (success: {$success}, failed: {$failed})");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done. Processed: {$processed}, Success: {$success}, Failed: {$failed}");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
