<?php

namespace lib\Validation;

class RequestValidator
{
    private $errors = [];

    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $this->validateField($field, $value, $fieldRules);
        }

        return count($this->errors) === 0;
    }

    private function validateField(string $field, $value, string $rules): void
    {
        $ruleList = explode('|', $rules);

        foreach ($ruleList as $rule) {
            $this->applyRule($field, $value, $rule);
        }
    }

    private function applyRule(string $field, $value, string $rule): void
    {
        if (strpos($rule, ':') !== false) {
            list($ruleName, $ruleParam) = explode(':', $rule, 2);
        } else {
            $ruleName = $rule;
            $ruleParam = null;
        }

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== 0 && $value !== '0') {
                    $this->addError($field, "$field is required");
                }
                break;

            case 'numeric':
                if (!is_numeric($value)) {
                    $this->addError($field, "$field must be numeric");
                }
                break;

            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, "$field must be a valid email");
                }
                break;

            case 'min':
                if ($value < $ruleParam) {
                    $this->addError($field, "$field must be at least $ruleParam");
                }
                break;

            case 'max':
                if ($value > $ruleParam) {
                    $this->addError($field, "$field must not exceed $ruleParam");
                }
                break;

            case 'in':
                $allowedValues = explode(',', $ruleParam);
                if (!in_array($value, $allowedValues)) {
                    $this->addError($field, "$field must be one of: $ruleParam");
                }
                break;
        }
    }

    private function addError(string $field, string $message): void
    {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return count($this->errors) > 0;
    }
}
