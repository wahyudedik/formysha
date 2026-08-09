<?php

namespace App\Http\Controllers\SuperAdmin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ErrorLogController extends Controller
{
    /**
     * Tampilkan daftar error log dari Laravel log file.
     */
    public function index(Request $request): View
    {
        $logPath = storage_path('logs/laravel.log');
        $level = $request->input('level', 'all');
        $search = $request->input('search', '');

        $logs = [];
        $totalErrors = 0;
        $totalWarnings = 0;
        $totalInfo = 0;
        $fileSize = 0;

        if (File::exists($logPath)) {
            $fileSize = File::size($logPath);
            $content = File::get($logPath);
            $logs = $this->parseLogs($content);

            // Count by level
            foreach ($logs as $log) {
                match ($log['level']) {
                    'ERROR' => $totalErrors++,
                    'WARNING' => $totalWarnings++,
                    'INFO' => $totalInfo++,
                    default => null,
                };
            }

            // Filter by level
            if ($level !== 'all') {
                $logs = array_filter($logs, fn ($log) => strtolower($log['level']) === strtolower($level));
            }

            // Filter by search
            if ($search !== '') {
                $logs = array_filter($logs, fn ($log) => str_contains(strtolower($log['message']), strtolower($search)) ||
                    str_contains(strtolower($log['context'] ?? ''), strtolower($search)) ||
                    str_contains(strtolower($log['stack'] ?? ''), strtolower($search))
                );
            }

            $logs = array_values($logs);
        }

        return view('super-admin.error-logs.index', compact(
            'logs',
            'totalErrors',
            'totalWarnings',
            'totalInfo',
            'fileSize',
            'level',
            'search',
        ));
    }

    /**
     * Parse Laravel log content menjadi array of log entries.
     */
    private function parseLogs(string $content): array
    {
        $logs = [];
        // Pattern: [2024-01-01 00:00:00] local.LEVEL: message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.+?)(?:\n(?:[ \t]+.+?)*(?:\n\n|\z))/ms';

        if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
            foreach ($matches as $match) {
                $timestamp = $match[1][0];
                $environment = $match[2][0];
                $level = strtoupper($match[3][0]);
                $message = $match[4][0];

                // Extract context and stack from the message
                $context = '';
                $stack = '';
                $this->extractContextAndStack($message, $context, $stack);

                $logs[] = [
                    'timestamp' => $timestamp,
                    'environment' => $environment,
                    'level' => $level,
                    'message' => trim($message),
                    'context' => $context,
                    'stack' => $stack,
                    'raw' => $match[0][0],
                ];
            }
        }

        // Sort by timestamp descending (newest first)
        usort($logs, function ($a, $b) {
            return strcmp($b['timestamp'], $a['timestamp']);
        });

        return $logs;
    }

    /**
     * Extract context array dan stack trace dari log message.
     */
    private function extractContextAndStack(string &$message, string &$context, string &$stack): void
    {
        // Check for stack trace (starts with "Stack trace:")
        if (preg_match('/\nStack trace:\n(.+)/s', $message, $stackMatch)) {
            $stack = trim($stackMatch[1]);
            $message = trim(substr($message, 0, $stackMatch[0][0] ?? strpos($message, "\nStack trace:")));
        }

        // Check for context (starts with "Context:")
        if (preg_match('/\nContext:\n(\{.+\})/s', $message, $contextMatch)) {
            $context = trim($contextMatch[1]);
            $message = trim(substr($message, 0, $contextMatch[0][0] ?? strpos($message, "\nContext:")));
        }
    }

    /**
     * Hapus isi log file.
     */
    public function clear(): RedirectResponse
    {
        $logPath = storage_path('logs/laravel.log');

        if (File::exists($logPath)) {
            File::put($logPath, '');
        }

        return redirect()->route('super-admin.error-logs.index')
            ->with('success', 'Log file berhasil dikosongkan.');
    }
}
