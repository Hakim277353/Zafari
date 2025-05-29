<?php
require_once '../config/database.php';

try {
    // Read and execute the SQL file
    $sql = file_get_contents('../database/users_table.sql');
    $pdo->exec($sql);
    echo "Users table created successfully";
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
