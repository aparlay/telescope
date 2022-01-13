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

    public static function modalButton($btnText, $dataId, $target, $color = 'primary')
    {
        return
            '<button type="submit" class="btn btn-'.$color.' btn-xs"
                data-toggle="modal" data-target="'.$target.'"
                data-id="'.$dataId.'"
                >
                '.$btnText.'</button>';
    }

    /**
     * @param $id
     * @param $resourceName
     * @return string
     */
    public static function link($text, $url): string
    {
        return '<a class="btn btn-success btn-sm" target="_blank" href="'.$url.'" title="'.$text.'"><i class=""></i>'.$text.'</a>';
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

    public static function getViewDeleteActionButton($id, $resourceName)
    {
        $buttons = '<div class="d-flex justify-content-center"><a class="btn btn-primary btn-sm" href="/'.$resourceName.'/'.$id.'" title="View"><i class="fas fa-eye"></i> View</a>';
        $buttons .= '<form action="'.route('core.admin.setting.delete', ['setting' => $id]).'" method="POST">
                '.csrf_field().method_field('DELETE').'
                <a class="btn btn-danger btn-sm ml-3 delete" href="" title="Delete"><i class="fas fa-trash"></i> Delete</a>
            </form></div>';

        return $buttons;
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
        return '<img src="'.$user->avatar.'?aspect_ratio=1:1&width=150" alt="" class="img-circle img-size-50 mr-2">'.
            $user->username.
            ($user->is_online ? ' <span class="text-info text-sm fas fa-circle"></span>' : ' <span class="text-gray text-sm far fa-circle"></span>');
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
        if (Arr::accessible($value)) {
            return '<span class="text-success">(array)</span>';
        } elseif ($value instanceof UTCDateTime) {
            return $value->toDateTime()->format('Y-m-d H:i:s');
        } else {
            return $value;
        }
    }
}
