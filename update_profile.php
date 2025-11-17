<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['error'] = "User not found";
    header('Location: index.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate name
    if (empty($name)) {
        $_SESSION['error'] = "Name is required";
        header('Location: profile.php');
        exit();
    }
    
    // Initialize update data array
    $update_data = ['name' => $name];
    $update_fields = ['name = :name'];
    
    // Handle password change if requested
    if (!empty($new_password)) {
        // Verify current password
        if (empty($current_password) || !password_verify($current_password, $user['password'])) {
            $_SESSION['error'] = "Current password is incorrect";
            header('Location: profile.php');
            exit();
        }
        
        // Validate new password
        if (strlen($new_password) < 8) {
            $_SESSION['error'] = "New password must be at least 8 characters long";
            header('Location: profile.php');
            exit();
        }
        
        if ($new_password !== $confirm_password) {
            $_SESSION['error'] = "New passwords do not match";
            header('Location: profile.php');
            exit();
        }
        
        // Add new password to update data
        $update_data['password'] = password_hash($new_password, PASSWORD_DEFAULT);
        $update_fields[] = 'password = :password';
    }
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        // Check file type
        if (!in_array($file['type'], $allowed_types)) {
            $_SESSION['error'] = "Only JPG, PNG, and GIF files are allowed";
            header('Location: profile.php');
            exit();
        }
        
        // Check file size
        if ($file['size'] > $max_size) {
            $_SESSION['error'] = "File is too large. Maximum size is 5MB";
            header('Location: profile.php');
            exit();
        }
        
        // Create uploads directory if it doesn't exist
        $upload_dir = 'uploads/profiles/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
        $destination = $upload_dir . $filename;
        
        // Delete old profile image if exists
        if (!empty($user['profile_image']) && file_exists($upload_dir . $user['profile_image'])) {
            unlink($upload_dir . $user['profile_image']);
        }
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $update_data['profile_image'] = $filename;
            $update_fields[] = 'profile_image = :profile_image';
        } else {
            $_SESSION['error'] = "Failed to upload profile image";
            header('Location: profile.php');
            exit();
        }
    }
    
    // Update user data in database
    try {
        $sql = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = :id";
        $update_data['id'] = $user_id;
        
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($update_data)) {
            $_SESSION['success'] = "Profile updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update profile";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }
    
    header('Location: profile.php');
    exit();
} else {
    header('Location: profile.php');
    exit();
}
