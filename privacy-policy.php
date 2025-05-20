<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - Card Game</title>
    <link rel="stylesheet" href="privacy-policy-styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="left-nav">
            <div class="logo">
                <img src="card logo.jpg" alt="Logo">
            </div>
        </nav>
        
        <button class="mobile-menu-btn">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="right-nav">
             <a href="game.php"<?php echo basename($_SERVER['PHP_SELF']) == 'game.php' ? 'class="active"' : ''; ?>>Home</a>
            <a href="privacy-policy.php" <?php echo basename($_SERVER['PHP_SELF']) == 'privacy-policy.php' ? 'class="active"' : ''; ?>>Privacy Policy</a>
            <a href="terms.php"<?php echo basename($_SERVER['PHP_SELF']) == 'terms.php' ? 'class="active"' : ''; ?>>Terms</a>
            <a href="faqs.php"<?php echo basename($_SERVER['PHP_SELF']) == 'faqs.php' ? 'class="active"' : ''; ?>>Support & FAQs</a>
        </nav>
        </header>

    <main class="legal-container">
        <h1>Privacy Policy</h1>
        <p class="last-updated">Last Updated: April 25, 2024</p>

        <section class="legal-section">
            <h2>Introduction</h2>
            <p>Welcome to our Card Game. We, the developer team of Court Card Slash, are committed to protect the privacy of our users. This Privacy Policy outlines our practices regarding the collection, use, and protection of your information while you engage with our game.</p>
       
            <h2>Information We Collect</h2>
            <p>We do not collect any personal information from users. The game may collect non-personal information such as game scores and leaderboard rankings to enhance user experience.</p>
        
            <h2>Use of Information</h2>
            <p>The information collected is used solely for the purpose of maintaining the leaderboard and improving the game. We do not share or sell any information to third parties.</p>
        
            <h2>Data Security</h2>
            <p>We implemented an appropriatable security measures that can prevent your personal data from being accidentally lost, used, or accessed in an unauthorized way.</p>
        
            <h2>Changes to This Privacy Policy </h2>
            <p>We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
        </section>
    </main>

    <footer class="footer-content">
        <p>&copy; 2025 Card Game. All rights reserved.</p>
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

    </script>
</body>
</html> 