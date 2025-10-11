<?php
class Validation
{
    public static function isRut8(string $rut): bool
    {
        return (bool) preg_match('/^\d{8}$/', $rut);
    }

    public static function requireFields(array $data, array $required): array
    {
        $errors = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $errors[] = "Falta el campo: {$field}";
            }
        }
        return $errors;
    }
}
