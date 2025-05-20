<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support & FAQs</title>
    <link rel="stylesheet" href="faqs(1).css">
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
        <h1>Support</h1>
        <p class="last-updated">Last Updated: April 25, 2024</p>

        <section class="legal-section">
            <h2>Welcome to the Court Card Slash Support Center!</h2>
            <p>We are here to help you with any questions or issues you may encounter while playing our game. Below are some frequently asked questions (FAQs) and information on how to contact us for further assistance.</p>
        </section>
    </main>
        
    <main class="legal-container">
        <h1>Frequently Asked Questions (FAQs)</h1>
        <section class="legal-section">
            <h2>1. What is Court Card Slash?</h2>
            <p>Court Card Slash is a single-player card game developed as a school project. Players can compete for high scores on a leaderboard, providing an engaging experience with card battles.</p>

            <h2>2. How do I play Court Card Slash?</h2>
            <p>You can run the game locally on your computer. Just download it and you can play the game </p>

            <h2>3. Do I need an internet connection to play?</h2>
            <p>No, since this is a local project, you do not need an internet connection to play Court Card Slash. All game features, including the leaderboard, are accessible offline.</p>

            <h2>4. How is my score calculated?</h2>
            <p>Your score is calculated based on your performance in the game, including the number of rounds won and any bonuses earned during gameplay.</p>

            <h2>5. How can I view the leaderboard?</h2>
            <p>You can view the leaderboard directly within the game interface or the Website itself</p>
        </section>
    </main>

    <footer class="footer-content">
        <p>&copy; 2025 Card Game. All rights reserved.</p>
        <div class="social-links">
                  <a href="mailto:abarcajohnandy@gmail.com"><i class="fab fa-google"></i></a>
                  <a href="https://www.facebook.com/yourpage" target="_blank"><i class="fab fa-facebook"></i></a>
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