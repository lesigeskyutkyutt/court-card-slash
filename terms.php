<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - Card Game</title>
    <link rel="stylesheet" href="terms(1).css">
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
        <h1>Terms of Service</h1>
        <p class="last-updated">Last Updated: April 25, 2024</p>

        <section class="legal-section">
            <h2>Acceptance of Terms</h2>
            <p>By using Court Card Slash, you agree to comply with and be bound by these Terms of Service.</p>

            <h2>User Accounts</h2>
            <p>To access certain features of the website, you may be required to create an account. You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.</p>

            <h2>Usr Conduct</h2>
            <p>Court Card Slash is a single-player game that features a leaderboard. You may not use the game for any illegal or unauthorized purpose.</p>
            <p>You agree not to engage in any conduct that may harm the game or its users, including but not limited to cheating, hacking, or exploiting any bugs.</p>

            <h2>Intellectual Property</h2>
            <p>All content included on this website, such as text, graphics, logos, images, and software, is the property of the Card Game and is protected by copyright and other intellectual property laws.</p>

            <h2>Limitation of Liability</h2>
            <p>In no event shall Court Card Slash be liable for any damages arising from the use of or inability to use the game.</p>

            <h2>Changes to Terms</h2>
            <p>We reserve the right to modify these terms at any time. We will notify users of any changes by posting the new terms on this page and updating the "Last Updated" date.</p>
        
            <h2>Contact Information</h2>
            <p>If you have any questions about these Terms of Service, please contact us at:</p>
            <p>Email: support@courtcardslash.com</p>
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