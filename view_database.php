<?php
include 'includes/conn.php';


class DatabaseViewer {
    private $db;
    private $conn;
    
    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->open();
    }
    
    public function getAllTables() {
        try {
            $stmt = $this->conn->query('SHOW TABLES');
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            echo "Error getting tables: " . $e->getMessage();
            return [];
        }
    }
    
    public function getTableColumns($tableName) {
        try {
            $stmt = $this->conn->query("SHOW COLUMNS FROM $tableName");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (PDOException $e) {
            echo "Error getting columns: " . $e->getMessage();
            return [];
        }
    }
    
    public function getTableRecords($tableName, $page = 1, $limit = 50) {
        try {
            $offset = ($page - 1) * $limit;
            $stmt = $this->conn->prepare("SELECT * FROM $tableName LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error getting records: " . $e->getMessage();
            return [];
        }
    }
    
    public function getTotalRecords($tableName) {
        try {
            $stmt = $this->conn->query("SELECT COUNT(*) FROM $tableName");
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            echo "Error counting records: " . $e->getMessage();
            return 0;
        }
    }
    
    public function __destruct() {
        $this->db->close();
    }
}


// Create DatabaseViewer instance
$viewer = new DatabaseViewer();

// Get selected table or default to first table
$tables = $viewer->getAllTables();
$selectedTable = isset($_GET['table']) ? $_GET['table'] : (count($tables) > 0 ? $tables[0] : null);
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
?>

<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="">
    
    
    <div class="">
        <section class="">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Database Tables</h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="list-group">
                                        <?php foreach ($tables as $table): ?>
                                            <a href="?table=<?php echo urlencode($table); ?>" 
                                               class="list-group-item <?php echo ($table == $selectedTable) ? 'active' : ''; ?>">
                                                <?php echo htmlspecialchars($table); ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <?php if ($selectedTable): ?>
                                        <div class="box box-primary">
                                            <div class="box-header with-border">
                                                <h3 class="box-title">Table: <?php echo htmlspecialchars($selectedTable); ?></h3>
                                                <span class="pull-right">Total Records: 
                                                    <?php echo $viewer->getTotalRecords($selectedTable); ?>
                                                </span>
                                            </div>
                                            <div class="box-body table-responsive">
                                                <table class="table table-bordered table-striped">
                                                    <?php 
                                                    $columns = $viewer->getTableColumns($selectedTable);
                                                    $records = $viewer->getTableRecords($selectedTable, $page);
                                                    ?>
                                                    <thead>
                                                        <tr>
                                                            <?php foreach ($columns as $column): ?>
                                                                <th><?php echo htmlspecialchars($column); ?></th>
                                                            <?php endforeach; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($records as $record): ?>
                                                            <tr>
                                                                <?php foreach ($columns as $column): ?>
                                                                    <td>
                                                                        <?php 
                                                                        $value = $record[$column] ?? 'NULL';
                                                                        echo htmlspecialchars(strlen($value) > 100 ? substr($value, 0, 100) . '...' : $value); 
                                                                        ?>
                                                                    </td>
                                                                <?php endforeach; ?>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
   
</div>
<?php include 'includes/scripts.php'; ?>
</body>
</html>