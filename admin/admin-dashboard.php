<?php
session_start();
require_once '../db_operations.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Get admin information
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="left-nav">
            <div class="logo">
                <img src="../card logo.jpg" alt="Logo">
            </div>
            <span class="user-welcome">Admin Panel</span>
            <a href="../logout.php" class="logout-btn">Logout</a>
        </nav>
    </header>
    <div class="container">
        <h1 class="title" style="margin-top: 2rem;">Admin Dashboard</h1>
        <div class="features-grid">
            <?php
            // Get total users count
            $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
            $users_count = $result->fetch_assoc()['total'];

            // Get total posts count
            $result = $conn->query("SELECT COUNT(*) as total FROM forum_posts");
            $posts_count = $result->fetch_assoc()['total'];

            // Get total comments count
            $result = $conn->query("SELECT COUNT(*) as total FROM forum_comments");
            $comments_count = $result->fetch_assoc()['total'];

            // Get total patchnotes count
            $result = $conn->query("SELECT COUNT(*) as total FROM patch_notes");
            $patchnotes_count = $result->fetch_assoc()['total'];
            ?>
            <div class="feature-card" onclick="window.location.href='admin-users.php'">
                <div class="feature-icon"><i class="fas fa-users"></i></div>
                <h2>Users</h2>
                <p style="font-size:2.5rem; color:#ff4655; font-weight:700; margin:1rem 0;"> <?php echo $users_count; ?> </p>
            </div>
            <div class="feature-card" onclick="window.location.href='admin-creator.php'">
                <div class="feature-icon"><i class="fas fa-user-tie"></i></div>
                <h2>Creators</h2>
                <p style="font-size:2.5rem; color:#ff4655; font-weight:700; margin:1rem 0;"> <i class="fas fa-cog"></i> </p>
            </div>
            <div class="feature-card" onclick="window.location.href='admin-howtoplay.php'">
                <div class="feature-icon"><i class="fas fa-book"></i></div>
                <h2>How to Play</h2>
                <p style="font-size:2.5rem; color:#ff4655; font-weight:700; margin:1rem 0;"> <i class="fas fa-cog"></i> </p>
            </div>
            <div class="feature-card" onclick="window.location.href='admin-leaderboard.php'">
                <div class="feature-icon"><i class="fas fa-trophy"></i></div>
                <h2>Leaderboard</h2>
                <p style="font-size:2.5rem; color:#ff4655; font-weight:700; margin:1rem 0;"> <i class="fas fa-cog"></i> </p>
            </div>
            <div class="feature-card" onclick="window.location.href='admin-patchnotes.php'">
                <div class="feature-icon"><i class="fas fa-file-alt"></i></div>
                <h2>Patch Notes</h2>
                <p style="font-size:2.5rem; color:#ff4655; font-weight:700; margin:1rem 0;"> <?php echo $patchnotes_count; ?> </p>
            </div>
            <div class="feature-card" onclick="window.location.href='admin-community-forum.php'">
                <div class="feature-icon"><i class="fas fa-comments"></i></div>
                <h2>Community Forum</h2>
                <p style="font-size:2.5rem; color:#ff4655; font-weight:700; margin:1rem 0;"> <?php echo $posts_count; ?> </p>
            </div>
        </div>
    </div>
    <script>
        // Mobile menu functionality (reuse from gameinfo.php)
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const rightNav = document.querySelector('.right-nav');
        const menuSpans = document.querySelectorAll('.mobile-menu-btn span');
        mobileMenuBtn.addEventListener('click', () => {
            rightNav.classList.toggle('active');
            menuSpans[0].style.transform = rightNav.classList.contains('active') 
                ? 'rotate(45deg) translate(6px, 6px)' 
                : 'none';
            menuSpans[1].style.opacity = rightNav.classList.contains('active') 
                ? '0' 
                : '1';
            menuSpans[2].style.transform = rightNav.classList.contains('active') 
                ? 'rotate(-45deg) translate(6px, -6px)' 
                : 'none';
        });
        document.addEventListener('click', (e) => {
            if (!mobileMenuBtn.contains(e.target) && !rightNav.contains(e.target)) {
                rightNav.classList.remove('active');
                menuSpans.forEach(span => {
                    span.style.transform = 'none';
                    span.style.opacity = '1';
                });
            }
        });
    </script>
</body>
</html> 