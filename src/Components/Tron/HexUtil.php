<?php

namespace Aparlay\Core\Components\Tron;

use Exception;
use InvalidArgumentException;

class HexUtil
{
    /**
     * isZeroPrefixed.
     *
     * @param  string
     * @param mixed $value
     */
    public static function isZeroPrefixed($value): bool
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to isZeroPrefixed function must be string.');
        }

        return str_starts_with($value, '0x');
    }

    /**
     * Check if the string is a 16th notation.
     *
     * @param mixed $str
     */
    public static function isHex($str): bool
    {
        return is_string($str) and ctype_xdigit($str);
    }

    /**
     * hexToBin.
     *
     * @param  string
     * @param mixed $value
     */
    public static function hexToBin($value): string
    {
        if (!is_string($value)) {
            throw new InvalidArgumentException('The value to hexToBin function must be string.');
        }
        if (self::isZeroPrefixed($value)) {
            $count = 1;
            $value = str_replace('0x', '', $value, $count);
        }

        return pack('H*', $value);
    }

    /**
     * @param mixed $address
     *
     * @throws Exception
     */
    public static function validate($address): bool
    {
        $decoded = Base58::decode($address);

        $d1      = hash('sha256', substr($decoded, 0, 21), true);
        $d2      = hash('sha256', $d1, true);

        if (substr_compare($decoded, $d2, 21, 4)) {
            throw new Exception('bad digest');
        }

        return true;
    }

    /**
     * @param mixed $input
     *
     * @throws Exception
     */
    public static function decodeBase58($input): string
    {
        $alphabet = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';

        $out      = array_fill(0, 25, 0);
        for ($i = 0; $i < strlen($input); $i++) {
            if (($p = strpos($alphabet, $input[$i])) === false) {
                throw new Exception('invalid character found');
            }
            $c = $p;
            for ($j = 25; $j--;) {
                $c += (int) (58 * $out[$j]);
                $out[$j] = (int) ($c % 256);
                $c /= 256;
                $c       = (int) $c;
            }
            if ($c != 0) {
                throw new Exception('address too long');
            }
        }

        $result   = '';
        foreach ($out as $val) {
            $result .= chr($val);
        }

        return $result;
    }

    /**
     * @param mixed $pubkey
     *
     * @throws Exception
     */
    public static function pubKeyToAddress($pubkey): string
    {
        return '41' . substr(Keccak::hash(substr(hex2bin($pubkey), 1), 256), 24);
    }

    /**
     * Test if a string is prefixed with "0x".
     *
     * @param string $str
     *                    String to test prefix
     *
     * @return bool
     *              TRUE if string has "0x" prefix or FALSE
     */
    public static function hasHexPrefix($str): bool
    {
        return str_starts_with($str, '0x');
    }

    /**
     * Remove Hex Prefix "0x".
     *
     * @param string $str
     */
    public static function removeHexPrefix($str): string
    {
        if (!self::hasHexPrefix($str)) {
            return $str;
        }

        return substr($str, 2);
    }
}
