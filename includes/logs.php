<?php
require_once 'db.php';

class Logs {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function logAction($userId, $action, $details = null) {
        $query = "INSERT INTO logs (user_id, action, details, created_at) VALUES (:userId, :action, :details, NOW())";
        $this->db->executeQuery($query, [
            'userId' => $userId,
            'action' => $action,
            'details' => $details
        ]);
    }

    public function getLogs($filters = []) {
        $query = "SELECT logs.*, users.username FROM logs LEFT JOIN users ON logs.user_id = users.id WHERE 1=1";
        $params = [];

        if (!empty($filters['user_id'])) {
            $query .= " AND logs.user_id = :userId";
            $params['userId'] = $filters['user_id'];
        }

        if (!empty($filters['action'])) {
            $query .= " AND logs.action = :action";
            $params['action'] = $filters['action'];
        }

        if (!empty($filters['date_from'])) {
            $query .= " AND logs.created_at >= :dateFrom";
            $params['dateFrom'] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $query .= " AND logs.created_at <= :dateTo";
            $params['dateTo'] = $filters['date_to'];
        }

        return $this->db->fetchAll($query, $params);
    }

    public function addLog($userId, $action, $description) {
        // Example implementation: Log to a database or a file
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] User ID: $userId | Action: $action | Description: $description\n";

        // Append log to a file (logs.txt)
        file_put_contents('logs.txt', $logEntry, FILE_APPEND);
    }
}
?>
