<?php
session_start();
require_once '../db_operations.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_leaderboard'])) {
    $conn->query("TRUNCATE TABLE games");
    $message = "Leaderboard has been reset!";
}
// Get top players for HTML view
$topPlayers = getTopPlayers(10); // Get top 10 players
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Leaderboard - Court Card Slash</title>
    <link rel="stylesheet" href="admin-leaderboard.css">
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
    <div class="leaderboard">
        <div class="leaderboard-container">
            <div class="leaderboard-header">
                <h1>Leaderboard (Admin)</h1>
                <?php if ($message): ?><p style="color:#ff4655; font-weight:600;"> <?php echo $message; ?> </p><?php endif; ?>
                <form method="POST" onsubmit="return confirm('Are you sure you want to reset the leaderboard?');">
                    <button type="submit" name="reset_leaderboard" class="reset-btn"><i class="fas fa-redo"></i> Reset Leaderboard</button>
                </form>
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
                        </div>
                        <div class="score"><?php echo number_format($player['highest_score']); ?></div>
                        <div class="games"><?php echo $player['games_played']; ?></div>
                    </div>
                    <?php endforeach; ?>
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