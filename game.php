<?php
session_start();
require_once 'db_operations.php';

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

// Fetch latest forum posts
$sql = "SELECT * FROM forum_posts ORDER BY created_at DESC LIMIT 3";
$result = $conn->query($sql);

// Fetch latest patch notes
$patch_notes_sql = "SELECT * FROM patch_notes ORDER BY release_date DESC LIMIT 2";
$patch_notes_result = $conn->query($patch_notes_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Court Card Slash</title>
    <link rel="stylesheet" href="game-styles.css">
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

    <div class="hero">
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="shaders/card(1).mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
        <div class="hero-content">
            <h1>Court Card Slash</h1>
            <p>Download now! and enjoy this thrilling game for free!</p>
            <button class="play-button" onclick="checkLogin()">PLAY FREE</button>
        </div>
    </div>


    <div class="game-library">
        <div class="game-library-content">
            <div class="game-library-text">
                <h1>Game Library</h1>
                    <div class="game-description">
                        <div class="description-column">
                            <h2>Overview</h2>
                            <ul>
                                <li>Court Card Slash is an engaging and strategic solo card game that challenges players to outsmart a their opponent in defeating the royal cards using the standard deck of number cards. Perfect for card game enthusiasts and casual players alike, this game combines elements of strategy, planning, and a bit of luck, making each playthrough a unique experience.</li>
                            </ul>
                        </div>
                        <div class="description-column">
                            <h2>Objective</h2>
                            <ul>
                                <li>In Court Card Slash, your primary goal is to defeat all the royal cards before your deck of number cards runs out or before your opponent's defeat all the royal cards. The game encourages strategic thinking and careful planning, as you must anticipate the moves of your royal adversaries.</li>
                            </ul>
                        </div>
                    </div>
            </div>
            <div class="media-slider">
                <div class="main-display">
                    <button class="nav-btn prev-btn" onclick="changeSlide(-1)"> < </button>
                    <div class="slide active">
                        <video controls id="mainVideo">
                            <source src="shaders/videos.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <div class="slide">
                        <img src="shaders/1.jpg" alt="Game Screenshot 1">
                    </div>
                    <div class="slide">
                        <img src="shaders/2.jpg" alt="Game Screenshot 2">
                    </div>
                    <div class="slide">
                        <img src="shaders/3.jpg" alt="Game Screenshot 3">
                    </div>
                    <div class="slide">
                        <img src="shaders/5.jpg" alt="Game Screenshot 4">
                    </div>
                    <button class="nav-btn next-btn" onclick="changeSlide(1)"> > </button>
                </div>

                <div class="thumbnails">
                    <div class="thumb active" onclick="showSlide(0)">
                        <video>
                            <source src="shaders/videos.mp4" type="video/mp4">
                        </video>
                    </div>

                    <div class="thumb" onclick="showSlide(1)">
                        <img src="shaders/1.jpg" alt="Thumbnail 1">
                    </div>
                    <div class="thumb" onclick="showSlide(2)">
                        <img src="shaders/2.jpg" alt="Thumbnail 2">
                    </div>
                    <div class="thumb" onclick="showSlide(3)">
                        <img src="shaders/3.jpg" alt="Thumbnail 3">
                    </div>
                    <div class="thumb" onclick="showSlide(4)">
                        <img src="shaders/5.jpg" alt="Thumbnail 4">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="preview-sections">
        <div class="preview-card">
            <div class="preview-content">
                <h2>Latest Updates</h2>

                <div class="patchnotes-preview">
                    <?php if ($patch_notes_result && $patch_notes_result->num_rows > 0): ?>
                        <?php while($patch = $patch_notes_result->fetch_assoc()): ?>
                            <div class="version-preview">
                                <div class="version-header">
                                    <h3>Version <?php echo htmlspecialchars($patch['version']); ?></h3>
                                    <span class="release-date"><?php echo date('F j, Y', strtotime($patch['release_date'])); ?></span>
                                </div>
                                <div class="version-changes">
                                    <h4>Features:</h4>
                                    <ul>
                                        <?php 
                                        $features = explode("\n", $patch['features']);
                                        foreach($features as $feature) {
                                            if(trim($feature) !== '') {
                                                echo "<li>" . htmlspecialchars(trim($feature)) . "</li>";
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No patch notes available.</p>
                    <?php endif; ?>
                </div>
                <a href="patchnotes.php" class="preview-btn">View All Updates</a>
            </div>
        </div>

        <div class="preview-card">
            <div class="preview-content">
                <h2>Community Forum</h2>
                <div class="forum-preview">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($post = $result->fetch_assoc()): ?>
                            <div class="forum-post-preview">
                                <div class="post-preview-header">
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
                                    </span>
                                </div>
                                <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                <p class="post-excerpt"><?php echo substr(htmlspecialchars($post['content']), 0, 100) . '...'; ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-posts">No forum posts yet. Be the first to create a post!</p>
                    <?php endif; ?>
                </div>
                <a href="community-forum.php" class="preview-btn">Join the Discussion</a>
            </div>
        </div>
    </div>

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

    <div class="modal-bg" id="modalBg"></div>

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

        // Modal functionality
        const loginModal = document.getElementById('loginModal');
        const signupModal = document.getElementById('signupModal');

        function checkLogin() {
            <?php if(isset($_SESSION['user_id'])): ?>
                window.location.href = 'dashboard.php';
            <?php else: ?>
                showLogin();
            <?php endif; ?>
        }

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

        let currentSlide = 0;
        const slides = document.querySelectorAll('.slide');
        const thumbs = document.querySelectorAll('.thumb');
        const mainVideo = document.getElementById('mainVideo');

        function showSlide(n) {
            // Hide all slides
            slides.forEach(slide => slide.classList.remove('active'));
            thumbs.forEach(thumb => thumb.classList.remove('active'));

            // Reset current slide if out of bounds
            currentSlide = n;
            if (currentSlide >= slides.length) currentSlide = 0;
            if (currentSlide < 0) currentSlide = slides.length - 1;

            // Show current slide
            slides[currentSlide].classList.add('active');
            thumbs[currentSlide].classList.add('active');

            // Handle video playback
            if (currentSlide === 0) {
                mainVideo.play();
            } else {
                mainVideo.pause();
            }
        }

        function changeSlide(direction) {
            showSlide(currentSlide + direction);
        }

        // Initialize first slide
        showSlide(0);

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
            if (!searchBar.contains(e.target) && !searchToggle.contains(e.target)) {
                searchBar.classList.remove('active');
                searchResults.classList.remove('active');
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
</body>
</html>