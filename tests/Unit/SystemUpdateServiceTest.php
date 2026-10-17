<?php

namespace Tests\Unit;

use App\Services\SystemUpdateService;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;
use RuntimeException;
use Tests\TestCase;

class SystemUpdateServiceTest extends TestCase
{
    private string $repositoryPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryPath = sys_get_temp_dir().'/system-update-test-'.bin2hex(random_bytes(5));
        mkdir($this->repositoryPath.'/.git', 0777, true);

        config()->set('system_update.path', $this->repositoryPath);
        config()->set('system_update.remote', 'origin');
        config()->set('system_update.branch', 'main');
    }

    protected function tearDown(): void
    {
        rmdir($this->repositoryPath.'/.git');
        rmdir($this->repositoryPath);

        parent::tearDown();
    }

    public function test_status_parses_repository_and_redacts_remote_credentials(): void
    {
        Process::fake(fn (PendingProcess $process) => Process::result(
            output: $this->outputFor($process, dirty: false)
        ));

        $status = app(SystemUpdateService::class)->status();

        $this->assertSame('main', $status['branch']);
        $this->assertSame(2, $status['behind']);
        $this->assertSame(1, $status['ahead']);
        $this->assertFalse($status['dirty']);
        $this->assertSame('https://***@github.com/acme/example.git', $status['remote_url']);
        $this->assertCount(2, $status['commits']);
        $this->assertSame('Commit pertama', $status['commits'][0]['subject']);
    }

    public function test_update_refuses_to_pull_when_tracked_files_are_dirty(): void
    {
        Process::fake(fn (PendingProcess $process) => Process::result(
            output: $this->outputFor($process, dirty: true)
        ));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('perubahan tracked');

        app(SystemUpdateService::class)->update();
    }

    private function outputFor(PendingProcess $process, bool $dirty): string
    {
        $command = implode(' ', (array) $process->command);

        return match (true) {
            str_contains($command, 'branch --show-current') => "main\n",
            str_contains($command, 'show-ref --verify') => '',
            str_contains($command, 'rev-list --left-right') => "1\t2\n",
            str_contains($command, 'remote get-url') => "https://secret-token@github.com/acme/example.git\n",
            str_contains($command, 'rev-parse --short=12') => "aaaaaaaaaaaa\n",
            str_contains($command, 'status --porcelain') => $dirty ? " M routes/web.php\n" : '',
            str_contains($command, 'log -15') => implode("\x1e", [
                "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa\x1faaaaaaa\x1fAlice\x1f2026-10-17T10:00:00+07:00\x1fCommit pertama",
                "bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb\x1fbbbbbbb\x1fBob\x1f2026-10-16T09:00:00+07:00\x1fCommit kedua",
            ])."\x1e",
            default => '',
        };
    }
}
