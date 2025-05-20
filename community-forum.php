<?php
session_start();

// Redirect admin users to the admin dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: /GAME/admin/admin-dashboard.php');
    exit();
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'login_system');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all posts with like and comment counts
$sql = "SELECT p.*, 
        COUNT(DISTINCT l.id) as like_count,
        COUNT(DISTINCT c.id) as comment_count,
        " . (isset($_SESSION['user_id']) ? "EXISTS(SELECT 1 FROM forum_likes WHERE post_id = p.id AND user_id = " . intval($_SESSION['user_id']) . ")" : "0") . " as is_liked
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
    <title>Community Forum - Court Card Slash</title>
    <link rel="stylesheet" href="communityforum-styles(1).css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="left-nav">
            <div class="logo">
                <img src="card logo.jpg" alt="Logo">
            </div>
            <div class="search-container">
                <button id="searchToggle" aria-label="Toggle search bar" class="search-toggle">
                    <i class="fas fa-search"></i>
                </button>
                <div id="searchBar" class="search-bar">
                    <input type="text" placeholder="Search..." id="searchInput">
                    <div id="searchResults" class="search-results"></div>
                </div>
            </div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <span>Hello, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="#" onclick="showLogin()">Login</a>
                <a href="#" onclick="showSignup()">Sign Up</a>
            <?php endif; ?>
        </nav>

        <button class="mobile-menu-btn">
            <span></span>
            <span></span>
            <span></span>
        </button>
        
        <nav class="right-nav">
            <a href="game.php" <?php echo basename($_SERVER['PHP_SELF']) == 'game.php' ? 'class="active"' : ''; ?>>Home</a>
            <a href="gameinfo.php" <?php echo basename($_SERVER['PHP_SELF']) == 'gameinfo.php' ? 'class="active"' : ''; ?>>Game Info</a>
            <a href="creator.php" <?php echo basename($_SERVER['PHP_SELF']) == 'creator.php' ? 'class="active"' : ''; ?>>Creators</a>
            <a href="howtoplay.php" <?php echo basename($_SERVER['PHP_SELF']) == 'howtoplay.php' ? 'class="active"' : ''; ?>>How to Play</a>
            <a href="leaderboard.php" <?php echo basename($_SERVER['PHP_SELF']) == 'leaderboard.php' ? 'class="active"' : ''; ?>>Leaderboard</a>
            <a href="patchnotes.php" <?php echo basename($_SERVER['PHP_SELF']) == 'patchnotes.php' ? 'class="active"' : ''; ?>>Patch Notes</a>
            <a href="community-forum.php" <?php echo basename($_SERVER['PHP_SELF']) == 'community-forum.php' ? 'class="active"' : ''; ?>>Community Forum</a>
        </nav>
    </header>

    <div class="forum-container">
        <h1>Community Forum</h1>
        
        <div class="forum-header">
            <?php if(isset($_SESSION['user_id'])): ?>
                <button class="create-post-btn" onclick="showCreatePostModal()">
                    <i class="fas fa-plus"></i> Create New Post
                </button>
            <?php else: ?>
                <button class="create-post-btn" onclick="showLogin()">
                    <i class="fas fa-sign-in-alt"></i> Login to Post
                </button>
            <?php endif; ?>
        </div>

        <div class="posts-container">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($post = $result->fetch_assoc()): ?>
                    <div class="post">
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
                                    data-post-id="<?php echo $post['id']; ?>"
                                    <?php echo !isset($_SESSION['user_id']) ? 'onclick="showLogin()"' : ''; ?>>
                                <i class="fas fa-heart"></i>
                                <span class="like-count"><?php echo $post['like_count']; ?></span>
                            </button>
                            <button class="comment-btn" onclick="toggleComments(<?php echo $post['id']; ?>)">
                                <i class="fas fa-comment"></i>
                                <span class="comment-count"><?php echo $post['comment_count']; ?></span>
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
                                    </div>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <p class="no-comments">No comments yet. Be the first to comment!</p>
                                <?php endif; ?>
                            </div>
                            
                            <?php if(isset($_SESSION['user_id'])): ?>
                            <form class="comment-form" data-post-id="<?php echo $post['id']; ?>">
                                <textarea placeholder="Write a comment..." required></textarea>
                                <button type="submit">Post Comment</button>
                            </form>
                            <?php else: ?>
                            <p class="login-to-comment">Please <a href="#" onclick="showLogin()">login</a> to comment</p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="post">
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
            <form id="createPostForm" action="db_operations.php" method="POST">
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
                <button type="submit" class="submit-btn">
                    <i class="fas fa-paper-plane"></i> Post
                </button>
            </form>
        </div>
    </div>

    <div class="modal-bg" id="modalBg"></div>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLogin()">&times;</span>
            <h2>Login</h2>
            <form id="loginForm" action="db_operations.php" method="POST">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Login</button>
            </form>
            <p class="form-footer">Don't have an account? <a href="#" onclick="switchToSignup()">Sign Up</a></p>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signupModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeSignup()">&times;</span>
            <h2>Sign Up</h2>
            <form id="signupForm" action="db_operations.php" method="POST">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">Sign Up</button>
            </form>
            <p class="form-footer">Already have an account? <a href="#" onclick="switchToLogin()">Login</a></p>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <p>&copy; 2025 Court Card Slash. All rights reserved.</p>
            <nav>
            <a href="privacy-policy.php">Privacy Policy</a>
            <a href="terms.php">Terms of Service</a>
          </nav>
          <nav>
            <a href="faqs.php">Faq's</a>
          </nav>
            <div class="social-links">
                  <a href="mailto:abarcajohnandy@gmail.com"><i class="fab fa-google"></i></a>
                  <a href="https://www.facebook.com/yourpage" target="_blank"><i class="fab fa-facebook"></i></a>
            </div>
        </div>
    </footer>

    <script>
                // Mobile menu functionality
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

        // Close mobile menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenuBtn.contains(e.target) && !rightNav.contains(e.target)) {
                rightNav.classList.remove('active');
                menuSpans.forEach(span => {
                    span.style.transform = 'none';
                    span.style.opacity = '1';
                });
            }
        });

        
        // Check if user is logged in
        const isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        
        // Modal functionality
        const loginModal = document.getElementById('loginModal');
        const signupModal = document.getElementById('signupModal');

        function showLogin() {
            loginModal.style.display = 'block';
            signupModal.style.display = 'none';
        }

        function showSignup() {
            signupModal.style.display = 'block';
            loginModal.style.display = 'none';
        }

        function closeLogin() {
            loginModal.style.display = 'none';
        }

        function closeSignup() {
            signupModal.style.display = 'none';
        }

        function switchToSignup() {
            closeLogin();
            showSignup();
        }

        function switchToLogin() {
            closeSignup();
            showLogin();
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target == loginModal) {
                closeLogin();
            }
            if (event.target == signupModal) {
                closeSignup();
            }
        }

        // Handle form submissions
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('db_operations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Login failed. Please check your credentials.');
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
            });
        });

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('db_operations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if(data.includes('Registration successful')) {
                    alert('Registration successful! Please login.');
                    closeSignup();
                    showLogin();
                } else {
                    alert('Registration failed. Please try again.');
                }
            });
        });

        // Create Post Modal Functions
        function showCreatePostModal() {
            document.getElementById('createPostModal').style.display = 'block';
            document.getElementById('modalBg').style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeCreatePostModal() {
            document.getElementById('createPostModal').style.display = 'none';
            document.getElementById('modalBg').style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('modalBg').addEventListener('click', function() {
            closeCreatePostModal();
        });

        // Form submission handling
        document.getElementById('createPostForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'create_post');

            fetch('db_operations.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeCreatePostModal();
                    location.reload();
                } else {
                    alert(data.message || 'Error creating post');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the post. Please try again.');
            });
        });

        // Like functionality
        document.querySelectorAll('.like-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!isLoggedIn) {
                    showLogin();
                    return;
                }
                
                const postId = this.dataset.postId;
                const likeCount = this.querySelector('.like-count');
                
                fetch('db_operations.php', {
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
                
                fetch('db_operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add_comment&post_id=${postId}&content=${encodeURIComponent(content)}`
                })
                .then(response => response.text())
                .then(data => {
                    if (data === 'success') {
                        // Reload the page to show the new comment
                        window.location.reload();
                    } else {
                        alert('Error adding comment: ' + data);
                    }
                });
            });
        });

        // Search functionality
        const searchToggle = document.getElementById('searchToggle');
        const searchBar = document.getElementById('searchBar');
        const searchInput = document.getElementById('searchInput');
        const searchResults = document.getElementById('searchResults');

        searchToggle.addEventListener('click', () => {
            searchBar.classList.toggle('active');
            if (searchBar.classList.contains('active')) {
                searchInput.focus();
            }
        });

        // Close search when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchToggle.contains(e.target) && !searchBar.contains(e.target)) {
                searchBar.classList.remove('active');
            }
        });

        // Handle search input
        let searchTimeout;
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length > 0) {
                searchTimeout = setTimeout(() => {
                    console.log('Searching for:', query);
                    // Perform search across all content
                    fetch(`search.php?q=${encodeURIComponent(query)}`)
                        .then(response => {
                            console.log('Search response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Search results:', data);
                            searchResults.innerHTML = '';
                            if (data && data.length > 0) {
                                data.forEach(result => {
                                    console.log('Adding result:', result);
                                    const div = document.createElement('div');
                                    div.className = 'search-result-item';
                                    div.innerHTML = `
                                        <div class="result-title">${result.title}</div>
                                        <div class="result-type">${result.type}</div>
                                    `;
                                    div.addEventListener('click', () => {
                                        window.location.href = result.url;
                                    });
                                    searchResults.appendChild(div);
                                });
                                searchResults.classList.add('active');
                            } else {
                                searchResults.innerHTML = '<div class="search-result-item">No results found</div>';
                                searchResults.classList.add('active');
                            }
                        })
                        .catch(error => {
                            console.error('Search error:', error);
                            searchResults.innerHTML = '<div class="search-result-item">Error performing search</div>';
                            searchResults.classList.add('active');
                        });
                }, 300);
            } else {
                searchResults.classList.remove('active');
            }
        });
    </script>
</body>
</html> 