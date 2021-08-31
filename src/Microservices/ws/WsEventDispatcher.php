<?php

namespace Aparlay\Core\Microservices\ws;

use Exception;

interface WsEventDispatcher
{

    /**
     * @throws Exception
     */
    public function execute();
}
