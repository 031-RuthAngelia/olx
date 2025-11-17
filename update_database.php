<?php
require_once 'config.php';

try {
    // Add profile_image column to users table if it doesn't exist
    $sql = "ALTER TABLE users 
            ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) NULL AFTER password";
    
    $pdo->exec($sql);
    
    echo "Database updated successfully. <a href='index.php'>Go to Home</a>";
    
} catch (PDOException $e) {
    die("Error updating database: " . $e->getMessage());
}
