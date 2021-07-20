<?php

namespace Aparlay\Core\Tests\Feature\Commands;

class CoreCommandTest extends \Aparlay\Core\Tests\TestCase
{
    /** @test */
    public function the_core_command_works()
    {
        $this->artisan('core')->assertExitCode(0);
    }
}
