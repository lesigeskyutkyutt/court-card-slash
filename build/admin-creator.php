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
                $name = $_POST['name'];
                $role = $_POST['role'];
                $bio = $_POST['bio'];
                
                // Handle image upload
                $image = '';
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $target_dir = "../uploads/creators/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $image = $target_dir . time() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $image);
                }

                $stmt = $conn->prepare("INSERT INTO creators (name, role, bio, image) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $role, $bio, $image);
                $stmt->execute();
                break;

            case 'edit':
                $id = $_POST['id'];
                $name = $_POST['name'];
                $role = $_POST['role'];
                $bio = $_POST['bio'];
                
                // Handle image upload
                $image = $_POST['current_image'];
                if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                    $target_dir = "../uploads/creators/";
                    if (!file_exists($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }
                    $image = $target_dir . time() . '_' . basename($_FILES['image']['name']);
                    move_uploaded_file($_FILES['image']['tmp_name'], $image);
                    
                    // Delete old image if exists
                    if (!empty($_POST['current_image']) && file_exists($_POST['current_image'])) {
                        unlink($_POST['current_image']);
                    }
                }

                $stmt = $conn->prepare("UPDATE creators SET name = ?, role = ?, bio = ?, image = ? WHERE id = ?");
                $stmt->bind_param("ssssi", $name, $role, $bio, $image, $id);
                $stmt->execute();
                break;

            case 'delete':
                $id = $_POST['id'];
                
                // Get image path before deleting
                $stmt = $conn->prepare("SELECT image FROM creators WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $creator = $result->fetch_assoc();
                
                // Delete image if exists
                if (!empty($creator['image']) && file_exists($creator['image'])) {
                    unlink($creator['image']);
                }
                
                // Delete creator
                $stmt = $conn->prepare("DELETE FROM creators WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                break;

            case 'update_contact':
                $email = $_POST['email'];
                $phone = $_POST['phone'];
                $location = $_POST['location'];
                
                $stmt = $conn->prepare("UPDATE creators SET email = ?, phone = ?, location = ? WHERE id = ?");
                $stmt->bind_param("ssss", $email, $phone, $location, $id);
                $stmt->execute();
                break;
        }
        
        // Redirect to prevent form resubmission
        header('Location: admin-creator.php');
        exit();
    }
}

// Check if creators table exists, if not create it
$table_check = $conn->query("SHOW TABLES LIKE 'creators'");
if ($table_check->num_rows == 0) {
    $create_table = "CREATE TABLE IF NOT EXISTS creators (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        role VARCHAR(255) NOT NULL,
        bio TEXT NOT NULL,
        image VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(255),
        location TEXT
    )";
    $conn->query($create_table);
    
    // Insert initial creators if table was just created
    $insert_creators = "INSERT INTO creators (name, role, bio) VALUES
        ('John Andy Abarca', 'Game Developer', 'Passionate about game development and creating immersive experiences.'),
        ('DM Felicilda', 'Web Developer', 'Specializing in game mechanics and user experience design.'),
        ('Angelo Lesiges', 'Co-Developer', 'Creating stunning visuals and character designs.'),
        ('Justine Estrella', 'Web Designer', 'Creating stunning visuals and character designs.'),
        ('Trixie Talinting', 'Art Director', 'Creating stunning visuals and character designs.'),
        ('Shane Flores', 'Art Director', 'Creating stunning visuals and character designs.')";
    $conn->query($insert_creators);
}

