<?php
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check if a column exists in a table
function columnExists($pdo, $table, $column) {
    $sql = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $stmt = $pdo->query($sql);
    return $stmt->rowCount() > 0;
}

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Update users table
    if (!columnExists($pdo, 'users', 'updated_at')) {
        $pdo->exec("ALTER TABLE users 
                   ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }
    
    if (!columnExists($pdo, 'users', 'phone')) {
        $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) AFTER email");
    }
    
    // Update ads table
    if (!columnExists($pdo, 'ads', 'status')) {
        $pdo->exec("ALTER TABLE ads 
                   ADD COLUMN status ENUM('active', 'sold', 'inactive') DEFAULT 'active' AFTER location");
    }
    
    if (!columnExists($pdo, 'ads', 'updated_at')) {
        $pdo->exec("ALTER TABLE ads 
                   ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }
    
    // Create messages table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        ad_id INT NOT NULL,
        message TEXT NOT NULL,
        is_read BOOLEAN DEFAULT FALSE,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE
    )");
    
    // Create favorites table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS favorites (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        ad_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
        UNIQUE KEY unique_favorite (user_id, ad_id)
    )");
    
    // Create indexes for better performance
    $indexes = [
        'idx_ads_user' => 'CREATE INDEX IF NOT EXISTS idx_ads_user ON ads(user_id)',
        'idx_ads_category' => 'CREATE INDEX IF NOT EXISTS idx_ads_category ON ads(category_id)',
        'idx_ads_created' => 'CREATE INDEX IF NOT EXISTS idx_ads_created ON ads(created_at)',
        'idx_messages_ad' => 'CREATE INDEX IF NOT EXISTS idx_messages_ad ON messages(ad_id)',
        'idx_messages_sender' => 'CREATE INDEX IF NOT EXISTS idx_messages_sender ON messages(sender_id, receiver_id)'
    ];
    
    foreach ($indexes as $index) {
        $pdo->exec($index);
    }
    
    // Add fulltext index for search
    try {
        $pdo->exec("ALTER TABLE ads ADD FULLTEXT(title, description, location)");
    } catch (PDOException $e) {
        // Ignore if fulltext index already exists
        if (strpos($e->getMessage(), 'Duplicate key name') === false) {
            throw $e;
        }
    }
    
    $pdo->commit();
    
    echo "<h2>Database updated successfully!</h2>";
    echo "<p>The following updates were applied:</p>";
    echo "<ul>";
    echo "<li>Added updated_at and phone fields to users table</li>";
    echo "<li>Added status and updated_at fields to ads table</li>";
    echo "<li>Created messages table for user communication</li>";
    echo "<li>Created favorites table for saving favorite ads</li>";
    echo "<li>Added indexes for better performance</li>";
    echo "<li>Added fulltext search index for ads</li>";
    echo "</ul>";
    echo "<p><a href='index.php' class='btn btn-primary'>Go to Home</a></p>";
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "<div class='alert alert-danger'>";
    echo "<h3>Error updating database</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database configuration and try again.</p>";
    echo "</div>";
}
