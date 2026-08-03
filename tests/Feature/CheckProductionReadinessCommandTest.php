<?php

namespace Tests\Feature;

use Illuminate\Console\Command;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckProductionReadinessCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        config()->set([
            'app.env' => 'testing',
            'app.debug' => false,
            'app.key' => '',
            'app.url' => 'http://localhost',
            'session.secure' => false,
            'logging.channels.single.level' => 'debug',
        ]);

        parent::tearDown();
    }

    private function setProductionConfig(): void
    {
        config()->set([
            'app.env' => 'production',
            'app.debug' => false,
            'app.key' => 'base64:'.base64_encode('test-secret-key-32-chars-long!!'),
            'app.url' => 'https://rm1.example.com',
            'session.secure' => true,
            'logging.channels.single.level' => 'info',
        ]);
    }

    public function test_command_passes_when_all_production_requirements_are_met(): void
    {
        $this->setProductionConfig();

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('APP_ENV')
            ->expectsOutputToContain('APP_DEBUG')
            ->expectsOutputToContain('SESSION_SECURE_COOKIE')
            ->expectsOutputToContain('APP_KEY')
            ->expectsOutputToContain('APP_URL')
            ->expectsOutputToContain('LOG_LEVEL')
            ->expectsOutputToContain('PASSED')
            ->assertExitCode(Command::SUCCESS);
    }

    public function test_command_fails_when_app_env_is_not_production(): void
    {
        $this->setProductionConfig();
        config()->set('app.env', 'local');

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('FAILED')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_fails_when_app_debug_is_true(): void
    {
        $this->setProductionConfig();
        config()->set('app.debug', true);

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('FAILED')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_fails_when_session_secure_cookie_is_false(): void
    {
        $this->setProductionConfig();
        config()->set('session.secure', false);

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('FAILED')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_fails_when_app_key_is_empty(): void
    {
        $this->setProductionConfig();
        config()->set('app.key', '');

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('FAILED')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_fails_when_app_key_is_not_base64(): void
    {
        $this->setProductionConfig();
        config()->set('app.key', 'plain-text-key');

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('FAILED')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_fails_when_app_url_is_dev_default(): void
    {
        $this->setProductionConfig();
        config()->set('app.url', 'http://localhost:8000');

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('FAILED')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_fails_when_log_level_is_debug(): void
    {
        $this->setProductionConfig();
        config()->set('logging.channels.single.level', 'debug');

        $this->artisan('app:check-production-readiness')
            ->expectsOutputToContain('FAILED')
            ->assertExitCode(Command::FAILURE);
    }
}
