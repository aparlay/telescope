<?php

namespace Aparlay\Core\Tests\Feature\Commands;

use Aparlay\Core\Tests\TestCase;

class CoreCommandTest extends TestCase
{
    /**
     * @test
     */
    public function core_command_index()
    {
        $this->artisan('core:index')->assertExitCode(0);
    }
}
