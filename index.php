<?php
session_start();
require_once 'config/database.php';
require_once 'controllers/MainController.php';

// Simple router
$route = $_GET['route'] ?? 'home';

$controller = new MainController();
switch ($route) {
    case 'home':
        $controller->home();
        break;
    case 'api/data':
        $controller->getData();
        break;
    default:
        http_response_code(404);
        echo "404 Not Found";
        break;
}
?>