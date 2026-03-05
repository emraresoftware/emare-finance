<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class DeployWebhookController extends Controller
{
    /**
     * GitHub / GitLab / Bitbucket webhook endpoint
     * Push yapıldığında otomatik zero-downtime deploy tetikler.
     *
     * POST /deploy/webhook
     * Header: X-Hub-Signature-256 (GitHub) veya X-Gitlab-Token (GitLab)
     */
    public function handle(Request $request)
    {
        $secret = config('services.deploy.webhook_secret');

        // ─── 1. İmza Doğrulama ───
        if ($secret) {
            // GitHub
            $githubSignature = $request->header('X-Hub-Signature-256');
            if ($githubSignature) {
                $payload = $request->getContent();
                $expectedSignature = 'sha256=' . hash_hmac('sha256', $payload, $secret);

                if (!hash_equals($expectedSignature, $githubSignature)) {
                    Log::warning('[Deploy Webhook] Geçersiz GitHub imzası', [
                        'ip' => $request->ip(),
                    ]);
                    return response()->json(['error' => 'Geçersiz imza'], 403);
                }
            }

            // GitLab
            $gitlabToken = $request->header('X-Gitlab-Token');
            if ($gitlabToken && !hash_equals($secret, $gitlabToken)) {
                Log::warning('[Deploy Webhook] Geçersiz GitLab token', [
                    'ip' => $request->ip(),
                ]);
                return response()->json(['error' => 'Geçersiz token'], 403);
            }
        }

        // ─── 2. Branch Kontrolü ───
        $deployBranch = config('services.deploy.branch', 'main');
        $ref = $request->input('ref', '');

        // GitHub: refs/heads/main, GitLab: refs/heads/main
        if ($ref && !str_ends_with($ref, "/$deployBranch")) {
            Log::info("[Deploy Webhook] $ref — deploy atlanıyor (sadece $deployBranch)");
            return response()->json([
                'status' => 'skipped',
                'message' => "Sadece $deployBranch branch'i deploy edilir",
            ]);
        }

        // ─── 3. Deploy Tetikle ───
        $deployScript = base_path('deploy-zero.sh');

        if (!file_exists($deployScript)) {
            Log::error('[Deploy Webhook] deploy-zero.sh bulunamadı');
            return response()->json(['error' => 'Deploy script bulunamadı'], 500);
        }

        // Arka planda çalıştır (webhook timeout olmasın)
        $logFile = storage_path('logs/deploy-webhook.log');
        $command = sprintf(
            'nohup bash %s >> %s 2>&1 &',
            escapeshellarg($deployScript),
            escapeshellarg($logFile)
        );

        exec($command);

        $commitInfo = $this->extractCommitInfo($request);

        Log::info('[Deploy Webhook] Deploy tetiklendi', [
            'branch' => $deployBranch,
            'commit' => $commitInfo['sha'] ?? 'unknown',
            'author' => $commitInfo['author'] ?? 'unknown',
            'message' => $commitInfo['message'] ?? '',
            'ip' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'deploying',
            'message' => 'Zero-downtime deploy başlatıldı',
            'commit' => $commitInfo['sha'] ?? null,
            'log' => 'storage/logs/deploy-webhook.log',
        ]);
    }

    /**
     * Deploy durumu kontrol endpoint
     * GET /deploy/status
     */
    public function status(Request $request)
    {
        $secret = $request->header('X-Deploy-Secret') ?? $request->input('secret');

        if ($secret !== config('services.deploy.webhook_secret')) {
            return response()->json(['error' => 'Yetkisiz'], 403);
        }

        $baseDir = config('services.deploy.base_dir', '/var/www/emare-finance');
        $currentLink = "$baseDir/current";
        $releasesDir = "$baseDir/releases";

        $activeRelease = is_link($currentLink) ? basename(readlink($currentLink)) : null;

        $releases = [];
        if (is_dir($releasesDir)) {
            $dirs = array_filter(scandir($releasesDir), fn($d) => $d !== '.' && $d !== '..');
            rsort($dirs);
            $releases = array_values($dirs);
        }

        // Son deploy log
        $logFile = storage_path('logs/deploy-webhook.log');
        $lastLog = file_exists($logFile) ? tail($logFile, 20) : '';

        return response()->json([
            'active_release' => $activeRelease,
            'releases' => $releases,
            'total_releases' => count($releases),
            'last_log' => $lastLog,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    /**
     * Push event'inden commit bilgilerini çıkar
     */
    protected function extractCommitInfo(Request $request): array
    {
        // GitHub format
        $headCommit = $request->input('head_commit');
        if ($headCommit) {
            return [
                'sha' => substr($headCommit['id'] ?? '', 0, 8),
                'author' => $headCommit['author']['name'] ?? '',
                'message' => $headCommit['message'] ?? '',
            ];
        }

        // GitLab format
        $commits = $request->input('commits', []);
        if (!empty($commits)) {
            $last = end($commits);
            return [
                'sha' => substr($last['id'] ?? '', 0, 8),
                'author' => $last['author']['name'] ?? '',
                'message' => $last['message'] ?? '',
            ];
        }

        return [];
    }
}

/**
 * Dosyanın son N satırını oku (tail)
 */
function tail(string $file, int $lines = 20): string
{
    $handle = fopen($file, 'r');
    if (!$handle) return '';

    $buffer = '';
    $lineCount = 0;
    $pos = -1;
    $fileSize = filesize($file);

    if ($fileSize === 0) return '';

    while ($lineCount < $lines && abs($pos) <= $fileSize) {
        fseek($handle, $pos, SEEK_END);
        $char = fgetc($handle);
        if ($char === "\n") $lineCount++;
        $buffer = $char . $buffer;
        $pos--;
    }

    fclose($handle);
    return trim($buffer);
}
