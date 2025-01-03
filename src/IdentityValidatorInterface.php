<?php
declare(strict_types=1);

namespace Tervis\Validate\Identity;

interface IdentityValidatorInterface
{
    /**
     * Validate Personal Identity Number
     *
     * @param string $number number to be validated
     * @return bool true if number is valid, false otherwise
     */
    public static function validatePIN(string $number): bool;

    /**
     * Validate Social Security Number
     *
     * @param string $number number to be validated
     * @return bool true if number is valid, false otherwise
     */
    public static function validateSSN(string $number): bool;

    /**
     * Validate Value Added Tax number
     *
     * @param string $number number to be validated
     * @return bool true if number is valid, false otherwise
     */
    public static function validateVAT(string $number): bool;
}