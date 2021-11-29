<?php

namespace Aparlay\Core\Helpers;

use Illuminate\Support\Arr;
use MongoDB\BSON\UTCDateTime;

class ActionButtonBladeComponent
{
    /**
     * @param $color
     * @param $name
     * @return string
     */
    public static function getBadge($color, $name): string
    {
        return '<span class="badge bg-'.$color.'">'.ucfirst($name).'</span>';
    }

    /**
     * @param $id
     * @param $resourceName
     * @return string
     */
    public static function getViewActionButton($id, $resourceName): string
    {
        return '<a class="btn btn-primary btn-sm" href="/'.$resourceName.'/'.$id.'" title="View"><i class="fas fa-eye"></i> View</a>';
    }

    /**
     * @param $name
     * @param $imagePath
     * @return string
     */
    public static function getAvatarWithName($name, $imagePath): string
    {
        return '<img class="table-avatar mr-1.5" src="'.$imagePath.'" alt="'.$name.'">'.$name;
    }

    /**
     * @param $user
     * @return string
     */
    public static function getUsernameWithAvatar($user): string
    {
        return '<img src="'.$user->avatar.'?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-50 mr-2">'.$user->username;
    }

    /**
     * @return string
     */
    public static function defaultValueNotSet(): string
    {
        return '<span class="text-danger">(not set)</span>';
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function castDisplayValue($value): mixed
    {
        if(Arr::accessible($value)) {
            return '<span class="text-success">(array)</span>';
        } elseif($value instanceof UTCDateTime) {
            return $value->toDateTime()->format('Y-m-d H:i:s');
        } else {
            return $value;
        }
    }
}
