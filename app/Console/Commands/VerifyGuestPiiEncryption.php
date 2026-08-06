<?php

namespace App\Console\Commands;

use App\Models\Guest;
use Illuminate\Console\Command;

class VerifyGuestPiiEncryption extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:verify-guest-pii-encryption';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify that Guest PII ciphertext + hash columns decrypt/match the original plaintext values';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $guests = Guest::withoutTrashed()->get();
        $total = $guests->count();

        $this->info("Verifying {$total} guest records...");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $emailOk = 0;
        $idNumberOk = 0;
        $mismatchCount = 0;

        foreach ($guests as $guest) {
            $emailMatch = false;
            $idNumberMatch = false;

            // Email: decrypt ciphertext == original, and hash matches
            try {
                $emailDecrypted = ! empty($guest->email_ciphertext) ? decrypt($guest->email_ciphertext) : null;
                $emailHashComputed = hash_hmac('sha256', (string) $guest->email, (string) config('app.key'));
                $emailMatch = ($emailDecrypted === $guest->email)
                    && hash_equals($emailHashComputed, (string) $guest->email_hash);
            } catch (\Throwable $e) {
                $emailMatch = false;
            }

            // id_number: decrypt ciphertext == original, and hash matches
            try {
                $idNumberDecrypted = ! empty($guest->id_number_ciphertext) ? decrypt($guest->id_number_ciphertext) : null;
                $idNumberHashComputed = hash_hmac('sha256', (string) $guest->id_number, (string) config('app.key'));
                $idNumberMatch = ($idNumberDecrypted === $guest->id_number)
                    && hash_equals($idNumberHashComputed, (string) $guest->id_number_hash);
            } catch (\Throwable $e) {
                $idNumberMatch = false;
            }

            if ($emailMatch) {
                $emailOk++;
            } else {
                $mismatchCount++;
                $this->newLine();
                $this->error("Guest ID {$guest->id}: email mismatch");
            }

            if ($idNumberMatch) {
                $idNumberOk++;
            } else {
                $mismatchCount++;
                $this->newLine();
                $this->error("Guest ID {$guest->id}: id_number mismatch");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("Email matches: {$emailOk}/{$total}");
        $this->info("ID number matches: {$idNumberOk}/{$total}");
        $this->info("Total mismatches: {$mismatchCount}");

        if ($mismatchCount > 0) {
            $this->error('VERIFICATION FAILED — do NOT proceed to cutover.');

            return self::FAILURE;
        }

        $this->info('All PII records verified successfully (100%).');

        return self::SUCCESS;
    }
}
