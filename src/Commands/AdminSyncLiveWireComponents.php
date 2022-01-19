<?php

namespace Aparlay\Core\Commands;

use Illuminate\Console\Command;

class AdminSyncLiveWireComponents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'core:sync-livewire-components';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        exec('cp packages/Aparlay/Core/config/livewire-components.php bootstrap/cache');

        return 0;
    }
}
