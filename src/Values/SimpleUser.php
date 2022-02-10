<?php

namespace Aparlay\Core\Values;

use Aparlay\Core\Casts\SimpleUserCast;
use Illuminate\Contracts\Database\Eloquent\Castable;

/**
 * Class SimpleUser.
 */
class SimpleUser implements Castable
{
    /**
     * @var string
     */
    public string $_id;
    /**
     * @var string
     */
    public string $username;

    /**
     * @var string
     */
    public string $avatar;

    /**
     * @var bool
     */
    public bool $is_followed;

    /**
     * @var bool
     */
    public bool $is_liked;

    /**
     * @param  array  $arguments
     * @return string
     */
    public static function castUsing(array $arguments): string
    {
        return SimpleUserCast::class;
    }
}
