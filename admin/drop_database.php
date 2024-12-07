<?php
include 'includes/conn.php';

class DatabaseManager {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function dropDatabase() {
        $conn = $this->db->open();
        try {
            $conn->exec("SET FOREIGN_KEY_CHECKS=0;");
            $stmt = $conn->query('SHOW TABLES');
            while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                $conn->exec("DROP TABLE IF EXISTS `{$row[0]}`;");
            }
            $conn->exec("SET FOREIGN_KEY_CHECKS=1;");
            echo "Database dropped successfully.";
        } catch (Exception $e) {
            echo "Error dropping database: " . $e->getMessage();
        } finally {
            $this->db->close();
        }
    }
}

$manager = new DatabaseManager();
$manager->dropDatabase();
?>