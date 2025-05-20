<?php
session_start();
require_once '../db_operations.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle user actions (delete, update role)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        
        if ($_POST['action'] === 'delete') {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
        } elseif ($_POST['action'] === 'update_role') {
            $new_role = $_POST['new_role'];
            $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
            $stmt->bind_param("si", $new_role, $user_id);
            $stmt->execute();
        }
    }
}

// Get all users
$stmt = $conn->prepare("
    SELECT id, name, email, role, created_at, last_login, is_active 
    FROM users 
    ORDER BY created_at DESC
");
$stmt->execute();
$users = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Users</title>
    <link rel="stylesheet" href="admin-users.css">
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
        <nav class="right-nav">
            <a href="admin-dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
        </nav>
    </header>
    <div class="container">
        <h1 class="title" style="margin-top: 2rem;">User Management</h1>
        <div class="features-grid">
            <div class="feature-card" style="grid-column: 1 / -1;">
                <div class="table-responsive">
                    <div class="user-table">
                        <div class="user-table-header">
                            <div>Name</div>
                            <div>Email</div>
                            <div>Last Login</div>
                            <div>Status</div>
                            <div>Actions</div>
                        </div>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <div class="user-table-row">
                            <div><?php echo htmlspecialchars($user['name']); ?></div>
                            <div><?php echo htmlspecialchars($user['email']); ?></div>
                            <div><?php echo htmlspecialchars($user['last_login'] ?? 'Never'); ?></div>
                            <div><?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?></div>
                            <div>
                                <?php if ($user['role'] !== 'admin'): ?>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" class="delete-btn" title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
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