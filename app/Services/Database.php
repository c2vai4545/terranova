<?php
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }
        $configPathLocal = BASE_PATH . '/app/Config/config.local.php';
        $configPath = BASE_PATH . '/app/Config/config.php';
        $config = file_exists($configPathLocal)
            ? require $configPathLocal
            : require $configPath;
        $db = $config['db'];
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', $db['host'], $db['name'], $db['charset']);
        $pdo = new PDO($dsn, $db['user'], $db['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        self::$pdo = $pdo;
        return self::$pdo;
    }
}
