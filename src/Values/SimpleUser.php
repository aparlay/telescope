<?php

namespace Aparlay\Core\Values;

use Aparlay\Core\Casts\SimpleUserCast;
use Illuminate\Contracts\Database\Eloquent\Castable;

/**
 * Class SimpleUser.
 */
class SimpleUser implements Castable
{
    public string $_id;

    public string $username;

    public string $avatar;

    public bool $is_followed;

    public bool $is_liked;

    public static function castUsing(array $arguments): string
    {
        return SimpleUserCast::class;
    }
}
