<?php
include '../includes/conn.php';

class DatabaseExport {
    private $db;
    private $output = '';
    
    public function __construct() {
        $this->db = new Database();
    }
    
    public function exportDatabase($tables = '*', $download = true) {
        $conn = $this->db->open();
        
        try {
            // Get all tables if none specified
            if ($tables == '*') {
                $tables = [];
                $stmt = $conn->query('SHOW TABLES');
                while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                    $tables[] = $row[0];
                }
            } else {
                $tables = is_array($tables) ? $tables : explode(',', $tables);
            }
            
            // Disable foreign key checks
            $this->output .= "SET FOREIGN_KEY_CHECKS=0;\n\n";
            
            // Process each table
            foreach ($tables as $table) {
                // Get table structure
                $stmt = $conn->query("SHOW CREATE TABLE $table");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->output .= "\n\n" . $row[1] . ";\n\n";
                
                // Get table data
                $stmt = $conn->query("SELECT * FROM $table");
                $columnCount = $stmt->columnCount();
                
                while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
                    $this->output .= "INSERT INTO $table VALUES(";
                    for ($j = 0; $j < $columnCount; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        $row[$j] = str_replace("\n", "\\n", $row[$j]);
                        if (isset($row[$j])) {
                            $this->output .= '"' . $row[$j] . '"';
                        } else {
                            $this->output .= 'NULL';
                        }
                        if ($j < ($columnCount - 1)) {
                            $this->output .= ',';
                        }
                    }
                    $this->output .= ");\n";
                }
            }
            
            // Enable foreign key checks
            $this->output .= "\n\nSET FOREIGN_KEY_CHECKS=1;";
            
            $backup_file_name = 'ecomm' . date('Y-m-d_H-i-s') . '.sql';
            
            if ($download) {
                // Force download
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $backup_file_name . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . strlen($this->output));
                echo $this->output;
                exit;
            } else {
                // Save file locally
                $file_handler = fopen($backup_file_name, 'w+');
                fwrite($file_handler, $this->output);
                fclose($file_handler);
                return "Database backup created successfully: " . $backup_file_name;
            }
            
        } catch (Exception $e) {
            return "An error occurred: " . $e->getMessage();
        } finally {
            $this->db->close();
        }
    }
}

try {
    $export = new DatabaseExport();
    $export->exportDatabase('*', true); 
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>