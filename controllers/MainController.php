<?php
class MainController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function home() {
        include 'views/home.php';
    }

    public function getData() {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->query("SELECT * FROM items");
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'data' => $items]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
?>