<?php
// Permitir autenticación por header 'X-Session-Id' (alternativa a cookies para apps nativas)
$sidHeader = $_SERVER['HTTP_X_SESSION_ID'] ?? null;
if ($sidHeader && is_string($sidHeader)) {
    @session_id($sidHeader);
}

// Configurar cookie de sesión apta para apps móviles (HTTPS, SameSite=None)
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None',
]);
session_start();

// Encabezados de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
// Content-Security-Policy (CSP) - Ajustar según las necesidades de la aplicación
// Ejemplo básico: permite recursos del mismo origen y algunos CDNs
header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdn.jsdelivr.net https://code.jquery.com; style-src 'self' https://cdn.jsdelivr.net 'unsafe-inline'; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net;");

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/app/Config/config.php';

function requireDirectory(string $directory): void
{
    if (!is_dir($directory)) {
        return;
    }
    // Añadir bandera SKIP_DOTS para evitar procesar directorios . y ..
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isFile() && substr($file->getFilename(), -4) === '.php') {
            require_once $file->getPathname();
        }
    }
}

// Cargar AuthApiController.php explícitamente para asegurar que esté disponible
require_once BASE_PATH . '/app/Controllers/AuthApiController.php';

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

    $responsePayload = $payload;

    // Check if the payload indicates an error and reformat it for consistency
    if (isset($payload['error'])) {
        $responsePayload = [
            'status' => 'error',
            'message' => $payload['error']
        ];
    }

    // CORS sólo para API
    if (isApiRequest()) {
        header('Access-Control-Allow-Origin: *'); // TODO: Configurar para dominios específicos en producción
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Credentials: true');
    }
    echo json_encode($responsePayload);
    exit();
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

function isApiRequest(): bool
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return strpos($path, '/api/') === 0;
}

// function sendCorsHeaders(): void
// {
//     header('Access-Control-Allow-Origin: *');
//     header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
//     header('Access-Control-Allow-Headers: Content-Type, Authorization');
// }

// Responder preflight para API
if ($method === 'OPTIONS' && isApiRequest()) {
    // sendCorsHeaders(); // Eliminado, la lógica CORS ahora está en jsonResponse
    http_response_code(204);
    exit();
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
    if (isApiRequest()) {
        jsonResponse(['error' => 'not_found', 'route' => $routeKey], 404);
    } else {
        http_response_code(404);
        echo 'Ruta no encontrada: ' . htmlspecialchars($routeKey);
    }
    exit();
}

[$controllerClass, $action] = $routes[$routeKey];

if (!class_exists($controllerClass) || !method_exists($controllerClass, $action)) {
    if (isApiRequest()) {
        jsonResponse(['error' => 'invalid_handler'], 500);
    } else {
        http_response_code(500);
        echo 'Controlador o acción inválida.';
    }
    exit();
}

$controller = new $controllerClass();
$controller->$action();
