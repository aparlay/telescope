<?php

namespace Aparlay\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DbRestoreCommand extends Command
{
    public $signature = 'db:restore 
                         {--h|host=localhost : Database server IP address} 
                         {--P|port=27017 : Database mongodb port number} 
                         {--u|username= : Mongodb user account username} 
                         {--p|password= : Mongodb user account password} 
                         {--authenticationDatabase=admin : Authentication Database} 
                         {--d|db= : Database schema name} 
                         {--o|out=data/dump : Output folder} 
                         {--gzip : Compress output}';

    public $description = 'This command is responsible to restore backup from db';

    public function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');
        $database = $this->option('db');
        $username = $this->option('username');
        $password = $this->option('password');
        $authSource = $this->option('authenticationDatabase');
        $output = $this->option('out');
        $gzip = $this->option('gzip');

        $process = new Process([
            'mongorestore',
            '--host='.$host,
            '--port='.$port,
            '--database='.$database,
            '--authenticationDatabase='.($authSource ?? 'admin'),
            '--username='.$username,
            '--password='.$password,
            '--out='.$output,
            $gzip ? '--gzip' : '',
        ]);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();

        return self::SUCCESS;
    }
}
