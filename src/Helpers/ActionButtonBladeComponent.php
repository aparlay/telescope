<?php

namespace Aparlay\Core\Helpers;

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
}
