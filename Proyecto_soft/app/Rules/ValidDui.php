<?php

namespace App\Rules;

use App\Services\DuiService;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidDui implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    /**
     * Provide access to the full validation data set if it is needed.
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Run the validation rule against the attribute.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('El DUI debe ser una cadena de texto.');
            return;
        }

        $digits = DuiService::digits($value);
        if (strlen($digits) !== 9) {
            $fail('El DUI debe contener exactamente 9 dígitos (formato ########-#).');
            return;
        }

        if (! DuiService::isValid($value)) {
            $fail('Ingresa un DUI salvadoreño válido (8 dígitos + guion + dígito verificador).');
        }
    }
}
