<?php

namespace Webmasterskaya\Component\OauthServer\Administrator\Helper;

/**
 * Class for convert string to different cases styles
 * such as camelCase, kebab-case etc.
 *
 * @since       version
 */
class CasesHelper
{
    /**
     * Convert string to snake_case_style
     *
     * @param string $string input string
     *
     * @return string
     *
     * @since version
     */
    public static function snakeize(string $string): string
    {
        $string = static::clean($string, '_');

        return strtolower($string);
    }

    /**
     * Convert string to kebab-case-style
     *
     * @param string $string input string
     *
     * @return string
     *
     * @since version
     */
    public static function kebabize(string $string): string
    {
        $string = static::clean($string, '-');

        return strtolower($string);
    }

    /**
     * Convert string to dotted.case.style
     *
     * @param string $string input string
     *
     * @return string
     *
     * @since version
     */
    public static function dottedize(string $string)
    {
        $string = static::clean($string, '.');

        return strtolower($string);
    }

    /**
     * Convert string to lowerCameCase, UpperCamelCase, PascalCase styles
     *
     * @param string $string input string
     * @param bool $uc_first true - to UpperCamelCase, false - to lowerCamelCase
     *
     * @return string
     *
     * @since version
     */
    public static function camelize(string $string, bool $uc_first = false): string
    {
        $string = self::snakeize($string);
        $string = ucwords($string, '_');
        $string = str_replace('_', '', $string);

        return $uc_first ? ucfirst($string) : lcfirst($string);
    }

    /**
     * Convert string to PascalCase (without starting digits)
     *
     * @param string $string input string
     *
     * @return string
     *
     * @since version
     */
    public static function classify(string $string): string
    {
        $string = self::camelize($string, true);
        $string = preg_replace('/^\d+/', '', $string);

        return ucfirst($string);
    }

    /**
     * @param string $string
     * @param string $replacement
     *
     * @return string
     *
     * @since version
     */
    private static function clean(string $string, string $replacement): string
    {
        if (empty($replacement)) {
            throw new \RuntimeException('The replacement cannot be empty');
        }

        // Prepare $replacement to use in regular expression
        $preg = preg_quote($replacement, '/');

        // Replace multiple spaces between characters
        $string = (string)preg_replace('/[\s.]+/', $replacement, $string);

        $legal_cars = str_replace($replacement, '', '_-.');
        $legal_cars = preg_quote($legal_cars);

        // Remove all illegal characters
        $string = (string)preg_replace('/[^0-9a-zA-Z' . $legal_cars . $preg . ']/', '', $string);

        // Replace all non-alphabetic characters with replacement
        $string = (string)preg_replace('/[' . $legal_cars . ']/', $replacement, $string);

        // Inserts a replacement before all words starting with uppercase characters
        $string = (string)preg_replace('/(.)([A-Z][a-z]+)/', '$1' . $replacement . '$2', $string);

        // Insert a replacement between digit and uppercase characters
        $string = (string)preg_replace('/([a-z\d])([A-Z])/', '$1' . $replacement . '$2', $string);

        // Replace multiple replacement
        $string = (string)preg_replace('/[' . $preg . ']+([A-Z0-9])/', $replacement . '$1', $string);

        // Return without replacement at the beginning and end of the line
        return trim($string, $replacement);
    }
}