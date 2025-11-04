<?php
/**
 * Validator
 *  - Valida y sanea datos de entrada según un esquema de reglas.
 *  - No depende de librerías externas para mantener la simplicidad del proyecto.
 *
 * Uso rápido:
 *   $schema = [
 *       'rut' => ['required' => true, 'regex' => '/^\d{8}$/'],
 *       'nombre' => ['required' => true, 'min' => 1, 'max' => 50],
 *       'edad' => ['type' => 'int', 'min' => 0],
 *   ];
 *   [$ok, $cleanData, $errors] = Validator::validate($_POST, $schema);
 */
class Validator
{
    /**
     * Valida y sanea datos según un esquema.
     * @param array $input  Datos de entrada (por ej. $_POST, $_GET o payload JSON)
     * @param array $schema Reglas por campo
     * @return array [bool $ok, array $clean, array $errors]
     */
    public static function validate(array $input, array $schema): array
    {
        $clean = [];
        $errors = [];

        foreach ($schema as $field => $rules) {
            $value = $input[$field] ?? null;

            // Campo requerido
            if (($rules['required'] ?? false) && ($value === null || $value === '')) {
                $errors[] = "Falta el campo: {$field}";
                continue;
            }

            // Si no es requerido y viene vacío, omitir más validaciones pero mantener null
            if ($value === null || $value === '') {
                $clean[$field] = null;
                continue;
            }

            // Saneado básico (cadenas)
            if (!is_array($value)) {
                $value = is_string($value) ? trim($value) : $value;
            }

            // Tipo
            $type = $rules['type'] ?? 'string';
            switch ($type) {
                case 'int':
                    if (!filter_var($value, FILTER_VALIDATE_INT) && !ctype_digit((string)$value)) {
                        $errors[] = "{$field} debe ser un número entero";
                        continue 2;
                    }
                    $value = (int)$value;
                    break;
                case 'float':
                    if (!filter_var($value, FILTER_VALIDATE_FLOAT)) {
                        $errors[] = "{$field} debe ser un número";
                        continue 2;
                    }
                    $value = (float)$value;
                    break;
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "{$field} debe ser un correo válido";
                        continue 2;
                    }
                    $value = strtolower($value);
                    break;
                case 'string':
                default:
                    $value = htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    break;
            }

            // Longitud mínima/máxima (solo para strings)
            if (is_string($value)) {
                $len = strlen($value);
                if (isset($rules['min']) && $len < $rules['min']) {
                    $errors[] = "{$field} debe tener al menos {$rules['min']} caracteres";
                    continue;
                }
                if (isset($rules['max']) && $len > $rules['max']) {
                    $errors[] = "{$field} no puede superar {$rules['max']} caracteres";
                    continue;
                }
            }

            // Regex personalizada
            if (isset($rules['regex']) && !preg_match($rules['regex'], (string)$value)) {
                $errors[] = "{$field} tiene un formato inválido";
                continue;
            }

            // Enumeración / lista
            if (isset($rules['in']) && !in_array($value, $rules['in'], true)) {
                $allowed = implode(', ', $rules['in']);
                $errors[] = "{$field} debe ser uno de: {$allowed}";
                continue;
            }

            $clean[$field] = $value;
        }

        return [empty($errors), $clean, $errors];
    }
}