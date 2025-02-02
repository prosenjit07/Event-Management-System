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
            try {
                // Validate input
                $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
                $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm_password'];
    
                // Additional server-side validation
                if (!$email || !$username || strlen($password) < 6) {
                    throw new Exception("Invalid input data");
                }
    
                if ($password !== $confirmPassword) {
                    throw new Exception("Passwords do not match");
                }
    
                // Check if email already exists
                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    throw new Exception("Email already registered");
                }
    
                // Hash password and insert user
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $this->db->prepare(
                    "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')"
                );
                
                if ($stmt->execute([$username, $email, $hashedPassword])) {
                    header('Location: index.php?route=login&success=1');
                    exit;
                } else {
                    throw new Exception("Registration failed");
                }
    
            } catch (Exception $e) {
                error_log($e->getMessage());
                $error = $e->getMessage();
            }
        }
        
        include 'views/auth/register.php';
    }
}