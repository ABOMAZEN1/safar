<?php

declare(strict_types=1);

namespace App\Rules\Auth;

use Illuminate\Validation\Rules\Password;

final class PasswordRule
{
    private array $rules = [];

    private bool $isRequired = true;

    private bool $isConfirmed = false;

    private function __construct()
    {
        $this->rules[] = Password::default();
    }

    public static function make(): self
    {
        return new self();
    }

    public function optional(): self
    {
        $this->isRequired = false;

        return $this;
    }

    public function confirmed(): self
    {
        $this->isConfirmed = true;

        return $this;
    }

    public function rules(): array
    {
        $rules = [];

        $rules[] = $this->isRequired ? 'required' : 'nullable';

        $rules[] = 'string';

        $rules[] = Password::default();

        if ($this->isConfirmed) {
            $rules[] = 'confirmed';
        }

        return $rules;
    }
}
