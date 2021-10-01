<?php

namespace Aparlay\Core\Api\V1\Models;

use Aparlay\Core\Api\V1\Casts\SimpleUser;
use Aparlay\Core\Models\Block as BlockBase;

class Block extends BlockBase
{

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'user' => SimpleUser::class,
        'creator' => SimpleUser::class,
    ];
}
