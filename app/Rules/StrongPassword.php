<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    private array $blocklist = [
        'password', '123456', 'qwerty', 'abc123', 'password123', 'admin', 'letmein',
        'welcome', 'monkey', 'dragon', 'master', 'hello', 'freedom', 'whatever',
        'qazwsx', '111111', '000000', 'asdfgh', 'zxcvbn'
    ];

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value): bool
    {
        $value = strtolower($value);

        // Blocklist check
        if (in_array($value, $this->blocklist)) {
            return false;
        }

        // Min length 8
        if (strlen($value) < 8) {
            return false;
        }

        // Repeated characters
        if (preg_match('/(.)\1{2,}/', $value)) {
            return false;
        }

        // Keyboard sequences (simple patterns)
        $keyboardPatterns = ['qwertyuiop', 'asdfghjkl', 'zxcvbnm', '1234567890'];
        foreach ($keyboardPatterns as $pattern) {
            if (preg_match('/' . substr($pattern, 0, 6) . '/i', $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'Password must be at least 8 characters, no repeated letters, sequences, or blocked words.';
    }
}

