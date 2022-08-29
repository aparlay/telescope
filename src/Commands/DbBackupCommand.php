<?php

namespace Aparlay\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DbBackupCommand extends Command
{
    public $signature = 'db:backup 
                         {--host=localhost : Database server IP address} 
                         {--port=27017 : Database mongodb port number} 
                         {--username= : Mongodb user account username} 
                         {--password= : Mongodb user account password} 
                         {--authSource=admin : Authentication Database} 
                         {--database= : Database schema name} 
                         {--output=data/dump : Output folder} 
                         {--gzip : Compress output}';

    public $description = 'This command is responsible to create backup from db';

    public function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');
        $database = $this->option('database');
        $username = $this->option('username');
        $password = $this->option('password');
        $authSource = $this->option('authSource');
        $output = $this->option('output');
        $gzip = $this->option('gzip');

        $process = new Process([
            'mongodump',
            '--host='.$host,
            '--port='.$port,
            '--database='.$database,
            '--authenticationDatabase='.($authSource ?? 'admin'),
            '--username='.$username,
            '--password='.$password,
            '--out='.$output,
            '--gzip',
            ]);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
        return self::SUCCESS;
    }
}
