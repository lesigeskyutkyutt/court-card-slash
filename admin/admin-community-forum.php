<?php
session_start();
require_once '../db_operations.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Fetch all posts with like and comment counts
$sql = "SELECT p.*, 
        COUNT(DISTINCT l.id) as like_count,
        COUNT(DISTINCT c.id) as comment_count,
        EXISTS(SELECT 1 FROM forum_likes WHERE post_id = p.id AND user_id = " . intval($_SESSION['user_id']) . ") as is_liked
        FROM forum_posts p
        LEFT JOIN forum_likes l ON p.id = l.post_id
        LEFT JOIN forum_comments c ON p.id = c.post_id
        GROUP BY p.id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Community Forum</title>
    <link rel="stylesheet" href="admin-community-forum(1).css">
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
        <h1 class="title" style="margin-top: 2rem;">Community Forum (Admin)</h1>
        <div class="forum-header">
            <button class="create-post-btn" onclick="showCreatePostModal()">
                <i class="fas fa-plus"></i> Create New Post
            </button>
        </div>
        <div class="posts-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($post = $result->fetch_assoc()): ?>
                    <div class="post admin-post" data-post-id="<?php echo $post['id']; ?>">
                        <div class="post-header">
                            <span class="post-category">
                                <i class="fas <?php
                                    switch($post['category']) {
                                        case 'general':
                                            echo 'fa-comments';
                                            break;
                                        case 'strategy':
                                            echo 'fa-chess';
                                            break;
                                        case 'bug':
                                            echo 'fa-bug';
                                            break;
                                        case 'feature':
                                            echo 'fa-lightbulb';
                                            break;
                                        default:
                                            echo 'fa-comment';
                                    }
                                ?>"></i>
                                <?php echo htmlspecialchars(ucfirst($post['category'])); ?>
                            </span>
                            <span class="post-meta">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($post['user_name']); ?> 
                                <i class="fas fa-clock"></i> <?php echo date('F j, Y, g:i a', strtotime($post['created_at'])); ?>
                            </span>
                        </div>
                        <h2 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h2>
                        <div class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
                        
                        <div class="post-actions">
                            <button class="like-btn <?php echo $post['is_liked'] ? 'liked' : ''; ?>" 
                                    data-post-id="<?php echo $post['id']; ?>">
                                <i class="fas fa-heart"></i>
                                <span class="like-count"><?php echo $post['like_count']; ?></span>
                            </button>
                            <button class="comment-btn" onclick="toggleComments(<?php echo $post['id']; ?>)">
                                <i class="fas fa-comment"></i>
                                <span class="comment-count"><?php echo $post['comment_count']; ?></span>
                            </button>
                            <button class="delete-btn" onclick="deletePost(<?php echo $post['id']; ?>)">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <button onclick="editPost(<?php echo $post['id']; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>

                        <div class="comments-section" id="comments-<?php echo $post['id']; ?>" style="display: none;">
                            <div class="comments-list">
                                <?php
                                $comment_sql = "SELECT * FROM forum_comments WHERE post_id = ? ORDER BY created_at DESC";
                                $comment_stmt = $conn->prepare($comment_sql);
                                $comment_stmt->bind_param("i", $post['id']);
                                $comment_stmt->execute();
                                $comments_result = $comment_stmt->get_result();
                                
                                if ($comments_result->num_rows > 0):
                                    while($comment = $comments_result->fetch_assoc()):
                                ?>
                                    <div class="comment">
                                        <div class="comment-header">
                                            <span class="comment-author"><?php echo htmlspecialchars($comment['user_name']); ?></span>
                                            <span class="comment-time"><?php echo date('F j, Y, g:i a', strtotime($comment['created_at'])); ?></span>
                                        </div>
                                        <div class="comment-content"><?php echo nl2br(htmlspecialchars($comment['content'])); ?></div>
                                        <button class="delete-btn" onclick="deleteComment(<?php echo $comment['id']; ?>)">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <p class="no-comments">No comments yet. Be the first to comment!</p>
                                <?php endif; ?>
                            </div>
                            
                            <form class="comment-form" data-post-id="<?php echo $post['id']; ?>">
                                <textarea placeholder="Write a comment..." required></textarea>
                                <button type="submit">Post Comment</button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="post admin-post">
                    <div class="post-content no-posts">
                        <i class="fas fa-comments"></i>
                        <p>No posts yet. Be the first to create a post!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div class="modal" id="createPostModal">
        <div class="modal-content create-post-modal">
            <span class="close" onclick="closeCreatePostModal()">&times;</span>
            <h2><i class="fas fa-edit"></i> Create New Post</h2>
            <form id="createPostForm" action="../db_operations.php" method="POST">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Category</label>
                    <select name="category" required>
                        <option value="general">General Discussion</option>
                        <option value="strategy">Game Strategies</option>
                        <option value="bug">Bug Reports</option>
                        <option value="feature">Feature Requests</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Title</label>
                    <input type="text" name="title" required placeholder="Enter your post title">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-pen"></i> Content</label>
                    <textarea name="content" required placeholder="Write your post content here..."></textarea>
                </div>
                <input type="hidden" name="action" value="create_post">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Post
                </button>
            </form>
        </div>
    </div>

    <!-- Edit Post Modal -->
    <div class="modal" id="editPostModal">
        <div class="modal-content create-post-modal">
            <span class="close" onclick="closeEditPostModal()">&times;</span>
            <h2><i class="fas fa-edit"></i> Edit Post</h2>
            <form id="editPostForm">
                <input type="hidden" name="post_id" id="edit_post_id">
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Category</label>
                    <select name="category" id="edit_category" required>
                        <option value="general">General Discussion</option>
                        <option value="strategy">Game Strategies</option>
                        <option value="bug">Bug Reports</option>
                        <option value="feature">Feature Requests</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Title</label>
                    <input type="text" name="title" id="edit_title" required placeholder="Enter your post title">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-pen"></i> Content</label>
                    <textarea name="content" id="edit_content" required placeholder="Write your post content here..."></textarea>
                </div>
                <input type="hidden" name="action" value="edit_post">
                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </div>
    </div>

    <script>
        // Like functionality
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function() {
                const postId = this.dataset.postId;
                const likeCount = this.querySelector('.like-count');
                
                fetch('../db_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=toggle_like&post_id=${postId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'liked') {
                        this.classList.add('liked');
                        likeCount.textContent = parseInt(likeCount.textContent) + 1;
                    } else if (data === 'unliked') {
                        this.classList.remove('liked');
                        likeCount.textContent = parseInt(likeCount.textContent) - 1;
                    }
                });
            });
        });

        // Comment functionality
        function toggleComments(postId) {
            const commentsSection = document.getElementById(`comments-${postId}`);
            commentsSection.style.display = commentsSection.style.display === 'none' ? 'block' : 'none';
        }

        document.querySelectorAll('.comment-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const postId = this.dataset.postId;
                const textarea = this.querySelector('textarea');
                const content = textarea.value.trim();
                
                if (!content) return;
                
                fetch('../db_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add_comment&post_id=${postId}&content=${encodeURIComponent(content)}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        location.reload();
                    } else {
                        alert('Error adding comment: ' + data);
                    }
                });
            });
        });

        // Delete post functionality
        function deletePost(postId) {
            if (confirm('Are you sure you want to delete this post?')) {
                fetch('../db_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_post&post_id=${postId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        location.reload();
                    } else {
                        alert('Error deleting post: ' + data);
                    }
                });
            }
        }

        // Delete comment functionality
        function deleteComment(commentId) {
            if (confirm('Are you sure you want to delete this comment?')) {
                fetch('../db_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=delete_comment&comment_id=${commentId}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        location.reload();
                    } else {
                        alert('Error deleting comment: ' + data);
                    }
                });
            }
        }

        // Create post modal functionality
        function showCreatePostModal() {
            const modal = document.getElementById('createPostModal');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeCreatePostModal() {
            const modal = document.getElementById('createPostModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('createPostModal');
            if (event.target == modal) {
                closeCreatePostModal();
            }
        }

        // Handle form submission
        document.getElementById('createPostForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('../db_operations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('success')) {
                    closeCreatePostModal();
                    location.reload();
                } else {
                    alert('Error creating post: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the post. Please try again.');
            });
        });

        // Edit post functionality
        function editPost(postId) {
            // Find the post card and extract data
            const postCard = document.querySelector('.post.admin-post[data-post-id="' + postId + '"]') ||
                Array.from(document.querySelectorAll('.post.admin-post')).find(card => card.querySelector('.edit-btn')?.getAttribute('onclick')?.includes(postId));
            if (!postCard) return;
            const category = postCard.querySelector('.post-category').textContent.trim().toLowerCase();
            const title = postCard.querySelector('.post-title').textContent.trim();
            const content = postCard.querySelector('.post-content').textContent.trim();
            // Fill modal fields
            document.getElementById('edit_post_id').value = postId;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_content').value = content;
            // Show modal
            document.getElementById('editPostModal').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
        function closeEditPostModal() {
            document.getElementById('editPostModal').style.display = 'none';
            document.body.style.overflow = 'auto';
        }
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('editPostModal');
            if (event.target == modal) {
                closeEditPostModal();
            }
        });
        // Handle edit post form submission
        document.getElementById('editPostForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../db_operations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('success')) {
                    closeEditPostModal();
                    location.reload();
                } else {
                    alert('Error updating post: ' + data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the post. Please try again.');
            });
        });
    </script>
</body>
</html> 