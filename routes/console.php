<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// PII decommission plan (docs/pii-decommission-plan.md) requires the
// verify command to pass at 100% for 4 consecutive weeks before the
// plaintext email/id_number columns on `guests` can be dropped. These
// were not scheduled anywhere before, so no evidence was accumulating.
// Weekly is enough to catch drift without adding noise; `->emailOutputOnFailure()`
// requires MAIL_* to be configured, so we just log for now.
Schedule::command('app:verify-guest-pii-encryption')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::critical('PII verification failed — see app:verify-guest-pii-encryption output.');
    });

Schedule::command('app:audit-guest-pii-plaintext-usage')
    ->weekly()
    ->sundays()
    ->at('02:15')
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::warning('PII plaintext usage audit found findings — see app:audit-guest-pii-plaintext-usage output.');
    });