// Fetch all creators
$creators = $conn->query("SELECT * FROM creators ORDER BY id ASC");
if (!$creators) {
    die("Error fetching creators: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Creators - Admin Dashboard</title>
    <link rel="stylesheet" href="admin-creator(1).css">
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

    <div class="admin-content">
        <div class="content-header">
            <h1>Manage Creators</h1>
            <button class="add-creator-btn" onclick="showAddModal()">
                <i class="fas fa-plus"></i> Add New Creator
            </button>
        </div>

        <div class="creators-grid">
            <?php if ($creators->num_rows > 0): ?>
                <?php while ($creator = $creators->fetch_assoc()): ?>
                <div class="creator-card">
                    <div class="creator-image">
                        <?php if (!empty($creator['image'])): ?>
                            <img src="<?php echo htmlspecialchars($creator['image']); ?>" alt="<?php echo htmlspecialchars($creator['name']); ?>">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                    </div>
                    <h2><?php echo htmlspecialchars($creator['name']); ?></h2>
                    <p class="role"><?php echo htmlspecialchars($creator['role']); ?></p>
                    <p class="bio"><?php echo htmlspecialchars($creator['bio']); ?></p>
                    <div class="creator-actions">
                        <button onclick="showEditModal(<?php echo htmlspecialchars(json_encode($creator)); ?>)" class="edit-btn">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button onclick="confirmDelete(<?php echo $creator['id']; ?>)" class="delete-btn">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-creators">
                    <p>No creators found. Add your first creator using the button above.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Contact Information Section -->
        <div class="contact-section">
            <div class="content-header">
                <h1>Contact Information</h1>
                <button class="edit-contact-btn" onclick="showContactModal()">
                    <i class="fas fa-edit"></i> Edit Contact Info
                </button>
            </div>
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h2>Email</h2>
                    <p>support@courtcardslash.com</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h2>Phone</h2>
                    <p>+1 (555) 123-4567</p>
                </div>

                <div class="contact-card">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h2>Location</h2>
                    <p>123 Game Street,<br>Gaming City, GC 12345</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Creator Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Add New Creator</h2>
            <form action="admin-creator.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <input type="text" id="role" name="role" required>
                </div>
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" required></textarea>
                </div>
                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <button type="submit">Add Creator</button>
            </form>
        </div>
    </div>

    <!-- Edit Creator Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Creator</h2>
            <form action="admin-creator.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="current_image" id="current_image">
                <div class="form-group">
                    <label for="edit_name">Name</label>
                    <input type="text" id="edit_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="edit_role">Role</label>
                    <input type="text" id="edit_role" name="role" required>
                </div>
                <div class="form-group">
                    <label for="edit_bio">Bio</label>
                    <textarea id="edit_bio" name="bio" required></textarea>
                </div>
                <div class="form-group">
                    <label for="edit_image">Image</label>
                    <input type="file" id="edit_image" name="image" accept="image/*">
                    <div id="current_image_preview"></div>
                </div>
                <button type="submit">Update Creator</button>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Delete</h2>
            <p>Are you sure you want to delete this creator?</p>
            <form action="admin-creator.php" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-actions">
                    <button type="button" onclick="closeDeleteModal()" class="cancel-btn">Cancel</button>
                    <button type="submit" class="delete-btn">Delete</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contact Information Edit Modal -->
    <div id="contactModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeContactModal()">&times;</span>
            <h2>Edit Contact Information</h2>
            <form action="admin-creator.php" method="POST">
                <input type="hidden" name="action" value="update_contact">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="support@courtcardslash.com" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="+1 (555) 123-4567" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <textarea id="location" name="location" required>123 Game Street,
Gaming City, GC 12345</textarea>
                </div>
                <button type="submit">Update Contact Info</button>
            </form>
        </div>
    </div>

    <script>
        // Modal functionality
        function showAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }

        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }

        function showEditModal(creator) {
            document.getElementById('edit_id').value = creator.id;
            document.getElementById('edit_name').value = creator.name;
            document.getElementById('edit_role').value = creator.role;
            document.getElementById('edit_bio').value = creator.bio;
            document.getElementById('current_image').value = creator.image;
            
            const preview = document.getElementById('current_image_preview');
            if (creator.image) {
                preview.innerHTML = `<img src="${creator.image}" alt="Current image" style="max-width: 200px; margin-top: 10px;">`;
            } else {
                preview.innerHTML = '';
            }
            
            document.getElementById('editModal').style.display = 'block';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        function confirmDelete(id) {
            document.getElementById('delete_id').value = id;
            document.getElementById('deleteModal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }

        // Contact modal functions
        function showContactModal() {
            document.getElementById('contactModal').style.display = 'block';
        }

        function closeContactModal() {
            document.getElementById('contactModal').style.display = 'none';
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html> 