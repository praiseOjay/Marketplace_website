<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;

class HoneypotValidator
{
    /**
     * Common honeypot field names that bots automatically fill.
     */
    private const HONEYPOT_FIELDS = [
        'website_url',
        'fax_number',
        'hp_security_check',
    ];

    /**
     * Returns true if any honeypot field contains data (indicating a bot).
     */
    public function isSpam(?Request $request): bool
    {
        if (!$request) {
            return false;
        }

        foreach (self::HONEYPOT_FIELDS as $field) {
            $value = $request->request->get($field);
            if ($value !== null && trim((string) $value) !== '') {
                return true;
            }
        }

        return false;
    }
}
