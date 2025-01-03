<?php
declare(strict_types=1);

namespace Tervis\Validate\Identity;

class ValidateIdentityFIN implements IdentityValidatorInterface
{

    private const CONTROL_KEYS = "0123456789ABCDEFHJKLMNPRSTUVWXY";
    private const PATTERN_SSN = "/^(\d{2})(\d{2})(\d{2})([+\-UVWXYABCDEF])(\d{3})([0-9A-Z])$/";
    private const PATTERN_FINUID = "/^(\d{8})([0-9A-Z])$/";
    private const PATTERN_VAT = "/^(FI)\d{6,12}$/";
    private const PATTERN_BIN = "/^\d{6,7}-\d$/";

    /**
     * Validate Personal Identity Number
     *
     * The Finnish Personal Identity Number (HETU) is a 11 digit number in format ddmmyycxxxy
     *
     * Henkilötunnus on muotoa PPKKVVSNNNT, jossa:
     * PPKKVV   Syntymäaika; päivä, kuukausi ja kaksi viimeistä numeroa vuosiluvusta; tarvittaessa käytetään etunollia
     * SNNN     Yksilönumero
     * S        Vuosiluvun kaksi ensimmäistä numeroa osoittava välimerkki,
     *              "+" 1800-luvulla syntyneillä,
     *              "-", "U", "V", "W", "X" tai "Y" 1900-luvulla syntyneillä,
     *              "A", "B", "C", "D", "E" tai "F" 2000-luvulla syntyneillä
     * NNN      Kolminumeroinen luku, etunollitettu, naisilla parillinen, miehillä pariton
     * T        Tarkistusmerkki
     *
     * @param string $number number to be validated
     * @return bool true if number is valid, false otherwise
     */
    public static function validatePIN(string $number): bool
    {
        $regs = [];
        $number = strtoupper($number);

        if (preg_match(self::PATTERN_SSN, $number, $regs)) {
            $century = match ($regs[4]) {
                '+' => 18, //1800-luvulla syntyneillä
                "-", "U", "V", "W", "X", "Y" => 19, // 1900-luvulla syntyneet ja maahan muuttaneet
                default => 20, // rest keys: "A", "B", "C", "D", "E", "F" // 2000-luvulla syntyneet ja maahan muuttaneet
            };
            // Validate date of birth. Must be a Gregorian date.
            if (checkdate((int)$regs[2], (int)$regs[1], (int)($century.$regs[3]))) {
                $test = $regs[1].$regs[2].$regs[3].$regs[5];
                $control = str_split(self::CONTROL_KEYS);
                return $control[intval($test) % 31] == $regs[6];
            }
        }
        return false;
    }

    /**
     * Validate Social Security Number
     * @param string $number number to be validated
     * @return bool true if number is valid, false otherwise
     */
    public static function validateSSN(string $number): bool
    {
        return self::validatePIN($number);
    }

    /**
     * Validate Value Added Tax number
     *
     * The Finnish VAT number (ALV-numero) is maximum of 14 digits and is generated from Business ID.
     *
     * FORMAT: FIxxxxxxxy
     *
     * @param string $number number to be validated
     * @return bool true if number is valid, false otherwise
     * @link http://tarkistusmerkit.teppovuori.fi/tarkmerk.htm#alv-numero
     */
    public static function validateVAT(string $number): bool
    {
        $number = strtoupper($number);
        $countryCode = substr($number, 0, 2);
        $controlNum = substr($number, -1, 1);
        $businessNum = substr($number, 2, -1);
        $businessId = $businessNum . '-' . $controlNum;
        if ($countryCode == 'FI' && self::validateBusinessId($businessId)) {
            return true;
        }
        return false;
    }

    /**
     * Validate Finnish Business ID (Y-tunnus).
     *
     * The Finnish Business ID number is a 9 digit number, and the last digit is a control number (y).
     *
     * Format: xxxxxxx-y
     *
     * @param string $businessId
     * @return bool true if Business ID is valid, false otherwise
     * @link http://tarkistusmerkit.teppovuori.fi/tarkmerk.htm#y-tunnus2
     */
    public static function validateBusinessId(string $businessId): bool
    {
        if (preg_match(self::PATTERN_BIN, $businessId)) {
            list($num, $control) = explode('-', $businessId);
            // Add leading zeros if number is < 7
            $num = str_pad($num, 7, '0', STR_PAD_LEFT);
            $controlSum = 0;
            $controlSum += (int)substr($num, 0, 1) * 7;
            $controlSum += (int)substr($num, 1, 1) * 9;
            $controlSum += (int)substr($num, 2, 1) * 10;
            $controlSum += (int)substr($num, 3, 1) * 5;
            $controlSum += (int)substr($num, 4, 1) * 8;
            $controlSum += (int)substr($num, 5, 1) * 4;
            $controlSum += (int)substr($num, 6, 1) * 2;
            $controlSum = $controlSum % 11;

            if ($controlSum == 0) {
                return $controlSum == $control;
            }

            if ($controlSum >= 2 && $controlSum <= 10) {
                return (11 - $controlSum) == $control;
            }
        }
        return false;
    }

    /**
     * Validate Finnish Unique Identification Number (SATU).
     *
     * FINUID (SATU) is a 9 digit number. The last digit is a control number.
     *
     * Example: 10011187H
     *
     * @param string $number number to be validated
     * @return bool true if number is valid, false otherwise
     * @link http://tarkistusmerkit.teppovuori.fi/tarkmerk.htm#satu
     */
    public static function validateFINUID(string $number): bool
    {
        $regs = [];
        $number = strtoupper($number);
        $control = str_split(self::CONTROL_KEYS);
        if (preg_match(self::PATTERN_FINUID, $number, $regs)) {
            return $control[intval($regs[1]) % 31] == $regs[2];
        }
        return false;
    }


}