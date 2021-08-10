<?php

namespace Aparlay\Core\Services;

use Aparlay\Core\Api\V1\Models\Follow;
use Aparlay\Core\Api\V1\Models\Media;
use Aparlay\Core\Api\V1\Requests\MediaRequest;
use Aparlay\Core\Models\Media as BaseMedia;
use Aparlay\Core\Models\User;
use MongoDB\BSON\ObjectId;

class MediaService
{
    /**
     * @param int $length
     * @return string
     */
    public static function generateSlug(int $length): string
    {
        $slug = substr(str_shuffle('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);

        return (null === Media::slug($slug)->first()) ? $slug : self::generateSlug($length);
    }

    /**
     * @param Media $media
     * @return Media
     * @throws \Exception
     */
    public static function parseDescription(BaseMedia $media): BaseMedia
    {
        $description = trim($media->description);
        $tags = $people = [];
        foreach (explode(' ', $description) as $item) {
            if (isset($item[0]) && $item[0] === '#' && substr_count($item, '#') === 1) {
                $tags[] = substr($item, 1);
            }
            if (isset($item[0]) && $item[0] === '@' && substr_count($item, '@') === 1) {
                $people[] = substr($item, 1);
            }
        }
        $media->hashtags = array_slice($tags, 0, 20);
        $people = array_slice($people, 0, 20);
        $users = [];
        $usersQuery = User::select(['username', 'avatar', '_id'])->usernames($people)->limit(20)->get();
        foreach ($usersQuery->toArray() as $user) {
            $users[] = $media->createSimpleUser($user);
        }
        $media->people = $users;

        return $media;
    }
}
