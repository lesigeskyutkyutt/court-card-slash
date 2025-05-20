<?php
require_once '../db_operations.php';

class AdminFunctions {
    private $conn;

    public function __construct() {
        global $conn; // Use the global connection from db_operations.php
        $this->conn = $conn;
    }

    // Check if user is admin
    public function isAdmin($user_id) {
        $stmt = $this->conn->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        return $user && $user['role'] === 'admin';
    }

    // Get all users
    public function getAllUsers() {
        $stmt = $this->conn->prepare("
            SELECT id, name, email, role, created_at, last_login, is_active 
            FROM users 
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->get_result();
    }

    // Update user role
    public function updateUserRole($user_id, $new_role) {
        $stmt = $this->conn->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->bind_param("si", $new_role, $user_id);
        return $stmt->execute();
    }

    // Delete user
    public function deleteUser($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    // Get all posts with user information
    public function getAllPosts() {
        $stmt = $this->conn->prepare("
            SELECT p.*, u.name as author_name, 
                   (SELECT COUNT(*) FROM forum_comments WHERE post_id = p.id) as comment_count,
                   (SELECT COUNT(*) FROM forum_likes WHERE post_id = p.id) as like_count
            FROM forum_posts p
            JOIN users u ON p.user_id = u.id
            ORDER BY p.created_at DESC
        ");
        $stmt->execute();
        return $stmt->get_result();
    }

    // Delete post
    public function deletePost($post_id) {
        $stmt = $this->conn->prepare("DELETE FROM forum_posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        return $stmt->execute();
    }

    // Get all comments with user and post information
    public function getAllComments() {
        $stmt = $this->conn->prepare("
            SELECT c.*, u.name as author_name, p.title as post_title, p.id as post_id
            FROM forum_comments c
            JOIN users u ON c.user_id = u.id
            JOIN forum_posts p ON c.post_id = p.id
            ORDER BY c.created_at DESC
        ");
        $stmt->execute();
        return $stmt->get_result();
    }

    // Delete comment
    public function deleteComment($comment_id) {
        $stmt = $this->conn->prepare("DELETE FROM forum_comments WHERE id = ?");
        $stmt->bind_param("i", $comment_id);
        return $stmt->execute();
    }

    // Get dashboard statistics
    public function getDashboardStats() {
        $stats = [];

        // Total users
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total_users'] = $result->fetch_assoc()['total'];

        // Total posts
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM forum_posts");
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total_posts'] = $result->fetch_assoc()['total'];

        // Total comments
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM forum_comments");
        $stmt->execute();
        $result = $stmt->get_result();
        $stats['total_comments'] = $result->fetch_assoc()['total'];

        return $stats;
    }

    // Get recent activity
    public function getRecentActivity($limit = 5) {
        $stmt = $this->conn->prepare("
            SELECT p.*, u.name as author_name 
            FROM forum_posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC 
            LIMIT ?
        ");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>