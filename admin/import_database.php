<?php
include 'includes/conn.php';

class DatabaseManager {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function importDatabase($filePath) {
        $conn = $this->db->open();
        try {
            $sql = file_get_contents($filePath);
            $conn->exec($sql);
            echo "Database imported successfully.";
        } catch (Exception $e) {
            echo "Error importing database: " . $e->getMessage();
        } finally {
            $this->db->close();
        }
    }
}

if ($_FILES['import_file']['error'] === UPLOAD_ERR_OK) {
    $filePath = $_FILES['import_file']['tmp_name'];
    $manager = new DatabaseManager();
    $manager->importDatabase($filePath);
} else {
    echo "Error uploading file.";
}
?>