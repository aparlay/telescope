<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Events\ServerAlarmEvent;
use Aparlay\Core\Helpers\BladeHelper;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;

class ServerMonitorCommand extends Command implements Isolatable
{
    public $signature = 'server:monitor';

    public $description = 'This command is responsible to check server resources and trigger an event on critical situations';

    public function handle(): int
    {
        $alarm = false;
        $messages = [];
        if (disk_free_space('/') < config('app.monitor.minimum_disk_space')) {
            $alarm = true;
            $messages['Disk Space'] = BladeHelper::fileSize(disk_free_space('/')).'/'.BladeHelper::fileSize(disk_total_space('/'));
        }

        $load = sys_getloadavg();
        if ($load[0] > (config('app.monitor.maximum_cpu_load_last_min') * 1_000_000)) {
            $alarm = true;
            $messages['System Avg Load'] = "1m[{$load[0]}] 5m[{$load[1]}] 15m[{$load[2]}]";
        }

        $memory = $this->availableMemory();

        if ($memory < (config('app.monitor.minimum_disk_space') * 1_000_000)) {
            $alarm = true;
            $messages['System Available Mem'] = BladeHelper::fileSize($memory);
        }

        if ($alarm) {
            ServerAlarmEvent::dispatch(config('app.server_specific_queue'), $messages);
        }

        return self::SUCCESS;
    }

    private function availableMemory()
    {
        $fh = fopen('/proc/meminfo', 'r');
        $memory = 0;
        while ($line = fgets($fh)) {
            $pieces = [];
            if (preg_match('/^MemTotal:\s+(\d+)\skB$/', $line, $pieces)) {
                $memory = $pieces[1];
                break;
            }
        }
        fclose($fh);

        return $memory;
    }
}
