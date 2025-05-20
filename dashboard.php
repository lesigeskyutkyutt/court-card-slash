<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: game.php");
    exit();
}

// Include database operations
require_once 'db_operations.php';

// Get user's stats
$user_id = $_SESSION['user_id'];
$topPlayers = getTopPlayers(100); // Get top 100 players to find user's rank

$userStats = [
    'games_played' => 0,
    'total_score' => 0,
    'highest_score' => 0,
    'rank' => 0
];

// Find user's stats and rank
foreach ($topPlayers as $index => $player) {
    if ($player['user_id'] == $user_id) {
        $userStats['games_played'] = $player['games_played'];
        $userStats['total_score'] = $player['total_score'];
        $userStats['highest_score'] = $player['highest_score'];
        $userStats['rank'] = $index + 1;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Dashboard</title>
    <link rel="stylesheet" href="dashboard-style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="dashboard">
        <div class="dashboard-container">
            <div class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
                <div>
                    <a href="game.php" class="back-to-game">Back to Home</a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <div class="welcome-section">
                <h1>Your Gaming Dashboard</h1>
                <p>Track your progress, achievements, and game statistics all in one place.</p>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Games Played</h3>
                    <p><?php echo number_format($userStats['games_played']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Score</h3>
                    <p><?php echo number_format($userStats['total_score']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Highest Score</h3>
                    <p><?php echo number_format($userStats['highest_score']); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Leaderboard Rank</h3>
                    <p><?php echo $userStats['rank'] ? '#' . $userStats['rank'] : 'Not Ranked'; ?></p>
                </div>
            </div>

            <div class="game-section">
                <h2>Download a game for free!</h2>
                <div class="game-grid">
                    <div class="game-card">
                        <img src="card logo.jpg" alt="Game 1">
                        <div class="game-info">
                            <h3>Court Card Slash</h3>
                            <p>Experience the thrill of this exciting game with stunning graphics and intense gameplay.</p>
                            <a href="GODOT GAME/Court Card Slash.zip" class="play-now-btn" download>Download Game</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>