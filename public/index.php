<?php
session_start();

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Config/config.php';

function requireDirectory(string $directory): void
{
    if (!is_dir($directory)) {
        return;
    }
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
    foreach ($iterator as $file) {
        if ($file->isFile() && substr($file->getFilename(), -4) === '.php') {
            require_once $file->getPathname();
        }
    }
}

requireDirectory(BASE_PATH . '/app/Models');
requireDirectory(BASE_PATH . '/app/Services');
requireDirectory(BASE_PATH . '/app/Middleware');
requireDirectory(BASE_PATH . '/app/Controllers');

function view(string $template, array $data = []): void
{
    $viewPath = BASE_PATH . '/app/Views/' . $template . '.php';
    if (!file_exists($viewPath)) {
        http_response_code(500);
        echo 'Vista no encontrada: ' . htmlspecialchars($template);
        return;
    }
    extract($data, EXTR_OVERWRITE);
    require BASE_PATH . '/app/Views/layout/header.php';
    require $viewPath;
    require BASE_PATH . '/app/Views/layout/footer.php';
}

function jsonResponse(array $payload, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($payload);
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit();
}

$routes = require BASE_PATH . '/app/Config/routes.php';

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($uri !== '/' && substr($uri, -1) === '/') {
    $uri = rtrim($uri, '/');
}

// Servir assets estáticos fuera de public (compatibilidad con estructura existente)
if ($method === 'GET' && $uri === '/estilos.css') {
    $cssPath = BASE_PATH . '/estilos.css';
    if (is_file($cssPath)) {
        header('Content-Type: text/css');
        readfile($cssPath);
        exit();
    }
}
if ($method === 'GET' && strpos($uri, '/imgs/') === 0) {
    $imgPath = BASE_PATH . $uri;
    if (is_file($imgPath)) {
        $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
        $mime = 'application/octet-stream';
        if (in_array($ext, ['png'])) $mime = 'image/png';
        elseif (in_array($ext, ['jpg', 'jpeg'])) $mime = 'image/jpeg';
        elseif ($ext === 'gif') $mime = 'image/gif';
        elseif ($ext === 'webp') $mime = 'image/webp';
        header('Content-Type: ' . $mime);
        readfile($imgPath);
        exit();
    }
}
$routeKey = $method . ' ' . $uri;

if (!isset($routes[$routeKey])) {
    http_response_code(404);
    echo 'Ruta no encontrada: ' . htmlspecialchars($routeKey);
    exit();
}

[$controllerClass, $action] = $routes[$routeKey];

if (!class_exists($controllerClass) || !method_exists($controllerClass, $action)) {
    http_response_code(500);
    echo 'Controlador o acción inválida.';
    exit();
}

$controller = new $controllerClass();
$controller->$action();
