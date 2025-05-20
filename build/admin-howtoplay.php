<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /GAME/login.php');
    exit();
}

// Database connection
require_once '../includes/db_connection.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $title = $_POST['title'];
                $content = $_POST['content'];
                $icon = $_POST['icon'];
                
                $stmt = $conn->prepare("INSERT INTO how_to_play (title, content, icon) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $content, $icon);
                $stmt->execute();
                break;

            case 'edit':
                $id = $_POST['id'];
                $title = $_POST['title'];
                $content = $_POST['content'];
                $icon = $_POST['icon'];
                
                $stmt = $conn->prepare("UPDATE how_to_play SET title = ?, content = ?, icon = ? WHERE id = ?");
                $stmt->bind_param("sssi", $title, $content, $icon, $id);
                $stmt->execute();
                break;

            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM how_to_play WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;
        }
    }
}

// Fetch existing instructions
$result = $conn->query("SELECT * FROM how_to_play ORDER BY id");
$instructions = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - How to Play Management</title>
    <link rel="stylesheet" href="admin-howtoplay.css">
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

    <div class="how-to-play-section">
        <h1>How to Play Management</h1>
        
        <div class="admin-controls">
            <button onclick="showAddForm()" class="add-btn">Add New Instruction</button>
            
            <div id="addForm" class="edit-form">
                <h3>Add New Instruction</h3>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Icon (Font Awesome class):</label>
                        <input type="text" name="icon" placeholder="e.g., fas fa-trophy" required>
                    </div>
                    <div class="form-group">
                        <label>Content:</label>
                        <textarea name="content" required></textarea>
                    </div>
                    <button type="submit">Add Instruction</button>
                    <button type="button" onclick="hideAddForm()">Cancel</button>
                </form>
            </div>
        </div>

        <?php foreach ($instructions as $instruction): ?>
        <div class="instruction-card" id="instruction-<?php echo $instruction['id']; ?>">
            <div class="instruction-icon">
                <i class="<?php echo htmlspecialchars($instruction['icon']); ?>"></i>
            </div>
            <h2><?php echo htmlspecialchars($instruction['title']); ?></h2>
            <div class="content">
                <?php echo nl2br(htmlspecialchars($instruction['content'])); ?>
            </div>
            <div class="admin-buttons">
                <button class="edit-btn" onclick="showEditForm(<?php echo $instruction['id']; ?>)">Edit</button>
                <form action="" method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?php echo $instruction['id']; ?>">
                    <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete this instruction?')">Delete</button>
                </form>
            </div>
            
            <div id="editForm-<?php echo $instruction['id']; ?>" class="edit-form">
                <h3>Edit Instruction</h3>
                <form action="" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?php echo $instruction['id']; ?>">
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($instruction['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Icon (Font Awesome class):</label>
                        <input type="text" name="icon" value="<?php echo htmlspecialchars($instruction['icon']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Content:</label>
                        <textarea name="content" required><?php echo htmlspecialchars($instruction['content']); ?></textarea>
                    </div>
                    <button type="submit">Save Changes</button>
                    <button type="button" onclick="hideEditForm(<?php echo $instruction['id']; ?>)">Cancel</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script>
        function showAddForm() {
            document.getElementById('addForm').classList.add('active');
        }

        function hideAddForm() {
            document.getElementById('addForm').classList.remove('active');
        }

        function showEditForm(id) {
            document.getElementById(`editForm-${id}`).classList.add('active');
        }

        function hideEditForm(id) {
            document.getElementById(`editForm-${id}`).classList.remove('active');
        }
    </script>
</body>
</html> 