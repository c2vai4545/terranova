<?php
class AppConfig
{
    private static ?array $config = null;

    public static function all(): array
    {
        if (self::$config !== null) {
            return self::$config;
        }
        $configPathLocal = BASE_PATH . '/app/Config/config.local.php';
        $configPath = BASE_PATH . '/app/Config/config.php';
        $config = file_exists($configPathLocal)
            ? require $configPathLocal
            : require $configPath;
        self::$config = is_array($config) ? $config : [];
        return self::$config;
    }

    public static function get(string $path, $default = null)
    {
        $config = self::all();
        $segments = explode('.', $path);
        $value = $config;
        foreach ($segments as $seg) {
            if (!is_array($value) || !array_key_exists($seg, $value)) {
                return $default;
            }
            $value = $value[$seg];
        }
        return $value;
    }
}
