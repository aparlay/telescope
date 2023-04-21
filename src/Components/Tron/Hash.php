<?php

namespace Aparlay\Core\Components\Tron;

class Hash
{
    /**
     * Hashing SHA-256.
     *
     * @param bool  $raw
     * @param mixed $data
     *
     * @return string
     */
    public static function SHA256($data, $raw = true)
    {
        return hash('sha256', $data, $raw);
    }

    /**
     * Double hashing SHA-256.
     *
     * @param mixed $data
     *
     * @return string
     */
    public static function sha256d($data)
    {
        return hash('sha256', hash('sha256', $data, true), true);
    }

    /**
     * Hashing RIPEMD160.
     *
     * @param bool  $raw
     * @param mixed $data
     *
     * @return string
     */
    public static function RIPEMD160($data, $raw = true)
    {
        return hash('ripemd160', $data, $raw);
    }
}
