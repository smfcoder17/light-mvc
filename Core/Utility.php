<?php

declare(strict_types=1);

namespace Core;

/**
 * Utility class - Helper methods for string manipulation and validation
 * 
 * Provides static utility methods for common operations like
 * string case conversion and variable validation.
 * 
 * @package Core
 * @author Light-MVC
 * @version 2.0.0
 */
class Utility
{
    /**
     * Convert a string to StudlyCaps format
     * Example: 'user-profile' becomes 'UserProfile'
     * 
     * @param string $string String to convert (kebab-case)
     * @return string String in StudlyCaps format
     */
    public static function toStudlyCaps(string $string): string
    {
        return preg_replace_callback(['/^([a-z])/', '/-([a-z])/'], function (array $match): string {
            return strtoupper($match[1]);
        }, $string) ?? $string;
    }

    /**
     * Convert a string to camelCase format
     * Example: 'user-profile' becomes 'userProfile'
     * 
     * @param string $string String to convert (kebab-case)
     * @return string String in camelCase format
     */
    public static function toCamelCase(string $string): string
    {
        return lcfirst(self::toStudlyCaps($string));
    }

    /**
     * Check if all passed parameters are defined (isset)
     * Useful for validating multiple variables at once
     * 
     * @param mixed ...$args Variables to check
     * @return bool True if all variables are set, false otherwise
     */
    public static function areSet(mixed ...$args): bool
    {
        foreach ($args as $var) {
            if (!isset($var))  return false;
        }

        return true;
    }
}
