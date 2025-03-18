<?php
require_once 'db.php';

class Logs {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Function to add a log entry
    public function addLog($user_id, $action, $details) {
        $query = "INSERT INTO logs (user_id, action, details, created_at) VALUES (:user_id, :action, :details, NOW())";
        return $this->db->executeQuery($query, [
            'user_id' => $user_id,
            'action' => $action,
            'details' => $details
        ]);
    }

    // Function to retrieve all logs
    public function getAllLogs() {
        $query = "SELECT * FROM logs ORDER BY created_at DESC";
        return $this->db->fetchAll($query);
    }

    // Function to retrieve logs by user
    public function getLogsByUser($user_id) {
        $query = "SELECT * FROM logs WHERE user_id = :user_id ORDER BY created_at DESC";
        return $this->db->fetchAll($query, ['user_id' => $user_id]);
    }

    // Function to retrieve logs by action
    public function getLogsByAction($action) {
        $query = "SELECT * FROM logs WHERE action = :action ORDER BY created_at DESC";
        return $this->db->fetchAll($query, ['action' => $action]);
    }

    // Function to retrieve logs by date range
    public function getLogsByDateRange($start_date, $end_date) {
        $query = "SELECT * FROM logs WHERE created_at BETWEEN :start_date AND :end_date ORDER BY created_at DESC";
        return $this->db->fetchAll($query, [
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);
    }
}
?>
