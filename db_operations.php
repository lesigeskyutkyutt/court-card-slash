<?php
// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Start output buffering
ob_start();

// Log the start of the script
error_log("Script started - Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("Raw POST data: " . file_get_contents('php://input'));
error_log("POST data: " . print_r($_POST, true));
error_log("GET data: " . print_r($_GET, true));
error_log("Headers: " . print_r(getallheaders(), true));

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_log("Session data: " . print_r($_SESSION, true));

// Database connection
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') ?: '';
$db_name = getenv('DB_NAME') ?: 'login_system';

error_log("Attempting database connection to: $db_host, $db_name");

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    ob_end_clean(); // Clear any output
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

error_log("Database connection successful");

// Function to get top players
function getTopPlayers($limit = 10) {
    global $conn;
    
    $sql = "SELECT u.id as user_id, u.name as username, 
            COALESCE(SUM(g.score), 0) as total_score,
            COALESCE(MAX(g.score), 0) as highest_score,
            COUNT(g.id) as games_played
            FROM users u
            LEFT JOIN games g ON u.id = g.user_id
            GROUP BY u.id, u.name
            ORDER BY highest_score DESC
            LIMIT ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $players = [];
    while ($row = $result->fetch_assoc()) {
        $players[] = $row;
    }
    
    $stmt->close();
    return $players;
}

// Function to record game start
function recordGameStart($user_id) {
    global $conn;
    
    error_log("Attempting to record game start: user_id=$user_id");
    
    // Insert new game session with NULL score
    $sql = "INSERT INTO games (user_id, score, played_at) VALUES (?, NULL, CURRENT_TIMESTAMP)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        error_log("Game start recorded successfully");
        return $stmt->insert_id; // Return the game session ID
    } else {
        error_log("Error recording game start: " . $stmt->error);
        return false;
    }
    $stmt->close();
}

// Function to submit game score
function submitGameScore($user_id, $score) {
    global $conn;
    
    // Get the most recent game session for this user that has no score
    $check_sql = "SELECT id FROM games WHERE user_id = ? AND score IS NULL ORDER BY played_at DESC LIMIT 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $game_id = $row['id'];
        
        // Update the existing game session with the score
        $update_sql = "UPDATE games SET score = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $score, $game_id);
        
        if ($update_stmt->execute()) {
            error_log("Score updated for game session $game_id");
            return true;
        } else {
            error_log("Error updating score: " . $update_stmt->error);
            return false;
        }
        $update_stmt->close();
    } else {
        // If no active session found, create a new one with the score
        error_log("No active game session found for user $user_id, creating new session");
        $insert_sql = "INSERT INTO games (user_id, score, played_at) VALUES (?, ?, CURRENT_TIMESTAMP)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $user_id, $score);
        
        if ($insert_stmt->execute()) {
            error_log("Created new game session with score for user $user_id");
            return true;
        } else {
            error_log("Error creating new game session: " . $insert_stmt->error);
            return false;
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
}

// Handle forum post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_post') {
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['success' => false, 'message' => 'You must be logged in to create a post']);
        exit;
    }

    // Get form data
    $category = $_POST['category'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];
    
    // Validate inputs
    if (empty($category) || empty($title) || empty($content)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Create forum post
    $stmt = $conn->prepare("INSERT INTO forum_posts (user_id, user_name, category, title, content, created_at) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
    $stmt->bind_param("issss", $user_id, $user_name, $category, $title, $content);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Post created successfully']);
    } else {
        error_log("Database Error in forum post creation: " . $stmt->error . " (Error Code: " . $stmt->errno . ")");
        error_log("SQL State: " . $stmt->sqlstate);
        error_log("User ID: " . $user_id . ", Category: " . $category);
        echo json_encode(['success' => false, 'message' => 'Failed to create post. Please try again.']);
    }
    $stmt->close();
    exit;
}

// Handle like/unlike
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_like') {
    if (!isset($_SESSION['user_id'])) {
        echo "error: You must be logged in to like posts";
        exit;
    }

    $post_id = intval($_POST['post_id']);
    $user_id = $_SESSION['user_id'];

    // Check if user already liked the post
    $check_sql = "SELECT id FROM forum_likes WHERE post_id = ? AND user_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $post_id, $user_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Unlike
        $delete_sql = "DELETE FROM forum_likes WHERE post_id = ? AND user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("ii", $post_id, $user_id);
        $delete_stmt->execute();
        echo "unliked";
    } else {
        // Like
        $insert_sql = "INSERT INTO forum_likes (post_id, user_id) VALUES (?, ?)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("ii", $post_id, $user_id);
        $insert_stmt->execute();
        echo "liked";
    }
    exit;
}

