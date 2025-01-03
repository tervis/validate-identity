# validate-identity

Php class to provide a static methods to validate various information from national identity numbers.

## Usage
```php
use Tervis\Validate\Identity\ValidateIdentityFIN;
// Common validation in Finnish national identity numbers
ValidateIdentityFIN:validatePIN('171001A413L'); // true
ValidateIdentityFIN::validateSSN('171001A413L'); // true
ValidateIdentityFIN::validateVAT('FI15728600'); // true
```