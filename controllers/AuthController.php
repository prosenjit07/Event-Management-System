<?php
class AuthController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            try {
                if (!$this->db) {
                    throw new Exception("Database connection not established");
                }

                // Test database connection
                try {
                    $test = $this->db->query("SELECT 1")->fetch();
                    error_log("Database connection test successful");
                } catch (PDOException $e) {
                    error_log("Database connection test failed: " . $e->getMessage());
                    throw new Exception("Database connection test failed");
                }

                $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['name'] = $user['name'];
                    
                    header('Location: index.php?route=home');
                    exit;
                }
                
                $error = "Invalid email or password";
                
            } catch (PDOException $e) {
                error_log("Database Error in login: " . $e->getMessage());
                $error = "Login failed. Database error occurred.";
            } catch (Exception $e) {
                error_log("General Error in login: " . $e->getMessage());
                $error = "Login failed. Please try again.";
            }
        }

        include 'views/auth/login.php';
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);

            try {
                $stmt = $this->db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
                if ($stmt->execute([$name, $email, $password])) {
                    header('Location: index.php?route=login&success=1');
                    exit;
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = "Registration failed. Please try again.";
            }
        }
        include 'views/auth/register.php';
    }

    public function logout() {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Clear all session data
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();

        // Show logout page
        include 'views/auth/logout.php';
    }
}