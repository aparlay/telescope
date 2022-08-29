<?php

namespace Aparlay\Core\Commands;

use Aparlay\Core\Api\V1\Models\User;
use Aparlay\Core\Helpers\DT;
use Aparlay\Core\Models\Analytic;
use Aparlay\Core\Models\Email;
use Aparlay\Core\Models\Media;
use Aparlay\Core\Models\MediaLike;
use Aparlay\Core\Models\MediaVisit;
use Illuminate\Console\Command;

class DbBackupCommand extends Command
{
    public $signature = 'db:backup {host=localhost : Database server IP address} {port=27017 : Database mongodb port number} {username?} {password?} {authSource=admin : Authentication Database} {database?} {--O|output=} {--C|compress}';

    public $description = 'This command is responsible to create backup from db';

    public function handle()
    {
        $host = $this->argument('host');
        $port = $this->argument('port');
        $username = $this->argument('username');
        $password = $this->argument('password');
        $authSource = $this->argument('authSource');
        $output = $this->option('output');
        $compress = $this->option('compress');

        return self::SUCCESS;
    }
}
