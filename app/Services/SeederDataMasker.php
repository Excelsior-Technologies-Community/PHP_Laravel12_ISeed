<?php

namespace App\Services;

class SeederDataMasker
{
    /**
     * Standard sensitive field patterns.
     */
    protected array $defaultMaskRules = [
        'password' => '$2y$12$e8hGFmE4/m1k5tB6V9zW3e1dE.w8vF0wKjM.9V0vU8X7m2qY3o6tC', // bcrypt('password')
        'remember_token' => 'test_token_sample',
        'credit_card' => 'XXXX-XXXX-XXXX-1234',
        'card_number' => 'XXXX-XXXX-XXXX-1234',
        'cvv' => '000',
        'api_key' => 'sanitized_test_api_key_xxxxxxxx',
        'secret' => 'sanitized_secret_xxxxxxxx',
    ];

    /**
     * Apply masking rules to a generated seeder PHP file.
     */
    public function maskSeederFile(string $filePath, array $customRules = []): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $code = file_get_contents($filePath);
        $maskedCode = $this->maskSeederCode($code, $customRules);

        return file_put_contents($filePath, $maskedCode) !== false;
    }

    /**
     * Apply masking rules to seeder code string.
     */
    public function maskSeederCode(string $code, array $customRules = []): string
    {
        $rules = array_merge($this->defaultMaskRules, $customRules);

        // 1. Mask specific fixed rules
        foreach ($rules as $field => $replacement) {
            // Match 'field' => 'value' or 'field' => 123
            $pattern = "/('".preg_quote($field, '/')."'\s*=>\s*)(?:'[^']*'|\"[^\"]*\"|[0-9]+|NULL|null)/i";
            $code = preg_replace_callback($pattern, function ($matches) use ($field, $replacement) {
                return $matches[1] . "'" . addslashes($replacement) . "'";
            }, $code);
        }

        // 2. Anonymize emails with dynamic sequence or user{id}@example.test
        $rowId = 1;
        $code = preg_replace_callback("/('email'\s*=>\s*)(?:'[^']*'|\"[^\"]*\")/i", function ($matches) use (&$rowId) {
            $maskedEmail = 'user' . ($rowId++) . '@example.test';
            return $matches[1] . "'" . $maskedEmail . "'";
        }, $code);

        // 3. Anonymize phone/mobile numbers
        $phoneId = 1000;
        $code = preg_replace_callback("/('(?:phone|mobile|telephone|contact_no)'\s*=>\s*)(?:'[^']*'|\"[^\"]*\")/i", function ($matches) use (&$phoneId) {
            $maskedPhone = '+91 98000 ' . ($phoneId++);
            return $matches[1] . "'" . $maskedPhone . "'";
        }, $code);

        // 4. Add header disclaimer annotation in the docblock
        if (!str_contains($code, 'GDPR Data Masking Applied')) {
            $disclaimer = "/**\n * Auto generated seed file (GDPR Data Masking Applied)\n * Sensitive fields (passwords, emails, tokens, cards) have been sanitized.\n";
            $code = str_replace("/**\n     * Auto generated seed file", $disclaimer, $code);
        }

        return $code;
    }
}
