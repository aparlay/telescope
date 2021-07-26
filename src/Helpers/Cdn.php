<?php

namespace Aparlay\Core\Helpers;

class Cdn {

    /**
     * Responsible for returning the avatar URL based on filename if cdn is enabled
     * @param string $url
     * @return string
     */
    public static function avatar($url = null)
    {
        /** Empty url validation */
        $url = (string) $url;
        if(empty($url))
        {
            throw new \Exception('Url is missing');
        }

        /** Return the input file url if cdn is not enabled */
        if(!config('app.cdn.enabled'))
        {
            return $url;
        }

        /** Check if given url is valid */
        $pattern = '|^http[s]{0,1}://|i';        
        if(preg_match($pattern, $url))
        {
            throw new \Exception('Invalid URL. ' .
                'Use: /image.jpeg instead of full URI: ' .
                'http://domain.com/image.jpeg.'
            );
        }

        /** Prepend the CDN Server Url and return the file url */
        return config('app.cdn.avatars') . $url;
    }
}