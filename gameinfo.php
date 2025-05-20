<?php
session_start();

// Redirect admin users to the admin dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: /GAME/admin/admin-dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Cards</title>
    <link rel="stylesheet" href="gameinfo.css">
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

    <section class="features">
        <h1>Game Features</h1>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-desktop"></i>
                </div>
                <h2>Stunning Graphics</h2>
                <p>Immerse yourself in breathtaking visuals and realistic environments that bring the game to life.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h2>Competitiveness</h2>
                <p>Compete againts other players in the leaderboards</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <h2>Regular Updates</h2>
                <p>Stay engaged with new content, features, and improvements added regularly to enhance your gaming experience.</p>
            </div>
        </div>
    </section>

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

    <div class="container">
        <h1 class="title">Cards</h1>

        <h2>Aces</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/1D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Ace</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/1C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Ace</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/1H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Ace</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/1S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Ace</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Two</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/2D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Two</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/2C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Two</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/2H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Two</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/2S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Two</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Three</h2>
         <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/3D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Three</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/3C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Three</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/3H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Three</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/3S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Three</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Four</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/4D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Four</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/4C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Four</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/4H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Four</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/4S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Four</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Five</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/5D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Five</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/5C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Five</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/5H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Five</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/5S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Five </h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Six</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/6D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Six</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/6C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Six</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/6H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Six</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/6S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Six</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Seven</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/7D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Seven</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/7C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Seven</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/7H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Seven</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/7S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Seven</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Eight</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/8D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <e class="agent-name">Eight</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/8C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Eight</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/8H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Eight</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/8S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Eight</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Nine</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/9D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Nine</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/9C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Nine</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/9H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Nine</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/9S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Nine</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Ten</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/10D.png" alt="Diamond Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Ten</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/10C.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Ten</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/10H.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Ten</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/10S.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Ten</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <h2>Joker</h2>
        <div class="agents-grid" style="display: flex; justify-content: center; align-items: center;">
             <div class="agent-card" style="width: 250px; margin: 0 auto;">
                     <img src="Deck Cards/Joker.png" alt="Joker Card" class="agent-image" style="width: 100%; height: 320px; object-fit: cover; border-radius: 15px 15px 0 0;">
                 <div class="agent-info">
                    <h2 class="agent-name">Joker</h2>
                    <div class="agent-role">This has the ability to eliminate instantly any of the royal cards</div>
                </div>
            </div>
        </div>
        
        <h2>Royals</h2>
        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/Jack Diamond.png" alt="Diamond Card" class="agent-image">
              <div class="agent-info">
                    <h2 class="agent-name">Jack</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/Jack Club.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Jack</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/Jack Heart.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Jack</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/Jack Spade.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Jack</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/Queen Diamond.png" alt="Diamond Card" class="agent-image">
              <div class="agent-info">
                    <h2 class="agent-name">Queen</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/Queen Club.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Queen</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/Queen Heart.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Queen</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/Queen Spade.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">Queen</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div>

        <div class="agents-grid">
            <div class="agent-card">
                <img src="Deck Cards/Diamond/King Diamond.png" alt="Diamond Card" class="agent-image">
              <div class="agent-info">
                    <h2 class="agent-name">King</h2>
                    <div class="agent-role">Diamond</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Club/King Club.png" alt="Club Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">King</h2>
                    <div class="agent-role">Club</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Heart/King Heart.png" alt="Heart Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">King</h2>
                    <div class="agent-role">Heart</div>
                </div>
            </div>
            <div class="agent-card">
                <img src="Deck Cards/Spade/King Spade.png" alt="Spade Card" class="agent-image">
                <div class="agent-info">
                    <h2 class="agent-name">King</h2>
                    <div class="agent-role">Spade</div>
                </div>
            </div>
        </div> 

    </div>

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