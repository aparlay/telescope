<?php

namespace Aparlay\Core\Helpers;

use Exception;

class Cdn
{

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled
     * @param  string  $url
     * @return string
     * @throws Exception
     */
    public static function avatar(string $url): string
    {
        if (empty($url)) {
            throw new Exception('Url is missing');
        }

        /** Return the input file url if cdn is not enabled */
        if (!config('app.cdn.enabled')) {
            return $url;
        }

        /** Check if given url is valid */
        self::validateUrl($url);

        /** Prepend the CDN Server Url and return the file url */
        return config('app.cdn.avatars').$url;
    }

    /**
     * @param  string  $url
     * @throws Exception
     */
    private static function validateUrl(string $url): void
    {
        $pattern = '|^http[s]{0,1}://|i';
        if (preg_match($pattern, $url)) {
            throw new Exception('Invalid URL. Use: /image.jpeg instead of full URI');
        }
    }

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled
     * @param  string  $url
     * @return string
     * @throws Exception
     */
    public static function cover(string $url): string
    {
        if (empty($url)) {
            throw new Exception('Url is missing');
        }

        /** Return the input file url if cdn is not enabled */
        if (!config('app.cdn.enabled')) {
            return $url;
        }

        /** Check if given url is valid */
        self::validateUrl($url);

        /** Prepend the CDN Server Url and return the file url */
        return config('app.cdn.covers').$url;
    }

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled
     * @param  string  $url
     * @return string
     * @throws Exception
     */
    public static function video(string $url): string
    {
        if (empty($url)) {
            throw new Exception('Url is missing');
        }

        if (!config('app.cdn.enabled')) {
            return $url;
        }

        /** Check if given url is valid */
        self::validateUrl($url);

        /** Prepend the CDN Server Url and return the file url */
        return config('app.cdn.covers').$url;
    }
}
