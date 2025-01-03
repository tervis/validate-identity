<?php
declare(strict_types=1);

namespace Tervis\Validate\Identity\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tervis\Validate\Identity\ValidateIdentityFIN;

class ValidateIdentityFINTest extends TestCase
{

    #[DataProvider('pinDataProvider')]
    public function testValidatePIN($number, $expected): void
    {
        $result = ValidateIdentityFIN::validatePIN($number);
        $this->assertSame($expected, $result);
    }

    #[DataProvider('pinDataProvider')]
    public function testValidateSSN($number, $expected): void
    {
        $result = ValidateIdentityFIN::validateSSN($number);
        $this->assertSame($expected, $result);
    }

    #[DataProvider('vatDataProvider')]
    public function testValidateVAT($number, $expected): void
    {
        $result = ValidateIdentityFIN::validateVAT($number);
        $this->assertSame($expected, $result);
    }

    public static function pinDataProvider(): iterable
    {
        yield ['170583+123C', true];
        yield ['311280-888Y', true];
        yield ['171001A413L', true];
        yield ['311280-8880', false];
    }

    public static function vatDataProvider(): iterable
    {
        yield ['123', false];
        yield ['0592509-666', false];
        yield ['string', false];
        yield ['*', false];
        yield ['010101A1234', false];
        yield ['010201-123N', false];
        yield ['01001-123N', false];
        yield ['01745920123', false];
        yield ['0592509-6', false]; //not in vat-format
        yield ['FI15728600', true];
        yield ['010101-123N', false]; //SSN
    }
}