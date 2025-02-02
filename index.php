<?php
session_start();

// Error reporting for development
if (getenv('ENVIRONMENT') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Load required files
require_once 'config/database.php';
require_once 'controllers/MainController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/EventController.php';

// Simple router
$route = isset($_GET['route']) ? $_GET['route'] : 'home';

// Authentication middleware
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?route=login');
        exit;
    }
}

// Check if user is not logged in and trying to access protected routes
if (!isset($_SESSION['user_id']) && $route === 'home') {
    header('Location: index.php?route=login');
    exit;
}

// Initialize controllers
$mainController = new MainController();
$authController = new AuthController();
$eventController = new EventController();

// Route handling
try {
    switch ($route) {
        // Auth routes
        case 'login':
            $authController->login();
            break;
        case 'register':
            $authController->register();
            break;
        case 'logout':
            $authController->logout();
            break;

        // Event routes
        case 'events':
            requireAuth();
            $eventController->index();
            break;
        case 'events/create':
            requireAuth();
            $eventController->create();
            break;
        case 'events/edit':
            requireAuth();
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $eventController->edit($id);
            break;
        case 'events/delete':
            requireAuth();
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $eventController->delete($id);
            break;
        case 'events/register':
            requireAuth();
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $eventController->register($id);
            break;

        // API routes
        case 'api/events/upcoming':
            require_once 'api/events.php';
            break;
        case 'api/events/my-events':
            requireAuth();
            require_once 'api/events.php';
            break;
        case 'api/events/my-registrations':
            requireAuth();
            require_once 'api/events.php';
            break;

        // Report generation
        case 'events/report':
            requireAuth();
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            $mainController->generateReport($id);
            break;

        // Add this new case for all reports
        case 'events/report/all':
            requireAuth();
            $mainController->generateAllReports();
            break;

        // Default route (home)
        case 'home':
        default:
            $mainController->home();
            break;
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    include 'views/error.php';
}