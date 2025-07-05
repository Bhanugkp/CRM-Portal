<?php
// Initialize environment
date_default_timezone_set("Asia/Kolkata");  // Fixed case sensitivity
header('Content-Type: application/json');  // Set JSON response header

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Error reporting configuration
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error.log');

// Validate and sanitize action parameter
$action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);

if (!$action) {
    http_response_code(400);
    die(json_encode(['status' => 'error', 'message' => 'Action parameter is required']));
}

// Log action securely
file_put_contents("log.txt", date('Y-m-d H:i:s') . " - Action: " . $action . PHP_EOL, FILE_APPEND);

// Include class file with validation
$classFile = 'admin_class.php';
if (!file_exists($classFile)) {
    http_response_code(500);
    die(json_encode(['status' => 'error', 'message' => 'System configuration error']));
}

include $classFile;

try {
    $crud = new Action();
    
    // Validate CSRF token for state-changing actions
    $csrfRequiredActions = ['login', 'save_member', 'save_user', 'save_dept', 'save_village', 'delete_dept'];
    if (in_array($action, $csrfRequiredActions)) {
        if (!validateCsrfToken()) {
            http_response_code(403);
            die(json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']));
        }
    }

    // Route actions
    switch ($action) {
        case 'login':
            $response = $crud->login();
            break;
            
        case 'logout':
            $response = $crud->logout();
            break;
            
        case 'save_member':
            $response = $crud->save_member();
            break;
            
        case 'delete_member':
            $response = $crud->delete_member();
            break;
            
        case 'save_user':
            $response = $crud->save_user();
            break;
            
        case 'delete_user':
            $response = $crud->delete_user();
            break;
            
        case 'save_dept':
            $response = $crud->save_dept();
            file_put_contents("dept_log.txt", date('Y-m-d H:i:s') . " - " . json_encode($response) . PHP_EOL, FILE_APPEND);
            break;
            
        case 'save_village':
            $response = $crud->save_village();
            file_put_contents("village_log.txt", date('Y-m-d H:i:s') . " - " . json_encode($response) . PHP_EOL, FILE_APPEND);
            break;
            
        case 'delete_dept':
            $response = $crud->delete_dept();
            break;
            
        case 'create_user':
            $response = $crud->create_user();
            break;
            
        default:
            http_response_code(404);
            die(json_encode(['status' => 'error', 'message' => 'Invalid action']));
    }

    // Send response
    if ($response) {
        echo is_array($response) ? json_encode($response) : $response;
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Action failed']);
    }

} catch (Exception $e) {
    http_response_code(500);
    error_log("Exception in handler: " . $e->getMessage());
    die(json_encode(['status' => 'error', 'message' => 'System error occurred']));
}

/**
 * Validate CSRF token
 */
function validateCsrfToken() {
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

$action = $_GET['action'] ?? '';

// For actions that modify state, require CSRF validation
$csrfProtectedActions = ['login', 'logout', 'save_member', 'save_user'];

if (in_array($action, $csrfProtectedActions) && !validateCsrfToken()) {
    http_response_code(403);
    die(json_encode(['status' => 'error', 'message' => 'Invalid CSRF token']));
}