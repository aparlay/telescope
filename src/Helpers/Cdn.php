<?php

namespace Aparlay\Core\Helpers;

use Exception;

class Cdn
{
    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled.
     *
     * @throws Exception
     */
    public static function avatar(string $url): string
    {
        if (empty($url)) {
            throw new Exception('avatar file is missing');
        }

        /* Return the input file url if cdn is not enabled */
        if (!config('app.cdn.enabled')) {
            return $url;
        }

        /* Prepend the CDN Server Url and return the file url */
        return config('app.cdn.avatars') . $url;
    }

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled.
     *
     * @throws Exception
     */
    public static function cover(string $url): string
    {
        if (empty($url)) {
            throw new Exception('cover file is missing');
        }

        if (!config('app.cdn.enabled')) {
            return $url;
        }

        return config('app.cdn.covers') . $url;
    }

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled.
     *
     * @throws Exception
     */
    public static function video(string $url): string
    {
        if (empty($url)) {
            throw new Exception('video file is missing');
        }

        if (!config('app.cdn.enabled')) {
            return $url;
        }

        return config('app.cdn.videos') . $url;
    }

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled.
     *
     * @throws Exception
     */
    public static function galleryVideo(string $url): string
    {
        if (empty($url)) {
            throw new Exception('gallery file is missing');
        }

        if (!config('app.cdn.enabled')) {
            return $url;
        }

        return config('app.cdn.galleries.video') . $url;
    }

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled.
     *
     * @throws Exception
     */
    public static function galleryImage(string $url): string
    {
        if (empty($url)) {
            throw new Exception('gallery file is missing');
        }

        if (!config('app.cdn.enabled')) {
            return $url;
        }

        return config('app.cdn.galleries.image') . $url;
    }

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled.
     *
     * @throws Exception
     */
    public static function document(string $url): string
    {
        if (empty($url)) {
            throw new Exception('document file is missing');
        }

        if (!config('app.cdn.enabled')) {
            return $url;
        }

        return config('app.cdn.document') . $url;
    }
}
