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
        $query = "SELECT * FROM logs WHERE 1=1";
        $params = [];

        if (isset($filters['user_id'])) {
            $query .= " AND user_id = :userId";
            $params['userId'] = $filters['user_id'];
        }

        if (isset($filters['action'])) {
            $query .= " AND action = :action";
            $params['action'] = $filters['action'];
        }

        if (isset($filters['date_from'])) {
            $query .= " AND created_at >= :dateFrom";
            $params['dateFrom'] = $filters['date_from'];
        }

        if (isset($filters['date_to'])) {
            $query .= " AND created_at <= :dateTo";
            $params['dateTo'] = $filters['date_to'];
        }

        return $this->db->fetchAll($query, $params);
    }
}
?>
