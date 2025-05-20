<?php
session_start();
require_once '../db_operations.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add') {
            $version = $_POST['version'];
            $release_date = $_POST['release_date'];
            $download_link = $_POST['download_link'];
            $features = $_POST['features'];
            
            $sql = "INSERT INTO patch_notes (version, release_date, download_link, features) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssss", $version, $release_date, $download_link, $features);
            $stmt->execute();
        } elseif ($_POST['action'] === 'delete' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $sql = "DELETE FROM patch_notes WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
        } elseif ($_POST['action'] === 'edit' && isset($_POST['id'])) {
            $id = $_POST['id'];
            $version = $_POST['version'];
            $release_date = $_POST['release_date'];
            $download_link = $_POST['download_link'];
            $features = $_POST['features'];
            
            $sql = "UPDATE patch_notes SET version = ?, release_date = ?, download_link = ?, features = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssi", $version, $release_date, $download_link, $features, $id);
            $stmt->execute();
        }
    }
}

// Fetch existing patch notes
$sql = "SELECT * FROM patch_notes ORDER BY release_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Patch Notes</title>
    <link rel="stylesheet" href="admin-patchnotes.css">
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
        <h1 class="title" style="margin-top: 2rem;">Patch Notes Management</h1>
        
        <div class="patch-note-form">
            <h2>Add New Patch Note</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <input type="text" name="version" placeholder="Version (e.g., 1.0.1)" required>
                <input type="date" name="release_date" required>
                <input type="text" name="download_link" placeholder="Download Link (e.g., GODOTT_3_2_(COPY)_2/Court Card Slash.zip)" required>
                <textarea name="features" placeholder="Enter features (one per line)" required></textarea>
                <button type="submit" class="edit-btn"><i class="fas fa-plus"></i> Add Patch Note</button>
            </form>
        </div>

        <h2>Existing Patch Notes</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="patch-note-card">
                    <h3>Version <?php echo htmlspecialchars($row['version']); ?></h3>
                    <div class="release-date">Released: <?php echo htmlspecialchars($row['release_date']); ?></div>
                    <div class="download-link">Download: <?php echo htmlspecialchars($row['download_link']); ?></div>
                    <div class="features">
                        <h4>Features:</h4>
                        <?php 
                        $features = explode("\n", $row['features']);
                        echo "<ul>";
                        foreach($features as $feature) {
                            if(trim($feature) !== '') {
                                echo "<li>" . htmlspecialchars(trim($feature)) . "</li>";
                            }
                        }
                        echo "</ul>";
                        ?>
                    </div>
                    <div class="actions">
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="delete-btn"><i class="fas fa-trash"></i> Delete</button>
                        </form>
                        <button class="edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No patch notes found.</p>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Patch Note</h2>
            <form method="POST" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <input type="text" name="version" id="edit_version" placeholder="Version" required>
                <input type="date" name="release_date" id="edit_release_date" required>
                <input type="text" name="download_link" id="edit_download_link" placeholder="Download Link" required>
                <textarea name="features" id="edit_features" placeholder="Enter features (one per line)" required></textarea>
                <button type="submit" class="edit-btn"><i class="fas fa-save"></i> Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        const editModal = document.getElementById('editModal');
        const editForm = document.getElementById('editForm');

        function openEditModal(patchNote) {
            document.getElementById('edit_id').value = patchNote.id;
            document.getElementById('edit_version').value = patchNote.version;
            document.getElementById('edit_release_date').value = patchNote.release_date;
            document.getElementById('edit_download_link').value = patchNote.download_link;
            document.getElementById('edit_features').value = patchNote.features;
            editModal.style.display = 'block';
        }

        function closeEditModal() {
            editModal.style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == editModal) {
                closeEditModal();
            }
        }

        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const rightNav = document.querySelector('.right-nav');
        const menuSpans = document.querySelectorAll('.mobile-menu-btn span');
        
        if (mobileMenuBtn) {
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
        }

        document.addEventListener('click', (e) => {
            if (mobileMenuBtn && !mobileMenuBtn.contains(e.target) && !rightNav.contains(e.target)) {
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