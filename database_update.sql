-- Update users table
ALTER TABLE users 
ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD COLUMN phone VARCHAR(20) AFTER email,
ADD COLUMN profile_picture VARCHAR(255) DEFAULT NULL AFTER phone;

-- Update ads table
ALTER TABLE ads 
ADD COLUMN status ENUM('active', 'sold', 'inactive') DEFAULT 'active' AFTER location,
ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
ADD FULLTEXT(title, description, location);

-- Create messages table for user communication
CREATE TABLE IF NOT EXISTS messages (
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
);

-- Create favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ad_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, ad_id)
);

-- Create indexes for better performance
CREATE INDEX idx_ads_user ON ads(user_id);
CREATE INDEX idx_ads_category ON ads(category_id);
CREATE INDEX idx_ads_created ON ads(created_at);
CREATE INDEX idx_messages_ad ON messages(ad_id);
CREATE INDEX idx_messages_sender ON messages(sender_id, receiver_id);
