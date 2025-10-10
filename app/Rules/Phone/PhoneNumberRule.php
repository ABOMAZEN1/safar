<?php

declare(strict_types=1);

namespace App\Rules\Phone;

use App\Enum\UserTypeEnum;
use Illuminate\Validation\Rule;

final class PhoneNumberRule
{
    private array $rules = [];

    private bool $isRequired = true;

    private bool $isString = true;

    private ?UserTypeEnum $uniqueForType = null;

    private ?UserTypeEnum $existsForType = null;

    private ?string $ignoreId = null;

    private function __construct() {}

    public static function make(): self
    {
        return new self();
    }

    public function optional(): self
    {
        $this->isRequired = false;

        return $this;
    }

    public function notString(): self
    {
        $this->isString = false;

        return $this;
    }

    public function unique(UserTypeEnum $userTypeEnum): self
    {
        $this->uniqueForType = $userTypeEnum;

        return $this;
    }

    public function exists(UserTypeEnum $userTypeEnum): self
    {
        $this->existsForType = $userTypeEnum;

        return $this;
    }

    public function ignore(string $id): self
    {
        $this->ignoreId = $id;

        return $this;
    }

    public function rules(): array
    {
        $this->rules[] = $this->isRequired
            ? 'required'
            : 'nullable';

        if ($this->isString) {
            $this->rules[] = 'string';
        }

        $this->rules[] = 'regex:/^09\d{8}$/';

        if ($this->uniqueForType instanceof UserTypeEnum) {
            $uniqueRule = Rule::unique('users', 'phone_number')
                ->where(fn ($query) => $query->where('type', $this->uniqueForType->value));

            if ($this->ignoreId !== null) {
                $uniqueRule->ignore($this->ignoreId);
            }

            $this->rules[] = $uniqueRule;
        }

        if ($this->existsForType instanceof UserTypeEnum) {
            $this->rules[] = Rule::exists('users', 'phone_number')
                ->where(fn ($query) => $query->where('type', $this->existsForType->value));
        }

        return $this->rules;
    }
}
