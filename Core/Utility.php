<?php
namespace Core;

class Utility
{
    /**
     * Convert a string to StudlyCaps format
     * @return string converted value
     */
    public static function toStudlyCaps($string)
    {
        return preg_replace_callback(['/^([a-z])/', '/-([a-z])/'], function ($match) {
            return strtoupper($match[1]);
        }, $string);
    }
    /**
     * Convert a string to camelCase format
     * @return string converted value
     */
    public static function toCamelCase($string)
    {
        return lcfirst(self::toStudlyCaps($string));
    }

    /**
     * Check if all passed parameters are defined.
     * @param array $args list of variables
     */
    public static function areSet(...$args)
    {
        foreach ($args as $var) {
            if (!isset($var))  return false;
        }

        return true;
    }
}