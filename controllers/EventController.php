<?php
class EventController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            $stmt = $this->db->prepare("SELECT * FROM events ORDER BY date DESC LIMIT ? OFFSET ?");
            $stmt->execute([$limit, $offset]);
            $events = $stmt->fetchAll();

            $totalStmt = $this->db->query("SELECT COUNT(*) FROM events");
            $total = $totalStmt->fetchColumn();
            $totalPages = ceil($total / $limit);

            include 'views/events/list.php';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Failed to fetch events.";
            include 'views/error.php';
        }
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
            $description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);
            $date = $_POST['date'];
            $capacity = (int)$_POST['capacity'];

            try {
                $stmt = $this->db->prepare("INSERT INTO events (name, description, date, capacity, user_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $description, $date, $capacity, $_SESSION['user_id']]);
                header('Location: index.php?route=events');
                exit;
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error = "Failed to create event.";
            }
        }
        include 'views/events/create.php';
    }

    public function edit($id) {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $stmt = $this->db->prepare("UPDATE events SET name = ?, description = ?, date = ?, capacity = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'],
                    $_POST['date'],
                    $_POST['capacity'],
                    $id,
                    $_SESSION['user_id']
                ]);
                header('Location: index.php?route=events');
                exit;
            }

            $stmt = $this->db->prepare("SELECT * FROM events WHERE id = ?");
            $stmt->execute([$id]);
            $event = $stmt->fetch();

            include 'views/events/edit.php';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Failed to edit event.";
            include 'views/error.php';
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM events WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $_SESSION['user_id']]);
            header('Location: index.php?route=events');
            exit;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Failed to delete event.";
            include 'views/error.php';
        }
    }

    public function register($eventId) {
        try {
            // Check if event has available capacity
            $stmt = $this->db->prepare("
                SELECT capacity, 
                (SELECT COUNT(*) FROM event_registrations WHERE event_id = ?) as registered 
                FROM events WHERE id = ?
            ");
            $stmt->execute([$eventId, $eventId]);
            $event = $stmt->fetch();

            if ($event['registered'] < $event['capacity']) {
                $stmt = $this->db->prepare("INSERT INTO event_registrations (event_id, user_id) VALUES (?, ?)");
                $stmt->execute([$eventId, $_SESSION['user_id']]);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Event is full']);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            echo json_encode(['success' => false, 'error' => 'Registration failed']);
        }
    }
}