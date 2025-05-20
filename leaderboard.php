<?php
session_start();

// Include database operations
require_once 'db_operations.php';

// Redirect admin users to the admin dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: /GAME/admin/admin-dashboard.php');
    exit();
}

// Handle API request
if (isset($_GET['api']) && $_GET['api'] == '1') {
    header('Content-Type: application/json');
    
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }
    
    // Get top players
    $topPlayers = getTopPlayers(10);
    
    // Format the data for the API response
    $response = [];
    foreach ($topPlayers as $index => $player) {
        $response[] = [
            'rank' => $index + 1,
            'username' => $player['username'],
            'score' => (int)$player['highest_score'],
            'games_played' => (int)$player['games_played']
        ];
    }
    
    echo json_encode($response);
    exit;
}

// Get top players for HTML view
$topPlayers = getTopPlayers(10); // Get top 10 players
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - Court Card Slash</title>
    <link rel="stylesheet" href="leaderboard-style(1).css">
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

    <div class="leaderboard">
        <div class="leaderboard-container">
            <div class="leaderboard-header">
                <h1>Leaderboard</h1>
            </div>

            <div class="leaderboard-section">
                <div class="leaderboard-table">
                    <div class="table-header">
                        <div class="rank">Rank</div>
                        <div class="player">Player</div>
                        <div class="score">High Score</div>
                        <div class="games">Games Played</div>
                    </div>
                    <?php foreach ($topPlayers as $index => $player): ?>
                    <div class="table-row <?php echo $index < 3 ? 'top-three' : ''; ?>">
                        <div class="rank">#<?php echo $index + 1; ?></div>
                        <div class="player">
                            <?php echo htmlspecialchars($player['username']); ?>
                            <?php if (isset($_SESSION['user_id']) && $player['user_id'] == $_SESSION['user_id']): ?>
                                <span class="you-badge">You</span>
                            <?php endif; ?>
                        </div>
                        <div class="score"><?php echo number_format($player['highest_score']); ?></div>
                        <div class="games"><?php echo $player['games_played']; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
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