// Handle comment creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_comment') {
    if (!isset($_SESSION['user_id'])) {
        echo "error: You must be logged in to comment";
        exit;
    }

    $post_id = intval($_POST['post_id']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'];

    if (empty($content)) {
        echo "error: Comment cannot be empty";
        exit;
    }

    $sql = "INSERT INTO forum_comments (post_id, user_id, user_name, content) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $post_id, $user_id, $user_name, $content);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: Failed to add comment";
    }
    $stmt->close();
    exit;
}

// Handle login/signup
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Signup processing - check this condition first
    if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['password'])) {
        $name = $conn->real_escape_string($_POST['name']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        // Check if email already exists in users
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists as a user']);
            exit;
        }
        // Check if email already exists in admins
        $check_admin_sql = "SELECT id FROM admins WHERE email = ?";
        $check_admin_stmt = $conn->prepare($check_admin_sql);
        $check_admin_stmt->bind_param("s", $email);
        $check_admin_stmt->execute();
        $admin_result = $check_admin_stmt->get_result();
        if ($admin_result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists as an admin']);
            exit;
        }
        // Insert new user
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Registration successful']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed']);
        }
        exit;
    }
    
    // Login processing
    if (isset($_POST['email']) && isset($_POST['password'])) {
        // Always reset session before login
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        } else {
            session_unset();
            session_destroy();
            session_start();
        }
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        // 1. Check admins table
        $admin_sql = "SELECT id, name, password FROM admins WHERE email = ?";
        $admin_stmt = $conn->prepare($admin_sql);
        $admin_stmt->bind_param("s", $email);
        $admin_stmt->execute();
        $admin_result = $admin_stmt->get_result();

        if ($admin_result->num_rows === 1) {
            $admin = $admin_result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                // Set session variables for admin
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['user_name'] = $admin['name'];
                $_SESSION['role'] = 'admin';

                echo json_encode([
                    'success' => true,
                    'redirect' => 'admin/admin-dashboard.php',
                    'name' => $admin['name'],
                    'email' => $email
                ]);
                exit;
            }
        }

        // 2. If not found in admins, check users table
        $user_sql = "SELECT id, name, email, password, role FROM users WHERE email = ?";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("s", $email);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows === 1) {
            $user = $user_result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Update last login time
                $update_sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();

                // Set session variables for user
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                $redirect = $user['role'] === 'admin' ? 'admin/admin-dashboard.php' : 'dashboard.php';
                echo json_encode([
                    'success' => true,
                    'redirect' => $redirect,
                    'name' => $user['name'],
                    'email' => $user['email']
                ]);
                exit;
            }
        }

        // If we get here, either the email wasn't found or the password didn't match
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit;
    }
}

// Handle game start recording
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'record_game_start') {
    ob_end_clean(); // Clear any output
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['user_id'])) {
        error_log("No user_id in session");
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    
    if ($game_id = recordGameStart($user_id)) {
        error_log("Created new game session for user ID: $user_id");
        echo json_encode(['success' => true, 'message' => 'Game session started', 'game_id' => $game_id]);
    } else {
        error_log("Failed to create game session");
        echo json_encode(['success' => false, 'message' => 'Failed to start game session']);
    }
    exit;
}

// Handle game score submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_score') {
    ob_end_clean(); // Clear any output
    header('Content-Type: application/json');
    
    // Log the incoming request
    error_log("Score submission request received");
    error_log("POST data: " . print_r($_POST, true));
    error_log("Session data: " . print_r($_SESSION, true));
    
    if (!isset($_SESSION['user_id'])) {
        error_log("No user_id in session");
        echo json_encode(['success' => false, 'message' => 'Authentication required']);
        exit;
    }
    
    if (!isset($_POST['score']) || !is_numeric($_POST['score'])) {
        error_log("Invalid score received: " . (isset($_POST['score']) ? $_POST['score'] : 'not set'));
        echo json_encode(['success' => false, 'message' => 'Invalid score']);
        exit;
    }
    
    $user_id = $_SESSION['user_id'];
    $score = intval($_POST['score']);
    
    error_log("Attempting to submit score: user_id=$user_id, score=$score");
    
    if (submitGameScore($user_id, $score)) {
        error_log("Score submitted successfully");
        echo json_encode(['success' => true, 'message' => 'Score submitted successfully']);
    } else {
        error_log("Failed to submit score");
        echo json_encode(['success' => false, 'message' => 'Failed to submit score']);
    }
    exit;
}

// Handle post editing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_post') {
    // Check if user is logged in and is admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        echo "error: Unauthorized";
        exit;
    }

    $post_id = intval($_POST['post_id']);
    $category = $_POST['category'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    if (empty($category) || empty($title) || empty($content) || !$post_id) {
        echo "error: All fields are required";
        exit;
    }

    $stmt = $conn->prepare("UPDATE forum_posts SET category = ?, title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("sssi", $category, $title, $content, $post_id);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error: Failed to update post";
    }
    $stmt->close();
    exit;
}

// Don't close the connection - it will be closed automatically when the script ends