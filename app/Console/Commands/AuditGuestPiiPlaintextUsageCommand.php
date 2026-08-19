<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Audit command to find all locations in the codebase where plaintext PII columns
 * (email, id_number) are being accessed directly, not through model accessors.
 *
 * This command scans runtime application code plus database seeders/factories
 * (not tests, vendor, or migrations) for patterns that suggest direct plaintext
 * column access.
 */
class AuditGuestPiiPlaintextUsageCommand extends Command
{
    /** @var string */
    protected $signature = 'app:audit-guest-pii-plaintext-usage';

    /** @var string */
    protected $description = 'Audit the codebase for direct plaintext PII column (email, id_number) access';

    /**
     * Paths to scan for potential plaintext PII usage
     */
    private const SCAN_PATHS = [
        'app',
        'config',
        'database/factories',
        'database/seeders',
        'routes',
        'resources/views',
    ];

    /**
     * Paths to exclude from scanning
     */
    private const EXCLUDE_PATHS = [
        'vendor',
        'node_modules',
        'tests',
        'database/migrations',
        'app/Console/Commands/AuditGuestPiiPlaintextUsageCommand.php',
    ];

    /**
     * Patterns that indicate potential plaintext PII column access.
     * These are heuristic patterns; not all matches are actual issues.
     */
    private const RISKY_PATTERNS = [
        // Direct attribute/column selection in queries
        ['regex' => "\\->select\\([^)]*['\\\"](?<column>email|id_number)['\\\"]", 'description' => 'Direct select() with plaintext column'],
        ['regex' => "\\->select.*['\\\"](?<column>email|id_number)['\\\"]", 'description' => 'select() with plaintext column'],
        ['regex' => '\\->selectRaw[^\\n]*\\b(?<column>email|id_number)\\b', 'description' => 'selectRaw() with plaintext column'],
        ['regex' => "\\->pluck\\([^)]*['\\\"](?<column>email|id_number)['\\\"]", 'description' => 'pluck() with plaintext column'],

        // Raw SQL queries that mention plaintext columns
        ['regex' => '\\->whereRaw[^\\n]*\\b(?<column>email|id_number)\\b', 'description' => 'whereRaw() with plaintext column reference'],
        ['regex' => "\\->orderBy\\([^)]*['\\\"](?<column>email|id_number)['\\\"]", 'description' => 'orderBy() with plaintext column'],
        ['regex' => '\\bDB::raw[^\\n]*\\b(?<column>email|id_number)\\b', 'description' => 'DB::raw() with plaintext column'],

        // Array/attribute direct access that might bypass encryption
        ['regex' => "\\['(?<column>email|id_number)'\\]", 'description' => 'Array access to plaintext column'],
        ['regex' => '\\[\\"(?<column>email|id_number)\\"\\]', 'description' => 'Array access to plaintext column (double quotes)'],

        // Blade template direct access (potential issue if not using model accessor)
        ['regex' => '\\$guest->(?<column>email|id_number)', 'description' => 'View: direct property access (may be accessor)'],
    ];

    public function handle(): int
    {
        $this->info('🔍 Scanning codebase for plaintext PII column access...');
        $this->newLine();

        $findings = [];

        foreach (self::SCAN_PATHS as $path) {
            $fullPath = base_path($path);
            if (! is_dir($fullPath)) {
                continue;
            }

            $findings = array_merge($findings, $this->scanDirectory($fullPath));
        }

        if (empty($findings)) {
            $this->info('✅ No risky plaintext PII access patterns found!');

            return self::SUCCESS;
        }

        // Display findings
        usort($findings, static function (array $left, array $right): int {
            return [$left['file'], $left['line'], $left['column'], $left['pattern'], $left['snippet']]
                <=> [$right['file'], $right['line'], $right['column'], $right['pattern'], $right['snippet']];
        });

        $rows = [];
        foreach ($findings as $finding) {
            $rows[] = [
                $finding['file'],
                $finding['line'],
                $finding['column'] ?? 'email/id_number',
                $finding['pattern'],
                $finding['snippet'],
            ];
        }

        $this->table(
            ['File', 'Line', 'Column', 'Pattern', 'Code Snippet'],
            array_slice($rows, 0, 50) // Limit display to first 50
        );

        if (count($rows) > 50) {
            $this->newLine();
            $this->warn(sprintf('⚠️ Showing first 50 of %d findings', count($rows)));
        }

        $this->newLine();
        $this->info(sprintf('Found %d potential plaintext PII access pattern(s)', count($findings)));
        $this->warn('⚠️ Review each finding carefully — not all may be actual security issues.');
        $this->warn('   (Some may be false positives or safe uses through accessors)');

        return self::FAILURE;
    }

    /**
     * Recursively scan a directory for risky patterns
     */
    private function scanDirectory(string $baseDir): array
    {
        $findings = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $includedExtensions = ['php', 'blade.php'];

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! in_array($file->getExtension(), $includedExtensions, true)) {
                continue;
            }

            // Skip excluded paths
            $relativePath = str_replace(base_path(), '', $file->getRealPath());
            $normalizedRelativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
            $skip = false;
            foreach (self::EXCLUDE_PATHS as $excludePath) {
                if (str_contains($normalizedRelativePath, $excludePath)) {
                    $skip = true;
                    break;
                }
            }

            if ($skip) {
                continue;
            }

            $findings = array_merge($findings, $this->scanFile($file->getRealPath()));
        }

        return $findings;
    }

    /**
     * Scan a single file for risky patterns
     */
    private function scanFile(string $filePath): array
    {
        $findings = [];

        try {
            $content = File::get($filePath);
            $lines = explode("\n", $content);
        } catch (\Exception $e) {
            return [];
        }

        $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $filePath);

        foreach (self::RISKY_PATTERNS as $patternDefinition) {
            $pattern = $patternDefinition['regex'];
            $description = $patternDefinition['description'];

            foreach ($lines as $lineNum => $line) {
                // Skip comments and strings that might be false positives
                if (str_starts_with(trim($line), '//') || str_starts_with(trim($line), '*')) {
                    continue;
                }

                // Check if line matches pattern
                if (preg_match("/{$pattern}/i", $line, $matches)) {
                    // Determine which column was referenced from the actual regex match.
                    $column = $matches['column'] ?? $this->detectColumnFromLine($line);
                    if ($this->lineContainsBothColumns($line)) {
                        $column = 'email/id_number';
                    }

                    $snippet = trim(substr($line, 0, 80));
                    if (strlen($line) > 80) {
                        $snippet .= '...';
                    }

                    $findings[] = [
                        'file' => $relativePath,
                        'line' => $lineNum + 1, // 1-indexed
                        'column' => $column,
                        'pattern' => $description,
                        'snippet' => $snippet,
                    ];
                }
            }
        }

        return $findings;
    }

    /**
     * Detect the referenced column from the full line when the regex does not expose one.
     */
    private function detectColumnFromLine(string $line): string
    {
        $hasEmail = preg_match('/\bemail\b/i', $line) === 1;
        $hasIdNumber = preg_match('/\bid_number\b/i', $line) === 1;

        if ($hasEmail && $hasIdNumber) {
            return 'email/id_number';
        }

        return $hasEmail ? 'email' : 'id_number';
    }

    /**
     * Check whether both plaintext columns appear in the same line.
     */
    private function lineContainsBothColumns(string $line): bool
    {
        return preg_match('/\bemail\b/i', $line) === 1
            && preg_match('/\bid_number\b/i', $line) === 1;
    }
}
