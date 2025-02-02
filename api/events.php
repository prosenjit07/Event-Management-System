<?php
require_once 'config/database.php';

class EventsAPI {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function handleRequest() {
        header('Content-Type: application/json');
        
        $method = $_SERVER['REQUEST_METHOD'];
        $path = isset($_GET['action']) ? $_GET['action'] : '';

        try {
            switch ($path) {
                case 'upcoming':
                    $this->getUpcomingEvents();
                    break;
                case 'my-events':
                    $this->getMyEvents();
                    break;
                case 'my-registrations':
                    $this->getMyRegistrations();
                    break;
                case 'register':
                    $this->registerForEvent();
                    break;
                default:
                    if ($method === 'GET') {
                        $this->getAllEvents();
                    } elseif ($method === 'POST') {
                        $this->createEvent();
                    } else {
                        throw new Exception('Invalid endpoint');
                    }
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    private function getUpcomingEvents() {
        $stmt = $this->db->prepare("
            SELECT e.*, COUNT(er.id) as registered_count 
            FROM events e 
            LEFT JOIN event_registrations er ON e.id = er.event_id 
            WHERE e.date >= CURRENT_TIMESTAMP
            GROUP BY e.id 
            ORDER BY e.date ASC 
            LIMIT 6
        ");
        $stmt->execute();
        echo json_encode(['success' => true, 'events' => $stmt->fetchAll()]);
    }

    private function getMyEvents() {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('Authentication required');
        }

        $stmt = $this->db->prepare("
            SELECT e.*, COUNT(er.id) as registered_count 
            FROM events e 
            LEFT JOIN event_registrations er ON e.id = er.event_id 
            WHERE e.user_id = ?
            GROUP BY e.id 
            ORDER BY e.date DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        echo json_encode(['success' => true, 'events' => $stmt->fetchAll()]);
    }

    private function getMyRegistrations() {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('Authentication required');
        }

        $stmt = $this->db->prepare("
            SELECT e.*, er.created_at as registration_date 
            FROM events e 
            JOIN event_registrations er ON e.id = er.event_id 
            WHERE er.user_id = ?
            ORDER BY e.date ASC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        echo json_encode(['success' => true, 'registrations' => $stmt->fetchAll()]);
    }

    private function registerForEvent() {
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('Authentication required');
        }

        $eventId = filter_input(INPUT_POST, 'event_id', FILTER_VALIDATE_INT);
        if (!$eventId) {
            throw new Exception('Invalid event ID');
        }

        // Check if already registered
        $stmt = $this->db->prepare("SELECT id FROM event_registrations WHERE event_id = ? AND user_id = ?");
        $stmt->execute([$eventId, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            throw new Exception('Already registered for this event');
        }

        // Check capacity
        $stmt = $this->db->prepare("
            SELECT capacity, (SELECT COUNT(*) FROM event_registrations WHERE event_id = ?) as registered 
            FROM events WHERE id = ?
        ");
        $stmt->execute([$eventId, $eventId]);
        $event = $stmt->fetch();

        if ($event['registered'] >= $event['capacity']) {
            throw new Exception('Event is full');
        }

        // Register
        $stmt = $this->db->prepare("INSERT INTO event_registrations (event_id, user_id) VALUES (?, ?)");
        $stmt->execute([$eventId, $_SESSION['user_id']]);
        
        echo json_encode(['success' => true]);
    }
}

$api = new EventsAPI();
$api->handleRequest();