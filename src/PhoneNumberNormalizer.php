<?php

namespace TechLegend\LaravelBeemAfrica;

final class PhoneNumberNormalizer
{
    public function __construct(
        private readonly ?string $defaultCountryCallingCode = null,
    ) {}

    public function normalize(string $phone): string
    {
        // Strip spaces, dashes, parentheses
        $cleaned = preg_replace('/[\s\-\(\)]+/', '', $phone);
        assert(is_string($cleaned));

        // Remove leading +
        $cleaned = ltrim($cleaned, '+');

        // Strip any remaining non-digit characters
        $digits = preg_replace('/\D+/', '', $cleaned);
        assert(is_string($digits));

        // If the number looks local (9-10 digits) and a default country code is set, prepend it
        if ($this->defaultCountryCallingCode !== null
            && strlen($digits) >= 9
            && strlen($digits) <= 10
        ) {
            $code = ltrim($this->defaultCountryCallingCode, '+');
            $digits = $code . $digits;
        }

        return $digits;
    }
}
