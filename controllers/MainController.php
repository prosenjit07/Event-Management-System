<?php
class MainController {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function home() {
        try {
            // Fetch upcoming events
            $stmt = $this->db->prepare("
                SELECT e.*, COUNT(er.id) as registered_count 
                FROM events e 
                LEFT JOIN event_registrations er ON e.id = er.event_id 
                WHERE e.date >= CURDATE() 
                GROUP BY e.id 
                ORDER BY e.date ASC 
                LIMIT 5
            ");
            $stmt->execute();
            $upcomingEvents = $stmt->fetchAll();

            include 'views/home.php';
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error = "Failed to load homepage.";
            include 'views/error.php';
        }
    }

    public function generateReport($eventId) {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            exit('Access denied');
        }

        try {
            // Get event details
            $eventStmt = $this->db->prepare("
                SELECT name, date 
                FROM events 
                WHERE id = ?
            ");
            $eventStmt->execute([$eventId]);
            $event = $eventStmt->fetch();

            if (!$event) {
                throw new Exception("Event not found");
            }

            // Get registrations
            $stmt = $this->db->prepare("
                SELECT 
                    u.name,
                    u.email,
                    er.created_at as registration_date
                FROM event_registrations er
                JOIN users u ON er.user_id = u.id
                WHERE er.event_id = ?
                ORDER BY er.created_at ASC
            ");
            $stmt->execute([$eventId]);
            $registrations = $stmt->fetchAll();

            // Generate CSV filename
            $filename = sprintf(
                'event_registrations_%s_%s.csv',
                preg_replace('/[^a-z0-9]+/i', '_', $event['name']),
                date('Y-m-d')
            );

            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            // Create CSV
            $output = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Write headers
            fputcsv($output, [
                'Event: ' . $event['name'],
                'Date: ' . date('Y-m-d H:i', strtotime($event['date']))
            ]);
            fputcsv($output, []); // Empty line
            fputcsv($output, ['Name', 'Email', 'Registration Date']);

            // Write data
            foreach ($registrations as $registration) {
                fputcsv($output, [
                    $registration['name'],
                    $registration['email'],
                    date('Y-m-d H:i', strtotime($registration['registration_date']))
                ]);
            }

            fclose($output);
        } catch (Exception $e) {
            error_log("Report generation failed: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            echo "Failed to generate report: " . $e->getMessage();
        }
    }

    public function generateAllReports() {
        // Check if user is admin
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header('HTTP/1.1 403 Forbidden');
            exit('Access denied');
        }
    
        try {
            // Get all events with registrations and counts
            $stmt = $this->db->prepare("
                SELECT 
                    e.id, 
                    e.name, 
                    e.date,
                    e.capacity,
                    COUNT(er.id) as registered_count
                FROM events e
                LEFT JOIN event_registrations er ON e.id = er.event_id
                GROUP BY e.id
                ORDER BY e.date DESC
            ");
            $stmt->execute();
            $events = $stmt->fetchAll();
    
            if (empty($events)) {
                header('HTTP/1.1 404 Not Found');
                exit("No events found to generate report.");
            }
    
            // Clear any previous output
            if (ob_get_level()) ob_end_clean();
    
            // Set up CSV file
            $filename = 'events_report_' . date('Y-m-d_His') . '.csv';
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            $output = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
            // Write CSV headers
            fputcsv($output, ['Name', 'Date', 'Capacity', 'Registered']);
    
            // Write event data
            foreach ($events as $event) {
                fputcsv($output, [
                    $event['name'],
                    date('Y-m-d H:i', strtotime($event['date'])),
                    $event['capacity'],
                    $event['registered_count']
                ]);
            }
    
            fclose($output);
            exit();

        } catch (Exception $e) {
            error_log("Report generation failed: " . $e->getMessage());
            header('HTTP/1.1 500 Internal Server Error');
            exit("Failed to generate reports: " . $e->getMessage());
        }
    }
}