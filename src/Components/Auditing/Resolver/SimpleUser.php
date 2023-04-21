<?php

namespace Aparlay\Core\Components\Auditing\Resolver;

use Illuminate\Support\Facades\Auth;
use MongoDB\BSON\ObjectId;
use OwenIt\Auditing\Contracts\Auditable;

class SimpleUser implements \OwenIt\Auditing\Contracts\Resolver
{
    /**
     * @return null
     */
    public static function resolve(Auditable $auditable)
    {
        return \Auth::check() ? [
            '_id' => new ObjectId(Auth::getUser()->_id),
            'username' => Auth::getUser()->username,
            'avatar' => Auth::getUser()->avatar,
        ] : null;
    }
}
