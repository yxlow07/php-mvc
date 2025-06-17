<?php

namespace core\Models;

abstract class ValidationModel extends BaseModel
{
    const RULE_REQUIRED = 'required';
    const RULE_MIN = 'min';
    const RULE_MAX = 'max';
    const RULE_MATCH = 'match';
    const RULE_EMAIL = 'email';
    const RULE_UNIQUE = 'unique';
    const RULE_UNDEFINED = 'undefined';

    const ERROR_MESSAGES = [
        self::RULE_REQUIRED => '{field} is required',
        self::RULE_MIN => '{field} must be at least {min} characters',
        self::RULE_MAX => '{field} must be at most {max} characters',
        self::RULE_EMAIL => '{field} must be a valid email',
        self::RULE_MATCH => '{field} {match}',
        self::RULE_UNIQUE => 'Record with this {field} already exists',
        self::RULE_UNDEFINED => 'Undefined error occured. Contact an admin',
    ];

    public array $errors = [];

    abstract public function rules(): array;
    abstract public function fieldNames(): array;

    public function validate($rulesArray = []): bool
    {
        $rules_model = empty($rulesArray) ? $this->rules() : $rulesArray;

        foreach ($rules_model as $attribute => $rules) {
            $value = $this->{$attribute};

            foreach ($rules as $rule) {
                $ruleName = is_string($rule) ? $rule : $rule[0];
                $this->validateAttribute($ruleName, $value, $rule, $attribute);
            }
        }
        return empty($this->errors);
    }

    private function validateAttribute(string $ruleName, mixed $value, string|array $rule, string $attribute): void
    {
        match ($ruleName) {
            self::RULE_REQUIRED => $this->addError(!empty($value), $attribute, $ruleName),
            self::RULE_EMAIL => $this->addError(filter_var($value, FILTER_VALIDATE_EMAIL), $attribute, $ruleName),
            self::RULE_MIN => $this->addError(strlen($value) >= $rule['min'], $attribute, $ruleName, ['min', $rule['min']]),
            self::RULE_MAX => $this->addError(strlen($value) <= $rule['max'], $attribute, $ruleName, ['max', $rule['max']]),
            self::RULE_MATCH => $this->addError($value == $this->{$rule['match']}, $attribute, $ruleName, ['match', $rule['matchMsg']]),
            default => $this->addError(false, $attribute, $ruleName),
        };
    }

    public function addError(bool $eval, string $attribute, string $ruleName, mixed $ruleValue = []): bool
    {
        if($eval) return true;
        $subject = str_replace("{field}", $this->fieldNames()[$attribute] ?? $attribute, self::ERROR_MESSAGES[$ruleName]);
        $this->errors[$attribute][] = empty($ruleValue) ? $subject : str_replace("{{$ruleValue[0]}}", $ruleValue[1], $subject);
        return false;
    }
}