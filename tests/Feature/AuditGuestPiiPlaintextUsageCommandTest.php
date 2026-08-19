<?php

namespace Tests\Feature;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class AuditGuestPiiPlaintextUsageCommandTest extends TestCase
{
    public function test_command_executes_successfully(): void
    {
        $this->artisan('app:audit-guest-pii-plaintext-usage')
            ->assertExitCode(Command::FAILURE); // Returns FAILURE because findings exist (expected)
    }

    public function test_command_output_contains_required_sections(): void
    {
        $this->artisan('app:audit-guest-pii-plaintext-usage')
            ->expectsOutputToContain('Scanning codebase')
            ->expectsOutputToContain('File');
    }

    public function test_command_displays_results_as_table(): void
    {
        $this->artisan('app:audit-guest-pii-plaintext-usage')
            ->expectsOutputToContain('email')
            ->expectsOutputToContain('id_number');
    }

    public function test_command_shows_warning_about_false_positives(): void
    {
        $this->artisan('app:audit-guest-pii-plaintext-usage')
            ->expectsOutputToContain('Review each finding carefully')
            ->expectsOutputToContain('false positives');
    }

    public function test_command_includes_file_paths_in_output(): void
    {
        // The audit should find references in various files
        $this->artisan('app:audit-guest-pii-plaintext-usage')
            ->expectsOutputToContain('Controller')
            ->expectsOutputToContain('app');
    }

    public function test_command_detects_query_builder_patterns_in_database_fixtures(): void
    {
        $fixturePath = base_path('database/seeders/AuditGuestPiiPlaintextUsageFixture.php');

        File::put($fixturePath, <<<'PHP'
<?php

namespace Database\Seeders;

use App\Models\Guest;

class AuditGuestPiiPlaintextUsageFixture
{
    public function run(): void
    {
        Guest::query()->select('email');
        Guest::query()->selectRaw('id_number, COUNT(*) as total');
        Guest::query()->whereRaw('email = ?', ['demo@example.com']);
        Guest::query()->pluck('id_number');
    }
}
PHP);

        try {
            $exitCode = Artisan::call('app:audit-guest-pii-plaintext-usage');
            $output = Artisan::output();

            $this->assertSame(Command::FAILURE, $exitCode);
            $this->assertStringContainsString('AuditGuestPiiPlaintextUsageFixture.php', $output);
            $this->assertStringContainsString('select() with plaintext column', $output);
            $this->assertStringContainsString('selectRaw() with plaintext column', $output);
            $this->assertStringContainsString('whereRaw() with plaintext column reference', $output);
            $this->assertStringContainsString('pluck() with plaintext column', $output);
        } finally {
            File::delete($fixturePath);
        }
    }

    public function test_command_output_is_stable_across_consecutive_runs_with_same_input(): void
    {
        $fixturePath = base_path('database/seeders/AuditGuestPiiPlaintextUsageFixture.php');

        File::put($fixturePath, <<<'PHP'
<?php

namespace Database\Seeders;

use App\Models\Guest;

class AuditGuestPiiPlaintextUsageFixture
{
    public function run(): void
    {
        Guest::query()->select('email');
        Guest::query()->selectRaw('id_number, COUNT(*) as total');
        Guest::query()->whereRaw('email = ?', ['demo@example.com']);
        Guest::query()->pluck('id_number');
    }
}
PHP);

        try {
            Artisan::call('app:audit-guest-pii-plaintext-usage');
            $firstOutput = Artisan::output();

            Artisan::call('app:audit-guest-pii-plaintext-usage');
            $secondOutput = Artisan::output();

            $this->assertSame($firstOutput, $secondOutput);
        } finally {
            File::delete($fixturePath);
        }
    }
}
