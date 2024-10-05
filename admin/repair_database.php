<?php
session_start();
require_once 'includes/conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['admin']) || $_SESSION['admin']['type'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = $pdo->open();
        
        // Get all table names
        $stmt = $conn->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $results = [];
        foreach ($tables as $table) {
            $repairStmt = $conn->prepare("REPAIR TABLE $table");
            $repairStmt->execute();
            $repairResult = $repairStmt->fetchAll(PDO::FETCH_ASSOC);
            $results[$table] = $repairResult[0]['Msg_text'];
        }
        
        echo json_encode(['success' => true, 'message' => 'Database repair completed', 'results' => $results]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database repair failed: ' . $e->getMessage()]);
    } finally {
        $pdo->close();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>